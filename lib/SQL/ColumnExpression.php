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

class ColumnExpression
{
    /** @var    Compiler */
    protected $compiler;

    /** @var    array */
    protected $columns = array();

    /**
     * Constructor
     * 
     * @param   Compiler   $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @return  Expression
     */
    protected function expression()
    {
        return new Expression($this->compiler);
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add a column
     * 
     * @param   string|Closure  $name   Column's name
     * @param   string          $alias  (optional) Alias
     * 
     * @return  $this
     */
    public function column($name, $alias = null)
    {
        if ($name instanceof Closure) {
            $expression = $this->expression();
            $name($expression);
            $name = $expression;
        }

        $this->columns[] = array(
            'name' => $name,
            'alias' => $alias,
        );
        return $this;
    }

    /**
     * Add multiple columns at once
     * 
     * @param   array   $columns    Columns
     * 
     * @return  $this
     */
    public function columns(array $columns)
    {
        foreach ($columns as $name => $alias) {
            if (is_string($name)) {
                $this->column($name, $alias);
            } else {
                $this->column($alias, null);
            }
        }
        return $this;
    }

    /**
     * Add a `COUNT` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Column's alias
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function count($column = '*', $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->count($column, $distinct), $alias);
    }

    /**
     * Add an `AVG` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function avg($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->avg($column, $distinct), $alias);
    }

    /**
     * Add a `SUM` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function sum($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->sum($column, $distinct), $alias);
    }

    /**
     * Add a `MIN` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function min($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->min($column, $distinct), $alias);
    }

    /**
     * Add a `MAX` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * @param   bool    $distinct   (optional) Distinct column
     * 
     * @return  $this
     */
    public function max($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->max($column, $distinct), $alias);
    }

    /**
     * Add a `UCASE` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * 
     * @return  $this
     */
    public function ucase($column, $alias = null)
    {
        return $this->column($this->expression()->ucase($column), $alias);
    }

    /**
     * Add a `LCASE` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * 
     * @return  $this
     */
    public function lcase($column, $alias = null)
    {
        return $this->column($this->expression()->lcase($column), $alias);
    }

    /**
     * Add a `MID` expression
     * 
     * @param   string  $column     Column
     * @param   int     $start      (optional) Substring start  
     * @param   string  $alias      (optional) Alias
     * @param   int     $length     (optional) Substring length
     * 
     * @return  $this
     */
    public function mid($column, $start = 1, $alias = null, $length = 0)
    {
        return $this->column($this->expression()->mid($column, $start, $length), $alias);
    }

    /**
     * Add a `LEN` expression
     * 
     * @param   string  $column     Column
     * @param   string  $alias      (optional) Alias
     * 
     * @return  $this
     */
    public function len($column, $alias = null)
    {
        return $this->column($this->expression()->len($column), $alias);
    }

    /**
     * Add a `FORMAT` expression
     * 
     * @param   string  $column     Column
     * @param   int     $decimals   (optional) Decimals
     * @param   string  $alias      (optional) Alias
     * 
     * @return  $this
     */
    public function round($column, $decimals = 0, $alias = null)
    {
        return $this->column($this->expression()->format($column, $decimals), $alias);
    }

    /**
     * Add a `FORMAT` expression
     * 
     * @param   string  $column     Column
     * @param   int     $format     Decimals
     * @param   string  $alias      (optional) Alias
     * 
     * @return  $this
     */
    public function format($column, $format, $alias = null)
    {
        return $this->column($this->expression()->format($column, $format), $alias);
    }

    /**
     * Add a `NOW` expression
     * 
     * @param   string  $alias      (optional) Alias
     * 
     * @return  $this
     */
    public function now($alias = null)
    {
        return $this->column($this->expression()->now(), $alias);
    }
}
