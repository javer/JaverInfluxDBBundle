<?php

namespace Javer\InfluxDB\Bundle\Repository;

use Javer\InfluxDB\ODM\Repository\MeasurementRepository;

/**
 * @template T of object
 * @template-extends MeasurementRepository<T>
 */
class ServiceMeasurementRepository extends MeasurementRepository implements ServiceMeasurementRepositoryInterface
{
}
