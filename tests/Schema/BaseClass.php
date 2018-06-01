<?php
/* ===========================================================================
 * Copyright 2013-2018 Opis
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\Test\Schema;

use Opis\Database\Schema\AlterTable;
use Opis\Database\Schema\CreateTable;
use Opis\Database\Test\Connection;
use Opis\Database\Test\Schema;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class BaseClass extends TestCase
{
    protected static $data = [];

    /** @var Schema */
    protected static $db_schema;

    /** @var string|null */
    protected static $schema_name;

    /** @var Schema */
    protected $schema;

    public static function setUpBeforeClass()
    {
        $name = static::$schema_name;

        static::$data = array_map(function($value){
            if (is_string($value)) {
                $value = rtrim($value);
            }
            return $value;
        }, Yaml::parseFile(__DIR__ . '/../data/schema/' . $name . '.yaml'));
        static::$db_schema = new Schema(new Connection($name));
    }

    public function setUp()
    {
        $this->schema = static::$db_schema;
    }

    public function testCreateTable()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {

        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAddSingleColumn()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAddMultipleColumns()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a');
            $table->integer('b');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testTypes()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a');
            $table->float('b');
            $table->double('c');
            $table->decimal('d');
            $table->decimal("d1", 4);
            $table->decimal("d2", 4, 8);
            $table->boolean('e');
            $table->string("f");
            $table->string("f1", 32);
            $table->fixed('g');
            $table->fixed('g1', 2);
            $table->date('h');
            $table->dateTime('i');
            $table->timestamp('j');
            $table->time('k');
            $table->binary('l');
            $table->text('m');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testIntSizes()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->size('tiny');
            $table->integer('b')->size('small');
            $table->integer('c')->size('normal');
            $table->integer('d')->size('medium');
            $table->integer('e')->size('big');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testTextSizes()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->text('a')->size('tiny');
            $table->text('b')->size('small');
            $table->text('c')->size('normal');
            $table->text('d')->size('medium');
            $table->text('e')->size('big');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testBinarySizes()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->binary('a')->size('tiny');
            $table->binary('b')->size('small');
            $table->binary('c')->size('normal');
            $table->binary('d')->size('medium');
            $table->binary('e')->size('big');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testColumnProperties()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->unsigned();
            $table->float('b')->defaultValue(0.1);
            $table->string('c')->notNull();
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testColumnConstraints()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->primary();
            $table->integer('b')->unique();
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testColumnNamedConstraints()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->primary('pk_a');
            $table->integer('b')->unique('uk_b');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAutoincrement()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->autoincrement();
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testNamedAutoincrement()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->autoincrement('x');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testIndex()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a')->index();
            $table->integer('b')->index('x');
            $table->integer('c');
            $table->integer('d');

            $table->index('c');
            $table->index('d', 'y');
            $table->index(['a', 'b']);
            $table->index(['c', 'd'], 'z');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testForeignKey()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a');
            $table->foreign('a')
                ->references('bar', 'a')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });;

        $this->execTest(__FUNCTION__, $result);
    }

    public function testForeignKeyMultiple()
    {
        $result = $this->schema->create('foo', function (CreateTable $table) {
            $table->integer('a');
            $table->integer('b');
            $table->foreign(['a', 'b'])
                ->references('bar', 'a', 'b')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAlterTableAddColumn()
    {
        $result = $this->schema->alter('foo', function(AlterTable $table){
            $table->integer('a');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAlterTableAddMultipleColumns()
    {
        $result = $this->schema->alter('foo', function(AlterTable $table){
            $table->integer('a');
            $table->integer('b');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAlterTableDropColumn()
    {
        $result = $this->schema->alter('foo', function(AlterTable $table){
            $table->dropColumn('a');
        });

        $this->execTest(__FUNCTION__, $result);
    }

    public function testAlterTableAddDefaults()
    {
        $result = $this->schema->alter('foo', function(AlterTable $table){
            $table->setDefaultValue('a', 100);
        });

        $this->execTest(__FUNCTION__, $result);
    }

    private function execTest($test, $result)
    {
        $expected = static::$data[$test] ?? null;
        if ($expected === null) {
            $this->markTestSkipped();
        } else {
            $this->assertEquals($expected, $result);
        }
    }
}