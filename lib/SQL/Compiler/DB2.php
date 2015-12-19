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

class DB2 extends Compiler
{

    /**
     * Returns the SQL for a select statement
     * 
     * @param   \Opis\Database\SQL\SelectStatement  $select
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
}
