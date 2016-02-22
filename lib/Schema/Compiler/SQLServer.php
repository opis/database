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
    /** @var    string */
    protected $wrapper = '[%s]';

    /** @var    array */
    protected $modifiers = array('nullable', 'default', 'autoincrement');

    /** @var    string */
    protected $autoincrement = 'IDENTITY';

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeInteger(BaseColumn $column)
    {
        switch ($column->get('size', 'normal')) {
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
    protected function handleTypeBoolean(BaseColumn $column)
    {
        return 'BIT';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeString(BaseColumn $column)
    {
        return 'NVARCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeFixed(BaseColumn $column)
    {
        return 'NCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeText(BaseColumn $column)
    {
        return 'NVARCHAR(max)';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeBinary(BaseColumn $column)
    {
        return 'VARBINARY(max)';
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
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        return 'sp_rename ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data['from']) . ', '
            . $this->wrap($data['column']->getName()) . ', COLUMN';
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
     * @param   string  $old
     * @param   string  $new
     * 
     * @return  array
     */
    public function renameTable($old, $new)
    {
        return array(
            'sql' => 'sp_rename ' . $this->wrap($old) . ', ' . $this->wrap($new),
            'params' => array(),
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
            'sql' => 'SELECT SCHEMA_NAME()',
            'params' => array(),
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
