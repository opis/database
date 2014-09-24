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

use Opis\Database\DSN\MySQL;
use Opis\Database\DSN\DBLib;
use Opis\Database\DSN\Firebird;
use Opis\Database\DSN\IBM;
use Opis\Database\DSN\Oracle;
use Opis\Database\DSN\PostgreSQL;
use Opis\Database\DSN\SQLite;
use Opis\Database\DSN\SQLServer;

class DSN
{
    
    /** @var    array   DSN properties */
    protected $properties = array();
    
    /** @var    string  DSN prefix */
    protected $prefix;
    
    /** @var    string Builded DSN */
    protected $dsn;
    
    /**
     * Constructor
     *
     * @access  protected
     *
     * @param   string  $prefix     DSN prefix
     */
    
    protected function __construct($prefix)
    {
        $this->prefix = $prefix;
    }
    
    /**
     * Sets a DSN option
     *
     * @access  protected
     *
     * @param   string      $key    Option name
     * @param   string|int  $value  Option value
     *
     * @return  \Opis\Database\DSN  Self reference
     */
    
    protected function set($key, $value)
    {
        $this->properties[$key] = $value;
        return $this;
    }
    
    /**
     * Sets the name of the database.
     *
     * @access  public
     *
     * @param   string  $name   Database name
     *
     * @return  \Opis\Database\DSN    Self reference
     */
    
    public function database($name)
    {
        return $this->set('dbname', $name);
    }
    
    /**
     * Sets the hostname on which the database server resides
     *
     * @access  public
     *
     * @param   string  $name   Host's name
     *
     * @return  \Opis\Database\DSN    Self reference
     */
    
    public function host($name)
    {
        return $this->set('host', $name);
    }
    
    /**
     * Sets the port number where the database server is listening.
     *
     * @access  public
     *
     * @param   int     $value   Port
     *
     * @return  \Opis\Database\DSN    Self reference
     */
    
    public function port($value)
    {
        return $this->set('port', $value);
    }
    
    /**
     * Sets the client character set.
     *
     * @access  public
     *
     * @param   string  $value   Character set
     *
     * @return  \Opis\Database\DSN    Self reference
     */
    
    public function charset($value)
    {
        return $this->set('charset', $value);
    }
    
    /**
     * Builds the DSN
     *
     * @return  string
     */
    
    public function __toString()
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
    
    /**
     * Creates a MySQL DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\MySQL
     */
    
    public static function mysql($database)
    {
        $instance = new MySQL();
        return $instance->database($database);
    }
    
    /**
     * Creates a PostgreSQL DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\PostgreSQL
     */
    
    public static function postgreSQL($database)
    {
        $instance = new PostgreSQL();
        return $instance->database($database);
    }
    
    /**
     * Creates a Microsoft SQL DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\SQLServer|\Opis\Database\DSN\DBLib
     */
    
    public static function sqlServer($database, $driver = 'dblib')
    {
        $driver = strtolower($driver);
        
        if(!in_array($driver, array('sqlsrv', 'mssql', 'dblib', 'sybase')))
        {
            throw new \Exception("Unknown driver $driver");
        }
        
        if($driver === 'sqlsrv')
        {
            $instance = new SQLServer();
        }
        else
        {
            $instance = new DBLib($driver);
        }
        
        return $instance->database($database);
    }
    
    /**
     * Creates an IBM DB2 DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\IBM
     */
        
    public static function ibm($database)
    {
        $instance = new IBM();
        return $instance->database($database);
    }
    
    /**
     * Creates an Oracle DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\Oracle
     */
    
    public static function oracle($database)
    {
        $instance = new Oracle();
        return $instance->database($database);
    }
    
    /**
     * Creates a Firebird DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\Firebird
     */
    
    public static function firebird($database)
    {
        $instance = new Firebird();
        return $instance->database($database);
    }
    
    /**
     * Creates an SQLite DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\SQLite
     */
    
    public static function sqlite($database = null)
    {
        return new SQLite($database);
    }
    
    /**
     * Creates an SQLite 2 DSN object
     *
     * @access  public
     *
     * @param   string  $database   Database name
     *
     * @return  \Opis\Database\DSN\SQLite
     */
    
    public static function sqlite2($database = null)
    {
        return new SQLite($database, '2');
    }
    
}
