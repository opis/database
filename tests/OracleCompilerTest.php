<?php

class OracleCompilerTest extends CompilerTest
{
    
    protected function getConnection()
    {
        new FakeConnection('Oracle');
    }
    
    protected function wrap($text, $a = '"', $b = '"')
    {
        return strtoupper(parent::wrap($text, $a, $b));
    }
}