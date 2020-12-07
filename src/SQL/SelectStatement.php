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

class SelectStatement extends BaseStatement
{
    protected HavingStatement $have;

    public function __construct(string|array $tables, SQLStatement $statement = null)
    {
        parent::__construct($statement);

        if (!is_array($tables)) {
            $tables = [$tables];
        }

        $this->sql->addTables($tables);
        $this->have = new HavingStatement($this->sql);
    }

    public function into(string $table, string $database = null): static
    {
        $this->sql->setInto($table, $database);
        return $this;
    }

    public function distinct(bool $value = true): static
    {
        $this->sql->setDistinct($value);
        return $this;
    }

    public function groupBy(mixed $columns): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->sql->addGroupBy($columns);
        return $this;
    }

    public function having(mixed $column, Closure $value = null): static
    {
        $this->have->having($column, $value);
        return $this;
    }

    public function andHaving(mixed $column, Closure $value = null): static
    {
        $this->have->andHaving($column, $value);
        return $this;
    }

    public function orHaving(mixed $column, Closure $value = null): static
    {
        $this->have->orHaving($column, $value);
        return $this;
    }

    public function orderBy(mixed $columns, string $order = 'ASC', string $nulls = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->sql->addOrder($columns, $order, $nulls);
        return $this;
    }

    public function limit(int $value): static
    {
        $this->sql->setLimit($value);
        return $this;
    }

    public function offset(int $value): static
    {
        $this->sql->setOffset($value);
        return $this;
    }

    public function select(mixed $columns = []): mixed
    {
        $expr = new ColumnExpression($this->sql);

        if ($columns instanceof Closure) {
            $columns($expr);
        } else {
            if (!is_array($columns)) {
                $columns = [$columns];
            }
            $expr->columns($columns);
        }

        return null;
    }

    public function column(mixed $name): mixed
    {
        (new ColumnExpression($this->sql))->column($name);
        return null;
    }

    public function count(mixed $column = '*', bool $distinct = false): mixed
    {
        (new ColumnExpression($this->sql))->count($column, null, $distinct);
        return 0;
    }

    public function avg(mixed $column, bool $distinct = false): mixed
    {
        (new ColumnExpression($this->sql))->avg($column, null, $distinct);
        return 0;
    }

    public function sum(mixed $column, bool $distinct = false): mixed
    {
        (new ColumnExpression($this->sql))->sum($column, null, $distinct);
        return 0;
    }

    public function min(mixed $column, bool $distinct = false): mixed
    {
        (new ColumnExpression($this->sql))->min($column, null, $distinct);
        return 0;
    }

    public function max(mixed $column, bool $distinct = false): mixed
    {
        (new ColumnExpression($this->sql))->max($column, null, $distinct);
        return 0;
    }

    public function __clone()
    {
        parent::__clone();
        $this->have = new HavingStatement($this->sql);
    }
}
