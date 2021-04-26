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

class DeleteTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'delete all',
                'DELETE FROM "users"',
                fn(DatabaseHandler $db) => $db->from("users")->delete(),
            ],
            [
                'delete where condition',
                'DELETE FROM "users" WHERE "age" < 18',
                fn(DatabaseHandler $db) => $db->from("users")
                    ->where('age')->lt(18)
                    ->delete(),
            ],
            [
                'delete where expression',
                'DELETE FROM "users" WHERE LEN("name") < 18',
                fn(DatabaseHandler $db) => $db->from("users")
                    ->whereExpression(fn (Expression $expr) => $expr->len("name"))
                    ->lt(18)
                    ->delete()
            ],
        ];
    }
}