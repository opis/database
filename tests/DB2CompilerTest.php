<?php

class DB2CompilerTest extends CompilerTest
{

    protected function getConnection()
    {
        return new FakeConnection('DB2');
    }
}
