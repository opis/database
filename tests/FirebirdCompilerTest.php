<?php

class FirebirdCompilerTest extends CompilerTest
{
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('Firebird'));
    }
}