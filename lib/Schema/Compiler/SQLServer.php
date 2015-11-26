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
use Opis\Database\Schema\CreateTable;

class SQLServer extends Compiler
{
    protected $wrapper = '[%s]';
    
    protected $modifiers = array('nullable', 'default', 'autoincrement');
    
    protected $autoincrement = 'IDENTITY';
    
    protected function handleTypeInteger(BaseColumn $column)
    {
        switch($column->get('size', 'normal'))
        {
            case 'tiny':
                return 'TINYINT';
            case 'small':
                return 'SMALLINT';
            case 'medium':
                return 'INTEGER';
            case 'big':
                return 'BIGINT';
        }
        
        return 'INTEGER';
    }
    
    protected function handleTypeBoolean(BaseColumn $column)
    {
        return 'BIT';
    }
    
    protected function handleTypeString(BaseColumn $column)
    {
        return 'NVARCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }
    
    protected function handleTypeFixed(BaseColumn $column)
    {
        return 'NCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }
    
    protected function handleTypeText(BaseColumn $column)
    {
        return 'NVARCHAR(max)';
    }
    
    protected function handleTypeBinary(BaseColumn $column)
    {
        return 'VARBINARY(max)';
    }
    
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'DATETIME';
    }
    
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        return 'sp_rename ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data['from']) . ', '
                . $this->wrap($data['column']->getName()) . ', COLUMN';
    }
    
    protected function handleEngine(CreateTable $schema)
    {
        return '';
    }
    
    public function renameTable($old, $new)
    {
        return array(
            'sql' => 'sp_rename ' . $this->wrap($old) . ', ' . $this->wrap($new),
            'params' => array(),
        );
    }
    
    public function currentDatabase($dsn)
    {
        return array(
            'sql' => 'SELECT SCHEMA_NAME()',
            'params' => array(),
        );
    }
    
    public function getColumns($database, $table)
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
                . ', ' . $this->wrap('data_type') . ' AS ' . $this->wrap('type')
                . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
                . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
                . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';
        
        return array(
            'sql' => $sql,
            'params' => array($database, $table),
        );
    }
}
