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

class ColumnExpression
{
    /** @var  SQLStatement */
    protected $sql;

    /**
     * ColumnExpression constructor.
     * @param SQLStatement $statement
     */
    public function __construct(SQLStatement $statement)
    {
        $this->sql = $statement;
    }

    /**
     * Add a column
     *
     * @param   string|Closure|Expression $name Column's name
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function column($name, string $alias = null): self
    {
        $this->sql->addColumn($name, $alias);
        return $this;
    }

    /**
     * Add multiple columns at once
     *
     * @param   array $columns Columns
     *
     * @return  $this
     */
    public function columns(array $columns): self
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

    /**
     * Add a `COUNT` expression
     *
     * @param   string|array|Expression $column Column
     * @param   string $alias (optional) Column's alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function count($column = '*', string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->count($column, $distinct), $alias);
    }

    /**
     * Add an `AVG` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function avg($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->avg($column, $distinct), $alias);
    }

    /**
     * Add a `SUM` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function sum($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->sum($column, $distinct), $alias);
    }

    /**
     * Add a `MIN` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function min($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->min($column, $distinct), $alias);
    }

    /**
     * Add a `MAX` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     * @param   bool $distinct (optional) Distinct column
     *
     * @return  $this
     */
    public function max($column, string $alias = null, bool $distinct = false): self
    {
        return $this->column((new Expression())->max($column, $distinct), $alias);
    }

    /**
     * Add a `UCASE` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function ucase($column, string $alias = null): self
    {
        return $this->column((new Expression())->ucase($column), $alias);
    }

    /**
     * Add a `LCASE` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function lcase($column, string $alias = null): self
    {
        return $this->column((new Expression())->lcase($column), $alias);
    }

    /**
     * Add a `MID` expression
     *
     * @param   string|Expression $column Column
     * @param   int $start (optional) Substring start
     * @param   string $alias (optional) Alias
     * @param   int $length (optional) Substring length
     *
     * @return  $this
     */
    public function mid($column, int $start = 1, string $alias = null, int $length = 0): self
    {
        return $this->column((new Expression())->mid($column, $start, $length), $alias);
    }

    /**
     * Add a `LEN` expression
     *
     * @param   string|Expression $column Column
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function len($column, string $alias = null): self
    {
        return $this->column((new Expression())->len($column), $alias);
    }

    /**
     * Add a `FORMAT` expression
     *
     * @param   string|Expression $column Column
     * @param   int $decimals (optional) Decimals
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function round($column, int $decimals = 0, string $alias = null): self
    {
        return $this->column((new Expression())->format($column, $decimals), $alias);
    }

    /**
     * Add a `FORMAT` expression
     *
     * @param   string|Expression $column Column
     * @param   int $format Decimals
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function format($column, int $format, string $alias = null): self
    {
        return $this->column((new Expression())->format($column, $format), $alias);
    }

    /**
     * Add a `NOW` expression
     *
     * @param   string $alias (optional) Alias
     *
     * @return  $this
     */
    public function now($alias = null): self
    {
        return $this->column((new Expression())->now(), $alias);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
    }
}
