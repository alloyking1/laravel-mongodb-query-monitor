<?php

namespace MosesAnu\MongoQueryMonitor\Services;

use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class QueryMonitorService
{
    protected array $queries = [];
    protected int $slowThreshold = 200;

    public function record(string $collection, string $operation, float $duration): void
    {
        $this->queries[] = [
            'collection' => $collection,
            'operation' => $operation,
            'duration_ms' => $duration,
            'is_slow' => $duration > $this->slowThreshold
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