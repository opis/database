##Opis Database##

```php
use \PDO;
use \Opis\Database\Database;
use \Opis\Database\Connection;

Connection::mysql('test', true)
  ->database('db1')
  ->username('user')
  ->password('pass')
  ->charset('utf8');

$db = Database::connection('test');

$result =  $db->table('t1')
              ->where('user', '=', 'someuser')
              ->all();
```
