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
 * Class YamlParser.
 *
 * @package config
 */
class YamlParser extends Parser
{
    /**
     * Parses an YAML file.
     *
     * @param mixed $config path to the YAML file
     *
     * @throws ParserException
     *
     * @return array<mixed> The YAML parsed data
     */
    public static function parse(mixed $config) : array
    {
        static::checkConfig($config);
        return static::parseOrThrow(static function () use ($config) : array {
            $data = \yaml_parse_file($config);
            return static::ksortRecursive($data);
        });
    }
}
