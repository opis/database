<?php
/* ===========================================================================
 * Copyright 2019 Zindex Software
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

use Opis\Database\SQL\ColumnExpression;
use Opis\Database\SQL\Expression;
use Opis\Database\SQL\HavingExpression;
use Opis\Database\SQL\HavingStatement;
use Opis\Database\SQL\Join;

class HavingTest extends BaseClass
{
    public function testColumn()
    {
        $expected = 'SELECT * FROM "users" GROUP BY "age" HAVING COUNT("friends") > 5';
        $this->db->from('users')
            ->groupBy('age')
            ->having('friends', function (HavingExpression $column) {
                $column->count()->gt(5);
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testExpression()
    {
        $expected = 'SELECT * FROM "users" GROUP BY "age" HAVING COUNT("friends" * 2) > 5';
        $this->db->from('users')
            ->groupBy('age')
            ->having(function (Expression $expr) {
                $expr->column('friends')->{'*'}->value(2);
            }, function (HavingExpression $column) {
                $column->count()->gt(5);
            })
            ->select();
        $this->assertEquals($expected, $this->getSQL());
    }

    public function testNested()
    {
        $expected = 'SELECT COUNT("orders"."id") AS "total_orders", "customers"."name" AS "name" FROM "customers" LEFT JOIN "orders" ON "customers"."id" = "orders"."cid" GROUP BY LCASE("customers"."name") HAVING COUNT("orders"."id") > 10 AND (SUM("orders"."value") >= 1000 OR MIN(ROUND("orders"."value", 2)) >= 500)';
        $this->db->from('customers')
            ->leftJoin('orders', function(Join $join){
                $join->on('customers.id', 'orders.cid');
            })
            ->groupBy(function (Expression $expr) {
                $expr->lcase('customers.name');
            })
            ->having('orders.id', function(HavingExpression $column){
                $column->count()->gt(10);
            })
            ->andHaving(function(HavingStatement $group){
                $group->having('orders.value', function(HavingExpression $column){
                    $column->sum()->gte(1000);
                })
                    ->orHaving(function (Expression $expr) {
                        $expr->round('orders.value', 2);
                    }, function(HavingExpression $column){
                        $column->min()->gte(500);
                    });
            })
            ->select(function(ColumnExpression $include){
                $include->count('orders.id', 'total_orders')
                    ->column('customers.name', 'name');
            });
        $this->assertEquals($expected, $this->getSQL());
    }
}