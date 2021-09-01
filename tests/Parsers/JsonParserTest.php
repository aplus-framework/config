<?php
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Config\Parsers;

use Framework\Config\Parsers\JsonParser;
use Framework\Config\Parsers\ParserException;

final class JsonParserTest extends ParserTestCase
{
    protected mixed $config = __DIR__ . '/../configs/config.json';
    protected string $parserClass = JsonParser::class;

    public function testParseException() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches(
            '#^' . \strtr(JsonParser::class, ['\\' => '\\\\']) . ': Syntax error#'
        );
        JsonParser::parse(__FILE__);
    }
}
