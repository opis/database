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
    protected ?SQLStatement $sql;
    protected Where $where;

    public function __construct(SQLStatement $statement = null)
    {
        if ($statement === null) {
            $statement = new SQLStatement();
        }

        $this->sql = $statement;
        $this->where = new Where($this, $statement);
    }

    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    public function where(mixed $column, bool $isExpr = false): static|Where
    {
        return $this->addWhereCondition($column, 'AND', $isExpr);
    }

    public function andWhere(mixed $column, bool $isExpr = false): static|Where
    {
        return $this->addWhereCondition($column, 'AND', $isExpr);
    }

    public function orWhere(mixed $column, bool $isExpr = false): static|Where
    {
        return $this->addWhereCondition($column, 'OR', $isExpr);
    }

    public function whereExists(Closure $select): static|Where
    {
        return $this->addWhereExistCondition($select);
    }

    public function andWhereExists(Closure $select): static|Where
    {
        return $this->addWhereExistCondition($select);
    }

    public function orWhereExists(Closure $select): static|Where
    {
        return $this->addWhereExistCondition($select, 'OR');
    }

    public function whereNotExists(Closure $select): static|Where
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    public function andWhereNotExists(Closure $select): static|Where
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    public function orWhereNotExists(Closure $select): static|Where
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
        if (($column instanceof Closure) && !$isExpr) {
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