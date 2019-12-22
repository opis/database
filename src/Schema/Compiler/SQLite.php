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
    AlterTable, Compiler, BaseColumn, CreateTable
};

class SQLite extends Compiler
{
    /** @var string[] */
    protected $modifiers = ['nullable', 'default', 'autoincrement'];

    /** @var string */
    protected $autoincrement = 'AUTOINCREMENT';

    /** @var bool No primary key */
    private $nopk = false;

    /**
     * @inheritdoc
     */
    protected function handleTypeInteger(BaseColumn $column): string
    {
        return 'INTEGER';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTime(BaseColumn $column): string
    {
        return 'DATETIME';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTimestamp(BaseColumn $column): string
    {
        return 'DATETIME';
    }

    /**
     * @inheritdoc
     */
    public function handleModifierAutoincrement(BaseColumn $column): string
    {
        $modifier = parent::handleModifierAutoincrement($column);

        if ($modifier !== '') {
            $this->nopk = true;
            $modifier = 'PRIMARY KEY ' . $modifier;
        }

        return $modifier;
    }

    /**
     * @inheritdoc
     */
    public function handlePrimaryKey(CreateTable $schema): string
    {
        if ($this->nopk) {
            return '';
        }

        return parent::handlePrimaryKey($schema);
    }

    /**
     * @inheritdoc
     */
    protected function handleEngine(CreateTable $schema): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function handleAddUnique(AlterTable $table, $data): string
    {
        return 'CREATE UNIQUE INDEX ' . $this->wrap($data['name']) . ' ON '
            . $this->wrap($table->getTableName()) . '(' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleAddIndex(AlterTable $table, $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON '
            . $this->wrap($table->getTableName()) . '(' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @inheritdoc
     */
    public function currentDatabase(string $dsn): array
    {
        return [
            'result' => substr($dsn, strpos($dsn, ':') + 1),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTables(string $database): array
    {
        $sql = 'SELECT ' . $this->wrap('name') . ' FROM ' . $this->wrap('sqlite_master')
            . ' WHERE type = ? ORDER BY ' . $this->wrap('name') . ' ASC';

        return [
            'sql' => $sql,
            'params' => ['table'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getColumns(string $database, string $table): array
    {
        return [
            'sql' => 'PRAGMA table_info(' . $this->wrap($table) . ')',
            'params' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'ALTER TABLE ' . $this->wrap($current) . ' RENAME TO ' . $this->wrap($new),
            'params' => [],
        ];
    }
}
