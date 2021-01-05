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

class HavingExpression
{
    protected SQLStatement $sql;
    protected Having $having;
    protected mixed $column = null;
    protected ?string $separator = null;

    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
        $this->having = new Having($statement);
    }

    public function init(mixed $column, string $separator): static
    {
        if ($column instanceof Closure) {
            $column = Expression::fromClosure($column);
        }
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    public function count(bool $distinct = false): Having
    {
        $value = (new Expression())->count($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    public function avg(bool $distinct = false): Having
    {
        $value = (new Expression())->avg($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    public function sum(bool $distinct = false): Having
    {
        $value = (new Expression())->sum($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    public function min(bool $distinct = false): Having
    {
        $value = (new Expression())->min($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    public function max(bool $distinct = false): Having
    {
        $value = (new Expression())->max($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    public function __clone()
    {
        if ($this->column instanceof Expression) {
            $this->column = clone $this->column;
        }
        $this->sql = clone $this->sql;
        $this->having = new Having($this->sql);
    }
}
