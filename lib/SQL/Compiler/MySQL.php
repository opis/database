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

class MySQL extends Compiler
{
    /** @var string Wrapper used to escape table and column names. */
    protected $wrapper = '`%s`';

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionROUND(array $func)
    {
        return 'FORMAT(' . $this->wrap($func['column']) . ', ' . $this->param($func['decimals']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionLEN(array $func)
    {
        return 'LENGTH(' . $this->wrap($func['column']) . ')';
    }
}
