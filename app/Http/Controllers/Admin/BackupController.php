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
     * Database: u802714156_events
     */
    public function download(Request $request)
    {
        try {
            // Get database configuration from .env
            $database = Config::get('database.connections.mysql.database'); // u802714156_events
            $username = Config::get('database.connections.mysql.username'); // u802714156_eventsOrgPass
            $password = Config::get('database.connections.mysql.password'); // 1OrgEvents2025
            $host = Config::get('database.connections.mysql.host', 'localhost');
            $port = Config::get('database.connections.mysql.port', 3306);

            // Create backup filename with timestamp
            $filename = 'backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups');

            // Create backups directory if it doesn't exist
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            // Try mysqldump first (if available on server), fallback to manual backup
            if ($this->commandExists('mysqldump')) {
                try {
                    \Log::info('Attempting mysqldump backup...');
                    $this->mysqldumpBackup($fullPath, $host, $port, $username, $password, $database);
                    \Log::info('mysqldump backup successful');
                } catch (\Exception $e) {
                    \Log::warning('mysqldump failed, switching to manual backup: ' . $e->getMessage());
                    $this->manualDatabaseBackup($fullPath);
                }
            } else {
                // Manual backup method (most reliable for shared hosting)
                \Log::info('Using manual backup method');
                $this->manualDatabaseBackup($fullPath);
            }

            // Verify the backup file was created successfully
            if (!file_exists($fullPath)) {
                throw new \Exception('Backup file was not created');
            }

            if (filesize($fullPath) === 0) {
                throw new \Exception('Backup file is empty');
            }

            \Log::info('Backup created successfully: ' . $filename . ' (' . filesize($fullPath) . ' bytes)');

            // Download the file and automatically delete after sending
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Database backup failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create database backup. Please contact support.'
            ], 500);
        }
    }

    /**
     * Create backup using mysqldump command (faster for large databases)
     */
    private function mysqldumpBackup($filePath, $host, $port, $username, $password, $database)
    {
        // Build mysqldump command with proper escaping
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --quick --lock-tables=false --no-tablespaces %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database)
        );

        // Execute command and capture output
        $output = shell_exec($command . ' 2>&1');
        
        if ($output === null || empty($output)) {
            throw new \Exception('mysqldump command produced no output');
        }

        // Check for error messages in output
        if (stripos($output, 'ERROR') !== false || stripos($output, 'Access denied') !== false) {
            throw new \Exception('mysqldump error: ' . substr($output, 0, 200));
        }

        // Write output to file
        $bytesWritten = file_put_contents($filePath, $output);
        
        if ($bytesWritten === false || $bytesWritten === 0) {
            throw new \Exception('Failed to write mysqldump output to file');
        }
    }

    /**
     * Manual database backup (fallback method - works on all hosting)
     * Database: u802714156_events
     * 
     * FIXED VERSION:
     * - Proper INSERT syntax with parentheses
     * - Correct value escaping (no double escaping)
     * - Error handling for each table
     */
    private function manualDatabaseBackup($filePath)
    {
        $database = Config::get('database.connections.mysql.database'); // u802714156_events
        
        // Get all tables from the database
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;
        
        if (empty($tables)) {
            throw new \Exception('No tables found in database');
        }

        // Start building SQL dump
        $sql = "-- ========================================\n";
        $sql .= "-- MySQL Database Backup\n";
        $sql .= "-- ========================================\n";
        $sql .= "-- Database: {$database}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- ========================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET AUTOCOMMIT=0;\n";
        $sql .= "START TRANSACTION;\n\n";

        $tableCount = 0;
        $rowCount = 0;

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            try {
                $sql .= "-- ========================================\n";
                $sql .= "-- Table structure: {$tableName}\n";
                $sql .= "-- ========================================\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                
                // Get CREATE TABLE statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // Get table data
                $sql .= "-- Table data: {$tableName}\n";
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    // Get column names from first row
                    $firstRow = (array) $rows->first();
                    $columns = array_keys($firstRow);
                    
                    foreach ($rows as $row) {
                        $row = (array) $row;
                        
                        // FIXED: Proper value escaping without double escaping
                        $values = array_map(function($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            // Use addslashes for proper SQL escaping
                            return "'" . addslashes((string)$value) . "'";
                        }, $row);
                        
                        // FIXED: Proper INSERT syntax - parentheses around column list
                        $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                        $rowCount++;
                    }
                    $sql .= "\n";
                } else {
                    $sql .= "-- No data in table {$tableName}\n\n";
                }
                
                $tableCount++;
                
            } catch (\Exception $e) {
                \Log::warning("Failed to backup table {$tableName}: " . $e->getMessage());
                $sql .= "-- ERROR backing up table {$tableName}: " . $e->getMessage() . "\n\n";
            }
        }
        
        $sql .= "COMMIT;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n\n";
        $sql .= "-- ========================================\n";
        $sql .= "-- Backup completed\n";
        $sql .= "-- Tables backed up: {$tableCount}\n";
        $sql .= "-- Total rows: {$rowCount}\n";
        $sql .= "-- ========================================\n";
        
        // Write SQL to file
        $bytesWritten = file_put_contents($filePath, $sql);
        
        if ($bytesWritten === false) {
            throw new \Exception('Failed to write backup file to disk');
        }
        
        if ($bytesWritten === 0) {
            throw new \Exception('Backup file is empty (0 bytes written)');
        }
        
        \Log::info("Manual backup completed: {$tableCount} tables, {$rowCount} rows, {$bytesWritten} bytes");
    }

    /**
     * Check if a shell command exists on the server
     */
    private function commandExists($command)
    {
        try {
            $whereIsCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
            $process = Process::fromShellCommandline("$whereIsCommand $command");
            $process->run();
            
            return $process->isSuccessful();
        } catch (\Exception $e) {
            \Log::warning("Command check failed for {$command}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean old backup files (optional maintenance task)
     * Keeps backups for specified number of days
     */
    public function cleanOldBackups($daysToKeep = 7)
    {
        try {
            $backupPath = storage_path('app/backups');
            
            if (!file_exists($backupPath)) {
                return 0;
            }

            $files = glob($backupPath . '/*.sql');
            $now = time();
            $deletedCount = 0;

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileAge = $now - filemtime($file);
                    $maxAge = 60 * 60 * 24 * $daysToKeep;
                    
                    if ($fileAge >= $maxAge) {
                        if (unlink($file)) {
                            $deletedCount++;
                            \Log::info("Deleted old backup: " . basename($file));
                        }
                    }
                }
            }
            
            \Log::info("Cleaned {$deletedCount} old backup files (kept last {$daysToKeep} days)");
            return $deletedCount;
            
        } catch (\Exception $e) {
            \Log::error("Failed to clean old backups: " . $e->getMessage());
            return 0;
        }
    }
}