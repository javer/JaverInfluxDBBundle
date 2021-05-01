<?php

namespace Javer\InfluxDB\Bundle\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\ResetInterface;
use Throwable;

/**
 * Class InfluxDBLogger
 *
 * @package Javer\InfluxDB\Bundle\Logger
 */
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

    /**
     * InfluxDBLogger constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Logs a query.
     *
     * @param string         $query
     * @param integer        $numRows
     * @param float          $time
     * @param Throwable|null $exception
     */
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

    /**
     * Returns queries count.
     *
     * @return integer
     */
    public function getQueriesCount(): int
    {
        return $this->queriesCount;
    }

    /**
     * Returns queries rows.
     *
     * @return integer
     */
    public function getQueriesRows(): int
    {
        return $this->queriesRows;
    }

    /**
     * Returns queries time.
     *
     * @return float
     */
    public function getQueriesTime(): float
    {
        return $this->queriesTime;
    }

    /**
     * Returns errors count.
     *
     * @return integer
     */
    public function getErrorsCount(): int
    {
        return $this->errorsCount;
    }

    /**
     * Resets internal state.
     */
    public function reset(): void
    {
        $this->queries = [];
        $this->queriesCount = 0;
        $this->queriesRows = 0;
        $this->queriesTime = 0;
        $this->errorsCount = 0;
    }
}
