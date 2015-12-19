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

namespace Opis\Database\SQL;

use Closure;

class WhereCondition
{
    protected $whereClause;
    protected $where;
    protected $compiler;

    /**
     * WhereCondition constructor.
     * @param Compiler $compiler
     * @param WhereClause|null $clause
     */
    public function __construct(Compiler $compiler, WhereClause $clause = null)
    {
        $this->compiler = $compiler;

        if ($clause === null) {
            $clause = new WhereClause($compiler);
        }

        $this->whereClause = $clause;
        $this->where = new Where($this);
    }

    /**
     * @return WhereClause
     */
    public function getWhereClause()
    {
        return $this->whereClause;
    }

    /**
     * @return array
     */
    public function getWhereConditions()
    {
        return $this->whereClause->getWhereConditions();
    }

    /**
     * @param $column
     * @param $separator
     * @return $this|Where
     */
    protected function addWhereCondition($column, $separator)
    {
        if ($column instanceof Closure) {
            $this->whereClause->addConditionGroup($column, $separator);
            return $this;
        }

        return $this->where->init($column, $separator);
    }

    /**
     * @param Closure $select
     * @param $seperator
     * @param $not
     * @return $this
     */
    protected function addExistsCondition(Closure $select, $seperator, $not)
    {
        $this->whereClause->addExistsCondition($select, $seperator, $not);
        return $this;
    }

    /**
     * @param $column
     * @return WhereCondition|Where
     */
    public function where($column)
    {
        return $this->addWhereCondition($column, 'AND');
    }

    /**
     * @param $column
     * @return WhereCondition|Where
     */
    public function andWhere($column)
    {
        return $this->where($column);
    }

    /**
     * @param $column
     * @return WhereCondition|Where
     */
    public function orWhere($column)
    {
        return $this->addWhereCondition($column, 'OR');
    }

    /**
     * @param Closure $select
     * @return WhereCondition
     */
    public function whereExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'AND', false);
    }

    /**
     * @param Closure $select
     * @return WhereCondition
     */
    public function andWhereExists(Closure $select)
    {
        return $this->whereExists($select);
    }

    /**
     * @param Closure $select
     * @return WhereCondition
     */
    public function orWhereExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'OR', false);
    }

    /**
     * @param Closure $select
     * @return WhereCondition
     */
    public function whereNotExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return WhereCondition
     */
    public function andWhereNotExists(Closure $select)
    {
        return $this->whereNotExists($select);
    }

    /**
     * @param Closure $select
     * @return WhereCondition
     */
    public function orWhereNotExists(Closure $select)
    {
        return $this->addExistsCondition($select, 'OR', true);
    }
}
