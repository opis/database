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

class WhereCondition implements WhereInterface
{
    
    protected $where;
    
    protected $compiler;
    
    public function __construct(Compiler $compiler, Where $where = null)
    {
        $this->compiler = $compiler;
        if($where === null)
        {
            $where = new Where($compiler);
        }
        $this->where = $where;
    }
    
    public function getWhereClauses()
    {
        return $this->where->getWhereClauses();
    }
    
    public function where($column, $value = null, $operator = '=')
    {
        $this->where->where($column, $value, $operator);
        return $this;
    }
    
    public function orWhere($column, $value = null, $operator = '=')
    {
        $this->where->orWhere($column, $value, $operator);
        return $this;
    }
    
    public function between($column, $value1, $value2)
    {
        $this->where->between($column, $value1, $value2);
        return $this;
    }
    
    public function orBetween($column, $value1, $value2)
    {
        $this->where->orBetween($column, $value1, $value2);
        return $this;
    }
    
    public function notBetween($column, $value1, $value2)
    {
        $this->where->notBetween($column, $value1, $value2);
        return $this;
    }
    
    public function orNotBetween($column, $value1, $value2)
    {
        $this->where->orNotBetween($column, $value1, $value2);
        return $this;
    }
    
    public function in($column, $value)
    {
        $this->where->in($column, $value);
        return $this;
    }
    
    public function orIn($column, $value)
    {
        $this->where->orIn($column, $value);
        return $this;
    }
    
    public function notIn($column, $value)
    {
        $this->where->notIn($column, $value);
        return $this;
    }
    
    public function orNotIn($column, $value)
    {
        $this->where->orNotIn($column, $value);
        return $this;
    }
    
    public function isNull($column)
    {
        $this->where->isNull($column);
        return $this;
    }
    
    public function orNull($column)
    {
        $this->where->orNull($column);
        return $this;
    }
    
    public function notNull($column)
    {
        $this->where->notNull($column);
        return $this;
    }
    
    public function orNotNull($column)
    {
        $this->where->orNotNull($column);
    }
    
    public function exists(Closure $select)
    {
        $this->where->exists($select);
        return $this;
    }
    
    public function orExists(Closure $select)
    {
        $this->where->orExists($select);
        return $this;
    }
    
    public function notExists(Closure $select)
    {
        $this->where->notExists($select);
        return $this;
    }
    
    public function orNotExists(Closure $select)
    {
        $this->where->orNotExists($select);
        return $this;
    }
    
}