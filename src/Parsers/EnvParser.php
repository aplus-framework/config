<?php
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
 * Class EnvParser.
 */
class EnvParser extends Parser
{
	public static function parse(mixed $config) : array | false
	{
		static::checkConfig($config);
		$contents = \file_get_contents($config);
		if ($contents === false) {
			return false;
		}
		$contents = \explode(\PHP_EOL, $contents);
		$data = [];
		foreach ($contents as $line) {
			$line = \trim($line);
			if ($line === '' || \str_starts_with($line, '#')) {
				continue;
			}
			[$key, $value] = \explode('=', $line, 2);
			$key = \trim($key);
			$key = \explode('.', $key);
			$value = static::getValue($value);
			$parent = [];
			static::addChild($parent, $key, $value);
			$data = \array_replace_recursive($data, $parent);
		}
		return static::ksortRecursive($data);
	}
}
