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

use Opis\Database\Database;
use Opis\Database\Test\Connection;

class InsertOracleTest extends BaseClass
{
    public static function setUpBeforeClass(): void
    {
        static::$database = new Database(new Connection('oci'));
    }

    public function testMultipleRowsUseInsertAll()
    {
        $expected = 'INSERT ALL INTO "USERS" ("NAME", "AGE") VALUES (\'foo\', 18)'
            . ' INTO "USERS" ("NAME", "AGE") VALUES (\'bar\', 21) SELECT * FROM dual';
        $actual = $this->db->insert([
            ['name' => 'foo', 'age' => 18],
            ['name' => 'bar', 'age' => 21],
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testSingleRowIsUnaffected()
    {
        $expected = 'INSERT INTO "USERS" ("NAME", "AGE") VALUES (\'foo\', 18)';
        $actual = $this->db->insert(['name' => 'foo', 'age' => 18])->into('users');
        $this->assertEquals($expected, $actual);
    }
}
