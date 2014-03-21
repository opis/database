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

class Alter
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
    
    public function renameColumn($from, $to)
    {
        return $this->addCommand('renameColumn', array('from' => $from, 'to' => $to));
    }
    
    public function modifyColumn($column)
    {
        $columnObject = new AlterColumn($column);
        $this->addCommand('modifyColumn', $columnObject);
        return $columnObject;
    }
    
    public function addColumn($name)
    {
        $columnObject = new AlterColumn($column);
        $this->addCommand('addColumn', $columnObject);
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
    
    
}
