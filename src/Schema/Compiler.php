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
    protected string $separator = ';';
    protected string $wrapper = '"%s"';
    protected array $params = [];

    /** @var string[] */
    protected array $modifiers = ['unsigned', 'nullable', 'default', 'autoincrement'];

    /** @var string[] */
    protected array $serials = ['tiny', 'small', 'normal', 'medium', 'big'];

    protected string $autoincrement = 'AUTO_INCREMENT';
    protected Connection $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getParams(): array
    {
        $params = $this->params;
        $this->params = [];
        return $params;
    }

    public function currentDatabase(string $dsn): array
    {
        return [
            'sql' => 'SELECT database()',
            'params' => [],
        ];
    }

    public function renameTable(string $current, string $new): array
    {
        return [
            'sql' => 'RENAME TABLE ' . $this->wrap($current) . ' TO ' . $this->wrap($new),
            'params' => [],
        ];
    }

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

    public function create(Blueprint $schema): array
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

    public function alter(Blueprint $schema): array
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

    public function drop(string $table): array
    {
        return [
            'sql' => 'DROP TABLE ' . $this->wrap($table),
            'params' => [],
        ];
    }

    public function truncate(string $table): array
    {
        return [
            'sql' => 'TRUNCATE TABLE ' . $this->wrap($table),
            'params' => [],
        ];
    }

    public function setOptions(array $options): static
    {
        foreach ($options as $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }


    protected function wrap(string $name): string
    {
        return sprintf($this->wrapper, $name);
    }

    protected function wrapArray(array $value, string $separator = ', '): string
    {
        return implode($separator, array_map([$this, 'wrap'], $value));
    }

    protected function value(mixed $value): mixed
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
     * @param Column[] $columns
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

    protected function handleColumnType(Column $column): string
    {
        $type = 'handleType' . ucfirst($column->getType());
        $result = trim($this->{$type}($column));

        if ($result !== '') {
            $result = ' ' . $result;
        }

        return $result;
    }

    protected function handleColumnModifiers(Column $column): string
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

    protected function handleTypeInteger(Column $column): string
    {
        return 'INT';
    }

    protected function handleTypeFloat(Column $column): string
    {
        return 'FLOAT';
    }

    protected function handleTypeDouble(Column $column): string
    {
        return 'DOUBLE';
    }

    protected function handleTypeDecimal(Column $column): string
    {
        return 'DECIMAL';
    }

    protected function handleTypeBoolean(Column $column): string
    {
        return 'BOOLEAN';
    }

    protected function handleTypeBinary(Column $column): string
    {
        return 'BLOB';
    }

    protected function handleTypeText(Column $column): string
    {
        return 'TEXT';
    }

    protected function handleTypeString(Column $column): string
    {
        return 'VARCHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    protected function handleTypeFixed(Column $column): string
    {
        return 'CHAR(' . $this->value($column->get('length', 255)) . ')';
    }

    protected function handleTypeTime(Column $column): string
    {
        return 'TIME';
    }

    protected function handleTypeTimestamp(Column $column): string
    {
        return 'TIMESTAMP';
    }

    protected function handleTypeDate(Column $column): string
    {
        return 'DATE';
    }

    protected function handleTypeDateTime(Column $column): string
    {
        return 'DATETIME';
    }

    protected function handleModifierUnsigned(Column $column): string
    {
        return $column->get('unsigned', false) ? 'UNSIGNED' : '';
    }

    protected function handleModifierNullable(Column $column): string
    {
        if ($column->get('nullable', true)) {
            return '';
        }

        return 'NOT NULL';
    }

    protected function handleModifierDefault(Column $column): string
    {
        return null === $column->get('default') ? '' : 'DEFAULT ' . $this->value($column->get('default'));
    }

    protected function handleModifierAutoincrement(Column $column): string
    {
        if ($column->getType() !== 'integer' || !in_array($column->get('size', 'normal'), $this->serials)) {
            return '';
        }

        return $column->get('autoincrement', false) ? $this->autoincrement : '';
    }

    protected function handlePrimaryKey(Blueprint $schema): string
    {
        if (null === $pk = $schema->getPrimaryKey()) {
            return '';
        }

        return ",\n" . 'CONSTRAINT ' . $this->wrap($pk['name']) . ' PRIMARY KEY (' . $this->wrapArray($pk['columns']) . ')';
    }

    protected function handleUniqueKeys(Blueprint $schema): string
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

    protected function handleIndexKeys(Blueprint $schema): array
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

    protected function handleForeignKeys(Blueprint $schema): string
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

    protected function handleEngine(Blueprint $schema): string
    {
        if (null !== $engine = $schema->getEngine()) {
            return ' ENGINE = ' . strtoupper($engine);
        }

        return '';
    }

    protected function handleDropPrimaryKey(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    protected function handleDropUniqueKey(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    protected function handleDropIndex(Blueprint $table, mixed $data): string
    {
        return 'DROP INDEX ' . $this->wrap($table->getTableName()) . '.' . $this->wrap($data);
    }

    protected function handleDropForeignKey(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP CONSTRAINT ' . $this->wrap($data);
    }

    protected function handleDropColumn(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' DROP COLUMN ' . $this->wrap($data);
    }

    protected function handleRenameColumn(Blueprint $table, mixed $data): string
    {
        return '';
    }

    protected function handleModifyColumn(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' MODIFY COLUMN ' . $this->handleColumns([$data]);
    }

    protected function handleAddColumn(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD COLUMN ' . $this->handleColumns([$data]);
    }

    protected function handleAddPrimary(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' PRIMARY KEY (' . $this->wrapArray($data['columns']) . ')';
    }

    protected function handleAddUnique(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' UNIQUE (' . $this->wrapArray($data['columns']) . ')';
    }

    protected function handleAddIndex(Blueprint $table, mixed $data): string
    {
        return 'CREATE INDEX ' . $this->wrap($data['name']) . ' ON ' . $this->wrap($table->getTableName()) . ' (' . $this->wrapArray($data['columns']) . ')';
    }

    protected function handleAddForeign(Blueprint $table, mixed $data): string
    {
        /** @var ForeignKey $key */
        $key = $data['foreign'];
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ADD CONSTRAINT '
            . $this->wrap($data['name']) . ' FOREIGN KEY (' . $this->wrapArray($key->getColumns()) . ') '
            . 'REFERENCES ' . $this->wrap($key->getReferencedTable()) . '(' . $this->wrapArray($key->getReferencedColumns()) . ')';
    }

    protected function handleSetDefaultValue(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER COLUMN '
            . $this->wrap($data['column']) . ' SET DEFAULT ' . $this->value($data['value']);
    }

    protected function handleDropDefaultValue(Blueprint $table, mixed $data): string
    {
        return 'ALTER TABLE ' . $this->wrap($table->getTableName()) . ' ALTER COLUMN '
            . $this->wrap($data) . ' DROP DEFAULT';
    }
}
