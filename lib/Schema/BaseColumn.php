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

namespace Opis\Database\Schema;

class BaseColumn
{
    
    protected $name;
    
    protected $type;
    
    protected $properties = array();

    /**
     * BaseColumn constructor.
     * @param $name
     * @param null $type
     */
    public function __construct($name, $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }
    
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * @param $value
     * @return $this|BaseColumn
     */
    public function size($value)
    {
        $value = strtolower($value);
        
        if(!in_array($value, array('tiny', 'small', 'normal', 'medium', 'big')))
        {
            return $this;
        }
        
        return $this->set('size', $value);
    }
    
    /**
     * Deprecated since 2.1.0
     */
    
    public function nullable()
    {
        return $this->set('nullable', true);
    }

    /**
     * @return BaseColumn
     */
    public function notNull()
    {
        return $this->set('nullable', false);
    }

    /**
     * @param $comment
     * @return BaseColumn
     */
    public function description($comment)
    {
        return $this->set('description', $comment);
    }

    /**
     * @param $value
     * @return BaseColumn
     */
    public function defaultValue($value)
    {
        return $this->set('default', $value);
    }

    /**
     * @param bool|true $value
     * @return BaseColumn
     */
    public function unsigned($value = true)
    {
        return $this->set('unisgned', $value);
    }
    
}
