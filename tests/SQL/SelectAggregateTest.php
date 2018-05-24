<?php

namespace Opis\Database\Test\SQL;

class SelectAggregateTest extends BaseClass
{
    public function testCountNoColumns()
    {
        $expected = 'SELECT COUNT(*) FROM "users"';
        $actual = $this->db->from('users')->count();
        $this->assertEquals($expected, $actual);
    }

    public function testCountOneColumn()
    {
        $expected = 'SELECT COUNT("description") FROM "users"';
        $actual = $this->db->from('users')->count('description');
        $this->assertEquals($expected, $actual);
    }

    public function testCountOneColumnDistinct()
    {
        $expected = 'SELECT COUNT(DISTINCT "description") FROM "users"';
        $actual = $this->db->from('users')->count('description', true);
        $this->assertEquals($expected, $actual);
    }

    public function testLargestValue()
    {
        $expected = 'SELECT MAX("age") FROM "users"';
        $actual = $this->db->from('users')->max('age');
        $this->assertEquals($expected, $actual);
    }

    public function testSmallestValue()
    {
        $expected = 'SELECT MIN("age") FROM "users"';
        $actual = $this->db->from('users')->min('age');
        $this->assertEquals($expected, $actual);
    }

    public function testAverageValue()
    {
        $expected = 'SELECT AVG("age") FROM "users"';
        $actual = $this->db->from('users')->avg('age');
        $this->assertEquals($expected, $actual);
    }

    public function testTotalSum()
    {
        $expected = 'SELECT SUM("age") FROM "users"';
        $actual = $this->db->from('users')->sum('age');
        $this->assertEquals($expected, $actual);
    }


}