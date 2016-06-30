<?php

namespace Opis\Database\Test\ORM;

use Opis\Database\ORM;
use Opis\Database\Test\Connection;
use Opis\Database\Test\ORM\Models\User;
use PHPUnit\Framework\TestCase;

class QueryTest extends  TestCase
{
    protected static $ormobj;
    /** @var  ORM */
    protected $orm;

    public static function setUpBeforeClass()
    {
        static::$ormobj = new ORM(new Connection(''));
    }

    public function setUp()
    {
        $this->orm = static::$ormobj;
    }

    public function testFind()
    {
        $expected = 'SELECT * FROM "users" WHERE "id" = 1';
        $actual =  $this->orm->model(User::class)->find(1);
        $this->assertEquals($expected, $actual);
    }

    public function testFindColumns()
    {
        $expected = 'SELECT "name", "age", "id" FROM "users" WHERE "id" = 1';
        $actual =  $this->orm->model(User::class)->find(1, ['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testFindMany()
    {
        $expected = ['SELECT * FROM "users" WHERE "id" IN (1, 2, 3)'];
        $actual =  $this->orm->model(User::class)->findMany([1, 2, 3]);
        $this->assertEquals($expected, $actual);
    }

    public function testFindManyColumns()
    {
        $expected = ['SELECT "name", "age", "id" FROM "users" WHERE "id" IN (1, 2, 3)'];
        $actual =  $this->orm->model(User::class)->findMany([1, 2, 3], ['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testFindAll()
    {
        $expected = ['SELECT * FROM "users"'];
        $actual =  $this->orm->model(User::class)->findAll();
        $this->assertEquals($expected, $actual);
    }

    public function testFindAllColumns()
    {
        $expected = ['SELECT "name", "age", "id" FROM "users"'];
        $actual =  $this->orm->model(User::class)->findAll(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testFirst()
    {
        $expected = 'SELECT * FROM "users"';
        $actual =  $this->orm->model(User::class)->first();
        $this->assertEquals($expected, $actual);
    }

    public function testFirstColumns()
    {
        $expected = 'SELECT "name", "age", "id" FROM "users"';
        $actual =  $this->orm->model(User::class)->first(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testAll()
    {
        $expected = ['SELECT * FROM "users"'];
        $actual =  $this->orm->model(User::class)->all();
        $this->assertEquals($expected, $actual);
    }

    public function testAllColumns()
    {
        $expected = ['SELECT "name", "age", "id" FROM "users"'];
        $actual =  $this->orm->model(User::class)->all(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }
}