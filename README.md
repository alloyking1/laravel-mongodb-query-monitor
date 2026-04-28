# Laravel MongoDB Query Monitor

`laravel-mongodb-query-monitor` helps you inspect MongoDB performance in Laravel by recording:

- MongoDB operation name (`find`, `aggregate`, `insert`, etc.)
- Target collection
- Query duration in milliseconds
- Request duration in milliseconds
- Whether a query is considered slow
- Route path that triggered the query

The package writes logs to a MongoDB collection named `performance_logs`.

## How It Works

The package has two moving parts:

1. A MongoDB command subscriber listens to MongoDB driver events and measures query duration.
2. A Laravel middleware persists all collected query metrics at the end of each HTTP request.

If middleware is not applied, queries may be measured but they will not be saved.

## Requirements

- PHP `^8.2`
- Laravel application
- A working MongoDB connection configured in Laravel as `mongodb`
- A Laravel MongoDB driver that supports:
  - `DB::connection('mongodb')->getMongoDB()`

## Installation

Install via Composer:

```bash
composer require mosesanu/laravel-mongodb-query-monitor
```

Laravel package discovery will automatically register the service provider.

## Configuration

If you want to change configuration you can publish package's configuration file using the vendor:publish command:

```bash
php artisan vendor:publish --provider="MosesAnu\MongoQueryMonitor\MongoQueryMonitorServiceProvider"
```

If you are going to record only slow queries you must update your `config/query-monitor.php` file.

```bash
'record_only_slow_queries' => true,
```

## Usage

### 1. Ensure MongoDB Connection Exists

Your Laravel app must have a database connection named `mongodb`.

Example (`config/database.php`):

```php
'connections' => [
	// ...

	'mongodb' => [
		'driver' => 'mongodb',
		'dsn' => env('MONGODB_URI'),
		'database' => env('MONGODB_DATABASE', 'app'),
	],
],
```

### 2. Register the Middleware

Apply `PerformanceMiddleware` where you want monitoring to run.

Option A: Global middleware (all web requests)

```php
// app/Http/Kernel.php

protected $middleware = [
	// ...
	\MosesAnu\MongoQueryMonitor\Middleware\PerformanceMiddleware::class,
];
```

Option B: Route middleware (selected routes only)

```php
// app/Http/Kernel.php

protected $routeMiddleware = [
	// ...
	'mongo.monitor' => \MosesAnu\MongoQueryMonitor\Middleware\PerformanceMiddleware::class,
];
```

```php
// routes/web.php or routes/api.php

Route::middleware(['mongo.monitor'])->group(function () {
	Route::get('/reports', [ReportController::class, 'index']);
});
```

### 3. Trigger Queries

Hit routes that execute MongoDB operations. At response completion, logs are written to:

- Connection: `mongodb`
- Collection: `performance_logs`

## Logged Document Shape

Each captured query is stored as one MongoDB document:

```json
{
  "route": "api/reports",
  "collection": "reports",
  "operation": "find",
  "duration_ms": 14.23,
  "request_duration": 87.41,
  "is_slow": false,
  "created_at": "UTCDateTime"
}
```

## Slow Query Threshold

Queries are flagged as slow when duration is greater than `200ms`.

In the current version, this threshold is hard-coded in `QueryMonitorService`.

## Quick Verification

1. Enable middleware.
2. Call a route that runs MongoDB queries.
3. Inspect `performance_logs` in MongoDB.
4. Confirm documents include `route`, `collection`, `operation`, and duration fields.

## Notes and Limitations

- Monitoring is request-based and depends on middleware execution.
- Failed MongoDB commands are currently not persisted.
- CLI/queue jobs are not tracked unless you manually wire monitoring for those flows.
- No migration is required because logs are inserted directly into MongoDB collection `performance_logs`.

## Contributing

Contributions are welcome and appreciated.

- Open an issue for bugs, questions, or feature requests.
- Submit a pull request for fixes, tests, or improvements.
- Improve documentation and examples to help other users.

If this package helps you, please consider starring the repository to support the project and help others discover it.

## License

MIT
