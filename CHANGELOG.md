CHANGELOG
---------
### v3.6.8, 2016.08.14

* Added `json` type cast
* Added an optional argument to cast method

### v3.6.7, 2016.06.14

* Bugfix

### v3.6.6, 2016.06.07

* Fixed a bug related to PHP 7

### v3.6.5, 2016.05.29

* Bugfix

### v3.6.4, 2016.05.20

* Added `object` casting type

### v3.6.3, 2016.05.19

* Rollback. `Opis\Database\Connection::count` is no longer deprecated and  is used again.

### v3.6.2, 2016.05.19

* Method `Opis\Database\Connection::count` is now deprecated
and `Opis\Database\Connection::command` method is used instead.

### v3.6.1, 2016.05.19

* Fixed a bug in `Opis\Database\Model::__set` method

### v3.6.0, 2016.03.21

* Fixed a bug in `Opis\Database\Model::__set` method
* Added `throwExceptions` flag on `Opis\Database\Model` class
* Changed exception message in `Opis\Database\Model::__get`

### v3.5.1, 2016.03.08

* Fixed a bug in `Opis\Database\ORM\BaseQuery` that prevented proper using of grouped conditions

### v3.5.0, 2016.03.02

* Added support for soft deletes
* `Opis\Database\Model::softDelete`, `Opis\Database\Model::supportsSoftDeletes`, `Opis\Database\ORM\Query::softDelete`,
`Opis\Database\ORM\Query::restore`, `Opis\Database\ORM\BaseQuery::withSoftDeleted`, 
`Opis\Database\ORM\BaseQuery::onlySoftDeleted` methods were added.
* `Opis\Database\Model::destroy` and `Opis\Database\Model::softDestroy` methods were added
* The following methods were added to `Opis\Database\ORM\Relation` class: `column`, `count`, `sum`, `avg`, `min`, `max`,
`update`, `restore`, `delete` and `softDelete`
* Now you can count, update, delete, soft delete and restore related models
* Added support for timestamps
* Added `timestamps` and `softDelete` methods to `Opis\Database\Schema\CreateTable`
* `Opis\Database\Model::getDateFormat` method is now public
* Added `update` and `updateAll` methods to `Opis\Database\Model` class
* The `Opis\Database\Model::assign` method now returns the current model instance

### v3.4.2, 2016.02.23

* Fixed a bug in `Opis\Database\Model::__set` method

### v3.4.1, 2016.02.22

* Added `isNewRecord` property to `Opis\Database\Model` class in order to fix a bug that might
occur when using custom values for primary key.
* The `__set`, `save` and `delete` methods were updated to use the newly added
`isNewRecord` property.

### v3.4.0, 2016.02.22

* Added `Opis\Database\Schema\BaseColumn::length` method
* Updated `Opis\Database\Schema\Compiler\MySQL::handleTypeDecimal` method
* Updated `Opis\Database\Schema\Compiler\PostgreSQL::handleTypeDecimal` method
* Added `Opis\Database\Schema\Compiler\SQLServer::handleTypeDecimal` method

### v3.3.3, 2016.02.13

* Fixed a bug in `Opis\Database\Model::__set` method

### v3.3.2, 2016.02.04

* Added missing use statement for `DateTime` class in `Opis\Database\Model`
* Added support for custom cast handling

### v3.3.1, 2016.02.04

* Fixed a bug in `Opis\Database\Model::__get` and `Opis\Database\Model::__set`

### v3.3.0, 2016.02.03

* Fixed a potential bug in `Opis\Database\Model::delete` method
* Added `link` and `unlink` methods to `Opis\Database\ORM\Relation\BelongsToMany` class

### v3.2.3, 2016.01.31

* Fixed a bug related to nullable column handling in `Opis\Colibri\Module::__get`
* Casting types can now be declared nullable by adding a `?` mark at the end of the type name

### v3.2.2, 2016.01.29

* Fixed a bug in `Opis\Database\Transaction` class. See [issue #22](https://github.com/opis/database/issues/22).

### v3.2.1, 2015.12.19

* Fixed a bug in `Opis\Database\Transaction::onError`
* Moved `Opis\Database\ModelInterface` into a separate file
* Fixed CS

### v3.2.0, 2015.12.09

* `Opis\Database\Model::getConnection` method was moved to `Opis\Database\ModelInterface::getConnection`
* `Model` class implements `ModelInterface` interface and you must provide an implementation
for the `Opis\Database\ModelInterface::getConnection` method
* Added `Opis\Database\Model::using` method
* Added an optional `Opis\Database\Connection` argument to the `Opis\Database\Model::create` method
* The constructor of the `Opis\Database\Model` class accepts an optional `Opis\Database\Connection` argument
* Removed unused artifacts `Opis\Database\SQL\WhereInterface` and `Opis\Database\SQL\SelectStatement::addHavingClause`.
See [issue #19](https://github.com/opis/database/issues/19) and [issue #18](https://github.com/opis/database/issues/18).
* Various bugfixes. See [issue #20](https://github.com/opis/database/issues/20) and [issue #21](https://github.com/opis/database/pull/21).
* Fixed CS

### v3.1.0, 2015.11.20

* Added `Opis\Database\ResultSet::column` method.
See [issue #10](https://github.com/opis/database/issues/10)
* The `Opis\Database\Model::getConnection` method is not abstract anymore.
See issue [issue #11](https://github.com/opis/database/issues/11)

### v3.0.1, 2015.11.09

* The second argument of the `Opis\Database\Connection::column` method is now optional.

### v3.0.0, 2015.10.29

* Removed deprecated `Opis\Database\Schema\AlterTable::addPrimary` method
* Removed deprecated `Opis\Database\Schema\AlterTable::addUnique` method
* Removed deprecated `Opis\Database\Schema\AlterTable::addIndex` method
* Removed deprecated `Opis\Database\Schema\AlterTable::addForeign` method
* Removed `Opis\Database\DSN` class and all other classes that were under the
`Opis\Database\DSN` namespace
* Added `Opis\Database\SQL\Compiler::getDateFormat` method
* Added support for ORM
* Addes support for joins in `UPDATE` statementes
* Moved classes that were under the `Opis\Database\Compiler` namespace to `Opis\Database\SQL\Compiler` namespace
* Added .gitattributes file
* Added `Opis\Database\Connection::setDateFormat` method. The method allows you to set the date format
used by the compiler.
* Added `Opis\Database\Connection::setWrapperFormat` method. The method allows you to set the identifier wrapper
used by the compiler.
* Added support for `NULL` values ordering

### v2.3.1, 2015.10.21

* Fixed a bug in `Opis\Database\SQL\Compiler::sqlFunctionROUND`
* Fixed some bugs in `Opis\Database\Compiler\MySQL` compiler class

### v2.3.0, 2015.10.19

* Fixed various bugs
* Improved schema compilers
* Added `primary`, `unique`, `index` and `foreign` methods to `Opis\Database\Schema\AlterTable` class
* The `addPrimary`, `addUnique`, `addIndex` and `addForeign` methods were deprecated
in `Opis\Database\Schema\AlterTable` class
* The third argument of the `Opis\Database\Schema\AlterTable::renameColumn` method was removed
* Added `Opis\Database\Connection::schema` method
* Changed `Opis\Database\Database::schema` method. The schema object is now returned from the connection object.
* Added `Opis\Database\Schema::getColumns` method
* Schema compilers now takes as an argument the current connection

### v2.2.0, 2015.10.19

* Added an optional parameter to `Opis\Database\Connection`'s constructor method. The parameter
can be used to specify a the driver used by the current connection
* Added `Opis\Database\Connection::driver` method
* Bugfixes and improvements
* Added `Opis\Database\Schema\Compiler\SQLite` class. This class provides schema support for SQLite
* Newly added `increment` and `decrement` methods can now be used when a row is updated

### v2.1.2, 2015.10.01

* Removed `branch-alias` from `composer.json` file
* Fixed a bug that prevented boolean values to be as default value for a table's column.
* Fixed a bug where `text` and `binary` types were not mapped correctly.

### v2.1.1, 2015.02.02

* Fixed a bug (see https://github.com/opis/database/pull/4)

### v2.1.0, 2014.12.12

* Modified `persistent` method in `Opis\Database\Connection`. The method accepts now an optional
boolean argument that specify if the connection should pe persistent or not.
* Added `disconnect` method in `Opis\Database\Connection`
* Added `renameTable` method in `Opis\Database\Schema\Compiler`
* Added `renameTable` method in `Opis\Database\Schema`
* The `Opis\Database\Schema\BaseTable`'s `nullable` method was deprecated.
* Fixed several bugs in `Opis\Database\Schema\Compiler`
* Modified the `pdo` method in `Opis\Database\Transaction`. The `PDO` object is no longer stored as a property,
 in order to avoid keeping the connection alive after the `disconnect` method was called.

### v2.0.1, 2014.11.26

* Fixed a bug in `Opis\Database\Schema\Complier` class.

### v2.0.0, 2014.10.15

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

### v1.4.0, 2014.07.04

*  Modified `insert` command.

### v1.3.2, 2014.07.03

* Added autoload file

### v1.3.1, 2014.07.01

* Fixed a bug in `Opis\Database\SQL\Where` class.

### v1.3.0, 2014.06.26

* Removed `port` method from `Opis\Database\DSN\SQLServer` class
* Added an extra optional argument to the `server` method of `Opis\Database\DSN\SQLServer` class
* Fixed a bug in `Opis\Database\DSN\SQLite` class
* Commented code

### v1.2.2

* Started changelog
