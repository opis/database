<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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
use Opis\Database\SQL\SQLStatement;

class SQLServer extends Compiler
{
    /** @var string Date format. */
    protected $dateFormat = 'Y-m-d H:i:s.0000000';

    /** @var string Wrapper used to escape table and column names. */
    protected $wrapper = '[%s]';

    /**
     * Compiles a SELECT query
     *
     * @param SQLStatement $select
     * @return string
     */
    public function select(SQLStatement $select): string
    {
        $limit = $select->getLimit();

        if ($limit <= 0) {
            return parent::select($select);
        }

        $offset = $select->getOffset();

        if ($offset < 0) {
            $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
            $sql .= 'TOP ' . $limit . ' ';
            $sql .= $this->handleColumns($select->getColumns());
            $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
            $sql .= ' FROM ';
            $sql .= $this->handleTables($select->getTables());
            $sql .= $this->handleJoins($select->getJoins());
            $sql .= $this->handleWheres($select->getWheres());
            $sql .= $this->handleGroupings($select->getGroupBy());
            $sql .= $this->handleOrderings($select->getOrder());
            $sql .= $this->handleHavings($select->getHaving());

            return $sql;
        }

        $order = trim($this->handleOrderings($select->getOrder()));

        if (empty($order)) {
            $order = 'ORDER BY (SELECT 0)';
        }

        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= ', ROW_NUMBER() OVER (' . $order . ') AS opis_rownum';
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleHavings($select->getHaving());

        $limit += $offset;
        $offset++;

        return 'SELECT * FROM (' . $sql . ') AS m1 WHERE opis_rownum BETWEEN ' . $offset . ' AND ' . $limit;
    }

    /**
     * @param   SQLStatement $update
     *
     * @return  string
     */
    public function update(SQLStatement $update): string
    {
        $joins = $this->handleJoins($update->getJoins());
        $tables = $update->getTables();

        if ($joins !== '') {
            $joins = ' FROM ' . $this->handleTables($tables) . ' ' . $joins;
            $tables = array_values($tables);
        }

        $sql = 'UPDATE ';
        $sql .= $this->handleTables($tables);
        $sql .= $this->handleSetColumns($update->getColumns());
        $sql .= $joins;
        $sql .= $this->handleWheres($update->getWheres());

        return $sql;
    }
}
