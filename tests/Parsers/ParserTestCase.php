<?php
/*
 * This file is part of The Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Config\Parsers;

use PHPUnit\Framework\TestCase;

abstract class ParserTestCase extends TestCase
{
    protected mixed $config;
    protected string $parserClass;

    public function testParse() : void
    {
        self::assertSame([
            'service1' => [
                'default' => [
                    'array' => [
                        0 => true,
                        1 => false,
                        2 => null,
                        3 => 'string',
                        4 => 123,
                        5 => 1.5,
                    ],
                    'false' => false,
                    'float' => 1.5,
                    'int' => 123,
                    'null' => null,
                    'string1' => 'string1',
                    'string2' => 'string2',
                    'string3' => 'string3',
                    'true' => true,
                ],
                'other' => [
                    'apos' => "apos'4'56",
                    'mix' => 'null 123 \\',
                    'quot' => 'quot"1"23',
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
