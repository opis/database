---
layout: project
version: 3.x
title: Limits and offsets
description: Learn about limits and offsets
canonical: /database/4.x/limits-and-offsets.html
---

Limiting the number of results returned by a query, is achieved by using the `limit` method.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->limit(25)
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name` ASC LIMIT 25
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can skip a certain number of records by using the `offset` method. 
The skipped records will not be added to the results set. 
This method can be used only in conjunction with the `limit` method.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->limit(25)
             ->offset(10)
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name` ASC LIMIT 25 OFFSET 10
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}
