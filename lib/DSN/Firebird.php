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

class Firebird extends AbstractDSN
{
    
    protected $database;
    
    protected $host;
    
    protected $port;
    
    /**
     * Constructor
     *
     * @access  public
     *
     */
    
    public function __construct($username = null, $password = null)
    {
        parent::__construct('firebird');
    }
    
    
    /**
     * Sets the name of the database.
     *
     * @access  public
     *
     * @param   string  $name   Database name
     *
     * @return  \Opis\Database\DSN\Firebird    Self reference
     */
        
    public function database($name)
    {
        $this->database = $name;
        return $this;
    }
    
    /**
     * Sets the hostname on which the database server resides
     *
     * @access  public
     *
     * @param   string  $name   Host's name
     *
     * @return  \Opis\Database\DSN\Firebird    Self reference
     */
    
    public function host($name)
    {
        $this->host = $host;
        return $this;
    }
    
    /**
     * Sets the port number where the database server is listening.
     *
     * @access  public
     *
     * @param   int     $value   Port
     *
     * @return  \Opis\Database\DSN\Firebird    Self reference
     */
    
    public function port($value)
    {
        $this->port = $value;
        return $this;
    }
    
    /**
     * Sets the client character set.
     *
     * @access  public
     *
     * @param   string  $value   Character set
     *
     * @return  \Opis\Database\DSN\Firebird    Self reference
     */
        
    public function charset($value)
    {
        return $this->set('charset', $value);
    }
    
    /**
     * Sets the SQL role name. 
     *
     * @access  public
     *
     * @param   string  $value   Role name
     *
     * @return  \Opis\Database\DSN\Firebird    Self reference
     */
        
    public function role($value)
    {
        return $this->set('role', $value);
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
            $dbname = $this->database;
            
            if($this->host !== null)
            {
                if($this->port !== null)
                {
                    $dbname = $this->host . '/' . $this->port . ':' . $dbname;
                }
                else
                {
                    $dbname = $this->host . ':' . $dbname;
                }
            }
            
            $tmp = $this->properties;
            
            $this->properties = array(
                'dbname' => $dbname,
            );
            
            $this->properties += $tmp;
            
            return parent::__toString();
        }
        
        return $this->dsn;
    }
    
}
