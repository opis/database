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

class Column
{
    
    protected $properties = array();
    
    protected $name;
    
    protected $type;
    
    protected $table;
    
    public function __construct(Create $table, $type, $name)
    {
        $this->table = $table;
        $this->type = $type;
        $this->name = $name;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
    
    public function get($key, $default = null)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : $default;
    }
    
    public function set($key, $value)
    {
        $this->properties[$key] = $value;
        return $this;
    }
    
    public function has($key)
    {
        return isset($this->properties[$key]);
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
    
    public function nullable()
    {
        return $this->set('nullable', true);
    }
    
    public function description($comment)
    {
        return $this->set('description', $comment);
    }
    
    public function implicit($value)
    {
        return $this->set('default', $value);
    }
}
