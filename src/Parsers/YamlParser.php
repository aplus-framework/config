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
    public static function parse(mixed $config) : array | false
    {
        static::checkConfig($config);
        $data = \yaml_parse_file($config);
        return static::ksortRecursive($data);
    }
}
