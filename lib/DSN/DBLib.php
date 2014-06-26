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

namespace Opis\Database\DSN;

use Opis\Database\Connection;

class DBLib extends Connection
{
    
    /** @var    array   DSN properties */
    protected $properties = array(
        'host' => '127.0.0.1'
    );
    
    /** @var    int     Port. */
    protected $port;
    
    /**
     * Constructor
     *
     * @access  public
     *
     * @param   string  $driver     Driver (dblib, sybase or mssql)
     * @param   string  $username   (Optional) Username
     * @param   string  $password   (Optional) Password
     */
    
    public function __construct($driver, $username = null, $password = null)
    {
        parent::__construct($driver, $username, $password);
    }
    
    /**
     * Returns the compiler associated with this connection type.
     *
     * @access  public
     *
     * @return  \Opis\Database\Compiler\SQLServer
     */
    
    public function compiler()
    {
        return new \Opis\Database\Compiler\SQLServer();
    }
    
    /**
     * Sets the name of the database.
     *
     * @access  public
     *
     * @param   string  $name   Database name
     *
     * @return  \Opis\Database\DSN\DBLib    Self reference
     */
    
    public function database($name)
    {
        return $this->setDatabase('dbname', $name);
    }
    
    /**
     * Sets the hostname on which the database server resides
     *
     * @access  public
     *
     * @param   string  $name   Host's name
     *
     * @return  \Opis\Database\DSN\DBLib    Self reference
     */
    
    public function host($name)
    {
        if($this->port !== null)
        {
            $name = $name . ':' . $this->port;
        }
        return $this->set('host', $name);
    }
    
    /**
     * Sets the port number where the database server is listening.
     *
     * @access  public
     *
     * @param   int     $value   Port
     *
     * @return  \Opis\Database\DSN\DBLib    Self reference
     */
    
    public function port($value)
    {
        $this->port = $value;
        $value = $this->properties['host'] . ':' . $value;
        return $this->set('host', $value);
    }
    
    /**
     * Sets the client character set.
     *
     * @access  public
     *
     * @param   string  $value   Character set
     *
     * @return  \Opis\Database\DSN\DBLib    Self reference
     */
    
    public function charset($value)
    {
        return $this->set('charset', $value);
    }
    
    /**
     * Sets the application name (used in sysprocesses).
     *
     * Defaults to "PHP Generic DB-lib" or "PHP freetds".
     * 
     * @access  public
     *
     * @param   string  $value   Application name
     *
     * @return  \Opis\Database\DSN\DBLib    Self reference
     */
    
    public function appName($value)
    {
        return $this->set('appname', $value);
    }
    
}
