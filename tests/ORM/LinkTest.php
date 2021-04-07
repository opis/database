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

use Opis\Database\Test\ORM\Entities\{Article, Tag};
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function testAddSingleLink()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        /** @var Tag $tag */
        $tag = entity(Tag::class)->find("tag1");
        $article->addTag($tag);
        $this->assertEmpty($article->tags());
        $this->assertTrue(em()->save($article));
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        $tags = array_map(function(Tag $tag){
            return $tag->name();
        }, $article->tags());

        $this->assertEquals(['tag1'], $tags);
    }

    public function testRemoveSingleLink()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        /** @var Tag $tag */
        $tag = entity(Tag::class)->find("tag1");
        $article->removeTag($tag);
        $this->assertTrue(em()->save($article));
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        $this->assertEmpty($article->tags());
    }

    public function testAddMultipleLinks()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        /** @var Tag $tag */
        foreach (entity(Tag::class)->findAll('tag1', 'tag2') as $tag) {
            $article->addTag($tag);
        }
        $this->assertEmpty($article->tags());
        $this->assertTrue(em()->save($article));
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        $tags = array_map(function(Tag $tag){
            return $tag->name();
        }, $article->tags());

        $this->assertEquals(['tag1', 'tag2'], $tags);
    }

    public function testRemoveMultipleLinks()
    {
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        /** @var Tag $tag */
        foreach (entity(Tag::class)->findAll('tag1', 'tag2') as $tag) {
            $article->removeTag($tag);
        }
        $this->assertNotEmpty($article->tags());
        $this->assertTrue(em()->save($article));
        /** @var Article $article */
        $article = entity(Article::class)->find('00000000000000000000000000000002');
        $this->assertEmpty($article->tags());
    }
}