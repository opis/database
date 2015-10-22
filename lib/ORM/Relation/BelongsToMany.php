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

namespace Opis\Database\ORM\Relation;

use Opis\Database\Model;
use Opis\Database\ORM\Relation;

class BelongsToMany extends Relation
{
    
    protected $junctionTable;
    
    protected $junctionKey;
    
    public function __construct(Model $owner, Model $model, $foreignKey = null, $junctionTable = null, $junctionKey = null)
    {
        $this->junctionTable = $junctionTable;
        $this->junctionKey = $junctionKey;
        
        parent::__construct($owner, $model, $foreignKey);
    }
    
    protected function getJunctionTable()
    {
        if($this->junctionTable === null)
        {
            $table = array($this->owner->getTable(), $this->model->getTable());
            sort($table);
            $this->junctionTable = implode('_', $table);
        }
        
        return $this->junctionTable;
    }
    
    protected function getJunctionKey()
    {
        if($this->junctionKey === null)
        {
            $this->junctionKey = $this->model->getForeignKey();
        }
        
        return $this->junctionKey;
    }
    
    public function getResult()
    {
        $self = $this;
        $junctionTable = $this->getJunctionTable();
        $junctionKey = $this->getJunctionKey();
        $joinTable = $this->model->getTable();
        $joinColumn = $this->model->getPrimaryKey();
        $foreignKey = $this->getForeignKey();
        
        $this->query
             ->from($junctionTable)
             ->join($joinTable, function($join) use($junctionTable, $junctionKey, $joinTable, $joinColumn){
                $join->on($junctionTable . '.' . $junctionKey, $joinTable . '.' . $joinColumn);
             })
             ->where($foreignKey)->is($this->owner->{$this->owner->getPrimaryKey()})
             ->select($joinTable . '.*');
        
        return $this->query()
                    ->fetchClass(get_class($this->model), array(false))
                    ->all();
    }
}
