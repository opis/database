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

namespace Opis\Database\SQL;

use Opis\Database\Database;

class Query extends WhereJoinCondition
{
    protected $database;
    
    protected $tables;
    
    public function __construct(Database $database, $tables)
    {
        parent::__construct($database->getCompiler());
        $this->tables =  $tables;
        $this->database = $database;
    }
    
    protected function buildSelect()
    {
        return new Select($this->database, $this->compiler, $this->tables, $this->joins, $this->where);
    }
    
    protected function buildDelete()
    {
        return new Delete($this->database, $this->compiler, $this->tables, $this->joins, $this->where);
    }
    
    public function distinct($value = true)
    {
        return $this->buildSelect()->distinct($value);
    }
    
    public function groupBy($columns)
    {
        return $this->buildSelect()->groupBy($columns);
    }
    
    public function having($column, $value, $operator = '=')
    {
        return $this->buildSelect()->having($column, $value, $operator);
    }
    
    public function orHaving($column, $value, $operator = '=')
    {
        return $this->buildSelect()->orHaving($column, $value, $operator);
    }
    
    public function orderBy($columns, $order = 'ASC')
    {
        return $this->buildSelect()->orderBy($columns, $order);
    }
    
    public function limit($value)
    {
        return $this->buildSelect()->limit($value);
    }
    
    public function offset($value)
    {
        return $this->buildSelect()->offset($value);
    }
    
    public function into($table, $database = null)
    {
        return $this->buildSelect()->into($table, $database);
    }
    
    public function select($columns = array())
    {
        return $this->buildSelect()->select($columns);
    }
    
    public function first($columns = array())
    {
        return $this->buildSelect()->select($columns);
    }
    
    public function count($column = '*',  $distinct = false)
    {
        return $this->buildSelect()->count($column, $distinct);
    }
    
    public function avg($column, $distinct = false)
    {
        return $this->buildSelect()->avg($column, $distinct);
    }
    
    public function sum($column, $distinct  = false)
    {
        return $this->buildSelect()->sum($column, $distinct);
    }
    
    public function min($column, $distinct = false)
    {
        return $this->buildSelect()->min($column, $distinct);
    }
    
    public function max($column, $distinct = false)
    {
        return $this->buildSelect()->max($column, $distinct);
    }
    
    public function delete($tables = array())
    {
        return $this->buildDelete()->delete($tables);
    }
    
}
