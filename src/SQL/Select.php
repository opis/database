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

namespace Opis\Database\SQL;

use Closure;
use Opis\Database\Connection;
use Opis\Database\ResultSet;

class Select extends SelectStatement
{
    protected Connection $connection;

    public function __construct(Connection $connection, string|array $tables, SQLStatement $statement = null)
    {
        parent::__construct($tables, $statement);
        $this->connection = $connection;
    }

    public function select(mixed $columns = []): ResultSet
    {
        parent::select($columns);
        $compiler = $this->connection->getCompiler();
        return $this->connection->query($compiler->select($this->sql), $compiler->getParams());
    }

    public function column(mixed $name): mixed
    {
        parent::column($name);
        return $this->getColumnResult();
    }

    public function count(mixed $column = '*', bool $distinct = false): int
    {
        parent::count($column, $distinct);
        return $this->getColumnResult() ?? 0;
    }

    public function avg(mixed $column, bool $distinct = false): int|float
    {
        parent::avg($column, $distinct);
        return $this->getColumnResult() ?? 0;
    }

    public function sum(mixed $column, bool $distinct = false): int|float
    {
        parent::sum($column, $distinct);
        return $this->getColumnResult() ?? 0;
    }

    public function min(mixed $column, bool $distinct = false): int|float
    {
        parent::min($column, $distinct);
        return $this->getColumnResult() ?? 0;
    }

    public function max(mixed $column, bool $distinct = false): int|float
    {
        parent::max($column, $distinct);
        return $this->getColumnResult() ?? 0;
    }

    protected function getColumnResult(): mixed
    {
        $compiler = $this->connection->getCompiler();
        return $this->connection->column($compiler->select($this->sql), $compiler->getParams());
    }
}
