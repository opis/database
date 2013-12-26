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

use PDO;
use Closure;
use PDOException;
use RuntimeException;
use Opis\Database\Connection;
use Opis\Database\SQL\Query as QueryCommand;
use Opis\Database\SQL\Insert as InsertCommand;
use Opis\Database\SQL\Update as UpdateCommand;

class Database
{
    /** @var    \PDO    PDO instance. */
    protected $pdo;

    /** @var    \Opis\Database\Connection   Connection instance. */
    protected $connection;

    /** @var    boolean Enable log flag. */
    protected $enableLog;

    /** @var    array   Query log. */
    protected $log = array();
    
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
        $this->pdo = $connection->pdo();
        $this->enableLog = $connection->loggingEnabled();
    }

    /**
     * Returns a new instance of the compiler associated with this database
     *
     * @access  public
     *
     * @return  \Opis\Database\SQL\Compiler;
     */

    public function getCompiler()
    {
        return $this->connection->compiler();
    }
    
    /**
    * Get connection
    */
    
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Enables or disable the query log.
     *
     * @access public
     */

    public function enableLog($value = true)
    {
        $this->enableLog = $value;
    }


    /**
     * Replace placeholders with parameteters.
     *
     * @access  public
     * 
     * @param   string  $query  SQL query
     * @param   array   $params Query paramaters
     * 
     * @return string
     */

    public function replaceParams($query, array $params)
    {
        $pdo = $this->connection->pdo();
        
        return preg_replace_callback('/\?/', function($matches) use (&$params, $pdo){
            $param = array_shift($params);
            return (is_int($param) || is_float($param)) ? $param : $pdo->quote(is_object($param) ? get_class($param) : $param);
        }, $query);
    }

    /**
     * Log a query.
     *
     * @access  protected
     * 
     * @param   string  $query  SQL query
     * @param   array   $params Query parameters
     * @param   int     $start  Start time in microseconds
     */

    protected function log($query, array $params, $start)
    {
        $time = microtime(true) - $start;
        $query = $this->replaceParams($query, $params);
        $this->log[] = compact('query', 'time');
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
        return $this->log;
    }

    /**
     * Prepares a query.
     *
     * @access  protected
     * 
     * @param   string  $query  SQL query
     * @param   array   $params Query parameters
     * 
     * @return  array
     */

    protected function prepare($query, array $params)
    {
        // Prepare statement
        try
        {
            $statement = $this->pdo->prepare($query);
        }
        catch(PDOException $e)
        {
            throw new PDOException($e->getMessage() . ' [ ' . $this->replaceParams($query, $params) . ' ] ', (int) $e->getCode(), $e->getPrevious());
        }
        // Return query, parameters and the prepared statement
        return array('query' => $query, 'params' => $params, 'statement' => $statement);
    }

    /**
     * Executes the prepared query and returns TRUE on success or FALSE on failure.
     *
     * @access  protected
     *
     * @param   array   $prepared   Prepared query
     *
     * @return  boolean
     */

    protected function execute(array $prepared)
    {
        if($this->enableLog)
        {
            $start = microtime(true);
        }
        $result = $prepared['statement']->execute($prepared['params']);
        if($this->enableLog)
        {
            $this->log($prepared['query'], $prepared['params'], $start);
        }
        return $result;
    }
    
    public function count($sql, array $params)
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        return $prepared['statement']->rowCount();
    }
    
    public function query($sql, array $params)
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        return new ResultSet($prepared['statement']);
    }
    
    public function success($sql, array $params)
    {
        return $this->execute($this->prepare($sql, $params));
    }
    
    public function column($sql, array $params)
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        $result = $prepared['statement']->fetchColumn();
        $prepared['statement']->closeCursor();
        return $result;
    }
    
    public function from($tables)
    {
        return new QueryCommand($this, $tables);
    }
    
    public function insert($table, $columns = array())
    {
        return new InsertCommand($this, $table, $columns);
    }
    
    public function update($table)
    {
        return new UpdateCommand($this, $table);
    }
    
    
    /**
     * Executes queries and rolls back the transaction if any of them fail.
     *
     * @access public
     * @param \Closure $queries Queries
     * @return mixed
     */

    public function transaction(Closure $queries)
    {
        return new Transaction($this, $queries);
    }
}