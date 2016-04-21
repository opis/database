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

namespace Opis\Database\Schema;

use Opis\Database\Connection;

class Compiler
{
    /** @var    string */
    protected $separator = ';';

    /** @var    string */
    protected $wrapper = '"%s"';

    /** @var    array */
    protected $params = array();

    /** @var    array */
    protected $modifiers = array('unsigned', 'nullable', 'default', 'autoincrement');

    /** @var    array */
    protected $serials = array('tiny', 'small', 'normal', 'medium', 'big');

    /** @var    string */
    protected $autoincrement = 'AUTO_INCREMENT';

    /** @var    Connection */
    protected $connection;

    /**
     * Constructor
     * 
     * @param   Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Sets compiler options
     * 
     * @param   array   $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->{$name} = $value;
        }
    }


    /**
     * @param   string  $name
     * 
     * @return  string
     */
    protected function wrap($name)
    {
        return sprintf($this->wrapper, $name);
    }

    /**
     * @param   array   $value
     * @param   string  $separator  (optional)
     * 
     * @return  string
     */
    protected function wrapArray(array $value, $separator = ', ')
    {
        return implode($separator, array_map(array($this, 'wrap'), $value));
    }

    /**
     * @param   mixed   $value
     * 
     * @return  float|int|string
     */
    protected function value($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_string($value)) {
            return "'" . str_replace("'", "''", $value) . "'";
        }

        return 'NULL';
    }

    /**
     * @param   BaseColumn[]    $column
     * 
     * @return  string
     */
    protected function handleColumns(array $columns)
    {
        $sql = array();

        foreach ($columns as $column) {
            $line = $this->wrap($column->getName());
            $line .= $this->handleColumnType($column);
            $line .= $this->handleColumnModifiers($column);
            $sql[] = $line;
        }

        return implode(",\n", $sql);
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleColumnType(BaseColumn $column)
    {
        $type = 'handleType' . ucfirst($column->getType());
        $result = trim($this->{$type}($column));

        if ($result !== '') {
            $result = ' ' . $result;
        }

        return $result;
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleColumnModifiers(BaseColumn $column)
    {
        $line = '';

        foreach ($this->modifiers as $modifier) {
            $callback = 'handleModifier' . ucfirst($modifier);
            $result = trim($this->{$callback}($column));

            if ($result !== '') {
                $result = ' ' . $result;
            }

            $line .= $result;
        }

        return $line;
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeInteger(BaseColumn $column)
    {
        return 'INT';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeFloat(BaseColumn $column)
    {
        return 'FLOAT';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDouble(BaseColumn $column)
    {
        return 'DOUBLE';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDecimal(BaseColumn $column)
    {
        return 'DECIMAL';
    }

    /**
     * @param   BaseColumn   $column
     * 
     * @return  string
     */
    protected function handleTypeBoolean(BaseColumn $column)
    {
        return 'BOOLEAN';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeBinary(BaseColumn $column)
    {
        return 'BLOB';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeText(BaseColumn $column)
    {
        return 'TEXT';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeString(BaseColumn $column)
    {
        return 'VARCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeFixed(BaseColumn $column)
    {
        return 'CHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeTime(BaseColumn $column)
    {
        return 'TIME';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'TIMESTAMP';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDate(BaseColumn $column)
    {
        return 'DATE';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDateTime(BaseColumn $column)
    {
        return 'DATETIME';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleModifierUnsigned(BaseColumn $column)
    {
        return $column->get('unisgned', false) ? 'UNSIGNED' : '';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleModifierNullable(BaseColumn $column)
    {
        if ($column->get('nullable', true)) {
            return '';
        }

        return 'NOT NULL';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleModifierDefault(BaseColumn $column)
    {
        return null === $column->get('default') ? '' : 'DEFAULT ' . $this->value($column->get('default'));
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleModifierAutoincrement(BaseColumn $column)
    {
        if ($column->getType() !== 'integer' || !in_array($column->get('size', 'normal'), $this->serials)) {
            return '';
        }

        return $column->get('autoincrement', false) ? $this->autoincrement : '';
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  string
     */
    protected function handlePrimaryKey(CreateTable $schema)
    {
        if (null === $pk = $schema->getPrimaryKey()) {
            return '';
        }

        return ",\n" . 'CONSTRAINT ' . $this->wrap($pk['name']) . ' PRIMARY KEY (' . $this->wrapArray($pk['columns']) . ')';
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  string
     */
    protected function handleUniqueKeys(CreateTable $schema)
    {
        $indexes = $schema->getUniqueKeys();

        if (empty($indexes)) {
            return '';
        }

        $sql = array();

        foreach ($schema->getUniqueKeys() as $name => $columns) {
            $sql[] = 'CONSTRAINT ' . $this->wrap($name) . ' UNIQUE (' . $this->wrapArray($columns) . ')';
        }

        return ",\n" . implode(",\n", $sql);
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  string
     */
    protected function handleIndexKeys(CreateTable $schema)
    {
        $indexes = $schema->getIndexes();

        if (empty($indexes)) {
            return array();
        }

        $sql = array();
        $table = $this->wrap($schema->getTableName());

        foreach ($indexes as $name => $columns) {
            $sql[] = 'CREATE INDEX ' . $this->wrap($name) . ' ON ' . $table . '(' . $this->wrapArray($columns) . ')';
        }

        return $sql;
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  string
     */
    protected function handleForeignKeys(CreateTable $schema)
    {
        $keys = $schema->getForeignKeys();

        if (empty($keys)) {
            return '';
        }

        $sql = array();

        foreach ($keys as $name => $key) {
            $cmd = 'CONSTRAINT ' . $this->wrap($name) . ' FOREIGN KEY (' . $this->wrapArray($key->getColumns()) . ') ';
            $cmd .= 'REFERENCES ' . $this->wrap($key->getReferencedTable()) . ' (' . $this->wrapArray($key->getReferencedColumns()) . ')';

            foreach ($key->getActions() as $actionName => $action) {
                $cmd .= ' ' . $actionName . ' ' . $action;
            }

            $sql[] = $cmd;
        }

        return ",\n" . implode(",\n", $sql);
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  string
     */
    protected function handleEngine(CreateTable $schema)
    {
        if (null !== $engine = $schema->getEngine()) {
            return ' ENGINE = ' . strtoupper($engine);
        }

        return '';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropPrimaryKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropUniqueKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropIndex(AlterTable $table, $data)
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropForeignKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP COLUMN ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  null
     */
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        return null;
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleModifyColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' MODIFY COLUMN ' . $this->handleColumns(array($data));
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleAddColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD COLUMN ' . $this->handleColumns(array($data));
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleAddPrimary(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' PRIMARY KEY (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleAddUnique(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' UNIQUE (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleAddIndex(AlterTable $table, $data)
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleAddForeign(AlterTable $table, $data)
    {
        $key = $data['foreign'];
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' FOREIGN KEY (' . $this->wrapArray($key->getColumns()) . ') '
            . 'REFERENCES ' . $this->wrap($key->getReferencedTable()) . '(' . $this->wrapArray($key->getReferencedColumns()) . ')';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleSetDefaultValue(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER COLUMN '
            . $this->wrap($data['column']) . ' SET DEFAULT ' . $this->value($data['value']);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropDefaultValue(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER COLUMN '
            . $this->wrap($data) . ' DROP DEFAULT';
    }

    /**
     * @return  array
     */
    public function getParams()
    {
        $params = $this->params;
        $this->params = array();
        return $params;
    }

    /**
     * @param   string  $dsn
     * 
     * @return  array
     */
    public function currentDatabase($dsn)
    {
        return array(
            'sql' => 'SELECT database()',
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
            'sql' => 'RENAME TABLE ' . $this->wrap($old) . ' TO ' . $this->wrap($new),
            'params' => array(),
        );
    }

    /**
     * @param   string  $database
     * 
     * @return  array
     */
    public function getTables($database)
    {
        $sql = 'SELECT ' . $this->wrap('table_name') . ' FROM ' . $this->wrap('information_schema')
            . '.' . $this->wrap('tables') . ' WHERE table_type = ? AND table_schema = ? ORDER BY '
            . $this->wrap('table_name') . ' ASC';

        return array(
            'sql' => $sql,
            'params' => array('BASE TABLE', $database),
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
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
            . ', ' . $this->wrap('column_type') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return array(
            'sql' => $sql,
            'params' => array($database, $table),
        );
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  array
     */
    public function create(CreateTable $schema)
    {
        $sql = 'CREATE TABLE ' . $this->wrap($schema->getTableName());
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

        foreach ($this->handleIndexKeys($schema) as $index) {
            $commands[] = array(
                'sql' => $index,
                'params' => array(),
            );
        }

        return $commands;
    }

    /**
     * @param   AlterTable  $schema
     * 
     * @return  array
     */
    public function alter(AlterTable $schema)
    {
        $commands = array();

        foreach ($schema->getCommands() as $command) {
            $type = 'handle' . ucfirst($command['type']);
            $sql = $this->{$type}($schema, $command['data']);

            if ($sql === null) {
                continue;
            }

            $commands[] = array(
                'sql' => $sql,
                'params' => $this->getParams(),
            );
        }

        return $commands;
    }

    /**
     * @param   string  $table
     * 
     * @return  array
     */
    public function drop($table)
    {
        return array(
            'sql' => 'DROP TABLE ' . $this->wrap($table),
            'params' => array(),
        );
    }

    /**
     * @param   string  $table
     * 
     * @return  array
     */
    public function truncate($table)
    {
        return array(
            'sql' => 'TRUNCATE TABLE ' . $this->wrap($table),
            'params' => array(),
        );
    }
}
