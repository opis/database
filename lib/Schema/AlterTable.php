<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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
    /** @var    string */
    protected $table;

    /** @var    array */
    protected $commands = array();

    /**
     * Constructor
     * 
     * @param   string  $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @param   string  $name
     * @param   mixed   $data
     * 
     * @return  $this
     */
    protected function addCommand($name, $data)
    {
        $this->commands[] = array(
            'type' => $name,
            'data' => $data,
        );

        return $this;
    }

    /**
     * @param   string  $type
     * @param   string  $name
     * @param   mixed   $columns
     * 
     * @return  $this
     */
    protected function addKey($type, $name, $columns)
    {
        if ($columns === null) {
            $columns = array($name);
        } elseif (!is_array($columns)) {
            $columns = array($columns);
        }

        return $this->addCommand($type, array(
                'name' => $name,
                'columns' => $columns,
        ));
    }

    /**
     * @param   string  $name
     * @param   string  $type
     * 
     * @return  AlterColumn
     */
    protected function addColumn($name, $type)
    {
        $columnObject = new AlterColumn($this, $name, $type);
        $this->addCommand('addColumn', $columnObject);
        return $columnObject;
    }

    /**
     * @param   string  $column
     * @param   string  $type
     * 
     * @return  AlterColumn
     */
    protected function modifyColumn($column, $type)
    {
        $columnObject = new AlterColumn($this, $column, $type);
        $columnObject->set('handleDefault', false);
        $this->addCommand('modifyColumn', $columnObject);
        return $columnObject;
    }

    /**
     * @return  string
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param   string  $name
     * 
     * @return  $this
     */
    public function dropIndex($name)
    {
        return $this->addCommand('dropIndex', $name);
    }

    /**
     * @param   string  $name
     * 
     * @return  $this
     */
    public function dropUnique($name)
    {
        return $this->addCommand('dropUniqueKey', $name);
    }

    /**
     * @param   string  $name
     * 
     * @return  $this
     */
    public function dropPrimary($name)
    {
        return $this->addCommand('dropPrimaryKey', $name);
    }

    /**
     * @param   string  $name
     * 
     * @return  $this
     */
    public function dropForeign($name)
    {
        return $this->addCommand('dropForeignKey', $name);
    }

    /**
     * @param   string  $name
     * 
     * @return  $this
     */
    public function dropColumn($name)
    {
        return $this->addCommand('dropColumn', $name);
    }

    /**
     * @param   string  $name
     * 
     * @return  $this
     */
    public function dropDefaultValue($column)
    {
        return $this->addCommand('dropDefaultValue', $column);
    }

    /**
     * @param   string  $from
     * @param   string  $to
     * 
     * @return  $this
     */
    public function renameColumn($from, $to)
    {
        return $this->addCommand('renameColumn', array(
                'from' => $from,
                'column' => new AlterColumn($this, $to),
        ));
    }

    /**
     * @param   string          $name
     * @param   string|array    $columns    (optional)
     * 
     * @return  $this
     */
    public function primary($name, $columns = null)
    {
        return $this->addKey('addPrimary', $name, $columns);
    }

    /**
     * @param   string          $name
     * @param   string|array    $columns    (optional)
     * 
     * @return  $this
     */
    public function unique($name, $columns = null)
    {
        return $this->addKey('addUnique', $name, $columns);
    }

    /**
     * @param   string          $name
     * @param   string|array    $columns    (optional)
     * 
     * @return  $this
     */
    public function index($name, $columns = null)
    {
        return $this->addKey('addIndex', $name, $columns);
    }

    /**
     * @param   string          $name
     * @param   string|array    $columns    (optional)
     * 
     * @return  $this
     */
    public function foreign($name, $columns = null)
    {
        if ($columns === null) {
            $columns = array($name);
        } elseif (!is_array($columns)) {
            $columns = array($columns);
        }

        $foreign = new ForeignKey($columns);
        $this->addCommand('addForeign', array(
            'name' => $name,
            'foreign' => $foreign,
        ));

        return $foreign;
    }

    /**
     * @param   string  $name
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function setDefaultValue($column, $value)
    {
        return $this->addCommand('setDefaultValue', array(
                'column' => $column,
                'value' => $value,
        ));
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function integer($name)
    {
        return $this->addColumn($name, 'integer');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function float($name)
    {
        return $this->addColumn($name, 'float');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function double($name)
    {
        return $this->addColumn($name, 'double');
    }

    /**
     * @param   string      $name
     * @param   int|null    $maximum (optional)
     * @param   int|null    $decimal (optional)
     * 
     * @return  AlterColumn
     */
    public function decimal($name, $maximum = null, $decimal = null)
    {
        return $this->addColumn($name, 'decimal')->set('M', $maximum)->set('D', $maximum);
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function boolean($name)
    {
        return $this->addColumn($name, 'boolean');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function binary($name)
    {
        return $this->addColumn($name, 'binary');
    }

    /**
     * @param   string  $name
     * @param   int     $length (optional)
     * 
     * @return  AlterColumn
     */
    public function string($name, $length = 255)
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }

    /**
     * @param   string  $name
     * @param   int     $length (optional)
     * 
     * @return  AlterColumn
     */
    public function fixed($name, $length = 255)
    {
        return $this->addColumn($name, 'fixed')->set('length', $length);
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function text($name)
    {
        return $this->addColumn($name);
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function time($name)
    {
        return $this->addColumn($name, 'time');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function timestamp($name)
    {
        return $this->addColumn($name, 'timestamp');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function date($name)
    {
        return $this->addColumn($name, 'date');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function dateTime($name)
    {
        return $this->addColumn($name, 'dateTime');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toInteger($name)
    {
        return $this->modifyColumn($name, 'integer');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toFloat($name)
    {
        return $this->modifyColumn($name, 'float');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toDouble($name)
    {
        return $this->modifyColumn($name, 'double');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toDecimal($name, $maximum = null, $decimal = null)
    {
        return $this->modifyColumn($name, 'decimal')->set('M', $maximum)->set('D', $maximum);
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toBoolean($name)
    {
        return $this->modifyColumn($name, 'boolean');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toBinary($name)
    {
        return $this->modifyColumn($name, 'binary');
    }

    /**
     * @param $name
     * @param int $length
     * @return $this
     */
    public function toString($name, $length = 255)
    {
        return $this->modifyColumn($name, 'string')->set('length', $length);
    }

    /**
     * @param   string  $name
     * @param   int     $length (optional)
     * 
     * @return  AlterColumn
     */
    public function toFixed($name, $length = 255)
    {
        return $this->modifyColumn($name, 'fixed')->set('length', $length);
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toText($name)
    {
        return $this->modifyColumn($name);
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toTime($name)
    {
        return $this->modifyColumn($name, 'time');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toTimestamp($name)
    {
        return $this->modifyColumn($name, 'timestamp');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toDate($name)
    {
        return $this->modifyColumn($name, 'date');
    }

    /**
     * @param   string  $name
     * 
     * @return  AlterColumn
     */
    public function toDateTime($name)
    {
        return $this->modifyColumn($name, 'dateTime');
    }
}
