<?php namespace Tests\Config;

use Framework\Config\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
	protected Config $config;

	protected function setUp() : void
	{
		$this->config = new Config(__DIR__ . '/configs', [], '.config.php');
	}

	public function testLoadException() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Config file not found: bazz');
		$this->config->load('bazz');
	}

	public function testSetDirException() : void
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Config directory not found: ' . __DIR__ . '/unknown');
		(new Config(__DIR__ . '/unknown'));
	}

	public function testGetAll() : void
	{
		$this->assertEquals([], $this->config->getAll());
		$this->config->load('bar');
		$this->assertEquals([
			'bar' => [
				'default' => [
				],
			],
		], $this->config->getAll());
		$this->config->load('foo');
		$this->assertEquals([
			'bar' => [
				'default' => [
				],
			],
			'foo' => [
				'default' => [
				],
			],
		], $this->config->getAll());
	}

	public function testAdd() : void
	{
		$this->config->add('foo', ['baz']);
		$this->assertEquals(['baz'], $this->config->get('foo'));
		$this->config->set('foo', ['bar']);
		$this->assertEquals(['bar'], $this->config->get('foo'));
		$this->config->add('foo', ['baz', 'hi']);
		$this->assertEquals(['baz', 'hi'], $this->config->get('foo'));
	}
}
