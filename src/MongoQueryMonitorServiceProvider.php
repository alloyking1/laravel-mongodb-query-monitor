<?php

namespace MosesAnu\MongoQueryMonitor;

use Illuminate\Support\ServiceProvider;
use MongoDB\Driver\Monitoring;
use MosesAnu\MongoQueryMonitor\Monitoring\MongoCommandSubscriber;
use MosesAnu\MongoQueryMonitor\Services\QueryMonitorService;

class MongoQueryMonitorServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Share one monitor instance per request lifecycle.
        $this->app->singleton(QueryMonitorService::class, function () {
            return new QueryMonitorService();
        });
    }

    public function boot()
    {
        Monitoring\addSubscriber(new MongoCommandSubscriber());
    }
}