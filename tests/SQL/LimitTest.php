<?php

namespace Opis\Database\Test\SQL;

class LimitTest extends BaseClass
{
    public function testLimit()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC LIMIT 25';
        $actual = $this->db->from('users')->orderBy('name')->limit(25)->select();
        $this->assertEquals($expected, $actual);
    }

    public function testOffset()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC LIMIT 25 OFFSET 10';
        $actual = $this->db->from('users')->orderBy('name')->limit(25)->offset(10)->select();
        $this->assertEquals($expected, $actual);
    }

    /*
    public function testOffsetWithoutLimit()
    {
        $expected = 'SELECT * FROM "users" ORDER BY "name" ASC';
        $actual = $this->db->from('users')->orderBy('name')->offset(10)->select();
        $this->assertEquals($expected, $actual);
    }*/
}