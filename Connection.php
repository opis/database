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
use Opis\Database\DSN\MySQL as MySQLConnection;
use Opis\Database\DSN\PostgreSQL as PostgreSQLConnection;

class Connection
{
    protected $username = null;
    
    protected $password = null;
    
    protected $log = false;
    
    protected $queries = array();
    
    protected $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    );
    
    protected $prefix;
    
    protected $database;
    
    protected $properties = array();
    
    protected $compiler = null;
    
    protected $pdo = null;
    
    protected $dsn = null;
    
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }
    
    public function dbname()
    {
        return $this->database;
    }
    
    public function logQueries($value = true)
    {
        $this->log = $value;
        return $this;
    }
    
    public function loggingEnabled()
    {
        return $this->log;
    }
    
    public function query($query)
    {
        $this->queries[] = $query;
        return $this;
    }
    
    public function username($username)
    {
        $this->username = $username;
        return $this;
    }
    
    public function password($password)
    {
        $this->password = $password;
        return $this;
    }
    
    public function set($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }
    
    public function setDatabase($name, $value)
    {
        $this->database = $value;
        return $this->set($name, $value);
    }
    
    public function options(array $options)
    {
        foreach($options as $name => $value)
        {
            $this->option($name, $value);
        }
        return $this;
    }
    
    public function option($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }
    
    public function dsn()
    {
        if($this->dsn === null)
        {
            $tmp = array();
            foreach($this->properties as $key => $value)
            {
                $tmp[] = $key . '=' . $value;
            }
            $this->dsn = $this->prefix . ':' . implode(';', $tmp);
        }
        return $this->dsn;
    }
    
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
                throw new RuntimeException(vsprintf("%s(): Failed to connect to the '%s' database. %s", array(__METHOD__, $this->database, $e->getMessage())));
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
    
    public function compiler()
    {
        switch($this->prefix)
        {
            case 'mysql':
                return new \Opis\Database\Compiler\MySQL();
            case 'dblib':
            case 'mssql':
            case 'sqlsrv':
            case 'sybase':
                return new \Opis\Database\Compiler\SQLServer();
            case 'oci':
            case 'oracle':
                return new \Opis\Database\Compiler\Oracle();
            case 'firebird':
                return new \Opis\Database\Compiler\Firebird();
            case 'db2':
            case 'ibm':
            case 'odbc':
                return new \Opis\Database\Compiler\DB2();
            case 'nuodb':
                return new \Opis\Database\Compiler\NuoDB();
            default:
                return new \Opis\Database\SQL\Compiler();
        }
    }
    
    public static function create($prefix, $username = null, $password = null)
    {
        return (new Connection($prefix))->username($username)->password($password);
    }
    
    public static function dblib($username, $password)
    {
        return static::create('dblib', $username, $password);
    }
    
    public static function sybase($username, $password)
    {
        return static::create('sybase', $username, $password);
    }
    
    public static function mssql($username, $password)
    {
        return static::create('mssql', $username, $password);
    }
    
    public static function firebird($username, $password)
    {
        return static::create('firebird', $username, $password);
    }
    
    public static function ibm($username, $password)
    {
        return static::create('ibm', $username, $password);
    }
    
    public static function mysql($username, $password)
    {
        return (new MySQLConnection())->username($username)->password($password);
    }
    
    public static function sqlsrv($username, $password)
    {
        return static::create('sqlsrv', $name, $default);
    }
    
    public static function oci($username, $password)
    {
        return static::create('oci', $username, $password);
    }
    
    public static function odbc($username, $password)
    {
        return static::create('odbc', $username, $password);
    }
    
    public static function postgreSQL($username, $password)
    {
        return (new PostgreSQLConnection())->username($username)->password($password);
    }
    
    public static function sqlite($username = null, $password = null)
    {
        return static::create('sqlite', $username, $password);
    }
    
    public static function nuodb($username, $password)
    {
        return static::create('nuodb', $username, $password);
    }
}