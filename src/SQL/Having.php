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
    /** @var  SQLStatement */
    protected $sql;

    /** @var    string|Expression */
    protected $aggregate;

    /** @var    string */
    protected $separator;

    /**
     * Having constructor.
     * @param SQLStatement $statement
     */
    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
    }

    /**
     * @param   mixed $value
     * @param   string $operator
     * @param   boolean $is_column
     */
    protected function addCondition($value, string $operator, bool $is_column)
    {
        if ($is_column && is_string($value)) {
            $expr = new Expression();
            $value = $expr->column($value);
        }

        $this->sql->addHavingCondition($this->aggregate, $value, $operator, $this->separator);
    }

    /**
     * @param   string|Closure|Expression $aggregate
     * @param   string $separator
     *
     * @return  $this
     */
    public function init($aggregate, string $separator): self
    {
        if ($aggregate instanceof Closure) {
            $aggregate = Expression::fromClosure($aggregate);
        }
        $this->aggregate = $aggregate;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function eq($value, bool $is_column = false)
    {
        $this->addCondition($value, '=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function ne($value, bool $is_column = false)
    {
        $this->addCondition($value, '!=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function lt($value, bool $is_column = false)
    {
        $this->addCondition($value, '<', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function gt($value, bool $is_column = false)
    {
        $this->addCondition($value, '>', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function lte($value, bool $is_column = false)
    {
        $this->addCondition($value, '<=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     */
    public function gte($value, bool $is_column = false)
    {
        $this->addCondition($value, '>=', $is_column);
    }

    /**
     * @param   array|Closure $value
     */
    public function in($value)
    {
        $this->sql->addHavingInCondition($this->aggregate, $value, $this->separator, false);
    }

    /**
     * @param   array|Closure $value
     */
    public function notIn($value)
    {
        $this->sql->addHavingInCondition($this->aggregate, $value, $this->separator, true);
    }

    /**
     * @param   string|float|int $value1
     * @param   string|float|int $value2
     */
    public function between($value1, $value2)
    {
        $this->sql->addHavingBetweenCondition($this->aggregate, $value1, $value2, $this->separator, false);
    }

    /**
     * @param   string|float|int $value1
     * @param   string|float|int $value2
     */
    public function notBetween($value1, $value2)
    {
        $this->sql->addHavingBetweenCondition($this->aggregate, $value1, $value2, $this->separator, true);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        if ($this->aggregate instanceof Expression) {
            $this->aggregate = clone $this->aggregate;
        }
        $this->sql = clone $this->sql;
    }
}
