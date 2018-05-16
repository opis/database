---
layout: project
version: 3.6
title: Ordering criteria
description: Learn how to order results
canonical: /database/4.x/ordering-criteria
---
# Ordering criteria

Adding an ordering criterion is done by using the `orderBy` method.

```php
$result = $db->from('users')
             ->orderBy('name')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` ORDER BY `name` ASC
```

Changing an ordering criterion is done by passing `desc` as the second argument to the `orderBy` method.

```php
$result = $db->from('users')
             ->orderBy('name', 'desc')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` ORDER BY `name` DESC
```

You can provide multiple columns as an ordering criterion, by passing to the `orderBy` method 
an array containing all column names.

```php
$result = $db->from('users')
             ->orderBy(['name', 'age'])
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` ORDER BY `name`, `age` ASC
```

Adding multiple ordering criteria to the same query is done by calling the `orderBy` 
method as many times as you need.

```php
$result = $db->from('users')
             ->orderBy('name')
             ->orderBy('age', 'desc')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` ORDER BY `name` ASC, `age` DESC
```

#### Ordering NULL values

You can specify how `NULL` values should be ordered by passing `nulls first` or 
`nulls last` as the third argument to the `orderBy` method.

```php
$result = $db->from('users')
             ->orderBy('name')
             ->orderBy('age', 'desc', 'nulls first')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` ORDER BY `name` ASC, `age` DESC NULLS FIRST
```


