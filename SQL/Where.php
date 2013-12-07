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

class Where implements WhereInterface
{
    
    protected $clauses = array();
    
    protected $compiler;
    
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
    
    protected function addWhereClause($column, $value, $operator, $separator)
    {
        if($column instanceof Closure)
        {
            $where = new Where($this->compiler);
            $column($where);
            $this->clauses[] = array(
                'type' => 'whereNested',
                'clause' => $where,
                'separator' => $separator
            );
        }
        elseif($value instanceof Closure)
        {
            $expr = new Expression($this->compiler);
            $value($expr);
            $this->clauses[] = array(
                'type' => 'whereColumn',
                'column' => $column,
                'value' => $expr,
                'operator' => $operator,
                'separator' => $separator,
            );
        }
        else
        {
            $this->clauses[] = array(
                'type' => 'whereColumn',
                'column' => $column,
                'value' => $value,
                'operator' => $operator,
                'separator' => $separator,
            );
        }
        return $this;
    }
    
    protected function addBetweenClause($column, $value1, $value2, $separator, $not)
    {
        $this->clauses[] = array(
            'type' => 'whereBetween',
            'column' => $column,
            'value1' => $value1,
            'value2' => $value2,
            'separator' => $separator,
            'not' => $not,
        );
        return $this;   
    }
    
    protected function addInClause($column, $value, $separator, $not)
    {
        if($value instanceof Closure)
        {
            $select = new Subquery($this->compiler);
            $value($select);
            $this->clauses[] = array(
                'type' => 'whereInSelect',
                'column' => $column,
                'subquery' => $select,
                'separator' => $select,
                'not' => $not,
            );
        }
        else
        {
            $this->clauses[] = array(
                'type' => 'whereIn',
                'column' => $column,
                'value' => $value,
                'separator' => $separator,
                'not' => $not,
            );
        }
        return $this;
    }
    
    protected function addNullClause($column, $separator, $not)
    {
        $this->clauses[] = array(
            'type' => 'whereNull',
            'column' => $column,
            'separator' => $separator,
            'not' => $not,
        );
        return $this;
    }
    
    protected function addExistsClause($closure, $separator, $not)
    {
        $select = new Subquery($this->compiler);
        $closure($select);
        
        $this->clauses[] = array(
            'type' => 'whereExists',
            'subquery' => $select,
            'separator' => $separator,
            'not' => $not,
        );
        
        return $this;
    }
    
    
    public function getWhereClauses()
    {
        return $this->clauses;
    }
    
    
    public function where($column, $value = null, $operator = '=')
    {
        return $this->addWhereClause($column, $value, $operator, 'AND');
    }
    
    public function andWhere($column, $value = null, $operator = '=')
    {
        return $this->where($column, $value, $operator);
    }
    
    public function orWhere($column, $value = null, $operator = '=')
    {
        return $this->addWhereClause($column, $value, $operator, 'OR');
    }
    
    public function whereBetween($column, $value1, $value2)
    {
        return $this->addBetweenClause($column, $value1, $value2, 'AND', false);
    }
    
    public function andWhereBetween($column, $value1, $value2)
    {
        return $this->whereBetween($column, $value1, $value2);
    }
    
    public function orWhereBetween($column, $value1, $value2)
    {
        return $this->addBetweenClause($column, $value1, $value2, 'OR', false);
    }
    
    public function whereNotBetween($column, $value1, $value2)
    {
        return $this->addBetweenClause($column, $value1, $value2, 'AND', true);
    }
    
    public function andWhereNotBetween($column, $value1, $value2)
    {
        return $this->whereNotBetween($column, $value1, $value2);
    }
    
    public function orWhereNotBetween($column, $value1, $value2)
    {
        return $this->addBetweenClause($column, $value1, $value2, 'OR', true);
    }
    
    public function whereIn($column, $value)
    {
        return $this->addInClause($column, $value, 'AND', false);
    }
    
    public function andWhereIn($column, $value)
    {
        return $this->whereIn($column, $value);
    }
    
    public function orWhereIn($column, $value)
    {
        return $this->addInClause($column, $value, 'OR', false);
    }
    
    public function whereNotIn($column, $value)
    {
        return $this->addInClause($column, $value, 'AND', true);
    }
    
    public function andWhereNotIn($column, $value)
    {
        return $this->whereNotIn($column, $value);
    }
    
    public function orWhereNotIn($column, $value)
    {
        return $this->addInClause($column, $value, 'OR', true);
    }
    
    public function whereNull($column)
    {
        return $this->addNullClause($column, 'AND', false);
    }
    
    public function andWhereNull($column)
    {
        return $this->whereNull($column);
    }
    
    public function orWhereNull($column)
    {
        return $this->addNullClause($column, 'OR', false);
    }
    
    public function whereNotNull($column)
    {
        return $this->addNullClause($column, 'AND', true);
    }
    
    public function andWhereNotNull($column)
    {
        return $this->whereNotNull($column);
    }
    
    public function orWhereNotNull($column)
    {
        return $this->addNullClause($column, 'OR', true);
    }
    
    public function whereExists(Closure $select)
    {
        return $this->addExistsClause($select, 'AND', false);
    }
    
    public function andWhereExists(Closure $select)
    {
        return $this->whereExists($select);
    }
    
    public function orWhereExists(Closure $select)
    {
        return $this->addExistsClause($select, 'OR', false);
    }
    
    public function whereNotExists(Closure $select)
    {   
        return $this->addExistsClause($select, 'AND', true);
    }
    
    public function andWhereNotExists(Closure $select)
    {   
        return $this->whereNotExists($select);
    }
    
    public function orWhereNotExists(Closure $select)
    {
        return $this->addExistsClause($select, 'OR', true);
    }
}