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

class AlterTable
{
    protected $table;
    
    protected $commands = array();
    
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    protected function addCommand($name, $data)
    {
        $this->commands[] = array(
            'type' => $name,
            'data' => $data,
        );
        
        return $this;
    }
    
    protected function addKey($type, $name, $columns)
    {
        if($columns === null)
        {
            $columns = array($name);
        }
        elseif(!is_array($columns))
        {
            $columns = array($columns);
        }
        
        return $this->addCommand($type, array(
            'name' => $name,
            'columns' => $columns,
        ));
    }
    
    protected function addColumn($name, $type)
    {
        $columnObject = new AlterColumn($this, $name, $type);
        $this->addCommand('addColumn', $columnObject);
        return $columnObject;
    }
    
    public function getTableName()
    {
        return $this->table;
    }
    
    
    public function getCommands()
    {
        return $this->commands;
    }
    
    
    public function dropIndex($name)
    {
        return $this->addCommand('dropIndex', $name);
    }
    
    public function dropUnique($name)
    {
        return $this->addCommand('dropUniqueKey', $name);
    }
    
    public function dropPrimary($name)
    {
        return $this->addCommand('dropPrimaryKey', $name);
    }
    
    public function dropForeign($name)
    {
        return $this->addCommand('dropForeignKey', $name);
    }
    
    public function dropColumn($name)
    {
        return $this->addCommand('dropColumn', $name);
    }
    
    public function renameColumn($from, $to, $type)
    {
        $type = strtolower($type);
        
        if(!in_array($type, array('integer', 'float', 'double', 'boolean', 'decimal',
                                  'string', 'fixed', 'text', 'date', 'time',
                                  'datetime', 'timestamp', 'binary')))
        {
            return $this;
        }
        
        return $this->addCommand('renameColumn', array(
            'from' => $from,
            'column' => new AlterColumn($this, $to, $type),
        ));
    }
    
    public function modifyColumn($column)
    {
        $columnObject = new AlterColumn($column);
        $this->addCommand('modifyColumn', $columnObject);
        return $columnObject;
    }
    
    public function addPrimary($name, $columns = null)
    {
        return $this->addKey('addPrimary', $name, $columns);
    }
    
    public function addUnique($name, $columns = null)
    {
        return $this->addKey('addUnique', $name, $columns);
    }
    
    public function addIndex($name, $columns = null)
    {
        return $this->addKey('addIndex', $name, $columns);
    }
    
    public function addForeign($name, $columns = null)
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
        $this->addCommand('addForeign', array(
            'name' => $name,
            'foreign' => $foreign,
        ));
        
        return $foreign;
    }
    
    public function integer($name)
    {
        return $this->addColumn($name, 'integer');
    }
    
    public function float($name)
    {
        return $this->addColumn($name, 'float');
    }
    
    public function double($name)
    {
        return $this->addColumn($name, 'double');
    }
    
    public function decimal($name, $maximum = null, $decimal = null)
    {
        return $this->addColumn($name, 'decimal')->set('M', $maximum)->set('D', $maximum);
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
    
    public function fixed($name, $length = 255)
    {
        return $this->addColumn($name, 'fixed')->set('length', $length);
    }
    
    public function text($name)
    {
        return $this->addColumn($name);
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
