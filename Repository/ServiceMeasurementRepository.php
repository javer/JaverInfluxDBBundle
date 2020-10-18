<?php

namespace Javer\InfluxDB\Bundle\Repository;

use Javer\InfluxDB\ODM\Repository\MeasurementRepository;

/**
 * Class ServiceMeasurementRepository
 *
 * Optional MeasurementRepository base class with a simplified constructor (for autowiring).
 *
 * To use in your class, inject the "MeasurementManager" service and call the parent constructor. For example:
 *
 * class YourMeasurementRepository extends ServiceMeasurementRepository
 * {
 *     public function __construct(MeasurementManager $measurementManager)
 *     {
 *         parent::__construct($measurementManager, YourMeasurement::class);
 *     }
 * }
 *
 * @package Javer\InfluxDB\Bundle\Repository
 */
class ServiceMeasurementRepository extends MeasurementRepository implements ServiceMeasurementRepositoryInterface
{
}
