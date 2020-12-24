<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database;

use PDO;
use PDOStatement;
use PDOException;
use RuntimeException;

class Connection
{
    protected ?string $username = null;
    protected ?string $password = null;
    protected bool $logQueries = false;
    protected array $log = [];
    protected array $commands = [];
    protected array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    protected ?PDO $pdo = null;
    protected ?SQL\Compiler $compiler = null;
    protected ?Schema\Compiler $schemaCompiler = null;
    protected ?string $dsn = null;
    protected ?string $driver = null;
    protected ?Database $database = null;
    protected ?Schema $schema = null;
    protected array $compilerOptions = [];
    protected array $schemaCompilerOptions = [];
    protected bool $throwTransactionExceptions = false;

    public function __construct(
        string $dsn = null,
        string $username = null,
        string $password = null,
        string $driver = null,
        PDO $pdo = null
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->driver = $driver;
        $this->pdo = $pdo;
    }

    /**
     * Create new connection using an instance of PDO
     *
     * @param PDO $pdo
     * @return Connection
     */
    public static function fromPDO(PDO $pdo): self
    {
        return new static(null, null, null, null, $pdo);
    }

    /**
     * Enable or disable query logging
     *
     * @param bool $value
     * @return $this
     */
    public function logQueries(bool $value = true): static
    {
        $this->logQueries = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function throwTransactionExceptions(bool $value = true): static
    {
        $this->throwTransactionExceptions = $value;
        return $this;
    }

    /**
     * Add an init command
     *
     * @param string $query
     * @param array $params
     * @return $this
     */
    public function initCommand(string $query, array $params = []): static
    {
        $this->commands[] = [
            'sql' => $query,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * Set the username
     *
     * @param string $username
     * @return $this
     */
    public function username(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Set the password
     *
     * @param string $password
     * @return $this
     */
    public function password(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set PDO connection options
     *
     * @param array $options
     * @return $this
     */
    public function options(array $options): static
    {
        foreach ($options as $name => $value) {
            $this->option($name, $value);
        }

        return $this;
    }

    /**
     * Set a PDO connection option
     *
     * @param int $name
     * @param mixed $value
     * @return $this
     */
    public function option(int $name, mixed $value): static
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * Use persistent connections
     *
     * @param bool $value
     * @return $this
     */
    public function persistent(bool $value = true): static
    {
        return $this->option(PDO::ATTR_PERSISTENT, $value);
    }

    /**
     * Set date format
     *
     * @param string $format
     * @return $this
     */
    public function setDateFormat(string $format): static
    {
        $this->compilerOptions['dateFormat'] = $format;
        return $this;
    }

    /**
     * Set identifier wrapper
     *
     * @param string $wrapper
     * @return $this
     */
    public function setWrapperFormat(string $wrapper): static
    {
        $this->compilerOptions['wrapper'] = $wrapper;
        $this->schemaCompilerOptions['wrapper'] = $wrapper;
        return $this;
    }

    /**
     * Returns the DSN associated with this connection
     *
     * @return string|null
     */
    public function getDSN(): ?string
    {
        return $this->dsn;
    }

    /**
     * Returns the driver's name
     *
     * @return  string
     */
    public function getDriver(): string
    {
        if ($this->driver === null) {
            $this->driver = $this->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
        }

        return $this->driver;
    }

    /**
     * Returns the schema associated with this connection
     *
     * @return  Schema
     */
    public function getSchema(): Schema
    {
        if ($this->schema === null) {
            $this->schema = new Schema($this);
        }

        return $this->schema;
    }

    /**
     * Returns the database object associated with this connection
     *
     * @return Database
     */
    public function getDatabase(): Database
    {
        if ($this->database === null) {
            $this->database = new Database($this);
        }

        return $this->database;
    }

    /**
     * Returns the PDO object associated with this connection
     *
     * @return PDO
     */
    public function getPDO(): PDO
    {
        if ($this->pdo == null) {
            $this->pdo = new PDO($this->getDSN(), $this->username, $this->password, $this->options);

            foreach ($this->commands as $command) {
                $this->command($command['sql'], $command['params']);
            }
        }

        return $this->pdo;
    }

    /**
     * Returns an instance of the compiler associated with this connection
     *
     * @return  SQL\Compiler
     */
    public function getCompiler(): SQL\Compiler
    {
        if ($this->compiler === null) {
            switch ($this->getDriver()) {
                case 'mysql':
                    $this->compiler = new SQL\Compiler\MySQL();
                    break;
                case 'dblib':
                case 'mssql':
                case 'sqlsrv':
                case 'sybase':
                    $this->compiler = new SQL\Compiler\SQLServer();
                    break;
                case 'oci':
                case 'oracle':
                    $this->compiler = new SQL\Compiler\Oracle();
                    break;
                case 'firebird':
                    $this->compiler = new SQL\Compiler\Firebird();
                    break;
                case 'db2':
                case 'ibm':
                case 'odbc':
                    $this->compiler = new SQL\Compiler\DB2();
                    break;
                case 'nuodb':
                    $this->compiler = new SQL\Compiler\NuoDB();
                    break;
                default:
                    $this->compiler = new SQL\Compiler();
            }

            $this->compiler->setOptions($this->compilerOptions);
        }

        return $this->compiler;
    }

    /**
     * Returns an instance of the schema compiler associated with this connection
     *
     * @return Schema\Compiler
     */
    public function schemaCompiler(): Schema\Compiler
    {
        if ($this->schemaCompiler === null) {
            switch ($this->getDriver()) {
                case 'mysql':
                    $this->schemaCompiler = new Schema\Compiler\MySQL($this);
                    break;
                case 'pgsql':
                    $this->schemaCompiler = new Schema\Compiler\PostgreSQL($this);
                    break;
                case 'dblib':
                case 'mssql':
                case 'sqlsrv':
                case 'sybase':
                    $this->schemaCompiler = new Schema\Compiler\SQLServer($this);
                    break;
                case 'sqlite':
                    $this->schemaCompiler = new Schema\Compiler\SQLite($this);
                    break;
                case 'oci':
                case 'oracle':
                    $this->schemaCompiler = new Schema\Compiler\Oracle($this);
                    break;
                default:
                    throw new RuntimeException('Schema not supported yet');
            }

            $this->schemaCompiler->setOptions($this->schemaCompilerOptions);
        }

        return $this->schemaCompiler;
    }

    /**
     * Close the current connection by destroying the associated PDO object
     */
    public function disconnect(): void
    {
        $this->pdo = null;
    }

    /**
     * Returns the query log for this database.
     *
     * @return  array
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * Execute a query
     *
     * @param string $sql
     * @param array $params
     * @return ResultSet|string
     */
    public function query(string $sql, array $params = []): ResultSet
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        return new ResultSet($prepared['statement']);
    }

    /**
     * Execute a non-query SQL command
     *
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function command(string $sql, array $params = []): bool
    {
        return $this->execute($this->prepare($sql, $params));
    }

    /**
     * Execute a query and return the number of affected rows
     *
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function count(string $sql, array $params = []): int
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        $result = $prepared['statement']->rowCount();
        $prepared['statement']->closeCursor();
        return $result;
    }

    /**
     * Execute a query and fetch the first column
     *
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function column(string $sql, array $params = []): mixed
    {
        $prepared = $this->prepare($sql, $params);
        $this->execute($prepared);
        $result = $prepared['statement']->fetchColumn();
        $prepared['statement']->closeCursor();
        return $result;
    }


    /**
     * Perform a transaction
     *
     * @param callable $callback
     * @param mixed|null $that
     * @param mixed|null $default
     * @return mixed
     */
    public function transaction(callable $callback, mixed $that = null, mixed $default = null): mixed
    {
        if ($that === null) {
            $that = $this;
        }

        $pdo = $this->getPDO();

        if ($pdo->inTransaction()) {
            return $callback($that);
        }

        $result = $default;

        try {
            $pdo->beginTransaction();
            $result = $callback($that);
            $pdo->commit();
        } catch (PDOException $exception) {
            $pdo->rollBack();
            if ($this->throwTransactionExceptions) {
                throw $exception;
            }
        }

        return $result;
    }

    /**
     * Replace placeholders with parameters.
     *
     * @param string $query
     * @param array $params
     * @return string
     */
    protected function replaceParams(string $query, array $params): string
    {
        $compiler = $this->getCompiler();

        return preg_replace_callback('/\?/', function () use (&$params, $compiler) {
            $param = array_shift($params);
            $param = is_object($param) ? get_class($param) : $param;

            if (is_int($param) || is_float($param)) {
                return $param;
            } elseif ($param === null) {
                return 'NULL';
            } elseif (is_bool($param)) {
                return $param ? 'TRUE' : 'FALSE';
            } elseif (is_resource($param)) {
                return $compiler->quote('RESOURCE#' . get_resource_id($param));
            } else {
                return $compiler->quote($param);
            }
        }, $query);
    }

    /**
     * Prepares a query.
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    protected function prepare(string $query, array $params): array
    {
        try {
            $statement = $this->getPDO()->prepare($query);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . ' [ ' . $this->replaceParams($query, $params) . ' ] ',
                (int)$e->getCode(), $e->getPrevious());
        }

        return ['query' => $query, 'params' => $params, 'statement' => $statement];
    }

    /**
     * Executes a prepared query and returns TRUE on success or FALSE on failure.
     *
     * @param array $prepared
     * @return bool
     */
    protected function execute(array $prepared): bool
    {
        if ($this->logQueries) {
            $start = microtime(true);
            $log = [
                'query' => $this->replaceParams($prepared['query'], $prepared['params']),
            ];
            $this->log[] = &$log;
        }

        try {
            if ($prepared['params']) {
                $this->bindValues($prepared['statement'], $prepared['params']);
            }
            $result = $prepared['statement']->execute();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage() . ' [ ' . $this->replaceParams($prepared['query'],
                    $prepared['params']) . ' ] ', (int)$e->getCode(), $e->getPrevious());
        }

        if ($this->logQueries) {
            /** @noinspection PhpUndefinedVariableInspection */
            $log['time'] = microtime(true) - $start;
        }

        return $result;
    }

    /**
     * @param PDOStatement $statement
     * @param array $values
     */
    protected function bindValues(PDOStatement $statement, array $values): void
    {
        foreach ($values as $key => $value) {
            $param = PDO::PARAM_STR;

            if (is_null($value)) {
                $param = PDO::PARAM_NULL;
            } elseif (is_int($value)) {
                $param = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $param = PDO::PARAM_BOOL;
            } elseif (is_resource($value)) {
                $param = PDO::PARAM_LOB;
            }

            $statement->bindValue($key + 1, $value, $param);
        }
    }

    public function __serialize(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'logQueries' => $this->logQueries,
            'options' => $this->options,
            'commands' => $this->commands,
            'dsn' => $this->dsn,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->logQueries = $data['logQueries'];
        $this->options = $data['options'];
        $this->commands = $data['commands'];
        $this->dsn = $data['dsn'];
    }
}
