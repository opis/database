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

class Where
{
    /** @var    string|Expression */
    protected $column;

    /** @var    string */
    protected $separator;

    /** @var  SQLStatement */
    protected $sql;

    /** @var  WhereStatement */
    protected $statement;

    public function __construct(WhereStatement $statement, SQLStatement $sql)
    {
        $this->sql = $sql;
        $this->statement = $statement;
    }

    /**
     * @param   string|Expression|Closure $column
     * @param   string $separator
     * @return  Where
     */
    public function init($column, string $separator): self
    {
        if ($column instanceof Closure) {
            $column = Expression::fromClosure($column);
        }
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed $value
     * @param   string $operator
     * @param   bool $isColumn (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addCondition($value, string $operator, bool $isColumn = false): WhereStatement
    {
        if ($isColumn && is_string($value)) {
            $value = function (Expression $expr) use ($value) {
                $expr->column($value);
            };
        }
        $this->sql->addWhereCondition($this->column, $value, $operator, $this->separator);
        return $this->statement;
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addBetweenCondition($value1, $value2, bool $not): WhereStatement
    {
        $this->sql->addWhereBetweenCondition($this->column, $value1, $value2, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   string $pattern
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addLikeCondition(string $pattern, bool $not): WhereStatement
    {
        $this->sql->addWhereLikeCondition($this->column, $pattern, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   mixed $value
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addInCondition($value, bool $not): WhereStatement
    {
        $this->sql->addWhereInCondition($this->column, $value, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   bool $not
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    protected function addNullCondition(bool $not): WhereStatement
    {
        $this->sql->addWhereNullCondition($this->column, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function is($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function isNot($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '!=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function lessThan($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '<', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function greaterThan($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '>', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function atLeast($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '>=', $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function atMost($value, bool $is_column = false): WhereStatement
    {
        return $this->addCondition($value, '<=', $is_column);
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function between($value1, $value2): WhereStatement
    {
        return $this->addBetweenCondition($value1, $value2, false);
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notBetween($value1, $value2): WhereStatement
    {
        return $this->addBetweenCondition($value1, $value2, true);
    }

    /**
     * @param   string $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function like(string $value): WhereStatement
    {
        return $this->addLikeCondition($value, false);
    }

    /**
     * @param   string $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notLike(string $value): WhereStatement
    {
        return $this->addLikeCondition($value, true);
    }

    /**
     * @param   array|Closure $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function in($value): WhereStatement
    {
        return $this->addInCondition($value, false);
    }

    /**
     * @param   array|Closure $value
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notIn($value): WhereStatement
    {
        return $this->addInCondition($value, true);
    }

    /**
     * @return  WhereStatement|Select|Delete|Update
     */
    public function isNull(): WhereStatement
    {
        return $this->addNullCondition(false);
    }

    /**
     * @return  WhereStatement|Select|Delete|Update
     */
    public function notNull(): WhereStatement
    {
        return $this->addNullCondition(true);
    }
    //Aliases

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function eq($value, bool $is_column = false): WhereStatement
    {
        return $this->is($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function ne($value, bool $is_column = false): WhereStatement
    {
        return $this->isNot($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function lt($value, bool $is_column = false): WhereStatement
    {
        return $this->lessThan($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function gt($value, bool $is_column = false): WhereStatement
    {
        return $this->greaterThan($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function gte($value, bool $is_column = false): WhereStatement
    {
        return $this->atLeast($value, $is_column);
    }

    /**
     * @param   mixed $value
     * @param   bool $is_column (optional)
     *
     * @return  WhereStatement|Select|Delete|Update
     */
    public function lte($value, bool $is_column = false): WhereStatement
    {
        return $this->atMost($value, $is_column);
    }

    /**
     * @return  WhereStatement|Select|Delete|Update
     */
    public function nop(): WhereStatement {
        $this->sql->addWhereNop($this->column, $this->separator);
        return $this->statement;
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        if ($this->column instanceof Expression) {
            $this->column = clone $this->column;
        }
        $this->sql = clone $this->sql;
        $this->statement = new WhereStatement($this->sql);
    }
}
