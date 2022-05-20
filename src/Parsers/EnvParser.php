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
 * Class EnvParser.
 *
 * @package config
 */
class EnvParser extends Parser
{
    /**
     * Parses an .ENV file.
     *
     * @param mixed $config Path to the .ENV file.
     *
     * @throws ParserException
     *
     * @return array<mixed> The .ENV parsed data
     */
    public static function parse(mixed $config) : array
    {
        static::checkConfig($config);
        return static::parseOrThrow(static function () use ($config) : array {
            $contents = \file_get_contents($config);
            $contents = \explode(\PHP_EOL, $contents); // @phpstan-ignore-line
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
        });
    }
}
