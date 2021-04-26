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

class LimitTest extends BaseClass
{
    public function sqlDataProvider(): iterable
    {
        return [
            [
                'limit',
                'SELECT * FROM "users" ORDER BY "name" ASC LIMIT 25',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name')->limit(25)->select(),
            ],
            [
                'offset',
                'SELECT * FROM "users" ORDER BY "name" ASC LIMIT 25 OFFSET 10',
                fn(DatabaseHandler $db) => $db->from('users')->orderBy('name')->limit(25)->offset(10)->select(),
            ],
            /*
            [
                'offset without limit',
                'SELECT * FROM "users" ORDER BY "name" ASC OFFSET 10',
                fn(Database $db) => $db->from('users')->orderBy('name')->offset(10)->select(),
            ],*/
        ];
    }
}