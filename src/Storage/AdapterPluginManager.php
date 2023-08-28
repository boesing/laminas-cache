<?php

namespace Laminas\Cache\Storage;

use Laminas\ServiceManager\AbstractSingleInstancePluginManager;

/**
 * Plugin manager implementation for cache storage adapters
 *
 * Enforces that adapters retrieved are instances of
 * StorageInterface. Additionally, it registers a number of default
 * adapters available.
 *
 * @template-extends AbstractSingleInstancePluginManager<StorageInterface>
 */
final class AdapterPluginManager extends AbstractSingleInstancePluginManager
{
    /**
     * Do not share by default
     */
    protected bool $sharedByDefault = false;

    /** @var class-string */
    protected string $instanceOf = StorageInterface::class;
}
