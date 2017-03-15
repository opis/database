<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

namespace Opis\Database\Schema;

class CreateColumn extends BaseColumn
{
    /** @var    string */
    protected $table;

    /**
     * Constructor
     * 
     * @param   CreateTable $table
     * @param   string      $name
     * @param   string      $type
     */
    public function __construct(CreateTable $table, $name, $type)
    {
        $this->table = $table;
        parent::__construct($name, $type);
    }

    /**
     * @return  string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return  $this
     */
    public function autoincrement()
    {
        $this->table->autoincrement($this);
        return $this;
    }

    /**
     * @param mixed|null $columns
     * @return $this
     */
    public function primary($columns = null)
    {
        $this->table->primary($this->name, $columns);
        return $this;
    }

    /**
     * @param mixed|null $columns
     * @return $this
     */
    public function unique($columns = null)
    {
        $this->table->unique($this->name, $columns);
        return $this;
    }

    /**
     * @param mixed|null $columns
     * @return $this
     */
    public function index($columns = null)
    {
        $this->table->index($this->name, $columns);
        return $this;
    }
}
