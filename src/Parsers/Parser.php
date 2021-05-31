<?php namespace Framework\Config\Parsers;

use LogicException;

abstract class Parser
{
	/**
	 * Parse the config output.
	 *
	 * @param mixed $config
	 *
	 * @throws \LogicException if config has error
	 *
	 * @return array|false Array on success, otherwise false
	 */
	abstract public static function parse(mixed $config) : array | false;

	protected static function checkConfig(mixed $config) : void
	{
		if ( ! \is_string($config)) {
			throw new LogicException(__CLASS__ . ' config must be a string');
		}
		$file = \realpath($config);
		if ($file === false || ! \is_file($file)) {
			throw new LogicException('File not found: ' . $config);
		}
	}

	protected static function addChild(array &$parent, array $childs, mixed $value) : void
	{
		$key = \array_shift($childs);
		$parent[$key] = [];
		if ($childs === []) {
			$parent[$key] = $value;
			return;
		}
		static::addChild($parent[$key], $childs, $value);
	}

	protected static function getValue(string $value) : array | bool | int | float | string | null
	{
		$value = \trim($value);
		$lower_value = \strtolower($value);
		if ($lower_value === 'true') {
			return true;
		}
		if ($lower_value === 'false') {
			return false;
		}
		if ($lower_value === 'null') {
			return null;
		}
		if (\is_numeric($value) && $value >= \PHP_INT_MIN && $value <= \PHP_INT_MAX) {
			return \str_contains($value, '.') ? (float) $value : (int) $value;
		}
		if (\str_starts_with($value, '"') && \str_ends_with($value, '"')) {
			$value = \substr($value, 1, -1);
			return \strtr($value, [
				'\"' => '"',
				'\\\\' => '\\',
			]);
		}
		if (\str_starts_with($value, "'") && \str_ends_with($value, "'")) {
			return \substr($value, 1, -1);
		}
		return $value;
	}
}
