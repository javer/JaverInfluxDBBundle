<?php

namespace Javer\InfluxDB\Bundle\Repository;

use Javer\InfluxDB\ODM\MeasurementManager;
use Javer\InfluxDB\ODM\Repository\MeasurementRepository;
use Javer\InfluxDB\ODM\Repository\RepositoryFactoryInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ServiceRepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * @var MeasurementRepository[]
     *
     * @phpstan-var array<string, MeasurementRepository>
     * @phpstan-ignore-next-line: Unable to specify T for MeasurementRepository because it is hashmap for all classes
     */
    private array $repositories = [];

    private ServiceLocator $repositoryLocator;

    public function __construct(ServiceLocator $repositoryLocator)
    {
        $this->repositoryLocator = $repositoryLocator;
    }

    public function getRepository(MeasurementManager $measurementManager, string $className): MeasurementRepository
    {
        $repositoryHash = $measurementManager->getClassMetadata($className)->getName();

        return $this->repositories[$repositoryHash]
            ?? ($this->repositories[$repositoryHash] = $this->getOrCreateRepository($measurementManager, $className));
    }

    /**
     * Gets or creates a new repository.
     *
     * @param MeasurementManager $measurementManager
     * @param string             $className
     *
     * @return MeasurementRepository
     *
     * @throws RuntimeException
     *
     * @phpstan-template T of object
     * @phpstan-param    class-string<T> $className
     * @phpstan-return   MeasurementRepository<T>
     */
    private function getOrCreateRepository(
        MeasurementManager $measurementManager,
        string $className
    ): MeasurementRepository
    {
        $classMetadata = $measurementManager->getClassMetadata($className);
        $repositoryClassName = $classMetadata->customRepositoryClassName ?? MeasurementRepository::class;

        if ($this->repositoryLocator->has($repositoryClassName)) {
            $repository = $this->repositoryLocator->get($repositoryClassName);

            if (!$repository instanceof MeasurementRepository) {
                throw new RuntimeException(sprintf(
                    'The service "%s" must implement MeasurementRepository',
                    $repositoryClassName
                ));
            }

            if ($repository->getClassName() !== $className) {
                throw new RuntimeException(sprintf(
                    'The service "%s" must be MeasurementRepository for class "%s"',
                    $repositoryClassName,
                    $className
                ));
            }

            return $repository;
        }

        return new $repositoryClassName($measurementManager, $className);
    }
}
