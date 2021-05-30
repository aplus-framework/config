<?php namespace Tests\Config;

use Framework\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	protected Config $config;

	public function setup() : void
	{
		$this->config = new Config();
	}

	public function testConfig()
	{
		$this->assertEquals(
			'Framework\Config\Config::test',
			$this->config->test()
		);
	}
}
