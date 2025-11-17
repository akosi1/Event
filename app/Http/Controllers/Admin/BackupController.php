<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    /**
     * Download database backup
     */
    public function download(Request $request)
    {
        try {
            // Get database configuration
            $database = Config::get('database.connections.mysql.database');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $host = Config::get('database.connections.mysql.host');
            $port = Config::get('database.connections.mysql.port', 3306);

            // Create backup filename with timestamp
            $filename = $database . '_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups');

            // Create backups directory if it doesn't exist
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            // Try mysqldump first, fallback to manual backup
            if ($this->commandExists('mysqldump')) {
                try {
                    $this->mysqldumpBackup($fullPath, $host, $port, $username, $password, $database);
                } catch (\Exception $e) {
                    \Log::warning('mysqldump failed, using manual backup: ' . $e->getMessage());
                    $this->manualDatabaseBackup($fullPath);
                }
            } else {
                // Manual backup method
                $this->manualDatabaseBackup($fullPath);
            }

            // Verify the file was created
            if (!file_exists($fullPath) || filesize($fullPath) === 0) {
                throw new \Exception('Backup file was not created or is empty');
            }

            // Download the file and delete after sending
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Database backup failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create database backup. Please check logs for details.'
            ], 500);
        }
    }

    /**
     * Create backup using mysqldump command
     */
    private function mysqldumpBackup($filePath, $host, $port, $username, $password, $database)
    {
        // Build mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --quick --lock-tables=false %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database)
        );

        // Execute and write to file
        $output = shell_exec($command . ' 2>&1');
        
        if ($output === null) {
            throw new \Exception('mysqldump command failed');
        }

        file_put_contents($filePath, $output);
        
        if (filesize($filePath) === 0) {
            throw new \Exception('mysqldump produced empty file');
        }
    }

    /**
     * Manual database backup (fallback method)
     * Fixed version with proper SQL generation
     */
    private function manualDatabaseBackup($filePath)
    {
        // Use the correct database name: u802714156_events
        $database = Config::get('database.connections.mysql.database');
        
        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;
        
        // Start SQL dump
        $sql = "-- Database Backup\n";
        $sql .= "-- Database: {$database}\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            try {
                // Drop table if exists
                $sql .= "-- Table: {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                
                // Get create table statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // Get table data
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    // Get column names from first row
                    $firstRow = (array) $rows->first();
                    $columns = array_keys($firstRow);
                    
                    foreach ($rows as $row) {
                        $row = (array) $row;
                        
                        // FIXED: Proper value escaping
                        $values = array_map(function($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            // Use addslashes for proper escaping
                            return "'" . addslashes((string)$value) . "'";
                        }, $row);
                        
                        // FIXED: Proper INSERT syntax with parentheses around columns
                        $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to backup table {$tableName}: " . $e->getMessage());
                $sql .= "-- ERROR backing up table {$tableName}: " . $e->getMessage() . "\n\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Write to file
        $bytesWritten = file_put_contents($filePath, $sql);
        
        if ($bytesWritten === false) {
            throw new \Exception('Failed to write backup file');
        }
        
        \Log::info("Manual backup created: {$filePath} ({$bytesWritten} bytes)");
    }

    /**
     * Check if a command exists
     */
    private function commandExists($command)
    {
        try {
            $whereIsCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
            $process = Process::fromShellCommandline("$whereIsCommand $command");
            $process->run();
            
            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clean old backups (optional - call this periodically)
     */
    public function cleanOldBackups($daysToKeep = 7)
    {
        $backupPath = storage_path('app/backups');
        
        if (!file_exists($backupPath)) {
            return;
        }

        $files = glob($backupPath . '/*.sql');
        $now = time();
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $daysToKeep) {
                    unlink($file);
                    $deletedCount++;
                }
            }
        }
        
        \Log::info("Cleaned {$deletedCount} old backup files");
    }
}