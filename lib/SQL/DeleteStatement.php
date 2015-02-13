<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

class DeleteStatement extends WhereJoinCondition
{
    protected $tables;
    
    protected $from;
    
    protected $sql;
    
    public function __construct(Compiler $compiler, $from, WhereClause $clause = null)
    {
        parent::__construct($compiler, $clause);
        
        if(!is_array($from))
        {
            $from = array($from);
        }
        
        $this->from = $from;
    }
    
    public function getTables()
    {
        return $this->tables;
    }
    
    public function getFrom()
    {
        return $this->from;
    }
    
    public function delete($tables = array())
    {
        if(!is_array($tables))
        {
            $tables = array($tables);
        }
        $this->tables = $tables;
    }
    
    public function __toString()
    {
        if($this->sql === null)
        {
            $this->sql = $this->compiler->delete($this);
        }
        return $this->sql;
    }
    
}
