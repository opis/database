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
    
    protected $properties = array(
        'host' => '127.0.0.1'
    );
    
    protected $port;
    
    public function __construct($username = null, $password = null)
    {
        parent::__construct('dblib', $username, $password);
    }
    
    public function compiler()
    {
        return new \Opis\Database\Compiler\SQLServer();
    }
    
    public function database($name)
    {
        return $this->setDatabase('dbname', $name);
    }
    
    public function host($name)
    {
        if($this->port !== null)
        {
            $name = $name . ':' . $this->port;
        }
        return $this->set('host', $name);
    }
    
    public function port($value)
    {
        $this->port = $value;
        $value = $this->properties['host'] . ':' . $value;
        return $this->set('host', $value);
    }
    
    public function charset($value)
    {
        return $this->set('charset', $value);
    }
    
    public function appName($value)
    {
        return $this->set('appname', $value);
    }
    
}
