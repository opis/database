<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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
    
    function orWhere($column, $value = null, $operator = '=');
    
    function between($column, $value1, $value2);
    
    function orBetween($column, $value1, $value2);
    
    function notBetween($column, $value1, $value2);
    
    function orNotBetween($column, $value1, $value2);
    
    function in($column, $value);
    
    function orIn($column, $value);
    
    function notIn($column, $value);
    
    function orNotIn($column, $value);
    
    function isNull($column);
    
    function orNull($column);
    
    function notNull($column);
    
    function orNotNull($column);
    
    function exists(Closure $select);
    
    function orExists(Closure $select);
    
    function notExists(Closure $select);
    
    function orNotExists(Closure $select);
}