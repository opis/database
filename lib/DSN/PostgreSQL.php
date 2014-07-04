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

class PostgreSQL extends DSN
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
     */
    
    public function __construct()
    {
        parent::__construct('pgsql');
    }
    
    /**
     * Sets the client character set.
     *
     * @access  public
     *
     * @param   string  $value   Character set
     *
     * @return  \Opis\Database\DSN\PostgreSQL    Self reference
     */
    
    public function charset($value)
    {
        return $this;
    }
    
}
