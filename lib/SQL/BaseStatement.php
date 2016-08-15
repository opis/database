<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
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
use Opis\Database\ORM\Query;
use Opis\Database\ORM\Relation;

class BaseStatement extends WhereStatement
{
    
    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement|Query|Relation
     */
    public function join($table, Closure $closure)
    {
        $this->sql->addJoinClause('INNER', $table, $closure);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement|Query|Relation
     */
    public function leftJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('LEFT', $table, $closure);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement|Query|Relation
     */
    public function rightJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('RIGHT', $table, $closure);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  Delete|Select|BaseStatement|Query|Relation
     */
    public function fullJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('FULL', $table, $closure);
        return $this;
    }

}