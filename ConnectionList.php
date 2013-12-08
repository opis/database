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

namespace Opis\Database;

class ConnectionList
{
    protected $connections = array();
    
    protected $defaultConnection;
    
    public function add($name, Connection $connection, $default = false)
    {
        $this->connections[$name] = $connection;
        if($default || $this->defaultConnection === null)
        {
            $this->defaultConnection = $name;
        }
        return $this;
    }
    
    public function get($name = null)
    {
        if($name === null)
        {
            $name = $this->defaultConnection;
        }
        
        return isset($this->connections[$name]) ? $this->connections[$name] : null;
    }
}