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

namespace Opis\Database\Test\Schema;

use Opis\Database\Test\Connection;
use Opis\Database\Test\Schema;

class MySqlTest extends BaseClass
{
    public static function setUpBeforeClass()
    {
        static::$data = [
            'testCreateTable' => implode("\n", [
                'CREATE TABLE `foo`(', '', ')'
            ]),
            'testAddSingleColumn' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                ]), ')'
            ]),
            'testAddMultipleColumns' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    '`b` INT'
                ]), ')'
            ]),
            'testTypes' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    '`b` FLOAT',
                    '`c` DOUBLE',
                    '`d` DECIMAL',
                    '`d1` DECIMAL(4)',
                    '`d2` DECIMAL(4, 8)',
                    '`e` TINYINT(1)',
                    '`f` VARCHAR(255)',
                    '`f1` VARCHAR(32)',
                    '`g` CHAR(255)',
                    '`g1` CHAR(2)',
                    '`h` DATE',
                    '`i` DATETIME',
                    '`j` TIMESTAMP',
                    '`k` TIME',
                    '`l` BLOB',
                    '`m` TEXT',
                ]), ')'
            ]),
            'testIntSizes' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` TINYINT',
                    '`b` SMALLINT',
                    '`c` INT',
                    '`d` MEDIUMINT',
                    '`e` BIGINT',
                ]), ')'
            ]),
            'testTextSizes' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` TINYTEXT',
                    '`b` TINYTEXT',
                    '`c` TEXT',
                    '`d` MEDIUMTEXT',
                    '`e` LONGTEXT',
                ]), ')'
            ]),
            'testBinarySizes' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` TINYBLOB',
                    '`b` TINYBLOB',
                    '`c` BLOB',
                    '`d` MEDIUMBLOB',
                    '`e` LONGBLOB',
                ]), ')'
            ]),
            'testColumnProperties' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT UNSIGNED',
                    '`b` FLOAT DEFAULT 0.1',
                    '`c` VARCHAR(255) NOT NULL'
                ]), ')'
            ]),
            'testColumnConstraints' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    '`b` INT',
                    'CONSTRAINT `foo_pk_a` PRIMARY KEY (`a`)',
                    'CONSTRAINT `foo_uk_b` UNIQUE (`b`)',
                ]), ')'
            ]),
            'testColumnNamedConstraints' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    '`b` INT',
                    'CONSTRAINT `pk_a` PRIMARY KEY (`a`)',
                    'CONSTRAINT `uk_b` UNIQUE (`b`)',
                ]), ')'
            ]),
            'testAutoincrement' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT AUTO_INCREMENT',
                    'CONSTRAINT `foo_pk_a` PRIMARY KEY (`a`)',
                ]), ')'
            ]),
            'testNamedAutoincrement' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT AUTO_INCREMENT',
                    'CONSTRAINT `x` PRIMARY KEY (`a`)',
                ]), ')'
            ]),
            'testIndex' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    '`b` INT',
                    '`c` INT',
                    '`d` INT',
                ]), implode("\n", [
                    ')',
                    'CREATE INDEX `foo_ik_a` ON `foo`(`a`)',
                    'CREATE INDEX `x` ON `foo`(`b`)',
                    'CREATE INDEX `foo_ik_c` ON `foo`(`c`)',
                    'CREATE INDEX `y` ON `foo`(`d`)',
                    'CREATE INDEX `foo_ik_a_b` ON `foo`(`a`, `b`)',
                    'CREATE INDEX `z` ON `foo`(`c`, `d`)',
                ])
            ]),
            'testForeignKey' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    'CONSTRAINT `foo_fk_a` FOREIGN KEY (`a`) REFERENCES `bar` (`a`) ON UPDATE CASCADE ON DELETE CASCADE',
                ]), ')'
            ]),
            'testForeignKeyMultiple' => implode("\n", [
                'CREATE TABLE `foo`(', implode(",\n", [
                    '`a` INT',
                    '`b` INT',
                    'CONSTRAINT `foo_fk_a_b` FOREIGN KEY (`a`, `b`) REFERENCES `bar` (`a`, `b`) ON UPDATE CASCADE ON DELETE CASCADE',
                ]), ')'
            ]),
        ];
    }

    public function setUp()
    {
        $this->schema = new Schema(new Connection("mysql"));
    }
}