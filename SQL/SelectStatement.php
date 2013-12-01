<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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

class SelectStatement extends WhereCondition
{
    
    protected $joins = array();
    
    protected $have = array();
    
    protected $group = array();
    
    protected $order = array();
    
    protected $columns = array();
    
    protected $limitValue = null;
    
    protected $offsetValue = null;
    
    protected $tables;
    
    protected $distinct = false;
    
    protected $sql;
    
    
    public function __construct(Compiler $compiler)
    {
        parent::__construct($compiler);
    }
    
    public function getJoinClauses()
    {
        return $this->joins;
    }
    
    public function getHavingClauses()
    {
        return $this->have;
    }
    
    public function getOrderClauses()
    {
        return $this->order;
    }
    
    public function getGroupClauses()
    {
        return $this->group;
    }
    
    public function getLimit()
    {
        return $this->limitValue;
    }
    
    public function getOffset()
    {
        return $this->offsetValue;
    }
    
    public function getTables()
    {
        return $this->tables;
    }
    
    public function isDistinct()
    {
        return $this->distinct;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    protected function addJoinClause($type, $table, $column1, $column2, $operator, $closure)
    {
        $join = new Join();
        
        $join->andOn($column1, $column2, $operator);
        
        if($closure != null)
        {
            $closure($join);
        }
        
        $this->joins[] = array(
            'type' => $type,
            'table' => $table,
            'join' => $join,
        );
        
        return $this;
    }
    
    protected function addHavingClause($column, $value, $operator, $separator)
    {
        $this->have[] = array(
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
            'separator' => $separator,
        );
        return $this;
    }
    
    public function from($tables)
    {
        if(!is_array($tables))
        {
            $tables = array($tables);
        }
        $this->tables = $tables;
        return $this;
    }
    
    public function distinct($value = true)
    {
        $this->distinct = $value;
        return $this;
    }
    
    public function join($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('INNER', $table, $column1, $column2, $operator, $closure);
    }
    
    public function leftJoin($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('LEFT', $table, $column1, $column2, $operator, $closure);
    }
    
    public function rightJoin($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('RIGHT', $table, $column1, $column2, $operator, $closure);
    }
    
    public function fullJoin($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('FULL', $table, $column1, $column2, $operator, $closure);
    }
    
    public function groupBy($columns)
    {
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        $this->group = $columns;
        return $this;
    }
    
    public function having($column, $value, $operator = '=')
    {
        return $this->addHavingClause($column, $value, $operator, 'AND');
    }
    
    public function orHaving($column, $value, $operator = '=')
    {
        return $this->addHavingClause($column, $value, $operator, 'OR');
    }
    
    public function orderBy($columns, $order = 'ASC')
    {
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        $this->order[] = array(
            'columns' => $columns,
            'order' => strtoupper($order),
        );
        return $this;
    }
    
    public function limit($value)
    {
        $this->limitValue = (int) $value;
        return $this;
    }
    
    public function offset($value)
    {
        $this->offsetValue = (int) $value;
        return $this;
    }
    
    public function column($name, $alias = null)
    {
        $this->columns[] = array(
            'name' => $name,
            'alias' => $alias,
        );
        return $this;
    }
    
    public function columns(array $columns)
    {
        foreach($columns as $name => $alias)
        {
            if(is_string($name))
            {
                $this->column($name, $alias);
            }
            else
            {
                $this->column($alias, null);
            }
        }
        return $this;
    }
    
    public function count($column = '*', $alias = null, $distinct = false)
    {
        return $this->column($this->compiler->expression()->count($column, $distinct), $alias);
    }
    
    public function avg($column, $alias = null, $distinct = false)
    {
        return $this->column($this->compiler->expression()->avg($column, $distinct), $alias);
    }
    
    public function sum($column, $alias = null, $distinct  = false)
    {
        return $this->column($this->compiler->expression()->sum($column, $distinct), $alias);
    }
    
    public function min($column, $alias = null, $distinct = false)
    {
        return $this->column($this->compiler->expression()->min($column, $distinct), $alias);
    }
    
    public function max($column, $alias = null, $distinct = false)
    {
        return $this->column($this->compiler->expression()->max($column, $distinct), $alias);
    }
    
    public function ucase($column, $alias = null)
    {
        return $this->column($this->compiler->expression()->ucase($column), $alias);
    }
    
    public function lcase($column, $alias = null)
    {
        return $this->column($this->compiler->expression()->lcase($column), $alias);
    }
    
    public function mid($column, $start = 1, $alias = null, $length = 0)
    {
        return $this->column($this->compiler->expression()->mid($column, $start, $length), $alias);
    }
    
    public function len($column, $alias = null)
    {
        return $this->column($this->compiler->expression()->len($column), $alias);
    }
    
    public function round($column, $decimals = 0, $alias = null)
    {
        return $this->column($this->compiler->expression()->round($column, $decimals), $alias);
    }
    
    public function format($column, $format, $alias = null)
    {
        return $this->column($this->compiler->expression()->format($column, $format), $alias);
    }
    
    public function now($alias = null)
    {
        return $this->column($this->compiler->expression()->now(), $alias);
    }
    
    public function __toString()
    {
        if($this->sql === null)
        {
            $this->sql = $this->compiler->select($this);
        }
        return $this->sql;
    }
}