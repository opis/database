<?php

class SQLServerCompilerTest extends CompilerTest
{
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('SQLServer'));
    }
    
    protected function wrap($text)
    {
        return parent::wrap($text, '[', ']');
    }
}