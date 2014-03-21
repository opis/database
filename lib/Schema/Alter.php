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
    
    public function getTableName()
    {
        return $this->table;
    }
    
    
    public function dropIndex($name)
    {
        return $this->addCommand('dropIndex', $name);
    }
    
    public function dropUniqueKey($name)
    {
        return $this->addCommand('dropUniqueKey', $name);
    }
    
    public function dropPrimaryKey($name)
    {
        return $this->addCommand('dropPrimaryKey', $name);
    }
    
    public function dropColumn($name)
    {
        return $this->addCommand('dropColumn', $name);
    }
    
    public function renameColumn($from, $to)
    {
        return $this->addCommand('renameColumn', array('from' => $from, 'to' => $to));
    }
    
    
    
}
