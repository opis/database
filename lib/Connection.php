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
use PDO;
use PDOException;
use RuntimeException;
use Serializable;
use Opis\Database\DSN\Generic as GenericConnection;
use Opis\Database\DSN\MySQL as MySQLConnection;
use Opis\Database\DSN\IBM as IBMConnection;
use Opis\Database\DSN\Oracle as OracleConnection;
use Opis\Database\DSN\PostgreSQL as PostgreSQLConnection;
use Opis\Database\DSN\Firebird as FirebirdConnection;
use Opis\Database\DSN\SQLite as SQLiteConnection;
use Opis\Database\DSN\SQLServer as SQLServerConnection;
use Opis\Database\DSN\DBLib as DBLibConnection;

class Connection implements Serializable
{
    /** @var    string  Username */
    protected $username = null;
    
    /** @var    string  Password */
    protected $password = null;
    
    /** @var    bool    Log queries flag */
    protected $logQueries = false;
    
    /** @var    array   Logged queries */
    protected $log = array();
    
    /** @var    array   Init commands */
    protected $commands = array();
    
    /** @var    array   PDO connection options */
    protected $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    );
    
    /** @var    string  DSN prefix */
    protected $prefix;
    
    /** @var    string  Database */
    protected $database;
    
    /** @var    array   DSN properties */
    protected $properties = array();
    
    /** @var    \PDO    The PDO object associated with this connection */
    protected $pdo = null;
    
    /** @var    \Opis\Database\Compiler The compiler associated with this connection */
    protected $compiler = null;
    
    /** @var    string  The DSN assocatied with this connection */
    protected $dsn = null;
    
    /**
     * Constructor
     * 
     * @access public
     *
     * @param string $prefix    DSN prefix
     * @param string $username  (optional) Username
     * @param string $password  (optional) Password
     */
    
    public function __construct($prefix, $username = null, $password = null)
    {
        $this->prefix = $prefix;
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Returns the database name for this connection
     *
     * @return string
     */
    
    public function dbname()
    {
        return $this->database;
    }
    
    /**
     * Returns the DSN prefix
     *
     * @return string
     */
    
    public function prefix()
    {
        return $this->prefix;
    }
    
    /**
     * Enable or disable query logging
     *
     * @param   bool    $value  (optional) Value
     *
     * @return  \Opis\Database\Connection
     */
    
    public function logQueries($value = true)
    {
        $this->logQueries = $value;
        return $this;
    }
    
    
    /**
     * Add an init command
     *
     * @param   string  $query SQL command
     * @param   array   $params (optional) Params
     *
     * @return \Opis\Database\Connection
     */
    
    public function initCommand($query, array $params = array())
    {
        $this->commands[] = array(
            'sql' => $query,
            'params' => $params,
        );
        
        return $this;
    }
    
    /**
     * Set the username
     *
     * @param   string  $username   Username
     *
     * @return  \Opis\Database\Connection
     */
    
    public function username($username)
    {
        $this->username = $username;
        return $this;
    }
    
    /**
     * Set the password
     *
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\Connection
     */
    
    public function password($password)
    {
        $this->password = $password;
        return $this;
    }
    
    /**
     * Set a DSN property
     *
     * @param   string  $name   Property name
     * @param   string  $value  Property value
     *
     * @return  \Opis\Database\Connection
     */
    
    public function set($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }
    
    /**
     * Set the database DSN property
     *
     * @param   string  $name   DSN property
     * @param   string  $value  Database name
     *
     * @return  \Opis\Database\Connection
     */
    
    public function setDatabase($name, $value)
    {
        $this->database = $value;
        return $this->set($name, $value);
    }
    
    /**
     * Set PDO connection options
     *
     * @param   array   $options    PDO options
     *
     * @return  \Opis\Database\Compiler
     */
    
    public function options(array $options)
    {
        foreach($options as $name => $value)
        {
            $this->option($name, $value);
        }
        return $this;
    }
    
    /**
     * Set a connection option
     *
     * @param   string  $name   Option
     * @param   int     $value  Value
     *
     * @return  \Opis\Database\Connection
     */
    
    public function option($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }
    
    /**
     * Use persistent connections
     */
    
    public function persistent()
    {
        return $this->option(PDO::ATTR_PERSISTENT, true);
    }
    
    /**
     * Genarate the DSN associated with this connection
     *
     * @return  string
     */
    
    public function dsn()
    {
        if($this->dsn === null)
        {
            $tmp = array();
            foreach($this->properties as $key => $value)
            {
                $tmp[] = $key . '=' . $value;
            }
            $this->dsn = $this->prefix() . ':' . implode(';', $tmp);
        }
        return $this->dsn;
    }
    
    /**
     * Construct the PDO object associated with this connection
     *
     * @return \PDO
     */
    
    public function pdo()
    {
        if($this->pdo == null)
        {
            try
            {
                
                $this->pdo = new PDO($this->dsn(), $this->username, $this->password, $this->options);
                
            }
            catch(PDOException $e)
            {
                throw new RuntimeException(vsprintf("%s(): Failed to connect to the '%s' database. %s", array(__METHOD__, $this->dbname(), $e->getMessage())));
            }
            
            foreach($this->commands as $command)
            {
                $this->command($command['sql'], $command['params']);
            }
            
        }
        
        return $this->pdo;
    }
    
    /**
     * Returns an instance of the compiler associated with this connection
     *
     * @return \Opis\Database\SQL\Compiler
     */
    
    public function compiler()
    {
        if($this->compiler === null)
        {
            $this->compiler = new \Opis\Database\SQL\Compiler();
        }
        
        return $this->compiler;
    }
    
    /**
     * Returns an instance of the schema compiler associated with this connection
     *
     * @return \Opis\Database\Schema\Compiler
     */
    
    public function schemaCompiler()
    {
        throw new \Exception('Schema not supported yet');
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
     * Replace placeholders with parameteters.
     *
     * @access  protected
     * 
     * @param   string  $query  SQL query
     * @param   array   $params Query paramaters
     * 
     * @return string
     */

    protected function replaceParams($query, array $params)
    {
        $pdo = $this->pdo();
        
        return preg_replace_callback('/\?/', function($matches) use (&$params, $pdo){
            $param = array_shift($params);
            return (is_int($param) || is_float($param)) ? $param : $pdo->quote(is_object($param) ? get_class($param) : $param);
        }, $query);
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
        try
        {
            $statement = $this->pdo()->prepare($query);
        }
        catch(PDOException $e)
        {
            throw new PDOException($e->getMessage() . ' [ ' . $this->replaceParams($query, $params) . ' ] ', (int) $e->getCode(), $e->getPrevious());
        }
        
        return array('query' => $query, 'params' => $params, 'statement' => $statement);
    }
    
    /**
     * Executes a prepared query and returns TRUE on success or FALSE on failure.
     *
     * @access  protected
     *
     * @param   array   $prepared   Prepared query
     *
     * @return  boolean
     */

    protected function execute(array $prepared)
    {
        if($this->logQueries)
        {
            $start = microtime(true);
        }
        
        $result = $prepared['statement']->execute($prepared['params']);
        
        if($this->logQueries)
        {
            $this->log($prepared['query'], $prepared['params'], $start);
        }
        
        return $result;
    }
    
    /**
     * Execute a query
     *
     * @access  public
     *
     * @param   string  $sql    SQL Query
     * @param   array   $params (optional) Query params
     *
     * @return  \Opis\Database\ResultSet
     */
    
    public function query($sql, array $params = array())
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        return new ResultSet($prepared['statement']);
    }
    
    /**
     * Execute a non-query SQL command
     *
     * @access  public
     *
     * @param   string  $sql    SQL Command
     * @param   array   $params (optional) Command params
     *
     * @return  mixed Command result
     */
    
    public function command($sql, array $params = array())
    {
        return $this->execute($this->prepare($sql, $params));
    }
    
    /**
     * Execute a query and return the number of affected rows
     *
     * @access  public
     *
     * @param   string  $sql    SQL Query
     * @param   array   $params (optional) Query params
     *
     * @return  int
     */
    
    public function count($sql, array $params = array())
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        $result = $prepared['statement']->rowCount();
        $prepared['statement']->closeCursor();
        return $result;
    }
    
    /**
     * Execute a query and fetch the first column
     *
     * @access  public
     *
     * @param   string  $sql    SQL Query
     * @param   array   $params (optional) Query params
     *
     * @return  \Opis\Database\ResultSet
     */
    
    public function column($sql, array $params)
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        $result = $prepared['statement']->fetchColumn();
        $prepared['statement']->closeCursor();
        return $result;
    }
    
    public function serialize()
    {
        return serialize(array(
            'username' => $this->username,
            'password' => $this->password,
            'logQueries' => $this->logQueries,
            'options' => $this->options,
            'queries' => $this->queries,
            'properties' => $this->properties,
            'prefix' => $this->prefix,
            'database' => $this->database,
            'dsn' => $this->dsn(),
        ));
    }
    
    public function unserialize($data)
    {
        $object = unserialize($data);
        foreach($object as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Creates a generic connection
     *
     * @param   string  $dsn        DSN connection string
     * @param   string  $username   (optional) Username
     * @param   string  $password   (optional)  Password
     *
     * @return  \Opis\Database\Connection
     */
    
    public static function generic($dsn, $username = null, $password = null)
    {
        return new GenericConnection($dsn, $username, $password);
    }
    
    /**
     * Creates a Firebird specific connection
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\DSN\Firebird
     */
    
    public static function firebird($username, $password)
    {
        return new FirebirdConnection($username, $password);
    }
    
    /**
     * Creates an IBM specific connection
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\DSN\IBM
     */
    
    public static function ibm($username, $password)
    {
        return new IBMConnection($username, $password);
    }
    
    /**
     * Creates an Oracle specific connection
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\DSN\Oracle
     */
    
    public static function oracle($username, $password)
    {
        return new OracleConnection($username, $password);
    }
    
    /**
     * Creates a MySQL specific connection
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\DSN\MySQL
     */
    
    public static function mysql($username, $password)
    {
        return new MySQLConnection($username, $password);
    }
    
    /**
     * Creates a PostgreSQL specific connection
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\DSN\PostgreSQL
     */
    
    public static function postgreSQL($username, $password)
    {
        return new PostgreSQLConnection($username, $password);
    }
    
    /**
     * Connecting to an Microsoft SQL Server and SQL Azure database
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis|Database\DSN\SQLServer
     */
    
    public static function sqlServer($username, $password)
    {
        return new SQLServerConnection($username, $password);
    }
    
    /**
     * Connecting to an Microsoft SQL Server or Sybase database
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     * @param   string  $driver     (optional) Driver
     * 
     * @return  \Opis|Database\DSN\DBLib
     */
    
    public static function mssql($username, $password, $driver = 'dblib')
    {
        return new DBLibConnection($driver, $username, $password);
    }
    
    /**
     * Creates a SQLite specific connection
     *
     * If the $path parameter is omitted, an in-memory database will be created
     * 
     * @param   string  $path   (optional) Username
     *
     * @return  \Opis\Database\DSN\SQLite
     */
    
    public static function sqlite($path = null)
    {
        return new SQLiteConnection($path);
    }
    
    /**
     * Creates a SQLite2 specific connection
     *
     * If the $path parameter is omitted, an in-memory database will be created
     * 
     * @param   string  $path   (optional) Username
     *
     * @return  \Opis\Database\DSN\SQLite
     */
    
    public static function sqlite2($path = null)
    {
        return new SQLiteConnection($path, '2');
    }
}