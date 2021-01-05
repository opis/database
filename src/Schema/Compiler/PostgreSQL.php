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

class PostgreSQL extends Compiler
{
    protected array $modifiers = ['nullable', 'default'];

    public function getColumns(string $database, string $table): array
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
            . ', ' . $this->wrap('udt_name') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return [
            'sql' => $sql,
            'params' => [$database, $table],
        ];
    }

    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT current_schema()',
            'params' => [],
        ];
    }

    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'ALTER TABLE ' . $this->wrap($current) . ' RENAME TO ' . $this->wrap($new),
            'params' => [],
        ];
    }

    protected function handleTypeInteger(Column $column): string
    {
        $autoincrement = $column->get('autoincrement', false);

        return match($column->get('size', 'normal')) {
            'tiny', 'small' => $autoincrement ? 'SMALLSERIAL' : 'SMALLINT',
            // 'medium' => $autoincrement ? 'SERIAL' : 'INTEGER', // same as default
            'big' => $autoincrement ? 'BIGSERIAL' : 'BIGINT',
            default => $autoincrement ? 'SERIAL' : 'INTEGER',
        };
    }

    protected function handleTypeFloat(Column $column): string
    {
        return 'REAL';
    }

    protected function handleTypeDouble(Column $column): string
    {
        return 'DOUBLE PRECISION';
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

    protected function handleTypeBinary(Column $column): string
    {
        return 'BYTEA';
    }

    protected function handleTypeTime(Column $column): string
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }

    protected function handleTypeTimestamp(Column $column): string
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    protected function handleTypeDateTime(Column $column): string
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    protected function handleIndexKeys(Blueprint $schema): array
    {
        $indexes = $schema->getIndexes();

        if (empty($indexes)) {
            return [];
        }

        $sql = [];

        $table = $schema->getTableName();

        foreach ($indexes as $name => $columns) {
            $sql[] = 'CREATE INDEX ' . $this->wrap($table . '_' . $name) . ' ON ' . $this->wrap($table) . '(' . $this->wrapArray($columns) . ')';
        }

        return $sql;
    }

    protected function handleRenameColumn(Blueprint $table, mixed $data): string
    {
        /** @var Column $column */
        $column = $data['column'];
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' RENAME COLUMN '
            . $this->wrap($data['from']) . ' TO ' . $this->wrap($column->getName());
    }

    protected function handleAddIndex(Blueprint $table, mixed $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($table->getTableName() . '_' . $data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    protected function handleDropIndex(Blueprint $table, mixed $data): string
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName() . '_' . $data);
    }

    protected function handleEngine(Blueprint $schema): string
    {
        return '';
    }
}
