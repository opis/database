<?php

class SQLServerCompilerTest extends CompilerTest
{
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('SQLServer'));
    }
    
    protected function wrap($text, $a = '"', $b = '"')
    {
        return parent::wrap($text, '[', ']');
    }
}