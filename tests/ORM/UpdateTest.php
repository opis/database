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

use Opis\Database\Test\ORM\Entities\{Article, AutomatedEntity1, AutomatedEntity2, Tag, User};
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testUpdate()
    {
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $user->setAge(33);
        $this->assertEquals('', $user->getEventName());
        $this->assertTrue(em()->save($user));
        $this->assertEquals(33, $user->age());
        $this->assertEquals('update', $user->getEventName());
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $this->assertEquals(33, $user->age());
        $this->assertEquals('', $user->getEventName());
    }

    public function testUpdateBoolean()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000001');
        $article->setPublished(true);
        $this->assertTrue(em()->save($article));
    }

    public function testFailUpdatePrimaryKeyIfNotNew()
    {
        /** @var Tag $tag */
        $tag = entity(Tag::class)->find('tag3');
        $tag->setName('foo');
        $this->assertTrue(em()->save($tag));
        $this->assertEquals('tag3', $tag->name());
    }

    public function testUpdatePrimaryKeyIfNotNew()
    {
        $success = entity(Tag::class)
            ->where('id')->is('tag3')
            ->update(['id' => 'foo']);
        $this->assertEquals(1, $success);
        /** @var Tag $tag */
        $tag = entity(Tag::class)->find('foo');
        $this->assertNotNull($tag);
        $this->assertNull(entity(Tag::class)->find('tag3'));
        $this->assertEquals('foo', $tag->name());
    }

    public function testUpdatedAt()
    {
        /** @var AutomatedEntity1 $entity */
        $entity = entity(AutomatedEntity1::class)->find(2);
        $this->assertNotNull($entity);
        $this->assertNull($entity->getUpdatedAt());
        $entity->setData('bb');
        $this->assertTrue(em()->save($entity));
        $this->assertInstanceOf(\DateTime::class, $entity->getUpdatedAt());
    }

    public function testCustomUpdatedAt()
    {
        /** @var AutomatedEntity2 $entity */
        $entity = entity(AutomatedEntity2::class)->find(2);
        $this->assertNotNull($entity);
        $this->assertNull($entity->getUpdatedAt());
        $entity->setData('bb');
        $this->assertTrue(em()->save($entity));
        $this->assertInstanceOf(\DateTime::class, $entity->getUpdatedAt());
    }
}