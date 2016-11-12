<?php

namespace Opis\Database\Test\ORM;

use Opis\Database\Test\EntityManager;
use Opis\Database\ORM;
use Opis\Database\Test\Connection;
use Opis\Database\Test\ORM\Entities\User;
use PHPUnit\Framework\TestCase;

class EntityQueryTest extends  TestCase
{
    protected static $ormobj;
    /** @var  ORM */
    protected $orm;

    public static function setUpBeforeClass()
    {
        static::$ormobj = new EntityManager(new Connection(''));
    }

    public function setUp()
    {
        $this->orm = static::$ormobj;
    }

    public function testFind()
    {
        $expected = 'SELECT * FROM "users" WHERE "id" = 1';
        $actual =  $this->orm->query(User::class)->find(1);
        $this->assertEquals($expected, $actual);
    }

    public function testFindColumns()
    {
        $expected = 'SELECT "name", "age", "id" FROM "users" WHERE "id" = 1';
        $actual =  $this->orm->query(User::class)->find(1, ['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testFindAll()
    {
        $expected = ['SELECT * FROM "users" WHERE "id" IN (1, 2, 3)'];
        $actual =  $this->orm->query(User::class)->findAll([1, 2, 3]);
        $this->assertEquals($expected, $actual);
    }

    public function testFindAllColumns()
    {
        $expected = ['SELECT "name", "age", "id" FROM "users" WHERE "id" IN (1, 2, 3)'];
        $actual =  $this->orm->query(User::class)->findAll([1, 2, 3], ['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetFirst()
    {
        $expected = 'SELECT * FROM "users"';
        $actual =  $this->orm->query(User::class)->get();
        $this->assertEquals($expected, $actual);
    }

    public function testGetFirstColumns()
    {
        $expected = 'SELECT "name", "age", "id" FROM "users"';
        $actual =  $this->orm->query(User::class)->get(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetAll()
    {
        $expected = ['SELECT * FROM "users"'];
        $actual =  $this->orm->query(User::class)->all();
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllColumns()
    {
        $expected = ['SELECT "name", "age", "id" FROM "users"'];
        $actual =  $this->orm->query(User::class)->all(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }
}