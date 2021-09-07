<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Config;

use Framework\Helpers\Isolation;
use LogicException;

/**
 * Class Config.
 *
 * @package config
 */
class Config
{
    /**
     * @var array<int|string,mixed>
     */
    protected array $configs = [];
    protected ?string $configsDir = null;
    /**
     * @var array<string,mixed>
     */
    protected array $persistence = [];
    protected string $suffix;

    /**
     * Config constructor.
     *
     * @param array<string,mixed>|string|null $configs An array to set many
     * configs, the config base directory or null
     * @param array<string,mixed> $persistence Configs that will always overwrite
     * custom added, loaded or set configs
     * @param string $suffix The services filenames suffix used when the config
     * directory is set
     */
    public function __construct(
        array | string $configs = null,
        array $persistence = [],
        string $suffix = '.php'
    ) {
        if ($configs !== null) {
            \is_array($configs)
                ? $this->setMany($configs)
                : $this->setDir($configs);
        }
        $this->setPersistence($persistence);
        $this->suffix = $suffix;
    }

    /**
     * Set persistent configs.
     *
     * @param array<string,mixed> $configs
     */
    protected function setPersistence(array $configs) : void
    {
        $this->persistence = $configs;
    }

    /**
     * Replace configs with persistent configs.
     */
    protected function replacePersistence() : void
    {
        if (empty($this->persistence)) {
            return;
        }
        $this->configs = \array_replace_recursive($this->configs, $this->persistence);
    }

    /**
     * Get configs with persistence.
     *
     * @param string $name The service name
     * @param string $instance The service instance
     *
     * @return array<int|string,mixed> The service instance custom configs with
     * persistent configs
     */
    protected function getPersistentConfigs(string $name, string $instance) : array
    {
        $this->replacePersistence();
        return $this->configs[$name][$instance] ?? [];
    }

    /**
     * Set configs to a service instance.
     *
     * NOTE: These configs will replace an existing instance (except persistence).
     *
     * @param string $name The service name
     * @param array<int|string,mixed> $configs The new configs
     * @param string $instance The service instance
     *
     * @return array<int|string,mixed> The service instance configs
     */
    public function set(
        string $name,
        array $configs,
        string $instance = 'default'
    ) : array {
        $this->configs[$name][$instance] = $configs;
        return $this->getPersistentConfigs($name, $instance);
    }

    /**
     * Get configs by a service instance.
     *
     * @param string $name The service name
     * @param string $instance The service instance
     *
     * @throws LogicException If the service configs are empty and the Config
     * directory is set, and the config file is not found
     *
     * @return array<int|string,mixed>|null The instance configs as array or null
     * if is not set
     */
    public function get(string $name, string $instance = 'default') : ?array
    {
        if (empty($this->configs[$name]) && isset($this->configsDir)) {
            $this->load($name);
        }
        return $this->configs[$name][$instance] ?? null;
    }

    /**
     * Get service instances configs.
     *
     * @param string $name The service name
     *
     * @return array<string,array>|null The service instances configs
     */
    public function getInstances(string $name) : ?array
    {
        return $this->configs[$name] ?? null;
    }

    /**
     * Add configs to a service instance.
     *
     * NOTE: IF the service instance already exists, the configs will be merged
     *
     * @param string $name The service name
     * @param array<int|string,mixed> $configs The service configs
     * @param string $instance The service instance
     *
     * @return array<int|string,mixed> The service instance configs
     */
    public function add(string $name, array $configs, string $instance = 'default') : array
    {
        if (isset($this->configs[$name][$instance])) {
            $this->configs[$name][$instance] = \array_replace_recursive(
                $this->configs[$name][$instance],
                $configs
            );
            return $this->getPersistentConfigs($name, $instance);
        }
        return $this->set($name, $configs, $instance);
    }

    /**
     * Set many configs in one call.
     *
     * NOTE: The $configs will replace existing instances (except persistence).
     *
     * @param array<string,mixed> $configs
     */
    public function setMany(array $configs) : void
    {
        foreach ($configs as $name => $values) {
            foreach ($values as $instance => $config) {
                $this->set($name, $config, $instance);
            }
        }
    }

    /**
     * Get all configs.
     *
     * @return array <int|string,mixed>
     */
    public function getAll() : array
    {
        return $this->configs;
    }

    /**
     * Set the base directory.
     *
     * @param string $directory Directory path
     *
     * @throws LogicException If the config directory is not found
     *
     * @return static
     */
    public function setDir(string $directory) : static
    {
        $dir = \realpath($directory);
        if ($dir === false || ! \is_dir($dir)) {
            throw new LogicException('Config directory not found: ' . $directory);
        }
        $this->configsDir = $dir . \DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Get the base directory.
     *
     * @return string|null The directory realpath or null if it was not set
     */
    public function getDir() : ?string
    {
        return $this->configsDir;
    }

    /**
     * Loads a config file.
     *
     * @param string $name the file name without the directory path and the suffix
     *
     * @throws LogicException If the config file is not found
     */
    public function load(string $name) : void
    {
        $filename = $this->configsDir . $name . $this->suffix;
        $filename = \realpath($filename);
        if ($filename === false || ! \is_file($filename)) {
            throw new LogicException('Config file not found: ' . $name);
        }
        $configs = Isolation::require($filename);
        $this->setMany([$name => $configs]);
    }
}
