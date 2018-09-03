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
use Opis\Database\SQL\Expression;
use Opis\Database\SQL\SQLStatement;

class Oracle extends Compiler
{

    /**
     * Compiles a SELECT query.
     *
     * @param   SQLStatement $select
     *
     * @return  string
     */
    public function select(SQLStatement $select): string
    {
        $limit = $select->getLimit();

        if ($limit <= 0) {
            return parent::select($select);
        }

        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleOrderings($select->getOrder());
        $sql .= $this->handleHavings($select->getHaving());

        $offset = $select->getOffset();

        if ($offset < 0) {
            return 'SELECT * FROM (' . $sql . ') M1 WHERE ROWNUM <= ' . $limit;
        }

        $limit += $offset;
        $offset++;

        return 'SELECT * FROM (SELECT M1.*, ROWNUM AS OPIS_ROWNUM FROM (' . $sql . ') M1 WHERE ROWNUM <= ' . $limit . ') WHERE OPIS_ROWNUM >= ' . $offset;
    }

    /**
     * @param   mixed $value
     *
     * @return  string
     */
    protected function wrap($value)
    {
        if ($value instanceof Expression) {
            return $this->handleExpressions($value->getExpressions());
        }

        $wrapped = [];

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
     * @param   array $ordering
     *
     * @return  string
     */
    protected function handleOrderings(array $ordering)
    {
        if (empty($ordering)) {
            return '';
        }

        $sql = [];

        foreach ($ordering as $order) {
            if ($order['nulls'] !== null) {
                $sql[] = $this->columns($order['columns']) . ' ' . $order['order'] . ' ' . $order['nulls'];
            } else {
                $sql[] = $this->columns($order['columns']) . ' ' . $order['order'];
            }
        }

        return ' ORDER BY ' . implode(', ', $sql);
    }
}
