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

class ColumnExpression
{
    protected $compiler;
    
    protected $columns = array();
    
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
    
    protected function expression()
    {
        return new Expression($this->compiler);
    }
    
    public function getColumns()
    {
        return $this->columns;
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
        return $this->column($this->expression()->count($column, $distinct), $alias);
    }
    
    public function avg($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->avg($column, $distinct), $alias);
    }
    
    public function sum($column, $alias = null, $distinct  = false)
    {
        return $this->column($this->expression()->sum($column, $distinct), $alias);
    }
    
    public function min($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->min($column, $distinct), $alias);
    }
    
    public function max($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->max($column, $distinct), $alias);
    }
    
    public function ucase($column, $alias = null)
    {
        return $this->column($this->expression()->ucase($column), $alias);
    }
    
    public function lcase($column, $alias = null)
    {
        return $this->column($this->expression()->lcase($column), $alias);
    }
    
    public function mid($column, $start = 1, $alias = null, $length = 0)
    {
        return $this->column($this->expression()->mid($column, $start, $length), $alias);
    }
    
    public function len($column, $alias = null)
    {
        return $this->column($this->expression()->len($column), $alias);
    }
    
    public function round($column, $decimals = 0, $alias = null)
    {
        return $this->column($this->expression()->format($column, $format), $alias);
    }
    
    public function format($column, $format, $alias = null)
    {
        return $this->column($this->expression()->format($column, $format), $alias);
    }
    
    public function now($alias = null)
    {
        return $this->column($this->expression()->now(), $alias);
    }
}