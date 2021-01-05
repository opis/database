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

class Where
{
    protected mixed $column = null;
    protected ?string $separator = null;
    protected SQLStatement $sql;
    protected WhereStatement $statement;

    public function __construct(WhereStatement $statement, SQLStatement $sql)
    {
        $this->sql = $sql;
        $this->statement = $statement;
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

    public function is(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->addCondition($value, '=', $is_column);
    }

    public function isNot(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->addCondition($value, '!=', $is_column);
    }

    public function lessThan(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->addCondition($value, '<', $is_column);
    }

    public function greaterThan(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->addCondition($value, '>', $is_column);
    }

    public function atLeast(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->addCondition($value, '>=', $is_column);
    }

    public function atMost(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->addCondition($value, '<=', $is_column);
    }

    public function between(mixed $value1, mixed $value2): WhereStatement|Select|Update|Delete
    {
        return $this->addBetweenCondition($value1, $value2, false);
    }

    public function notBetween(mixed $value1, mixed $value2): WhereStatement|Select|Update|Delete
    {
        return $this->addBetweenCondition($value1, $value2, true);
    }

    public function like(string $value): WhereStatement|Select|Update|Delete
    {
        return $this->addLikeCondition($value, false);
    }

    public function notLike(string $value): WhereStatement|Select|Update|Delete
    {
        return $this->addLikeCondition($value, true);
    }

    public function in(mixed $value): WhereStatement|Select|Update|Delete
    {
        return $this->addInCondition($value, false);
    }

    public function notIn(mixed $value): WhereStatement|Select|Update|Delete
    {
        return $this->addInCondition($value, true);
    }

    public function isNull(): WhereStatement|Select|Update|Delete
    {
        return $this->addNullCondition(false);
    }

    public function isNotNull(): WhereStatement|Select|Update|Delete
    {
        return $this->addNullCondition(true);
    }

    public function notNull(): WhereStatement|Select|Update|Delete
    {
        return $this->addNullCondition(true);
    }

    public function eq(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->is($value, $is_column);
    }

    public function ne(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->isNot($value, $is_column);
    }

    public function lt(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->lessThan($value, $is_column);
    }

    public function gt(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->greaterThan($value, $is_column);
    }

    public function gte(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->atLeast($value, $is_column);
    }

    public function lte(mixed $value, bool $is_column = false): WhereStatement|Select|Update|Delete
    {
        return $this->atMost($value, $is_column);
    }

    public function nop(): WhereStatement|Select|Update|Delete
    {
        $this->sql->addWhereNop($this->column, $this->separator);
        return $this->statement;
    }

    public function __clone()
    {
        if ($this->column instanceof Expression) {
            $this->column = clone $this->column;
        }
        $this->sql = clone $this->sql;
        $this->statement = new WhereStatement($this->sql);
    }

    protected function addCondition(mixed $value, string $operator, bool $isColumn = false): WhereStatement|Select|Update|Delete
    {
        if ($isColumn && is_string($value)) {
            $value = static fn (Expression $expr) => $expr->column($value);
        }
        $this->sql->addWhereCondition($this->column, $value, $operator, $this->separator);
        return $this->statement;
    }

    protected function addBetweenCondition(mixed $value1, mixed $value2, bool $not): WhereStatement|Select|Update|Delete
    {
        $this->sql->addWhereBetweenCondition($this->column, $value1, $value2, $this->separator, $not);
        return $this->statement;
    }

    protected function addLikeCondition(string $pattern, bool $not): WhereStatement|Select|Update|Delete
    {
        $this->sql->addWhereLikeCondition($this->column, $pattern, $this->separator, $not);
        return $this->statement;
    }

    protected function addInCondition(mixed $value, bool $not): WhereStatement|Select|Update|Delete
    {
        $this->sql->addWhereInCondition($this->column, $value, $this->separator, $not);
        return $this->statement;
    }

    protected function addNullCondition(bool $not): WhereStatement|Select|Update|Delete
    {
        $this->sql->addWhereNullCondition($this->column, $this->separator, $not);
        return $this->statement;
    }
}
