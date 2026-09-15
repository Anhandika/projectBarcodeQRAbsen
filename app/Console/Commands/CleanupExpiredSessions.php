<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class CleanupExpiredSessions extends Command
{
    protected $signature = 'session:cleanup';

    protected $description = 'Remove expired sessions (database driver) or expired session files';

    public function handle(): int
    {
        $lifetime = (int) (config('session.lifetime', 120) * 60);
        $cutoff = time() - $lifetime;

        // Database session driver (SESSION_DRIVER=database on Railway) → prune expired rows.
        if (config('session.driver') === 'database') {
            try {
                $deleted = DB::table(config('session.table', 'sessions'))
                    ->where('last_activity', '<', $cutoff)
                    ->delete();

                $this->info("Session cleanup complete. Deleted expired DB sessions: {$deleted}");
            } catch (Throwable $exception) {
                report($exception);
                $this->error('Session DB cleanup failed: ' . $exception->getMessage());
                return self::FAILURE;
            }

            return self::SUCCESS;
        }

        // File session driver → remove expired sess_* files.
        $sessionPath = storage_path('framework/sessions');

        if (! File::exists($sessionPath)) {
            $this->info('Session directory does not exist.');
            return self::SUCCESS;
        }

        $deleted = 0;
        $remaining = 0;

        foreach (File::files($sessionPath) as $file) {
            if (! Str::startsWith($file->getFilename(), 'sess_')) {
                continue;
            }

            if ($cutoff > $file->getMTime()) {
                File::delete($file->getPathname());
                $deleted++;
            } else {
                $remaining++;
            }
        }

        $this->info("Session cleanup complete. Deleted: {$deleted}, Remaining: {$remaining}");
        return self::SUCCESS;
    }
}
