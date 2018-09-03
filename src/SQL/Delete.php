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

namespace Opis\Database\SQL;

use Opis\Database\Connection;

class Delete extends DeleteStatement
{
    /** @var    Connection */
    protected $connection;

    /**
     * Delete constructor.
     * @param Connection $connection
     * @param string|array $from
     * @param SQLStatement|null $statement
     */
    public function __construct(Connection $connection, $from, SQLStatement $statement = null)
    {
        parent::__construct($from, $statement);
        $this->connection = $connection;
    }

    /**
     * Delete records
     *
     * @param   string|array $tables (optional)
     *
     * @return  int
     */
    public function delete($tables = [])
    {
        parent::delete($tables);
        $compiler = $this->connection->getCompiler();
        return $this->connection->count($compiler->delete($this->sql), $compiler->getParams());
    }
}
