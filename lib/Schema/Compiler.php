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

class Compiler
{
    
    protected $separator = ';';
    
    protected $wrapper = '"%s"';
    
    protected $params = array();
    
    protected $modifiers = array('unsigned', 'nullable', 'default', 'autoincrement');
    
    protected function wrap($name)
    {
        return sprintf($this->wrapper, $name);
    }
    
    protected function wrapArray(array $value, $separator = ', ')
    {
        return implode($separator, array_map(array($this, 'wrap'), $value));
    }
    
    protected function value($value)
    {
        $this->params[] = $value;
        return '?';
    }
    
    protected function handleColumns(array $columns)
    {
        $sql = array();
        
        foreach($columns as $column)
        {
            $line  = $this->wrap($column->getName());
            $line .= $this->handleColumnType($column);
            $line .= $this->handleColumnModifiers($column);
            $sql[] = $line;
        }
        
        return implode(",\n", $sql);
    }
    
    protected function handleColumnType(BaseColumn $column)
    {
        $type  = 'handleType' . ucfirst($column->getType());
        return $this->{$type}($column);
    }
    
    protected function handleColumnModifiers(BaseColumn $column)
    {
        $line = '';
        foreach($this->modifiers as $modifier)
        {
            $callback = 'handleModifier' . ucfirst($modifier);
            $line .= $this->{$callback}($column);
        }
        return $line;
    }
    
    protected function handleTypeInteger(BaseColumn $column)
    {
        return ' INTEGER';
    }
    
    protected function handleTypeFloat(BaseColumn $column)
    {
        return ' FLOAT';
    }
    
    protected function hanleTypeDouble(BaseColumn $column)
    {
        return ' DOUBLE';
    }
    
    protected function handleTypeDecimal(BaseColumn $column)
    {
        return ' DECIMAL';
    }
    
    protected function handleTypeBoolean(BaseColumn $column)
    {
        return ' TINYINT(1)';
    }
    
    protected function handleTypeBinary(BaseColumn $column)
    {
        return ' BLOB';
    }
    
    protected function handleTypeString(BaseColumn $column)
    {
        return ' VARCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }
    
    protected function handleTypeFixed(BaseColumn $column)
    {
        return ' CHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }
    
    protected function handleTypeTime(BaseColumn $column)
    {
        return ' TIME';
    }
    
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return ' TIMESTAMP';
    }
    
    protected function handleTypeDate(BaseColumn $column)
    {
        return ' DATE';
    }
    
    protected function handleTypeDateTime(BaseColumn $column)
    {
        return ' DATETIME';
    }
    
    protected function handleModifierUnsigned(BaseColumn $column)
    {
        return $column->get('unisgned', false) ? ' UNISGNED' : '';
    }
    
    protected function handleModifierNullable(BaseColumn $column)
    {
        return $column->get('nullable', false) ? ' NULL' : ' NOT NULL';
    }
    
    protected function handleModifierDefault(BaseColumn $column)
    {
        return null === $column->get('default') ? '' : ' DEFAULT (' . $this->value($column->get('default')) . ')';
    }
    
    protected function handleModifierAutoincrement(BaseColumn $column)
    {
        if($column->getType() !== 'integer')
        {
            return '';
        }
        
        return $column->get('autoincrement', false) ? ' AUTO_INCREMENT' : '';
    }
    
    protected function handlePrimaryKey(CreateTable $schema)
    {
        
        if(null === $pk = $schema->getPrimaryKey())
        {
            return '';
        }
        
        return ",\n" . 'CONSTRAINT ' . $this->wrap($pk['name']) . ' PRIMARY KEY (' . $this->wrapArray($pk['columns']) . ')';
    }
    
    protected function handleUniqueKeys(CreateTable $schema)
    {
        
        $indexes = $schema->getUniqueKeys();
        
        if(empty($indexes))
        {
            return '';
        }
        
        $sql = array();
        
        foreach($schema->getUniqueKeys() as $name => $columns)
        {   
            $sql[] = 'CONSTRAINT ' . $this->wrap($name) . ' UNIQUE (' . $this->wrapArray($columns) . ')';
        }
        
        return ",\n" . implode(",\n", $sql);
    }
    
    protected function handleIndexKeys(CreateTable $schema)
    {
        $indexes = $schema->getIndexes();
        
        if(empty($indexes))
        {
            return array();
        }
        
        $sql = array();
        
        foreach($indexes as $name => $columns)
        {
            $sql[] = 'CREATE INDEX ' . $this->wrap($name) . ' ON ' . $this->wrap($schema->getTableName()) . '(' . $this->wrapArray($columns) . ')';
        }
        
        return $sql;
    }
    
    protected function handleForeignKeys(CreateTable $schema)
    {
        $keys = $schema->getForeignKeys();
        
        if(empty($keys))
        {
            return '';
        }
        
        $sql = array();
        
        foreach($keys as $name => $key)
        {
            $cmd  = 'CONSTRAINT ' . $this->wrap($name) . ' FOREIGN KEY (' . $this->wrapArray($key->getColumns()). ') ';
            $cmd .= 'REFERENCES ' . $this->wrap($key->getReferencedTable()) . ' ('. $this->wrapArray($key->getReferencedColumns()) .')';
            
            foreach($key->getActions() as $actionName => $action)
            {
                $cmd .= ' ' . $actionName . ' ' . $action;
            }
            
            $sql[] = $cmd;
        }
        
        return ",\n" . implode(",\n", $sql);
    }
    
    protected function handleEngine(CreateTable $schema)
    {
        if(null !== $engine = $schema->getEngine())
        {
            return ' ENGINE = ' . strtoupper($engine);
        }
        
        return '';
    }
    
    protected function handleDropPrimaryKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }
    
    protected function handleDropUniqueKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }
    
    protected function handleDropIndex(AlterTable $table, $data)
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data);
    }
    
    protected function handleDropForeignKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }
    
    protected function handleDropColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP COLUMN ' . $this->wrap($data);
    }
    
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' CHANGE '. $this->wrap($data['from']) .', '
        .  $this->wrap($data['column']->getName()) . $this->handleColumnType($data['column']);
    }
    
    protected function handleModifyColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' MODIFY COLUMN ' . $this->handleColumns(array($data));
    }
    
    protected function handleAddColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD COLUMN ' . $this->handleColumns(array($data));
    }
    
    protected function handleAddPrimary(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
        . $this->wrap($data['name']) . ' PRIMARY KEY (' . $this->wrapArray($data['columns']) . ')';
    }
    
    protected function handleAddUnique(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
        . $this->wrap($data['name']) . ' UNIQUE (' . $this->wrapArray($data['columns']) . ')';
    }
    
    protected function handleAddIndex(AlterTable $table, $data)
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' ('. $this->wrapArray($data['columns']) . ')';
    }
    
    protected function handleAddForeign(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
        . $this->wrap($data['name']) . ' UNIQUE (' . $this->wrapArray($data['columns']) . ')';
    }
    
    public function getParams()
    {
        $params = $this->params;
        $this->params = array();
        return $params;
    }
    
    public function create(CreateTable $schema)
    {
        $sql  = 'CREATE TABLE ' . $this->wrap($schema->getTableName());
        $sql .= "(\n";
        $sql .= $this->handleColumns($schema->getColumns());
        $sql .= $this->handlePrimaryKey($schema);
        $sql .= $this->handleUniqueKeys($schema);
        $sql .= $this->handleForeignKeys($schema);
        $sql .= "\n)" . $this->handleEngine($schema);
        
        $commands = array();
        
        $commands[] = array(
            'sql' => $sql,
            'params' => $this->getParams(),
        );
        
        foreach($this->handleIndexKeys($schema) as $index)
        {
            $commands[] = array(
                'sql' => $index,
                'params' => array(),
            );
        }
        
        return $commands;
    }
    
    public function alter(AlterTable $schema)
    {
        $commands = array();
        
        foreach($schema->getCommands() as $command)
        {
            $type = 'handle' . ucfirst($command['type']);
            $sql = $this->{$type}($schema, $command['data']);
            $commands[] = array(
                'sql' => $sql,
                'params' => $this->getParams(),
            );
        }
        
        return $commands;
    }
    
    public function drop($table)
    {
        return array(
            'sql' => 'DROP TABLE ' . $this->wrap($table),
            'params' => array(),
        );
    }
    
    public function truncate($table)
    {
        return array(
            'sql' => 'TRUNCATE TABLE ' . $this->wrap($table),
            'params' => array(),
        );
    }
    
}
