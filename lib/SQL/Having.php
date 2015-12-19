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

class Having
{
    /** @var    Compiler */
    protected $compiler;

    /** @var    HavingClause */
    protected $havingClause;

    /** @var    string */
    protected $aggregate;

    /** @var    string */
    protected $separator;

    /**
     * Constructor
     * 
     * @param   Compiler        $compiler
     * @param   HavingClause    $clause
     */
    public function __construct(Compiler $compiler, HavingClause $clause)
    {
        $this->compiler = $compiler;
        $this->havingClause = $clause;
    }

    /**
     * @param   mixed   $value
     * @param   string  $operator
     * @param   boolean $iscolumn
     */
    protected function addCondition($value, $operator, $iscolumn)
    {
        if ($iscolumn && is_string($value)) {
            $expr = new Expression($this->compiler);
            $value = $expr->column($value);
        }

        $this->havingClause->addCondition($this->aggregate, $value, $operator, $this->separator);
    }

    /**
     * @param   string  $aggregate
     * @param   string  $separator
     * 
     * @return  $this
     */
    public function init($aggregate, $separator)
    {
        $this->aggregate = $aggregate;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn (optional)
     */
    public function eq($value, $iscolumn = false)
    {
        $this->addCondition($value, '=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn (optional)
     */
    public function ne($value, $iscolumn = false)
    {
        $this->addCondition($value, '!=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn (optional)
     */
    public function lt($value, $iscolumn = false)
    {
        $this->addCondition($value, '<', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn (optional)
     */
    public function gt($value, $iscolumn = false)
    {
        $this->addCondition($value, '>', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn (optional)
     */
    public function lte($value, $iscolumn = false)
    {
        $this->addCondition($value, '<=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn (optional)
     */
    public function gte($value, $iscolumn = false)
    {
        $this->addCondition($value, '>=', $iscolumn);
    }

    /**
     * @param   array|Closure   $value
     */
    public function in($value)
    {
        $this->havingClause->addInCondition($this->aggregate, $value, $this->separator, false);
    }

    /**
     * @param   array|Closure   $value
     */
    public function notIn($value)
    {
        $this->havingClause->addInCondition($this->aggregate, $value, $this->separator, true);
    }

    /**
     * @param   int $value1
     * @param   int $value2
     */
    public function between($value1, $value2)
    {
        $this->havingClause->addBetweenCondition($this->aggregate, $value1, $value2, $this->separator, false);
    }

    /**
     * @param   int $value1
     * @param   int $value2
     */
    public function notBetween($value1, $value2)
    {
        $this->havingClause->addBetweenCondition($this->aggregate, $value1, $value2, $this->separator, true);
    }
}
