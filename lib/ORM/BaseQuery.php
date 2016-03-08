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

namespace Opis\Database\ORM;

use Closure;
use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\SelectStatement;

abstract class BaseQuery extends BaseLoader
{
    /** @var    SelectStatement */
    protected $query;

    /** @var    WhereCondition */
    protected $whereCondition;

    /** @var    bool */
    protected $isReadOnly = false;

    /** @var    Compiler */
    protected $compiler;

    /**
     * Constructor
     *
     * @param   Compiler        $compiler
     * @param   SelectStatement $query
     * @param   WhereCondition  $whereCondition
     */
    public function __construct(Compiler $compiler, SelectStatement $query, WhereCondition $whereCondition)
    {
        $this->compiler = $compiler;
        $this->query = $query;
        $this->whereCondition = $whereCondition;
    }

    /**
     * @param   string  $column
     *
     * @return  WhereCondition
     */
    public function where($column)
    {
        if ($column instanceof Closure) {
            $this->query->where($column);
            return $this;
        }

        return $this->whereCondition->setColumn($column, 'where');
    }

    /**
     * @param   string  $column
     *
     * @return  WhereCondition
     */
    public function andWhere($column)
    {
        return $this->where($column);
    }

    /**
     * @param   string  $column
     *
     * @return  WhereCondition
     */
    public function orWhere($column)
    {
        if ($column instanceof Closure) {
            $this->query->orWhere($column);
            return $this;
        }
        
        return $this->whereCondition->setColumn($column, 'orWhere');
    }

    /**
     * @param   Closure $select
     *
     * @return  $this
     */
    public function whereExists(Closure $select)
    {
        $this->query->whereExists($select);
        return $this;
    }

    /**
     * @param   Closure $select
     *
     * @return  $this
     */
    public function andWhereExists(Closure $select)
    {
        $this->query->andWhereExists($select);
        return $this;
    }

    /**
     * @param   Closure $select
     *
     * @return  $this
     */
    public function orWhereExists(Closure $select)
    {
        $this->query->orWhereExists($select);
        return $this;
    }

    /**
     * @param   Closure $select
     *
     * @return  $this
     */
    public function whereNotExists(Closure $select)
    {
        $this->query->whereNotExists($select);
        return $this;
    }

    /**
     * @param   Closure $select
     *
     * @return  $this
     */
    public function andWhereNotExists(Closure $select)
    {
        $this->query->andWhereNotExists($select);
        return $this;
    }

    /**
     * @param   Closure $select
     *
     * @return  $this
     */
    public function orWhereNotExists(Closure $select)
    {
        $this->query->orWhereNotExists($select);
        return $this;
    }

    /**
     * @param   string|array    $columns
     * @param   string          $order      (optional)
     *
     * @return  $this
     */
    public function orderBy($columns, $order = 'ASC')
    {
        $this->query->orderBy($columns, $order);
        return $this;
    }

    /**
     * @param   int $value
     *
     * @return  $this
     */
    public function limit($value)
    {
        $this->query->limit($value);
        return $this;
    }

    /**
     * @param   int $value
     *
     * @return  $this
     */
    public function offset($value)
    {
        $this->query->offset($value);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function join($table, Closure $closure)
    {
        $this->query->join($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function leftJoin($table, Closure $closure)
    {
        $this->query->leftJoin($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function rightJoin($table, Closure $closure)
    {
        $this->query->rightJoin($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function fullJoin($table, Closure $closure)
    {
        $this->query->fullJoin($table, $closure);
        $this->isReadOnly = true;
        return $this;
    }

    /**
     * @return  $this
     */
    public function distinct()
    {
        $this->query->distinct();
        return $this;
    }

    /**
     * 
     * @return  $this
     */
    public function withSoftDeleted()
    {
        $this->query->withSoftDeleted();
        return $this;
    }

    /**
     * 
     * @return  $this
     */
    public function onlySoftDeleted()
    {
        $this->query->onlySoftDeleted();
        return $this;
    }
}
