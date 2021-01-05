<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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

use Opis\Database\SQL\{
    InsertStatement,
    Query as QueryCommand,
    Insert as InsertCommand,
    Update as UpdateCommand
};

class Database
{
    protected Connection $connection;

    /**
     * Database constructor.
     * @param Connection $connection
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
    public function getLog(): array
    {
        return $this->connection->getLog();
    }

    /**
     * Execute a query in order to fetch or to delete records.
     *
     * @param string|array $tables
     * @return QueryCommand
     */
    public function from(string|array $tables): QueryCommand
    {
        return new QueryCommand($this->connection, $tables);
    }

    /**
     * Insert new records into a table.
     *
     * @param array $values
     * @return InsertStatement
     */
    public function insert(array $values): InsertStatement
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
    public function update(string $table): UpdateCommand
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
        return $this->connection->getSchema();
    }

    /**
     * Perform a transaction
     *
     * @param callable $query
     * @param mixed|null $default
     * @return mixed
     */
    public function transaction(callable $query, mixed $default = null): mixed
    {
        return $this->connection->transaction($query, $this, $default);
    }

    /**
     * @param string|null $name
     * @return string
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->connection->getPDO()->lastInsertId($name);
    }
}
