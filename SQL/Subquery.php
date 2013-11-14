<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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

use Opis\Database\SQL\Query;


class Subquery
{
    /** @var \Opis\Database\Factory\Query Query builder. */
    protected $query;

    /** @var string Alias. */
    protected $alias = null;

    protected $compiler;
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Factory\Query  $query  Query builder
     * @param   string                      $alias  Subquery alias
     */

    public function __construct(Query $query, $alias = null)
    {
        $this->query = $query;
        $this->alias = $alias;
    }

    /**
     * Returns the compiled query.
     *
     * @access  public
     * @return  array
     */

    public function get()
    {
        $query = $this->query->getCompiler()->select($this->query);
        $query['sql'] = '(' . $query['sql'] . ')';
        if($this->alias !== null)
        {
            $query['sql'] .= ' AS ' . $this->query->getCompiler()->wrap($this->alias);
        }
        return $query;
    }
}