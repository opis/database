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

class BaseColumn
{
    
    protected $name;
    
    protected $type;
    
    protected $properties = array();
    
    public function __construct($name, $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
    
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function set($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }
    
    public function has($name)
    {
        return isset($this->properties[$name]);
    }
    
    public function get($name, $default = null)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }
    
    public function size($value)
    {
        $value = strtolower($value);
        
        if(!in_array($value, array('tiny', 'small', 'normal', 'medium', 'big')))
        {
            return $this;
        }
        
        return $this->set('size', $value);
    }
    
    public function nullable($value = true)
    {
        return $this->set('nullable', $value);
    }
    
    public function description($comment)
    {
        return $this->set('description', $comment);
    }
    
    public function defaultValue($value)
    {
        return $this->set('default', $value);
    }
    
    public function unsigned($value = true)
    {
        return $this->set('unisgned', $value);
    }
    
}
