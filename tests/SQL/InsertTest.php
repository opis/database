<?php
/* ===========================================================================
 * Copyright 2013-2018 Opis
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


class InsertTest extends BaseClass
{
    public function testInsertSingleValue()
    {
        $expected = 'INSERT INTO "users" ("age") VALUES (18)';
        $actual = $this->db->insert(['age' => 18])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleValues()
    {
        $expected = 'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18)';
        $actual = $this->db->insert(['name' => 'foo', 'age' => 18])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertExpressions()
    {
        $expected = 'INSERT INTO "users" ("name") VALUES (LCASE( \'foo\' ))';
        $actual = $this->db->insert(['name' => function($expr){
            $expr->{'LCASE('}->value('foo')->{')'};
        }])->into('users');
        $this->assertEquals($expected, $actual);
    }
}