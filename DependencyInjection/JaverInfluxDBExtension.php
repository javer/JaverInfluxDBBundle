<?php

namespace Javer\InfluxDB\Bundle\DependencyInjection;

use Javer\InfluxDB\Bundle\Repository\ServiceMeasurementRepositoryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JaverInfluxDBExtension extends Extension
{
    private const REPOSITORY_SERVICE_TAG = 'influxdb.repository_service';

    /**
     * Loads a specific configuration.
     *
     * @param mixed[]          $configs
     * @param ContainerBuilder $container
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

        $managerDefinition = $container->getDefinition('javer_influxdb.odm.measurement_manager');

        if (!empty($config['types'])) {
            $managerDefinition->addMethodCall('loadTypes', [$config['types']]);
        }

        if ($config['mapping_type'] === 'attribute') {
            $managerDefinition->replaceArgument(0, new Reference('javer_influxdb.odm.mapping.driver.attribute'));
        }

        $container->registerForAutoconfiguration(ServiceMeasurementRepositoryInterface::class)
            ->addTag(self::REPOSITORY_SERVICE_TAG);
    }
}
