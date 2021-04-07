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

use Opis\Database\Test\ORM\Entities\{AutomatedEntity1, AutomatedEntity2, Tag};
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    public function testDelete()
    {
        /** @var Tag $tag */
        $tag = entity(Tag::class)->find('foo');
        $this->assertEquals('', $tag->getEventName());
        $this->assertTrue(em()->delete($tag));
        $this->assertEquals('delete', $tag->getEventName());
        $this->assertNull(entity(Tag::class)->find('foo'));
        $this->expectException(\Exception::class);
        em()->delete($tag);
    }

    public function testSoftDelete()
    {
        $count = entity(AutomatedEntity1::class)->count();
        $entity = entity(AutomatedEntity1::class)->find(1);
        em()->delete($entity);
        $this->assertEquals($count - 1, entity(AutomatedEntity1::class)->count());
        $this->assertEquals($count, entity(AutomatedEntity1::class)->withSoftDeleted()->count());
        $this->assertEquals(1, entity(AutomatedEntity1::class)->onlySoftDeleted()->count());
        $entity = entity(AutomatedEntity1::class)->find(1);
        $this->assertNull($entity);
        $entity = entity(AutomatedEntity1::class)->withSoftDeleted()->find(1);
        $this->assertInstanceOf(AutomatedEntity1::class, $entity);
        em()->delete($entity, true);
        $this->assertEquals($count - 1, entity(AutomatedEntity1::class)->count());
        $this->assertEquals($count - 1, entity(AutomatedEntity1::class)->withSoftDeleted()->count());
        $this->assertEquals(0, entity(AutomatedEntity1::class)->onlySoftDeleted()->count());
    }

    public function testCustomSoftDelete()
    {
        $count = entity(AutomatedEntity2::class)->count();
        $entity = entity(AutomatedEntity2::class)->find(1);
        em()->delete($entity);
        $this->assertEquals($count - 1, entity(AutomatedEntity2::class)->count());
        $this->assertEquals($count, entity(AutomatedEntity2::class)->withSoftDeleted()->count());
        $this->assertEquals(1, entity(AutomatedEntity2::class)->onlySoftDeleted()->count());
        $entity = entity(AutomatedEntity2::class)->find(1);
        $this->assertNull($entity);
        $entity = entity(AutomatedEntity2::class)->withSoftDeleted()->find(1);
        $this->assertInstanceOf(AutomatedEntity2::class, $entity);
        em()->delete($entity, true);
        $this->assertEquals($count - 1, entity(AutomatedEntity2::class)->count());
        $this->assertEquals($count - 1, entity(AutomatedEntity2::class)->withSoftDeleted()->count());
        $this->assertEquals(0, entity(AutomatedEntity2::class)->onlySoftDeleted()->count());
    }

}