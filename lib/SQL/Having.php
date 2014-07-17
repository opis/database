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

class Having
{
    
    protected $compiler;
    
    protected $havingClause;
    
    protected $aggregate;
    
    protected $separator;
    
    public function __construct(Compiler $compiler, HavingClause $clause)
    {
        $this->compiler = $compiler;
        $this->havingClause = $clause;
    }
    
    protected function addCondition($value, $operator, $iscolumn)
    {
        if($iscolumn && is_string($value))
        {
            $expr = new Expression($this->compiler);
            $value = $expr->column($value);
        }
        
        $this->havingClause->addCondition($this->aggregate, $value, $operator, $this->separator);
    }
    
    
    public function init($aggregate, $separator)
    {
        $this->aggregate = $aggregate;
        $this->separator = $separator;
        return $this;
    }
    
    
    public function eq($value, $iscolumn = false)
    {
        $this->addCondition($value, '=', $iscolumn);
    }
    
    public function ne($value, $iscolumn = false)
    {
        $this->addCondition($value, '!=', $iscolumn);
    }
    
    public function lt($value, $iscolumn = false)
    {
        $this->addCondition($value, '<', $iscolumn);
    }
    
    public function gt($value, $iscolumn = false)
    {
        $this->addCondition($value, '>', $iscolumn);
    }
    
    public function lte($value, $iscolumn = false)
    {
        $this->addCondition($value, '<=', $iscolumn);
    }
    
    public function gte($value, $iscolumn = false)
    {
        $this->addCondition($value, '>=', $iscolumn);
    }
    
    public function in($value)
    {
        $this->havingClause->addInCondition($this->aggregate, $value, $this->separator, false);
    }
    
    public function notIn($value)
    {
        $this->havingClause->addInCondition($this->aggregate, $value, $this->separator, true);
    }
    
    public function between($value1, $value2)
    {
        $this->havingClause->addBetweenCondition($this->aggregate, $value1, $value2, $this->separator, false);
    }
    
    public function notBetween($value1, $value2)
    {
         $this->havingClause->addBetweenCondition($this->aggregate, $value1, $value2, $this->separator, true);
    }
    
}
