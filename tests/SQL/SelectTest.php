<?php

namespace Opis\Database\Test\SQL;

use Opis\Database\Database;
use Opis\Database\Test\Connection;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
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

    public function testSelect()
    {
        $expected = 'SELECT * FROM "users"';
        $actual = $this->db->from('users')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectDistinct()
    {
        $expected = 'SELECT DISTINCT * FROM "users"';
        $actual = $this->db->from('users')->distinct()->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectSingleColumn()
    {
        $expected = 'SELECT "name" FROM "users"';
        $actual = $this->db->from('users')->select('name');
        $this->assertEquals($expected, $actual);
    }

    public function testSelectSingleColumnArray()
    {
        $expected = 'SELECT "name" FROM "users"';
        $actual = $this->db->from('users')->select(['name']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectMultipleColumns()
    {
        $expected = 'SELECT "name", "age" FROM "users"';
        $actual = $this->db->from('users')->select(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsAliases()
    {
        $expected = 'SELECT "name" AS "n", "age" AS "a" FROM "users"';
        $actual = $this->db->from('users')->select(['name' => 'n', 'age' => 'a']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsFirstAliased()
    {
        $expected = 'SELECT "name" AS "n", "age" FROM "users"';
        $actual = $this->db->from('users')->select(['name' => 'n', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsLastAliased()
    {
        $expected = 'SELECT "name", "age" AS "a" FROM "users"';
        $actual = $this->db->from('users')->select(['name', 'age' => 'a']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectFromMultipleTables()
    {
        $expected = 'SELECT * FROM "users", "sites"';
        $actual = $this->db->from(['users', 'sites'])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectFromMultipleTablesAliased()
    {
        $expected = 'SELECT * FROM "users" AS "u", "sites" AS "s"';
        $actual = $this->db->from(['users' => 'u', 'sites' => 's'])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsFromMultipleTablesAliased()
    {
        $expected = 'SELECT "u"."name", "s"."address" FROM "users" AS "u", "sites" AS "s"';
        $actual = $this->db->from(['users' => 'u', 'sites' => 's'])->select(['u.name', 's.address']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectAliasedColumnsFromMultipleTablesAliased()
    {
        $expected = 'SELECT "u"."name" AS "n", "s"."address" AS "s" FROM "users" AS "u", "sites" AS "s"';
        $actual = $this->db->from(['users' => 'u', 'sites' => 's'])->select(['u.name' => 'n', 's.address' => 's']);
        $this->assertEquals($expected, $actual);
    }

}