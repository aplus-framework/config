<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Config\Parsers;

use Framework\Database\Database;
use SensitiveParameter;

/**
 * Class DatabaseParser.
 *
 * @package config
 */
class DatabaseParser extends Parser
{
    /**
     * Get config from a database.
     *
     * @param mixed $config array with configs for database connection:
     * host, port, username, password, schema and table
     *
     * @throws ParserException
     *
     * @return array<mixed> The database parsed data
     */
    public static function parse(#[SensitiveParameter] mixed $config) : array
    {
        static::checkConfig($config);
        return static::parseOrThrow(static function () use ($config) : array {
            try {
                $database = new Database($config);
                $results = $database->select()
                    ->from($config['table'])
                    ->orderBy('key')
                    ->run()
                    ->fetchArrayAll();
            } catch (\Exception $exception) {
                throw new ParserException(
                    static::class . ': ' . $exception->getMessage(),
                    $exception->getCode(),
                    \E_ERROR,
                    $exception->getFile(),
                    $exception->getLine()
                );
            }
            $data = [];
            foreach ($results as $row) {
                $key = \explode('.', $row['key']);
                $value = static::getValue($row['value']);
                $parent = [];
                static::addChild($parent, $key, $value);
                $data = \array_replace_recursive($data, $parent);
            }
            return $data;
        });
    }

    protected static function checkConfig(#[SensitiveParameter] mixed $config) : void
    {
        if ( ! \is_array($config)) {
            throw new ParserException(static::class . ' config must be an array');
        }
        if ( ! isset($config['username'])) {
            throw new ParserException(static::class . ' config username not set');
        }
        if ( ! isset($config['schema'])) {
            throw new ParserException(static::class . ' config schema not set');
        }
        if ( ! isset($config['table'])) {
            throw new ParserException(static::class . ' config table not set');
        }
    }
}
