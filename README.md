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

###Fetching data###

Select all columns from a table

```sql
SELECT * FROM `customers`
```

```php
$result = $db->from('customers')
             ->select();
```

Select a single column from a table

```sql
SELECT `id` FROM `customers`
```

```php
$result = $db->from('customers')
             ->select('id');
```

Select multiple columns from a table

```sql
SELECT `name`, `email` FROM `customers`
```

```php
$result = $db->from('customers')
             ->select(array('name', 'email'));
```

Processing the result set

```php
foreach($result as $row)
{
    print  $row->name . ' => ' . $row->email;
}
```

To fetch as single row from the result-set use the **->first()** method

```sql
SELECT `name`, `email` FROM `customers`
```

```php
$result = $db->from('customers')
             ->first(array('name', 'email'));
             
print $result->name . ' => ' . $result->email;
```

To fetch a single column use the **->column()** method. 

```sql
SELECT `email` FROM `customers`
```

```php
$email = $db->from('customers')
             ->column('email');
             
print $email;
```

Aliasing column names

```sql
SELECT `phone_number` AS `phone`, `primary_email` AS `email` FROM `customers`
```

```php
$result = $db->from('customers')
             ->select(array('phone_number' => 'phone', 'primary_email' => 'email'));

//Processing the result set

foreach($result as $row)
{
    print $row->phone . ', ' . $row->email;
}
```

Selecting from multiple tables

```sql
SELECT * FROM `customers`, `products`
```

```php
$result = $db->from(array('customers', 'products'))
             ->select();
```

Aliasing table names

```sql
SELECT * FROM `customers` AS `c`, `products` AS `p`
```

```php
$result = $db->from(array('customers' => 'c', 'products' => 'p'))
             ->select();
```

Selecting distinct values from a table

```sql
SELECT DISTINCT * FROM `customers`
```

```php
$result = $db->from('customers')
             ->distinct()
             ->select();
```

###Aggregate functions###

**Counting**

```sql
SELECT COUNT(*) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count();
```

```sql
SELECT COUNT(`facebook_id`) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count('facebook_id');
```

```sql
SELECT COUNT(DISTINCT `city`) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count('city', true);
```

```sql
SELECT COUNT(DISTINCT `city`, `street`) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count(array('city', 'street'));
```

**Average**

```sql
SELECT AVG(`price`) FROM `products`
```

```php
$result = $db->from('products')
             ->avg('price');
```

```sql
SELECT AVG(DISTINCT `price`) FROM `products`
```

```php
$result = $db->from('products')
             ->avg('price', true);
```

**Largest value**

```sql
SELECT MAX(`price`) FROM `products`
```

```php
$result = $db->from('products')
             ->max('price');
```

```sql
SELECT MAX(DISTINCT `price`) FROM `products`
```

```php
$result = $db->from('products')
             ->max('price', true);
```

**Smallest value**

```sql
SELECT MIN(`price`) FROM `products`
```

```php
$result = $db->from('products')
             ->min('price');
```

```sql
SELECT MIN(DISTINCT `price`) FROM `products`
```

```php
$result = $db->from('products')
             ->min('price', true);
```

**Total**

```sql
SELECT SUM(`quantity`) FROM `products`
```

```php
$result = $db->from('products')
             ->sum('quantity');
```

```sql
SELECT SUM(DISTINCT `quantity`) FROM `products`
```

```php
$result = $db->from('products')
             ->sum('quantity', true);
```

###WHERE Clauses###

Using **->where()**, **->andWhere()**, **orWhere()** methods

```sql
SELECT * FROM `products` WHERE `quantity` = 10
```

```php
$result = $db->from('products')
             ->where('quantity', 10)
             ->select();
```

```sql
SELECT * FROM `products` WHERE `quantity` > 10
```

```php
$result = $db->from('products')
             ->where('quantity', 10, '>')
             ->select();
```

```sql
SELECT * FROM `products` WHERE `quantity` > 10 AND `quantity` < 20
```

```php
$result = $db->from('products')
             ->where('quantity', 10, '>')
             ->where('quantity', 20, '<')
             ->select();

//Alternative syntax

$result = $db->from('products')
             ->where('quantity', 10, '>')
             ->andWhere('quantity', 20, '<')
             ->select();
```

There is no difference between **->where()** and **->andWhere()** methods. The main purpose of the **->andWhere()** method is to improve code readability.

```sql
SELECT * FROM `products` WHERE `brand` = 'Toshiba' OR `brand` = 'Sony'
```

```php
$result = $db->from('products')
             ->where('brand', 'Toshiba')
             ->orWhere('brand', 'Sony')
             ->select();
```

Grouping conditions

```sql
SELECT * FROM `products` WHERE `category` = 'laptops' AND (`brand` = 'Toshiba' OR `brand` = 'Sony')
```

```php
$result = $db->from('products')
             ->where('category', 'laptops')
             ->andWhere(function($group){
                $group->where('brand', 'Toshiba')
                      ->orWhere('brand', 'Sony');
             })
             ->select();
```

Using **->whereNull()**, **->andWhereNull()**, **->orNull()**.