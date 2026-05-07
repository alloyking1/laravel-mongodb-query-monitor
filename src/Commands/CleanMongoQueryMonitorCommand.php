<?php

namespace MosesAnu\MongoQueryMonitor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class CleanMongoQueryMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo-query-monitor:clean
                            {--days= : (optional) Data older than this number of days will be removed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old records from the performance logs collection data.';

    /**
     * Removes old records from the performance logs collection based on the specified or default number of days.
     */
    public function handle(): void
    {
        $days = $this->option('days') ?: config('query-monitor.clean_after_days', 30);
        // Convert seconds to milliseconds for MongoDB UTCDateTime
        $threshold = new UTCDateTime(now()->subDays($days)->getTimestamp() * 1000);

        DB::connection('mongodb')
            ->getMongoDB()
            ->selectCollection('performance_logs')
            ->deleteMany([
                'created_at' => [
                    '$lt' => $threshold,
                ],
            ]);
    }
}
