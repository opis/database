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
    
    protected $port;
    
    protected $host;
    
    public function __construct($username = null, $password = null)
    {
        parent::__construct('oci', $username, $password);
    }
    
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
    
    public function compiler()
    {
        return new \Opis\Database\Compiler\Oracle();
    }
    
    public function database($name)
    {
        $this->database = $name;
        return $this;
    }
    
    public function port($value)
    {
        $this->port = $value;
        return $this;
    }
    
    public function host($value)
    {
        $this->host = $value;
        return $this;
    }
    
    public function charset($value)
    {
        return $this->set('charset', $value);
    }
}
