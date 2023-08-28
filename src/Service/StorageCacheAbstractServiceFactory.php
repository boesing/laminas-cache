<?php

namespace Laminas\Cache\Service;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function assert;
use function is_array;

/**
 * Storage cache factory for multiple caches.
 */
class StorageCacheAbstractServiceFactory implements AbstractFactoryInterface
{
    public const CACHES_CONFIGURATION_KEY = 'caches';

    /** @var array<string,mixed>|null */
    protected ?array $config = null;

    /**
     * Configuration key for cache objects
     *
     * @var non-empty-string
     */
    protected string $configKey = self::CACHES_CONFIGURATION_KEY;

    public function canCreate(ContainerInterface $container, string $requestedName): bool
    {
        $config = $this->getConfig($container);
        if (empty($config)) {
            return false;
        }
        return isset($config[$requestedName]) && is_array($config[$requestedName]);
    }

    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): object
    {
        $config  = $this->getConfig($container);
        $factory = $container->get(StorageAdapterFactoryInterface::class);
        assert($factory instanceof StorageAdapterFactoryInterface);
        $configForRequestedName = $config[$requestedName] ?? [];
        Assert::isMap($configForRequestedName);
        $factory->assertValidConfigurationStructure($configForRequestedName);
        return $factory->createFromArrayConfiguration($configForRequestedName);
    }

    /**
     * Retrieve cache configuration, if any
     */
    protected function getConfig(ContainerInterface $container): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (! $container->has('config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $container->get('config');
        Assert::isArrayAccessible($config);
        if (! isset($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $cacheConfigurations = $config[$this->configKey];
        Assert::isMap($cacheConfigurations);

        return $this->config = $cacheConfigurations;
    }
}
