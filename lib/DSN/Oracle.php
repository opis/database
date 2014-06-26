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

class Oracle extends Connection
{
    /** @var    int     Port. */
    protected $port;
    
    /** @var    string  Host name. */
    protected $host;
        
    /**
     * Constructor
     *
     * @access  public
     *
     * @param   string  $username   (Optional) Username
     * @param   string  $password   (Optional) Password
     */
    
    public function __construct($username = null, $password = null)
    {
        parent::__construct('oci', $username, $password);
    }
    
    /**
     * Generates the DSN associated with this connection
     *
     * @return  string
     */
    
    public function dsn()
    {
        if($this->dsn !== null)
        {
            $value = $this->database;
            
            if($this->host != null)
            {
                if($this->port != null)
                {
                    $value = '//' . $this->host . ':' . $this->port . '/' . $value;
                }
                else
                {
                    $value = '//' . $this->host . '/' . $value;
                }
            }
            
            $this->set('dbname', $value);
        }
        
        return parent::dsn();
    }
        
    /**
     * Returns the compiler associated with this connection type.
     *
     * @access  public
     *
     * @return  \Opis\Database\Compiler\Oracle
     */
    
    public function compiler()
    {
        return new \Opis\Database\Compiler\Oracle();
    }
        
    /**
     * Sets the name of the database.
     *
     * @access  public
     *
     * @param   string  $name   Database name
     *
     * @return  \Opis\Database\DSN\Oracle    Self reference
     */
    
    public function database($name)
    {
        $this->database = $name;
        return $this;
    }
        
    /**
     * Sets the port number where the database server is listening.
     *
     * @access  public
     *
     * @param   int     $value   Port
     *
     * @return  \Opis\Database\DSN\Oracle    Self reference
     */
    
    public function port($value)
    {
        $this->port = $value;
        return $this;
    }
        
    /**
     * Sets the hostname on which the database server resides
     *
     * @access  public
     *
     * @param   string  $name   Host's name
     *
     * @return  \Opis\Database\DSN\Oracle    Self reference
     */
    
    public function host($value)
    {
        $this->host = $value;
        return $this;
    }
        
    /**
     * Sets the client character set.
     *
     * @access  public
     *
     * @param   string  $value   Character set
     *
     * @return  \Opis\Database\DSN\Oracle    Self reference
     */
    
    public function charset($value)
    {
        return $this->set('charset', $value);
    }
}
