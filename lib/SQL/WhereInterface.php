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
    
    function where($column, $value = null, $operator = '=');
    
    function andWhere($column, $value = null, $operator = '=');
    
    function orWhere($column, $value = null, $operator = '=');
    
    function whereBetween($column, $value1, $value2);
    
    function andWhereBetween($column, $value1, $value2);
    
    function orWhereBetween($column, $value1, $value2);
    
    function whereNotBetween($column, $value1, $value2);
    
    function andWhereNotBetween($column, $value1, $value2);
    
    function orWhereNotBetween($column, $value1, $value2);
    
    function whereLike($column, $value);
    
    function andWhereLike($column, $value);
    
    function orWhereLike($column, $value);
    
    function whereNotLike($column, $value);
    
    function andWhereNotLike($column, $value);
    
    function orWhereNotLike($column, $value);
    
    function whereIn($column, $value);
    
    function andWhereIn($column, $value);
    
    function orWhereIn($column, $value);
    
    function whereNotIn($column, $value);
    
    function andWhereNotIn($column, $value);
    
    function orWhereNotIn($column, $value);
    
    function whereNull($column);
    
    function andWhereNull($column);
    
    function orWhereNull($column);
    
    function whereNotNull($column);
    
    function andWhereNotNull($column);
    
    function orWhereNotNull($column);
    
    function whereExists(Closure $select);
    
    function andWhereExists(Closure $select);
    
    function orWhereExists(Closure $select);
    
    function whereNotExists(Closure $select);
    
    function andWhereNotExists(Closure $select);
    
    function orWhereNotExists(Closure $select);
}