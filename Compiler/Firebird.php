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

class Firebird extends Compiler
{
	/**
	 * Compiles LIMIT clauses.
	 *
	 * @access  protected
	 * @param   int        $limit   Limit
	 * @param   int        $offset  Offset
	 * @return  string
	 */

	protected function limit($limit, $offset = null)
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

	protected function offset($offset, $limit = null)
	{
	    return ($offset === null) ? ($limit === null) ? '' :' ROWS 1 ' : ' ROWS ' . ($offset + 1);
	}

	/**
	 * Compiles a SELECT query.
	 *
	 * @access  public
	 * @param   \Opis\Database\SQL\Query    $query  Query object.
	 * @return  array
	 */

	public function select(Query $query)
	{
            $sql  = $query->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
            $sql .= $this->columns($query->getColumns());
            $sql .= ' FROM ';
            $sql .= $this->wrap($query->getTable());
            $sql .= $this->joins($query->getJoins());
            $sql .= $this->wheres($query->getWheres());
            $sql .= $this->groupings($query->getGroupings());
            $sql .= $this->orderings($query->getOrderings());
            $sql .= $this->havings($query->getHavings());
            $sql .= $this->offset($query->getOffset(), $query->getLimit());
            $sql .= $this->limit($query->getLimit(), $query->getOffset());
            return array('sql' => $sql, 'params' => $this->params);
	}
}