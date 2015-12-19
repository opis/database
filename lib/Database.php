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

namespace Opis\Database;

use Closure;
use Opis\Database\Connection;
use Opis\Database\SQL\Query as QueryCommand;
use Opis\Database\SQL\Insert as InsertCommand;
use Opis\Database\SQL\Update as UpdateCommand;

class Database
{
    /** @var    \Opis\Database\Connection   Connection instance. */
    protected $connection;

    /** @var    \Opis\Database\Schema       Schema instance. */
    protected $schema;

    /**
     * Constructor
     *
     * @param   \Opis\Database\Connection   $connection Connection instance.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Database connection
     *
     * @return   \Opis\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the query log for this database.
     *
     * @return array
     */
    public function getLog()
    {
        return $this->connection->getLog();
    }

    /**
     * Execute a query in order to fetch or to delete records.
     *
     * @param   string|array    $tables Table name or an array of tables
     *
     * @return  \Opis\Database\SQL\Query
     */
    public function from($tables)
    {
        return new QueryCommand($this->connection, $tables);
    }

    /**
     * Insert new records into a table.
     *
     * @param   array  $values  An array of values.
     *
     * @return  \Opis\Database\SQL\Insert
     */
    public function insert(array $values)
    {
        return new InsertCommand($this->connection, $values);
    }

    /**
     * Update records.
     *
     * @param   string  $table  Table name
     *
     * @return  \Opis\Database\SQL\Update
     */
    public function update($table)
    {
        return new UpdateCommand($this->connection, $table);
    }

    /**
     * The associated schema instance.
     *
     * @return  \Opis\Database\Schema
     */
    public function schema()
    {
        if ($this->schema === null) {
            $this->schema = $this->connection->schema();
        }

        return $this->schema;
    }

    /**
     * Initiate a new transaction
     *
     * @param   \Closure    $queries    A callback that will be called when the transaction will begin
     * 
     * @return  \Opis\Database\Transaction
     */
    public function transaction(Closure $queries)
    {
        return new Transaction($this, $queries);
    }
}
