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

namespace Opis\Database\Schema\Compiler;

use Opis\Database\Schema\Compiler;
use Opis\Database\Schema\BaseColumn;
use Opis\Database\Schema\AlterTable;

class SQLite extends Compiler
{
    protected $modifiers = array('nullable', 'default', 'autoincrement');
    
    protected $autoincrement = 'AUTOINCREMENT';
    
    
    protected function handleTypeTime(BaseColumn $column)
    {
        return 'DATETIME';
    }
    
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'DATETIME';
    }
    
    protected function handleEngine(CreateTable $schema)
    {   
        return '';
    }
    
    public function currentDatabase($dsn)
    {
        return substr($dsn, strpos($dsn, ':') + 1);
    }
    
    public function getTables($database)
    {
        $sql = 'SELECT ' . $this->wrap('name') . ' FROM ' .  $this->wrap('sqlite_master')
                . ' WHERE type = ? ORDER BY ' . $this->wrap('name') . ' ASC';
        
        return array(
            'sql' => $sql,
            'params' => array('table'),
        );
    }
    
    public function getColumns($database, $table)
    {
        return array(
            'sql' => 'PRAGMA table_info('. $this->wrap($table) . ')',
            'params' => array(),
        );
    }
    
    public function renameTable($old, $new)
    {
        return array(
            'sql' => 'ALTER TABLE ' .$this->wrap($old) . ' RENAME TO ' . $this->wrap($new),
            'params' => array(),
        );
    }
    
}
