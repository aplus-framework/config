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

use Framework\Config\Debug\ConfigCollection;
use PHPUnit\Framework\TestCase;

final class ConfigCollectionTest extends TestCase
{
    protected ConfigCollection $collection;

    protected function setUp() : void
    {
        $this->collection = new ConfigCollection('Config');
    }

    public function testIcon() : void
    {
        self::assertStringContainsString('<svg ', $this->collection->getIcon());
    }
}
