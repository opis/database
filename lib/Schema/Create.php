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

namespace Opis\Database\Schema;

class Create
{
    
    protected $columns = array();
    
    protected $primaryKey;
    
    protected $uniqueKeys = array();
    
    protected $indexes = array();
    
    protected $foreignKeys = array();
    
    protected $table;
    
    protected $engine;
    
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    protected function addColumn($name, $type)
    {
        $column = new Column($this, $type, $name);
        $this->columns[$name] = $column;
        return $column;
    }
    
    public function getTableName()
    {
        return $this->table;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    
    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }
    
    public function getIndexes()
    {
        return $this->indexes;
    }
    
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }
    
    public function getEngine()
    {
        return $this->engine;
    }
    
    public function engine($name)
    {
        $this->engine = $name;
        return $this;
    }
    
    public function primary($name, $columns = null)
    {
        if($columns === null)
        {
            $columns = array($name);
        }
        elseif(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        $this->primaryKey = array(
            'name' => $name,
            'columns' => $columns,
        );
        
        return $this;
    }
    
    public function unique($name, $columns = null)
    {
        
        if($columns === null)
        {
            $columns = array($name);
        }
        elseif(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        $this->uniqueKeys[$name] = $columns; 
        
        return $this;
    }
    
    public function index($name, $columns = null)
    {
        if($columns === null)
        {
            $columns = array($name);
        }
        elseif(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        $this->indexes[$name] = $columns;
        
        return $this;
    }
    
    public function foreign($name, $columns = null)
    {
        if($columns === null)
        {
            $columns = array($name);
        }
        elseif(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        $foreign = new ForeignKey($columns);
        
        $this->foreignKeys[$name] = $foreign;
        return $foreign;
    }
    
    public function integer($name)
    {
        return $this->addColumn($name, 'integer');
    }
    
    public function bigInteger($name)
    {
        return $this->addColumn($name, 'bigInteger');
    }
    
    public function smallInteger($name)
    {
        return $this->addColumn($name, 'smallInteger');
    }
    
    public function mediumInteger($name)
    {
        return $this->addColumn($name, 'mediumInteger');
    }
    
    public function serial($name)
    {
        $this->addColumn($name, 'serial')->primary();
    }
    
    public function bigSerial($name)
    {
        return $this->addColumn($name, 'bigSerial')->primary();
    }
    
    public function float($name)
    {
        return $this->addColumn($name, 'float');
    }
    
    public function double($name)
    {
        return $this->addColumn($name, 'double');
    }
    
    public function decimal($name)
    {
        return $this->addColumn($name, 'decimal');
    }
    
    public function boolean($name)
    {
        return $this->addColumn($name, 'boolean');
    }
    
    public function binary($name)
    {
        return $this->addColumn($name, 'binary');
    }
    
    public function string($name, $length = 255)
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }
    
    public function text($name)
    {
        return $this->addColumn($name);
    }
    
    public function longText($name)
    {
        return $this->addColumn($name, 'longText');
    }
    
    public function mediumText($name)
    {
        return $this->addColumn($name, 'mediumText');
    }
    
    public function time($name)
    {
        return $this->addColumn($name, 'time');
    }
    
    public function timestamp($name)
    {
        return $this->addColumn($name, 'timestamp');
    }
    
    public function date($name)
    {
        return $this->addColumn($name, 'date');
    }
    
    public function dateTime($name)
    {
        return $this->addColumn($name, 'dateTime');
    }
    
}
