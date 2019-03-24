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

namespace Opis\Database\SQL;

use Closure;

class WhereStatement
{
    /** @var SQLStatement */
    protected $sql;

    /** @var Where */
    protected $where;

    /**
     * WhereStatement constructor.
     * @param SQLStatement|null $statement
     */
    public function __construct(SQLStatement $statement = null)
    {
        if ($statement === null) {
            $statement = new SQLStatement();
        }

        $this->sql = $statement;
        $this->where = new Where($this, $statement);
    }

    /**
     * @param $column
     * @param string $separator
     * @param bool $isExpr
     * @return WhereStatement|Where
     */
    protected function addWhereCondition($column, string $separator = 'AND', bool $isExpr = false)
    {
        if (($column instanceof Closure) && !$isExpr) {
            $this->sql->addWhereConditionGroup($column, $separator);
            return $this;
        }

        return $this->where->init($column, $separator);
    }

    /**
     * @param Closure $select
     * @param string $separator
     * @param bool $not
     * @return WhereStatement
     */
    protected function addWhereExistCondition(Closure $select, string $separator = 'AND', bool $not = false): self
    {
        $this->sql->addWhereExistsCondition($select, $separator, $not);
        return $this;
    }

    /**
     * @internal
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    /**
     * @param string|Closure|Expression $column
     * @param bool $isExpr
     * @return Where|Delete|Select|Update
     */
    public function where($column, bool $isExpr = false)
    {
        return $this->addWhereCondition($column, 'AND', $isExpr);
    }

    /**
     * @param string|Closure|Expression $column
     * @param bool $isExpr
     * @return Where|Delete|Select|Update
     */
    public function andWhere($column, bool $isExpr = false)
    {
        return $this->addWhereCondition($column, 'AND', $isExpr);
    }

    /**
     * @param string|Closure|Expression $column
     * @param bool $isExpr
     * @return Where|Delete|Select|Update
     */
    public function orWhere($column, bool $isExpr = false)
    {
        return $this->addWhereCondition($column, 'OR', $isExpr);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function whereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function andWhereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function orWhereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'OR');
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function whereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function andWhereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return WhereStatement|Where|Delete|Select|Update
     */
    public function orWhereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'OR', true);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->where = new Where($this, $this->sql);
    }
}