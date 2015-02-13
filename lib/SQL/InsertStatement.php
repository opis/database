<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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
    
    protected $columns = array();
    
    protected $values = array();
    
    protected $autoincrement;
    
    protected $sql;
    
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
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
    
    public function insert(array $values)
    {
        foreach($values as $column => $value)
        {
            $this->columns[] = array(
                'name' => $column,
                'alias' => null,
            );
            
            if($value instanceof Closure)
            {
                $expression = new Expression($this->compiler);
                $value($expression);
                $this->values[] = $expression;
            }
            else
            {
                $this->values[] = $value;
            }
        }
        
        return $this;
    }
    
    public function into($table)
    {
        $this->tables = array((string) $table);
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
