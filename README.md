##Opis Database##

Opis Database is an advanced Database Abstraction Layer with support for MySQL, PostgreSQL, DB2, Firebird, Oracle

###Documentation###

[Fetching records](https://github.com/opis/database/blob/master/doc/fetching.md)
[Aggregate functions](https://github.com/opis/database/blob/master/doc/aggregate.md)
[Aggregate functions](https://github.com/opis/database/blob/master/doc/aggregate.md)
[WHERE clauses](https://github.com/opis/database/blob/master/doc/where.md)

###Examples###

Connecting to a database

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

####Selecting records####

```sql
SELECT * FROM `products` WHERE `category` = 'laptops' AND `quantity` > 10
```

```php
$result = $db->from('products')
             ->where('category', 'laptops')
             ->andWhere('quantity', 10, '>')
             ->select();
```