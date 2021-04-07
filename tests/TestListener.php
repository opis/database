<?php
/* ===========================================================================
 * Copyright 2021 Zindex Software
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

namespace Opis\Database\Test;

use Opis\Database\Connection;
use Opis\Database\Database;
use Opis\Database\EntityManager;
use Opis\Database\Schema\Blueprint;
use PHPUnit\Runner\BeforeTestHook;

class TestListener implements BeforeTestHook
{
    private bool $init = false;

    public function executeBeforeTest(string $test): void
    {
        if ($this->init) {
            return;
        }
        if (str_starts_with($test, 'Opis\\Database\\Test\\ORM\\')) {
            $this->init = true;
            $this->setup();
        }
    }

    private function setup()
    {
        $file = __DIR__ . '/db.sql';

        if (is_file($file)) {
            unlink($file);
        }

        $connection = new Connection('sqlite:' . $file);
        $connection->initCommand('PRAGMA foreign_keys = ON');
        $db = new Database($connection);
        $schema = $db->schema();

        $schema->create('users', function(Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name')->notNull();
            $table->integer('age')->size('small')->notNull();
            $table->string('gender', 1)->notNull();
        });

        $schema->create('articles', function(Blueprint $table){
            $table->string('id', 32)->primary();
            $table->integer('user_id')->notNull()->index();
            $table->boolean('published')->notNull();
            $table->string('title')->notNull();
            $table->string('content')->notNull();

            $table->foreign('user_id')
                ->references('users', 'id')
                ->onUpdate('cascade')
                ->onUpdate('cascade');
        });

        $schema->create('profiles', function(Blueprint $table){
            $table->string('id', 32)->primary();
            $table->integer('user_id')->notNull()->index();
            $table->string('city')->notNull();

            $table->foreign('user_id')
                ->references('users', 'id')
                ->onUpdate('cascade')
                ->onUpdate('cascade');
        });

        $schema->create('tags', function(Blueprint $table){
            $table->string('id', 32)->primary();
        });

        $schema->create('ck_records', function(Blueprint $table){
            $table->integer('key1')->notNull();
            $table->integer('key2')->notNull();
            $table->string('data');
            $table->primary(['key1', 'key2']);
        });

        $schema->create('ck_related', function(Blueprint $table){
            $table->integer('id')->primary();
            $table->integer('ck_record_key1')->notNull();
            $table->integer('ck_record_key2')->notNull();
            $table->index(['ck_record_key1', 'ck_record_key2']);

            $table->foreign(['ck_record_key1', 'ck_record_key2'])
                ->references('ck_records', 'key1', 'key2')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        $schema->create('articles_tags', function(Blueprint $table){
            $table->string('article_id', 32);
            $table->string('tag_id', 32);
            $table->primary(['article_id', 'tag_id']);
            $table->foreign('article_id')
                ->references('articles', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('tag_id')
                ->references('tags', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        $schema->create('automated_entity_1', function(Blueprint $table){
            $table->integer('id')->autoincrement();
            $table->string('data')->notNull();
            $table->softDelete();
            $table->timestamps();
        });


        $schema->create('automated_entity_2', function(Blueprint $table){
            $table->integer('id')->autoincrement();
            $table->string('data')->notNull();
            $table->softDelete('d_at');
            $table->timestamps('c_at', 'u_at');
        });

        $data = json_decode(file_get_contents(__DIR__ . '/data/entities.json'), true);

        foreach ($data as $table => $records) {
            foreach ($records as $record) {
                $db->insert($record)->into($table);
            }
        }

        unset($data);

        \Opis\Database\Test\entityManager($connection->getEntityManager());
    }
}