<?php

class OracleCompilerTest extends CompilerTest
{
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('Oracle'));
    }
}