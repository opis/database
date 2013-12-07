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
    
    protected $value;
    
    protected $alias;
    
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
    
    protected function expression()
    {
        return new Expression($this->compiler);
    }
    
    public function getExpression()
    {
        return $this->value;
    }
    
    public function count($column = '*', $distinct = false)
    {
        $this->value = $this->expression()->count($column, $distinct);
    }
    
    public function avg($column, $distinct = false)
    {
        $this->value = $this->expression()->avg($column, $distinct);
    }
    
    public function sum($column, $distinct  = false)
    {
        $this->value = $this->expression()->sum($column, $distinct);
    }
    
    public function min($column, $distinct = false)
    {
        $this->value = $this->expression()->min($column, $distinct);
    }
    
    public function max($column, $distinct = false)
    {
        $this->value = $this->expression()->max($column, $distinct);
    }
    
}