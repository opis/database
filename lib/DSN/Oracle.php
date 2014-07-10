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

use Opis\Database\DSN;

class Oracle extends DSN
{
    /** @var    int     Port. */
    protected $port;
    
    /** @var    string  Host name. */
    protected $host;
    
    /** @var    string  Database name. */
    protected $database;
        
    /**
     * Constructor
     *
     * @access  public
     */
    
    public function __construct()
    {
        parent::__construct('oci');
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
     * Builds the DSN
     *
     * @return  string
     */
    
    public function __toString()
    {
        if($this->dsn === null)
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
            $tmp = $this->properties;
            $this->properties = array(
                'dbname' => $value,
            );
            $this->properties += $tmp;
            
            return parent::__toString();
        }
        
        return $this->dsn;
    }
}
