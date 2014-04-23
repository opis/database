<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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
    
    /**
     * Constructor
     *
     * @access  public
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
    * @access   public
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
     * @access public
     * 
     * @return array
     */

    public function getLog()
    {
        return $this->connection->getLog();
    }
    
    public function from($tables)
    {
        return new QueryCommand($this->connection, $tables);
    }
    
    public function insert($table, $columns = array())
    {
        return new InsertCommand($this->connection, $table, $columns);
    }
    
    public function update($table)
    {
        return new UpdateCommand($this->connection, $table);
    }
    
    
    /**
     * Executes queries and rolls back the transaction if any of them fail.
     *
     * @access public
     * 
     * @param \Closure $queries Queries
     * 
     * @return mixed
     */

    public function transaction(Closure $queries)
    {
        return new Transaction($this, $queries);
    }
}
