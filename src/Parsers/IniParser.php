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

/**
 * Class IniParser.
 */
class IniParser extends Parser
{
	public static function parse(mixed $config) : array | false
	{
		static::checkConfig($config);
		$parsed = \parse_ini_file($config, true, \INI_SCANNER_TYPED);
		if ($parsed === false) {
			return false;
		}
		$data = [];
		foreach ($parsed as $section => $values) {
			$data[$section] = [];
			foreach ($values as $key => $value) {
				$key = \explode('.', $key);
				$parent = [];
				static::addChild($parent, $key, $value);
				$data[$section] = \array_replace_recursive(
					$data[$section],
					$parent
				);
			}
		}
		return static::ksortRecursive($data);
	}
}
