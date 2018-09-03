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
    protected $columns = [];

    /** @var string|string[] */
    protected $primaryKey;

    /** @var string[] */
    protected $uniqueKeys = [];

    /** @var array */
    protected $indexes = [];

    /** @var array */
    protected $foreignKeys = [];

    /** @var string */
    protected $table;

    /** @var string|null */
    protected $engine;

    /** @var bool|null */
    protected $autoincrement;

    /**
     * CreateTable constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param string $name
     * @param string $type
     * @return CreateColumn
     */
    protected function addColumn(string $name, string $type): CreateColumn
    {
        $column = new CreateColumn($this, $name, $type);
        $this->columns[$name] = $column;
        return $column;
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

    /**
     * @return  mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return  array
     */
    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }

    /**
     * @return  array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return  array
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * @return  mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @return  mixed
     */
    public function getAutoincrement()
    {
        return $this->autoincrement;
    }

    /***
     * @param string $name
     * @return $this
     */
    public function engine(string $name): self
    {
        $this->engine = $name;
        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function primary($columns, string $name = null): self
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
    public function unique($columns, string $name = null): self
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
    public function index($columns, string $name = null)
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
    public function foreign($columns, string $name = null): ForeignKey
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_fk_' . implode('_', $columns);
        }

        return $this->foreignKeys[$name] = new ForeignKey($columns);
    }

    /**
     * @param CreateColumn $column
     * @param string|null $name
     * @return $this
     */
    public function autoincrement(CreateColumn $column, string $name = null): self
    {
        if ($column->getType() !== 'integer') {
            return $this;
        }

        $this->autoincrement = $column->set('autoincrement', true);
        return $this->primary($column->getName(), $name);
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function integer(string $name): CreateColumn
    {
        return $this->addColumn($name, 'integer');
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function float(string $name): CreateColumn
    {
        return $this->addColumn($name, 'float');
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function double(string $name): CreateColumn
    {
        return $this->addColumn($name, 'double');
    }

    /**
     * @param string $name
     * @param int|null $length
     * @param int|null $precision
     * @return CreateColumn
     */
    public function decimal(string $name, int $length = null, int $precision = null): CreateColumn
    {
        return $this->addColumn($name, 'decimal')->length($length)->set('precision', $precision);
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function boolean(string $name): CreateColumn
    {
        return $this->addColumn($name, 'boolean');
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function binary(string $name): CreateColumn
    {
        return $this->addColumn($name, 'binary');
    }

    /**
     * @param string $name
     * @param int $length
     * @return CreateColumn
     */
    public function string(string $name, int $length = 255): CreateColumn
    {
        return $this->addColumn($name, 'string')->length($length);
    }

    /**
     * @param string $name
     * @param int $length
     * @return CreateColumn
     */
    public function fixed(string $name, int $length = 255): CreateColumn
    {
        return $this->addColumn($name, 'fixed')->length($length);
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function text(string $name): CreateColumn
    {
        return $this->addColumn($name, 'text');
    }

    /***
     * @param string $name
     * @return CreateColumn
     */
    public function time(string $name): CreateColumn
    {
        return $this->addColumn($name, 'time');
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function timestamp(string $name): CreateColumn
    {
        return $this->addColumn($name, 'timestamp');
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function date(string $name): CreateColumn
    {
        return $this->addColumn($name, 'date');
    }

    /**
     * @param string $name
     * @return CreateColumn
     */
    public function dateTime(string $name): CreateColumn
    {
        return $this->addColumn($name, 'dateTime');
    }

    /**
     * @param string $column
     * @return $this
     */
    public function softDelete(string $column = 'deleted_at'): self
    {
        $this->dateTime($column);
        return $this;
    }

    /**
     * @param string $createColumn
     * @param string $updateColumn
     * @return $this
     */
    public function timestamps(string $createColumn = 'created_at', string $updateColumn = 'updated_at'): self
    {
        $this->dateTime($createColumn)->notNull();
        $this->dateTime($updateColumn);
        return $this;
    }
}
