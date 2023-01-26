<?php

namespace Javer\InfluxDB\Bundle\DataCollector;

use Javer\InfluxDB\ODM\Logger\InfluxLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

/**
 * @phpstan-type T array{query: string, time: float, rows: int, error?: string|null}
 */
final class InfluxDBDataCollector extends DataCollector
{
    public function __construct(
        private readonly InfluxLoggerInterface $logger,
    )
    {
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
        $this->data = [
            'queries' => $this->logger->getQueries(),
            'writes' => $this->logger->getWrites(),
            'deletions' => $this->logger->getDeletions(),
            'queriesCount' => $this->logger->getQueriesCount(),
            'queriesRows' => $this->logger->getQueriesRows(),
            'queriesTime' => $this->logger->getQueriesTime(),
            'errorsCount' => $this->logger->getErrorsCount(),
        ];
    }

    /**
     * Returns an array of queries.
     *
     * @return mixed[]
     *
     * @phpstan-return array<int, T>
     */
    public function getQueries(): array
    {
        return $this->data['queries'];
    }

    /**
     * Returns an array of writes.
     *
     * @return mixed[]
     *
     * @phpstan-return array<int, T>
     */
    public function getWrites(): array
    {
        return $this->data['writes'];
    }

    /**
     * Returns an array of deletions.
     *
     * @return mixed[]
     *
     * @phpstan-return array<int, T>
     */
    public function getDeletions(): array
    {
        return $this->data['deletions'];
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
    }

    public function getName(): string
    {
        return 'influxdb';
    }
}
