<?php
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Config\Config;

use Framework\Config\Config;
use Framework\Config\Debug\ConfigCollector;
use PHPUnit\Framework\TestCase;

final class ConfigCollectorTest extends TestCase
{
    protected Config $config;
    protected ConfigCollector $collector;

    protected function setUp() : void
    {
        $this->config = new Config();
        $this->collector = new ConfigCollector();
    }

    protected function makeConfig() : Config
    {
        return $this->config->setDebugCollector($this->collector);
    }

    public function testNoConfig() : void
    {
        self::assertStringContainsString(
            'This collector has not been added to a Config instance',
            $this->collector->getContents()
        );
    }

    public function testNoDir() : void
    {
        $this->makeConfig();
        self::assertStringNotContainsString(
            'Config directory:',
            $this->collector->getContents()
        );
    }

    public function testDir() : void
    {
        $dir = __DIR__ . '/config';
        $this->makeConfig()->setDir($dir);
        $contents = $this->collector->getContents();
        self::assertStringContainsString(
            'Config directory:',
            $contents
        );
        self::assertStringContainsString(
            $dir,
            $contents
        );
    }

    public function testNoConfigs() : void
    {
        $this->makeConfig();
        self::assertStringContainsString(
            '0 configurations have been set.',
            $this->collector->getContents()
        );
    }

    public function testLoadConfigs() : void
    {
        $this->makeConfig()
            ->setDir(__DIR__ . '/config')
            ->load('foo');
        self::assertStringContainsString(
            '1 configuration have been set.',
            $this->collector->getContents()
        );
        $this->config->load('bar')->load('baz');
        self::assertStringContainsString(
            '3 configurations have been set.',
            $this->collector->getContents()
        );
        self::assertStringContainsString(
            'foo[bar]',
            $this->collector->getContents()
        );
    }

    public function testManyConfigs() : void
    {
        $this->makeConfig()
            ->setDir(__DIR__ . '/config')
            ->setMany([
                'database' => [
                    'default' => [
                        'host' => 'localhost',
                        'failover' => [
                            'host' => '192.168.1.1',
                            'port' => 3306,
                        ],
                    ],
                    'replica' => [
                        'host' => '192.168.1.100',
                    ],
                ],
                'cache' => [
                    'default' => [
                        'host' => '127.0.0.1',
                    ],
                ],
            ]);
        self::assertStringContainsString(
            '2 configurations have been set.',
            $this->collector->getContents()
        );
        $this->config->load('foo');
        self::assertStringContainsString(
            '3 configurations have been set.',
            $this->collector->getContents()
        );
        self::assertStringContainsString(
            'foo[bar]',
            $this->collector->getContents()
        );
        self::assertStringContainsString(
            'failover[host]',
            $this->collector->getContents()
        );
    }

    public function testActivities() : void
    {
        $this->makeConfig();
        $this->config->setDir(__DIR__ . '/config');
        self::assertEmpty($this->collector->getActivities());
        $this->config->load('foo');
        self::assertSame(
            [
                'collector',
                'class',
                'description',
                'start',
                'end',
            ],
            \array_keys($this->collector->getActivities()[0]) // @phpstan-ignore-line
        );
        $this->config->load('bar');
        self::assertCount(2, $this->collector->getActivities());
    }
}
