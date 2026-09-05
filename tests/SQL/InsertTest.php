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

    public function testInsertBooleanValues()
    {
        $expected = 'INSERT INTO "test" ("foo", "bar") VALUES (TRUE, FALSE)';
        $actual = $this->db->insert(['foo' => true, 'bar' => false])->into('test');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertExpressions()
    {
        $expected = 'INSERT INTO "users" ("name") VALUES (LCASE( \'foo\' ))';
        $actual = $this->db->insert([
            'name' => function (Expression $expr) {
                $expr->{'LCASE('}->value('foo')->{')'};
            },
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRows()
    {
        $expected = 'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18), (\'bar\', 21)';
        $actual = $this->db->insert([
            ['name' => 'foo', 'age' => 18],
            ['name' => 'bar', 'age' => 21],
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsPreservesFirstRowColumnOrder()
    {
        $expected = 'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18), (\'bar\', 21)';
        $actual = $this->db->insert([
            ['name' => 'foo', 'age' => 18],
            ['age' => 21, 'name' => 'bar'],
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsFromNonSequentialList()
    {
        $rows = [
            ['name' => 'foo', 'age' => 18],
            ['name' => 'skipped', 'age' => 0],
            ['name' => 'bar', 'age' => 21],
        ];

        $rows = array_filter($rows, function (array $row) {
            return $row['age'] > 0;
        });

        $expected = 'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18), (\'bar\', 21)';
        $actual = $this->db->insert($rows)->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsWithExpressions()
    {
        $expected = 'INSERT INTO "users" ("name") VALUES (LCASE( \'foo\' )), (LCASE( \'bar\' ))';
        $actual = $this->db->insert([
            [
                'name' => function (Expression $expr) {
                    $expr->{'LCASE('}->value('foo')->{')'};
                },
            ],
            [
                'name' => function (Expression $expr) {
                    $expr->{'LCASE('}->value('bar')->{')'};
                },
            ],
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsWithNullAndBoolean()
    {
        $expected = 'INSERT INTO "test" ("foo", "bar") VALUES (NULL, TRUE), (FALSE, NULL)';
        $actual = $this->db->insert([
            ['foo' => null, 'bar' => true],
            ['foo' => false, 'bar' => null],
        ])->into('test');
        $this->assertEquals($expected, $actual);
    }

    public function testChainedInsertAppendsRows()
    {
        $expected = 'INSERT INTO "users" ("name", "age") VALUES (\'foo\', 18), (\'bar\', 21)';
        $actual = $this->db->insert(['name' => 'foo', 'age' => 18])
            ->insert(['name' => 'bar', 'age' => 21])
            ->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertRejectsRowWithMissingColumn()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row 3 is missing the column "score"');

        $this->db->insert([
            ['tag' => 'a', 'score' => 1],
            ['tag' => 'b', 'score' => 2],
            ['tag' => 'c'],
        ])->into('tags');
    }

    public function testInsertRejectsRowWithUnknownColumn()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row 3 contains an unknown column "extra"');

        $this->db->insert([
            ['tag' => 'a', 'score' => 1],
            ['tag' => 'b', 'score' => 2],
            ['tag' => 'c', 'score' => 3, 'extra' => 4],
        ])->into('tags');
    }

    public function testInsertEmptyArrayIsUnchanged()
    {
        $expected = 'INSERT INTO "users" VALUES ()';
        $actual = $this->db->insert([])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsWithVaryingExpressionArity()
    {
        $expected = 'INSERT INTO "users" ("name") VALUES (CONCAT( \'foo\' , \'bar\' )), (NOW()), (\'baz\')';
        $actual = $this->db->insert([
            [
                'name' => function (Expression $expr) {
                    $expr->{'CONCAT('}->value('foo')->{','}->value('bar')->{')'};
                },
            ],
            [
                'name' => function (Expression $expr) {
                    $expr->now();
                },
            ],
            [
                'name' => 'baz',
            ],
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsWithDateTime()
    {
        $expected = 'INSERT INTO "users" ("name", "created_at") VALUES (\'foo\', \'2023-01-01 10:00:00\'), (\'bar\', \'2023-01-01 12:00:00\')';
        $actual = $this->db->insert([
            ['name' => 'foo', 'created_at' => new \DateTime('2023-01-01 10:00:00')],
            ['name' => 'bar', 'created_at' => new \DateTime('2023-01-01 12:00:00')],
        ])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testChainedInsertReportsRowPositionAcrossCalls()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row 3 contains an unknown column "extra"');

        $this->db->insert(['name' => 'foo', 'age' => 18])
            ->insert(['name' => 'bar', 'age' => 21])
            ->insert(['name' => 'baz', 'age' => 30, 'extra' => 1])
            ->into('users');
    }

    public function testInsertEmptyArrayThenRow()
    {
        $expected = 'INSERT INTO "users" ("name") VALUES (\'foo\')';
        $actual = $this->db->insert([])->insert(['name' => 'foo'])->into('users');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertMultipleRowsAsSeparateArguments()
    {
        $expected = $this->db->insert([
            ['tag' => 'asd', 'score' => 1],
            ['tag' => 'asd2', 'score' => 2],
            ['tag' => 'asd3', 'score' => 3],
        ])->into('tags');

        $actual = $this->db->insert(
            ['tag' => 'asd', 'score' => 1],
            ['tag' => 'asd2', 'score' => 2],
            ['tag' => 'asd3', 'score' => 3]
        )->into('tags');

        $this->assertEquals($expected, $actual);
    }

    public function testInsertMixedArgumentForms()
    {
        $expected = 'INSERT INTO "tags" ("tag", "score") VALUES (\'a\', 1), (\'b\', 2), (\'c\', 3)';
        $actual = $this->db->insert(
            [
                ['tag' => 'a', 'score' => 1],
                ['tag' => 'b', 'score' => 2],
            ],
            ['tag' => 'c', 'score' => 3]
        )->into('tags');
        $this->assertEquals($expected, $actual);
    }

    public function testInsertSeparateArgumentsRejectMismatchedRow()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Row 2 is missing the column "score"');

        $this->db->insert(
            ['tag' => 'a', 'score' => 1],
            ['tag' => 'b']
        )->into('tags');
    }
}