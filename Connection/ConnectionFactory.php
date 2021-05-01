<?php

namespace Javer\InfluxDB\Bundle\Connection;

use InfluxDB\Client\Exception as ClientException;
use InfluxDB\Database;
use InfluxDB\Driver\UDP;
use Javer\InfluxDB\Bundle\Logger\InfluxDBLogger;
use Javer\InfluxDB\ODM\Connection\ConnectionFactoryInterface;

/**
 * Class ConnectionFactory
 *
 * @package Javer\InfluxDB\Bundle\Connection
 */
class ConnectionFactory implements ConnectionFactoryInterface
{
    private InfluxDBLogger $logger;

    private bool $logging;

    /**
     * @var array<string, Database>
     */
    private array $databases = [];

    /**
     * ConnectionFactory constructor.
     *
     * @param InfluxDBLogger $logger
     * @param boolean        $logging
     */
    public function __construct(InfluxDBLogger $logger, bool $logging)
    {
        $this->logger = $logger;
        $this->logging = $logging;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ClientException
     */
    public function createConnection(string $dsn): Database
    {
        if (isset($this->databases[$dsn])) {
            return $this->databases[$dsn];
        }

        $connParams = parse_url($dsn);

        if ($connParams === false) {
            throw new ClientException('Unable to parse the InfluxDB DSN');
        }

        if (!isset($connParams['scheme'])) {
            throw new ClientException('Scheme must be specified in InfluxDB DSN');
        }

        if (!isset($connParams['host'])) {
            throw new ClientException('Host must be specified in InfluxDB DSN');
        }

        if (!isset($connParams['port'])) {
            throw new ClientException('Port must be specified in InfluxDB DSN');
        }

        if (!isset($connParams['path'])) {
            throw new ClientException('Database name must be specified in InfluxDB DSN');
        }

        $schemeInfo = explode('+', $connParams['scheme']);
        $dbName = null;
        $modifier = null;
        $scheme = $schemeInfo[0];

        if (isset($schemeInfo[1])) {
            $modifier = strtolower($schemeInfo[0]);
            $scheme = $schemeInfo[1];
        }

        if ($scheme !== 'influxdb') {
            throw new ClientException($scheme . ' is not a valid scheme');
        }

        $ssl = $modifier === 'https';
        $dbName = isset($connParams['path']) ? substr($connParams['path'], 1) : null;

        $client = new ConnectionWrapper(
            $connParams['host'],
            $connParams['port'],
            isset($connParams['user']) ? urldecode($connParams['user']) : '',
            isset($connParams['pass']) ? urldecode($connParams['pass']) : '',
            $ssl
        );

        // set the UDP driver when the DSN specifies UDP
        if ($modifier === 'udp') {
            $client->setDriver(new UDP($connParams['host'], $connParams['port']));
        }

        if ($this->logging) {
            $client->setLogger($this->logger);
        }

        $database = $client->selectDB($dbName);

        $this->databases[$dsn] = $database;

        return $database;
    }
}
