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

namespace Opis\Database\SQL;

use Closure;

class UpdateStatement extends WhereJoinCondition
{
    /** @var    array */
    protected $tables;

    /** @var    array */
    protected $columns = array();

    /** @var    string */
    protected $sql;

    /**
     * Constructor
     * 
     * @param   Compiler        $compiler
     * @param   array|string    $table
     * @param   WhereClause     $clause     (optional)
     */
    public function __construct(Compiler $compiler, $table, WhereClause $clause = null)
    {
        if (!is_array($table)) {
            $table = array($table);
        }

        $this->tables = $table;

        parent::__construct($compiler, $clause);
    }

    /**
     * @return  array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param   array   $columns
     * 
     * @return  $this
     */
    public function set(array $columns)
    {
        foreach ($columns as $column => $value) {
            if ($value instanceof Closure) {
                $expr = new Expression($this->compiler);
                $value($expr);

                $this->columns[] = array(
                    'column' => $column,
                    'value' => $expr,
                );
            } else {
                $this->columns[] = array(
                    'column' => $column,
                    'value' => $value,
                );
            }
        }
        return $this;
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        if ($this->sql === null) {
            $this->sql = $this->compiler->update($this);
        }

        return $this->sql;
    }
}
