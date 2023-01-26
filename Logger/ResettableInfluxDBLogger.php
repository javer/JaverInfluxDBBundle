<?php

namespace Javer\InfluxDB\Bundle\Logger;

use Javer\InfluxDB\ODM\Logger\InfluxLogger;
use Javer\InfluxDB\ODM\Logger\InfluxLoggerInterface;
use Symfony\Contracts\Service\ResetInterface;
use Throwable;

final class ResettableInfluxDBLogger implements InfluxLoggerInterface, ResetInterface
{
    public function __construct(
        private readonly InfluxLogger $logger,
    )
    {
    }

    public function logQuery(string $query, float $time, int $rows = 1, ?Throwable $exception = null): void
    {
        $this->logger->logQuery($query, $time, $rows, $exception);
    }

    public function logWrite(string $query, float $time, int $rows = 1, ?Throwable $exception = null): void
    {
        $this->logger->logWrite($query, $time, $rows, $exception);
    }

    public function logDelete(string $query, float $time, int $rows = 1, ?Throwable $exception = null): void
    {
        $this->logger->logDelete($query, $time, $rows, $exception);
    }

    /**
     * {@inheritDoc}
     */
    public function getQueries(): array
    {
        return $this->logger->getQueries();
    }

    /**
     * {@inheritDoc}
     */
    public function getWrites(): array
    {
        return $this->logger->getWrites();
    }

    /**
     * {@inheritDoc}
     */
    public function getDeletions(): array
    {
        return $this->logger->getDeletions();
    }

    public function getQueriesCount(): int
    {
        return $this->logger->getQueriesCount();
    }

    public function getQueriesRows(): int
    {
        return $this->logger->getQueriesRows();
    }

    public function getQueriesTime(): float
    {
        return $this->logger->getQueriesTime();
    }

    public function getErrorsCount(): int
    {
        return $this->logger->getErrorsCount();
    }

    public function reset(): void
    {
        $this->logger->reset();
    }
}
