<?php namespace Tests\Config\Parsers;

use Framework\Config\Parsers\EnvParser;

class EnvParserTest extends ParserTestCase
{
	protected mixed $config = __DIR__ . '/../configs/config.env';
	protected string $parserClass = EnvParser::class;
}
