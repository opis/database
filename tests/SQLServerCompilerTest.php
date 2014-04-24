<?php

class SQLServerCompilerTest extends CompilerTest
{    
    protected function getConnection()
    {
        return new FakeConnection('SQLServer');
    }
    
    protected function wrap($text, $a = '"', $b = '"')
    {
        return parent::wrap($text, '[', ']');
    }
}