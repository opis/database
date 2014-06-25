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
    
    protected $pdo;
    
    public function __construct(Database $database, Closure $transaction)
    {
        $this->database = $database;
        $this->transaction = $transaction;
    }
    
    public function onSuccess(Closure $callback)
    {
        $this->successCallback = $callback;
        return $this;
    }
    
    public function onError(Closure $callback)
    {
        $this->errorCallback = $error;
        return $this;
    }
    
    public function getOnSuccessCallback()
    {
        return $this->successCallback;
    }
    
    public function getOnErrorCallback()
    {
        return $this->errorCallback;
    }
    
    public function database()
    {
        return $this->database;
    }
    
    public function pdo()
    {
        if($this->pdo === null)
        {
            $this->pdo = $this->database->getConnection()->pdo();
        }
        return $this->pdo;
    }
    
    public function begin()
    {
        $this->pdo()->beginTransaction();
    }
    
    public function commit()
    {
        $this->pdo()->commit();
    }
    
    public function rollBack()
    {
        $this->pdo()->rollBack();
    }
    
    public function execute(Closure $execute = null)
    {
        if($execute !== null)
        {
            return $execute($this, $this->transaction);
        }
        
        try
        {
            $this->begin();
            $result = $this->transaction($this->database);
            $pdo->commit();
            
            if($this->successCallback !== null)
            {
                $this->successCallback($this);
            }
            
            return $result;
        }
        catch(PDOException $e)
        {
            $this->rollBack();
            
            if($this->errorCallback !== null)
            {
                $this->errorCallback($e, $this);
            }
        }
    }
    
}
