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
use PDOException;
use Opis\Database\Schema\CreateTable;
use Opis\Database\Schema\AlterTable;
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

    /**
     * Creates a new table
     *
     * @access  public
     *
     * @param   string      $table      Table name
     * @param   \Closure    $callback   A callback that will define table's fields and indexes
     */
       
    public function create($table, Closure $callback)
    {
        $compiler = $this->connection->schemaCompiler();
        
        $schema = new CreateTable($table);
        
        $callback($schema);
        
        foreach($compiler->create($schema) as $result)
        {
            $this->connection->command($result['sql'], $result['params']);
        }
    }
    
    /**
     * Alters a table's definition
     *
     * @access  public
     *
     * @param   string      $table      Table name
     * @param   \Closure    $callback   A callback that will add or remove fields or indexes
     */
    
    public function alter($table, Closure $callback)
    {
        $compiler = $this->connection->schemaCompiler();
        
        $schema = new AlterTable($table);
        
        $callback($schema);
        
        foreach($compiler->create($schema) as $result)
        {
            $this->connection->command($result['sql'], $result['params']);
        }
    }
    
    /**
     * Deletes a table
     *
     * @access  public
     *
     * @param   string  $table  Table name
     */
    
    public function drop($table)
    {
        $compiler = $this->connection->schemaCompiler();
        
        $result = $compiler->drop($table);
        
        $this->connection->command($result['sql'], $result['params']);
    }
    
    /**
     * Deletes all records from a table
     *
     * @access  public
     *
     * @param   string  $table  Table name
     */
    
    public function truncate($table)
    {
        $compiler = $this->connection->schemaCompiler();
        
        $result = $compiler->truncate($table);
        
        $this->connection->command($result['sql'], $result['params']);
    }
    
}
