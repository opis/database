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

class Having
{
    protected SQLStatement $sql;
    protected mixed $aggregate = null;
    protected ?string $separator = null;

    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
    }

    public function init(mixed $aggregate, string $separator): static
    {
        if ($aggregate instanceof Closure) {
            $aggregate = Expression::fromClosure($aggregate);
        }
        $this->aggregate = $aggregate;
        $this->separator = $separator;
        return $this;
    }

    public function eq(mixed $value, bool $is_column = false): void
    {
        $this->addCondition($value, '=', $is_column);
    }

    public function ne(mixed $value, bool $is_column = false): void
    {
        $this->addCondition($value, '!=', $is_column);
    }

    public function lt(mixed $value, bool $is_column = false): void
    {
        $this->addCondition($value, '<', $is_column);
    }

    public function gt(mixed $value, bool $is_column = false): void
    {
        $this->addCondition($value, '>', $is_column);
    }

    public function lte(mixed $value, bool $is_column = false): void
    {
        $this->addCondition($value, '<=', $is_column);
    }

    public function gte(mixed $value, bool $is_column = false): void
    {
        $this->addCondition($value, '>=', $is_column);
    }

    public function in(mixed $value): void
    {
        $this->sql->addHavingInCondition($this->aggregate, $value, $this->separator, false);
    }

    public function notIn(mixed $value): void
    {
        $this->sql->addHavingInCondition($this->aggregate, $value, $this->separator, true);
    }

    public function between(mixed $value1, mixed $value2): void
    {
        $this->sql->addHavingBetweenCondition($this->aggregate, $value1, $value2, $this->separator, false);
    }

    public function notBetween(mixed $value1, mixed $value2): void
    {
        $this->sql->addHavingBetweenCondition($this->aggregate, $value1, $value2, $this->separator, true);
    }

    public function __clone()
    {
        if ($this->aggregate instanceof Expression) {
            $this->aggregate = clone $this->aggregate;
        }
        $this->sql = clone $this->sql;
    }

    protected function addCondition(mixed $value, string $operator, bool $is_column): void
    {
        if ($is_column && is_string($value)) {
            $expr = new Expression();
            $value = $expr->column($value);
        }

        $this->sql->addHavingCondition($this->aggregate, $value, $operator, $this->separator);
    }
}
