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

class OrderTest extends BaseClass
{
    public function testOrderAsc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC';
        $actual = $this->db->from('users')->orderBy('name')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderDesc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" DESC';
        $actual = $this->db->from('users')->orderBy('name', 'desc')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderMultipleColumnsAsc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name", "age" ASC';
        $actual = $this->db->from('users')->orderBy(['name', 'age'])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderMultipleColumnsDesc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name", "age" DESC';
        $actual = $this->db->from('users')->orderBy(['name', 'age'], 'desc')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderAscDesc()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC, "age" DESC';
        $actual = $this->db->from('users')->orderBy('name')->orderBy('age', 'desc')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderNullsFirst()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC, (CASE WHEN "age" IS NULL THEN 0 ELSE 1 END), "age" DESC';
        $actual = $this->db->from('users')->orderBy('name')->orderBy('age', 'desc', 'nulls first')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOrderNullsLast()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC, (CASE WHEN "age" IS NULL THEN 1 ELSE 0 END), "age" DESC';
        $actual = $this->db->from('users')->orderBy('name')->orderBy('age', 'desc', 'nulls last')->select();
        $this->assertEquals($expected, $actual);
    }
}