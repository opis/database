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

class Expression
{
    /** @var    array */
    protected $expressions = array();

    /** @var    Compiler */
    protected $compiler;

    /**
     * Constructor
     * 
     * @param   Compiler    $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Returns an array of expressions
     * 
     * @return  array
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @param   string  $type
     * @param   mixed   $value
     * 
     * @return  $this
     */
    protected function addExpression($type, $value)
    {
        $this->expressions[] = array(
            'type' => $type,
            'value' => $value,
        );

        return $this;
    }

    /**
     * @param   string          $type
     * @param   string          $name
     * @param   Closure|string  $column
     * @param   array           $arguments  (optional)
     * 
     * @return  $this
     */
    protected function addFunction($type, $name, $column, $arguments = array())
    {
        if ($column instanceof Closure) {
            $expression = new Expression($this->compiler);
            $column($expression);
            $column = $expression;
        }

        $func = array_merge(array('type' => $type, 'name' => $name, 'column' => $column), $arguments);

        return $this->addExpression('function', $func);
    }

    /**
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function column($value)
    {
        return $this->addExpression('column', $value);
    }

    /**
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function op($value)
    {
        return $this->addExpression('op', $value);
    }

    /**
     * @param   mixed   $value
     * @return  $this
     */
    public function value($value)
    {
        return $this->addExpression('value', $value);
    }

    /**
     * @param   Closure $closure
     * 
     * @return  $this
     */
    public function group(Closure $closure)
    {
        $expresion = new Expression($this->compiler);
        $closure($expresion);
        return $this->addExpression('group', $expresion);
    }

    /**
     * @param   array|string    $tables
     * 
     * @return  SelectStatement
     */
    public function from($tables)
    {
        $subquery = new Subquery($this->compiler);
        $this->addExpression('subquery', $subquery);
        return $subquery->from($tables);
    }

    /**
     * @param   string  $column     (optional)
     * @param   bool    $distinct   (optional)
     * 
     * @return  $this
     */
    public function count($column = '*', $distinct = false)
    {
        if (!is_array($column)) {
            $column = array($column);
        }
        $distinct = $distinct || (count($column) > 1);
        return $this->addFunction('aggregateFunction', 'COUNT', $column, array('distinct' => $distinct));
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  $this
     */
    public function sum($column, $distinct = false)
    {
        return $this->addFunction('aggregateFunction', 'SUM', $column, array('distinct' => $distinct));
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  $this
     */
    public function avg($column, $distinct = false)
    {
        return $this->addFunction('aggregateFunction', 'AVG', $column, array('distinct' => $distinct));
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  $this
     */
    public function max($column, $distinct = false)
    {
        return $this->addFunction('aggregateFunction', 'MAX', $column, array('distinct' => $distinct));
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     * 
     * @return  $this
     */
    public function min($column, $distinct = false)
    {
        return $this->addFunction('aggregateFunction', 'MIN', $column, array('distinct' => $distinct));
    }

    /**
     * @param   string  $column
     * 
     * @return  $this
     */
    public function ucase($column)
    {
        return $this->addFunction('sqlFunction', 'UCASE', $column);
    }

    /**
     * @param   string  $column
     * 
     * @return  $this
     */
    public function lcase($column)
    {
        return $this->addFunction('sqlFunction', 'LCASE', $column);
    }

    /**
     * @param   string  $column
     * @param   int     $start  (optional)
     * @param   int     $length (optional)
     * 
     * @return  $this
     */
    public function mid($column, $start = 1, $length = 0)
    {
        return $this->addFunction('sqlFunction', 'MID', $column, array('start' => $start, 'lenght' => $length));
    }

    /**
     * @param   string  $column
     * 
     * @return  $this
     */
    public function len($column)
    {
        return $this->addFunction('sqlFunction', 'LEN', $column);
    }

    /**
     * @param   string  $column
     * @param   int     $decimals   (optional)
     * 
     * @return  $this
     */
    public function round($column, $decimals = 0)
    {
        return $this->addFunction('sqlFunction', 'ROUND', $column, array('decimals' => $decimals));
    }

    /**
     * @return  $this
     */
    public function now()
    {
        return $this->addFunction('sqlFunction', 'NOW', '');
    }

    /**
     * @param $column
     * @param $format
     * @return Expression
     */
    public function format($column, $format)
    {
        return $this->addFunction('sqlFunction', 'FORMAT', $column, array('format' => $format));
    }

    /**
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function __get($value)
    {
        return $this->addExpression('op', $value);
    }
}
