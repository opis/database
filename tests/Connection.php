<?php

namespace Opis\Database\Test;


use Opis\Database\ResultSet;

class Connection extends \Opis\Database\Connection
{

    public function __construct($driver)
    {
        parent::__construct('');
        $this->driver = $driver;
        //$this->setWrapperFormat('`%s`');
    }

    public function query(string $sql, array $params = [])
    {
        return $this->replaceParams($sql, $params);
    }

    public function column(string $sql, array $params = [])
    {
        return $this->replaceParams($sql, $params);
    }

    public function count(string $sql, array $params = [])
    {
        return $this->replaceParams($sql, $params);
    }

    public function command(string $sql, array $params = [])
    {
        return $this->replaceParams($sql, $params);
    }
}