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

class ColumnExpression
{
    protected SQLStatement $sql;

    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
    }

    public function column(mixed $name, string $alias = null): static
    {
        $this->sql->addColumn($name, $alias);
        return $this;
    }

    public function columns(array $columns): static
    {
        foreach ($columns as $name => $alias) {
            if (!is_string($name)) {
                $this->column($alias, null);
                continue;
            }
            if (is_string($alias)) {
                $this->column($name, $alias);
            } else {
                $this->column($alias, $name);
            }
        }
        return $this;
    }

    public function count(mixed $column = '*', string $alias = null, bool $distinct = false): static
    {
        return $this->column((new Expression())->count($column, $distinct), $alias);
    }

    public function avg(mixed $column, string $alias = null, bool $distinct = false): static
    {
        return $this->column((new Expression())->avg($column, $distinct), $alias);
    }

    public function sum(mixed $column, string $alias = null, bool $distinct = false): static
    {
        return $this->column((new Expression())->sum($column, $distinct), $alias);
    }

    public function min(mixed $column, string $alias = null, bool $distinct = false): static
    {
        return $this->column((new Expression())->min($column, $distinct), $alias);
    }

    public function max(mixed $column, string $alias = null, bool $distinct = false): static
    {
        return $this->column((new Expression())->max($column, $distinct), $alias);
    }

    public function ucase(mixed $column, string $alias = null): static
    {
        return $this->column((new Expression())->ucase($column), $alias);
    }

    public function lcase(mixed $column, string $alias = null): static
    {
        return $this->column((new Expression())->lcase($column), $alias);
    }

    public function mid(mixed $column, int $start = 1, string $alias = null, int $length = 0): static
    {
        return $this->column((new Expression())->mid($column, $start, $length), $alias);
    }

    public function len(mixed $column, string $alias = null): static
    {
        return $this->column((new Expression())->len($column), $alias);
    }

    public function round(mixed $column, int $decimals = 0, string $alias = null): static
    {
        return $this->column((new Expression())->format($column, $decimals), $alias);
    }

    public function format(mixed $column, int $format, string $alias = null): static
    {
        return $this->column((new Expression())->format($column, $format), $alias);
    }

    public function now(string $alias = null): static
    {
        return $this->column((new Expression())->now(), $alias);
    }

    public function __clone()
    {
        $this->sql = clone $this->sql;
    }
}
