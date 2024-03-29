<?php

namespace Javer\InfluxDB\Bundle\Connection;

use InfluxDB\Client;
use InfluxDB\ResultSet;
use Javer\InfluxDB\Bundle\Logger\InfluxDBLogger;
use Throwable;

class ConnectionWrapper extends Client
{
    private ?InfluxDBLogger $logger = null;

    public function setLogger(?InfluxDBLogger $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Throwable
     *
     * @phpstan-param array<string, mixed> $parameters
     */
    public function query($database, $query, $parameters = []): ResultSet
    {
        if (!$this->logger) {
            return parent::query($database, $query, $parameters);
        }

        $startTime = microtime(true);
        $result = null;
        $exception = null;

        try {
            $result = parent::query($database, $query, $parameters);

            return $result;
        } catch (Throwable $e) {
            $exception = $e;

            throw $e;
        } finally {
            $endTime = microtime(true);

            $this->logger->logQuery(
                $query,
                $result ? count($result->getPoints()) : 0,
                $endTime - $startTime,
                $exception
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @phpstan-param array<string, mixed> $parameters
     * @phpstan-param string|string[] $payload
     */
    public function write(array $parameters, $payload): bool
    {
        if (!$this->logger) {
            return parent::write($parameters, $payload);
        }

        $startTime = microtime(true);

        try {
            return parent::write($parameters, $payload);
        } finally {
            $endTime = microtime(true);

            if (is_array($payload)) {
                $query = implode("\n", $payload);
                $numRows = count($payload);
            } else {
                $query = $payload;
                $numRows = 1;
            }

            $this->logger->logQuery($query, $numRows, $endTime - $startTime);
        }
    }
}
