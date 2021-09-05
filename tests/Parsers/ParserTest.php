<?php
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Config\Parsers;

use Framework\Config\Parsers\ParserException;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest.
 */
final class ParserTest extends TestCase
{
    public function testConfigIsNotString() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(ParserMock::class . ' config must be a string');
        ParserMock::checkConfig([]);
    }

    public function testConfigFileNotFound() : void
    {
        $file = '/tmp/config.ini';
        if (\is_file($file)) {
            \unlink($file);
        }
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('File not found: ' . $file);
        ParserMock::checkConfig($file);
    }

    public function testConfigFileIsNotReadable() : void
    {
        if (\getenv('GITLAB_CI')) {
            self::markTestIncomplete();
        }
        $file = '/tmp/config.ini';
        \file_put_contents($file, 'foo=bar');
        \chmod($file, 0200);
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('File is not readable: ' . $file);
        ParserMock::checkConfig($file);
    }
}
