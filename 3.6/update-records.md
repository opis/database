---
layout: project
version: 3.x
title: Update records
description: Learn how to update existing records
canonical: /database/4.x/update-records.html
---

Updating existing records is done by using the `update` method. 
This method takes a single argument representing the table name which needs to be updated
and returns an integer value, representing the number of updated records.

Setting new values is done by passing as an argument to the `set` method a `key => value`
mapped array, where the `key` represents the column that will be updated, and `value`
represents the value that will be stored into the specified column.

{% capture php %}
```php
// Update the description of all users

$result = $db->update('users')
             ->set(array(
                'description' => 'Some description'
             ));
```
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET `description` = "Some description"
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Updating a specific set of records is done by adding filtering conditions.

{% capture php %}
```php
// Update the email and the description of a specific user

$result = $db->update('users')
             ->where('id')->is(2014)
             ->set(array(
                'email' => 'email@example.com',
                'description' => 'Some description',
             ));
```
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET 
    `email` = "email@example.com", 
    `description` = "Some description" 
WHERE `id` = 2014
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also use expressions when you update a table by setting a closure as the value for the column.

{% capture php %}
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
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET `friends_no` = `friends_no` + 1 WHERE `id` = 2014
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Incrementing a column's value can be achieved by using the `increment` method.

{% capture php %}
```php
// Increments the number of friends

$result = $db->update('users')
             ->where('id')->is(2014)
             ->increment('friends_no');
```
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET `friends_no` = `friends_no` + 1 WHERE `id` = 2014
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Decrementing a column's value can be achieved by using the `decrement` method.

{% capture php %}
```php
// Decrements the number of friends

$result = $db->update('users')
             ->where('id')->is(2014)
             ->decrement('friends_no');
```
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET `friends_no` = `friends_no` - 1 WHERE `id` = 2014
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can increment or decrement the value of a column by specifying an explicit quantity.

{% capture php %}
```php
// Increments the number of friends by 2

$result = $db->update('users')
             ->where('id')->is(2014)
             ->increment('friends_no', 2);
```
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET `friends_no` = `friends_no` + 2 WHERE `id` = 2014
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also increment or decrement multiple columns at the same time and you can use different
incrementing/decrementing values for each column.

{% capture php %}
```php
/* Increments the number of friends and unread messages by 1
   and the user's age by 5 */ 

$result = $db->update('users')
             ->where('id')->is(2014)
             ->increment(array('friends_no', 'unread_messages', 'age' => 5));
```
{% endcapture %}
{% capture sql %}
```sql
UPDATE `users` SET
    `friends_no` = `friends_no` + 1,
    `unread_messages` = `unread_messages` + 1,
    `age` = `age` + 5
WHERE `id` = 2014
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}
