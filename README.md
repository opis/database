##Opis Database##

Connecting to database

```php
use \Opis\Database\Database;
use \Opis\Database\Connection;

Connection::mysql('test', true)
  ->database('db1')
  ->username('user')
  ->password('pass')
  ->charset('utf8');

$db = Database::connection('test');
```

###Select###

Select all

```sql
SELECT * FROM `table1`
```

```php
$result = $db->from('table1')
             ->select();
             
//Get only the first result

$result = $db->from('table1')
             ->first();
```

Select with columns

```sql
SELECT `column1`, `column2` FROM `table1`
```

```php
$result = $db->from('table1')
             ->select(array('column1', 'column2'));

//Get only the first result

$result = $db->from('table1')
             ->first(array('column1', 'column2'));
```

Aliasing column names

```sql
SELECT `column1` AS `c1`, `column2` AS `c2` FROM `table1`
```

```php
$result = $db->from('table1')
             ->select(array('column1' => 'c1', 'column2' => 'c2'));

//Get only the first result

$result = $db->from('table1')
             ->first(array('column1' => 'c1', 'column2' => 'c2'));
```

Selecting from multiple tables

```sql
SELECT * FROM `table1`, `table2`
```

```php
$result = $db->from(array('table1', 'table2'))
             ->select();
             
//Get only the first result

$result = $db->from(array('table1', 'table2'))
             ->first();
```

Aliasing table names

```sql
SELECT * FROM `table1` AS `t1`, `table2` AS `t2`
```

```php
$result = $db->from(array('table1' => 't1', 'table2' => 't2'))
             ->select();
             
//Get only the first result

$result = $db->from(array('table1' => 't1', 'table2' => 't2'))
             ->first();
```

WHERE conditions

```sql
SELECT * FROM `table1` WHERE `column1` = 1
```

```php
$result = $db->from('table1')
             ->where('column1', 1)
             ->select();
             
//Get only the first result

$result = $db->from('table1')
             ->where('column1', 1)
             ->first();
```

```sql
SELECT * FROM `table1` WHERE `column1` = 1 AND `column2` > 2
```

```php
$result = $db->from('table1')
             ->where('column1', 1)
             ->where('column2, 2, '>')
             ->select();
             
//Get only the first result

$result = $db->from('table1')
             ->where('column1', 1)
             ->where('column2, 2, '>')
             ->first();
```

```sql
SELECT * FROM `table1` WHERE `column1` = 1 OR `column2` > 2
```

```php
$result = $db->from('table1')
             ->where('column1', 1)
             ->orWhere('column2, 2, '>')
             ->select();
             
//Get only the first result

$result = $db->from('table1')
             ->where('column1', 1)
             ->orWhere('column2, 2, '>')
             ->first();
```

Grouping WHERE conditions

```sql
SELECT * FROM `table1` WHERE `column1` = 1 OR (`column2` = 2 AND `column3` < 3)
```

```php
$result = $db->from('table1')
             ->where('column1', 1)
             ->orWhere(function($condition){
                $condition->where('column2', 2)
                          ->where('column3', 3, '<');
             })
             ->select();

//Get only the first result

$result = $db->from('table1')
             ->where('column1', 1)
             ->orWhere(function($condition){
                $condition->where('column2', 2)
                          ->where('column3', 3, '<');
             })
             ->first();
```

WHERE IN conditions

```sql
SELECT * FROM `table1` WHERE `column1` IN (1, 2, 3)
```

```php
$result = $db->from('table1')
             ->in('column1', array(1, 2, 3))
             ->select();

//Get only the first result

$result = $db->from('table1')
             ->in('column1', array(1, 2, 3))
             ->first();
```

```sql
SELECT * FROM `table1` WHERE `column1` IN (1, 2, 3) OR `column2` IN (4, 5, 6)
```

```php
$result = $db->from('table1')
             ->in('column1', array(1, 2, 3))
             ->orIn('column2', array(4, 5, 6))
             ->select();

//Get only the first result

$result = $db->from('table1')
             ->in('column1', array(1, 2, 3))
             ->orIn('column2', array(4, 5, 6))
             ->first();
```

WHERE IN Subquery

```sql
SELECT * FROM `table1` WHERE `column1` IN (SELECT `id` FROM `table2`)
```

```php
$result = $db->from('table1')
             ->in('column1', function($subquery){
                $subquery->from('table2')
                         ->select('id');
             })
             ->select();

//Get only the first result

$result = $db->from('table1')
             ->in('column1', function($subquery){
                $subquery->from('table2')
                         ->select('id');
             })
             ->first();
```
