<?php

namespace Opis\Database;

use Closure;
use Opis\Database\Schema\CreateTable;
use Opis\Database\Schema\AlterTable;

class Schema
{
    /** @var    \Opis\Database\Connection   Connection. */
    protected $connection;
    
    /**
     * Constructor
     *
     * @access public
     *
     * @param   \Opis\Database\Connection   $connection Connection.
     */
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function create($table, Closure $callback)
    {
        $schema = new CreateTable($table);
        $callback($schema);
        return $schema;
    }
    
    public function alter($table, Closure $callback)
    {
        $schema = new AlterTable($table);
        $callback($schema);
        return $schema;
    }
    
}