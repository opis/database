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
    protected $log = false;
    
    /** @var    array   Queries */
    protected $queries = array();
    
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
        $this->log = $value;
        return $this;
    }
    
    /**
     * Check if query logging is enabled
     *
     * @return  bool
     */
    
    public function loggingEnabled()
    {
        return $this->log;
    }
    
    /**
     * Add a query
     *
     * @param   string  $query Query
     *
     * @return \Opis\Database\Connection
     */
    
    public function query($query)
    {
        $this->queries[] = $query;
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
            if(!empty($this->queries))
            {
                foreach($this->queries as $query)
                {
                    $this->pdo->exec($query);
                }
            }
        }
        return $this->pdo;
    }
    
    /**
     * Use persistent connections
     */
    
    public function persistent()
    {
        return $this->option(PDO::ATTR_PERSISTENT, true);
    }
    
    /**
     * Returns an instance of the compiler associated with this connection
     *
     * @return \Opis\Database\SQL\Compiler
     */
    
    public function compiler()
    {
        return new \Opis\Database\SQL\Compiler();
    }
    
    public function schemaCompiler()
    {
        throw new \Exception('Schema not supported');
    }
    
    public function serialize()
    {
        return serialize(array(
            'username' => $this->username,
            'password' => $this->password,
            'log' => $this->log,
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
     * Creates a MariaDB specific connection
     *
     * @param   string  $username   Username
     * @param   string  $password   Password
     *
     * @return  \Opis\Database\DSN\MySQL
     */
    
    public static function mariaDB($username, $password)
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