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

namespace Opis\Database;

use Opis\Database\SQL\InsertStatement;
use Opis\Database\SQL\Query as QueryCommand;
use Opis\Database\SQL\Insert as InsertCommand;
use Opis\Database\SQL\Update as UpdateCommand;

class Database
{
    /** @var   Connection   Connection instance. */
    protected $connection;

    /** @var    Schema       Schema instance. */
    protected $schema;

    /**
     * Constructor
     *
     * @param   Connection $connection Connection instance.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Database connection
     *
     * @return   Connection
     */
    public function getConnection(): Connection
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
     * @param   string|array $tables Table name or an array of tables
     *
     * @return  QueryCommand
     */
    public function from($tables): QueryCommand
    {
        return new QueryCommand($this->connection, $tables);
    }

    /**
     * Insert new records into a table.
     *
     * @param   array $values An array of values.
     *
     * @return  InsertCommand|InsertStatement
     */
    public function insert(array $values): InsertCommand
    {
        return (new InsertCommand($this->connection))->insert($values);
    }

    /**
     * Update records.
     *
     * @param   string $table Table name
     *
     * @return  UpdateCommand
     */
    public function update($table): UpdateCommand
    {
        return new UpdateCommand($this->connection, $table);
    }

    /**
     * The associated schema instance.
     *
     * @return  Schema
     */
    public function schema(): Schema
    {
        if ($this->schema === null) {
            $this->schema = $this->connection->getSchema();
        }

        return $this->schema;
    }

    /**
     * Performs a transaction
     *
     * @param callable $query
     * @param mixed|null $default
     * @return mixed|null
     * @throws \PDOException
     */
    public function transaction(callable $query, $default = null)
    {
        return $this->connection->transaction($query, $this, $default);
    }
}
