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

class HavingStatement
{
    /** @var    SQLStatement */
    protected $sql;

    /** @var    HavingExpression */
    protected $expression;

    /**
     * HavingStatement constructor.
     * @param SQLStatement|null $statement
     */
    public function __construct(SQLStatement $statement = null)
    {
        if ($statement === null) {
            $statement = new SQLStatement();
        }
        $this->sql = $statement;
        $this->expression = new HavingExpression($statement);
    }

    /**
     * @param   string|Expression|Closure $column
     * @param   Closure $value
     * @param   string $separator
     *
     * @return  $this
     */
    protected function addCondition($column, Closure $value = null, $separator = 'AND'): self
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

    /**
     * @internal
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    /**
     * @param   string|Expression|Closure $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function having($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'AND');
    }

    /**
     * @param   string|Expression $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function andHaving($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'AND');
    }

    /**
     * @param   string|Expression $column
     * @param   Closure $value (optional)
     *
     * @return  $this
     */
    public function orHaving($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'OR');
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
        $this->expression = new HavingExpression($this->sql);
    }
}
