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

class Firebird extends Compiler
{

    /**
     * Handle limits
     * @param int|null $limit
     * @param null $offset
     * @return string
     */
    protected function handleLimit($limit, $offset = null)
    {
        return ($limit <= 0) ? '' : ' TO ' . ($limit + (($offset < 0) ? 0 : $offset));
    }

    /**
     * Compiles OFFSET clause.
     *
     * @access  protected
     * @param   int $limit Offset
     * @param   int $offset Limit
     * @return  string
     */
    protected function handleOffset($offset, $limit = null)
    {
        return ($offset < 0) ? ($limit <= 0) ? '' : ' ROWS 1 ' : ' ROWS ' . ($offset + 1);
    }

    /**
     * Compiles a SELECT query.
     *
     * @access  public
     * @param   SQLStatement $select
     * @return  string
     */
    public function select(SQLStatement $select): string
    {
        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleOrderings($select->getOrder());
        $sql .= $this->handleHavings($select->getHaving());
        $sql .= $this->handleOffset($select->getOffset(), $select->getLimit());
        $sql .= $this->handleLimit($select->getLimit(), $select->getOffset());

        return $sql;
    }
}
