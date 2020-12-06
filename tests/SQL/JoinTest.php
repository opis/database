<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

use Opis\Database\SQL\Expression;
use Opis\Database\SQL\Join;

class JoinTest extends BaseClass
{
    public function testDefaultJoin()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id"';
        $this->db->from('users')
            ->join('profiles', function (Join $join) {
                $join->on('users.id', 'profiles.id');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testDefaultJoinGTE()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" >= "profiles"."id"';
        $this->db->from('users')
            ->join('profiles', function (Join $join) {
                $join->on('users.id', 'profiles.id', '>=');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testDefaultJoinAnd()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id" AND "users"."email" = "profile"."primary_email"';
        $this->db->from('users')
            ->join('profiles', function (Join $join) {
                $join->on('users.id', 'profiles.id')
                    ->andOn('users.email', 'profile.primary_email');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testDefaultJoinOr()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id" OR "users"."email" = "profile"."primary_email"';
        $this->db->from('users')
            ->join('profiles', function (Join $join) {
                $join->on('users.id', 'profiles.id')
                    ->orOn('users.email', 'profile.primary_email');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testDefaultJoinGroup()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = "profiles"."id" AND ("users"."email" = "profiles"."primary_email" OR "users"."email" = "profiles"."secondary_email")';
        $this->db->from('users')
            ->join('profiles', function (Join $join) {
                $join->on('users.id', 'profiles.id')
                    ->andOn(function (Join $join) {
                        $join->on('users.email', 'profiles.primary_email')
                            ->orOn('users.email', 'profiles.secondary_email');
                    });
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testDefaultJoinAlias()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" AS "p" ON "users"."id" = "p"."id"';
        $this->db->from('users')
            ->join(['profiles' => 'p'], function (Join $join) {
                $join->on('users.id', 'p.id');
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testCrossJoin()
    {
        $expected = 'SELECT * FROM "users" CROSS JOIN "profiles"';
        $this->db->from('users')
            ->crossJoin('profiles')
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testJoinExpression()
    {
        $expected = 'SELECT * FROM "users" INNER JOIN "profiles" ON "users"."id" = LEN("profiles"."name")';
        $this->db->from('users')
            ->join('profiles', function (Join $join) {
                $join->on(function (Expression $expr) {
                    $expr->column('users.id')->{'='}->len('profiles.name');
                }, true);
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }
}