<?php

class NuoDBCompilerTest extends CompilerTest
{

    protected function getConnection()
    {
        return new FakeConnection('NuoDB');
    }
}
