CHANGELOG
-----------
### Opis Database 2.0.1, 2014.11.26

* Fixed a bug in `Opis\Database\Schema\Complier` class.

### Opis Database 2.0.0, 2014.10.15

* This is a full API change
* Changed `where`, `andWhere` and `orWhere` methods of the `Opis\Database\SQL\WhereCondition` class. The methods accepts now a single
argument, representing a column or a closure used to group conditions.
* Added `is`, `eq`, `isNot`, `ne`, ` lessThan`, `lt`, `greaterThan`, `gt`, `atLeast`, `gte`, `atMost`, `lte`, `between`,
`notBetween`, `in`, `notIn`, `like`, `notLike`, `isNull` and `notNull` methods. This methods are used in conjunction with the
`where`, `andWhere` and `orWhere` methods.
* Removed `whereBetween`, `andWhereBetween`, `orWhereBetween`, `whereNotBetween`, `andWhereNotBetween` and `orWhereNotBetween` methods
from `Opis\Database\SQL\WhereCondition` class.
* Removed `whereIn`, `andWhereIn`, `orWhereIn`, `whereNotIn`, `andWhereNotIn` and `orWhereNotIn` methods
from `Opis\Database\SQL\WhereCondition` class.
* Removed `whereLike`, `andWhereLike`, `orWhereLike`, `whereNotLike`, `andWhereNotLike` and `orWhereNotLike` methods
from `Opis\Database\SQL\WhereCondition` class.
* Removed `whereNull`, `andWhereNull`, `orWhereNull`, `whereNotNull`, `andWhereNotNull` and `orWhereNotNull` methods 
from `Opis\Database\SQL\WhereCondition` class.
* Modified `join`, `leftJoin`, `rightJoin`, `fullJoin` methods of the `Opis\Database\SQL\WhereJoinCondition` class.
* Modified `having` method of the `Opis\Database\SQL\SelectStatement` class.
* Removed `execute` method from `Opis\Database\SQL\Update` class.
* Changed `set` method of the `Opis\Database\SQL\Update` class.
* Added `schema` method to the `Opis\Database\Database` class.
* The schema builder is now officially supported, although it is still marked as experimental.

### Opis Database 1.4.0, 2014.07.04

*  Modified `insert` command.

### Opis Database 1.3.2, 2014.07.03

* Added autoload file

### Opis Database 1.3.1, 2014.07.01

* Fixed a bug in `Opis\Database\SQL\Where` class.

### Opis Database 1.3.0, 2014.06.26

* Removed `port` method from `Opis\Database\DSN\SQLServer` class
* Added an extra optional argument to the `server` method of `Opis\Database\DSN\SQLServer` class
* Fixed a bug in `Opis\Database\DSN\SQLite` class
* Commented code

### Opis Database 1.2.2

* Started changelog
