<?php

namespace Javer\InfluxDB\Bundle\DataCollector;

use Javer\InfluxDB\Bundle\Logger\InfluxDBLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class InfluxDBDataCollector extends DataCollector
{
    private InfluxDBLogger $logger;

    public function __construct(InfluxDBLogger $logger)
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
        $this->data = [
            'queries' => $this->logger->getQueries(),
            'queriesCount' => $this->logger->getQueriesCount(),
            'queriesRows' => $this->logger->getQueriesRows(),
            'queriesTime' => $this->logger->getQueriesTime(),
            'errorsCount' => $this->logger->getErrorsCount(),
        ];
    }

    /**
     * Returns an array of queries.
     *
     * @return array<array{query: string, rows: int, time: float, error: ?string}>
     */
    public function getQueries(): array
    {
        return $this->data['queries'];
    }

    public function getQueriesCount(): int
    {
        return $this->data['queriesCount'];
    }

    public function getQueriesRows(): int
    {
        return $this->data['queriesRows'];
    }

    public function getQueriesTime(): float
    {
        return $this->data['queriesTime'];
    }

    public function getErrorsCount(): int
    {
        return $this->data['errorsCount'];
    }

    public function reset(): void
    {
        $this->data = [];

        $this->logger->reset();
    }

    public function getName(): string
    {
        return 'influxdb';
    }
}
