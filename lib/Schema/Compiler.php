<?php

namespace Opis\Database\Schema;

class Compiler
{
    
    protected $separator = ';';
    
    protected $wrapper = '`%s`';
    
    protected $params = array();
    
    protected function wrap($name)
    {
        return sprintf($this->wrapper, $name);
    }
    
    protected function wrapArray(array $value)
    {
        return array_map(array($this, 'wrap'), $value);
    }
    
    protected function value($value)
    {
        $this->params[] = $value;
        return '?';
    }
    
    protected function handleColumns(array $columns)
    {
        $sql = array();
        
        foreach($columns as $name => $column)
        {
            $line  = $this->wrap($name);
            $line .= $this->handleColumnType($column);
            $line .= $column->get('unsigned', false) ? ' UNSIGNED ' : '';
            $line .= $column->get('nullable', false) ? ' NULL ' : ' NOT NULL ';
            $default = $column->get('default');
            $line .= $default === null ? '' : ' DEFAULT ' . $this->value($default);
            $sql[] = $line;
        }
        
        return implode(",\n", $sql);
    }
    
    protected function handleColumnType(Column $column)
    {
        switch($column->getType())
        {
            case 'integer':
                return ' INT ';
            case 'smallInteger':
                return ' SMALLINT ';
            case 'mediumInteger':
                return ' MEDIUMINT ';
            case 'bigInteger':
                return ' BIGINT ';
            case 'serial':
                return ' INT AUTO_INCREMENT ';
            case 'boolean':
                return ' TINYINT(1) ';
            case 'string':
                return ' VARCHAR(' . $column->get('length') . ') ';
            case 'text':
                return '';
        }
    }
    
    protected function handlePrimaryKey(Create $schema)
    {
        $pk = $schema->getPrimaryKey();
        
        if($pk === null)
        {
            return '';
        }
        
        return ",\n" . 'CONSTRAINT ' . $this->wrap($pk['name']) . ' PRIMARY KEY (' . implode(', ', $this->wrapArray($pk['columns'])) . ')';
    }
    
    protected function handleUniqueKeys(Create $schema)
    {
        
        $indexes = $schema->getUniqueKeys();
        
        if(empty($indexes))
        {
            return '';
        }
        
        $sql = array();
        
        foreach($schema->getUniqueKeys() as $name => $columns)
        {   
            $sql[] = 'CONSTRAINT ' . $this->wrap($name) . ' UNIQUE (' . implode(', ', $this->wrapArray($columns)) . ')';
        }
        
        return ",\n" . implode(",\n", $sql);
    }
    
    protected function handleIndexKeys(Create $schema)
    {
        $indexes = $schema->getIndexes();
        
        if(empty($indexes))
        {
            return '';
        }
        
        $sql = array();
        
        foreach($indexes as $name => $columns)
        {
            $sql[] = 'CREATE INDEX ' . $this->wrap($name) . ' ON ' . $this->wrap($schema->getTableName()) . '(' . $this->wrapArray($columns) . ')';
        }
        
        return "\n" . $this->separator . implode("\n" . $this->separator, $sql);
    }
    
    protected function handleEngine(Create $schema)
    {
        return '';
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    public function create(Create $schema)
    {
        $sql  = 'CREATE TABLE ' . $this->wrap($schema->getTableName());
        $sql .= "(\n";
        $sql .= $this->handleColumns($schema->getColumns());
        $sql .= $this->handlePrimaryKey($schema);
        $sql .= $this->handleUniqueKeys($schema);
        $sql .= "\n)" . $this->handleEngine($schema);
        $sql .= $this->handleIndexKeys($schema);
        
        return $sql;
    }
    
    public function alter(Alter $schema)
    {
        
    }
    
}
