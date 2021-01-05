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

class SelectAggregateTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'count *',
                'SELECT COUNT(*) FROM "users"',
                fn(Database $db) => $db->from('users')->count(),
            ],
            [
                'count column',
                'SELECT COUNT("description") FROM "users"',
                fn(Database $db) => $db->from('users')->count('description'),
            ],
            [
                'count distinct column',
                'SELECT COUNT(DISTINCT "description") FROM "users"',
                fn(Database $db) => $db->from('users')->count('description', true),
            ],
            [
                'max value',
                'SELECT MAX("age") FROM "users"',
                fn(Database $db) => $db->from('users')->max('age'),
            ],
            [
                'min value',
                'SELECT MIN("age") FROM "users"',
                fn(Database $db) => $db->from('users')->min('age'),
            ],
            [
                'avg value',
                'SELECT AVG("age") FROM "users"',
                fn(Database $db) => $db->from('users')->avg('age'),
            ],
            [
                'sum value',
                'SELECT SUM("age") FROM "users"',
                fn(Database $db) => $db->from('users')->sum('age'),
            ],
            [
                'expression aggregate',
                'SELECT SUM("friends" - "enemies") FROM "users"',
                fn(Database $db) => $db->from('users')
                    ->sum(fn (Expression $expr) => $expr->column('friends')->{'-'}->column("enemies")),
            ],
        ];
    }
}