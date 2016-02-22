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
    /** @var    string */
    protected $name;

    /** @var    string */
    protected $type;

    /** @var    array */
    protected $properties = array();

    /**
     * Constructor
     * 
     * @param   string      $name
     * @param   string|null $type   (optional)
     */
    public function __construct($name, $type = null)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return  string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return  array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param   string  $type
     * 
     * @return  $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param   string  $name
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function set($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * @param   string  $name
     * 
     * @return  bool
     */
    public function has($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param   string      $name
     * @param   mixed|null  $default    (optional)
     * 
     * @return  mixed|null
     */
    public function get($name, $default = null)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * @param   string  $value
     * 
     * @return  $this
     */
    public function size($value)
    {
        $value = strtolower($value);

        if (!in_array($value, array('tiny', 'small', 'normal', 'medium', 'big'))) {
            return $this;
        }

        return $this->set('size', $value);
    }

    /**
     * @deprecated  2.1.0   No longer used
     *
     * @return  $this
     */
    public function nullable()
    {
        return $this->set('nullable', true);
    }

    /**
     * @return  $this
     */
    public function notNull()
    {
        return $this->set('nullable', false);
    }

    /**
     * @param   string  $comment
     * 
     * @return  $this
     */
    public function description($comment)
    {
        return $this->set('description', $comment);
    }

    /**
     * @param   mixed   $value
     * 
     * @return  $this
     */
    public function defaultValue($value)
    {
        return $this->set('default', $value);
    }

    /**
     * @param   bool|true   $value  (optional)
     * 
     * @return  $this
     */
    public function unsigned($value = true)
    {
        return $this->set('unisgned', $value);
    }

    /**
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function length($value)
    {
    	return $this->set('length', $value);
    }
}
