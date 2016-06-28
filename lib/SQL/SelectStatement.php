<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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
    /** @var    HavingStatement */
    protected $have;

    public function __construct($tables, SQLStatement $statement = null)
    {
        parent::__construct($statement);

        if (!is_array($tables)) {
            $tables = array($tables);
        }

        $this->sql->addTables($tables);
        $this->have = new HavingStatement($this->sql);
    }

    /**
     * @param string $table
     * @param string|null $database
     * @return SelectStatement
     */
    public function into(string $table, string $database = null): self
    {
        $this->sql->setInto($table, $database);
        return $this;
    }


    /**
     * @param bool $value
     * @return SelectStatement
     */
    public function distinct(bool $value = true): self
    {
        $this->sql->setDistinct($value);
        return $this;
    }

    /**
     * @param   string|array    $columns
     * 
     * @return  $this
     */
    public function groupBy($columns): self
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->sql->addGroupBy($columns);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     * 
     * @return  $this
     */
    public function having($column, Closure $value = null)
    {
        $this->have->having($column, $value);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     * 
     * @return  $this
     */
    public function andHaving($column, Closure $value = null)
    {
        $this->have->andHaving($column, $value);
        return $this;
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     * 
     * @return  $this
     */
    public function orHaving($column, Closure $value = null)
    {
        $this->have->orHaving($column, $value);
        return $this;
    }

    /**
     * @param $columns
     * @param string $order
     * @param string|null $nulls
     * @return SelectStatement
     */
    public function orderBy($columns, string $order = 'ASC', string $nulls = null): self
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->sql->addOrder($columns, $order, $nulls);
        return $this;
    }

    /**
     * @param int $value
     * @return SelectStatement
     */
    public function limit(int $value): self
    {
        $this->sql->setLimit($value);
        return $this;
    }

    /**
     * @param int $value
     * @return SelectStatement
     */
    public function offset(int $value): self
    {
        $this->sql->setOffset($value);
        return $this;
    }

    /**
     * @param   string|array|Closure    $columns
     *
     */
    public function select($columns = array())
    {
        $expr = new ColumnExpression($this->sql);

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
     * @param   string  $name
     */
    public function column(string $name)
    {
        (new ColumnExpression($this->sql))->column($name);
    }

    /**
     * @param   string  $column     (optional)
     * @param   bool    $distinct   (optional)
     */
    public function count($column = '*', bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->count($column, null, $distinct);
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function avg(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->avg($column, null, $distinct);
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function sum(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->sum($column, null, $distinct);
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function min(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->min($column, null, $distinct);
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function max(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->sql))->max($column, null, $distinct);
    }
    
}
