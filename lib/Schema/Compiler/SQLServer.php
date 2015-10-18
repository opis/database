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

namespace Opis\Database\Schema\Compiler;

use Opis\Database\Schema\Compiler;
use Opis\Database\Schema\BaseColumn;
use Opis\Database\Schema\AlterTable;

class SQLServer extends Compiler
{
    protected $wrapper = '[%s]';
    
    protected $modifiers = array('nullable', 'default', 'autoincrement');
    
    protected function handleTypeInteger(BaseColumn $column)
    {
        switch($column->get('size', 'normal'))
        {
            case 'tiny':
                return 'TINYINT';
            case 'small':
                return 'SMALLINT';
            case 'medium':
                return 'INTEGER';
            case 'big':
                return 'BIGINT';
        }
        
        return 'INTEGER';
    }
    
    protected function handleTypeBoolean(BaseColumn $column)
    {
        return 'BIT';
    }
    
    protected function handleTypeString(BaseColumn $column)
    {
        return 'NVARCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }
    
    protected function handleTypeFixed(BaseColumn $column)
    {
        return 'NCHAR(' . $this->value($column->get('lenght', 255)) . ')';
    }
    
    protected function handleTypeText(BaseColumn $column)
    {
        return 'NVARCHAR(max)';
    }
    
    protected function handleTypeBinary(BaseColumn $column)
    {
        return 'VARBINARY(max)';
    }
    
    protected function handleTypeTimestamp(BaseColumn $column)
    {
        return 'DATETIME';
    }
    
    protected function handleModifierAutoincrement(BaseColumn $column)
    {
        if($column->getType() !== 'integer' || !in_array($column->get('size', 'normal'), $this->serials))
        {
            return '';
        }
        
        return $column->get('autoincrement', false) ? 'IDENTITY' : '';
    }
    
    protected function handleEngine(CreateTable $schema)
    {
        return '';
    }
    
    public function currentDatabase($dsn)
    {
        return array(
            'sql' => 'SELECT SCHEMA_NAME()',
            'params' => array(),
        );
    }
}
