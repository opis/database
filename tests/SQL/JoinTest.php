<?php

namespace Opis\Database\Test\SQL;

use Opis\Database\Database;
use Opis\Database\Test\Connection;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{
    protected static $database;
    /** @var  Database */
    protected $db;

    public static function setUpBeforeClass()
    {
        static::$database = new Database(new Connection(''));
    }

    public function setUp()
    {
        $this->db = static::$database;
    }

    public function testDefaultJoin()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id"';
        $actual = $this->db->from('users')
            ->join('profiles', function ($join) {
                $join->on('users.id', 'profiles.id');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultJoinGTE()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" >= "profiles"."id"';
        $actual = $this->db->from('users')
            ->join('profiles', function ($join) {
                $join->on('users.id', 'profiles.id', '>=');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultJoinAnd()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id" AND "users"."email" = "profile"."primary_email"';
        $actual = $this->db->from('users')
            ->join('profiles', function ($join) {
                $join->on('users.id', 'profiles.id')
                    ->andOn('users.email', 'profile.primary_email');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultJoinOr()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id" OR "users"."email" = "profile"."primary_email"';
        $actual = $this->db->from('users')
            ->join('profiles', function ($join) {
                $join->on('users.id', 'profiles.id')
                    ->orOn('users.email', 'profile.primary_email');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultJoinGroup()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id" AND ("users"."email" = "profiles"."primary_email" OR "users"."email" = "profiles"."secondary_email")';
        $actual = $this->db->from('users')
            ->join('profiles', function ($join) {
                $join->on('users.id', 'profiles.id')
                    ->andOn(function ($join) {
                        $join->on('users.email', 'profiles.primary_email')
                            ->orOn('users.email', 'profiles.secondary_email');
                    });
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }

    public function testDefaultJoinAlias()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" AS "p" ON "users"."id" = "p"."id"';
        $actual = $this->db->from('users')
            ->join(['profiles' => 'p'], function ($join) {
                $join->on('users.id', 'p.id');
            })
            ->select();
        $this->assertEquals($expected, $actual);
    }
}