---
layout: project
version: 3.x
title: Extended selections
description: Learn about how to define custom selections
canonical: /database/4.x/fields-selection.html
---

Let's assume that you want to select from a table, the minimum, the maximum and 
the average value of a column. Of course, to achieve this, you could simply use 
the [aggregates functions](aggregate-functions) but, unfortunately,
this means to make three separate queries to the database. 
To overcome this issue **Opis Database** provides developers a mechanism that can 
be used to define the columns or other properties that will be included in the final result set. 

In order to use this mechanism you must pass as an argument to the `select` method 
an anonymous callback function, which in turn will receive as an argument an object 
that must be used to specify which columns should be added to the result set. 

```php
$result = $db->from('users')
             ->select(function($include){
                //code here
             })
             ->all();
```

## Adding columns

Adding columns to the result set is done using the `column` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->column('name');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT `name` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

If you want to provide an alias name for the column, you must pass that alias 
as the second argument to the `column` method. 

{% capture php %}
```php


$result = $db->from('users')
             ->select(function($include){
                $include->column('name', 'n');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT `name` AS `n` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Of course, you can add multiple columns, by calling the `column` method multiple times, once for each column. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->column('name')
                        ->column('age');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT `name`, `age` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding multiple columns at once can be achieved by using the `columns` method 
and passing as an argument to the method an array of column names. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->columns(['name', 'age']);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT `name`, `age` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Aliasing column names is done by passing as an argument to the `columns` method 
a `key => value` mapped array, where the `key` represents the column name 
and the `value` represents the alias name. If you want that a column to not being aliased,
 all you have to do is to omit the `key` for that column, providing only the `value`.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->columns(['user' => 'u', 'age']);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT `user` AS `u`, `age` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

## Adding aggregates

Adding aggregates is done by using the `count`, `max`, `min`, `avg`, and `sum` methods. 
These methods can be used in conjunction with `column` and `columns` methods. 

#### Counting

Counting records is done using the count method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->count();
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT COUNT(*) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Counting all values(`NULL` values will not be counted) of a column is done by passing
the column's name as an argument to the `count` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->count('email');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT COUNT(`email`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `count` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->count('email', 'email_count');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT COUNT(`email`) AS `email_count` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

If you want to count only the distinct values of a column, you must pass `true`
 as the third argument to the `count` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->count('email', 'email_count', true);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT COUNT(DISTINCT `email`) AS `email_count` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Largest value

Finding the largest value of a column is done using the `max` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->max('age');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MAX(`age`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding an alias for the value returned by this aggregate function is done by passing 
the alias name as the second argument to the `max` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->max('age', 'max_age');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MAX(`age`) AS `max_age` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Smallest value

Finding the smallest value of a column is done using the `min` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->min('age');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MIN(`age`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `min` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->min('age', 'min_age');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MIN(`age`) AS `min_age` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Summing

Findind the sum of a column is done using the `sum` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT SUM(`wallet`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `sum` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet', 'total_amount');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT SUM(`wallet`) AS `total_amount` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

If you want to sum only the distinct values of a column, you must pass `true` as 
the third argument to the `sum` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet', 'total_amount', true);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT SUM(DISTINCT `wallet`) AS `total_amount` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Average

Finding the average value of a column is done using the `avg` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->avg('wallet');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT AVG(`wallet`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `avg` method. 

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet', 'average_amount');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT SUM(`wallet`) AS `average_amount` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

## Adding functions

Adding functions is done using one of the following methods: `ucase`, `lcase`, 
`mid`, `len`, `round`, `format` and `now`.

#### Upper case

Selecting the upper case value of a column is done by using the `ucase` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->ucase('name');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT UCASE(`name`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also alias the returned value by passing an alias name as the second argument of the method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->ucase('ucname');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT UCASE(`name`) AS `ucname` FROM `users` 
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Lower case

Selecting the lower case value of a column is done by using the `lcase` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->lcase('name');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT LCASE(`name`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also alias the returned value by passing an alias name as the second argument of the method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->lcase('lcname');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT LCASE(`name`) AS `lcname` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Substring

Extracting a substring from a column is done by using the `mid` method. 
The method takes as arguments the column's name and the starting position 
from which the substring will be extracted. The substring counting starts from `1`.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->mid('name', 3);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MID(`name`, 3) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also alias the returned value by passing an alias name as the third argument of the method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->mid('name', 3, 'sname');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MID(`name`, 3) AS `sname` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can limit the length of the substring by passing a fourth argument to the `mid` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->mid('name', 3, null, 2);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT MID(`name`, 3, 2) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Length

Adding the length of a column is done by using the `len` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->len('name');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT LENGTH(`name`) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also alias the returned value by passing an alias name as the second argument of the method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->len('lname');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT LENGTH(`name`) AS `lname` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Formatting

Formatting the value of a column to a number with a specified number of decimals
is done by using the `format` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->format('wallet', 4);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT FORMAT(`wallet`, 4) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

The same effect can be obtained by using the `round` method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->round('wallet', 4);
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT FORMAT(`wallet`, 4) FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can also alias the returned value by passing an alias name as the second argument of the method.

{% capture php %}
```php
$result = $db->from('users')
             ->select(function($include){
                $include->format('wallet', 4, 'fwallet');
             })
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT FORMAT(`wallet`, 4) AS `fwallet` FROM `users`
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

