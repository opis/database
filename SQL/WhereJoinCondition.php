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

class WhereJoinCondition extends WhereCondition
{
    protected $joins = array();
    
    public function __construct(Compiler $compiler, Where $where = null)
    {
        parent::__construct($compiler, $where);
    }
    
    public function getJoinClauses()
    {
        return $this->joins;
    }
    
    protected function addJoinClause($type, $table, $column1, $column2, $operator, $closure)
    {
        $join = new Join();
        
        $join->andOn($column1, $column2, $operator);
        
        if($closure != null)
        {
            $closure($join);
        }
        if(!is_array($table))
        {
            $table = array($table);
        }
        $this->joins[] = array(
            'type' => $type,
            'table' => $table,
            'join' => $join,
        );
        
        return $this;
    }
    
    public function join($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('INNER', $table, $column1, $column2, $operator, $closure);
    }
    
    public function leftJoin($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('LEFT', $table, $column1, $column2, $operator, $closure);
    }
    
    public function rightJoin($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('RIGHT', $table, $column1, $column2, $operator, $closure);
    }
    
    public function fullJoin($table, $column1, $column2, $operator = '=', Closure $closure = null)
    {
        return $this->addJoinClause('FULL', $table, $column1, $column2, $operator, $closure);
    }
}