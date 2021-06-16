<?php namespace Tests\Config;

use Framework\Config\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
	protected Config $config;
	/**
	 * @var array<string,mixed>
	 */
	protected array $persistence = [];

	protected function setUp() : void
	{
		$this->config = new Config(__DIR__ . '/configs', $this->persistence, '.config.php');
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
		self::assertSame([], $this->config->getAll());
		$this->config->load('bar');
		self::assertSame([
			'bar' => [
				'default' => [
				],
			],
		], $this->config->getAll());
		$this->config->load('foo');
		self::assertSame([
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
		self::assertSame(['baz'], $this->config->get('foo'));
		$this->config->set('foo', ['bar']);
		self::assertSame(['bar'], $this->config->get('foo'));
		$this->config->add('foo', ['baz', 'hi']);
		self::assertSame(['baz', 'hi'], $this->config->get('foo'));
	}

	public function testPersistence() : void
	{
		$this->persistence = [
			'bar' => [
				'custom' => [
				],
			],
			'foo' => [
				'default' => [
					'foo' => 'unchanged',
				],
			],
		];
		$this->setUp();
		$this->config->load('bar');
		$this->config->load('foo');
		self::assertSame([
			'bar' => [
				'default' => [
				],
				'custom' => [
				],
			],
			'foo' => [
				'default' => [
					'foo' => 'unchanged',
				],
			],
		], $this->config->getAll());
		$this->config->set('foo', ['foo' => 'try-change', 'bar' => 25]);
		self::assertSame([
			'bar' => [
				'default' => [
				],
				'custom' => [
				],
			],
			'foo' => [
				'default' => [
					'foo' => 'unchanged',
					'bar' => 25,
				],
			],
		], $this->config->getAll());
		$this->config->add('foo', ['foo' => 'try-change-again', 'bar' => 42]);
		self::assertSame([
			'bar' => [
				'default' => [
				],
				'custom' => [
				],
			],
			'foo' => [
				'default' => [
					'foo' => 'unchanged',
					'bar' => 42,
				],
			],
		], $this->config->getAll());
	}
}
