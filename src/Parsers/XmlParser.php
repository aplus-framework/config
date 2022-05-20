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

use Framework\Config\Parsers\Extra\JsonXMLElement;

/**
 * Class XmlParser.
 *
 * @package config
 */
class XmlParser extends Parser
{
    /**
     * Parses an XML file.
     *
     * @param mixed $config path to the XML file
     *
     * @throws ParserException
     *
     * @return array<mixed> The XML parsed data
     */
    public static function parse(mixed $config) : array
    {
        static::checkConfig($config);
        return static::parseOrThrow(static function () use ($config) : array {
            $config = \file_get_contents($config);
            $config = \simplexml_load_string($config, JsonXMLElement::class); // @phpstan-ignore-line
            $config = \json_encode($config);
            $config = \json_decode($config, true); // @phpstan-ignore-line
            $data = [];
            foreach ($config as $instance => $values) {
                foreach ($values as &$value) {
                    $value = static::parseValue($value);
                }
                unset($value);
                $data[$instance] = $values;
            }
            return static::ksortRecursive($data);
        });
    }

    /**
     * @param array<int|string,mixed>|string $value
     *
     * @return array<int|string,mixed>|bool|float|int|string|null
     */
    protected static function parseValue(array|string $value) : array|bool|int|float|string|null
    {
        if (\is_array($value)) {
            foreach ($value as &$val) {
                $val = static::parseValue($val);
            }
            return $value;
        }
        return static::getValue($value);
    }
}
