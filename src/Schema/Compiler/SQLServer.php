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

class SQLServer extends Compiler
{
    /** @var string */
    protected $wrapper = '[%s]';

    /** @var string[] */
    protected $modifiers = ['nullable', 'default', 'autoincrement'];

    /** @var string */
    protected $autoincrement = 'IDENTITY';

    /**
     * @inheritdoc
     */
    protected function handleTypeInteger(BaseColumn $column): string
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
    protected function handleTypeBoolean(BaseColumn $column): string
    {
        return 'BIT';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeString(BaseColumn $column): string
    {
        return 'NVARCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeFixed(BaseColumn $column): string
    {
        return 'NCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeText(BaseColumn $column): string
    {
        return 'NVARCHAR(max)';
    }

    /**
     * @inheritdoc
     */
    protected function handleTypeBinary(BaseColumn $column): string
    {
        return 'VARBINARY(max)';
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
    protected function handleRenameColumn(AlterTable $table, $data): string
    {
        /** @var BaseColumn $column */
        $column = $data['column'];
        return 'sp_rename ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data['from']) . ', '
            . $this->wrap($column->getName()) . ', COLUMN';
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
    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'sp_rename ' . $this->wrap($current) . ', ' . $this->wrap($new),
            'params' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT SCHEMA_NAME()',
            'params' => [],
        ];
    }

    /**
     * @inheritdoc
     */
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
}
