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

class WhereCondition
{
    /** @var    BaseQuery */
    protected $builder;

    /** @var    Select */
    protected $query;

    /** @var    string */
    protected $method;

    /** @var    string */
    protected $column;

    /**
     * Constructor
     *
     * @param   BaseQuery   $builder
     * @param   Select      $query
     */
    public function __construct(BaseQuery $builder, Select $query)
    {
        $this->builder = $builder;
        $this->query = $query;
    }

    /**
     * @param   string  $column
     * @param   string  $method
     *
     * @return  $this
     */
    public function setColumn($column, $method)
    {
        $this->column = $column;
        $this->method = $method;
        return $this;
    }

    /**
     * @param   string  $name
     * @param   array   $arguments
     *
     * @return  BaseQuery
     */
    public function __call($name, $arguments)
    {
        $where = $this->query->{$this->method}($this->column);
        call_user_func_array(array($where, $name), $arguments);
        return $this->builder;
    }
}
