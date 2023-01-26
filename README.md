JaverInfluxDBBundle
===================

This bundle integrates the [InfluxDB Object Document Mapper (ODM) library](https://github.com/javer/influxdb-odm)
into Symfony so that you can persist and retrieve objects to and from InfluxDB.

[![Build Status](https://secure.travis-ci.org/javer/JaverInfluxDBBundle.png?branch=master)](http://travis-ci.org/javer/JaverInfluxDBBundle)

Compatibility
=============

The current version of this bundle has the following requirements:
* InfluxDB 1.x
* PHP 8.1+
* Symfony 4.4+

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require javer/influxdb-odm-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require javer/influxdb-odm-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Javer\InfluxDB\Bundle\JaverInfluxDBBundle::class => ['all' => true],
];
```

Configuration
=============

```yaml
# config/packages/javer_influx_db.yaml
javer_influx_db:
    url: '%env(INFLUXDB_URL)%'
    mapping_dir: '%kernel.project_dir%/src/Measurement'
```

```ini
# .env
INFLUXDB_URL=influxdb://localhost:8086/metrics
```

Mapping Configuration
---------------------

Explicit definition of all the mapped measurements is the only necessary configuration for the ODM. 
The following configuration options exist for a mapping:
* `mapping_dir` - Path to the measurement files

To avoid having to configure lots of information for your mappings you should put all your measurement
in a directory ``Measurement/`` inside your project. For example ``src/Measurement/``.

Custom Types
------------

`Custom types` can come in handy when you're missing a specific mapping type or when you want to replace
the existing implementation of a mapping type for your measurements.

```yaml
# config/packages/javer_influx_db.yaml
javer_influx_db:
    types:
        custom_type: Fully\Qualified\Class\Name
```

Usage
=====

Refer to the [influxdb-odm library](https://github.com/javer/influxdb-odm) documentation
about declaring, creating, updating and removing measurements.

MeasurementManager
------------------

Inject `Javer\InfluxDB\ODM\MeasurementManager` to any service which needs ability to create,
update or remove measurements:

```php
use App\Measurement\CpuLoad;
use Javer\InfluxDB\ODM\MeasurementManager;

public function demoAction(MeasurementManager $measurementManager)
{
    $now = new DateTime();

    // Create
    $cpuLoad = new CpuLoad();
    $cpuLoad->setTime($now);
    $cpuLoad->setServerId(42);
    $cpuLoad->setCoreNumber(0);
    $cpuLoad->setLoad(3.14);
    $measurementManager->persist($cpuLoad);

    // Fetch
    $cpuLoad = $measurementManager->getRepository(CpuLoad::class)->find($now);

    // Update
    $cpuLoad->setLoad(2.54);
    $measurementManager->persist($cpuLoad);

    // Remove
    $measurementManager->remove($cpuLoad);
}
```

Service Repositories
--------------------

This bundle adds another way of obtaining a repository instance:
use the repository as a service and inject it as a dependency into other services.

```php
// src/Repository/CpuLoadRepository.php
namespace App\Repository;

use App\Measurement\CpuLoad;
use Javer\InfluxDB\Bundle\Repository\ServiceMeasurementRepository;
use Javer\InfluxDB\ODM\MeasurementManager;

/**
 * Remember to map this repository in the corresponding measurement repositoryClass.
 */
class CpuLoadRepository extends ServiceMeasurementRepository
{
    public function __construct(MeasurementManager $measurementManager)
    {
        parent::__construct($measurementManager, CpuLoad::class);
    }
}
```

The `ServiceMeasurementRepository` class your custom repository is extending allows you to
leverage Symfony's `autowiring` and `autoconfiguration`. To register all of your
repositories as services you can use the following service configuration:

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\Repository\:
        resource: '../src/Repository/*'
```

Symfony Profiler Data Collector
-------------------------------

This bundle adds a new icon to the Symfony Profiler Toolbar and a new Symfony Profiler Page to enable you to monitor
all queries and writes to InfluxDB. It is enabled by default only in development environment.
