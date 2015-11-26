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

class Where
{
    /** @var Query|WhereCondition */
    protected $condition;
    protected $column;
    protected $separator;
    protected $whereClause;

    /**
     * Where constructor.
     * @param WhereCondition|Query $condition
     */
    public function __construct(WhereCondition $condition)
    {
        $this->condition = $condition;
        $this->whereClause = $condition->getWhereClause();
    }

    /**
     * @param $column
     * @param $separator
     * @return $this
     */
    public function init($column, $separator)
    {
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param $value
     * @param $operator
     * @param bool|false $iscolumn
     * @return WhereCondition|Query
     */
    protected function addCondition($value, $operator, $iscolumn = false)
    {
        if($iscolumn && is_string($value))
        {
            $value = function(Expression $expr) use ($value){
                $expr->column($value);
            };
        }
        
        $this->whereClause->addCondition($this->column, $value, $operator, $this->separator);
        return $this->condition;
    }

    /**
     * @param $value1
     * @param $value2
     * @param $not
     * @return WhereCondition|Query
     */
    protected function addBetweenCondition($value1, $value2, $not)
    {
        $this->whereClause->addBetweenCondition($this->column, $value1, $value2, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param $pattern
     * @param $not
     * @return WhereCondition|Query
     */
    protected function addLikeCondition($pattern, $not)
    {
        $this->whereClause->addLikeCondition($this->column, $pattern, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param $value
     * @param $not
     * @return WhereCondition|Query
     */
    protected function addInCondition($value, $not)
    {
        $this->whereClause->addInCondition($this->column, $value, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param $not
     * @return WhereCondition|Query
     */
    public function addNullCondition($not)
    {
        $this->whereClause->addNullCondition($this->column, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function is($value, $iscolumn = false)
    {
        return $this->addCondition($value, '=', $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function isNot($value, $iscolumn = false)
    {
        return $this->addCondition($value, '!=', $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function lessThan($value, $iscolumn = false)
    {
        return $this->addCondition($value, '<', $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function greaterThan($value, $iscolumn = false)
    {
        return $this->addCondition($value, '>', $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function atLeast($value, $iscolumn = false)
    {
        return $this->addCondition($value, '>=', $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function atMost($value, $iscolumn = false)
    {
        return $this->addCondition($value, '<=', $iscolumn);
    }

    /**
     * @param $value1
     * @param $value2
     * @return Query
     */
    public function between($value1, $value2)
    {
        return $this->addBetweenCondition($value1, $value2, false);
    }

    /**
     * @param $value1
     * @param $value2
     * @return Query
     */
    public function notBetween($value1, $value2)
    {
        return $this->addBetweenCondition($value1, $value2, true);
    }

    /**
     * @param $value
     * @return Query
     */
    public function like($value)
    {
        return $this->addLikeCondition($value, false);
    }

    /**
     * @param $value
     * @return Query
     */
    public function notLike($value)
    {
        return $this->addLikeCondition($value, true);
    }

    /**
     * @param $value
     * @return Query
     */
    public function in($value)
    {
        return $this->addInCondition($value, false);
    }

    /**
     * @param $value
     * @return Query
     */
    public function notIn($value)
    {
        return $this->addInCondition($value, true);
    }

    /**
     * @return Query
     */
    public function isNull()
    {
        return $this->addNullCondition(false);
    }

    /**
     * @return Query
     */
    public function notNull()
    {
        return $this->addNullCondition(true);
    }
    
    //Aliases

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function eq($value, $iscolumn = false)
    {
        return $this->is($value, $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function ne($value, $iscolumn = false)
    {
        return $this->isNot($value, $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function lt($value, $iscolumn = false)
    {
        return $this->lessThan($value, $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function gt($value, $iscolumn = false)
    {
        return $this->greaterThan($value, $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function gte($value, $iscolumn = false)
    {
        return $this->atLeast($value, $iscolumn);
    }

    /**
     * @param $value
     * @param bool|false $iscolumn
     * @return Query
     */
    public function lte($value, $iscolumn = false)
    {
        return $this->atMost($value, $iscolumn);
    }
    
}
