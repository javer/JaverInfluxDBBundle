<?php

namespace Javer\InfluxDB\Bundle\DependencyInjection;

use Javer\InfluxDB\Bundle\Repository\ServiceMeasurementRepositoryInterface;
use Javer\InfluxDB\ODM\MeasurementManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class JaverInfluxDBExtension
 *
 * @package Javer\InfluxDB\Bundle\DependencyInjection
 */
class JaverInfluxDBExtension extends Extension
{
    private const REPOSITORY_SERVICE_TAG = 'influxdb.repository_service';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('javer_influxdb.url', $config['url']);
        $container->setParameter('javer_influxdb.mapping_dir', $config['mapping_dir']);
        $container->setParameter('javer_influxdb.logging', $config['logging']);

        if (!empty($config['types'])) {
            $managerDefinition = $container->getDefinition(MeasurementManager::class);
            $managerDefinition->addMethodCall('loadTypes', [$config['types']]);
        }

        $container->registerForAutoconfiguration(ServiceMeasurementRepositoryInterface::class)
            ->addTag(self::REPOSITORY_SERVICE_TAG);
    }
}
