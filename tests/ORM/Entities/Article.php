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

namespace Opis\Database\Test\ORM\Entities;

use Opis\Database\ORM\Entity;
use Opis\Database\ORM\Internal\Query;
use Opis\Database\ORM\{
    EntityMapper
};
use Opis\Database\ORM\MappableEntity;
use function Opis\Database\Test\unique_id;

class Article extends Entity implements MappableEntity
{
    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    public function title(): string
    {
        return $this->orm()->getColumn('title');
    }

    public function content(): string
    {
        return $this->orm()->getColumn('content');
    }

    public function author(): User
    {
        return $this->orm()->getRelated('author');
    }

    /**
     * @return Tag[]
     */
    public function tags(): array
    {
        return $this->orm()->getRelated('tags');
    }

    /**
     * @return Tag|null
     */
    public function firstTag()
    {
        return $this->orm()->getRelated('first_tag');
    }

    public function addTag(Tag $tag): self
    {
        $this->orm()->link('tags', $tag);
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->orm()->unlink('tags', $tag);
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->orm()->setColumn('title', $title);
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->orm()->setColumn('content', $content);
        return $this;
    }

    public function setAuthor(User $user): self
    {
        $this->orm()->setRelated('author', $user);
        return $this;
    }

    public function setPublished(bool $value): self
    {
        $this->orm()->setColumn('published', $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->primaryKeyGenerator(function(){
            return unique_id();
        });

        $mapper->cast([
            'published' => 'bool'
        ]);

        $mapper->relation('author')->belongsTo(User::class);
        $mapper->relation('tags')->shareMany(Tag::class);
        $mapper->relation('first_tag')->shareOne(Tag::class);
    }

}