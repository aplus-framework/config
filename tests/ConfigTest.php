<?php
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Config;

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

    public function testGetAndSetDir() : void
    {
        self::assertSame(__DIR__ . '/configs/', $this->config->getDir());
        $config = new Config();
        self::assertNull($config->getDir());
        $config->setDir(__DIR__ . '/configs');
        self::assertSame(__DIR__ . '/configs/', $config->getDir());
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

    public function testGet() : void
    {
        self::assertSame([], $this->config->get('foo'));
        self::assertSame(['one' => 1], $this->config->get('bar'));
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Config file not found: skavurska');
        $this->config->get('skavurska');
    }

    public function testGetWithConfigDirNotSet() : void
    {
        $this->config = new Config();
        self::assertNull($this->config->get('foo'));
        self::assertNull($this->config->get('bar'));
        self::assertNull($this->config->get('skavurska'));
        $this->config->set('foo', []);
        self::assertSame([], $this->config->get('foo'));
    }

    public function testGetAllWithConfigSetAsArray() : void
    {
        $configs = [
            'foo' => [
                'default' => [],
            ],
        ];
        $this->config = new Config($configs);
        self::assertSame($configs, $this->config->getAll());
    }

    public function testGetAll() : void
    {
        self::assertSame([], $this->config->getAll());
        $this->config->load('bar');
        self::assertSame([
            'bar' => [
                'default' => [
                    'one' => 1,
                ],
            ],
        ], $this->config->getAll());
        $this->config->load('foo');
        self::assertSame([
            'bar' => [
                'default' => [
                    'one' => 1,
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
                    'one' => 1,
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
                    'one' => 1,
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
                    'one' => 1,
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

    public function testGetInstances() : void
    {
        $configs = [
            'sv1' => [
                'default' => [
                    'one' => 1,
                ],
                'custom' => [
                ],
            ],
            'sv2' => [
                'default' => [
                    'foo' => 'unchanged',
                    'bar' => 42,
                ],
            ],
        ];
        self::assertNull($this->config->getInstances('sv1'));
        self::assertNull($this->config->getInstances('sv2'));
        $this->config->setMany($configs);
        self::assertSame($configs['sv1'], $this->config->getInstances('sv1'));
        self::assertSame($configs['sv2'], $this->config->getInstances('sv2'));
    }
}
