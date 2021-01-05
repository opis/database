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
use Opis\Database\SQL\{ColumnExpression, Expression};

class SelectTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'select *',
                'SELECT * FROM "users"',
                fn(Database $db) => $db->from('users')->select(),
            ],
            [
                'select distinct *',
                'SELECT DISTINCT * FROM "users"',
                fn(Database $db) => $db->from('users')->distinct()->select(),
            ],
            [
                'select column',
                'SELECT "name" FROM "users"',
                fn(Database $db) => $db->from('users')->select('name'),
            ],
            [
                'select single column array',
                'SELECT "name" FROM "users"',
                fn(Database $db) => $db->from('users')->select(['name']),
            ],
            [
                'select multiple columns',
                'SELECT "name", "age" FROM "users"',
                fn(Database $db) => $db->from('users')->select(['name', 'age']),
            ],
            [
                'select multiple columns aliased',
                'SELECT "name" AS "n", "age" AS "a" FROM "users"',
                fn(Database $db) => $db->from('users')->select(['name' => 'n', 'age' => 'a']),
            ],
            [
                'select multiple columns - first aliased',
                'SELECT "name" AS "n", "age" FROM "users"',
                fn(Database $db) => $db->from('users')->select(['name' => 'n', 'age']),
            ],
            [
                'select multiple columns - last aliased',
                'SELECT "name", "age" AS "a" FROM "users"',
                fn(Database $db) => $db->from('users')->select(['name', 'age' => 'a']),
            ],
            [
                'select from multiple tables',
                'SELECT * FROM "users", "sites"',
                fn(Database $db) => $db->from(['users', 'sites'])->select(),
            ],
            [
                'select from multiple tables aliased',
                'SELECT * FROM "users" AS "u", "sites" AS "s"',
                fn(Database $db) => $db->from(['users' => 'u', 'sites' => 's'])->select(),
            ],
            [
                'select columns from multiple tables aliased',
                'SELECT "u"."name", "s"."address" FROM "users" AS "u", "sites" AS "s"',
                fn(Database $db) => $db->from(['users' => 'u', 'sites' => 's'])->select(['u.name', 's.address']),
            ],
            [
                'select aliased columns from multiple tables aliased',
                'SELECT "u"."name" AS "n", "s"."address" AS "s" FROM "users" AS "u", "sites" AS "s"',
                fn(Database $db) => $db->from(['users' => 'u', 'sites' => 's'])->select(['u.name' => 'n', 's.address' => 's']),
            ],
            [
                'select aliased expression',
                'SELECT LCASE("name") AS "lower_name" FROM "users"',
                fn(Database $db) => $db->from('users')
                    ->select(fn (ColumnExpression $expr) => $expr->lcase('name', 'lower_name')),
            ],
            [
                'select multiple aliased expressions',
                'SELECT "name", LEN("name") AS "name_length", "age" AS "alias_age" FROM "users"',
                fn(Database $db) => $db->from('users')
                    ->select([
                        'name',
                        'name_length' => fn (Expression $expr) => $expr->len('name'),
                        'age' => 'alias_age',
                    ]),
            ],
        ];
    }
}