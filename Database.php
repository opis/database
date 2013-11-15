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
use Opis\Database\SQL\Query;
use Opis\Database\Connection;

class Database
{

    /** @var    \PDO    PDO object. */
    protected $pdo;
    
    /** @var    \Opis\Database\SQL\Compiler  Compiler. */
    protected $compiler;

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
        $this->pdo = $connection->pdo();
        $this->compiler = $connection->compiler();
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
     * Returns the PDO instance.
     *
     * @access public
     * @return \PDO
     */

    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * Returns the compiler name.
     *
     * @access public
     * @return string
     */

    public function getCompiler()
    {
        return $this->compiler;
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

    protected function replaceParams($query, array $params)
    {
        $pdo = $this->pdo;
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
        $pdo = $this->pdo;
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
        // Replace IN clause placeholder with escaped values
        replace:
        if(strpos($query, '([?])') !== false)
        {
            foreach($params as $key => $value)
            {
                if(is_array($value))
                {
                    array_splice($params, $key, 1, $value);
                    $query = preg_replace('/\(\[\?\]\)/', '(' . trim(str_repeat('?, ', count($value)), ', ') . ')', $query, 1);
                    goto replace;
                }
            }
        }
        // Prepare statement
        try
        {
            $statement = $this->pdo->prepare($query);
        }catch(PDOException $e)
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

    /**
     * Executes the query and returns TRUE on success or FALSE on failure.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return mixed
     */

    public function query($query, array $params = array())
    {
        return $this->execute($this->prepare($query, $params));
    }

    /**
     * Executes the query and returns TRUE on success or FALSE on failure.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return boolean
     */

    public function insert($query, array $params = array())
    {
        return $this->query($query, $params);
    }

    /**
     * Returns an array containing all of the result set rows.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return array
     */

    public function all($query, array $params = array())
    {
        $prepared = $this->prepare($query, $params);
        $this->execute($prepared);
        return $prepared['statement']->fetchAll();
    }

    /**
     * Returns the first row of the result set.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query params
     * @return mixed
     */

    public function first($query, array $params = array())
    {
        $prepared = $this->prepare($query, $params);
        $this->execute($prepared);
        return $prepared['statement']->fetch();
    }

    /**
     * Returns the value of the first column of the first row of the result set.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return mixed
     */

    public function column($query, array $params = array())
    {
        $prepared = $this->prepare($query, $params);
        $this->execute($prepared);
        return $prepared['statement']->fetchColumn();
    }

    /**
     * Executes the query and return number of affected rows.
     *
     * @access protected
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return int
     */

    protected function executeAndCount($query, array $params)
    {
        $prepared = $this->prepare($query, $params);
        $this->execute($prepared);
        return $prepared['statement']->rowCount();
    }

    /**
     * Executes the query and returns the number of updated records.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return int
     */

    public function update($query, array $params = array())
    {
        return $this->executeAndCount($query, $params);
    }

    /**
     * Executes the query and returns the number of deleted records.
     *
     * @access public
     * @param string $query SQL query
     * @param array $params (optional) Query parameters
     * @return int
     */

    public function delete($query, array $params = array())
    {
        return $this->executeAndCount($query, $params);
    }

    /**
     * Returns a query builder instance.
     *
     * @access public
     * @param mixed $table Table name or subquery
     * @return \Opis\Database\SQL\Query
     */

    public function table($table)
    {
        return new Query($this, $table);
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
            
        }catch(PDOException $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
        return $result;
    }
}