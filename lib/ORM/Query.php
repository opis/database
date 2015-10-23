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
use Opis\Database\Model;

class Query extends BaseQuery
{
    protected $model;
    protected $connection;
    protected $compiler;
    protected $with;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->connection = $connection = $model->getConnection();
        $this->compiler = $compiler = $connection->compiler();
        
        $query = new Select($compiler, $model->getTable());
        $whereCondition = new WhereCondition($this, $query);
        
        parent::__construct($query, $whereCondition);
    }
    
    
    protected function query(array &$columns = array())
    {
        if(!empty($columns))
        {
            $columns[] = $this->model->getPrimaryKey();
        }
        
        return $this->connection->query((string) $this->query->select($columns),
                                        $this->compiler->getParams());
    }
    
    public function with($value)
    {
        if(!is_array($value))
        {
            $value = array($value);
        }
        
        $this->with = $value;
        return $this;
    }
    
    
    public function first(array $columns = array())
    {
        return $this->query()
                    ->fetchClass(get_class($this->model), array(false))
                    ->first();
    }
    
    public function all(array $columns = array())
    {
        $results = $this->query($columns)
                        ->fetchClass(get_class($this->model), array(false))
                        ->all();
        
        if(!empty($results) && !empty($this->with))
        {
            foreach($this->with as $with)
            {
                if(!method_exists($this->model, $with))
                {
                    continue;
                }
                
                $loader = $this->model->{$with}()->getLazyLoader($this->query);
                
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
        
        return $results;
    }
    
    public function find($id, array $columns = array())
    {
        $this->query->where($this->model->getPrimaryKey())->is($id);
        return $this->first($columns);
    }
    
    public function findAll(array $columns = array())
    {
        return $this->findMany(array(), $columns);
    }
    
    public function findMany(array $ids = null, array $columns = array())
    {
        if($ids !== null && !empty($ids))
        {
            $this->query->where($this->model->getPrimaryKey())->in($ids);
        }
        return $this->all($columns);
    }
    
}
