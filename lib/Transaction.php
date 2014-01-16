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

namespace Opis\Database;

use Closure;
use PDOException;

class Transaction
{
    
    protected $database;
    
    protected $transaction;
    
    protected $successCallback;
    
    protected $errorCallback;
    
    public function __construct(Database $database, Closure $transaction)
    {
        $this->database = $database;
        $this->transaction = $transaction;
    }
    
    public function success(Closure $callback)
    {
        $this->successCallback = $callback;
        return $this;
    }
    
    public function error(Closure $callback)
    {
        $this->errorCallback = $error;
        return $this;
    }
    
    public function execute()
    {
        try
        {
            $pdo = $this->database->getConnection()->pdo();
            $pdo->beginTransaction();
            $result = $this->transaction($this->database);
            $pdo->commit();
            
            if($this->successCallback !== null)
            {
                $this->successCallback($this->database);
            }
            
            return $result;
        }
        catch(PDOException $e)
        {
            $pdo->rollBack();
            
            if($this->errorCallback !== null)
            {
                $this->errorCallback($e, $this->database);
            }
        }
    }
    
}