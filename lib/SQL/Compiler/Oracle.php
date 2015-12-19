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
use Opis\Database\SQL\Expression;

class Oracle extends Compiler
{

    /**
     * @param   mixed   $value
     * 
     * @return  string
     */
    protected function wrap($value)
    {
        if ($value instanceof Expression) {
            return $this->handleExpressions($value->getExpressions());
        }

        $wrapped = array();

        foreach (explode('.', $value) as $segment) {
            if ($segment == '*') {
                $wrapped[] = $segment;
            } else {
                $wrapped[] = sprintf($this->wrapper, strtoupper($segment));
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * @param   array   $ordering
     * 
     * @return  string
     */
    protected function handleOrderings(array $ordering)
    {
        if (empty($ordering)) {
            return '';
        }

        $sql = array();

        foreach ($ordering as $order) {
            if ($order['nulls'] !== null) {
                $sql[] = $this->columns($order['columns']) . ' ' . $order['order'] . ' ' . $order['nulls'];
            } else {
                $sql[] = $this->columns($order['columns']) . ' ' . $order['order'];
            }
        }

        return ' ORDER BY ' . implode(', ', $sql);
    }

    /**
     * Compiles a SELECT query.
     *
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

        $sql = $select->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoinClauses());
        $sql .= $this->handleWheres($select->getWhereConditions());
        $sql .= $this->handleGroupings($select->getGroupClauses());
        $sql .= $this->handleOrderings($select->getOrderClauses());
        $sql .= $this->handleHavings($select->getHavingConditions());

        if ($offset === null) {
            return 'SELECT * FROM (' . $sql . ') M1 WHERE ROWNUM <= ' . $limit;
        }

        $limit += $offset;
        $offset++;

        return 'SELECT * FROM (SELECT M1.*, ROWNUM AS OPIS_ROWNUM FROM (' . $sql . ') M1 WHERE ROWNUM <= ' . $limit . ') WHERE OPIS_ROWNUM >= ' . $offset;
    }
}
