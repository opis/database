<?php
/* ===========================================================================
 * Copyright 2013-2018 Opis
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\Test\SQL;

class JoinTest extends BaseClass
{
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