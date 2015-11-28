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
use Opis\Database\Connection;
use Opis\Database\ResultSet;

class Query extends WhereJoinCondition
{
    protected $connection;
    
    protected $tables;

    /**
     * Query constructor.
     * @param Connection $connection
     * @param WhereClause $tables
     */
    public function __construct(Connection $connection, $tables)
    {
        parent::__construct($connection->compiler());
        $this->tables =  $tables;
        $this->connection = $connection;
    }

    /**
     * @return Select
     */
    protected function buildSelect()
    {
        return new Select($this->connection, $this->compiler, $this->tables, $this->joins, $this->whereClause);
    }

    /**
     * @return Delete
     */
    protected function buildDelete()
    {
        return new Delete($this->connection, $this->compiler, $this->tables, $this->joins, $this->whereClause);
    }

    /**
     * @param bool|true $value
     * @return $this
     */
    public function distinct($value = true)
    {
        return $this->buildSelect()->distinct($value);
    }

    /**
     * @param $columns
     * @return $this
     */
    public function groupBy($columns)
    {
        return $this->buildSelect()->groupBy($columns);
    }

    /**
     * @param $column
     * @param Closure|null $value
     * @return $this
     */
    public function having($column, Closure $value = null)
    {
        return $this->buildSelect()->having($column, $value);
    }

    /**
     * @param $column
     * @param Closure $value
     * @return $this
     */
    public function andHaving($column, Closure $value)
    {
        return $this->buildSelect()->andHaving($column, $value);
    }

    /**
     * @param $column
     * @param Closure|null $value
     * @return $this
     */
    public function orHaving($column, Closure $value = null)
    {
        return $this->buildSelect()->orHaving($column, $value);
    }

    /**
     * @param $columns
     * @param string $order
     * @param null $nulls
     * @return $this
     */
    public function orderBy($columns, $order = 'ASC', $nulls = null)
    {
        return $this->buildSelect()->orderBy($columns, $order, $nulls);
    }

    /**
     * @param $value
     * @return $this
     */
    public function limit($value)
    {
        return $this->buildSelect()->limit($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function offset($value)
    {
        return $this->buildSelect()->offset($value);
    }

    /**
     * @param $table
     * @param null $database
     * @return $this
     */
    public function into($table, $database = null)
    {
        return $this->buildSelect()->into($table, $database);
    }

    /**
     * @param array $columns
     * @return $this|ResultSet
     */
    public function select($columns = array())
    {
        return $this->buildSelect()->select($columns);
    }

    /**
     * @param $name
     * @return mixed|void
     */
    public function column($name)
    {
        return $this->buildSelect()->column($name);
    }

    /**
     * @param string $column
     * @param bool|false $distinct
     * @return mixed|void
     */
    public function count($column = '*',  $distinct = false)
    {
        return $this->buildSelect()->count($column, $distinct);
    }

    /**
     * @param $column
     * @param bool|false $distinct
     * @return mixed|void
     */
    public function avg($column, $distinct = false)
    {
        return $this->buildSelect()->avg($column, $distinct);
    }

    /**
     * @param $column
     * @param bool|false $distinct
     * @return mixed|void
     */
    public function sum($column, $distinct  = false)
    {
        return $this->buildSelect()->sum($column, $distinct);
    }

    /**
     * @param $column
     * @param bool|false $distinct
     * @return mixed|void
     */
    public function min($column, $distinct = false)
    {
        return $this->buildSelect()->min($column, $distinct);
    }

    /**
     * @param $column
     * @param bool|false $distinct
     * @return mixed|void
     */
    public function max($column, $distinct = false)
    {
        return $this->buildSelect()->max($column, $distinct);
    }

    /**
     * @param array $tables
     * @return int|void
     */
    public function delete($tables = array())
    {
        return $this->buildDelete()->delete($tables);
    }
    
}
