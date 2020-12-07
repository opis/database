<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

use Opis\Database\Schema\{
    Compiler, Column, Blueprint
};

class MySQL extends Compiler
{
    protected string $wrapper = '`%s`';

    protected function handleTypeInteger(Column $column): string
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

    protected function handleTypeDecimal(Column $column): string
    {
        if (null !== $l = $column->get('length')) {
            if (null === $p = $column->get('precision')) {
                return 'DECIMAL(' . $this->value($l) . ')';
            }
            return 'DECIMAL(' . $this->value($l) . ', ' . $this->value($p) . ')';
        }
        return 'DECIMAL';
    }

    protected function handleTypeBoolean(Column $column): string
    {
        return 'TINYINT(1)';
    }

    protected function handleTypeText(Column $column): string
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

    protected function handleTypeBinary(Column $column): string
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

    protected function handleDropPrimaryKey(Blueprint $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP PRIMARY KEY';
    }

    protected function handleDropUniqueKey(Blueprint $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP INDEX ' . $this->wrap($data);
    }

    protected function handleDropIndex(Blueprint $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP INDEX ' . $this->wrap($data);
    }

    protected function handleDropForeignKey(Blueprint $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP FOREIGN KEY ' . $this->wrap($data);
    }

    protected function handleSetDefaultValue(Blueprint $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER '
            . $this->wrap($data['column']) . ' SET DEFAULT ' . $this->value($data['value']);
    }

    protected function handleDropDefaultValue(Blueprint $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER ' . $this->wrap($data) . ' DROP DEFAULT';
    }

    protected function handleRenameColumn(Blueprint $table, $data): string
    {
        $table_name = $table->getTableName();
        $column_name = $data['from'];
        /** @var Column $column */
        $column = $data['column'];
        $new_name = $column->getName();
        $columns = $this->connection->getSchema()->getColumns($table_name, false, false);
        $column_type = isset($columns[$column_name]) ? $columns[$column_name]['type'] : 'integer';

        return 'ALTER TABLE ' . $this->wrap($table_name) . ' CHANGE ' . $this->wrap($column_name)
            . ' ' . $this->wrap($new_name) . ' ' . $column_type;
    }
}
