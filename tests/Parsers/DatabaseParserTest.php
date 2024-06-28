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

use Framework\Config\Parsers\DatabaseParser;
use Framework\Config\Parsers\ParserException;
use Framework\Database\Database;
use Framework\Database\Definition\Table\TableDefinition;

final class DatabaseParserTest extends ParserTestCase
{
    protected mixed $config;
    protected string $parserClass = DatabaseParser::class;

    protected function setUp() : void
    {
        $this->config = [
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
            'username' => \getenv('DB_USERNAME'),
            'password' => \getenv('DB_PASSWORD'),
            'schema' => \getenv('DB_SCHEMA'),
            'table' => 'Config',
        ];
        $this->prepareDatabase();
    }

    protected function prepareDatabase() : void
    {
        $database = new Database($this->config);
        $database->dropTable($this->config['table'])->ifExists()->run();
        $database->createTable($this->config['table'])
            ->definition(static function (TableDefinition $definition) : void {
                $definition->column('key')->varchar(255)->primaryKey();
                $definition->column('value')->varchar(255);
            })->run();
        $database->insert($this->config['table'])
            ->columns('key', 'value')
            ->values('service1.default.string1', 'string1')
            ->values('service1.default.string2', "'string2'")
            ->values('service1.default.string3', '"string3"')
            ->values('service1.default.int', 123)
            ->values('service1.default.float', 1.5)
            ->values('service1.default.true', 'True')
            ->values('service1.default.false', 'False')
            ->values('service1.default.null', 'Null')
            ->values('service1.default.array.0', 'True')
            ->values('service1.default.array.1', 'False')
            ->values('service1.default.array.2', 'Null')
            ->values('service1.default.array.3', 'string')
            ->values('service1.default.array.4', '123')
            ->values('service1.default.array.5', '1.5')
            ->values('service1.other.quot', '"quot\"1\"23"')
            ->values('service1.other.apos', "apos'4'56")
            ->values('service1.other.mix', '"null 123 \"')
            ->values('service2.default.array.0', "'True'")
            ->values('service2.default.array.1', "'False'")
            ->values('service2.default.array.2', "'Null'")
            ->values('service2.default.array.3', "'string'")
            ->values('service2.default.array.4', "'123'")
            ->values('service2.default.array.5', "'1.5'")
            ->run();
    }

    public function testConfigIsNotArray() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            DatabaseParser::class . ' config must be an array'
        );
        DatabaseParser::parse(__FILE__);
    }

    public function testConfigUsernameNotSet() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            DatabaseParser::class . ' config username not set'
        );
        DatabaseParser::parse([
            'user' => 'foo',
        ]);
    }

    public function testConfigSchemaNotSet() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            DatabaseParser::class . ' config schema not set'
        );
        DatabaseParser::parse([
            'username' => 'foo',
        ]);
    }

    public function testConfigTableNotSet() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            DatabaseParser::class . ' config table not set'
        );
        DatabaseParser::parse([
            'username' => 'foo',
            'schema' => 'app',
        ]);
    }

    public function testParseException() : void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches(
            '#^' . \strtr(DatabaseParser::class, ['\\' => '\\\\']) . ': Access denied for user +#'
        );
        DatabaseParser::parse([
            'host' => \getenv('DB_HOST'),
            'port' => \getenv('DB_PORT'),
            'username' => 'foo',
            'schema' => 'app',
            'table' => 'configs',
        ]);
    }
}
