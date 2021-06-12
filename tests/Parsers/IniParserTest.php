<?php namespace Tests\Config\Parsers;

use Framework\Config\Parsers\IniParser;

final class IniParserTest extends ParserTestCase
{
	protected mixed $config = __DIR__ . '/../configs/config.ini';
	protected string $parserClass = IniParser::class;
}
