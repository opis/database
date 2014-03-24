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

class CreateColumn extends BaseColumn
{
    
    protected $table;
    
    public function __construct(CreateTable $table, $name, $type)
    {
        $this->table = $table;
        parent::__construct($name, $type);
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    
    public function autoincrement()
    {
        $this->table->autoincrement($this);
        return $this;
    }
    
    public function primary()
    {
        $this->table->primary($this->name);
        return $this;
    }
    
    public function unique()
    {
        $this->table->unique($this->name);
        return $this;
    }
    
    public function index()
    {
        $this->table->index($this->name);
        return $this;
    }

}
