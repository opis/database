<?php


use Opis\Database\Connection;
use Opis\Database\Database;

class SqliteTest extends PHPUnit_Framework_TestCase
{
    /** @var Connection */
    private $con;
    /** @var Database */
    private $db;

    public function setUp()
    {
        $this->con = new Connection('sqlite::memory:');
        $this->con->logQueries(true);
        $this->db = new Database($this->con);
        $this->db->schema()->create('simple', function (Opis\Database\Schema\CreateTable $table) {
            $table->integer('id')->primary();
        });
    }

    public function testThatItCreatesTables()
    {
        $defs = $this->con->query('PRAGMA table_info(simple)')->all();
        $this->assertNotEmpty($defs);
    }

    public function testThatItInsertsRows()
    {
        $this->db->insert(array('id' => null))->into('simple');
        $all = $this->db->from('simple')->select()->all();
        $this->assertNotEmpty($all);
        $this->assertNotNull($all[0]->id);
    }
}
