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

class Delete extends DeleteStatement
{
    /** @var    Connection */
    protected $connection;

    /**
     * Constructor
     * 
     * @param   Connection      $connection
     * @param   Compiler        $compiler
     * @param   string|array    $from
     * @param   array           $joins
     * @param   WhereClause     $clause     (optional)
     */
    public function __construct(Connection $connection, Compiler $compiler, $from, $joins, WhereClause $clause = null)
    {
        parent::__construct($compiler, $from, $clause);
        $this->connection = $connection;
        $this->joins = $joins;
    }

    /**
     * Delete records
     * 
     * @param   string|array    $tables (optional)
     * 
     * @return  int
     */
    public function delete($tables = array())
    {
        parent::delete($tables);
        return $this->connection->count((string) $this, $this->compiler->getParams());
    }
}
