---
layout: project
version: 3.x
title: Transactions
description: Learn how to use transactions
canonical: /database/4.x/transactions.html
---

## Performing transactions

Transactions are performed with the help of the `transaction` and `execute` methods. 
The result returned from a transaction will be the result returned from the anonymous callback function
that was passed as an argument to the `transaction` method.

```php
use Opis\Database\Database;
use Opis\Database\Connection;

$connection = new Connection('mysql:host=localhost;dbname=test', 'username', 'password');
$db = new Database($connection);

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
})
->execute();
```

## Receiving notifications

You can receive notifications about transaction errors by using the `onError` method. 
The method accepts as an argument an anonymous function which will be called if an error occurs. 
The arguments passed to the callback function are the current instance of the `Opis\Database\Transaction` 
class and the instance of the `PDOException` class that was thrown as an exception.

```php
use PDOException;
use Opis\Database\Transaction;

// ...

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
})
->onError(function(PDOException $exception, Transaction $transaction){
    // code here
})
->execute();
```

Receiving notifications about successful transaction is done by using the `onSuccess` method. 
The method accepts as an argument an anonymous callback function that will be called if the transaction succeeds.
The arguments passed to the callback function is the current instance of the `Opis\Database\Transaction` class.

```php
use PDOException;
use Opis\Database\Transaction;

// ...

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
})
->onSuccess(function(Transaction $transaction){
    // code here
})
->execute();
```

## Controlling transaction's flow 
{: #ctrling-transaction-s-flow }

You can control in slight details how a transaction will be executed by passing 
an anonymous function as an argument to the `execute` method. 
The anonymous function will receive as an argument the current instance of the 
`Opis\Database\Transaction` and the closure that was passed to the transaction method.

```php
use Closure;
use Opis\Database\Transaction;

// ...

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
})
->execute(function(Transaction $transaction, Closure $callback){
    // ...
});
```

The `Opis\Database\Transaction` class offers a series of method that can be used to control 
how a transaction will be performed.

Starting a transaction is done by using the `begin` method. 
Committing a transaction is done by using the `commit` method, and rolling back 
a transaction is done by using the `rollBack` method.

You can obtain the instance of `Opis\Database\Database` class used for the ongoing transaction 
by calling the `database` method. 
You also have access to the underlying `PDO` object by calling the `pdo` method.

```php
use Closure;
use PDOException;
use Opis\Database\Transaction;

// ...

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
})
->execute(function(Transaction $transaction, Closure $callback){
    
    try {
        // Begin the transaction
        $transaction->begin();
        // Execute the callback
        $result = $callback($transaction->database());
        // Commit the transaction
        $transaction->commit();
        // Return the result of the transaction
        return $result;
    } catch(PDOException $e) {
        // Rollback the transaction
        $transaction->rollBack();
    }
    
});
```

You can get the success and error callbacks by using the `getOnSuccessCallback` and `getOnErrorCallback` methods.

```php
use Closure;
use PDOException;
use Opis\Database\Transaction;

// ...

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
})
->execute(function(Transaction $transaction, Closure $callback){
    
    try {
        // Begin the transaction
        $transaction->begin();
        // Execute the callback
        $result = $callback($transaction->database());
        // Commit the transaction
        $transaction->commit();
        // Get the success callback
        if(null !== $success = $transaction->getOnSuccessCallback())
        {
            $success($transaction);
        }
        // Return the result of the transaction
        return $result;
    } catch(PDOException $e) {
        // Rollback the transaction
        $transaction->rollBack();
        // Get the error callback
        if (null !== $error = $transaction->getOnErrorCallback()) {
            $error($e, $transaction);
        }
    }
    
});
```
