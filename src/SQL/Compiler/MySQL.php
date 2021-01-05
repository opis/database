<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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
    protected string $wrapper = '`%s`';

    protected function sqlFunctionROUND(array $func): string
    {
        return 'FORMAT(' . $this->wrap($func['column']) . ', ' . $this->param($func['decimals']) . ')';
    }

    protected function sqlFunctionLEN(array $func): string
    {
        return 'LENGTH(' . $this->wrap($func['column']) . ')';
    }
}
