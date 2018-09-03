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
    Compiler, BaseColumn, AlterTable, CreateTable
};

class PostgreSQL extends Compiler
{
    /** @var string[] */
    protected $modifiers = ['nullable', 'default'];

    /**
     * @inheritdoc
     */
    protected function handleTypeInteger(BaseColumn $column): string
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
     * @inheritdoc
     */
    protected function handleTypeFloat(BaseColumn $column): string
    {
        return 'REAL';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDouble(BaseColumn $column): string
    {
        return 'DOUBLE PRECISION';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDecimal(BaseColumn $column): string
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
     * @inheritdoc
     */
    protected function handleTypeBinary(BaseColumn $column): string
    {
        return 'BYTEA';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTime(BaseColumn $column): string
    {
        return 'TIME(0) WITHOUT TIME ZONE';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeTimestamp(BaseColumn $column): string
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeDateTime(BaseColumn $column): string
    {
        return 'TIMESTAMP(0) WITHOUT TIME ZONE';
    }

    /**
     * @inheritdoc
     */
    protected function handleIndexKeys(CreateTable $schema): array
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

    /**
     * @inheritdoc
     */
    protected function handleRenameColumn(AlterTable $table, $data): string
    {
        /** @var BaseColumn $column */
        $column = $data['column'];
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' RENAME COLUMN '
            . $this->wrap($data['from']) . ' TO ' . $this->wrap($column->getName());
    }

    /**
     * @inheritdoc
     */
    protected function handleAddIndex(AlterTable $table, $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($table->getTableName() . '_' . $data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleDropIndex(AlterTable $table, $data): string
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName() . '_' . $data);
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

    /**
     * @inheritdoc
     */
    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT current_schema()',
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
