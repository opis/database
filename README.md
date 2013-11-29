##Opis Database##

```php
use \Opis\Database\Database;
use \Opis\Database\Connection;

Connection::mysql('test', true)
  ->database('db1')
  ->username('user')
  ->password('pass')
  ->charset('utf8');

$db = Database::connection('test');

$result =  $db->select('table')
              ->columns(array('user', 'age'))
              ->where('age', 18)
              ->orWhere('age', 32, '>')
              ->execute();
```
