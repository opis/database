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

use Opis\Database\Model;
use Opis\Database\Connection;

class LazyLoader
{
    protected $connection;
    protected $model;
    protected $fk;
    protected $pk;
    protected $results;
    protected $hasMany;
    protected $query;
    protected $readonly;
    protected $with;
    protected $modelClass;
    protected $params;
    protected $immediate;
    
    public function __construct(Connection $connection, Select $query, array $params, array $with, $immediate, $readonly, $hasMany, $model, $fk, $pk)
    {
        $this->connection = $connection;
        $this->modelClass = $model;
        $this->with = $with;
        $this->hasMany = $hasMany;
        $this->fk = $fk;
        $this->pk = $pk;
        $this->readonly = $readonly;
        $this->query = $query;
        $this->params = $params;
        $this->immediate = $immediate;
        
        if($immediate)
        {
            $this->getResults();
        }
    }
    
    protected function &getResults()
    {
        if($this->results === null)
        {
            $results = $this->connection
                            ->query((string) $this->query->obpk($this->fk), $this->params)
                            ->fetchClass($this->modelClass, array($this->readonly))
                            ->all();
                            
            $this->prepareResults($results);
            $this->results = &$results;
        }
        
        return $this->results;
    }
    
    protected function prepareResults(array &$results)
    {
        if(!empty($results) && !empty($this->with))
        {
            $model = $this->modelClass;
            $this->model = new $model;
            $attr = $this->getWithAttributes();
            
            foreach($attr['with'] as $with)
            {
                if(!method_exists($this->model, $with))
                {
                    continue;
                }
                
                $loader = $this->model->{$with}()->getLazyLoader($this->query, $this->params,
                                                                 $attr['extra'][$with], $this->immediate);
                
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
        
        foreach($this->with as $value)
        {
            $value = explode('.', $value);
            $name = array_shift($value);
            
            if(!isset($extra[$name]))
            {
                $extra[$name] = array();
                $with[] = $name;
            }
            
            if(!empty($value))
            {
                $extra[$name][] = implode('.', $value);
            }
            
        }
        
        foreach($extra as &$value)
        {
            $value = array_unique($value);
        }
        
        return array(
            'with' => $with,
            'extra' => $extra,
        );
    }
    
    protected function getFirst(Model $model, $with)
    {
        $results = &$this->getResults();
        
        foreach($results as $result)
        {
            if($result->{$this->fk} == $model->{$this->pk})
            {
                return $result;
            }
        }
        
        return $model->{$with}()->getResult();
    }
    
    protected function getAll(Model $model, $with)
    {
        $results = &$this->getResults();
        
        $all = array();
        
        foreach($results as $result)
        {
            if($result->{$this->fk} == $model->{$this->pk})
            {
                $all[] = $result;
            }
        }
        
        return $all;
    }
    
    public function getResult(Model $model, $with)
    {
        if($this->hasMany)
        {
            return $this->getAll($model, $with);
        }
        
        return $this->getFirst($model, $with);
    }
    
}
