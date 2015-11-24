<?php


class SqliteTest extends PHPUnit_Framework_TestCase
{
    public function testThatItCreatesTables()
    {
        $con = new \Opis\Database\Connection('sqlite::memory:');
        $db = new \Opis\Database\Database($con);
        $db->schema()->create('simple', function (Opis\Database\Schema\CreateTable $table) {
            $table->integer('id');
        });
        $defs = $con->query('PRAGMA table_info(simple)')->all();
        $this->assertNotEmpty($defs);
    }
}
