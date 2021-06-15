<?php namespace Framework\Config;

use LogicException;

/**
 * Class Config.
 */
class Config
{
	/**
	 * @var array<int|string,mixed>
	 */
	protected array $configs = [];
	protected string $configsDir;
	/**
	 * @var array<string,mixed>
	 */
	protected array $persistence = [];
	protected string $suffix;

	/**
	 * Config constructor.
	 *
	 * @param string $directory The configs base directory
	 * @param array<string,mixed> $persistence Configs that always will overwrite
	 * custom added, loaded or set configs
	 * @param string $suffix The services filenames suffix
	 */
	public function __construct(
		string $directory,
		array $persistence = [],
		string $suffix = '.php'
	) {
		$this->setDir($directory);
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
	 * @return array<int|string,mixed> The service instance custom configs with persistent configs
	 */
	protected function getPersistentConfigs(string $name, string $instance) : array
	{
		$this->replacePersistence();
		return $this->configs[$name][$instance] ?? [];
	}

	/**
	 * Set configs to a service instance.
	 *
	 * NOTE: This configs will replace an existing instance (except persistent).
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
	 * @return array<int|string,mixed>|null The instance configs as array or null
	 * if is not set
	 */
	public function get(string $name, string $instance = 'default') : ?array
	{
		if (empty($this->configs[$name])) {
			$this->load($name);
		}
		return $this->configs[$name][$instance] ?? null;
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
	 * NOTE: This configs will replace existing instances.
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
	 */
	protected function setDir(string $directory) : void
	{
		$dir = \realpath($directory);
		if ($dir === false || ! \is_dir($dir)) {
			throw new LogicException('Config directory not found: ' . $directory);
		}
		$this->configsDir = $dir . \DIRECTORY_SEPARATOR;
	}

	/**
	 * Loads a config file.
	 *
	 * @param string $name the file name without the directory path and the suffix
	 */
	public function load(string $name) : void
	{
		$filename = $this->configsDir . $name . $this->suffix;
		$filename = \realpath($filename);
		if ($filename === false || ! \is_file($filename)) {
			throw new LogicException('Config file not found: ' . $name);
		}
		$configs = require_isolated($filename);
		$this->setMany([$name => $configs]);
	}
}
