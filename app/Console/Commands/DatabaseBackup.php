<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--type=full : Type of backup (full|structure|data)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');
        
        $type = $this->option('type');
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$type}_{$timestamp}.sql";
        
        try {
            $this->createBackupDirectory();
            
            switch ($type) {
                case 'full':
                    $this->createFullBackup($filename);
                    break;
                case 'structure':
                    $this->createStructureBackup($filename);
                    break;
                case 'data':
                    $this->createDataBackup($filename);
                    break;
                default:
                    $this->error('Invalid backup type. Use: full, structure, or data');
                    return 1;
            }
            
            $this->cleanOldBackups();
            $this->info("Backup completed successfully: {$filename}");
            
            // Log the backup
            Log::info('Database backup completed', [
                'type' => $type,
                'filename' => $filename,
                'timestamp' => $timestamp
            ]);
            
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'type' => $type
            ]);
            return 1;
        }
        
        return 0;
    }

    /**
     * Create backup directory if it doesn't exist
     */
    private function createBackupDirectory(): void
    {
        $backupPath = storage_path('app/backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
    }

    /**
     * Create full database backup
     */
    private function createFullBackup(string $filename): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port");
        
        $backupPath = storage_path("app/backups/{$filename}");
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("mysqldump failed with return code: {$returnCode}");
        }
    }

    /**
     * Create structure-only backup
     */
    private function createStructureBackup(string $filename): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port");
        
        $backupPath = storage_path("app/backups/{$filename}");
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --no-data --routines --triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("mysqldump failed with return code: {$returnCode}");
        }
    }

    /**
     * Create data-only backup
     */
    private function createDataBackup(string $filename): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port");
        
        $backupPath = storage_path("app/backups/{$filename}");
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --no-create-info --skip-triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("mysqldump failed with return code: {$returnCode}");
        }
    }

    /**
     * Clean old backup files (keep last 30 days)
     */
    private function cleanOldBackups(): void
    {
        $backupPath = storage_path('app/backups');
        $cutoffDate = Carbon::now()->subDays(30);
        
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/backup_*.sql');
            
            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));
                
                if ($fileTime->lt($cutoffDate)) {
                    unlink($file);
                    $this->info("Deleted old backup: " . basename($file));
                }
            }
        }
    }
}
