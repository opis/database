---
layout: project
version: 3.x
title: Delete records
description: Learn how to delete existing records
canonical: /database/4.x/delete-records.html
---
# Delete records

Deleting records is done by using the `from` and `delete` methods.

```php
// Delete all users

$result = $db->from('users')
             ->delete();
```
```sql
DELETE FROM `users`
```

Deleting a specific set of records is done by adding filters.

```php
// Delete all users which don't have a description

$result = $db->from('users')
             ->where('description')->isNull()
             ->delete();
```
```sql
DELETE FROM `users` WHERE `description` IS NULL
```

You can also delete from multiple tables simultaneously by performing a join 
and passing to the `delete` method a list of tables as an array argument.

```php
// Delete a specific user and all its orders

$result = $db->from('users')
             ->where('users.id')->is(2014)
             ->join('orders', function($join){
                $join->on('users.id', 'orders.user_id');
             })
             ->delete(array('users', 'orders'));
```
```sql
DELETE `users`, `orders` FROM `users`
    INNER JOIN `orders` ON `users`.`id` = `orders`.`user_id`
WHERE `users`.`id` = 2014
```
