<?php

namespace Opis\Database\Test;


class Connection extends \Opis\Database\Connection
{

    public function __construct($driver)
    {
        parent::__construct('');
        $this->driver = $driver;
        //$this->setWrapperFormat('`%s`');
    }

    public function query($sql, array $params = array())
    {
        return $this->replaceParams($sql, $params);
    }

    public function column($sql, array $params = array())
    {
        return $this->replaceParams($sql, $params);
    }

    public function count($sql, array $params = array())
    {
        return $this->replaceParams($sql, $params);
    }

    public function command($sql, array $params = array())
    {
        return $this->replaceParams($sql, $params);
    }
}