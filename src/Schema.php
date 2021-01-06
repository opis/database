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

use RuntimeException;
use Opis\Database\Schema\Blueprint;

class Schema
{
    protected Connection $connection;
    protected ?array $tableList = null;
    protected ?string $currentDatabase = null;
    protected array $columns = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the name of the currently used database
     *
     * @return string
     */
    public function getCurrentDatabase(): string
    {
        if ($this->currentDatabase === null) {
            $compiler = $this->connection->schemaCompiler();
            $result = $compiler->currentDatabase($this->connection->getDSN());

            if (isset($result['result'])) {
                $this->currentDatabase = $result['result'];
            } else {
                $this->currentDatabase = $this->connection->column($result['sql'], $result['params']);
            }
        }

        return $this->currentDatabase;
    }

    /**
     * Check if the specified table exists
     *
     * @param string $table
     * @param bool $clear
     * @return bool
     */
    public function hasTable(string $table, bool $clear = false): bool
    {
        $list = $this->getTables($clear);
        return isset($list[strtolower($table)]);
    }

    /**
     * Get a list with all tables that belong to the currently used database
     *
     * @param bool $clear
     * @return string[]
     */
    public function getTables(bool $clear = false): array
    {
        if ($clear) {
            $this->tableList = null;
        }

        if ($this->tableList === null) {
            $compiler = $this->connection->schemaCompiler();

            $database = $this->getCurrentDatabase();

            $sql = $compiler->getTables($database);

            $results = $this->connection
                ->query($sql['sql'], $sql['params'])
                ->fetchNum()
                ->all();

            $this->tableList = [];

            foreach ($results as $result) {
                $this->tableList[strtolower($result[0])] = $result[0];
            }
        }

        return $this->tableList;
    }

    /**
     * Get a list with all columns that belong to the specified table
     *
     * @param string $table
     * @param bool $clear
     * @param bool $names
     * @return string[]
     */
    public function getColumns(string $table, bool $clear = false, bool $names = true): array
    {
        if ($clear) {
            unset($this->columns[$table]);
        }

        if (!$this->hasTable($table, $clear)) {
            throw new RuntimeException(sprintf("Invalid table name '%s'", $table));
        }

        if (!isset($this->columns[$table])) {
            $compiler = $this->connection->schemaCompiler();

            $database = $this->getCurrentDatabase();

            $sql = $compiler->getColumns($database, $table);

            $results = $this->connection
                ->query($sql['sql'], $sql['params'])
                ->fetchAssoc()
                ->all();

            $columns = [];

            foreach ($results as $ord => $col) {
                $columns[$col['name']] = [
                    'name' => $col['name'],
                    'type' => $col['type'],
                ];
            }

            $this->columns[$table] = $columns;
        }

        return $names ? array_keys($this->columns[$table]) : $this->columns[$table];
    }

    /**
     * Creates a new table
     *
     * @param string $table
     * @param callable $callback
     */
    public function create(string $table, callable $callback): void
    {
        $schema = new Blueprint($table);

        $callback($schema);

        $connection = $this->connection;
        foreach ($connection->schemaCompiler()->create($schema) as $result) {
            $connection->command($result['sql'], $result['params']);
        }

        //clear table list
        $this->tableList = null;
    }

    /**
     * Alters a table's definition
     *
     * @param string $table
     * @param callable $callback
     */
    public function alter(string $table, callable $callback): void
    {
        $schema = new Blueprint($table, true);

        $callback($schema);

        unset($this->columns[strtolower($table)]);

        $connection = $this->connection;
        foreach ($connection->schemaCompiler()->alter($schema) as $result) {
            $connection->command($result['sql'], $result['params']);
        }
    }

    /**
     * Change a table's name
     *
     * @param string $table
     * @param string $name
     */
    public function renameTable(string $table, string $name): void
    {
        $result = $this->connection->schemaCompiler()->renameTable($table, $name);
        $this->connection->command($result['sql'], $result['params']);
        $this->tableList = null;
        unset($this->columns[strtolower($table)]);
    }

    /**
     * Deletes a table
     *
     * @param string $table
     */
    public function drop(string $table): void
    {
        $compiler = $this->connection->schemaCompiler();

        $result = $compiler->drop($table);

        $this->connection->command($result['sql'], $result['params']);

        //clear table list
        $this->tableList = null;
        unset($this->columns[strtolower($table)]);
    }

    /**
     * Deletes all records from a table
     *
     * @param string $table
     */
    public function truncate(string $table): void
    {
        $compiler = $this->connection->schemaCompiler();

        $result = $compiler->truncate($table);

        $this->connection->command($result['sql'], $result['params']);
    }
}
