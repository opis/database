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

class WhereClause
{
    protected $conditions = array();
    protected $compiler;

    /**
     * WhereClause constructor.
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @param Closure $callback
     * @param $separator
     * @return $this
     */
    public function addConditionGroup(Closure $callback, $separator)
    {
        $condition = new WhereCondition($this->compiler);
        $callback($condition);
        $this->conditions[] = array(
            'type' => 'whereNested',
            'clause' => $condition->getWhereClause(),
            'separator' => $separator
        );
        return $this;
    }

    /**
     * @param $column
     * @param $value
     * @param $operator
     * @param $separator
     * @return $this
     */
    public function addCondition($column, $value, $operator, $separator)
    {
        if ($value instanceof Closure) {
            $expr = new Expression($this->compiler);
            $value($expr);
            $this->conditions[] = array(
                'type' => 'whereColumn',
                'column' => $column,
                'value' => $expr,
                'operator' => $operator,
                'separator' => $separator,
            );
        } else {
            $this->conditions[] = array(
                'type' => 'whereColumn',
                'column' => $column,
                'value' => $value,
                'operator' => $operator,
                'separator' => $separator,
            );
        }

        return $this;
    }

    /**
     * @param $column
     * @param $pattern
     * @param $separator
     * @param $not
     * @return $this
     */
    public function addLikeCondition($column, $pattern, $separator, $not)
    {
        $this->conditions[] = array(
            'type' => 'whereLike',
            'column' => $column,
            'pattern' => $pattern,
            'separator' => $separator,
            'not' => $not,
        );
        return $this;
    }

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @param $separator
     * @param $not
     * @return $this
     */
    public function addBetweenCondition($column, $value1, $value2, $separator, $not)
    {
        $this->conditions[] = array(
            'type' => 'whereBetween',
            'column' => $column,
            'value1' => $value1,
            'value2' => $value2,
            'separator' => $separator,
            'not' => $not,
        );

        return $this;
    }

    /**
     * @param $column
     * @param $value
     * @param $separator
     * @param $not
     * @return $this
     */
    public function addInCondition($column, $value, $separator, $not)
    {
        if ($value instanceof Closure) {
            $select = new Subquery($this->compiler);
            $value($select);
            $this->conditions[] = array(
                'type' => 'whereInSelect',
                'column' => $column,
                'subquery' => $select,
                'separator' => $separator,
                'not' => $not,
            );
        } else {
            $this->conditions[] = array(
                'type' => 'whereIn',
                'column' => $column,
                'value' => $value,
                'separator' => $separator,
                'not' => $not,
            );
        }
        return $this;
    }

    /**
     * @param $column
     * @param $separator
     * @param $not
     * @return $this
     */
    public function addNullCondition($column, $separator, $not)
    {
        $this->conditions[] = array(
            'type' => 'whereNull',
            'column' => $column,
            'separator' => $separator,
            'not' => $not,
        );
        return $this;
    }

    /**
     * @param $closure
     * @param $separator
     * @param $not
     * @return $this
     */
    public function addExistsCondition($closure, $separator, $not)
    {
        $select = new Subquery($this->compiler);
        $closure($select);

        $this->conditions[] = array(
            'type' => 'whereExists',
            'subquery' => $select,
            'separator' => $separator,
            'not' => $not,
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getWhereConditions()
    {
        return $this->conditions;
    }
}
