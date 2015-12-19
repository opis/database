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

class AggregateExpression
{
    /** @var    Compiler */
    protected $compiler;

    /** @var    HavingClause */
    protected $havingClause;

    /** @var    Having */
    protected $having;

    /** @var    string */
    protected $column;

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
        $this->having = new Having($this->compiler, $this->havingClause);
    }

    /**
     * @return  Expression
     */
    protected function expression()
    {
        return new Expression($this->compiler);
    }

    /**
     * @param   string  $column
     * @param   string  $separator
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
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function count($distinct = false)
    {
        $value = $this->expression()->count($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function avg($distinct = false)
    {
        $value = $this->expression()->avg($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function sum($distinct = false)
    {
        $value = $this->expression()->sum($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function min($distinct = false)
    {
        $value = $this->expression()->min($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }

    /**
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function max($distinct = false)
    {
        $value = $this->expression()->max($this->column, $distinct);
        return $this->having->init($value, $this->separator);
    }
}
