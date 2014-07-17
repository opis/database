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

class AggregateExpression
{
    protected $compiler;
    
    protected $havingClause;
    
    protected $having;
    
    protected $column;
    
    protected $separator;
    
    public function __construct(Compiler $compiler, HavingClause $clause)
    {
        $this->compiler = $compiler;
        $this->havingClause = $clause;
        $this->having = new Having($this->compiler, $this->havingClause);
    }
    
    protected function expression()
    {
        return new Expression($this->compiler);
    }
    
    
    public function init($column, $separator)
    {
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }
    
    
    public function count($distinct = false)
    {
        $value = $this->expression()->count($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }
    
    public function avg($distinct = false)
    {
        $value = $this->expression()->avg($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }
    
    public function sum($distinct  = false)
    {
        $value = $this->expression()->sum($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }
    
    public function min($distinct = false)
    {
        $value = $this->expression()->min($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }
    
    public function max($distinct = false)
    {
        $value = $this->expression()->max($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }
    
}
