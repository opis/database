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

use Closure;
use Opis\Database\Schema\Create;
use Opis\Database\Schema\Alter;
use Opis\Database\Schema\Compiler;

class Schema
{
    /** @var    \Opis\Database\Connection   Connection. */
    protected $connection;
    
    /**
     * Constructor
     *
     * @access public
     *
     * @param   \Opis\Database\Connection   $connection Connection.
     */
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function create($table, Closure $callback)
    {
        $schema = new Create($table);
        $callback($schema);
        $compiler = new Compiler();
        return $compiler->create($schema);
    }
    
    public function alter($table, Closure $callback)
    {
        $schema = new Alter($table);
        $callback($schema);
        return $schema;
    }
    
}
