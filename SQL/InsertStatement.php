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

class InsertStatement
{
    protected $tables;
    
    protected $compiler;
    
    protected $columns;
    
    protected $values = array();
    
    protected $sql;
    
    public function __construct(Compiler $compiler, $table, $columns = array())
    {
        $this->compiler = $compiler;
        $this->tables = array((string) $table);
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        foreach($columns as $column)
        {
            $this->columns[] = array(
                'name' => $column,
                'alias' => null,
            );
        }
    }
    
    public function getTables()
    {
        return $this->tables;
    }
    
    public function getValues()
    {
        return $this->values;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    
    public function values($values)
    {
        if(!is_array($values))
        {
            $values = array($values);
        }
        
        $result = array();
        
        foreach($values as $value)
        {
            if($value instanceof Closure)
            {
                $expression = new Expression($this->compiler);
                $value($expression);
                $result[] = $expression;
            }
            else
            {
                $result[] = $value;
            }
        }
        $this->values[] = $result;
        return $this;
    }
    
    public function __toString()
    {
        if($this->sql === null)
        {
            $this->sql = $this->compiler->insert($this);
        }
        return $this->sql;
    }
}