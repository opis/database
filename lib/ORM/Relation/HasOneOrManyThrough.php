<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

namespace Opis\Database\ORM\Relation;

use Opis\Database\Entity;
use Opis\Database\EntityManager;
use Opis\Database\ORM\DataMapper;
use Opis\Database\ORM\EntityMapper;
use Opis\Database\ORM\EntityQuery;
use Opis\Database\ORM\LazyLoader;
use Opis\Database\ORM\Query;
use Opis\Database\ORM\Relation;
use Opis\Database\SQL\Delete;
use Opis\Database\SQL\Insert;
use Opis\Database\SQL\Join;
use Opis\Database\SQL\SQLStatement;

class HasOneOrManyThrough extends Relation
{
    /** @var  string|null */
    protected $juctionKey;

    /** @var  string|null */
    protected $junctionTable;

    /** @var  string|null */
    protected $joinTable;

    /** @var  string|null */
    protected $joinColumn;

    /** @var  bool */
    protected $hasMany;

    public function __construct(string $entityClass,
                                string $foreignKey = null,
                                string $junctionTable = null,
                                string $junctionKey = null,
                                bool $hasMany = false)
    {
        parent::__construct($entityClass, $foreignKey);
        $this->hasMany = $hasMany;
        $this->juctionKey = $junctionKey;
        $this->junctionTable = $junctionTable;
    }

    /**
     * @param DataMapper $data
     * @param $items
     */
    public function link(DataMapper $data, $items)
    {
        if(!is_array($items)){
            $items = [$items];
        }

        $manager = $data->getEntityManager();
        $owner = $data->getEntityMapper();
        $related = $manager->resolveEntityMapper($this->entityClass);

        if($this->junctionTable === null){
            $table = [$owner->getTable(), $related->getTable()];
            sort($table);
            $this->junctionTable = implode('_', $table);
        }

        if($this->juctionKey === null){
            $this->juctionKey = $related->getForeignKey();
        }

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        $table = $this->junctionTable;
        $col1 = $this->foreignKey;
        $col2 = $this->juctionKey;
        $val1 = $data->getColumn($owner->getPrimaryKey());
        $key = $related->getPrimaryKey();
        $connection = $manager->getConnection();

        $extractor = function () use($key){
            return $this->orm()->getColumn($key);
        };

        foreach ($items as $item){
            $val2 = is_subclass_of($item, $this->entityClass, false) ? $extractor->call($item) : $item;
            try{

                (new Insert($connection))->insert([
                    $col1 => $val1,
                    $col2 => $val2
                ])->into($table);

            }catch (\Exception $e){
                // Ignore
            }
        }
    }

    /**
     * @param DataMapper $data
     * @param $items
     */
    public function unlink(DataMapper $data, $items)
    {
        if(!is_array($items)){
            $items = [$items];
        }

        $manager = $data->getEntityManager();
        $owner = $data->getEntityMapper();
        $related = $manager->resolveEntityMapper($this->entityClass);

        if($this->junctionTable === null){
            $table = [$owner->getTable(), $related->getTable()];
            sort($table);
            $this->junctionTable = implode('_', $table);
        }

        if($this->juctionKey === null){
            $this->juctionKey = $related->getForeignKey();
        }

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        $table = $this->junctionTable;
        $col1 = $this->foreignKey;
        $col2 = $this->juctionKey;
        $val1 = $data->getColumn($owner->getPrimaryKey());
        $val2 = [];
        $key = $related->getPrimaryKey();
        $connection = $manager->getConnection();

        $extractor = function () use($key){
            return $this->orm()->getColumn($key);
        };

        foreach ($items as $item){
            $val2[] = is_subclass_of($item, $this->entityClass, false) ? $extractor->call($item) : $item;
        }

        try{
            (new Delete($connection, $table))
                ->where($col1)->is($val1)
                ->andWhere($col2)->in($val2)
                ->delete();
        }
        catch(\Exception $e){
            //ignore
        }
    }


    /**
     * @param EntityManager $manager
     * @param EntityMapper $owner
     * @param array $options
     * @return LazyLoader
     */
    protected function getLazyLoader(EntityManager $manager, EntityMapper $owner, array $options)
    {
        $related = $manager->resolveEntityMapper($this->entityClass);

        if($this->junctionTable === null){
            $table = [$owner->getTable(), $related->getTable()];
            sort($table);
            $this->junctionTable = implode('_', $table);
        }

        if($this->juctionKey === null){
            $this->juctionKey = $related->getForeignKey();
        }

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        if($this->joinTable === null){
            $this->joinTable = $related->getTable();
        }

        if($this->joinColumn === null){
            $this->joinColumn = $related->getPrimaryKey();
        }

        $statement = new SQLStatement();

        $select = new class($manager, $related, $statement, $this->junctionTable) extends EntityQuery{

            protected $junctionTable;

            public function __construct(EntityManager $entityManager, EntityMapper $entityMapper, $statement, $table)
            {
                parent::__construct($entityManager, $entityMapper, $statement);
                $this->junctionTable = $table;
            }

            protected function buildQuery()
            {
                $this->locked = true;
                $this->sql->addTables([$this->junctionTable]);
                return $this;
            }

            protected function isReadOnly(): bool
            {
                return count($this->sql->getJoins()) > 1;
            }
        };

        $linkKey = 'hidden_' . $this->junctionTable . '_' . $this->foreignKey;

        $select->join($this->joinTable, function (Join $join){
            $join->on($this->junctionTable . '.' . $this->juctionKey, $this->joinTable . '.' . $this->joinColumn);
        })
            ->where($this->junctionTable . '.' . $this->foreignKey)->in($options['ids']);

        $statement->addColumn($this->joinTable . '.*');
        $statement->addColumn($this->junctionTable . '.' . $this->foreignKey, $linkKey);

        if($options['callback'] !== null){
            $options['callback'](new Query($statement));
        }

        $select->with($options['with'], $options['immediate']);

        return new LazyLoader($select, $owner->getPrimaryKey(), $linkKey, $this->hasMany, $options['immediate']);
    }

    /**
     * @param DataMapper $data
     * @param callable|null $callback
     * @return mixed
     */
    protected function getResult(DataMapper $data, callable $callback = null)
    {
        $manager = $data->getEntityManager();
        $owner = $data->getEntityMapper();
        $related = $manager->resolveEntityMapper($this->entityClass);

        if($this->junctionTable === null){
            $table = [$owner->getTable(), $related->getTable()];
            sort($table);
            $this->junctionTable = implode('_', $table);
        }

        if($this->juctionKey === null){
            $this->juctionKey = $related->getForeignKey();
        }

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        if($this->joinTable === null){
            $this->joinTable = $related->getTable();
        }

        if($this->joinColumn === null){
            $this->joinColumn = $related->getPrimaryKey();
        }

        $statement = new SQLStatement();

        $select = new class($manager, $related, $statement, $this->junctionTable) extends EntityQuery{

            protected $junctionTable;

            public function __construct(EntityManager $entityManager, EntityMapper $entityMapper, $statement, $table)
            {
                parent::__construct($entityManager, $entityMapper, $statement);
                $this->junctionTable = $table;
            }

            protected function buildQuery()
            {
                $this->locked = true;
                $this->sql->addTables([$this->junctionTable]);
                return $this;
            }
            
            protected function isReadOnly(): bool
            {
                return count($this->sql->getJoins()) > 1;
            }
        };

        $select->join($this->joinTable, function (Join $join){
            $join->on($this->junctionTable . '.' . $this->juctionKey, $this->joinTable . '.' . $this->joinColumn);
        })
        ->where($this->junctionTable . '.' . $this->foreignKey)->is($data->getColumn($owner->getPrimaryKey()));

        $statement->addColumn($this->joinTable . '.*');

        if($this->queryCallback !== null || $callback !== null){
            $query = new Query($statement);
            if($this->queryCallback !== null){
                ($this->queryCallback)($query);
            }
            if($callback !== null){
                $callback($query);
            }
        }

        return $this->hasMany ? $select->all() : $select->get();
    }

}