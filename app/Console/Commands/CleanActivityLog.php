<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class CleanActivityLog extends Command
{
    protected $signature = 'activitylog:clean-custom';

    protected $description = 'Clean activity log records based on retention days from settings';

    public function handle(): int
    {
        $days = (int) (app('settings')['activity_log_retention_days'] ?? 365);

        if ($days <= 0) {
            $this->info('Activity log retention is disabled (0 or invalid value). Skipping cleanup.');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days);

        $count = Activity::where('created_at', '<', $cutoff)->count();

        Activity::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} activity log record(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
