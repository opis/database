<?php

namespace Opis\Database\Schema;

class AlterTable
{
    protected $table;
    
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    public function getTableName()
    {
        return $this->table;
    }
}
