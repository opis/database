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

use Closure;
use Opis\Database\Database;
use Opis\Database\Test\Connection;
use PHPUnit\Framework\TestCase;

abstract class BaseClass extends TestCase
{
    protected static Database $database;

    protected Database $db;

    public static function setUpBeforeClass(): void
    {
        static::$database = new Database(new Connection(''));
    }

    public function setUp(): void
    {
        $this->db = static::$database;
    }

    protected function getSQL(): string
    {
        /** @var Connection $connection */
        $connection = $this->db->getConnection();
        return $connection->getResult();
    }

    /**
     * @dataProvider sqlDataProvider
     */
    public function testSQL(string $message, string $expected, Closure $sql): void
    {
        $sql($this->db);
        $this->assertEquals($expected, $this->getSQL(), $message);
    }

    abstract public function sqlDataProvider(): iterable;
}