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

class CreateTable
{
    /** @var CreateColumn[] */
    protected array $columns = [];

    protected ?array $primaryKey = null;
    protected array $uniqueKeys = [];
    protected array $indexes = [];
    protected array $foreignKeys = [];
    protected string $table;
    protected ?string $engine = null;
    protected ?bool $autoincrement = null;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table;
    }

    /**
     * @return CreateColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
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

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function primary(string|array $columns, string $name = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_pk_' . implode('_', $columns);
        }

        $this->primaryKey = [
            'name' => $name,
            'columns' => $columns,
        ];

        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function unique(string|array $columns, string $name = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_uk_' . implode('_', $columns);
        }

        $this->uniqueKeys[$name] = $columns;

        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function index(string|array $columns, string $name = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_ik_' . implode('_', $columns);
        }

        $this->indexes[$name] = $columns;

        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return ForeignKey
     */
    public function foreign(string|array $columns, string $name = null): ForeignKey
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_fk_' . implode('_', $columns);
        }

        return $this->foreignKeys[$name] = new ForeignKey($columns);
    }

    public function autoincrement(CreateColumn $column, string $name = null): static
    {
        if ($column->getType() !== 'integer') {
            return $this;
        }
        $column->set('autoincrement', true);
        $this->autoincrement = true;
        return $this->primary($column->getName(), $name);
    }

    public function integer(string $name): CreateColumn
    {
        return $this->addColumn($name, 'integer');
    }

    public function float(string $name): CreateColumn
    {
        return $this->addColumn($name, 'float');
    }

    public function double(string $name): CreateColumn
    {
        return $this->addColumn($name, 'double');
    }

    public function decimal(string $name, int $length = null, int $precision = null): CreateColumn
    {
        return $this->addColumn($name, 'decimal')->length($length)->set('precision', $precision);
    }

    public function boolean(string $name): CreateColumn
    {
        return $this->addColumn($name, 'boolean');
    }

    public function binary(string $name): CreateColumn
    {
        return $this->addColumn($name, 'binary');
    }

    public function string(string $name, int $length = 255): CreateColumn
    {
        return $this->addColumn($name, 'string')->length($length);
    }

    public function fixed(string $name, int $length = 255): CreateColumn
    {
        return $this->addColumn($name, 'fixed')->length($length);
    }

    public function text(string $name): CreateColumn
    {
        return $this->addColumn($name, 'text');
    }

    public function time(string $name): CreateColumn
    {
        return $this->addColumn($name, 'time');
    }

    public function timestamp(string $name): CreateColumn
    {
        return $this->addColumn($name, 'timestamp');
    }

    public function date(string $name): CreateColumn
    {
        return $this->addColumn($name, 'date');
    }

    public function dateTime(string $name): CreateColumn
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

    protected function addColumn(string $name, string $type): CreateColumn
    {
        $column = new CreateColumn($this, $name, $type);
        $this->columns[$name] = $column;
        return $column;
    }
}
