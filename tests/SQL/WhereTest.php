<?php

namespace Opis\Database\Test\SQL;

use Opis\Database\Database;
use Opis\Database\Test\Connection;
use PHPUnit\Framework\TestCase;

class WhereTest extends TestCase
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

    public function testWhereIs()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 21';
        $actual = $this->db->from('users')->where('age')->is(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereIsNot()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" != 21';
        $actual = $this->db->from('users')->where('age')->isNot(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLT()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < 21';
        $actual = $this->db->from('users')->where('age')->lessThan(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < 21';
        $actual = $this->db->from('users')->where('age')->lt(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGT()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > 21';
        $actual = $this->db->from('users')->where('age')->greaterThan(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > 21';
        $actual = $this->db->from('users')->where('age')->gt(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTE()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= 21';
        $actual = $this->db->from('users')->where('age')->atMost(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTEAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= 21';
        $actual = $this->db->from('users')->where('age')->lte(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTE()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= 21';
        $actual = $this->db->from('users')->where('age')->atLeast(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTEAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= 21';
        $actual = $this->db->from('users')->where('age')->gte(21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testBetween()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" BETWEEN 18 AND 21';
        $actual = $this->db->from('users')->where('age')->between(18, 21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testNotBetween()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" NOT BETWEEN 18 AND 21';
        $actual = $this->db->from('users')->where('age')->notBetween(18, 21)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereInArray()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" IN (18, 21, 31)';
        $actual = $this->db->from('users')->where('age')->in([18, 21, 31])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereNotInArray()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" NOT IN (18, 21, 31)';
        $actual = $this->db->from('users')->where('age')->notIn([18, 21, 31])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereInQuery()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" IN (SELECT "name" FROM "customers")';
        $actual = $this->db->from('users')->where('age')->in(function ($query) {
            $query->from('customers')->select('name');
        })->select();
        $this->assertEquals($expected, $actual);
    }


    public function testWhereNotInQuery()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" NOT IN (SELECT "name" FROM "customers")';
        $actual = $this->db->from('users')->where('age')->notIn(function ($query) {
            $query->from('customers')->select('name');
        })->select();;
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLike()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" LIKE \'%foo%\'';
        $actual = $this->db->from('users')->where('name')->like('%foo%')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereNotLike()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" NOT LIKE \'%foo%\'';
        $actual = $this->db->from('users')->where('name')->notLike('%foo%')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereIsNull()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" IS NULL';
        $actual = $this->db->from('users')->where('name')->isNull()->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereIsNotNull()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" IS NOT NULL';
        $actual = $this->db->from('users')->where('name')->notNull()->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereAndCondition()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 18 AND "city" = \'London\'';
        $actual = $this->db->from('users')
            ->where('age')->is(18)
            ->andWhere('city')->is('London')
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereOrCondition()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 18 OR "city" = \'London\'';
        $actual = $this->db->from('users')
            ->where('age')->is(18)
            ->orWhere('city')->is('London')
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGroupCondition()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 18 AND ("city" = \'London\' OR "city" = \'Paris\')';
        $actual = $this->db->from('users')
            ->where('age')->is(18)
            ->andWhere(function ($group) {
                $group->where('city')->is('London')
                    ->orWhere('city')->is('Paris');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereIsColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = "foo"';
        $actual = $this->db->from('users')->where('age')->is('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereIsNotColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" != "foo"';
        $actual = $this->db->from('users')->where('age')->isNot('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < "foo"';
        $actual = $this->db->from('users')->where('age')->lessThan('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < "foo"';
        $actual = $this->db->from('users')->where('age')->lt('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > "foo"';
        $actual = $this->db->from('users')->where('age')->greaterThan('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > "foo"';
        $actual = $this->db->from('users')->where('age')->gt('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTEColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= "foo"';
        $actual = $this->db->from('users')->where('age')->atMost('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereLTEAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= "foo"';
        $actual = $this->db->from('users')->where('age')->lte('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTEColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= "foo"';
        $actual = $this->db->from('users')->where('age')->atLeast('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereGTEAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= "foo"';
        $actual = $this->db->from('users')->where('age')->gte('foo', true)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereExists()
    {
        $expected = 'SELECT * FROM "users" WHERE EXISTS (SELECT * FROM "orders" WHERE "orders"."name" = "users"."name")';
        $actual = $this->db->from('users')
            ->whereExists(function ($query) {
                $query->from('orders')
                    ->where('orders.name')->eq('users.name', true)
                    ->select();
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereExpression1()
    {
        $expected = 'SELECT * FROM "numbers" WHERE "c" = "b" + 10';
        $actual = $this->db->from('numbers')
            ->where('c')->eq(function ($expr) {
                $expr->column('b')->op('+')->value(10);
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testWhereExpression2()
    {
        $expected = 'SELECT * FROM "numbers" WHERE "c" = "a" + "b"';
        $actual = $this->db->from('numbers')
            ->where('c')->eq(function ($expr) {
                $expr->column('a')->{'+'}->column('b');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }
}