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

/**
 * Class JsonParser.
 *
 * @package config
 */
class JsonParser extends Parser
{
    /**
     * Parses a JSON file.
     *
     * @param mixed $config path to the JSON file
     *
     * @throws ParserException
     *
     * @return array<mixed> The JSON parsed data
     */
    public static function parse(mixed $config) : array
    {
        static::checkConfig($config);
        return static::parseOrThrow(static function () use ($config) : array {
            $config = \file_get_contents($config);
            try {
                $data = \json_decode($config, true, 512, \JSON_THROW_ON_ERROR); // @phpstan-ignore-line
            } catch (\Exception $exception) {
                throw new ParserException(
                    static::class . ': ' . $exception->getMessage(),
                    $exception->getCode(),
                    \E_ERROR,
                    $exception->getFile(),
                    $exception->getLine()
                );
            }
            return static::ksortRecursive($data);
        });
    }
}
