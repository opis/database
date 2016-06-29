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

use PDO;
use PDOException;

class Transaction
{
    /** @var  Database Database object. */
    protected $database;

    /** @var    callable    Transaction callback. */
    protected $transaction;

    /** @var    callable    Success callback. */
    protected $successCallback;

    /** @var    callable    Error callback. */
    protected $errorCallback;

    /**
     * Transaction constructor.
     * @param Database $database
     * @param callable $transaction
     */
    public function __construct(Database $database, callable $transaction)
    {
        $this->database = $database;
        $this->transaction = $transaction;
    }

    /**
     * Add callback that will be called if transaction succeeded
     *
     * @param   callable    $callback   The callback
     *
     * @return Transaction
     */
    public function onSuccess(callable $callback): self
    {
        $this->successCallback = $callback;
        return $this;
    }

    /**
     * Add callback that will be called if transaction fails
     *
     * @param   callable    $callback   The callback
     *
     * @return  Transaction
     */
    public function onError(callable $callback): self
    {
        $this->errorCallback = $callback;
        return $this;
    }

    /**
     * Get the callback that needs to be called if transaction succeeded
     *
     * @return  callable
     */
    public function getOnSuccessCallback(): callable
    {
        return $this->successCallback;
    }

    /**
     * Get the callback that needs to be called if transaction fails
     *
     * @return  callable
     */
    public function getOnErrorCallback(): callable
    {
        return $this->errorCallback;
    }

    /**
     * Get the database for the current transaction
     *
     * @return  Database
     */
    public function database(): Database
    {
        return $this->database;
    }

    /**
     * Get the PDO object associated with the current transaction
     *
     * @return  PDO
     */
    public function pdo(): PDO
    {
        return $this->database->getConnection()->getPDO();
    }

    /**
     * Begin the transaction
     */
    public function begin()
    {
        $this->pdo()->beginTransaction();
    }

    /**
     * Commit all changes
     */
    public function commit()
    {
        $this->pdo()->commit();
    }

    /**
     * Roll back all changes
     */
    public function rollBack()
    {
        $this->pdo()->rollBack();
    }

    /**
     * Run the current transaction
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
     * @param   callable    $execute    (optional) Callback
     *
     * @return  mixed
     */
    public function execute(callable $execute = null)
    {
        if ($execute !== null) {
            return $execute($this, $this->transaction);
        }

        try {
            $this->begin();
            $result = $this->run();
            $this->commit();
            $successCallback = $this->successCallback;
            
            if ($successCallback !== null) {
                $successCallback($this);
            }

            return $result;
        } catch (PDOException $e) {
            $this->rollBack();
            $errorCallback = $this->errorCallback;
            
            if ($errorCallback !== null) {
                $errorCallback($e, $this);
            }
        }
    }
}
