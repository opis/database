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

use Opis\Database\DatabaseHandler;
use Opis\Database\SQL\Expression;

class OrderTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'order asc',
                'SELECT * FROM "users" ORDER BY "name" ASC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name')->select(),
            ],
            [
                'order desc',
                'SELECT * FROM "users" ORDER BY "name" DESC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name', 'desc')->select(),
            ],
            [
                'order asc multiple',
                'SELECT * FROM "users" ORDER BY "name", "age" ASC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy(['name', 'age'])->select(),
            ],
            [
                'order desc multiple',
                'SELECT * FROM "users" ORDER BY "name", "age" DESC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy(['name', 'age'], 'desc')->select(),
            ],
            [
                'order asc desc',
                'SELECT * FROM "users" ORDER BY "name" ASC, "age" DESC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name')->orderBy('age', 'desc')->select(),
            ],
            [
                'order nulls first',
                'SELECT * FROM "users" ORDER BY "name" ASC, (CASE WHEN "age" IS NULL THEN 0 ELSE 1 END), "age" DESC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name')->orderBy('age', 'desc', 'nulls first')->select(),
            ],
            [
                'order mulls last',
                'SELECT * FROM "users" ORDER BY "name" ASC, (CASE WHEN "age" IS NULL THEN 1 ELSE 0 END), "age" DESC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name')->orderBy('age', 'desc', 'nulls last')->select(),
            ],
            [
                'order expression',
                'SELECT * FROM "users" ORDER BY LEN("name") ASC',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy(fn (Expression $expr) => $expr->len('name'))->select(),
            ],
        ];
    }
}