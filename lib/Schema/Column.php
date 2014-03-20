<?php

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
