---
layout: project
version: 4.x
title: Expressions
description: Learn about expressions
---

Expressions can be used to build complex queries. Using an expression to add a complex 
filter condition is simply a matter of passing a closure to the condition method. 
You can use then the object passed to the closure as an argument, to build your custom expressions. 
The methods used to build expressions are `column`, `op` and `value`.


{% capture tabs %}
{% capture php %}
```php
$result = $db->from('numbers')
             ->where('c')->eq(function($expr){
                $expr->column('b')->op('+')->value(10);
             })
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql%}
```sql
SELECT * FROM `numbers` WHERE `c` = `b` + 10
```
{% endcapture %}
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}

The `column` method is used to add to the expression a value that must be treated 
as a column name. The `value` method is used to add an arbitrary value that must 
treated as user input and handled properly. The `op` method is used to add a raw 
value to the expression.

The `op` method can be replaced by curly brackets.

{% capture tabs %}
{% capture php %}
```php
$result = $db->from('numbers')
             ->where('c')->eq(function($expr){
                $expr->column('a')->{'+'}->column('b');
             })
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql%}
```sql
SELECT * FROM `numbers` WHERE `c` = `a` + `b`
```
{% endcapture %}
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}
