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

use Opis\Database\Test\ORM\Entities\{Article, CKRecord, CKRelated, Tag, User};
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class RelationsTest extends TestCase
{
    public function testHasOne()
    {
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $this->assertEquals("New York", $user->profile()->city());
    }

    public function testHasMany()
    {
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $this->assertEquals("Hello, World!", $user->articles()[0]->title());
    }

    public function testHasManyComposite()
    {
        /** @var CKRecord $entity */
        $entity = entity(CKRecord::class)->find(['key1' => 1, 'key2' => 1]);
        em()->getConnection()->logQueries();
        $related = $entity->getCKRelated();
        $this->assertEquals(2, count($related));
        em()->getConnection()->logQueries(false);
    }

    public function testHasManyUseUnprefixed()
    {
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $this->assertEquals(3, count($user->articles()));
        $this->assertEquals(3, count($user->publishedArticles()));
        $this->assertEquals(3, count($user->unpublishedArticles()));
    }

    public function testHasManyUsePrefixed()
    {
        /** @var User $user */
        $user = entity(User::class)->find(1);
        $this->assertEquals(3, count($user->articles()));
        $this->assertEquals(2, count($user->publishedArticles(true)));
        $this->assertEquals(1, count($user->unpublishedArticles(true)));
    }

    public function testBelongsTo()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find("00000000000000000000000000000001");
        $this->assertEquals("Emma", $article->author()->name());
    }

    public function testBelongsToComposite()
    {
        /** @var CKRelated $entity */
        $entity = entity(CKRelated::class)->find(3);
        $related = $entity->getRecord();
        $this->assertInstanceOf(CKRecord::class, $related);
        $this->assertEquals('k12', $related->getData());
    }

    public function testShareOne()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find("00000000000000000000000000000001");
        $this->assertEquals("tag1", $article->firstTag()->name());
    }

    public function testShareMany()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find("00000000000000000000000000000001");
        $tags = array_map(function (Tag $tag){
            return $tag->name();
        }, $article->tags());
        $this->assertEquals(["tag1", "tag2"], $tags);
    }
}