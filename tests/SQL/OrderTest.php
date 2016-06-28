<?php

namespace Opis\Database\Test\SQL;

use Opis\Database\Database;
use Opis\Database\Test\Connection;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    protected static $database;
    /** @var  Database */
    protected $db;

    public static function setUpBeforeClass()
    {
        static::$database = new Database(new Connection(''));
    }

    public function setUp()
    {
        $this->db = static::$database;
    }

    public function testOrderAsc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC';
        $actual = $this->db->from('users')->orderBy('name')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderDesc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" DESC';
        $actual = $this->db->from('users')->orderBy('name', 'desc')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderMultipleColumnsAsc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name", "age" ASC';
        $actual = $this->db->from('users')->orderBy(['name', 'age'])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderMultipleColumnsDesc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name", "age" DESC';
        $actual = $this->db->from('users')->orderBy(['name', 'age'], 'desc')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderAscDesc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC, "age" DESC';
        $actual = $this->db->from('users')->orderBy('name')->orderBy('age', 'desc')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderNullsFirst()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC, (CASE WHEN "age" IS NULL THEN 0 ELSE 1 END), "age" DESC';
        $actual = $this->db->from('users')->orderBy('name')->orderBy('age', 'desc', 'nulls first')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderNullsLast()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC, (CASE WHEN "age" IS NULL THEN 1 ELSE 0 END), "age" DESC';
        $actual = $this->db->from('users')->orderBy('name')->orderBy('age', 'desc', 'nulls last')->select();
        $this->assertEquals($expected, $actual);
    }
}