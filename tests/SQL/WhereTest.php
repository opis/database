<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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

use Opis\Database\Database;
use Opis\Database\SQL\{Expression, SubQuery, WhereStatement};

class WhereTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'where is',
                'SELECT * FROM "users" WHERE "age" = 21',
                fn(Database $db) => $db->from('users')->where('age')->is(21)->select(),
            ],
            [
                'where is not',
                'SELECT * FROM "users" WHERE "age" != 21',
                fn(Database $db) => $db->from('users')->where('age')->isNot(21)->select(),
            ],
            [
                'where is lessThan',
                'SELECT * FROM "users" WHERE "age" < 21',
                fn(Database $db) => $db->from('users')->where('age')->lessThan(21)->select(),
            ],
            [
                'where is lt',
                'SELECT * FROM "users" WHERE "age" < 21',
                fn(Database $db) => $db->from('users')->where('age')->lt(21)->select(),
            ],
            [
                'where is greaterThan',
                'SELECT * FROM "users" WHERE "age" > 21',
                fn(Database $db) => $db->from('users')->where('age')->greaterThan(21)->select(),
            ],
            [
                'where is gt',
                'SELECT * FROM "users" WHERE "age" > 21',
                fn(Database $db) => $db->from('users')->where('age')->gt(21)->select(),
            ],
            [
                'where is atMost',
                'SELECT * FROM "users" WHERE "age" <= 21',
                fn(Database $db) => $db->from('users')->where('age')->atMost(21)->select(),
            ],
            [
                'where is lte',
                'SELECT * FROM "users" WHERE "age" <= 21',
                fn(Database $db) => $db->from('users')->where('age')->lte(21)->select(),
            ],
            [
                'where is atLeast',
                'SELECT * FROM "users" WHERE "age" >= 21',
                fn(Database $db) => $db->from('users')->where('age')->atLeast(21)->select(),
            ],
            [
                'where is gte',
                'SELECT * FROM "users" WHERE "age" >= 21',
                fn(Database $db) => $db->from('users')->where('age')->gte(21)->select(),
            ],
            [
                'where between',
                'SELECT * FROM "users" WHERE "age" BETWEEN 18 AND 21',
                fn(Database $db) => $db->from('users')->where('age')->between(18, 21)->select(),
            ],
            [
                'where not between',
                'SELECT * FROM "users" WHERE "age" NOT BETWEEN 18 AND 21',
                fn(Database $db) => $db->from('users')->where('age')->notBetween(18, 21)->select(),
            ],
            [
                'where in array',
                'SELECT * FROM "users" WHERE "age" IN (18, 21, 31)',
                fn(Database $db) => $db->from('users')->where('age')->in([18, 21, 31])->select(),
            ],
            [
                'where not in array',
                'SELECT * FROM "users" WHERE "age" NOT IN (18, 21, 31)',
                fn(Database $db) => $db->from('users')->where('age')->notIn([18, 21, 31])->select(),
            ],
            [
                'where in query',
                'SELECT * FROM "users" WHERE "age" IN (SELECT "name" FROM "customers")',
                fn(Database $db) => $db->from('users')->where('age')->in(function (SubQuery $query) {
                    $query->from('customers')->select('name');
                })->select(),
            ],
            [
                'where not in query',
                'SELECT * FROM "users" WHERE "age" NOT IN (SELECT "name" FROM "customers")',
                fn(Database $db) => $db->from('users')->where('age')->notIn(function (SubQuery $query) {
                    $query->from('customers')->select('name');
                })->select(),
            ],
            [
                'where like',
                'SELECT * FROM "users" WHERE "name" LIKE \'%foo%\'',
                fn(Database $db) => $db->from('users')->where('name')->like('%foo%')->select(),
            ],
            [
                'where not like',
                'SELECT * FROM "users" WHERE "name" NOT LIKE \'%foo%\'',
                fn(Database $db) => $db->from('users')->where('name')->notLike('%foo%')->select(),
            ],
            [
                'where is null',
                'SELECT * FROM "users" WHERE "name" IS NULL',
                fn(Database $db) => $db->from('users')->where('name')->isNull()->select(),
            ],
            [
                'where is not null',
                'SELECT * FROM "users" WHERE "name" IS NOT NULL',
                fn(Database $db) => $db->from('users')->where('name')->notNull()->select(),
            ],
            [
                'where and condition',
                'SELECT * FROM "users" WHERE "age" = 18 AND "city" = \'London\'',
                fn(Database $db) => $db->from('users')
                    ->where('age')->is(18)
                    ->andWhere('city')->is('London')
                    ->select(),
            ],
            [
                'where or condition',
                'SELECT * FROM "users" WHERE "age" = 18 OR "city" = \'London\'',
                fn(Database $db) => $db->from('users')
                    ->where('age')->is(18)
                    ->orWhere('city')->is('London')
                    ->select(),
            ],
            [
                'where group condition',
                'SELECT * FROM "users" WHERE "age" = 18 AND ("city" = \'London\' OR "city" = \'Paris\')',
                fn(Database $db) => $db->from('users')
                    ->where('age')->is(18)
                    ->andWhere(function (WhereStatement $group) {
                        $group->where('city')->is('London')
                            ->orWhere('city')->is('Paris');
                    })
                    ->select(),
            ],
            [
                'where is column',
                'SELECT * FROM "users" WHERE "age" = "foo"',
                fn(Database $db) => $db->from('users')->where('age')->is('foo', true)->select(),
            ],
            [
                'where is not column',
                'SELECT * FROM "users" WHERE "age" != "foo"',
                fn(Database $db) => $db->from('users')->where('age')->isNot('foo', true)->select(),
            ],
            [
                'where is lessThan column',
                'SELECT * FROM "users" WHERE "age" < "foo"',
                fn(Database $db) => $db->from('users')->where('age')->lessThan('foo', true)->select(),
            ],
            [
                'where is lt column',
                'SELECT * FROM "users" WHERE "age" < "foo"',
                fn(Database $db) => $db->from('users')->where('age')->lt('foo', true)->select(),
            ],
            [
                'where is greaterThan column',
                'SELECT * FROM "users" WHERE "age" > "foo"',
                fn(Database $db) => $db->from('users')->where('age')->greaterThan('foo', true)->select(),
            ],
            [
                'where is gt column',
                'SELECT * FROM "users" WHERE "age" > "foo"',
                fn(Database $db) => $db->from('users')->where('age')->gt('foo', true)->select(),
            ],
            [
                'where is atMost column',
                'SELECT * FROM "users" WHERE "age" <= "foo"',
                fn(Database $db) => $db->from('users')->where('age')->atMost('foo', true)->select(),
            ],
            [
                'where is lte column',
                'SELECT * FROM "users" WHERE "age" <= "foo"',
                fn(Database $db) => $db->from('users')->where('age')->lte('foo', true)->select(),
            ],
            [
                'where is atLeast column',
                'SELECT * FROM "users" WHERE "age" >= "foo"',
                fn(Database $db) => $db->from('users')->where('age')->atLeast('foo', true)->select(),
            ],
            [
                'where is gte column',
                'SELECT * FROM "users" WHERE "age" >= "foo"',
                fn(Database $db) => $db->from('users')->where('age')->gte('foo', true)->select(),
            ],
            [
                'where exists',
                'SELECT * FROM "users" WHERE EXISTS (SELECT * FROM "orders" WHERE "orders"."name" = "users"."name")',
                fn(Database $db) => $db->from('users')
                    ->whereExists(function (SubQuery $query) {
                        $query->from('orders')
                            ->where('orders.name')->eq('users.name', true)
                            ->select();
                    })
                    ->select(),
            ],
            [
                'where eq 1',
                'SELECT * FROM "numbers" WHERE "c" = "b" + 10',
                fn(Database $db) => $db->from('numbers')
                    ->where('c')
                    ->eq(fn(Expression $expr) => $expr->column('b')->op('+')->value(10))
                    ->select(),
            ],
            [
                'where eq 2',
                'SELECT * FROM "numbers" WHERE "c" = "a" + "b"',
                fn(Database $db) => $db->from('numbers')
                    ->where('c')
                    ->eq(function (Expression $expr) {
                        $expr->column('a')->{'+'}->column('b');
                    })
                    ->select(),
            ],
            [
                'where with expression (old style)',
                'SELECT * FROM "names" WHERE LCASE("name") LIKE \'%test%\'',
                fn(Database $db) => $db->from('names')
                    ->where(function (Expression $expr) {
                        $expr->lcase('name');
                    }, true) // true indicates an expression
                    ->like('%test%')
                    ->select(),
            ],
            [
                'where expression',
                'SELECT * FROM "names" WHERE LCASE("name") LIKE \'%test%\'',
                fn(Database $db) => $db->from('names')
                    ->whereExpression(function (Expression $expr) {
                        $expr->lcase('name');
                    })
                    ->like('%test%')
                    ->select(),
            ],
            [
                'and where expression',
                'SELECT * FROM "names" WHERE "age" = 21 AND LCASE("name") LIKE \'%test%\'',
                fn(Database $db) => $db->from('names')
                    ->where('age')->is(21)
                    ->andWhereExpression(function (Expression $expr) {
                        $expr->lcase('name');
                    })
                    ->like('%test%')
                    ->select(),
            ],
            [
                'or where expression',
                'SELECT * FROM "names" WHERE "age" = 21 OR LCASE("name") LIKE \'%test%\'',
                fn(Database $db) => $db->from('names')
                    ->where('age')->is(21)
                    ->orWhereExpression(function (Expression $expr) {
                        $expr->lcase('name');
                    })
                    ->like('%test%')
                    ->select(),
            ],
            [
                'where expression nop',
                'SELECT * FROM "users" WHERE match( "username" ) against( \'expression\' )',
                fn(Database $db) => $db->from('users')
                    ->whereExpression(function (Expression $expr) {
                        $expr->op('match(')->column('username')->op(') against(')
                            ->value('expression')->op(')');
                    })
                    ->nop()
                    ->select(),
            ],
            [
                'where expression call 1 - nop',
                'SELECT * FROM "users" WHERE match("username") against(\'expression\')',
                fn(Database $db) => $db->from('users')
                    ->whereExpression(function (Expression $expr) {
                        $expr->call('match', fn(Expression $e) => $e->column('username'));
                        $expr->call('against', 'expression');
                    })
                    ->nop()
                    ->select(),
            ],
            [
                'where expression call 1 magic - nop',
                'SELECT * FROM "users" WHERE match("username") against(\'expression\')',
                fn(Database $db) => $db->from('users')
                    ->whereExpression(function (Expression $expr) {
                        $expr->match(fn(Expression $e) => $e->column('username'));
                        $expr->against('expression');
                    })
                    ->nop()
                    ->select(),
            ],
            [
                'where expression call 2',
                'SELECT * FROM "users" WHERE CUSTOM_AGE_CALC(\'secret\', 5, "age" - 10) = "age"',
                fn(Database $db) => $db->from('users')
                    ->whereExpression(function (Expression $expr) {
                        $expr->call('CUSTOM_AGE_CALC', 'secret', 5, function (Expression $expr) {
                            $expr->column('age')->op('-')->value(10);
                        });
                    })
                    ->is('age', true)
                    ->select(),
            ],
            [
                'where expression call 3 - magic',
                'SELECT * FROM "users" WHERE CUSTOM_AGE_CALC(HASH(\'secret\'), CONCAT(\'prefix-\', "name"), "age" - 10) = "age"',
                fn(Database $db) => $db->from('users')
                    ->whereExpression(function (Expression $expr) {
                        $expr->{'CUSTOM_AGE_CALC'}(
                            fn (Expression $e) => $e->{'HASH'}('secret'),
                            fn (Expression $e) => $e->{'CONCAT'}('prefix-', fn(Expression $e) => $e->column('name')),
                            fn (Expression $e) => $e->column('age')->op('-')->value(10)
                        );
                    })
                    ->is('age', true)
                    ->select(),
            ],
            [
                'where using expression\'sshortcuts',
                'SELECT * FROM "users" WHERE FIND_IN_SET("name", \'a,b,c\')',
                fn (Database $db) => $db->from('users')
                    ->whereExpression(Expression::fromCall(
                        'FIND_IN_SET',
                        Expression::fromColumn('name'),
                        'a,b,c'
                    ))
                    ->nop()
                    ->select(),
            ],
        ];
    }

}