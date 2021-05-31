<?php namespace Framework\Config\Parsers;

use Framework\Database\Database;
use LogicException;

class DatabaseParser extends Parser
{
	public static function parse(mixed $config) : array | false
	{
		static::checkConfig($config);
		$db = new Database($config);
		$results = $db->select()->from($config['table'])->run()->fetchArrayAll();
		$data = [];
		foreach ($results as $row) {
			$key = \explode('.', $row['key']);
			$value = static::getValue($row['value']);
			$parent = [];
			static::addChild($parent, $key, $value);
			$data = \array_replace_recursive($data, $parent);
		}
		return $data;
	}

	protected static function checkConfig(mixed $config) : void
	{
		if ( ! isset($config['user'])) {
			throw new LogicException('Config user not set');
		}
		if ( ! isset($config['schema'])) {
			throw new LogicException('Config schema not set');
		}
		if ( ! isset($config['table'])) {
			throw new LogicException('Config table not set');
		}
	}
}
