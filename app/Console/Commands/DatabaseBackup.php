<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--retention=7 : Number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-His') . ".sql";
        $path = storage_path("app/backups/" . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path("app/backups"))) {
            mkdir(storage_path("app/backups"), 0755, true);
        }

        // Get database config
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $database = config('database.connections.mysql.database');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        // Get mysqldump path from env or default
        $mysqldumpPath = env('MYSQLDUMP_PATH', 'mysqldump');

        // Construct command
        // Note: Using --column-statistics=0 for compatibility with some MySQL 8 versions
        $command = "\"{$mysqldumpPath}\" --user=\"{$username}\" --password=\"{$password}\" --host=\"{$host}\" --port=\"{$port}\" \"{$database}\" > \"{$path}\"";

        // Mask password for display
        $displayCommand = str_replace($password, '********', $command);
        $this->info("Running backup command: {$displayCommand}");

        $returnVar = null;
        $output = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Backup failed with exit code: {$returnVar}");
            $this->error("Make sure mysqldump is installed and accessible.");
            return 1;
        }

        $this->info("Backup successfully created: {$path}");

        // Handle Retention Policy
        $this->cleanupOldBackups();

        return 0;
    }

    private function cleanupOldBackups()
    {
        $days = (int)$this->option('retention');
        $this->info("Cleaning up backups older than {$days} days...");

        $files = glob(storage_path("app/backups/backup-*.sql"));
        $deletedCount = 0;
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} old backup(s).");
        }
        else {
            $this->info("No old backups found to delete.");
        }
    }
}
