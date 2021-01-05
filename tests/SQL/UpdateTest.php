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

class UpdateTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'update one column',
                'UPDATE "users" SET "age" = 18',
                fn(Database $db) => $db->update('users')->set(['age' => 18]),
            ],
            [
                'update multiple columns',
                'UPDATE "users" SET "age" = 18, "name" = \'foo\'',
                fn(Database $db) => $db->update('users')->set(['age' => 18, 'name' => 'foo']),
            ],
            [
                'update col as col',
                'UPDATE "users" SET "name" = "username"',
                fn(Database $db) => $db->update('users')->set([
                    'name' => fn (Expression $expr) => $expr->column("username"),
                ]),
            ],
            [
                'update increment by 1',
                'UPDATE "users" SET "age" = "age" + 1',
                fn(Database $db) => $db->update('users')->increment("age"),
            ],
            [
                'update increment by N',
                'UPDATE "users" SET "age" = "age" + 5',
                fn(Database $db) => $db->update('users')->increment("age", 5),
            ],
            [
                'update increment multiple by 1',
                'UPDATE "users" SET "age" = "age" + 1, "foo" = "foo" + 1',
                fn(Database $db) => $db->update('users')->increment(["age", "foo"]),
            ],
            [
                'update increment multiple by N',
                'UPDATE "users" SET "age" = "age" + 5, "foo" = "foo" + 5',
                fn(Database $db) => $db->update('users')->increment(["age", "foo"], 5),
            ],
            [
                'update decrement by 1',
                'UPDATE "users" SET "age" = "age" - 1',
                fn(Database $db) => $db->update('users')->decrement("age"),
            ],
            [
                'update decrement by N',
                'UPDATE "users" SET "age" = "age" - 5',
                fn(Database $db) => $db->update('users')->decrement("age", 5),
            ],
            [
                'update decrement multiple by 1',
                'UPDATE "users" SET "age" = "age" - 1, "foo" = "foo" - 1',
                fn(Database $db) => $db->update('users')->decrement(["age", "foo"]),
            ],
            [
                'update decrement multiple by N',
                'UPDATE "users" SET "age" = "age" - 5, "foo" = "foo" - 5',
                fn(Database $db) => $db->update('users')->decrement(["age", "foo"], 5),
            ],
        ];
    }
}