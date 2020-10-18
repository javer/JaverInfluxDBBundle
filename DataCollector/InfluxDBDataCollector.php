<?php

namespace Javer\InfluxDB\Bundle\DataCollector;

use Javer\InfluxDB\Bundle\Logger\InfluxDBLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

/**
 * Class InfluxDBDataCollector
 *
 * @package Javer\InfluxDB\Bundle\DataCollector
 */
class InfluxDBDataCollector extends DataCollector
{
    private InfluxDBLogger $logger;

    /**
     * InfluxDBDataCollector constructor.
     *
     * @param InfluxDBLogger $logger
     */
    public function __construct(InfluxDBLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
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
     * @return array
     */
    public function getQueries(): array
    {
        return $this->data['queries'];
    }

    /**
     * Returns queries count.
     *
     * @return integer
     */
    public function getQueriesCount(): int
    {
        return $this->data['queriesCount'];
    }

    /**
     * Returns queries rows.
     *
     * @return integer
     */
    public function getQueriesRows(): int
    {
        return $this->data['queriesRows'];
    }

    /**
     * Returns queries time.
     *
     * @return float
     */
    public function getQueriesTime(): float
    {
        return $this->data['queriesTime'];
    }

    /**
     * Returns errors count.
     *
     * @return integer
     */
    public function getErrorsCount(): int
    {
        return $this->data['errorsCount'];
    }

    /**
     * Resets internal state.
     */
    public function reset(): void
    {
        $this->data = [];

        $this->logger->reset();
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'influxdb';
    }
}
