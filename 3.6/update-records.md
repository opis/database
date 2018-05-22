---
layout: project
version: 3.x
title: Update records
description: Learn how to update existing records
canonical: /database/4.x/update-records.html
---
# Update records

Updating existing records is done by using the `update` method. 
This method takes a single argument representing the table name which needs to be updated
and returns an integer value, representing the number of updated records.

Setting new values is done by passing as an argument to the `set` method a `key => value`
mapped array, where the `key` represents the column that will be updated, and `value`
represents the value that will be stored into the specified column.

```php
// Update the description of all users

$result = $db->update('users')
             ->set(array(
                'description' => 'Some description'
             ));
```
```sql
UPDATE `users` SET `description` = "Some description"
```

Updating a specific set of records is done by adding filtering conditions.

```php
// Update the email and the description of a specific user

$result = $db->update('users')
             ->where('id')->is(2014)
             ->set(array(
                'email' => 'email@example.com',
                'description' => 'Some description',
             ));
```
```sql
UPDATE `users` SET 
    `email` = "email@example.com", 
    `description` = "Some description" 
WHERE `id` = 2014
```

You can also use expressions when you update a table by setting a closure as the value for the column.

```php
// Increments the number of friends

$result = $db->update('users')
             ->where('id')->is(2014)
             ->set(array(
                'friends_no' => function($expr){
                    $expr->column('friends_no')->{'+'}->value(1);
                }
             ));
```
```sql
UPDATE `users` SET `friends_no` = `friends_no` + 1 WHERE `id` = 2014
```

Incrementing a column's value can be achieved by using the `increment` method.

```php
// Increments the number of friends

$result = $db->update('users')
             ->where('id')->is(2014)
             ->increment('friends_no');
```
```sql
UPDATE `users` SET `friends_no` = `friends_no` + 1 WHERE `id` = 2014
```

Decrementing a column's value can be achieved by using the `decrement` method.

```php
// Decrements the number of friends

$result = $db->update('users')
             ->where('id')->is(2014)
             ->decrement('friends_no');
```
```sql
UPDATE `users` SET `friends_no` = `friends_no` - 1 WHERE `id` = 2014
```

You can increment or decrement the value of a column by specifying an explicit quantity.

```php
// Increments the number of friends by 2

$result = $db->update('users')
             ->where('id')->is(2014)
             ->increment('friends_no', 2);
```
```sql
UPDATE `users` SET `friends_no` = `friends_no` + 2 WHERE `id` = 2014
```

You can also increment or decrement multiple columns at the same time and you can use different
incrementing/decrementing values for each column.

```php
/* Increments the number of friends and unread messages by 1
   and the user's age by 5 */ 

$result = $db->update('users')
             ->where('id')->is(2014)
             ->increment(array('friends_no', 'unread_messages', 'age' => 5));
```
```sql
UPDATE `users` SET
    `friends_no` = `friends_no` + 1,
    `unread_messages` = `unread_messages` + 1,
    `age` = `age` + 5
WHERE `id` = 2014
```
