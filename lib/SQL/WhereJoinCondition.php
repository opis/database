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

class WhereJoinCondition extends WhereCondition
{
    /** @var    array */
    protected $joins = array();

    /**
     * Constructor
     * 
     * @param   Compiler    $compiler
     * @param   WhereClause $clause     (optional)
     */
    public function __construct(Compiler $compiler, WhereClause $clause = null)
    {
        parent::__construct($compiler, $clause);
    }

    /**
     * @return  array
     */
    public function getJoinClauses()
    {
        return $this->joins;
    }

    /**
     *  @param  string          $type
     *  @param  string|array    $table
     *  @param  Closure         $closure
     *
     *  @return $this
     */
    protected function addJoinClause($type, $table, $closure)
    {
        $join = new Join();

        $closure($join);

        if (!is_array($table)) {
            $table = array($table);
        }

        $this->joins[] = array(
            'type' => $type,
            'table' => $table,
            'join' => $join,
        );

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
        return $this->addJoinClause('INNER', $table, $closure);
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function leftJoin($table, Closure $closure)
    {
        return $this->addJoinClause('LEFT', $table, $closure);
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function rightJoin($table, Closure $closure)
    {
        return $this->addJoinClause('RIGHT', $table, $closure);
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function fullJoin($table, Closure $closure)
    {
        return $this->addJoinClause('FULL', $table, $closure);
    }
}
