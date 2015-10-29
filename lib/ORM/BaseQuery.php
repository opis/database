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

namespace Opis\Database\ORM;

use Closure;
use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\SelectStatement;

abstract class BaseQuery
{
    protected $query;
    protected $whereCondition;
    protected $isReadOnly = false;
    protected $prepared;
    protected $compiler;
    protected $immediate = false;
    protected $with = array();
    
    public function __construct(Compiler $compiler, SelectStatement $query, WhereCondition $whereCondition)
    {
        $this->compiler = $compiler;
        $this->query = $query;
        $this->whereCondition = $whereCondition;
    }
    
    public function with($value, $immediate = false)
    {
        if(!is_array($value))
        {
            $value = array($value);
        }
        
        $this->with = $value;
        $this->immediate = $immediate;
        return $this;
    }
    
    public function where($column)
    {
        return $this->whereCondition->setColumn($column, 'where');
    }
    
    public function andWhere($column)
    {
        return $this->where($column);
    }
    
    public function orWhere($column)
    {
        return $this->whereCondition->setColumn($column, 'orWhere');
    }
    
    public function whereExists(Closure $select)
    {
        $this->query->whereExists($select);
        return $this;
    }
    
    public function andWhereExists(Closure $select)
    {
        $this->query->andWhereExists($select);
        return $this;
    }
    
    public function orWhereExists(Closure $select)
    {
        $this->query->orWhereExists($select);
        return $this;
    }
    
    public function whereNotExists(Closure $select)
    {
        $this->query->whereNotExists($select);
        return $this;
    }
    
    public function andWhereNotExists(Closure $select)
    {   
        $this->query->andWhereNotExists($select);
        return $this;
    }
    
    public function orWhereNotExists(Closure $select)
    {
        $this->query->orWhereNotExists($select);
        return $this;
    }
    
    public function orderBy($columns, $order = 'ASC')
    {
        $this->query->orderBy($columns, $order);
        return $this;
    }
    
    public function limit($value)
    {
        $this->query->limit($value);
        return $this;
    }
    
    public function offset($value)
    {
        $this->query->offset($value);
        return $this;
    }
    
    public function join($table, Closure $closure)
    {
        $this->query->join($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }
    
    public function leftJoin($table, Closure $closure)
    {
        $this->query->leftJoin($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }
    
    public function rightJoin($table, Closure $closure)
    {
        $this->query->rightJoin($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }
    
    public function fullJoin($table, Closure $closure)
    {
        $this->query->fullJoin($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }
    
    public function distinct()
    {
        $this->query->distinct();
        return $this;
    }
    
    protected function prepareQuery()
    {
        if($this->prepared === null)
        {
            $this->prepared = array(
                'sql' => (string) $this->query,
                'params' => $this->compiler->getParams(),
            );
        }
        
        return $this->prepared;
    }
    
    protected function prepareResults(array &$results)
    {
        if(!empty($results) && !empty($this->with))
        {
            $attr = $this->getWithAttributes();
            $prepared = $this->prepareQuery();
            
            foreach($attr['with'] as $with => $callback)
            {
                if(!method_exists($this->model, $with))
                {
                    continue;
                }
                
                $loader = $this->model->{$with}()->getLazyLoader(array(
                    'query' => $this->query,
                    'params' => $prepared['params'],
                    'callback' => $callback,
                    'with' => $attr['extra'][$with],
                    'immediate' => $this->immediate,
                ));
                
                if($loader === null)
                {
                    continue;
                }
                
                foreach($results as $result)
                {
                    $result->setLazyLoader($with, $loader);
                }
            }
        }
    }
    
    protected function getWithAttributes()
    {
        $with = array();
        $extra = array();
        
        foreach($this->with as $key => $value)
        {
            $fullName = $value;
            $callback = null;
            
            if($value instanceof Closure)
            {
                $fullName = $key;
                $callback = $value;
            }
            
            $fullName = explode('.', $fullName);
            $name = array_shift($fullName);
            $fullName = implode('.', $fullName);
            
            if($fullName == '')
            {
                if(!isset($with[$name]))
                {
                    $with[$name] = $callback;
                    
                    if(!isset($extra[$name]))
                    {
                        $extra[$name] = array();
                    }
                }
            }
            else
            {
                if(!isset($extra[$name]))
                {
                    $extra[$name] = array();
                }
                
                $t = &$extra[$name];
                
                if(isset($t[$fullName]) || in_array($fullName, $t))
                {
                    continue;
                }
                
                if($callback === null)
                {
                    $t[] = $fullName;
                }
                else
                {
                    $t[$fullName] = $callback;
                }
            }
            
        }
        
        return array(
            'with' => $with,
            'extra' => $extra,
        );
    }
}
