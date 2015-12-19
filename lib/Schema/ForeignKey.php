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

class ForeignKey
{
    /** @var    string */
    protected $refTable;

    /** @var    array */
    protected $refColumns;

    /** @var    array */
    protected $actions = array();

    /** @var    array */
    protected $columns;

    /**
     * Constructor
     * 
     * @param   array   $columns
     */
    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param   string  $on
     * @param   string  $action
     * 
     * @return  $this
     */
    protected function addAction($on, $action)
    {
        $action = strtoupper($action);

        if (!in_array($action, array('RESTRICT', 'CASCADE', 'NO ACTION', 'SET NULL'))) {
            return $this;
        }

        $this->actions[$on] = $action;
        return $this;
    }

    /**
     * @return  string
     */
    public function getReferencedTable()
    {
        return $this->refTable;
    }

    /**
     * @return  array
     */
    public function getReferencedColumns()
    {
        return $this->refColumns;
    }

    /**
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return  array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param   string  $table
     * 
     * @return  $this
     */
    public function references($table)
    {
        $this->refTable = $table;
        return $this;
    }

    /**
     * @param   string|array $columns
     * 
     * @return  $this
     */
    public function on($columns)
    {
        if (!is_array($columns)) {
            $columns = array($columns);
        }

        $this->refColumns = $columns;
        return $this;
    }

    /**
     * @param   string  $action
     * 
     * @return  $this
     */
    public function onDelete($action)
    {
        return $this->addAction('ON DELETE', $action);
    }

    /**
     * @param   string  $action
     * 
     * @return  $this
     */
    public function onUpdate($action)
    {
        return $this->addAction('ON UPDATE', $action);
    }
}
