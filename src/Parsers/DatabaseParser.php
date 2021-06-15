<?php namespace Framework\Config\Parsers;

use Framework\Database\Database;
use LogicException;

/**
 * Class DatabaseParser.
 */
class DatabaseParser extends Parser
{
	public static function parse(mixed $config) : array | false
	{
		static::checkConfig($config);
		$db = new Database($config);
		$results = $db->select()
			->from($config['table'])
			->orderBy('key')
			->run()
			->fetchArrayAll();
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
		if ( ! \is_array($config)) {
			throw new LogicException(static::class . ' config must be an array');
		}
		if ( ! isset($config['username'])) {
			throw new LogicException('Config username not set');
		}
		if ( ! isset($config['schema'])) {
			throw new LogicException('Config schema not set');
		}
		if ( ! isset($config['table'])) {
			throw new LogicException('Config table not set');
		}
	}
}
