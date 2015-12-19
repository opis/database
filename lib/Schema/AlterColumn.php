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

namespace Opis\Database\Schema;

class AlterColumn extends BaseColumn
{
    /** @var    string */
    protected $table;

    /**
     * Constructor
     * 
     * @param   AlterTable  $table
     * @param   string      $name
     * @param   string|null $type   (optional)
     */
    public function __construct(AlterTable $table, $name, $type = null)
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
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function defaultValue($value)
    {
        if ($this->get('handleDefault', true)) {
            return parent::defaultValue($value);
        }

        return $this;
    }

    /**
     * @return  $this
     */
    public function autoincrement()
    {
        return $this->set('autoincrement', true);
    }
}
