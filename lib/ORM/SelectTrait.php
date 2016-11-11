<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
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

namespace Opis\Database\ORM;

use Closure;
use Opis\Database\SQL\ColumnExpression;
use Opis\Database\SQL\HavingStatement;
use Opis\Database\SQL\SQLStatement;

trait SelectTrait
{
    /**
     * @return SQLStatement
     */
    abstract protected function getSQLStatement(): SQLStatement;

    /**
     * @return HavingStatement
     */
    abstract protected function getHavingStatement(): HavingStatement;

    /**
     * @return mixed
     */
    abstract protected function executeStatement();


    /**
     * @param   string|array|Closure    $columns
     *
     */
    public function select($columns = array())
    {
        $expr = new ColumnExpression($this->getSQLStatement());

        if ($columns instanceof Closure) {
            $columns($expr);
        } else {
            if (!is_array($columns)) {
                $columns = array($columns);
            }
            $expr->columns($columns);
        }
    }

    /**
     * @param   string|array    $columns
     *
     * @return  self
     */
    public function groupBy($columns): self
    {
        $this->getSQLStatement()->addGroupBy($columns);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     *
     * @return  self
     */
    public function having($column, Closure $value = null): self
    {
        $this->getHavingStatement()->having($column, $value);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value
     *
     * @return  self
     */
    public function andHaving($column, Closure $value = null): self
    {
        $this->getHavingStatement()->andHaving($column, $value);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value
     *
     * @return  self
     */
    public function orHaving($column, Closure $value = null): self
    {
        $this->getHavingStatement()->orHaving($column, $value);
        return $this;
    }

    /**
     * @param   string|array    $columns
     * @param   string          $order      (optional)
     * @param   string          $nulls      (optional)
     *
     * @return  self
     */
    public function orderBy($columns, string $order = 'ASC', string $nulls = null): self
    {
        $this->getSQLStatement()->addOrder($columns, $order, $nulls);
        return $this;
    }

    /**
     * @param   int $value
     *
     * @return  self
     */
    public function limit(int $value): self
    {
        $this->getSQLStatement()->setLimit($value);
        return $this;
    }

    /**
     * @param   int $value
     *
     * @return  self
     */
    public function offset(int $value): self
    {
        $this->getSQLStatement()->setOffset($value);
        return $this;
    }

    /**
     * @param   string $name
     * @return mixed
     */
    public function column(string $name)
    {
        (new ColumnExpression($this->getSQLStatement()))->column($name);
        return $this->executeStatement();
    }

    /**
     * @param   string $column (optional)
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function count($column = '*', bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->count($column, null, $distinct);
        return $this->executeStatement();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function avg(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->avg($column, null, $distinct);
        return $this->executeStatement();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function sum(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->sum($column, null, $distinct);
        return $this->executeStatement();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function min(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->min($column, null, $distinct);
        return $this->executeStatement();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function max(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->max($column, null, $distinct);
        return $this->executeStatement();
    }

}