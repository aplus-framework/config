<?php namespace Tests\Config\Parsers;

use Framework\Config\Parsers\YamlParser;

class YamlParserTest extends ParserTestCase
{
	protected mixed $config = __DIR__ . '/../configs/config.yaml';
	protected string $parserClass = YamlParser::class;
}
