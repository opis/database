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
    /** @var  Connection Database connection */
    protected $connection;

    /** @var    callable    Transaction callback. */
    protected $transaction;

    /** @var    callable    Success callback. */
    protected $successCallback;

    /** @var    callable    Error callback. */
    protected $errorCallback;

    /** @var  mixed */
    protected $dataObject;

    /**
     * Transaction constructor.
     * @param Connection $connection
     * @param callable $transaction
     * @param mixed $dataObject
     */
    public function __construct(Connection $connection, callable $transaction, $dataObject = null)
    {
        $this->connection = $connection;
        $this->transaction = $transaction;
        $this->dataObject = $dataObject;
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
     * @return mixed|null
     */
    public function getDataObject()
    {
        return $this->dataObject;
    }

    /**
     * Get the PDO object associated with the current transaction
     *
     * @return  PDO
     */
    public function pdo(): PDO
    {
        return $this->connection->getPDO();
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
        return ($this->transaction)($this->dataObject);
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

            if($this->successCallback !== null){
                ($this->successCallback)($this);
            }

            return $result;
        } catch (PDOException $e) {
            $this->rollBack();

            if($this->errorCallback !== null){
                ($this->errorCallback)($e, $this);
            }
        }
    }
}
