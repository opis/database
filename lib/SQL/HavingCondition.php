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

class HavingCondition
{
    
    protected $havingClause;
    
    protected $conditions = array();
    
    protected $compiler;
    
    protected $aggregate;
    
    public function __construct(Compiler $compiler, HavingClause $clause = null)
    {
        $this->compiler = $compiler;
        
        if($clause === null)
        {
            $clause = new HavingClause($compiler);
        }
        
        $this->havingClause = $clause;
        
        $this->aggregate = new AggregateExpression($this->compiler, $this->havingClause);
    }
    
    protected function addCondition($column, $value, $separator)
    {
        if($column instanceof Closure)
        {
            $this->havingClause->addGroupCondition($column, $separator);
        }
        else
        {
            $value($this->aggregate->init($column, $separator));
        }
        
        return $this;
    }
    
    public function getHavingConditions()
    {
        return $this->havingClause->getHavingConditions();
    }
    
    public function having($column, Closure $value = null)
    {
        return $this->addCondition($column, $value, 'AND');
    }
    
    public function andHaving($column, Closure $value)
    {
        return $this->having($column, $value);
    }
    
    public function orHaving($column, Closure $value = null)
    {
        return $this->addCondition($column, $value, 'OR');
    }
    
}
