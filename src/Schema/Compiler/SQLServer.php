<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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

class SQLServer extends Compiler
{
    protected string $wrapper = '[%s]';
    protected array $modifiers = ['nullable', 'default', 'autoincrement'];
    protected string $autoincrement = 'IDENTITY';

    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'sp_rename ' . $this->wrap($current) . ', ' . $this->wrap($new),
            'params' => [],
        ];
    }

    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT SCHEMA_NAME()',
            'params' => [],
        ];
    }

    public function getColumns(string $database, string $table): array
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
            . ', ' . $this->wrap('data_type') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return [
            'sql' => $sql,
            'params' => [$database, $table],
        ];
    }

    protected function handleTypeInteger(Column $column): string
    {
        return match($column->get('size', 'normal')) {
            'tiny' => 'TINYINT',
            'small' => 'SMALLINT',
            // 'medium' => 'INTEGER',
            'big' => 'BIGINT',
            default => 'INTEGER',
        };
    }

    protected function handleTypeDecimal(Column $column): string
    {
        if (null !== $l = $column->get('length')) {
            if (null === $p = $column->get('precision')) {
                return 'DECIMAL (' . $this->value($l) . ')';
            }
            return 'DECIMAL (' . $this->value($l) . ', ' . $this->value($p) . ')';
        }
        return 'DECIMAL';
    }

    protected function handleTypeBoolean(Column $column): string
    {
        return 'BIT';
    }

    protected function handleTypeString(Column $column): string
    {
        return 'NVARCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    protected function handleTypeFixed(Column $column): string
    {
        return 'NCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    protected function handleTypeText(Column $column): string
    {
        return 'NVARCHAR(max)';
    }

    protected function handleTypeBinary(Column $column): string
    {
        return 'VARBINARY(max)';
    }

    protected function handleTypeTimestamp(Column $column): string
    {
        return 'DATETIME';
    }

    protected function handleRenameColumn(Blueprint $table, $data): string
    {
        /** @var Column $column */
        $column = $data['column'];
        return 'sp_rename ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data['from']) . ', '
            . $this->wrap($column->getName()) . ', COLUMN';
    }

    protected function handleEngine(Blueprint $schema): string
    {
        return '';
    }
}
