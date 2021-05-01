<?php

namespace Javer\InfluxDB\Bundle\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\ResetInterface;
use Throwable;

class InfluxDBLogger implements ResetInterface
{
    private ?LoggerInterface $logger;

    /**
     * @var array<array{query: string, rows: int, time: float, error: ?string}>
     */
    private array $queries = [];

    private int $queriesCount = 0;

    private int $queriesRows = 0;

    private float $queriesTime = 0;

    private int $errorsCount = 0;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function logQuery(string $query, int $numRows, float $time, ?Throwable $exception = null): void
    {
        $this->queries[] = [
            'query' => $query,
            'rows' => $numRows,
            'time' => $time,
            'error' => $exception ? $exception->getMessage() : null,
        ];

        $this->queriesCount++;
        $this->queriesRows += $numRows;
        $this->queriesTime += $time;

        if ($exception) {
            $this->errorsCount++;
        }

        if ($this->logger) {
            $this->logger->debug($query);
        }
    }

    /**
     * Returns queries.
     *
     * @return array<array{query: string, rows: int, time: float, error: ?string}>
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getQueriesCount(): int
    {
        return $this->queriesCount;
    }

    public function getQueriesRows(): int
    {
        return $this->queriesRows;
    }

    public function getQueriesTime(): float
    {
        return $this->queriesTime;
    }

    public function getErrorsCount(): int
    {
        return $this->errorsCount;
    }

    public function reset(): void
    {
        $this->queries = [];
        $this->queriesCount = 0;
        $this->queriesRows = 0;
        $this->queriesTime = 0;
        $this->errorsCount = 0;
    }
}
