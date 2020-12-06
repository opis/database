<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

namespace Opis\Database\Test\SQL;

use Opis\Database\SQL\Expression;

class WhereTest extends BaseClass
{
    public function testWhereIs()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 21';
        $this->db->from('users')->where('age')->is(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereIsNot()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" != 21';
        $this->db->from('users')->where('age')->isNot(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLT()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < 21';
        $this->db->from('users')->where('age')->lessThan(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < 21';
        $this->db->from('users')->where('age')->lt(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGT()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > 21';
        $this->db->from('users')->where('age')->greaterThan(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > 21';
        $this->db->from('users')->where('age')->gt(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTE()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= 21';
        $this->db->from('users')->where('age')->atMost(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTEAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= 21';
        $this->db->from('users')->where('age')->lte(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTE()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= 21';
        $this->db->from('users')->where('age')->atLeast(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTEAlt()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= 21';
        $this->db->from('users')->where('age')->gte(21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testBetween()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" BETWEEN 18 AND 21';
        $this->db->from('users')->where('age')->between(18, 21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testNotBetween()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" NOT BETWEEN 18 AND 21';
        $this->db->from('users')->where('age')->notBetween(18, 21)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereInArray()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" IN (18, 21, 31)';
        $this->db->from('users')->where('age')->in([18, 21, 31])->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereNotInArray()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" NOT IN (18, 21, 31)';
        $this->db->from('users')->where('age')->notIn([18, 21, 31])->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereInQuery()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" IN (SELECT "name" FROM "customers")';
        $this->db->from('users')->where('age')->in(function ($query) {
            $query->from('customers')->select('name');
        })->select();
        $this->assertEquals($expected, $this->getSQL());
    }


    public function testWhereNotInQuery()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" NOT IN (SELECT "name" FROM "customers")';
        $this->db->from('users')->where('age')->notIn(function ($query) {
            $query->from('customers')->select('name');
        })->select();;
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLike()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" LIKE \'%foo%\'';
        $this->db->from('users')->where('name')->like('%foo%')->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereNotLike()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" NOT LIKE \'%foo%\'';
        $this->db->from('users')->where('name')->notLike('%foo%')->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereIsNull()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" IS NULL';
        $this->db->from('users')->where('name')->isNull()->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereIsNotNull()
    {
        $expected = 'SELECT * FROM "users" WHERE "name" IS NOT NULL';
        $this->db->from('users')->where('name')->notNull()->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereAndCondition()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 18 AND "city" = \'London\'';
        $this->db->from('users')
            ->where('age')->is(18)
            ->andWhere('city')->is('London')
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereOrCondition()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 18 OR "city" = \'London\'';
        $this->db->from('users')
            ->where('age')->is(18)
            ->orWhere('city')->is('London')
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGroupCondition()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = 18 AND ("city" = \'London\' OR "city" = \'Paris\')';
        $this->db->from('users')
            ->where('age')->is(18)
            ->andWhere(function ($group) {
                $group->where('city')->is('London')
                    ->orWhere('city')->is('Paris');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereIsColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" = "foo"';
        $this->db->from('users')->where('age')->is('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereIsNotColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" != "foo"';
        $this->db->from('users')->where('age')->isNot('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < "foo"';
        $this->db->from('users')->where('age')->lessThan('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" < "foo"';
        $this->db->from('users')->where('age')->lt('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > "foo"';
        $this->db->from('users')->where('age')->greaterThan('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" > "foo"';
        $this->db->from('users')->where('age')->gt('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTEColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= "foo"';
        $this->db->from('users')->where('age')->atMost('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereLTEAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" <= "foo"';
        $this->db->from('users')->where('age')->lte('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTEColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= "foo"';
        $this->db->from('users')->where('age')->atLeast('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereGTEAltColumn()
    {
        $expected = 'SELECT * FROM "users" WHERE "age" >= "foo"';
        $this->db->from('users')->where('age')->gte('foo', true)->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereExists()
    {
        $expected = 'SELECT * FROM "users" WHERE EXISTS (SELECT * FROM "orders" WHERE "orders"."name" = "users"."name")';
        $this->db->from('users')
            ->whereExists(function ($query) {
                $query->from('orders')
                    ->where('orders.name')->eq('users.name', true)
                    ->select();
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereExpression1()
    {
        $expected = 'SELECT * FROM "numbers" WHERE "c" = "b" + 10';
        $this->db->from('numbers')
            ->where('c')->eq(function ($expr) {
                $expr->column('b')->op('+')->value(10);
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereExpression2()
    {
        $expected = 'SELECT * FROM "numbers" WHERE "c" = "a" + "b"';
        $this->db->from('numbers')
            ->where('c')->eq(function ($expr) {
                $expr->column('a')->{'+'}->column('b');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereExpression3()
    {
        $expected = 'SELECT * FROM "names" WHERE LCASE("name") LIKE \'%test%\'';
        $this->db->from('names')
            ->where(function (Expression $expr) {
                $expr->lcase('name');
            }, true)
            ->like('%test%')
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testWhereNop() {
        $expected = 'SELECT * FROM "users" WHERE match( "username" ) against( \'expression\' )';
        $this->db->from('users')
            ->where(function (Expression $expr) {
                $expr->op('match(')->column('username')->op(') against(')
                    ->value('expression')->op(')');
            }, true)->nop()
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }
}