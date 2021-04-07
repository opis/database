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

use Opis\Database\Test\ORM\Entities\{Article, User};
use function Opis\Database\Test\{
    entityManager as em,
    query as entity
};
use PHPUnit\Framework\TestCase;

class LazyLoaderTest extends TestCase
{
    public function testHasOne()
    {
        em()->getConnection()->logQueries();
        $initial = count(em()->getConnection()->getLog());
        /** @var User[] $users */
        $users = entity(User::class)->with('profile')->all();
        foreach ($users as $user) {
            $user->profile();
        }
        $this->assertEquals(2, count(em()->getConnection()->getLog()) - $initial);
        em()->getConnection()->logQueries(false);
    }

    public function testHasMany()
    {
        em()->getConnection()->logQueries();
        $initial = count(em()->getConnection()->getLog());
        /** @var User[] $users */
        $users = entity(User::class)->with('articles')->all();
        foreach ($users as $user) {
            $user->articles();
        }
        $this->assertEquals(2, count(em()->getConnection()->getLog()) - $initial);
        em()->getConnection()->logQueries(false);
    }

    public function testBelongsTo()
    {
        em()->getConnection()->logQueries();
        $initial = count(em()->getConnection()->getLog());
        /** @var Article[] $articles */
        $articles = entity(Article::class)->with('author')->all();
        foreach ($articles as $article) {
            $article->author();
        }
        $this->assertEquals(2, count(em()->getConnection()->getLog()) - $initial);
        em()->getConnection()->logQueries(false);
    }

    public function testShareOne()
    {
        em()->getConnection()->logQueries();
        $initial = count(em()->getConnection()->getLog());
        /** @var Article[] $articles */
        $articles = entity(Article::class)->with('first_tag')->all();
        foreach ($articles as $article) {
            $tag = $article->firstTag();
            if ($article->id() === "00000000000000000000000000000001") {
                $this->assertNotNull($tag);
            } else {
                $this->assertNull($tag);
            }
        }
        $this->assertEquals(2, count(em()->getConnection()->getLog()) - $initial);
        em()->getConnection()->logQueries(false);
    }

    public function testShareMany()
    {
        em()->getConnection()->logQueries();
        $initial = count(em()->getConnection()->getLog());
        /** @var Article[] $articles */
        $articles = entity(Article::class)->with('tags')->all();
        foreach ($articles as $article) {
            if ($article->id() === "00000000000000000000000000000001") {
                $this->assertNotEmpty($article->tags());
                $this->assertEquals(2, count($article->tags()));
            } else {
                $this->assertEmpty($article->tags());
            }
        }
        $this->assertEquals(2, count(em()->getConnection()->getLog()) - $initial);
        em()->getConnection()->logQueries(false);
    }
}