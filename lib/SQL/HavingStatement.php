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

class HavingStatament
{
    /** @var    SQLStatement */
    protected $sql;

    /** @var    HavingExpression */
    protected $expression;

    /**
     * HavingStatament constructor.
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
     * @param   string|Closure  $column
     * @param   Closure   $value
     * @param   string  $separator
     * 
     * @return  $this
     */
    protected function addCondition($column, Closure $value = null, $separator): self
    {
        if ($column instanceof Closure) {
            $this->sql->addHavingGroupCondition($column, $separator);
        } else {
            $value($this->expression->init($column, $separator));
        }
        return $this;
    }

    /**
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     * 
     * @return  $this
     */
    public function having($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'AND');
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     * 
     * @return  $this
     */
    public function andHaving($column, Closure $value = null): self
    {
        return $this->having($column, $value);
    }

    /**
     * @param   string  $column
     * @param   Closure $value  (optional)
     * 
     * @return  $this
     */
    public function orHaving($column, Closure $value = null): self
    {
        return $this->addCondition($column, $value, 'OR');
    }
}
