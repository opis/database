<?php

class MySQLCompilerTest extends CompilerTest
{
    
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('MySQL'));
    }
    
    protected function wrap($text, $a = '"', $b = '"')
    {
        return parent::wrap($text, '`', '`');
    }
}