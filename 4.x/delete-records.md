---
layout: project
version: 4.x
title: Delete records
description: Learn how to delete existing records
---

Deleting records is done by using the `from` and the `delete` methods.

{% capture php %}
```php
// Delete all users

$result = $db->from('users')
             ->delete();
```
{% endcapture %}
{% capture sql %}
```sql
DELETE FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}

Deleting a specific set of records is done by adding filters.

{% capture php %}
```php
// Delete all users which don't have a description

$result = $db->from('users')
             ->where('description')->isNull()
             ->delete();
```
{% endcapture %}
{% capture sql %}
```sql
DELETE FROM `users` WHERE `description` IS NULL
```
{% endcapture %}
{% include_relative _tabs.html %}

You can also delete from multiple tables simultaneously by performing a join 
and passing to the `delete` method a list of tables as an array argument.

{% capture php %}
```php
// Delete a specific user and all its orders

$result = $db->from('users')
             ->where('users.id')->is(2014)
             ->join('orders', function($join){
                $join->on('users.id', 'orders.user_id');
             })
             ->delete(array('users', 'orders'));
```
{% endcapture %}
{% capture sql %}
```sql
DELETE `users`, `orders` FROM `users`
    INNER JOIN `orders` ON `users`.`id` = `orders`.`user_id`
WHERE `users`.`id` = 2014
```
{% endcapture %}
{% include_relative _tabs.html %}
