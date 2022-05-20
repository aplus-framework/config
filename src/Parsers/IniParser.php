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
 * Class IniParser.
 *
 * @package config
 */
class IniParser extends Parser
{
    /**
     * Parses an INI file.
     *
     * @param mixed $config path to the INI file
     *
     * @throws ParserException
     *
     * @return array<mixed> The INI parsed data
     */
    public static function parse(mixed $config) : array
    {
        static::checkConfig($config);
        return static::parseOrThrow(static function () use ($config) : array {
            $parsed = \parse_ini_file($config, true, \INI_SCANNER_TYPED);
            $data = [];
            // @phpstan-ignore-next-line
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
        });
    }
}
