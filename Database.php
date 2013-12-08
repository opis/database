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
    
    protected $pdo;

    protected $connection;

    /** @var    boolean Enable log. */
    protected $enableLog;

    /** @var    array   Query log. */
    protected $log = array();
    
    protected static $instances = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string  $name   Connection name.
     * @param   array   $config Connection configuration.
     */
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $connection->pdo();
        $this->enableLog = $connection->loggingEnabled();
    }
    
    
    public static function connection($name = null)
    {
        if($name === null)
        {
            $name = Connection::getDefaultName();
        }
        if(!isset(static::$instances[$name]))
        {
            static::$instances[$name] = new Database(Connection::get($name));
        }
        return static::$instances[$name];
    }

    /**
     * Returns the compiler name.
     *
     * @access public
     * @return string
     */

    public function getCompiler()
    {
        return $this->connection->compiler();
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
     * @access protected
     * @param string $query SQL query
     * @param array $params Query paramaters
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
     * Adds a query to the query log.
     *
     * @access protected
     * @param string $query SQL query
     * @param array $params Query parameters
     * @param int $start Start time in microseconds
     */

    protected function log($query, array $params, $start)
    {
        $time = microtime(true) - $start;
        $query = $this->replaceParams($query, $params);
        $this->log[] = compact('query', 'time');
    }

    /**
     * Returns the query log for the connection.
     *
     * @access public
     * @return array
     */

    public function getLog()
    {
        return $this->log;
    }

    /**
     * Prepares a query.
     *
     * @access protected
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array
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
     * @access protected
     * @param array $prepared Prepared query
     * @return boolean
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
        try
        {
            $this->pdo->beginTransaction();
            $result = $queries($this);
            $this->pdo->commit();
            
        }
        catch(PDOException $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
        return $result;
    }
}