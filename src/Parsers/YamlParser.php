<?php namespace Framework\Config\Parsers;

/**
 * Class YamlParser.
 */
class YamlParser extends Parser
{
	public static function parse(mixed $config) : array | false
	{
		static::checkConfig($config);
		$data = \yaml_parse_file($config);
		return static::ksortRecursive($data);
	}
}
