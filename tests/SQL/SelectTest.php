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

use Opis\Database\SQL\ColumnExpression;
use Opis\Database\SQL\Expression;

class SelectTest extends BaseClass
{
    public function testSelect()
    {
        $expected = 'SELECT * FROM "users"';
        $actual = $this->db->from('users')->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectDistinct()
    {
        $expected = 'SELECT DISTINCT * FROM "users"';
        $actual = $this->db->from('users')->distinct()->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectSingleColumn()
    {
        $expected = 'SELECT "name" FROM "users"';
        $actual = $this->db->from('users')->select('name');
        $this->assertEquals($expected, $actual);
    }

    public function testSelectSingleColumnArray()
    {
        $expected = 'SELECT "name" FROM "users"';
        $actual = $this->db->from('users')->select(['name']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectMultipleColumns()
    {
        $expected = 'SELECT "name", "age" FROM "users"';
        $actual = $this->db->from('users')->select(['name', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsAliases()
    {
        $expected = 'SELECT "name" AS "n", "age" AS "a" FROM "users"';
        $actual = $this->db->from('users')->select(['name' => 'n', 'age' => 'a']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsFirstAliased()
    {
        $expected = 'SELECT "name" AS "n", "age" FROM "users"';
        $actual = $this->db->from('users')->select(['name' => 'n', 'age']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsLastAliased()
    {
        $expected = 'SELECT "name", "age" AS "a" FROM "users"';
        $actual = $this->db->from('users')->select(['name', 'age' => 'a']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectFromMultipleTables()
    {
        $expected = 'SELECT * FROM "users", "sites"';
        $actual = $this->db->from(['users', 'sites'])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectFromMultipleTablesAliased()
    {
        $expected = 'SELECT * FROM "users" AS "u", "sites" AS "s"';
        $actual = $this->db->from(['users' => 'u', 'sites' => 's'])->select();
        $this->assertEquals($expected, $actual);
    }

    public function testSelectColumnsFromMultipleTablesAliased()
    {
        $expected = 'SELECT "u"."name", "s"."address" FROM "users" AS "u", "sites" AS "s"';
        $actual = $this->db->from(['users' => 'u', 'sites' => 's'])->select(['u.name', 's.address']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectAliasedColumnsFromMultipleTablesAliased()
    {
        $expected = 'SELECT "u"."name" AS "n", "s"."address" AS "s" FROM "users" AS "u", "sites" AS "s"';
        $actual = $this->db->from(['users' => 'u', 'sites' => 's'])->select(['u.name' => 'n', 's.address' => 's']);
        $this->assertEquals($expected, $actual);
    }

    public function testSelectAliasedSingleExpression()
    {
        $expected = 'SELECT LCASE("name") AS "lower_name" FROM "users"';
        $actual = $this->db->from('users')
            ->select(function (ColumnExpression $expr) {
                $expr->lcase('name', 'lower_name');
            });
        $this->assertEquals($expected, $actual);
    }

    public function testSelectAliasedExpressionMultiple()
    {
        $expected = 'SELECT "name", LEN("name") AS "name_length", "age" AS "alias_age" FROM "users"';
        $actual = $this->db->from('users')
            ->select([
                'name',
                'name_length' => function (Expression $expr) {
                    $expr->len('name');
                },
                'age' => 'alias_age',
            ]);
        $this->assertEquals($expected, $actual);
    }

}