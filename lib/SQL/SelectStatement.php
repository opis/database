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

class SelectStatement extends WhereJoinCondition
{
    /** @var    HavingCondition */
    protected $have;

    /** @var    array */
    protected $group = array();

    /** @var    array */
    protected $order = array();

    /** @var    array */
    protected $columns = array();

    /** @var    int */
    protected $limitValue = null;

    /** @var    int */
    protected $offsetValue = null;

    /** @var    array */
    protected $tables;

    /** @var    bool */
    protected $distinct = false;

    /** @var    string */
    protected $intoTable = null;

    /** @var    string */
    protected $intoDatabase = null;

    /** @var    string */
    protected $sql;

    /**
     * Constructor
     * 
     * @param   Compiler        $compiler
     * @param   string|array    $tables
     * @param   WhereClause     $clause     (optional)
     */
    public function __construct(Compiler $compiler, $tables, WhereClause $clause = null)
    {
        parent::__construct($compiler, $clause);

        if (!is_array($tables)) {
            $tables = array($tables);
        }

        $this->tables = $tables;
        $this->have = new HavingCondition($this->compiler);
    }

    /**
     * @return array
     */
    public function getHavingConditions()
    {
        return $this->have->getHavingConditions();
    }

    /**
     * @return array
     */
    public function getOrderClauses()
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getGroupClauses()
    {
        return $this->group;
    }

    /**
     * @return  int|null
     */
    public function getLimit()
    {
        return $this->limitValue;
    }

    /**
     * @return  int|null
     */
    public function getOffset()
    {
        return $this->offsetValue;
    }

    /**
     * @return  array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return  bool
     */
    public function isDistinct()
    {
        return $this->distinct;
    }

    /**
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return  string|null
     */
    public function getIntoTable()
    {
        return $this->intoTable;
    }

    /**
     * @return  string|null
     */
    public function getIntoDatabase()
    {
        return $this->intoDatabase;
    }

    /**
     * @return  ColumnExpression
     */
    protected function expression()
    {
        return new ColumnExpression($this->compiler);
    }

    /**
     * @param   bool    $value  (optional)
     * 
     * @return  $this
     */
    public function distinct($value = true)
    {
        $this->distinct = $value;
        return $this;
    }

    /**
     * @param   string|array    $columns
     * 
     * @return  $this
     */
    public function groupBy($columns)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        $this->group = $columns;
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
     * @param   array|string    $columns
     * @param   string          $order      (optional)
     * @param   string          $nulls      (optional)
     * 
     * @return  $this
     */
    public function orderBy($columns, $order = 'ASC', $nulls = null)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }

        $order = strtoupper($order);

        if ($order !== 'ASC' && $order !== 'DESC') {
            $order = 'ASC';
        }

        if ($nulls !== null) {
            $nulls = strtoupper($nulls);

            if ($nulls !== 'NULLS FIRST' && $nulls !== 'NULLS LAST') {
                $nulls = null;
            }
        }

        $this->order[] = array(
            'columns' => $columns,
            'order' => $order,
            'nulls' => $nulls,
        );

        return $this;
    }

    /**
     * @param   int $value
     * 
     * @return  $this
     */
    public function limit($value)
    {
        $this->limitValue = (int) $value;
        return $this;
    }

    /**
     * @param   int $value
     * 
     * @return  $this
     */
    public function offset($value)
    {
        $this->offsetValue = (int) $value;
        return $this;
    }

    /**
     * @param   string|array\Closure    $columns
     * 
     * @return  $this
     */
    public function select($columns = array())
    {
        $expr = $this->expression();

        if ($columns instanceof Closure) {
            $columns($expr);
        } else {
            if (!is_array($columns)) {
                $columns = array($columns);
            }
            $expr->columns($columns);
        }
        $this->columns = $expr->getColumns();

        return $this;
    }

    /**
     * @param   string  $name
     */
    public function column($name)
    {
        $this->columns = $this->expression()->column($name)->getColumns();
    }

    /**
     * @param   string  $column     (optional)
     * @param   bool    $distinct   (optional)
     */
    public function count($column = '*', $distinct = false)
    {
        $this->columns = $this->expression()->count($column, null, $distinct)->getColumns();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function avg($column, $distinct = false)
    {
        $this->columns = $this->expression()->avg($column, null, $distinct)->getColumns();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function sum($column, $distinct = false)
    {
        $this->columns = $this->expression()->sum($column, null, $distinct)->getColumns();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function min($column, $distinct = false)
    {
        $this->columns = $this->expression()->min($column, null, $distinct)->getColumns();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     */
    public function max($column, $distinct = false)
    {
        $this->columns = $this->expression()->max($column, null, $distinct)->getColumns();
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        if ($this->sql === null) {
            $this->sql = $this->compiler->select($this);
        }
        return $this->sql;
    }
}
