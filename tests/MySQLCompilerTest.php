<?php

class MySQLCompilerTest extends CompilerTest
{
    
    protected function getConnection()
    {
        new FakeConnection('MySQL');
    }
    
    protected function wrap($text, $a = '"', $b = '"')
    {
        return parent::wrap($text, '`', '`');
    }
}