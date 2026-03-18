<?php

namespace MosesAnu\MongoQueryMonitor\Monitoring;

use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MosesAnu\MongoQueryMonitor\Services\QueryMonitorService;

class MongoCommandSubscriber implements CommandSubscriber
{
    protected array $startTimes = [];
    protected array $operations = [];
    protected array $collections = [];

    public function commandStarted(CommandStartedEvent $event): void
    {
        $requestId = $event->getRequestId();

        $this->startTimes[$requestId] = microtime(true);

        $operation = $event->getCommandName();
        $command = get_object_vars($event->getCommand());
        $collection = $command[$operation] ?? 'unknown';

        $this->operations[$requestId] = $operation;
        $this->collections[$requestId] = $collection;
    }

    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        $requestId = $event->getRequestId();

        if (!isset($this->startTimes[$requestId])) {
            return;
        }

        $duration = (microtime(true) - $this->startTimes[$requestId]) * 1000;

        $operation = $this->operations[$requestId] ?? $event->getCommandName();
        $collection = $this->collections[$requestId] ?? 'unknown';

        $monitor = app(QueryMonitorService::class);

        $monitor->record(
            $collection,
            $operation,
            $duration
        );

        unset($this->startTimes[$requestId]);
        unset($this->operations[$requestId]);
        unset($this->collections[$requestId]);
    }

    public function commandFailed(CommandFailedEvent $event): void
    {
        $requestId = $event->getRequestId();

        unset($this->startTimes[$requestId]);
        unset($this->operations[$requestId]);
        unset($this->collections[$requestId]);
    }
}