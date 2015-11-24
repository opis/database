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

namespace Opis\Database;

use Closure;
use PDO;
use PDOException;
use RuntimeException;
use Serializable;

class Connection implements Serializable
{
    /** @var    string  Username */
    protected $username;
    
    /** @var    string  Password */
    protected $password;
    
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
    
    /** @var    \PDO    The PDO object associated with this connection */
    protected $pdo;
    
    /** @var    \Opis\Database\Compiler The compiler associated with this connection */
    protected $compiler;
    
    /** @var    \Opis\Database\Schema\Compiler The schema compiler associated with this connection */
    protected $schemaCompiler;
    
    /** @var    string  The DSN for this connection */
    protected $dsn;
    
    /** @var    string  Driver's name */
    protected $driver;
    
    /** @var    \Opis\Database\Schema   Schema instance */
    protected $schema;
    
    /** @var    arrray  Compiler options */
    protected $compilerOptions = array();
    
    /**
     * Constructor
     * 
     * @access public
     *
     * @param   string  $dsn        The DSN string
     * @param   string  $username   (optional) Username
     * @param   string  $password   (optional) Password
     * @param   string  $driver     (optional) Driver's name
     */
    
    public function __construct($dsn, $username = null, $password = null, $driver = null)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->driver = $driver;
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
     * Set a PDO connection option
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
     *
     * @param   bool    $value  (optional) Value
     * 
     * @return  \Opis\Database\Connection
     */
    
    public function persistent($value = true)
    {
        return $this->option(PDO::ATTR_PERSISTENT, $value);
    }
    
    /**
     * Set date format
     *
     * @param   string  $format Date format
     *
     * @return  \Opis\Database\Connection
     */
    
    public function setDateFormat($format)
    {
        $this->compilerOptions['dateFormat'] = $format;
        return $this;
    }
    
    /**
     * Set identifier wrapper
     *
     * @param   string  $wrapper    Identifier wrapper
     *
     * @return  \Opis\Database\Connection
     */
    
    public function setWrapperFormat($wrapper)
    {
        $this->compilerOptions['wrapper'] = $wrapper;
        return $this;
    }
    
    /**
     * Returns the DSN associated with this connection
     *
     * @return  string
     */
    
    public function dsn()
    {
        return $this->dsn;
    }
    
    /**
     * Returns the driver's name
     *
     * @return  string
     */
    
    public function driver()
    {
        if($this->driver === null)
        {
            $this->driver = $this->pdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        }
        
        return $this->driver;
    }
    
    /**
     * Returns the schema associated with this connection
     *
     * @return  \Opis\Database\Schema
     */
    
    public function schema()
    {
        if($this->schema === null)
        {
            $this->schema = new Schema($this);
        }
        
        return $this->schema;
    }
    
    /**
     * Returns the PDO object associated with this connection
     *
     * @return \PDO
     */
    
    public function pdo()
    {
        if($this->pdo == null)
        {
            $this->pdo = new PDO($this->dsn(), $this->username, $this->password, $this->options);
            
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
            switch($this->driver())
            {
                case 'mysql':
                    $this->compiler = new \Opis\Database\SQL\Compiler\MySQL();
                    break;
                case 'dblib':
                case 'mssql':
                case 'sqlsrv':
                case 'sybase':
                    $this->compiler = new \Opis\Database\SQL\Compiler\SQLServer();
                    break;
                case 'oci':
                case 'oracle':
                    $this->compiler = new \Opis\Database\SQL\Compiler\Oracle();
                    break;
                case 'firebird':
                    $this->compiler = new \Opis\Database\SQL\Compiler\Firebird();
                    break;
                case 'db2':
                case 'ibm':
                case 'odbc':
                    $this->compiler = new \Opis\Database\SQL\Compiler\DB2();
                    break;
                case 'nuodb':
                    $this->compiler = new \Opis\Database\SQL\Compiler\NuoDB();
                    break;
                default:
                    $this->compiler = new \Opis\Database\SQL\Compiler();
            }
            
            $this->compiler->setOptions($this->compilerOptions);
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
        if($this->schemaCompiler === null)
        {   
            switch($this->driver())
            {
                case 'mysql':
                    $this->schemaCompiler = new \Opis\Database\Schema\Compiler\MySQL($this);
                    break;
                case 'pgsql':
                    $this->schemaCompiler = new \Opis\Database\Schema\Compiler\PostgreSQL($this);
                    break;
                case 'dblib':
                case 'mssql':
                case 'sqlsrv':
                case 'sybase':
                    $this->schemaCompiler = new \Opis\Database\Schema\Compiler\SQLServer($this);
                    break;
                case 'sqlite':
                    $this->schemaCompiler = new \Opis\Database\Schema\Compiler\SQLite($this);
                    break;
                default:
                    throw new \Exception('Schema not supported yet');
            }
        }
        
        return $this->schemaCompiler;
    }
    
    
    /**
     * Close the current connection by destroying the associated PDO object
     */
    
    public function disconnect()
    {
        $this->pdo = null;
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
     * @return  mixed
     */
    
    public function column($sql, array $params = array())
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        $result = $prepared['statement']->fetchColumn();
        $prepared['statement']->closeCursor();
        return $result;
    }
    
    /**
     * Implementation of Serializable::serialize
     *
     * @return  string
     */
    
    public function serialize()
    {
        return serialize(array(
            'username' => $this->username,
            'password' => $this->password,
            'logQueries' => $this->logQueries,
            'options' => $this->options,
            'commands' => $this->commands,
            'dsn' => $this->dsn,
        ));
    }
    
    /**
     * Implementation of Serializable::unserialize
     *
     * @param   string  $data   Serialized data
     */
    
    public function unserialize($data)
    {
        $object = unserialize($data);
        
        foreach($object as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Creates a new connection
     *
     * @param   string  $dsn        DSN connection string
     * @param   string  $username   (optional) Username
     * @param   string  $password   (optional) Password
     * @param   string  $driver     (optional) Driver's name
     *
     * @return  \Opis\Database\Connection
     */
    
    public static function create($dsn, $username = null, $password = null, $driver = null)
    {
        return new static($dsn, $username, $password, $driver);
    }

}
