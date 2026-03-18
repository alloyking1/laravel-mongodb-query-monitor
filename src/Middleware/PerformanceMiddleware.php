<?php

namespace MosesAnu\MongoQueryMonitor\Middleware;

use Closure;
use Illuminate\Http\Request;
use MosesAnu\MongoQueryMonitor\Services\QueryMonitorService;

class PerformanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000;

        $monitor = app(QueryMonitorService::class);

        $monitor->persist(
            $request->path(),
            $duration
        );

        return $response;
    }
}