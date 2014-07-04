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

class SQLite extends DSN
{
    
    protected $database;
    
    /**
     * Constructor
     *
     * @access  public
     *
     * @param   string  $path   (Optional) Database path
     * @param   string  $suffix (Optional) Driver suffix
     */
    
    public function __construct($path = null, $suffix = '')
    {
        parent::__construct('sqlite' . $suffix);
        
        if($path === null)
        {
            $path = ':memory:';
        }
        
        $this->database = $path;
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
            $this->dsn = $this->prefix . ':' . $this->database;
        }
        
        return $this->dsn;
    }
    
}
