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

namespace Opis\Database\SQL\Compiler;

use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\SelectStatement;

class Firebird extends Compiler
{

    /**
     * Handle limits
     * 
     * @param   int|null    $limit
     * 
     * @return  string
     */
    protected function handleLimit($limit, $offset = null)
    {
        return ($limit === null) ? '' : ' TO ' . ($limit + (($offset === null) ? 0 : $offset));
    }

    /**
     * Compiles OFFSET clause.
     *
     * @access  protected
     * @param   int        $limit   Offset
     * @param   int        $offset  Limit
     * @return  string
     */
    protected function hanleOffset($offset, $limit = null)
    {
        return ($offset === null) ? ($limit === null) ? '' : ' ROWS 1 ' : ' ROWS ' . ($offset + 1);
    }

    /**
     * Compiles a SELECT query.
     *
     * @access  public
     * @param   \Opis\Database\SQL\SelectStatement    $select  Select object.
     * @return  array
     */
    public function select(SelectStatement $select)
    {
        $sql = $select->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoinClauses());
        $sql .= $this->handleWheres($select->getWhereConditions());
        $sql .= $this->handleGroupings($select->getGroupClauses());
        $sql .= $this->handleOrderings($select->getOrderClauses());
        $sql .= $this->handleHavings($select->getHavingConditions());
        $sql .= $this->handleOffset($select->getOffset(), $select->getLimit());
        $sql .= $this->handleLimit($select->getLimit(), $select->getOffset());

        return $sql;
    }
}
