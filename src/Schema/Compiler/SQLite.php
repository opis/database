<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

use Opis\Database\Schema\AlterTable;
use Opis\Database\Schema\Compiler;
use Opis\Database\Schema\BaseColumn;
use Opis\Database\Schema\CreateTable;

class SQLite extends Compiler
{
    /** @var    array */
    protected $modifiers = array('nullable', 'default', 'autoincrement');

    /** @var    string */
    protected $autoincrement = 'PRIMARY KEY AUTOINCREMENT';

    /** @var bool No primary key */
    private $nopk = false;

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeInteger(BaseColumn $column)
    {
        return 'INTEGER';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeTime(BaseColumn $column)
    {
        return 'DATETIME';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'DATETIME';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    public function handleModifierAutoincrement(BaseColumn $column)
    {
        $modifier = parent::handleModifierAutoincrement($column);

        if($modifier !== ''){
            $this->nopk = true;
        }

        return $modifier;
    }

    /**
     * @param AlterTable $table
     * @param mixed $data
     * @return string
     */
    public function handleAddPrimary(AlterTable $table, $data)
    {
        if($this->nopk){
            return '';
        }

        return parent::handleAddPrimary($table, $data);
    }

    /**
     * @param   CreateTable     $schema
     * 
     * @return  string
     */
    protected function handleEngine(CreateTable $schema)
    {
        return '';
    }

    /**
     * @param   string  $dsn
     * 
     * @return  string
     */
    public function currentDatabase($dsn)
    {
        return substr($dsn, strpos($dsn, ':') + 1);
    }

    /**
     * @param   string  $database
     * 
     * @return  array
     */
    public function getTables($database)
    {
        $sql = 'SELECT ' . $this->wrap('name') . ' FROM ' . $this->wrap('sqlite_master')
            . ' WHERE type = ? ORDER BY ' . $this->wrap('name') . ' ASC';

        return array(
            'sql' => $sql,
            'params' => array('table'),
        );
    }

    /**
     * @param   string  $database
     * @param   string  $table
     * 
     * @return  array
     */
    public function getColumns($database, $table)
    {
        return array(
            'sql' => 'PRAGMA table_info(' . $this->wrap($table) . ')',
            'params' => array(),
        );
    }

    /**
     * @param   string  $old
     * @param   string  $new
     * 
     * @return  array
     */
    public function renameTable($old, $new)
    {
        return array(
            'sql' => 'ALTER TABLE ' . $this->wrap($old) . ' RENAME TO ' . $this->wrap($new),
            'params' => array(),
        );
    }
}
