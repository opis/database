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

namespace Opis\Database;

use Closure;
use PDOException;

class Transaction
{
    /** @var    \Opis\Database\Database Database object. */
    protected $database;
    
    /** @var    \Closure    Transaction callback. */
    protected $transaction;
    
    /** @var    \Closure    Success callback. */
    protected $successCallback;
    
    /** @var    \Closure    Error callback. */
    protected $errorCallback;
    
    /**
     * Constructor
     *
     * @access  public
     * 
     * @param   \Opis\Database\Database $database       Current database
     * @param   \Closure                $transaction    Transaction callback
     */
    
    public function __construct(Database $database, Closure $transaction)
    {
        $this->database = $database;
        $this->transaction = $transaction;
    }
    
    /**
     * Add callback that will be called if transaction succeeded
     *
     * @access  public
     *
     * @param   \Closure    $callback   The callback
     *
     * @return  \Opis\Database\Transaction  Self reference
     */
    
    public function onSuccess(Closure $callback)
    {
        $this->successCallback = $callback;
        return $this;
    }
    
    /**
     * Add callback that will be called if transaction fails
     *
     * @access  public
     *
     * @param   \Closure    $callback   The callback
     *
     * @return  \Opis\Database\Transaction  Self reference
     */
    
    public function onError(Closure $callback)
    {
        $this->errorCallback = $error;
        return $this;
    }
    
    /**
     * Get the callback that needs to be called if transaction succeeded
     *
     * @access  public
     *
     * @return  \Closure
     */
    
    public function getOnSuccessCallback()
    {
        return $this->successCallback;
    }
    
    /**
     * Get the callback that needs to be called if transaction fails
     *
     * @access  public
     *
     * @return  \Closure
     */
    
    public function getOnErrorCallback()
    {
        return $this->errorCallback;
    }
    
    /**
     * Get the database for the current transaction
     *
     * @access  public
     *
     * @return  \Opis\Database\Database
     */
    
    public function database()
    {
        return $this->database;
    }
    
    /**
     * Get the PDO object associated with the current transaction
     *
     * @access  public
     *
     * @return  \PDO
     */
    
    public function pdo()
    {
        return $this->database->getConnection()->pdo();
    }
    
    /**
     * Begin the transaction
     *
     * @access  public
     */
    
    public function begin()
    {
        $this->pdo()->beginTransaction();
    }
    
    /**
     * Commit all changes
     *
     * @access  public
     */
    
    public function commit()
    {
        $this->pdo()->commit();
    }
    
    /**
     * Roll back all changes
     *
     * @access  public
     */
    
    public function rollBack()
    {
        $this->pdo()->rollBack();
    }
    
    
    /**
     * Run the current transaction
     *
     * @access  public
     *
     * @return  mixed
     */
    
    public function run()
    {
        $transaction = $this->transaction;
        return $transaction($this->database);
    }
    
    /**
     * Execute the current transaction
     *
     * @param   \Closure    $execute    (optional) Callback
     *
     * @return  mixed
     */
    
    public function execute(Closure $execute = null)
    {
        if($execute !== null)
        {
            return $execute($this, $this->transaction);
        }
        
        try
        {
            $this->begin();
            $result = $this->run();
            $this->commit();
            
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
