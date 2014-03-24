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

namespace Opis\Database\Schema;

class AlterColumn extends BaseColumn
{
    
    protected $table;
    
    public function __construct(AlterTable $table, $name, $type = null)
    {
        $this->table = $table;
        parent::__construct($name, $type);
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    public function integer()
    {
        return $this->setType('integer');
    }
    
    public function implicit($value)
    {
        return $this->set('default', $value);
    }
    
    public function float()
    {
        return $this->setType('float');
    }
    
    public function double()
    {
        return $this->setType('double');
    }
    
    public function decimal()
    {
        return $this->setType('decimal');
    }
    
    public function boolean()
    {
        return $this->setType('boolean');
    }
    
    public function binary()
    {
        return $this->setType('binary');
    }
    
    public function string($length = 255)
    {
        return $this->setType('string')->set('length', $length);
    }
    
    public function text()
    {
        return $this->setType('text');
    }
    
    public function fixed()
    {
        return $this->setType('fixed');
    }
    
    public function time()
    {
        return $this->setType('time');
    }
    
    public function timestamp()
    {
        return $this->setType('timestamp');
    }
    
    public function date()
    {
        return $this->setType('date');
    }
    
    public function dateTime()
    {
        return $this->setType('dateTime');
    }
    
}
