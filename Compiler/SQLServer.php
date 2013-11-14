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

namespace Opis\Database\Compiler;

use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\Query;

class SQLServer extends Compiler
{

    /** @var string Date format. */
    protected $dateForamt = 'Y-m-d H:i:s.0000000';

    /** @var string Wrapper used to escape table and column names. */
    protected $wrapper = '[%s]';


    /**
     * Compiles a SELECT query.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Query    $query  Query object.
     * @return  array
     */

    public function select(Query $query)
    {
        if($query->getLimit() === null)
        {
            // No limit so we can just execute a normal query
            return parent::select($query);
        }
        else
        {
            if($query->getOffset() === null)
            {
                // No offset so we can just use the TOP clause
                $sql  = $query->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
                $sql .= 'TOP ' . $query->getLimit() . ' ';
                $sql .= $this->columns($query->getColumns());
                $sql .= ' FROM ';
                $sql .= $this->wrap($query->getTable());
                $sql .= $this->joins($query->getJoins());
                $sql .= $this->wheres($query->getWheres());
                $sql .= $this->groupings($query->getGroupings());
                $sql .= $this->orderings($query->getOrderings());
                $sql .= $this->havings($query->getHavings());
            }
            else
            {
                // There is an offset so we need to emulate the OFFSET clause with ANSI-SQL
                $order = trim($this->orderings($query->getOrderings()));
                if(empty($order))
                {
                    $order = 'ORDER BY (SELECT 0)';
                }
                $sql  = $query->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
                $sql .= $this->columns($query->getColumns());
                $sql .= ', ROW_NUMBER() OVER (' . $order . ') AS opis_rownum';
                $sql .= ' FROM ';
                $sql .= $this->wrap($query->getTable());
                $sql .= $this->joins($query->getJoins());
                $sql .= $this->wheres($query->getWheres());
                $sql .= $this->groupings($query->getGroupings());
                $sql .= $this->havings($query->getHavings());
                $limit  = $query->getOffset() + $query->getLimit();
                $offset = $query->getOffset() + 1;
                $sql = 'SELECT * FROM (' . $sql . ') AS m1 WHERE opis_rownum BETWEEN ' . $offset . ' AND ' . $limit;
            }
            return array('sql' => $sql, 'params' => $this->params);
        }
    }
}