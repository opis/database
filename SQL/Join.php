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

class Join
{
    /** @var string Join type. */
    protected $type;

    /** @var string Table we are joining. */
    protected $table;

    /** @var array ON clauses. */
    protected $clauses = array();


    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $type   Join type
     * @param   string  $table  Table we are joining
     */

    public function __construct($type, $table)
    {
        $this->type  = $type;
        $this->table = $table;
    }


    /**
     * Returns the join type.
     * 
     * @access  public
     * @return  string
     */

    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the table name
     * 
     * @access  public
     * @return  string
     */

    public function getTable()
    {
        return $this->table;
    }

    /**
     * Returns ON clauses.
     * 
     * @access  public
     * @return  array
     */

    public function getClauses()
    {
        return $this->clauses;
    }

    /**
     * Adds a ON clause to the join.
     *
     * @access  public
     * @param   string  $column1    Column name
     * @param   string  $operator   Operator
     * @param   string  $column2    Column name
     * @param   string  $separator  (optional) Clause separator
     */

    public function on($column1, $operator, $column2, $separator = 'AND')
    {
        $this->clauses[] = array(
            'column1'   => $column1,
            'operator'  => $operator,
            'column2'   => $column2,
            'separator' => $separator,
        );
        return $this;
    }

    /**
     * Adds a OR ON clause to the join.
     *
     * @access  public
     * @param   string  $column1   Column name
     * @param   string  $operator  Operator
     * @param   string  $column2   Column name
     */

    public function orOn($column1, $operator, $column2)
    {
        return $this->on($column1, $operator, $column2, 'OR');
    }
}