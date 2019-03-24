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

use Opis\Database\SQL\Expression;

class UpdateTest extends BaseClass
{
    public function testUpdate()
    {
        $expected = 'UPDATE "users" SET "age" = 18';
        $actual = $this->db->update('users')->set(['age' => 18]);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateMultiple()
    {
        $expected = 'UPDATE "users" SET "age" = 18, "name" = \'foo\'';
        $actual = $this->db->update('users')->set(['age' => 18, 'name' => 'foo']);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateColAsCol()
    {
        $expected = 'UPDATE "users" SET "name" = "username"';
        $actual = $this->db->update('users')->set([
            'name' => function (Expression $expr) {
                $expr->column("username");
            },
        ]);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateIncrementByOne()
    {
        $expected = 'UPDATE "users" SET "age" = "age" + 1';
        $actual = $this->db->update('users')->increment("age");
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateIncrementMultipleByOne()
    {
        $expected = 'UPDATE "users" SET "age" = "age" + 1, "foo" = "foo" + 1';
        $actual = $this->db->update('users')->increment(["age", "foo"]);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateIncrementByN()
    {
        $expected = 'UPDATE "users" SET "age" = "age" + 5';
        $actual = $this->db->update('users')->increment("age", 5);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateIncrementMultipleByN()
    {
        $expected = 'UPDATE "users" SET "age" = "age" + 5, "foo" = "foo" + 5';
        $actual = $this->db->update('users')->increment(["age", "foo"], 5);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateDecrementByOne()
    {
        $expected = 'UPDATE "users" SET "age" = "age" - 1';
        $actual = $this->db->update('users')->decrement("age");
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateDecrementMultipleByOne()
    {
        $expected = 'UPDATE "users" SET "age" = "age" - 1, "foo" = "foo" - 1';
        $actual = $this->db->update('users')->decrement(["age", "foo"]);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateDecrementByN()
    {
        $expected = 'UPDATE "users" SET "age" = "age" - 5';
        $actual = $this->db->update('users')->decrement("age", 5);
        $this->assertEquals($expected, $actual);
    }

    public function testUpdateDecrementMultipleByN()
    {
        $expected = 'UPDATE "users" SET "age" = "age" - 5, "foo" = "foo" - 5';
        $actual = $this->db->update('users')->decrement(["age", "foo"], 5);
        $this->assertEquals($expected, $actual);
    }
}