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

use Opis\Database\Test\ORM\Entities\AutomatedEntity1;
use Opis\Database\Test\ORM\Entities\AutomatedEntity2;
use Opis\Database\Test\ORM\Entities\CKRecord;
use Opis\Database\Test\ORM\Entities\Tag;
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testInstantiate()
    {
        $tag = em()->create(Tag::class);
        $this->assertNotNull($tag);
        $this->assertInstanceOf(Tag::class, $tag);
    }

    public function testCreate()
    {
        $count = entity(Tag::class)->count();
        /** @var Tag $tag */
        $tag = em()->create(Tag::class);
        $tag->setName('tag3');
        $this->assertEquals('', $tag->getEventName());
        $this->assertTrue(em()->save($tag));
        $this->assertEquals('tag3', $tag->name());
        $this->assertEquals('save', $tag->getEventName());
        $this->assertEquals($count + 1, entity(Tag::class)->count());
    }

    public function testCreateComposite()
    {
        $count = entity(CKRecord::class)->count();
        /** @var CKRecord $entity */
        $entity = em()->create(CKRecord::class);
        $entity->setData('c');
        $this->assertTrue(em()->save($entity));
        $this->assertEquals('c', $entity->getData());
        $this->assertEquals($count + 1, entity(CKRecord::class)->count());
    }

    public function testFailCreate()
    {
        /** @var Tag $tag */
        $tag = em()->create(Tag::class);
        $tag->setName('tag3');
        $this->assertFalse(em()->save($tag));
    }

    public function testCreatedAtAutomationFail()
    {
        /** @var AutomatedEntity1 $entity */
        $entity = em()->create(AutomatedEntity1::class);
        $entity->setData('c');
        $this->expectException(\Exception::class);
        $this->assertNull($entity->getCreatedAt());
    }

    public function testCreatedAtAutomation()
    {
        /** @var AutomatedEntity1 $entity */
        $entity = em()->create(AutomatedEntity1::class);
        $entity->setData('c');
        $this->assertTrue(em()->save($entity));
        $this->assertInstanceOf(\DateTime::class, $entity->getCreatedAt());
    }

    public function testCustomCreatedAtAutomation()
    {
        /** @var AutomatedEntity2 $entity */
        $entity = em()->create(AutomatedEntity2::class);
        $entity->setData('c');
        $this->assertTrue(em()->save($entity));
        $this->assertInstanceOf(\DateTime::class, $entity->getCreatedAt());
    }
}