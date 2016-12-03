<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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
     * @param bool $value
     * @return self|mixed
     */
    public function distinct(bool $value = true): self
    {
        $this->getSQLStatement()->setDistinct($value);
        return $this;
    }

    /**
     * @param string|array $columns
     * @return self|mixed
     */
    public function groupBy($columns): self
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->getSQLStatement()->addGroupBy($columns);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     *
     * @return  self|mixed
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
     * @return  self|mixed
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
     * @return  self|mixed
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
     * @return  self|mixed
     */
    public function orderBy($columns, string $order = 'ASC', string $nulls = null): self
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->getSQLStatement()->addOrder($columns, $order, $nulls);
        return $this;
    }

    /**
     * @param   int $value
     *
     * @return  self|mixed
     */
    public function limit(int $value): self
    {
        $this->getSQLStatement()->setLimit($value);
        return $this;
    }

    /**
     * @param   int $value
     *
     * @return  self|mixed
     */
    public function offset(int $value): self
    {
        $this->getSQLStatement()->setOffset($value);
        return $this;
    }
}