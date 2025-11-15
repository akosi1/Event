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
            $filename = $database . '_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups');

            // Create backups directory if it doesn't exist
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            // Method 1: Using mysqldump (recommended if available)
            if ($this->commandExists('mysqldump')) {
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    escapeshellarg($fullPath)
                );

                exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception('mysqldump failed');
                }
            } else {
                // Method 2: Manual backup using PHP
                $this->manualDatabaseBackup($fullPath);
            }

            // Download the file
            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Database backup failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create database backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual database backup (fallback method)
     */
    private function manualDatabaseBackup($filePath)
    {
        $database = Config::get('database.connections.mysql.database');
        
        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;
        
        $sql = "-- Database Backup\n";
        $sql .= "-- Database: {$database}\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            // Drop table if exists
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            
            // Get create table statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::table($tableName)->get();
            
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $row = (array) $row;
                    $values = array_map(function($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, $row);
                    
                    $columns = array_keys($row);
                    $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Write to file
        file_put_contents($filePath, $sql);
    }

    /**
     * Check if a command exists
     */
    private function commandExists($command)
    {
        $whereIsCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $process = Process::fromShellCommandline("$whereIsCommand $command");
        $process->run();
        
        return $process->isSuccessful();
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

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $daysToKeep) {
                    unlink($file);
                }
            }
        }
    }
}