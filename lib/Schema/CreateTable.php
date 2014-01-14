<?php

namespace Opis\Database\Schema;

class CreateTable
{
    
    protected $columns = array();
    
    protected $table;
    
    protected $checkIfExists = false;
    
    public function __construct($table)
    {
        $this->table;
    }
    
    public function ifNotExists()
    {
        $this->checkIfExists = true;
        return $this;
    }
    
    protected function addColumn($name, $type)
    {
        $column = new TableColumn($type, $name);
        $columns[] = $column;
        return $column;
    }
    
    protected function booleanColumn($name)
    {
        return $this->addColumn($name, 'boolean');
    }
    
    protected function binaryColumn($name)
    {
        return $this->addColumn($name, 'binary');
    }
    
    protected function integerColumn($name)
    {
        return $this->addColumn($name, 'integer');
    }
    
    protected function serialColumn($name)
    {
        return $this->addColumn($name, 'serial');
    }
    
    protected function bigSerial($name)
    {
        return $this->addColumn($name, 'bigSerial');
    }
    
    protected function floatColumn($name)
    {
        return $this->addColumn($name, 'float');
    }
    
    protected function stringColumn($name, $length = 255)
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }
    
    protected function timeColumn($name)
    {
        return $this->addColumn($name, 'time');
    }
    
    protected function dateColumn($name)
    {
        return $this->addColumn($name, 'date');
    }
    
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this, $name . 'Column'), $arguments);
    }
    
    public function getTableName()
    {
        return $this->table;
    }
    
    public function getExistsCondition()
    {
        return $this->checkIfExists;
    }
    
}