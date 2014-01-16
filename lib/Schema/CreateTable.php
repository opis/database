<?php

namespace Opis\Database\Schema;

class CreateTable
{
    
    protected $columns = array();
    
    protected $primaryKey;
    
    protected $uniqueKeys = array();
    
    protected $indexes = array();
    
    protected $table;
    
    protected $checkIfExists = false;
    
    public function __construct($table)
    {
        $this->table;
    }
    
    protected function addColumn($name, $type)
    {
        $column = new TableColumn($this, $type, $name);
        $columns[] = $column;
        return $column;
    }
    
    public function getTableName()
    {
        return $this->table;
    }
    
    public function getExistsCondition()
    {
        return $this->checkIfExists;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    
    public function ifNotExists()
    {
        $this->checkIfExists = true;
        return $this;
    }
    
    public function primary($columns)
    {
        $this->primaryKey = $columns;
        return $this;
    }
    
    public function unique($columns)
    {
        $this->uniqueKeys[] = $columns;
        return $this;
    }
    
    public function index($columns)
    {
        $this->indexes[] = $columns;
        return $this;
    }
    
    public function integer($name)
    {
        return $this->addColumn($name, 'integer');
    }
    
    public function bigInteger($name)
    {
        return $this->addColumn($name, 'bigInteger');
    }
    
    public function smallInteger($name)
    {
        return $this->addColumn($name, 'smallInteger');
    }
    
    public function mediumInteger($name)
    {
        return $this->addColumn($name, 'mediumInteger');
    }
    
    public function serial($name)
    {
        return $this->addColumn($name, 'serial');
    }
    
    public function bigSerial($name)
    {
        return $this->addColumn($name, 'bigSerial');
    }
    
    public function float($name)
    {
        return $this->addColumn($name, 'float');
    }
    
    public function double($name)
    {
        return $this->addColumn($name, 'double');
    }
    
    public function decimal($name)
    {
        return $this->addColumn($name, 'decimal');
    }
    
    public function boolean($name)
    {
        return $this->addColumn($name, 'boolean');
    }
    
    public function binary($name)
    {
        return $this->addColumn($name, 'binary');
    }
    
    public function string($name, $length = 255)
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }
    
    public function text($name)
    {
        return $this->addColumn($name);
    }
    
    public function longText($name)
    {
        return $this->addColumn($name, 'longText');
    }
    
    public function mediumText($name)
    {
        return $this->addColumn($name, 'mediumText');
    }
    
    public function time($name)
    {
        return $this->addColumn($name, 'time');
    }
    
    public function timestamp($name)
    {
        return $this->addColumn($name, 'timestamp');
    }
    
    public function date($name)
    {
        return $this->addColumn($name, 'date');
    }
    
    public function dateTime($name)
    {
        return $this->addColumn($name, 'dateTime');
    }
    
}