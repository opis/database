<?php

class FirebirdCompilerTest extends CompilerTest
{

    protected function getConnection()
    {
        return new FakeConnection('Firebird');
    }
}
