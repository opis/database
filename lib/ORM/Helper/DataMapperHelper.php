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

namespace Opis\Database\ORM\Helper;

use Opis\Database\ORM\DataMapper;

class DataMapperHelper extends DataMapper
{

    /**
     * @param DataMapper $data
     * @param $id
     * @return bool
     */
    public static function markAsSaved(DataMapper $data, $id): bool
    {
        $data->rawColumns[$data->mapper->getPrimaryKey()] = $id;
        $data->dehidrated = true;
        $data->isNew = false;
        $data->modified = [];
        return true;
    }

    /**
     * @param DataMapper $data
     */
    public static function markAsDeleted(DataMapper $data)
    {
        $data->deleted = true;
    }

    /**
     * @param DataMapper $data
     */
    public static function markAsUpdated(DataMapper $data)
    {
        $data->modified = [];
    }
}