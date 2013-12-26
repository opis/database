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
    
    public function andWhere($column, $value = null, $operator = '=')
    {
        return $this->where($column, $value, $operator);
    }
    
    public function orWhere($column, $value = null, $operator = '=')
    {
        $this->where->orWhere($column, $value, $operator);
        return $this;
    }
    
    public function whereBetween($column, $value1, $value2)
    {
        $this->where->whereBetween($column, $value1, $value2);
        return $this;
    }
    
    public function andWhereBetween($column, $value1, $value2)
    {
        return $this->andWhereBetween($column, $value1, $value2);
    }
    
    public function orWhereBetween($column, $value1, $value2)
    {
        $this->where->orWhereBetween($column, $value1, $value2);
        return $this;
    }
    
    public function whereNotBetween($column, $value1, $value2)
    {
        $this->where->whereNotBetween($column, $value1, $value2);
        return $this;
    }
    
    public function andWhereNotBetween($column, $value1, $value2)
    {
        return $this->whereNotBetween($column, $value1, $value2);
    }
    
    public function orWhereNotBetween($column, $value1, $value2)
    {
        $this->where->orWhereNotBetween($column, $value1, $value2);
        return $this;
    }
    
    public function whereLike($column, $value)
    {
        $this->where->whereLike($column, $value);
        return $this;
    }
    
    public function andWhereLike($column, $value)
    {
        $this->where->andWhereLike($column, $value);
        return $this;
    }
    
    public function orWhereLike($column, $value)
    {
        $this->where->orWhereLike($column, $value);
        return $this;
    }
    
    public function whereNotLike($column, $value)
    {
        $this->where->whereNotLike($column, $value);
        return $this;
    }
    
    public function andWhereNotLike($column, $value)
    {
        $this->where->andWhereNotLike($column, $value);
        return $this;
    }
    
    public function orWhereNotLike($column, $value)
    {
        $this->where->orWhereNotLike($column, $value);
        return $this;
    }
    
    public function whereIn($column, $value)
    {
        $this->where->whereIn($column, $value);
        return $this;
    }
    
    public function andWhereIn($column, $value)
    {
        return $this->whereIn($column, $value);
    }
    
    public function orWhereIn($column, $value)
    {
        $this->where->orWhereIn($column, $value);
        return $this;
    }
    
    public function whereNotIn($column, $value)
    {
        $this->where->whereNotIn($column, $value);
        return $this;
    }
    
    public function andWhereNotIn($column, $value)
    {
        return $this->whereNotIn($column, $value);
    }
    
    public function orWhereNotIn($column, $value)
    {
        $this->where->orWhereNotIn($column, $value);
        return $this;
    }
    
    public function whereNull($column)
    {
        $this->where->whereNull($column);
        return $this;
    }
    
    public function andWhereNull($column)
    {
        return $this->whereNull($column);
    }
    
    public function orWhereNull($column)
    {
        $this->where->orWhereNull($column);
        return $this;
    }
    
    public function whereNotNull($column)
    {
        $this->where->whereNotNull($column);
        return $this;
    }
    
    public function andWhereNotNull($column)
    {
        return $this->whereNotNull($column);
    }
    
    public function orWhereNotNull($column)
    {
        $this->where->orWhereNotNull($column);
    }
    
    public function whereExists(Closure $select)
    {
        $this->where->whereExists($select);
        return $this;
    }
    
    public function andWhereExists(Closure $select)
    {
        return $this->whereExists($select);
    }
    
    public function orWhereExists(Closure $select)
    {
        $this->where->orWhereExists($select);
        return $this;
    }
    
    public function whereNotExists(Closure $select)
    {
        $this->where->whereNotExists($select);
        return $this;
    }
    
    public function andWhereNotExists(Closure $select)
    {   
        return $this->whereNotExists($select);
    }
    
    public function orWhereNotExists(Closure $select)
    {
        $this->where->orWhereNotExists($select);
        return $this;
    }
    
}