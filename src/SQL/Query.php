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

namespace Opis\Database\SQL;

use Closure;
use Opis\Database\{Connection, ResultSet};

class Query extends BaseStatement
{
    protected Connection $connection;
    protected string|array $tables;

    public function __construct(Connection $connection, string|array $tables, ?SQLStatement $statement = null)
    {
        parent::__construct($statement);
        $this->tables = $tables;
        $this->connection = $connection;
    }

    public function distinct(bool $value = true): Select
    {
        return $this->buildSelect()->distinct($value);
    }

    public function groupBy(mixed $columns): Select
    {
        return $this->buildSelect()->groupBy($columns);
    }

    public function having(mixed $column, Closure $value = null): Select
    {
        return $this->buildSelect()->having($column, $value);
    }

    public function andHaving(mixed $column, Closure $value = null): Select
    {
        return $this->buildSelect()->andHaving($column, $value);
    }

    public function orHaving(mixed $column, Closure $value = null): Select
    {
        return $this->buildSelect()->orHaving($column, $value);
    }

    public function orderBy(mixed $columns, string $order = 'ASC', ?string $nulls = null): Select
    {
        return $this->buildSelect()->orderBy($columns, $order, $nulls);
    }

    public function limit(int $value): Select
    {
        return $this->buildSelect()->limit($value);
    }

    public function offset(int $value): Select
    {
        return $this->buildSelect()->offset($value);
    }

    public function into(string $table, ?string $database = null): Select
    {
        return $this->buildSelect()->into($table, $database);
    }

    public function select(mixed $columns = []): ResultSet
    {
        return $this->buildSelect()->select($columns);
    }

    public function column(mixed $name): mixed
    {
        return $this->buildSelect()->column($name);
    }

    public function count(mixed $column = '*', bool $distinct = false): int
    {
        return $this->buildSelect()->count($column, $distinct);
    }

    public function avg(mixed $column, bool $distinct = false): int|float
    {
        return $this->buildSelect()->avg($column, $distinct);
    }

    public function sum(mixed $column, bool $distinct = false): int|float
    {
        return $this->buildSelect()->sum($column, $distinct);
    }

    public function min(mixed $column, bool $distinct = false): int|float
    {
        return $this->buildSelect()->min($column, $distinct);
    }

    public function max(mixed $column, bool $distinct = false): int|float
    {
        return $this->buildSelect()->max($column, $distinct);
    }

    public function delete(string|array $tables = []): int
    {
        return $this->buildDelete()->delete($tables);
    }

    protected function buildSelect(): Select
    {
        return new Select($this->connection, $this->tables, $this->sql);
    }

    protected function buildDelete(): Delete
    {
        return new Delete($this->connection, $this->tables, $this->sql);
    }
}
