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
use Opis\Database\SQL\Expression;

class InsertTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'insert single value',
                'INSERT INTO "users" ("age") VALUES (18)',
                fn(Database $db) => $db->insert(['age' => 18])->into('users'),
            ],
            [
                'insert multiple values',
                'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18)',
                fn(Database $db) => $db->insert(['name' => 'foo', 'age' => 18])->into('users'),
            ],
            [
                'insert boolean',
                'INSERT INTO "test" ("foo", "bar") VALUES (TRUE, FALSE)',
                fn(Database $db) => $db->insert(['foo' => true, 'bar' => false])->into('test'),
            ],
            [
                'insert expressions',
                'INSERT INTO "users" ("name") VALUES (LCASE( \'foo\' ))',
                fn(Database $db) => $db->insert([
                    'name' => function (Expression $expr) {
                        $expr->{'LCASE('}->value('foo')->{')'};
                    },
                ])->into('users')
            ],
            [
                'insert multiple rows',
                'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18), (\'bar\', 20), (\'baz\', NULL), (NULL, 30), (\'extra\', 26)',
                fn(Database $db) => $db
                    ->insert(
                        ['name' => 'foo', 'age' => 18],
                        ['age' => 20, 'name' => 'bar'],
                        ['name' => 'baz'],
                        ['age' => 30, 'email' => 'mail@example.com'],
                    )
                    ->insert(['name' => 'extra', 'age' => 26])
                    ->into('users'),
            ],
        ];
    }
}