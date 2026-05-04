<?php

namespace MosesAnu\MongoQueryMonitor\Services;

use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class QueryMonitorService
{
    protected array $queries = [];

    public function record(string $collection, string $operation, float $duration): void
    {
        $isSlow = $duration > config('query-monitor.slow_threshold', 200);

        if (config('query-monitor.record_only_slow_queries', false) && ! $isSlow) {
            return;
        }

        $this->queries[] = [
            'collection' => $collection,
            'operation' => $operation,
            'duration_ms' => $duration,
            'is_slow' => $isSlow
        ];
    }

    public function persist(string $route, float $requestDuration): void
    {
        foreach ($this->queries as $query) {

            DB::connection('mongodb')
            ->getMongoDB()
            ->selectCollection('performance_logs')
            ->insertOne([
                'route' => $route,
                'collection' => $query['collection'],
                'operation' => $query['operation'],
                'duration_ms' => $query['duration_ms'],
                'request_duration' => $requestDuration,
                'is_slow' => $query['is_slow'],
                'created_at' => new UTCDateTime()
            ]);
        }
    }
}