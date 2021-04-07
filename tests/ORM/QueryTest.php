<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

namespace Opis\Database\Test\ORM;

use Opis\Database\Test\ORM\Entities\{CKRecord, User};
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testConnection()
    {
        $this->assertNotNull(em()->getConnection());
    }

    public function testFindById()
    {
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $this->assertEquals(1, $user->id());
    }

    public function testFindByIdComposite()
    {
        /** @var CKRecord $e1 , $e2 */
        $e1 = entity(CKRecord::class)->find(['key1' => 1, 'key2' => 1]);
        $e2 = entity(CKRecord::class)->find(['key1' => 1, 'key2' => 2]);
        $this->assertEquals("k11", $e1->getData());
        $this->assertEquals("k12", $e2->getData());
    }

    public function testNotFoundById()
    {
        $this->assertNull(entity(User::class)->find(10000));
    }

    public function testNotFoundByIdComposite()
    {
        $this->assertNull(entity(CKRecord::class)->find(['key1' => 0, 'key2' => 1]));
    }

    public function testFindAll()
    {
        /** @var User[] $users */
        $users = entity(User::class)->findAll(1, 2, 3);
        $this->assertEquals(3, count($users));
    }

    public function testFindAllPartial()
    {
        /** @var User[] $users */
        $users = entity(User::class)->findAll(1, 2, 3, 1000);
        $this->assertEquals(3, count($users));
    }

    public function testFindAllComposite()
    {
        $entities = entity(CKRecord::class)->findAll(['key1' => 1, 'key2' => 1], ['key1' => 1, 'key2' => 2]);
        $this->assertEquals(2, count($entities));
    }

    public function testFindAllCompositePartial()
    {
        $entities = entity(CKRecord::class)->findAll(['key1' => 1, 'key2' => 1],
            ['key1' => 1, 'key2' => 2], ['key1' => 0, 'key2' => 1]);
        $this->assertEquals(2, count($entities));
    }

    public function testFilter1()
    {
        /** @var User $user */
        $user = entity(User::class)
            ->where('age')->is(30)
            ->get();
        $this->assertEquals('Emma', $user->name());
    }

    public function testFilter2()
    {
        /** @var User $user */
        $user = entity(User::class)
            ->where('age')->is(16)
            ->andWhere('gender')->is('m')
            ->get();
        $this->assertEquals('Noah', $user->name());
    }

    public function testFilter3()
    {
        $user = entity(User::class)
            ->where('age')->is(10)
            ->get();
        $this->assertNull($user);
    }

    public function testColumn()
    {
        $value = entity(User::class)->where('age')->is(45)->column('name');
        $this->assertEquals('Logan', $value);
    }

    public function testCount()
    {
        $value = entity(User::class)->where('age')->is(16)->count();
        $this->assertEquals(2, $value);
    }

    public function testCountColumn()
    {
        $value = entity(User::class)->count('age');
        $this->assertEquals(8, $value);
    }

    public function testCountColumnDistinct()
    {
        $value = entity(User::class)->count('age', true);
        $this->assertEquals(7, $value);
    }

    public function testMin()
    {
        $value = entity(User::class)->min('age');
        $this->assertEquals(16, $value);
    }

    public function testMax()
    {
        $value = entity(User::class)->max('age');
        $this->assertEquals(45, $value);
    }

    public function testSum()
    {
        $value = entity(User::class)
            ->where('name')->in(['Ava', 'Liam'])
            ->sum('age');
        $this->assertEquals(44, $value);
    }

    public function testAvg()
    {
        $value = entity(User::class)
            ->where('name')->in(['Oliver', 'Isabella', 'Olivia'])
            ->avg('age');
        $this->assertEquals(26, $value);
    }
}