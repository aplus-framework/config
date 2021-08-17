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

use Framework\Config\Parsers\XmlParser;

final class XmlParserTest extends ParserTestCase
{
    protected mixed $config = __DIR__ . '/../configs/config.xml';
    protected string $parserClass = XmlParser::class;
}
