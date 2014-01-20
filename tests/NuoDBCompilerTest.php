<?php

class NuoDBCompilerTest extends CompilerTest
{
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('NuoDB'));
    }
}