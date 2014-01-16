<?php

namespace Opis\Database\Schema;

class Compiler
{
    
    protected function wrap($name)
    {
        
    }
    
    protected function handleColumns(array $columns)
    {
        $sql = array();
        
        foreach($columns as $column)
        {
            $type = $column->getType();
            $sql[] = $this->{$type}($column);
        }
        
        return implode(",\n", $sql);
    }
    
    protected function handleKeys(array $keys)
    {
        if(empty($keys))
        {
            return '';
        }
    }
    
    protected function handleColumnType(TableColumn $column)
    {
        switch($column->getType())
        {
            case 'integer':
                return 'INT';
            case 'smallInteger':
                return 'SMALLINT';
            case 'mediumInteger':
                return 'MEDIUMINT';
            case 'bigInteger':
                return 'BIGINT';
            case 'boolean':
                return 'TINYINT(1)';
            case 'string':
                return 'VARCHAR(' . $column->get('length') . ')';
            case 'text':
                return '';
        }
    }
    
    protected function integerColumn(TableColumn $column)
    {
        
    }
    
    protected function bigIntegerColumn($name)
    {
        
    }
    
    protected function smallIntegerColumn($name)
    {
        
    }
    
    protected function serialColumn($name)
    {
        
    }
    
    protected function bigSerialColumn($name)
    {
        return $this->addColumn($name, 'bigSerial');
    }
    
    protected function floatColumn($name)
    {
        return $this->addColumn($name, 'float');
    }
    
    protected function doubleColumn($name)
    {
        return $this->addColumn($name, 'double');
    }
    
    protected function decimalColumn($name)
    {
        return $this->addColumn($name, 'decimal');
    }
    
    protected function booleanColumn($name)
    {
        return $this->addColumn($name, 'boolean');
    }
    
    protected function binaryColumn($name)
    {
        return $this->addColumn($name, 'binary');
    }
    
    protected function stringColumn($name, $length = 255)
    {
        return $this->addColumn($name, 'string')->set('length', $length);
    }
    
    protected function textColumn($name)
    {
        return $this->addColumn($name);
    }
    
    protected function longTextColumn($name)
    {
        return $this->addColumn($name, 'longText');
    }
    
    protected function mediumTextColumn($name)
    {
        return $this->addColumn($name, 'mediumText');
    }
    
    protected function timeColumn($name)
    {
        return $this->addColumn($name, 'time');
    }
    
    protected function timestampColumn($name)
    {
        return $this->addColumn($name, 'timestamp');
    }
    
    protected function dateColumn($name)
    {
        return $this->addColumn($name, 'date');
    }
    
    protected function dateTimeColumn($name)
    {
        return $this->addColumn($name, 'dateTime');
    }
    
    
    public function create(CreateTable $schema)
    {
        $sql = 'CREATE TABLE ' . $this->wrap($schema->getTableName());
        $sql .= '(';
        $sql .= $this->handleColumns($schema->getColumns());
        $sql .= $this->handleKeys($schema->getKeys());
        $sql .= ')';
    }
    
    public function alter(AlterTable $schema)
    {
        
    }
    
}