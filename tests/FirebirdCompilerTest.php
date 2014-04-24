<?php

class FirebirdCompilerTest extends CompilerTest
{
    protected function getConnection()
    {
        new FakeConnection('Firebird');
    }
}