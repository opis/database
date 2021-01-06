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

namespace Opis\Database\Schema;

class Blueprint
{
    /** @var Column[] */
    protected array $columns = [];
    protected array $commands = [];
    protected ?array $primaryKey = null;
    protected array $uniqueKeys = [];
    protected array $indexes = [];
    protected array $foreignKeys = [];
    protected string $table;
    protected ?string $engine = null;
    protected ?bool $autoincrement = null;
    protected bool $alter;

    public function __construct(string $table, bool $alter = false)
    {
        $this->table = $table;
        $this->alter = $alter;
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function alterContext(): bool
    {
        return $this->alter;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getPrimaryKey(): ?array
    {
        return $this->primaryKey;
    }

    public function getUniqueKeys(): array
    {
        return $this->uniqueKeys;
    }

    public function getIndexes(): array
    {
        return $this->indexes;
    }

    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    public function getEngine(): ?string
    {
        return $this->engine;
    }

    public function getAutoincrement(): ?bool
    {
        return $this->autoincrement;
    }

    public function engine(string $name): static
    {
        $this->engine = $name;
        return $this;
    }

    public function primary(string|array $columns, ?string $name = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_pk_' . implode('_', $columns);
        }

        if ($this->alter) {
            return $this->addCommand('addPrimary', [
                'name' => $name,
                'columns' => $columns,
            ]);
        } else {
            $this->primaryKey = [
                'name' => $name,
                'columns' => $columns,
            ];
        }

        return $this;
    }

    public function unique(string|array $columns, ?string $name = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_uk_' . implode('_', $columns);
        }

        if ($this->alter) {
            return $this->addCommand('addUnique', [
                'name' => $name,
                'columns' => $columns,
            ]);
        } else {
            $this->uniqueKeys[$name] = $columns;
        }

        return $this;
    }

    public function index(string|array $columns, ?string $name = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_ik_' . implode('_', $columns);
        }

        if ($this->alter) {
            return $this->addCommand('addIndex', [
                'name' => $name,
                'columns' => $columns,
            ]);
        } else {
            $this->indexes[$name] = $columns;
        }


        return $this;
    }

    public function foreign(string|array $columns, ?string $name = null): ForeignKey
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_fk_' . implode('_', $columns);
        }

        $foreign = new ForeignKey($columns);

        if ($this->alter) {
            $this->addCommand('addForeign', [
                'name' => $name,
                'foreign' => $foreign,
            ]);
        } else {
            $this->foreignKeys[$name] = $foreign;
        }

        return $foreign;
    }

    public function autoincrement(Column $column, ?string $name = null): static
    {
        if ($column->getType() !== 'integer') {
            return $this;
        }

        $column->set('autoincrement', true);

        if ($this->alter) {
            return $this;
        }

        $this->autoincrement = true;
        return $this->primary($column->getName(), $name);
    }

    public function setDefaultValue(string $column, mixed $value): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('setDefaultValue', [
            'column' => $column,
            'value' => $value,
        ]);
    }

    public function renameColumn(string $from, string $to): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('renameColumn', [
            'from' => $from,
            'column' => new Column($this, $to),
        ]);
    }

    public function dropIndex(string $name): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('dropIndex', $name);
    }

    public function dropUnique(string $name): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('dropUniqueKey', $name);
    }

    public function dropPrimary(string $name): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('dropPrimaryKey', $name);
    }

    public function dropForeign(string $name): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('dropForeignKey', $name);
    }

    public function dropColumn(string $name): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('dropColumn', $name);
    }

    public function dropDefaultValue(string $column): static
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        return $this->addCommand('dropDefaultValue', $column);
    }

    public function integer(string $name): Column
    {
        return $this->addColumn($name, 'integer');
    }

    public function float(string $name): Column
    {
        return $this->addColumn($name, 'float');
    }

    public function double(string $name): Column
    {
        return $this->addColumn($name, 'double');
    }

    public function decimal(string $name, ?int $length = null, ?int $precision = null): Column
    {
        return $this->addColumn($name, 'decimal')
            ->set('length', $length)
            ->set('precision', $precision);
    }

    public function boolean(string $name): Column
    {
        return $this->addColumn($name, 'boolean');
    }

    public function binary(string $name): Column
    {
        return $this->addColumn($name, 'binary');
    }

    public function string(string $name, int $length = 255): Column
    {
        return $this->addColumn($name, 'string')->length($length);
    }

    public function fixed(string $name, int $length = 255): Column
    {
        return $this->addColumn($name, 'fixed')->length($length);
    }

    public function text(string $name): Column
    {
        return $this->addColumn($name, 'text');
    }

    public function json(string $name): Column
    {
        return $this->addColumn($name, 'json');
    }

    public function time(string $name): Column
    {
        return $this->addColumn($name, 'time');
    }

    public function timestamp(string $name): Column
    {
        return $this->addColumn($name, 'timestamp');
    }

    public function date(string $name): Column
    {
        return $this->addColumn($name, 'date');
    }

    public function dateTime(string $name): Column
    {
        return $this->addColumn($name, 'dateTime');
    }

    public function softDelete(string $column = 'deleted_at'): static
    {
        $this->dateTime($column);
        return $this;
    }

    public function timestamps(string $createColumn = 'created_at', string $updateColumn = 'updated_at'): static
    {
        $this->dateTime($createColumn)->notNull();
        $this->dateTime($updateColumn);
        return $this;
    }

    public function toInteger(string $name): Column
    {
        return $this->modifyColumn($name, 'integer');
    }

    public function toFloat(string $name): Column
    {
        return $this->modifyColumn($name, 'float');
    }

    public function toDouble(string $name): Column
    {
        return $this->modifyColumn($name, 'double');
    }

    public function toDecimal(string $name, ?int $length = null, ?int $precision = null): Column
    {
        return $this->modifyColumn($name, 'decimal')
            ->set('length', $length)
            ->set('precision', $precision);
    }

    public function toBoolean(string $name): Column
    {
        return $this->modifyColumn($name, 'boolean');
    }

    public function toBinary(string $name): Column
    {
        return $this->modifyColumn($name, 'binary');
    }

    public function toString(string $name, int $length = 255): Column
    {
        return $this->modifyColumn($name, 'string')->set('length', $length);
    }

    public function toFixed(string $name, int $length = 255): Column
    {
        return $this->modifyColumn($name, 'fixed')->set('length', $length);
    }

    public function toText(string $name): Column
    {
        return $this->modifyColumn($name, 'text');
    }

    public function toJson(string $name): Column
    {
        return $this->modifyColumn($name, 'json');
    }

    public function toTime(string $name): Column
    {
        return $this->modifyColumn($name, 'time');
    }

    public function toTimestamp(string $name): Column
    {
        return $this->modifyColumn($name, 'timestamp');
    }

    public function toDate(string $name): Column
    {
        return $this->modifyColumn($name, 'date');
    }

    public function toDateTime(string $name): Column
    {
        return $this->modifyColumn($name, 'dateTime');
    }

    protected function addCommand(string $name, mixed $data): static
    {
        $this->commands[] = [
            'type' => $name,
            'data' => $data,
        ];

        return $this;
    }

    protected function modifyColumn(string $column, string $type): Column
    {
        if (!$this->alter) {
            throw new InvalidOperation();
        }

        $columnObject = new Column($this, $column, $type);
        $columnObject->set('handleDefault', false);
        $this->addCommand('modifyColumn', $columnObject);
        return $columnObject;
    }

    protected function addColumn(string $name, string $type): Column
    {
        $column = new Column($this, $name, $type);

        if ($this->alter) {
            $this->addCommand('addColumn', $column);
        } else {
            $this->columns[$name] = $column;
        }

        return $column;
    }
}
