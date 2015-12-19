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
    /** @var    WhereCondition */
    protected $condition;

    /** @var    string */
    protected $column;

    /** @var    string */
    protected $separator;

    /** @var    WhereClause */
    protected $whereClause;

    public function __construct(WhereCondition $condition)
    {
        $this->condition = $condition;
        $this->whereClause = $condition->getWhereClause();
    }

    /**
     * @param   string  $column
     * @param   strin   $separator
     *
     * @return  $this
     */
    public function init($column, $separator)
    {
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed   $value
     * @param   string  $operator
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    protected function addCondition($value, $operator, $iscolumn = false)
    {
        if ($iscolumn && is_string($value)) {
            $value = function ($expr) use ($value) {
                $expr->column($value);
            };
        }

        $this->whereClause->addCondition($this->column, $value, $operator, $this->separator);
        return $this->condition;
    }

    /**
     * @param   int     $value1
     * @param   int     $value2
     * @param   bool    $not    
     *
     * @return  WhereCondition
     */
    protected function addBetweenCondition($value1, $value2, $not)
    {
        $this->whereClause->addBetweenCondition($this->column, $value1, $value2, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param   string  $pattern
     * @param   bool    $not
     *
     * @return  WhereCondition
     */
    protected function addLikeCondition($pattern, $not)
    {
        $this->whereClause->addLikeCondition($this->column, $pattern, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param   mixed   $value
     * @param   bool    $not
     *
     * @return  WhereCondition
     */
    protected function addInCondition($value, $not)
    {
        $this->whereClause->addInCondition($this->column, $value, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param   bool    $not
     *
     * @return  WhereCondition
     */
    public function addNullCondition($not)
    {
        $this->whereClause->addNullCondition($this->column, $this->separator, $not);
        return $this->condition;
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function is($value, $iscolumn = false)
    {
        return $this->addCondition($value, '=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function isNot($value, $iscolumn = false)
    {
        return $this->addCondition($value, '!=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function lessThan($value, $iscolumn = false)
    {
        return $this->addCondition($value, '<', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function greaterThan($value, $iscolumn = false)
    {
        return $this->addCondition($value, '>', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function atLeast($value, $iscolumn = false)
    {
        return $this->addCondition($value, '>=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function atMost($value, $iscolumn = false)
    {
        return $this->addCondition($value, '<=', $iscolumn);
    }

    /**
     * @param   int $value1
     * @param   int $value2
     *
     * @return  WhereCondition
     */
    public function between($value1, $value2)
    {
        return $this->addBetweenCondition($value1, $value2, false);
    }

    /**
     * @param   int $value1
     * @param   int $value2
     *
     * @return  WhereCondition
     */
    public function notBetween($value1, $value2)
    {
        return $this->addBetweenCondition($value1, $value2, true);
    }

    /**
     * @param   string  $value
     *
     * @return  WhereCondition
     */
    public function like($value)
    {
        return $this->addLikeCondition($value, false);
    }

    /**
     * @param   string  $value
     *
     * @return  WhereCondition
     */
    public function notLike($value)
    {
        return $this->addLikeCondition($value, true);
    }

    /**
     * @param   array|Closure   $value
     *
     * @return  WhereCondition
     */
    public function in($value)
    {
        return $this->addInCondition($value, false);
    }

    /**
     * @param   array|Closure   $value
     *
     * @return  WhereCondition
     */
    public function notIn($value)
    {
        return $this->addInCondition($value, true);
    }

    /**
     * @return  WhereCondition
     */
    public function isNull()
    {
        return $this->addNullCondition(false);
    }

    /**
     * @return  WhereCondition
     */
    public function notNull()
    {
        return $this->addNullCondition(true);
    }
    //Aliases

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function eq($value, $iscolumn = false)
    {
        return $this->is($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function ne($value, $iscolumn = false)
    {
        return $this->isNot($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function lt($value, $iscolumn = false)
    {
        return $this->lessThan($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function gt($value, $iscolumn = false)
    {
        return $this->greaterThan($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function gte($value, $iscolumn = false)
    {
        return $this->atLeast($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereCondition
     */
    public function lte($value, $iscolumn = false)
    {
        return $this->atMost($value, $iscolumn);
    }
}
