<?php

class NuoDBCompilerTest extends CompilerTest
{
    
    protected function getConnection()
    {
        new FakeConnection('NuoDB');
    }
}