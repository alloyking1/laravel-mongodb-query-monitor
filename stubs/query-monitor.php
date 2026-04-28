<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Record Only Slow Queries
    |--------------------------------------------------------------------------
    |
    | Here you may specify only slow queries should be recorded. This can help
    | reduce the amount of data stored and focus on performance issues. If
    | enabled, only queries that exceed the defined slow threshold will be
    | recorded otherwise all queries will be recorded.
    |
    */

    'record_only_slow_queries' => false,


    /*
    |--------------------------------------------------------------------------
    | Slow Query Threshold
    |--------------------------------------------------------------------------
    |
    | This value defines the threshold in milliseconds that determines whether
    | a query is considered slow. Queries that take longer than this duration
    | will be marked as slow. You are free to change this value.
    |
    */

    'slow_threshold' => 200
];
