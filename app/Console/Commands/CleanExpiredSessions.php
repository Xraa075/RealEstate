<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean-expired {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired session files and optimize session storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        $lifetime = config('session.lifetime') * 60; // Convert minutes to seconds
        $expiredTime = Carbon::now()->subSeconds($lifetime);

        if (!File::exists($sessionPath)) {
            $this->error('Session storage path does not exist: ' . $sessionPath);
            return 1;
        }

        $files = File::files($sessionPath);
        $expiredCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            $fileTime = Carbon::createFromTimestamp(File::lastModified($file->getPathname()));

            if ($fileTime->lt($expiredTime)) {
                $fileSize = File::size($file->getPathname());
                $totalSize += $fileSize;

                if ($this->option('force') || $this->confirm("Delete expired session file: {$file->getFilename()}?", true)) {
                    File::delete($file->getPathname());
                    $expiredCount++;
                }
            }
        }

        $this->info("Cleaned {$expiredCount} expired session files.");
        $this->info("Freed " . $this->formatBytes($totalSize) . " of storage space.");

        // Also clean any orphaned session data
        $this->cleanOrphanedSessions();

        return 0;
    }

    /**
     * Clean orphaned session data
     */
    private function cleanOrphanedSessions()
    {
        // If using database sessions, clean expired records
        if (config('session.driver') === 'database') {
            $deletedRows = DB::table('sessions')
                ->where('last_activity', '<', Carbon::now()->subMinutes(config('session.lifetime'))->timestamp)
                ->delete();

            if ($deletedRows > 0) {
                $this->info("Cleaned {$deletedRows} expired database session records.");
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
