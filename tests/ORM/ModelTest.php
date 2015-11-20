<?php

use Opis\Database\Model;
class ModelTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException \BadMethodCallException
    */
    public function testThatItThrowsWhenGetConnectionIsUnimplemented()
    {
        Model::getConnection();
    }
}
