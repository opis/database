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

class IBM extends DSN
{
    /** @var    array   DSN properties */
    protected $properties = array(
        'DIRIVER' => '{IBM DB2 ODBC DRIVER}',
        'PROTOCOL' => 'TCPIP',
    );
    
    /**
     * Constructor
     *
     * @access  public
     */
    
    public function __construct()
    {
        parent::__construct('ibm');
    }
        
    /**
     * Sets the name of the database.
     *
     * @access  public
     *
     * @param   string  $name   Database name
     *
     * @return  \Opis\Database\DSN\IBM    Self reference
     */
    
    public function database($name)
    {
        return $this->set('DATABASE', $name);
    }
        
    /**
     * Sets the hostname on which the database server resides
     *
     * @access  public
     *
     * @param   string  $name   Host's name
     *
     * @return  \Opis\Database\DSN\IBM    Self reference
     */
    
    public function host($name)
    {
        return $this->set('HOSTNAME', $name);
    }
        
    /**
     * Sets the port number where the database server is listening.
     *
     * @access  public
     *
     * @param   int     $value   Port
     *
     * @return  \Opis\Database\DSN\IBM    Self reference
     */
    
    public function port($value)
    {
        return $this->set('PORT', $value);
    }
    
    /**
     * Sets the client character set.
     *
     * @access  public
     *
     * @param   string  $value   Character set
     *
     * @return  \Opis\Database\DSN\IBM    Self reference
     */
    
    public function charset($value)
    {
        return $this;
    }
    
}
