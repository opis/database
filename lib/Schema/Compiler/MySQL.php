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

namespace Opis\Database\Schema\Compiler;

use Opis\Database\Schema\Compiler;
use Opis\Database\Schema\BaseColumn;


class MySQL extends Compiler
{
    protected $wrapper = '`%s`';
    
    
    protected function handleTypeInteger(BaseColumn $column)
    {
        switch($column->get('size', 'normal'))
        {
            case 'tiny':
                return ' TINY';
            case 'small':
                return ' SMALLINT';
            case 'medium':
                return ' MEDIUMINT';
            case 'big':
                return ' BIGINT';
        }
        
        return ' INTEGER';
    }
    
    protected function handleTypeDecimal(BaseColumn $column)
    {
        if(null !== $m = $column->get('M') && null !== $p = $column->get('P'))
        {
            return ' DECIAMAL (' . $this->value($m) . ', ' . $this->value($p) . ')';
        }
        
        return ' DECIMAL';
    }
    
}