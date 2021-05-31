<?php namespace Framework\Config\Parsers;

class IniParser extends Parser
{
	public static function parse(mixed $config) : array | false
	{
		static::checkConfig($config);
		$parsed = \parse_ini_file($config, true, \INI_SCANNER_TYPED);
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
		return $data;
	}
}
