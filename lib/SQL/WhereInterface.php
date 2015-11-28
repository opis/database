<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

namespace Opis\Database\SQL;

use Closure;

interface WhereInterface
{
    function getWhereClauses();

    /**
     * @param $column
     * @param null $value
     * @param string $operator
     * @return mixed
     */
    function where($column, $value = null, $operator = '=');

    /**
     * @param $column
     * @param null $value
     * @param string $operator
     * @return mixed
     */
    function andWhere($column, $value = null, $operator = '=');

    /**
     * @param $column
     * @param null $value
     * @param string $operator
     * @return mixed
     */
    function orWhere($column, $value = null, $operator = '=');

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @return mixed
     */
    function whereBetween($column, $value1, $value2);

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @return mixed
     */
    function andWhereBetween($column, $value1, $value2);

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @return mixed
     */
    function orWhereBetween($column, $value1, $value2);

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @return mixed
     */
    function whereNotBetween($column, $value1, $value2);

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @return mixed
     */
    function andWhereNotBetween($column, $value1, $value2);

    /**
     * @param $column
     * @param $value1
     * @param $value2
     * @return mixed
     */
    function orWhereNotBetween($column, $value1, $value2);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function whereLike($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function andWhereLike($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function orWhereLike($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function whereNotLike($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function andWhereNotLike($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function orWhereNotLike($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function whereIn($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function andWhereIn($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function orWhereIn($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function whereNotIn($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function andWhereNotIn($column, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    function orWhereNotIn($column, $value);

    /**
     * @param $column
     * @return mixed
     */
    function whereNull($column);

    /**
     * @param $column
     * @return mixed
     */
    function andWhereNull($column);

    /**
     * @param $column
     * @return mixed
     */
    function orWhereNull($column);

    /**
     * @param $column
     * @return mixed
     */
    function whereNotNull($column);

    /**
     * @param $column
     * @return mixed
     */
    function andWhereNotNull($column);

    /**
     * @param $column
     * @return mixed
     */
    function orWhereNotNull($column);

    /**
     * @param Closure $select
     * @return mixed
     */
    function whereExists(Closure $select);

    /**
     * @param Closure $select
     * @return mixed
     */
    function andWhereExists(Closure $select);

    /**
     * @param Closure $select
     * @return mixed
     */
    function orWhereExists(Closure $select);

    /**
     * @param Closure $select
     * @return mixed
     */
    function whereNotExists(Closure $select);

    /**
     * @param Closure $select
     * @return mixed
     */
    function andWhereNotExists(Closure $select);

    /**
     * @param Closure $select
     * @return mixed
     */
    function orWhereNotExists(Closure $select);
}