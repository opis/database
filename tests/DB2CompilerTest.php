<?php

class DB2CompilerTest extends CompilerTest
{
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('DB2'));
    }
}