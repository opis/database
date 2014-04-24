<?php

class DB2CompilerTest extends CompilerTest
{   
    protected function getConnection()
    {
        new FakeConnection('DB2');
    }
}