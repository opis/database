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
    
    protected $have;
    
    protected $group = array();
    
    protected $order = array();
    
    protected $columns = array();
    
    protected $limitValue = null;
    
    protected $offsetValue = null;
    
    protected $tables;
    
    protected $distinct = false;
    
    protected $intoTable = null;
    
    protected $intoDatabase = null;
    
    protected $sql;


    /**
     * SelectStatement constructor.
     * @param Compiler $compiler
     * @param WhereClause $tables
     * @param WhereClause|null $clause
     */
    public function __construct(Compiler $compiler, $tables, WhereClause $clause = null)
    {
        parent::__construct($compiler, $clause);
        
        if(!is_array($tables))
        {
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
     * @return null
     */
    public function getLimit()
    {
        return $this->limitValue;
    }

    /**
     * @return null
     */
    public function getOffset()
    {
        return $this->offsetValue;
    }

    /**
     * @return array|WhereClause
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return bool
     */
    public function isDistinct()
    {
        return $this->distinct;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return null
     */
    public function getIntoTable()
    {
        return $this->intoTable;
    }

    /**
     * @return null
     */
    public function getIntoDatabase()
    {
        return $this->intoDatabase;
    }

    /**
     * @param $aggregate
     * @param $value
     * @param $operator
     * @param $separator
     * @return $this
     */
    protected function addHavingClause($aggregate, $value, $operator, $separator)
    {
        $column = new AggregateExpression($this->compiler);
        $aggregate($column);
        $this->have[] = array(
            'column' => $column->getExpression(),
            'value' => $value,
            'operator' => $operator,
            'separator' => $separator,
        );
        return $this;
    }

    /**
     * @return ColumnExpression
     */
    protected function expression()
    {
        return new ColumnExpression($this->compiler);
    }

    /**
     * @param bool|true $value
     * @return $this
     */
    public function distinct($value = true)
    {
        $this->distinct = $value;
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function groupBy($columns)
    {
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        $this->group = $columns;
        return $this;
    }

    /**
     * @param $column
     * @param Closure|null $value
     * @return $this
     */
    public function having($column, Closure $value = null)
    {
        $this->have->having($column, $value);
        return $this;
    }

    /**
     * @param $column
     * @param Closure|null $value
     * @return $this
     */
    public function andHaving($column, Closure $value = null)
    {
        $this->have->andHaving($column, $value);
        return $this;
    }

    /**
     * @param $column
     * @param Closure|null $value
     * @return $this
     */
    public function orHaving($column, Closure $value = null)
    {
        $this->have->orHaving($column, $value);
        return $this;
    }

    /**
     * @param $columns
     * @param string $order
     * @param null $nulls
     * @return $this
     */
    public function orderBy($columns, $order = 'ASC', $nulls = null)
    {
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        $order = strtoupper($order);
        
        if($order !== 'ASC' && $order !== 'DESC')
        {
            $order = 'ASC';
        }
        
        if($nulls !== null)
        {
            $nulls = strtoupper($nulls);
            
            if($nulls !== 'NULLS FIRST' && $nulls !== 'NULLS LAST')
            {
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
     * @param $value
     * @return $this
     */
    public function limit($value)
    {
        $this->limitValue = (int) $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function offset($value)
    {
        $this->offsetValue = (int) $value;
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function select($columns = array())
    {
        $expr = $this->expression();
        
        if($columns instanceof Closure)
        {
            $columns($expr);
        }
        else
        {
            if(!is_array($columns))
            {
                $columns = array($columns);
            }
            $expr->columns($columns);
        }
        $this->columns = $expr->getColumns();
        
        return $this;
    }

    /**
     * @param $name
     */
    public function column($name)
    {
        $this->columns = $this->expression()->column($name)->getColumns();
    }

    /**
     * @param string $column
     * @param bool|false $distinct
     */
    public function count($column = '*',  $distinct = false)
    {
        $this->columns = $this->expression()->count($column, null, $distinct)->getColumns();
    }

    /**
     * @param $column
     * @param bool|false $distinct
     */
    public function avg($column, $distinct = false)
    {
        $this->columns = $this->expression()->avg($column, null, $distinct)->getColumns();
    }

    /**
     * @param $column
     * @param bool|false $distinct
     */
    public function sum($column, $distinct  = false)
    {
        $this->columns = $this->expression()->sum($column, null, $distinct)->getColumns();
    }

    /**
     * @param $column
     * @param bool|false $distinct
     */
    public function min($column, $distinct = false)
    {
        $this->columns = $this->expression()->min($column, null, $distinct)->getColumns();
    }

    /**
     * @param $column
     * @param bool|false $distinct
     */
    public function max($column, $distinct = false)
    {
        $this->columns = $this->expression()->max($column, null, $distinct)->getColumns();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if($this->sql === null)
        {
            $this->sql = $this->compiler->select($this);
        }
        return $this->sql;
    }
}
