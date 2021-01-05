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

class SQLite extends Compiler
{
    private bool $noPrimaryKey = false;

    protected array $modifiers = ['nullable', 'default', 'autoincrement'];
    protected string $autoincrement = 'AUTOINCREMENT';

    public function currentDatabase(string $dsn): array
    {
        return [
            'result' => substr($dsn, strpos($dsn, ':') + 1),
        ];
    }

    public function getTables(string $database): array
    {
        $sql = 'SELECT ' . $this->wrap('name') . ' FROM ' . $this->wrap('sqlite_master')
            . ' WHERE type = ? ORDER BY ' . $this->wrap('name') . ' ASC';

        return [
            'sql' => $sql,
            'params' => ['table'],
        ];
    }

    public function getColumns(string $database, string $table): array
    {
        return [
            'sql' => 'PRAGMA table_info(' . $this->wrap($table) . ')',
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
        return 'INTEGER';
    }

    protected function handleTypeTime(Column $column): string
    {
        return 'DATETIME';
    }

    protected function handleTypeTimestamp(Column $column): string
    {
        return 'DATETIME';
    }

    protected function handleModifierAutoincrement(Column $column): string
    {
        $modifier = parent::handleModifierAutoincrement($column);

        if ($modifier !== '') {
            $this->noPrimaryKey = true;
            $modifier = 'PRIMARY KEY ' . $modifier;
        }

        return $modifier;
    }

    protected function handlePrimaryKey(Blueprint $schema): string
    {
        if ($this->noPrimaryKey) {
            return '';
        }

        return parent::handlePrimaryKey($schema);
    }

    protected function handleEngine(Blueprint $schema): string
    {
        return '';
    }

    protected function handleAddUnique(Blueprint $table, mixed $data): string
    {
        return 'CREATE UNIQUE INDEX ' . $this->wrap($data['name']) . ' ON '
            . $this->wrap($table->getTableName()) . '(' . $this->wrapArray($data['columns']) . ')';
    }

    protected function handleAddIndex(Blueprint $table, mixed $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON '
            . $this->wrap($table->getTableName()) . '(' . $this->wrapArray($data['columns']) . ')';
    }
}
