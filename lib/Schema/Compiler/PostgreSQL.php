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

class PostgreSQL extends Compiler
{
    /** @var    array */
    protected $modifiers = array('nullable', 'default');

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeInteger(BaseColumn $column)
    {
        $autoincrement = $column->get('autoincrement', false);

        switch ($column->get('size', 'normal')) {
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

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeFloat(BaseColumn $column)
    {
        return 'REAL';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDouble(BaseColumn $column)
    {
        return 'DOUBLE PRECISION';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDecimal(BaseColumn $column)
    {
        if (null !== $l = $column->get('length')) {
        	if (null === $p = $column->get('precision')) {
        		return 'DECIMAL (' . $this->value($l) . ')';
        	}
            return 'DECIMAL (' . $this->value($l) . ', ' . $this->value($p) . ')';
        }
        return 'DECIMAL';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeBinary(BaseColumn $column)
    {
        return 'BYTEA';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeTime(BaseColumn $column)
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeDateTime(BaseColumn $column)
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
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

        $table = $schema->getTableName();

        foreach ($indexes as $name => $columns) {
            $sql[] = 'CREATE INDEX ' . $this->wrap($table . '_' . $name) . ' ON ' . $this->wrap($table) . '(' . $this->wrapArray($columns) . ')';
        }

        return $sql;
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' RENAME COLUMN '
            . $this->wrap($data['from']) . ' TO ' . $this->wrap($data['column']->getName());
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleAddIndex(AlterTable $table, $data)
    {
        return 'CREATE INDEX ' . $this->wrap($table->getTableName() . '_' . $data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropIndex(AlterTable $table, $data)
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName() . '_' . $data);
    }

    /**
     * @param   CreateTable $schema
     * 
     * @return  string
     */
    protected function handleEngine(CreateTable $schema)
    {
        return '';
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
            . ', ' . $this->wrap('udt_name') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return array(
            'sql' => $sql,
            'params' => array($database, $table),
        );
    }

    /**
     * @param   string  $dsn
     * 
     * @return  array
     */
    public function currentDatabase($dsn)
    {
        return array(
            'sql' => 'SELECT current_schema()',
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
