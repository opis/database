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

class WhereCondition
{
    
    protected $whereClause;
    
    protected $where;
    
    protected $compiler;
    
    public function __construct(Compiler $compiler, WhereClause $clause = null)
    {
        $this->compiler = $compiler;
        
        if($clause === null)
        {
            $clause = new WhereClause($compiler);
        }
        
        $this->whereClause = $clause;
        $this->where = new Where($this);
    }
    
    public function getWhereClause()
    {
        return $this->whereClause;
    }
    
    public function getWhereConditions()
    {
        return $this->whereClause->getWhereConditions();
    }
    
    protected function addWhereCondition($column, $separator)
    {
        if($column instanceof Closure)
        {
            $this->whereClause->addConditionGroup($column, $separator);
            return $this;
        }
        
        return $this->where->init($column, $separator);
    }
    
    protected function addExistsCondition(Closure $select, $seperator, $not)
    {
        $this->whereClause->addExistsCondition($select, $seperator, $not);
        return $this;
    }
    
    public function where($column)
    {
        return $this->addWhereCondition($column, 'AND');
    }
    
    public function andWhere($column)
    {
        return $this->where($column);
    }
    
    public function orWhere($column)
    {
        return $this->addWhereCondition($column, 'OR');
    }
    
    public function whereExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'AND', false);
    }
    
    public function andWhereExists(Closure $select)
    {
        return $this->whereExists($select);
    }
    
    public function orWhereExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'OR', false);
    }
    
    public function whereNotExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'AND', true);
    }
    
    public function andWhereNotExists(Closure $select)
    {   
        return $this->whereNotExists($select);
    }
    
    public function orWhereNotExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'OR', true);
    }
    
}
