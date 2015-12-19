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
use Opis\Database\SQL\UpdateStatement;

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
     * @param   SelectStatement $select
     * 
     * @return  string
     */
    public function select(SelectStatement $select)
    {
        $limit = $select->getLimit();
        $offset = $select->getOffset();

        if ($limit === null && $offset === null) {
            return parent::select($select);
        }

        if ($offset === null) {
            $sql = $select->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
            $sql .= 'TOP ' . $limit . ' ';
            $sql .= $this->handleColumns($select->getColumns());
            $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
            $sql .= ' FROM ';
            $sql .= $this->handleTables($select->getTables());
            $sql .= $this->handleJoins($select->getJoinClauses());
            $sql .= $this->handleWheres($select->getWhereConditions());
            $sql .= $this->handleGroupings($select->getGroupClauses());
            $sql .= $this->handleOrderings($select->getOrderClauses());
            $sql .= $this->handleHavings($select->getHavingConditions());

            return $sql;
        }

        $order = trim($this->handleOrderings($select->getOrderClauses()));

        if (empty($order)) {
            $order = 'ORDER BY (SELECT 0)';
        }

        $sql = $select->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= ', ROW_NUMBER() OVER (' . $order . ') AS opis_rownum';
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoinClauses());
        $sql .= $this->handleWheres($select->getWhereConditions());
        $sql .= $this->handleGroupings($select->getGroupClauses());
        $sql .= $this->handleHavings($select->getHavingConditions());

        if ($offset === null) {
            $offset = 0;
        }

        $limit += $offset;
        $offset++;

        return 'SELECT * FROM (' . $sql . ') AS m1 WHERE opis_rownum BETWEEN ' . $offset . ' AND ' . $limit;
    }

    /**
     * @param   UpdateStatement $update
     * 
     * @return  string
     */
    public function update(UpdateStatement $update)
    {
        $joins = $this->handleJoins($update->getJoinClauses());
        $tables = $update->getTables();

        if ($joins !== '') {
            $joins = ' FROM ' . $this->handleTables($tables) . ' ' . $joins;
            $tables = array_values($tables);
        }

        $sql = 'UPDATE ';
        $sql .= $this->handleTables($tables);
        $sql .= $this->handleSetColumns($update->getColumns());
        $sql .= $joins;
        $sql .= $this->handleWheres($update->getWhereConditions());

        return $sql;
    }
}
