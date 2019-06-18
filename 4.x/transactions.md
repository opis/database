---
layout: project
version: 4.x
title: Transactions
description: Learn how to perform transactions
---

## Performing transactions

Transactions are performed with the help of the `transaction` method.

```php
use Opis\Database\Database;
use Opis\Database\Connection;

$connection = new Connection(
    'mysql:host=localhost;dbname=test', 
    'username', 
    'password'
);

$db = new Database($connection);

$result = $db->transaction(function(Database $db){
    
    $db->insert(['user' => 'John Doe', 'email' => 'jd@foobar.com'])
       ->into('users');
       
    return $db->getConnection()->pdo()->lastInsertId();
});
```

You can pass a second argument to the `transaction` method, representing the value
that will be returned when a transaction fails. The default value that is returned
in case of a failure is `null`.

```php
$result = $db->transaction(function(Database $db){
    // simulate failure
    throw new \Exception();
}, false);

var_dump($result); // bool(false)
```

## Throwing exceptions

When doing a transaction, any exception that is thrown, gets caught and handled by the library
itself. But there are some cases when such a behavior is not desirable. 
Changing the default behavior, and allowing exceptions to be thrown, is done by calling 
the `throwTransactionExceptions` method on the connection object.

```php
$db->getConnection()->throwTransactionExceptions();

// No return value. Exception is thrown
$result = $db->transaction(function(Database $db){
    // simulate failure
    throw new \Exception();
}, false);
```

When you want to re-enable the default behavior, simply pass `false` as an argument
to the method.

```php
$db->getConnection()->throwTransactionExceptions(false);

// No exception is thrown, `false` is returned
$result = $db->transaction(function(Database $db){
    // simulate failure
    throw new \Exception();
}, false);
```
