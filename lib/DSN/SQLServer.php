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

class SQLServer extends Connection
{
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
        parent::__construct('sqlsrv', $username, $password);
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
     * @return  \Opis\Database\DSN\SQLServer    Self reference
     */
    
    public function database($name)
    {
        return $this->setDatabase('Database', $name);
    }
    
    /**
     * Sets the application name used in tracing.
     *
     * @access  public
     *
     * @param   string  $name   Application name
     *
     * @return  \Opis\Database\DSN\SQLServer    Self reference
     */
    
    public function app($name)
    {
        return $this->set('App', $name);
    }
    
    /**
     * Sets the name of the database server.
     *
     * @access  public
     *
     * @param   string  $name   Server name.
     * @param   int     $port   (Optional) Server port.
     *
     * @return  \Opis\Database\DSN\SQLServer    Self reference
     */
    
    public function server($name, $port = null)
    {
        if($port !== null)
        {
            $name .= ',' . $port; 
        }
        
        return $this->set('Server', $name);
    }
        
    /**
     * Specifies whether the connection is assigned from a connection pool or not. 
     *
     * @access  public
     *
     * @param   boolean $value  (Optional) Flag
     *
     * @return  \Opis\Database\DSN\SQLServer    Self reference
     */
    
    public function connectionPooling($value = true)
    {
        return $this->set('ConnectionPooling', $value ? 1 : 0);
    }
    
    /**
     * Specifies whether the communication with SQL Server is encrypted or unencrypted.
     *
     * @access  public
     *
     * @param   boolean $value  (Optional) Flag
     *
     * @return  \Opis\Database\DSN\SQLServer    Self reference
     */
    
    public function encrypt($value = true)
    {
        return $this->set('Encrypt', $value ? 1 : 0);
    }
    
}
