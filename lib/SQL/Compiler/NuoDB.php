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

namespace Opis\Database\SQL\Compiler;

use Opis\Database\SQL\Compiler;

class NuoDB extends Compiler
{
    /** @var string Wrapper used to escape table and column names. */
    protected $wrapper = '"%s"';

    /**
     * Compiles LIMIT clauses.
     *
     * @param   int        $limit  Limit
     * 
     * @return  string
     */
    protected function handleLimit($limit)
    {
        return ($limit === null) ? '' : ' FETCH ' . $limit;
    }

    /**
     * Compiles OFFSET clauses.
     * 
     * @param   int        $offset  Limit
     * 
     * @return  string
     */
    protected function handleOffset($offset)
    {
        return ($offset === null) ? '' : ' OFFSET ' . $offset;
    }
}
