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

namespace Opis\Database;

use RuntimeException;
use Opis\Database\ORM\Query;
use Opis\Database\ORM\Relation\HasOne;
use Opis\Database\ORM\Relation\HasMany;
use Opis\Database\ORM\Relation\BelongsTo;
use Opis\Database\ORM\Relation\BelongsToMany;

abstract class Model
{
    protected $table;
    
    protected $className;
    
    protected $columns;
    
    protected $primaryKey = 'id';
     
    protected $isNew = false;
    
    protected $loaded = false;
    
    protected $readonly = false;
    
    protected $mapColumns = array();
    
    protected $mapGetSet = array();
    
    protected $result = array();
    
    protected $modified = array();
    
    protected $loader = array();
    
    protected $deleted = false;
    
    protected $database;
    
    protected $sequence;
    
    protected $fillable;
    
    protected $guarded;
    
    public final function __construct($new = true)
    {
        $this->isNew = $new;
        $this->loaded = true;
        $this->mapGetSet = array_flip($this->mapColumns);
    }
    
    public static abstract function getConnection();
    
    public static function create(array $columns)
    {
        $item = new static();
        $item->assign($columns);
        $item->save();
        return $item;
    }
    
    public function __set($name, $value)
    {   
        if(!$this->loaded)
        {
            $this->columns[$name] = $value;
            return;
        }
        
        if($this->readonly)
        {
            throw new RuntimeException('Readonly');
        }
        
        if(isset($this->mapGetSet[$name]))
        {
            $name = $this->mapGetSet[$name];
        }
        
        $mutator = $name . 'Mutator';
        
        if(method_exists($this, $mutator))
        {
            $value = $this->{$mutator}($value);
        }
        
        if(method_exists($this, $name))
        {
            $name = $this->{$name}()->getRelatedColumn($this, $name);
        }
        
        $this->modified[$name] = true;
        $this->columns[$name] = $value;
    }
    
    public function __get($name)
    {
        $getter = $name;
        
        if(isset($this->mapGetSet[$name]))
        {
            $name = $this->mapGetSet[$name];
        }
        
        if(isset($this->columns[$name]))
        {
            $accesor = $getter . 'Accessor';
            
            if(method_exists($this, $accesor))
            {
                return $this->{$accesor}($this->columns[$name]);                
            }
            
            return $this->columns[$name];
        }
        
        if(isset($this->result[$name]))
        {
            return $this->result[$name];
        }
        
        if(isset($this->loader[$name]))
        {
            return $this->result[$name] = $this->loader[$name]->getResult($this, $name);
        }
        
        if(method_exists($this, $name))
        {
            return $this->result[$name] = $this->{$name}()->getResult();
        }
        
        throw new RuntimeException('Not found');
    }
    
    public function save()
    {
        if($this->deleted)
        {
            throw new RuntimeException('The record was deleted');
        }
        
        if($this->isNew)
        {
            $self = $this;
            
            $id = $this->database()->transaction(function($db) use($self){
                
                $db->insert($self->prepareColumns())
                   ->into($self->getTable());
                
                return $db->getConnection()->pdo()->lastInsertId($self->getSequence());
                
            })
            ->execute();
            
            $this->columns[$this->getPrimaryKey()] = $id;
            $this->isNew = false;
            
            return (bool) $id;
        }
        
        if(!empty($this->modified))
        {
            $pk = $this->getPrimaryKey();
            
            $result = $this->database()
                           ->update($this->getTable())
                           ->where($pk)->is($this->columns[$pk])
                           ->set($this->prepareColumns(true));
                           
            $this->modified = array();
            return (bool) $result;
        }
        
        return true;
        
    }
    
    public function delete()
    {
        if($this->deleted)
        {
            throw new RuntimeException('The record was deleted');
        }
        
        if($this->isNew)
        {
            throw new RuntimeException('This is a new record that was not saved yet');
        }
        $pk = $this->getPrimaryKey();
        
        $result = $this->database->from($this->getTable())
                                 ->where($pk)->is($this->columns[$pk])
                                 ->delete();
        $this->deleted = true;
        
        return (bool) $result;
    }
    
    public function assign(array $values)
    {
        if($this->fillable !== null && is_array($this->fillable))
        {
            foreach($values as $column => &$value)
            {
                if(in_array($column, $this->fillable))
                {
                    $this->{$column} = $value;
                }
            }
        }
        elseif($this->guarded !== null && is_array($this->guarded))
        {
            foreach($values as $column => &$value)
            {
                if(!in_array($column, $this->guarded))
                {
                    $this->{$column} = $value;
                }
            }
        }
        else
        {
            foreach($values as $column => &$value)
            {
                $this->{$column} = $value;
            }
        }
    }
    
    public function setLazyLoader($name, $value)
    {
        $this->loader[$name] = $value;
    }
    
    public function getTable()
    {
        if($this->table === null)
        {
            $this->table = strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $this->getClassShortName())) . 's';
        }
        
        return $this->table;
    }
    
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    
    public function getForeignKey()
    {
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $this->getClassShortName())) . '_id';
    }
    
    public function hasOne($model, $foreignKey = null)
    {
        return new HasOne(static::getConnection(), $this, new $model, $foreignKey);
    }
    
    public function hasMany($model, $foreignKey = null)
    {
        return new HasMany(static::getConnection(), $this, new $model, $foreignKey);
    }
    
    public function belongsTo($model, $foreignKey = null)
    {
        return new BelongsTo(static::getConnection(), $this, new $model, $foreignKey);
    }
    
    public function belongsToMany($model, $foreignKey = null, $junctionTable = null, $junctionKey = null)
    {
        return new BelongsToMany(static::getConnection(), $this, new $model, $foreignKey, $junctionTable, $junctionKey);
    }
    
    protected function database()
    {
        if($this->database === null)
        {
            $this->database = new Database(static::getConnection());
        }
        
        return $this->database;
    }
    
    protected function getSequence()
    {
        if($this->sequence === null)
        {
            $this->sequence = $this->getTable() . '_' . $this->getPrimaryKey() . '_seq';
        }
        
        return $this->sequence;
    }
    
    protected function prepareColumns($update = false)
    {
        $results = array();
        
        $columns = $update ? array_intersect_key($this->columns, $this->modified) : $this->columns;
        
        foreach($columns as $column => &$value)
        {
            if($value instanceof Model)
            {
                $results[$column] = $value->{$value->getPrimaryKey()};
                continue;
            }
            
            $results[$column] = &$value;
        }
        
        return $results;
    }
    
    protected function getClassShortName()
    {
        if($this->className === null)
        {
            $name = get_class($this);
            
            if(false !== $pos = strrpos($name, '\\'))
            {
                $name = substr($name, $pos + 1);
            }
            
            $this->className = $name;
        }
        
        return $this->className;
    }
    
    protected function queryBuilder()
    {
        return new Query(static::getConnection(), $this);
    }
    
    public function __call($name, array $arguments)
    {
        $object = $this->queryBuilder();
        
        if(method_exists($this, $name . 'Scope'))
        {
            array_unshift($arguments, $object);
            $object = $this;
            $name .= 'Scope';
        }
        
        return call_user_func_array(array($object, $name), $arguments);
    }
    
    public static function __callStatic($name, array $arguments)
    {
        return call_user_func_array(array(new static(), $name), $arguments);
    }
    
}
