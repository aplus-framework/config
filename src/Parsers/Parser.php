<?php declare(strict_types=1);
/*
 * This file is part of The Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Config\Parsers;

use LogicException;

/**
 * Class Parser.
 */
abstract class Parser
{
	/**
	 * Parse the config output.
	 *
	 * @param mixed $config
	 *
	 * @throws LogicException if config has error
	 *
	 * @return array<int|string,mixed>|false Array on success, otherwise false
	 */
	abstract public static function parse(mixed $config) : array | false;

	/**
	 * Check for config issues.
	 *
	 * @param mixed $config The parser configuration
	 *
	 * @throws LogicException if config is invalid
	 */
	protected static function checkConfig(mixed $config) : void
	{
		if ( ! \is_string($config)) {
			throw new LogicException(static::class . ' config must be a string');
		}
		$file = \realpath($config);
		if ($file === false || ! \is_file($file)) {
			throw new LogicException('File not found: ' . $config);
		}
	}

	/**
	 * Recursively adds childs to an array tree.
	 *
	 * @param array<int|string,mixed> $parent The main array, where the childs will be added
	 * @param array<int|string,mixed> $childs Childs to add
	 * @param mixed $value The last child value
	 */
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

	/**
	 * Interprets a string value and returns it with a PHP type.
	 *
	 * @param string $value The input value
	 *
	 * @return array<int|string,mixed>|bool|float|int|string|null The output value
	 */
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

	/**
	 * Sort arrays by keys recursively.
	 *
	 * @param mixed $value The input value
	 *
	 * @return mixed The output value (sorted by keys if the $value is an array)
	 */
	protected static function ksortRecursive(mixed $value) : mixed
	{
		if ( ! \is_array($value)) {
			return $value;
		}
		\ksort($value);
		foreach ($value as &$val) {
			$val = static::ksortRecursive($val);
		}
		return $value;
	}
}
