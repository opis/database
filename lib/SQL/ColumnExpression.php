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
    protected $compiler;
    
    protected $columns = array();

    /**
     * ColumnExpression constructor.
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @return Expression
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
     * @param $name
     * @param null $alias
     * @return $this
     */
    public function column($name, $alias = null)
    {
        if($name instanceof Closure)
        {
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
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns)
    {
        foreach($columns as $name => $alias)
        {
            if(is_string($name))
            {
                $this->column($name, $alias);
            }
            else
            {
                $this->column($alias, null);
            }
        }
        return $this;
    }

    /**
     * @param string $column
     * @param null $alias
     * @param bool|false $distinct
     * @return ColumnExpression
     */
    public function count($column = '*', $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->count($column, $distinct), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @param bool|false $distinct
     * @return ColumnExpression
     */
    public function avg($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->avg($column, $distinct), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @param bool|false $distinct
     * @return ColumnExpression
     */
    public function sum($column, $alias = null, $distinct  = false)
    {
        return $this->column($this->expression()->sum($column, $distinct), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @param bool|false $distinct
     * @return ColumnExpression
     */
    public function min($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->min($column, $distinct), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @param bool|false $distinct
     * @return ColumnExpression
     */
    public function max($column, $alias = null, $distinct = false)
    {
        return $this->column($this->expression()->max($column, $distinct), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @return ColumnExpression
     */
    public function ucase($column, $alias = null)
    {
        return $this->column($this->expression()->ucase($column), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @return ColumnExpression
     */
    public function lcase($column, $alias = null)
    {
        return $this->column($this->expression()->lcase($column), $alias);
    }

    /**
     * @param $column
     * @param int $start
     * @param null $alias
     * @param int $length
     * @return ColumnExpression
     */
    public function mid($column, $start = 1, $alias = null, $length = 0)
    {
        return $this->column($this->expression()->mid($column, $start, $length), $alias);
    }

    /**
     * @param $column
     * @param null $alias
     * @return ColumnExpression
     */
    public function len($column, $alias = null)
    {
        return $this->column($this->expression()->len($column), $alias);
    }

    /**
     * @param $column
     * @param int $decimals
     * @param null $alias
     * @return ColumnExpression
     */
    public function round($column, $decimals = 0, $alias = null)
    {
        return $this->column($this->expression()->format($column, $format), $alias);
    }

    /**
     * @param $column
     * @param $format
     * @param null $alias
     * @return ColumnExpression
     */
    public function format($column, $format, $alias = null)
    {
        return $this->column($this->expression()->format($column, $format), $alias);
    }

    /**
     * @param null $alias
     * @return ColumnExpression
     */
    public function now($alias = null)
    {
        return $this->column($this->expression()->now(), $alias);
    }
}