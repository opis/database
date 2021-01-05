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

class HavingStatement
{
    protected ?SQLStatement $sql;
    protected HavingExpression $expression;

    public function __construct(SQLStatement $statement = null)
    {
        $this->sql = $statement ?? new SQLStatement();
        $this->expression = new HavingExpression($this->sql);
    }

    /**
     * @internal
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    public function having(mixed $column, ?Closure $value = null): static
    {
        return $this->addCondition($column, $value);
    }

    public function andHaving(mixed $column, ?Closure $value = null): static
    {
        return $this->addCondition($column, $value);
    }

    public function orHaving(mixed $column, ?Closure $value = null): static
    {
        return $this->addCondition($column, $value, 'OR');
    }

    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->expression = new HavingExpression($this->sql);
    }

    protected function addCondition(mixed $column, ?Closure $value = null, string $separator = 'AND'): static
    {
        if (($column instanceof Closure) && $value === null) {
            $this->sql->addHavingGroupCondition($column, $separator);
        } else {
            $expr = $this->expression->init($column, $separator);
            if ($value) {
                $value($expr);
            }
        }
        return $this;
    }
}
