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

class SelectStatement extends WhereJoinCondition
{
    
    protected $have = array();
    
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
    
    
    public function __construct(Compiler $compiler, $tables, Where $where = null)
    {
        parent::__construct($compiler, $where);
        
        if(!is_array($tables))
        {
            $tables = array($tables);
        }
        
        $this->tables = $tables;
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
    
    public function getIntoTable()
    {
        return $this->intoTable;
    }
    
    public function getIntoDatabase()
    {
        return $this->intoDatabase;
    }
    
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
    
    protected function expression()
    {
        return new ColumnExpression($this->compiler);
    }
    
    public function distinct($value = true)
    {
        $this->distinct = $value;
        return $this;
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
    
    public function having(Closure $aggregate, $value, $operator = '=')
    {
        return $this->addHavingClause($aggregate, $value, $operator, 'AND');
    }
    
    public function andHaving(Closure $aggregate, $value, $operator = '=')
    {
        return $this->having($aggregate, $value, $operator);
    }
    
    public function orHaving(Closure $aggregate, $value, $operator = '=')
    {
        return $this->addHavingClause($aggregate, $value, $operator, 'OR');
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
    }
    
    public function column($name)
    {
        $this->columns = $this->expression()->column($name)->getColumns();
    }
    
    public function count($column = '*',  $distinct = false)
    {
        $this->columns = $this->expression()->count($column, null, $distinct)->getColumns();
    }
    
    public function avg($column, $distinct = false)
    {
        $this->columns = $this->expression()->avg($column, null, $distinct)->getColumns();
    }
    
    public function sum($column, $distinct  = false)
    {
        $this->columns = $this->expression()->sum($column, null, $distinct)->getColumns();
    }
    
    public function min($column, $distinct = false)
    {
        $this->columns = $this->expression()->min($column, null, $distinct)->getColumns();
    }
    
    public function max($column, $distinct = false)
    {
        $this->columns = $this->expression()->max($column, null, $distinct)->getColumns();
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