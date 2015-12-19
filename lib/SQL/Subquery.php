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

class Subquery
{
    /** @var    Compiler */
    protected $compiler;

    /** @var    SelectStatement */
    protected $select;

    /**
     * Constructor
     * 
     * @param   Compiler    $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * @param   string|array    $tables
     * 
     * @return  SelectStatement
     */
    public function from($tables)
    {
        $this->select = new SelectStatement($this->compiler, $tables);
        return $this->select;
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        return (string) $this->select;
    }
}
