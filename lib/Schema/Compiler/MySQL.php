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

class MySQL extends Compiler
{
    /** @var    string */
    protected $wrapper = '`%s`';

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
                return 'MEDIUMINT';
            case 'big':
                return 'BIGINT';
        }

        return 'INT';
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
        return 'TINYINT(1)';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeText(BaseColumn $column)
    {
        switch ($column->get('size', 'normal')) {
            case 'tiny':
            case 'small':
                return 'TINYTEXT';
            case 'medium':
                return 'MEDIUMTEXT';
            case 'big':
                return 'LONGTEXT';
        }

        return 'TEXT';
    }

    /**
     * @param   BaseColumn  $column
     * 
     * @return  string
     */
    protected function handleTypeBinary(BaseColumn $column)
    {
        switch ($column->get('size', 'normal')) {
            case 'tiny':
            case 'small':
                return 'TINYBLOB';
            case 'medium':
                return 'MEDIUMBLOB';
            case 'big':
                return 'LONGBLOB';
        }

        return 'BLOB';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropPrimaryKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP PRIMARY KEY';
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropUniqueKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP INDEX ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropIndex(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP INDEX ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleDropForeignKey(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP FOREIGN KEY ' . $this->wrap($data);
    }

    /**
     * @param   AlterTable  $table
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleSetDefaultValue(AlterTable $table, $data)
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER '
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
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER ' . $this->wrap($data) . ' DROP DEFAULT';
    }

    /**
     * @param   AlterTable  $column
     * @param   mixed       $data
     * 
     * @return  string
     */
    protected function handleRenameColumn(AlterTable $table, $data)
    {
        $table_name = $table->getTableName();
        $column_name = $data['from'];
        $new_name = $data['column']->getName();
        $columns = $this->connection->schema()->getColumns($table_name, false, false);
        $column_type = isset($columns[$column_name]) ? $columns[$column_name]['type'] : 'integer';

        return 'ALTER TABLE ' . $this->wrap($table_name) . ' CHANGE ' . $this->wrap($column_name)
            . ' ' . $this->wrap($new_name) . ' ' . $column_type;
    }
}
