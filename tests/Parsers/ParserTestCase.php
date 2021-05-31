<?php namespace Tests\Config\Parsers;

use PHPUnit\Framework\TestCase;

abstract class ParserTestCase extends TestCase
{
	protected mixed $config;
	protected string $parserClass;

	public function testParse()
	{
		$this->assertEquals([
			'service1' => [
				'default' => [
					'string1' => 'string1',
					'string2' => 'string2',
					'string3' => 'string3',
					'int' => 123,
					'float' => 1.5,
					'true' => true,
					'false' => false,
					'null' => null,
					'array' => [
						0 => true,
						1 => false,
						2 => null,
						3 => 'string',
						4 => 123,
						5 => 1.5,
					],
				],
				'other' => [
					'quot' => 'quot"1"23',
					'apos' => "apos'4'56",
					'mix' => 'null 123 \\',
				],
			],
			'service2' => [
				'default' => [
					'array' => [
						0 => 'True',
						1 => 'False',
						2 => 'Null',
						3 => 'string',
						4 => '123',
						5 => '1.5',
					],
				],
			],
		], $this->parserClass::parse($this->config));
	}
}
