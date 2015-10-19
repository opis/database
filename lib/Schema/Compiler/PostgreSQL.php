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

class PostgreSQL extends Compiler
{
    
    protected $modifiers = array('nullable', 'default');
    
    protected function handleTypeInteger(BaseColumn $column)
    {
        $autoincrement = $column->get('autoincrement', false);
        
        switch($column->get('size', 'normal'))
        {
            case 'tiny':
            case 'small':
                return $autoincrement ? 'SMALLSERIAL' : 'SMALLINT';
            case 'medium':
                return $autoincrement ? 'SERIAL' : 'INTEGER';
            case 'big':
                return $autoincrement ? 'BIGSERIAL' : 'BIGINT';
        }
        
        return $autoincrement ? 'SERIAL' : 'INTEGER';
        
    }
    
    protected function handleTypeFloat(BaseColumn $column)
    {
        return 'REAL';
    }
    
    protected function handleTypeDouble(BaseColumn $column)
    {
        return 'DOUBLE PRECISION';
    }
    
    protected function handleTypeDecimal(BaseColumn $column)
    {
        if(null !== $m = $column->get('M') && null !== $p = $column->get('P'))
        {
            return 'DECIMAL (' . $this->value($m) . ', ' . $this->value($p) . ')';
        }
        
        return 'DECIMAL';
    }
    
    protected function handleTypeBinary(BaseColumn $column)
    {
        return 'BYTEA';
    }
    
    protected function handleTypeTime(BaseColumn $column)
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }
    
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }
    
    protected function handleTypeDateTime(BaseColumn $column)
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }
    
    protected function handleIndexKeys(CreateTable $schema)
    {
        $indexes = $schema->getIndexes();
        
        if(empty($indexes))
        {
            return array();
        }
        
        $sql = array();
        
        $table = $schema->getTableName();
        
        foreach($indexes as $name => $columns)
        {
            $sql[] = 'CREATE INDEX ' . $this->wrap($table . '_' . $name) . ' ON ' . $this->wrap($table) . '(' . $this->wrapArray($columns) . ')';
        }
        
        return $sql;
    }
    
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' RENAME COLUMN '
                . $this->wrap($data['from']) . ' TO ' . $this->wrap($data['column']->getName()); 
    }
    
    protected function handleAddIndex(AlterTable $table, $data)
    {
        return 'CREATE INDEX ' . $this->wrap($table->getTableName() . '_' . $data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' ('. $this->wrapArray($data['columns']) . ')';
    }
    
    protected function handleDropIndex(AlterTable $table, $data)
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName() . '_' . $data);
    }
    
    protected function handleEngine(CreateTable $schema)
    {
        return '';
    }
    
    public function getColumns($database, $table)
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
                . ', ' . $this->wrap('udt_name') . ' AS ' . $this->wrap('type')
                . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
                . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
                . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';
        
        return array(
            'sql' => $sql,
            'params' => array($database, $table),
        );
    }
    
    public function currentDatabase($dsn)
    {
        return array(
            'sql' => 'SELECT current_schema()',
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
