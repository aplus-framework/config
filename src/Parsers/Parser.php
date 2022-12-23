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

use Closure;
use JetBrains\PhpStorm\Pure;
use SensitiveParameter;

/**
 * Class Parser.
 *
 * @package config
 */
abstract class Parser
{
    /**
     * Parse the config output.
     *
     * @param mixed $config
     *
     * @throws ParserException If config is invalid or data can not be parsed
     *
     * @return array<int|string,mixed> The parsed configs
     */
    abstract public static function parse(mixed $config) : array;

    /**
     * @param Closure $function
     *
     * @throws ParserException If config data can not be parsed
     *
     * @return array<int|string,mixed>
     */
    protected static function parseOrThrow(Closure $function) : array
    {
        \set_error_handler(static function ($severity, $message, $file, $line) : void {
            $message = static::class . ': ' . $message;
            throw new ParserException($message, $severity, $severity, $file, $line);
        });
        $result = $function();
        \restore_error_handler();
        return $result;
    }

    /**
     * Check for config issues.
     *
     * @param mixed $config The parser configuration
     *
     * @throws ParserException If config is invalid
     */
    protected static function checkConfig(#[SensitiveParameter] mixed $config) : void
    {
        if ( ! \is_string($config)) {
            throw new ParserException(static::class . ' config must be a string');
        }
        $file = \realpath($config);
        if ($file === false || ! \is_file($file)) {
            throw new ParserException('File not found: ' . $config);
        }
        if ( ! \is_readable($file)) {
            throw new ParserException('File is not readable: ' . $config);
        }
    }

    /**
     * Recursively adds childs to an array tree.
     *
     * @param array<int|string,mixed> $parent The main array, where the childs will be added
     * @param array<int|string,mixed> $childs Childs to add
     * @param mixed $value The last child value
     */
    protected static function addChild(array &$parent, array $childs, mixed $value) : void
    {
        $key = \array_shift($childs);
        $parent[$key] = [];
        if ($childs === []) {
            $parent[$key] = $value;
            return;
        }
        static::addChild($parent[$key], $childs, $value);
    }

    /**
     * Interprets a string value and returns it with a PHP type.
     *
     * @param string $value The input value
     *
     * @return array<int|string,mixed>|bool|float|int|string|null The output value
     */
    #[Pure]
    protected static function getValue(string $value) : array|bool|int|float|string|null
    {
        $value = \trim($value);
        $lowerValue = \strtolower($value);
        if ($lowerValue === 'true') {
            return true;
        }
        if ($lowerValue === 'false') {
            return false;
        }
        if ($lowerValue === 'null') {
            return null;
        }
        if (\is_numeric($value) && $value >= \PHP_INT_MIN && $value <= \PHP_INT_MAX) {
            return \str_contains($value, '.') ? (float) $value : (int) $value;
        }
        if (\str_starts_with($value, '"') && \str_ends_with($value, '"')) {
            $value = \substr($value, 1, -1);
            return \strtr($value, [
                '\"' => '"',
                '\\\\' => '\\',
            ]);
        }
        if (\str_starts_with($value, "'") && \str_ends_with($value, "'")) {
            return \substr($value, 1, -1);
        }
        return $value;
    }

    /**
     * Sort arrays by keys recursively.
     *
     * @param mixed $value The input value
     *
     * @return mixed The output value (sorted by keys if the $value is an array)
     */
    protected static function ksortRecursive(mixed $value) : mixed
    {
        if ( ! \is_array($value)) {
            return $value;
        }
        \ksort($value);
        foreach ($value as &$val) {
            $val = static::ksortRecursive($val);
        }
        return $value;
    }
}
