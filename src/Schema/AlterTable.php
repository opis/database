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

class AlterTable
{
    protected string $table;
    protected array $commands = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function dropIndex(string $name): static
    {
        return $this->addCommand('dropIndex', $name);
    }

    public function dropUnique(string $name): static
    {
        return $this->addCommand('dropUniqueKey', $name);
    }

    public function dropPrimary(string $name): static
    {
        return $this->addCommand('dropPrimaryKey', $name);
    }

    public function dropForeign(string $name): static
    {
        return $this->addCommand('dropForeignKey', $name);
    }

    public function dropColumn(string $name): static
    {
        return $this->addCommand('dropColumn', $name);
    }

    public function dropDefaultValue(string $column): static
    {
        return $this->addCommand('dropDefaultValue', $column);
    }

    public function renameColumn(string $from, string $to): static
    {
        return $this->addCommand('renameColumn', [
            'from' => $from,
            'column' => new AlterColumn($this, $to),
        ]);
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function primary(string|array $columns, string $name = null): static
    {
        return $this->addKey('addPrimary', $columns, $name);
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function unique(string|array $columns, string $name = null): static
    {
        return $this->addKey('addUnique', $columns, $name);
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function index(string|array $columns, string $name = null): static
    {
        return $this->addKey('addIndex', $columns, $name);
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

        $foreign = new ForeignKey($columns);

        $this->addCommand('addForeign', [
            'name' => $name,
            'foreign' => $foreign,
        ]);

        return $foreign;
    }

    public function setDefaultValue(string $column, mixed $value): static
    {
        return $this->addCommand('setDefaultValue', [
            'column' => $column,
            'value' => $value,
        ]);
    }

    public function integer(string $name): AlterColumn
    {
        return $this->addColumn($name, 'integer');
    }

    public function float(string $name): AlterColumn
    {
        return $this->addColumn($name, 'float');
    }

    public function double(string $name): AlterColumn
    {
        return $this->addColumn($name, 'double');
    }

    public function decimal(string $name, int $length = null, int $precision = null): AlterColumn
    {
        return $this->addColumn($name, 'decimal')
            ->set('length', $length)
            ->set('precision', $precision);
    }

    public function boolean(string $name): AlterColumn
    {
        return $this->addColumn($name, 'boolean');
    }

    public function binary(string $name): AlterColumn
    {
        return $this->addColumn($name, 'binary');
    }

    public function string(string $name, int $length = 255): AlterColumn
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }

    public function fixed(string $name, int $length = 255): AlterColumn
    {
        return $this->addColumn($name, 'fixed')->set('length', $length);
    }

    public function text(string $name): AlterColumn
    {
        return $this->addColumn($name, 'text');
    }

    public function time(string $name): AlterColumn
    {
        return $this->addColumn($name, 'time');
    }

    public function timestamp(string $name): AlterColumn
    {
        return $this->addColumn($name, 'timestamp');
    }

    public function date(string $name): AlterColumn
    {
        return $this->addColumn($name, 'date');
    }

    public function dateTime(string $name): AlterColumn
    {
        return $this->addColumn($name, 'dateTime');
    }

    public function toInteger(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'integer');
    }

    public function toFloat(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'float');
    }

    public function toDouble(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'double');
    }

    public function toDecimal(string $name, int $length = null, int $precision = null): AlterColumn
    {
        return $this->modifyColumn($name, 'decimal')
            ->set('length', $length)
            ->set('precision', $precision);
    }

    public function toBoolean(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'boolean');
    }

    public function toBinary(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'binary');
    }

    public function toString(string $name, int $length = 255): AlterColumn
    {
        return $this->modifyColumn($name, 'string')->set('length', $length);
    }

    public function toFixed(string $name, int $length = 255): AlterColumn
    {
        return $this->modifyColumn($name, 'fixed')->set('length', $length);
    }

    public function toText(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'text');
    }

    public function toTime(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'time');
    }

    public function toTimestamp(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'timestamp');
    }

    public function toDate(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'date');
    }

    public function toDateTime(string $name): AlterColumn
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

    protected function addKey(string $type, array $columns, string $name = null): static
    {
        static $map = [
            'addPrimary' => 'pk',
            'addUnique' => 'uk',
            'addForeignKey' => 'fk',
            'addIndex' => 'ik',
        ];

        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_' . $map[$type] . '_' . implode('_', $columns);
        }

        return $this->addCommand($type, [
            'name' => $name,
            'columns' => $columns,
        ]);
    }

    protected function addColumn(string $name, string $type): AlterColumn
    {
        $columnObject = new AlterColumn($this, $name, $type);
        $this->addCommand('addColumn', $columnObject);
        return $columnObject;
    }

    protected function modifyColumn(string $column, string $type): AlterColumn
    {
        $columnObject = new AlterColumn($this, $column, $type);
        $columnObject->set('handleDefault', false);
        $this->addCommand('modifyColumn', $columnObject);
        return $columnObject;
    }
}
