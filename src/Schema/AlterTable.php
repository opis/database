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
    /** @var string */
    protected $table;

    /** @var array */
    protected $commands = [];

    /**
     * AlterTable constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param string $name
     * @param $data
     * @return $this
     */
    protected function addCommand(string $name, $data): self
    {
        $this->commands[] = [
            'type' => $name,
            'data' => $data,
        ];

        return $this;
    }

    /**
     * @param string $type
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    protected function addKey(string $type, $columns, string $name = null): self
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

    /**
     * @param string $name
     * @param string $type
     * @return AlterColumn
     */
    protected function addColumn(string $name, string $type): AlterColumn
    {
        $columnObject = new AlterColumn($this, $name, $type);
        $this->addCommand('addColumn', $columnObject);
        return $columnObject;
    }

    /**
     * @param string $column
     * @param string $type
     * @return AlterColumn
     */
    protected function modifyColumn(string $column, string $type): AlterColumn
    {
        $columnObject = new AlterColumn($this, $column, $type);
        $columnObject->set('handleDefault', false);
        $this->addCommand('modifyColumn', $columnObject);
        return $columnObject;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function dropIndex(string $name): self
    {
        return $this->addCommand('dropIndex', $name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function dropUnique(string $name): self
    {
        return $this->addCommand('dropUniqueKey', $name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function dropPrimary(string $name): self
    {
        return $this->addCommand('dropPrimaryKey', $name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function dropForeign(string $name): self
    {
        return $this->addCommand('dropForeignKey', $name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function dropColumn(string $name): self
    {
        return $this->addCommand('dropColumn', $name);
    }

    /**
     * @param string $column
     * @return $this
     */
    public function dropDefaultValue(string $column): self
    {
        return $this->addCommand('dropDefaultValue', $column);
    }

    /**
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function renameColumn(string $from, string $to): self
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
    public function primary($columns, string $name = null): self
    {
        return $this->addKey('addPrimary', $columns, $name);
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function unique($columns, string $name = null): self
    {
        return $this->addKey('addUnique', $columns, $name);
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function index($columns, string $name = null): self
    {
        return $this->addKey('addIndex', $columns, $name);
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

        $foreign = new ForeignKey($columns);

        $this->addCommand('addForeign', [
            'name' => $name,
            'foreign' => $foreign,
        ]);

        return $foreign;
    }

    /**
     * @param string $column
     * @param $value
     * @return $this
     */
    public function setDefaultValue(string $column, $value): self
    {
        return $this->addCommand('setDefaultValue', [
            'column' => $column,
            'value' => $value,
        ]);
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function integer(string $name): AlterColumn
    {
        return $this->addColumn($name, 'integer');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function float(string $name): AlterColumn
    {
        return $this->addColumn($name, 'float');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function double(string $name): AlterColumn
    {
        return $this->addColumn($name, 'double');
    }

    /**
     * @param string $name
     * @param int|null $length
     * @param int|null $precision
     * @return AlterColumn
     */
    public function decimal(string $name, int $length = null, int $precision = null): AlterColumn
    {
        return $this->addColumn($name, 'decimal')
            ->set('length', $length)
            ->set('precision', $precision);
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function boolean(string $name): AlterColumn
    {
        return $this->addColumn($name, 'boolean');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function binary(string $name): AlterColumn
    {
        return $this->addColumn($name, 'binary');
    }

    /**
     * @param string $name
     * @param int $length
     * @return AlterColumn
     */
    public function string(string $name, int $length = 255): AlterColumn
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }

    /**
     * @param string $name
     * @param int $length
     * @return AlterColumn
     */
    public function fixed(string $name, int $length = 255): AlterColumn
    {
        return $this->addColumn($name, 'fixed')->set('length', $length);
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function text(string $name): AlterColumn
    {
        return $this->addColumn($name, 'text');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function time(string $name): AlterColumn
    {
        return $this->addColumn($name, 'time');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function timestamp(string $name): AlterColumn
    {
        return $this->addColumn($name, 'timestamp');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function date(string $name): AlterColumn
    {
        return $this->addColumn($name, 'date');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function dateTime(string $name): AlterColumn
    {
        return $this->addColumn($name, 'dateTime');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toInteger(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'integer');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toFloat(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'float');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toDouble(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'double');
    }


    /**
     * @param string $name
     * @param int|null $length
     * @param int|null $precision
     * @return AlterColumn
     */
    public function toDecimal(string $name, int $length = null, int $precision = null): AlterColumn
    {
        return $this->modifyColumn($name, 'decimal')
            ->set('length', $length)
            ->set('precision', $precision);
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toBoolean(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'boolean');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toBinary(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'binary');
    }

    /**
     * @param $name
     * @param int $length
     * @return AlterColumn
     */
    public function toString(string $name, int $length = 255): AlterColumn
    {
        return $this->modifyColumn($name, 'string')->set('length', $length);
    }

    /**
     * @param string $name
     * @param int $length
     * @return AlterColumn
     */
    public function toFixed(string $name, int $length = 255): AlterColumn
    {
        return $this->modifyColumn($name, 'fixed')->set('length', $length);
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toText(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'text');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toTime(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'time');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toTimestamp(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'timestamp');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toDate(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'date');
    }

    /**
     * @param string $name
     * @return AlterColumn
     */
    public function toDateTime(string $name): AlterColumn
    {
        return $this->modifyColumn($name, 'dateTime');
    }
}
