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

use Opis\Database\Connection;
use Opis\Database\ResultSet;

class Select extends SelectStatement
{
    protected $connection;

    /**
     * Select constructor.
     * @param Connection $connection
     * @param array|string $tables
     * @param SQLStatement|null $statement
     */
    public function __construct(Connection $connection, $tables, SQLStatement $statement = null)
    {
        parent::__construct($tables, $statement);
        $this->connection = $connection;
    }

    /**
     * @param string $table
     * @param string|null $database
     * @return Select
     */
    public function into(string $table, string $database = null): self
    {
        $this->sql->setInto($table, $database);
        return $this;
    }

    /**
     * @param   string|array|\Closure    $columns    (optional)
     * 
     * @return  ResultSet
     */
    public function select($columns = array()): ResultSet
    {
        parent::select($columns);
        return $this->connection->query((string) $this, $this->compiler->getParams());
    }

    /**
     * @param   string  $name
     * 
     * @return  mixed|false
     */
    public function column($name)
    {
        parent::column($name);
        return $this->connection->column((string) $this, $this->compiler->getParams());
    }

    /**
     * @param   string  $column     (optional)
     * @param   bool    $distinct   (optional)
     * 
     * @return  int
     */
    public function count($column = '*', bool $distinct = false)
    {
        parent::count($column, $distinct);
        return $this->connection->column((string) $this, $this->compiler->getParams());
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  int|float
     */
    public function avg($column, bool $distinct = false)
    {
        parent::avg($column, $distinct);
        return $this->connection->column((string) $this, $this->compiler->getParams());
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  int|float
     */
    public function sum($column, bool $distinct = false)
    {
        parent::sum($column, $distinct);
        return $this->connection->column((string) $this, $this->compiler->getParams());
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  int|float
     */
    public function min($column, bool $distinct = false)
    {
        parent::min($column, $distinct);
        return $this->connection->column((string) $this, $this->compiler->getParams());
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  int|float
     */
    public function max($column, bool $distinct = false)
    {
        parent::max($column, $distinct);
        return $this->connection->column((string) $this, $this->compiler->getParams());
    }
}
