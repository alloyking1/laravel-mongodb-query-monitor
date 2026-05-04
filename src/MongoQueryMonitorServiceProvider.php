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
        $this->mergeConfigFrom(
            __DIR__.'/../config/query-monitor.php', 'query-monitor'
        );

        // Share one monitor instance per request lifecycle.
        $this->app->singleton(QueryMonitorService::class, function () {
            return new QueryMonitorService();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
             $this->publishes([
                __DIR__.'/../stubs/query-monitor.php' => config_path('query-monitor.php'),
            ], 'query-monitor-config');
        }

        Monitoring\addSubscriber(new MongoCommandSubscriber());
    }
}