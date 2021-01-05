<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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
    protected ?SQLStatement $sql;
    protected Where $where;

    public function __construct(?SQLStatement $statement = null)
    {
        $this->sql = $statement ?? new SQLStatement();
        $this->where = new Where($this, $this->sql);
    }

    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    public function where(mixed $column, bool $isExpression = false): static|Where
    {
        return $this->addWhereCondition($column, 'AND', $isExpression);
    }

    public function andWhere(mixed $column, bool $isExpression = false): static|Where
    {
        return $this->addWhereCondition($column, 'AND', $isExpression);
    }

    public function orWhere(mixed $column, bool $isExpression = false): static|Where
    {
        return $this->addWhereCondition($column, 'OR', $isExpression);
    }

    public function whereExpression(Closure $expression): Where
    {
        return $this->addWhereCondition($expression, 'AND', true);
    }

    public function andWhereExpression(Closure $expression): Where
    {
        return $this->addWhereCondition($expression, 'AND', true);
    }

    public function orWhereExpression(Closure $expression): Where
    {
        return $this->addWhereCondition($expression, 'OR', true);
    }

    public function whereExists(Closure $select): static
    {
        return $this->addWhereExistCondition($select);
    }

    public function andWhereExists(Closure $select): static
    {
        return $this->addWhereExistCondition($select);
    }

    public function orWhereExists(Closure $select): static
    {
        return $this->addWhereExistCondition($select, 'OR');
    }

    public function whereNotExists(Closure $select): static
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    public function andWhereNotExists(Closure $select): static
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    public function orWhereNotExists(Closure $select): static
    {
        return $this->addWhereExistCondition($select, 'OR', true);
    }

    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->where = new Where($this, $this->sql);
    }

    protected function addWhereCondition(mixed $column, string $separator = 'AND', bool $isExpr = false): static|Where
    {
        if (!$isExpr && ($column instanceof Closure)) {
            $this->sql->addWhereConditionGroup($column, $separator);
            return $this;
        }

        return $this->where->init($column, $separator);
    }

    protected function addWhereExistCondition(Closure $select, string $separator = 'AND', bool $not = false): static
    {
        $this->sql->addWhereExistsCondition($select, $separator, $not);
        return $this;
    }
}