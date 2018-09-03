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

namespace Opis\Database\Schema;

use Opis\Database\Connection;

class Compiler
{
    /** @var string */
    protected $separator = ';';

    /** @var string */
    protected $wrapper = '"%s"';

    /** @var array */
    protected $params = [];

    /** @var string[] */
    protected $modifiers = ['unsigned', 'nullable', 'default', 'autoincrement'];

    /** @var string[] */
    protected $serials = ['tiny', 'small', 'normal', 'medium', 'big'];

    /** @var string */
    protected $autoincrement = 'AUTO_INCREMENT';

    /** @var Connection */
    protected $connection;

    /**
     * Compiler constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }


    /**
     * @param string $name
     * @return string
     */
    protected function wrap(string $name): string
    {
        return sprintf($this->wrapper, $name);
    }

    /**
     * @param string[] $value
     * @param string $separator
     * @return string
     */
    protected function wrapArray(array $value, string $separator = ', '): string
    {
        return implode($separator, array_map([$this, 'wrap'], $value));
    }

    /**
     * @param int|float|string|bool|null $value
     *
     * @return float|int|string
     */
    protected function value($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_string($value)) {
            return "'" . str_replace("'", "''", $value) . "'";
        }

        return 'NULL';
    }

    /**
     * @param BaseColumn[] $columns
     * @return string
     */
    protected function handleColumns(array $columns): string
    {
        $sql = [];

        foreach ($columns as $column) {
            $line = $this->wrap($column->getName());
            $line .= $this->handleColumnType($column);
            $line .= $this->handleColumnModifiers($column);
            $sql[] = $line;
        }

        return implode(",\n", $sql);
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleColumnType(BaseColumn $column): string
    {
        $type = 'handleType' . ucfirst($column->getType());
        $result = trim($this->{$type}($column));

        if ($result !== '') {
            $result = ' ' . $result;
        }

        return $result;
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleColumnModifiers(BaseColumn $column): string
    {
        $line = '';

        foreach ($this->modifiers as $modifier) {
            $callback = 'handleModifier' . ucfirst($modifier);
            $result = trim($this->{$callback}($column));

            if ($result !== '') {
                $result = ' ' . $result;
            }

            $line .= $result;
        }

        return $line;
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeInteger(BaseColumn $column): string
    {
        return 'INT';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeFloat(BaseColumn $column): string
    {
        return 'FLOAT';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeDouble(BaseColumn $column): string
    {
        return 'DOUBLE';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeDecimal(BaseColumn $column): string
    {
        return 'DECIMAL';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeBoolean(BaseColumn $column): string
    {
        return 'BOOLEAN';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeBinary(BaseColumn $column): string
    {
        return 'BLOB';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeText(BaseColumn $column): string
    {
        return 'TEXT';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeString(BaseColumn $column): string
    {
        return 'VARCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeFixed(BaseColumn $column): string
    {
        return 'CHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeTime(BaseColumn $column): string
    {
        return 'TIME';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeTimestamp(BaseColumn $column): string
    {
        return 'TIMESTAMP';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeDate(BaseColumn $column): string
    {
        return 'DATE';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleTypeDateTime(BaseColumn $column): string
    {
        return 'DATETIME';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleModifierUnsigned(BaseColumn $column): string
    {
        return $column->get('unsigned', false) ? 'UNSIGNED' : '';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleModifierNullable(BaseColumn $column): string
    {
        if ($column->get('nullable', true)) {
            return '';
        }

        return 'NOT NULL';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleModifierDefault(BaseColumn $column): string
    {
        return null === $column->get('default') ? '' : 'DEFAULT ' . $this->value($column->get('default'));
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    protected function handleModifierAutoincrement(BaseColumn $column): string
    {
        if ($column->getType() !== 'integer' || !in_array($column->get('size', 'normal'), $this->serials)) {
            return '';
        }

        return $column->get('autoincrement', false) ? $this->autoincrement : '';
    }

    /**
     * @param CreateTable $schema
     * @return string
     */
    protected function handlePrimaryKey(CreateTable $schema): string
    {
        if (null === $pk = $schema->getPrimaryKey()) {
            return '';
        }

        return ",\n" . 'CONSTRAINT ' . $this->wrap($pk['name']) . ' PRIMARY KEY (' . $this->wrapArray($pk['columns']) . ')';
    }

    /**
     * @param CreateTable $schema
     * @return string
     */
    protected function handleUniqueKeys(CreateTable $schema): string
    {
        $indexes = $schema->getUniqueKeys();

        if (empty($indexes)) {
            return '';
        }

        $sql = [];

        foreach ($schema->getUniqueKeys() as $name => $columns) {
            $sql[] = 'CONSTRAINT ' . $this->wrap($name) . ' UNIQUE (' . $this->wrapArray($columns) . ')';
        }

        return ",\n" . implode(",\n", $sql);
    }

    /**
     * @param CreateTable $schema
     * @return string[]
     */
    protected function handleIndexKeys(CreateTable $schema): array
    {
        $indexes = $schema->getIndexes();

        if (empty($indexes)) {
            return [];
        }

        $sql = [];
        $table = $this->wrap($schema->getTableName());

        foreach ($indexes as $name => $columns) {
            $sql[] = 'CREATE INDEX ' . $this->wrap($name) . ' ON ' . $table . '(' . $this->wrapArray($columns) . ')';
        }

        return $sql;
    }

    /**
     * @param CreateTable $schema
     * @return string
     */
    protected function handleForeignKeys(CreateTable $schema): string
    {
        /** @var ForeignKey[] $keys */
        $keys = $schema->getForeignKeys();

        if (empty($keys)) {
            return '';
        }

        $sql = [];

        foreach ($keys as $name => $key) {
            $cmd = 'CONSTRAINT ' . $this->wrap($name) . ' FOREIGN KEY (' . $this->wrapArray($key->getColumns()) . ') ';
            $cmd .= 'REFERENCES ' . $this->wrap($key->getReferencedTable()) . ' (' . $this->wrapArray($key->getReferencedColumns()) . ')';

            foreach ($key->getActions() as $actionName => $action) {
                $cmd .= ' ' . $actionName . ' ' . $action;
            }

            $sql[] = $cmd;
        }

        return ",\n" . implode(",\n", $sql);
    }

    /**
     * @param CreateTable $schema
     * @return string
     */
    protected function handleEngine(CreateTable $schema): string
    {
        if (null !== $engine = $schema->getEngine()) {
            return ' ENGINE = ' . strtoupper($engine);
        }

        return '';
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleDropPrimaryKey(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleDropUniqueKey(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleDropIndex(AlterTable $table, $data): string
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleDropForeignKey(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleDropColumn(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP COLUMN ' . $this->wrap($data);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleRenameColumn(AlterTable $table, $data): string
    {
        return '';
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleModifyColumn(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' MODIFY COLUMN ' . $this->handleColumns([$data]);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleAddColumn(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD COLUMN ' . $this->handleColumns([$data]);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleAddPrimary(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' PRIMARY KEY (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleAddUnique(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' UNIQUE (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleAddIndex(AlterTable $table, $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleAddForeign(AlterTable $table, $data): string
    {
        /** @var ForeignKey $key */
        $key = $data['foreign'];
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' FOREIGN KEY (' . $this->wrapArray($key->getColumns()) . ') '
            . 'REFERENCES ' . $this->wrap($key->getReferencedTable()) . '(' . $this->wrapArray($key->getReferencedColumns()) . ')';
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleSetDefaultValue(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER COLUMN '
            . $this->wrap($data['column']) . ' SET DEFAULT ' . $this->value($data['value']);
    }

    /**
     * @param AlterTable $table
     * @param $data
     * @return string
     */
    protected function handleDropDefaultValue(AlterTable $table, $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER COLUMN '
            . $this->wrap($data) . ' DROP DEFAULT';
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        $params = $this->params;
        $this->params = [];
        return $params;
    }

    /**
     * @param string $dsn
     * @return array
     */
    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT database()',
            'params' => [],
        ];
    }

    /**
     * @param string $current
     * @param string $new
     * @return array
     */
    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'RENAME TABLE ' . $this->wrap($current) . ' TO ' . $this->wrap($new),
            'params' => [],
        ];
    }

    /**
     * @param string $database
     * @return array
     */
    public function getTables(string $database): array
    {
        $sql = 'SELECT ' . $this->wrap('table_name') . ' FROM ' . $this->wrap('information_schema')
            . '.' . $this->wrap('tables') . ' WHERE table_type = ? AND table_schema = ? ORDER BY '
            . $this->wrap('table_name') . ' ASC';

        return [
            'sql' => $sql,
            'params' => ['BASE TABLE', $database],
        ];
    }

    /**
     * @param string $database
     * @param string $table
     * @return array
     */
    public function getColumns(string $database, string $table): array
    {
        $sql = 'SELECT ' . $this->wrap('column_name') . ' AS ' . $this->wrap('name')
            . ', ' . $this->wrap('column_type') . ' AS ' . $this->wrap('type')
            . ' FROM ' . $this->wrap('information_schema') . '.' . $this->wrap('columns')
            . ' WHERE ' . $this->wrap('table_schema') . ' = ? AND ' . $this->wrap('table_name') . ' = ? '
            . ' ORDER BY ' . $this->wrap('ordinal_position') . ' ASC';

        return [
            'sql' => $sql,
            'params' => [$database, $table],
        ];
    }

    /**
     * @param CreateTable $schema
     * @return array
     */
    public function create(CreateTable $schema): array
    {
        $sql = 'CREATE TABLE ' . $this->wrap($schema->getTableName());
        $sql .= "(\n";
        $sql .= $this->handleColumns($schema->getColumns());
        $sql .= $this->handlePrimaryKey($schema);
        $sql .= $this->handleUniqueKeys($schema);
        $sql .= $this->handleForeignKeys($schema);
        $sql .= "\n)" . $this->handleEngine($schema);

        $commands = [];

        $commands[] = [
            'sql' => $sql,
            'params' => $this->getParams(),
        ];

        foreach ($this->handleIndexKeys($schema) as $index) {
            $commands[] = [
                'sql' => $index,
                'params' => [],
            ];
        }

        return $commands;
    }

    /**
     * @param AlterTable $schema
     * @return array
     */
    public function alter(AlterTable $schema): array
    {
        $commands = [];

        foreach ($schema->getCommands() as $command) {
            $type = 'handle' . ucfirst($command['type']);
            $sql = $this->{$type}($schema, $command['data']);

            if ($sql === '') {
                continue;
            }

            $commands[] = [
                'sql' => $sql,
                'params' => $this->getParams(),
            ];
        }

        return $commands;
    }

    /**
     * @param string $table
     * @return array
     */
    public function drop(string $table): array
    {
        return [
            'sql' => 'DROP TABLE ' . $this->wrap($table),
            'params' => [],
        ];
    }

    /**
     * @param string $table
     * @return array
     */
    public function truncate(string $table): array
    {
        return [
            'sql' => 'TRUNCATE TABLE ' . $this->wrap($table),
            'params' => [],
        ];
    }
}
