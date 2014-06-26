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

class PostgreSQL extends Connection
{
    /** @var    array   DSN properties */
    protected $properties = array(
        'host' => 'localhost',
        'port' => '5432',
    );
        
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
        parent::__construct('pgsql', $username, $password);
    }
            
    /**
     * Sets the name of the database.
     *
     * @access  public
     *
     * @param   string  $name   Database name
     *
     * @return  \Opis\Database\DSN\PostgreSQL    Self reference
     */
    
    public function database($name)
    {
        return $this->setDatabase('dbase', $name);
    }
    
    /**
     * Sets the hostname on which the database server resides
     *
     * @access  public
     *
     * @param   string  $name   Host's name
     *
     * @return  \Opis\Database\DSN\PostgreSQL    Self reference
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
     * @return  \Opis\Database\DSN\PostgreSQL    Self reference
     */
    
    public function port($value)
    {
        return $this->set('port', $value);
    }
}
