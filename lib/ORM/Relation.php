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

abstract class Relation extends BaseQuery
{
    
    protected $model;
    protected $foreignKey;
    protected $connection;
    protected $compiler;
    protected $owner;
    protected $ownerPK;
    
    public function __construct(Model $owner, Model $model, $foreignKey = null)
    {        
        $this->connection = $connection = $owner->getConnection();
        $this->compiler = $compiler = $connection->compiler();
        $this->model = $model;
        $this->foreignKey = $foreignKey;
        $this->owner = $owner;
        
        $query = new Select($compiler, $model->getTable());
        $whereCondition = new WhereCondition($this, $query);
        
        parent::__construct($query, $whereCondition);
    }
    
    public function getForeignKey()
    {
        if($this->foreignKey === null)
        {
            $this->foreignKey = $this->owner->getForeignKey();
        }
        
        return $this->foreignKey;
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
    
    public function first(array $columns = array())
    {
        return $this->query($columns)
                    ->fetchClass(get_class($this->model), array(false))
                    ->first();
    }
    
    public function all(array $columns = array())
    {
        return $this->query($columns)
                    ->fetchClass(get_class($this->model), array(false))
                    ->all();
    }
    
    public abstract function getModel();
}
