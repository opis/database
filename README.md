##Opis Database##

```php
use \PDO;
use \Opis\Database\Database;
use \Opis\Database\Connection;

Connection::mysql('test', true)
  ->username('user')
  ->password('pass')
  ->set('dbname', 'test')
  ->set('host','localhost')
  ->set('port','3306')
  ->option(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
  
$db = Database::connection('test');

$result = $db->table('t1')
  ->where('user', '=', 'someuser')
  ->all();
```
