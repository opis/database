<?php

namespace Opis\Database;

use Closure;
use PDO;

class Connection
{
    
    protected static $connections = array();
    
    protected static $compilers = array();
    
    protected static $defaultConnection = null;
    
    protected $username = null;
    
    protected $password = null;
    
    protected $log = false;
    
    protected $queries = array();
    
    protected $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    );
    
    protected $prefix;
    
    protected $name;
    
    protected $properties = array();
    
    public function __construct($name, $prefix)
    {
        $this->prefix = $prefix;
    }
    
    public function name()
    {
        return $name;
    }
    
    public function enableLog($value = true)
    {
        $this->log = $value;
    }
    
    public function logQueries()
    {
        return $this->log;
    }
    
    public function query($query)
    {
        $this->queries[] = $query;
    }
    
    public function queries()
    {
        return $this->queries;
    }
    
    public function username($username)
    {
        $this->username = $username;
        return $this;
    }
    
    public function password($password)
    {
        $this->password = $password;
        return $this;
    }
    
    public function property($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }
    
    public function options(array $options)
    {
        foreach($options as $name => $value)
        {
            $this->option($name, $value);
        }
        
        return $this;
    }
    
    public function option($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }
    
    public function pdo()
    {
        return new PDO($this->prefix . ':' . implode(';', $this->properties), $this->username, $this->password, $this->options);
    }
    
    protected function compiler()
    {
        switch($this->prefix)
        {
            case 'mysql':
                return new \Opis\Database\Compiler\MySQL();
            case 'dblib':
            case 'mssql':
            case 'sqlsrv':
            case 'sybase':
                return new \Opis\Database\Compiler\SQLServer();
            case 'oci':
            case 'oracle':
                return new \Opis\Database\Compiler\Oracle();
            case 'firebird':
                return new \Opis\Database\Compiler\Firebird();
            case 'db2':
            case 'ibm':
            case 'odbc':
                return new \Opis\Database\Compiler\DB2();
            case 'nuodb':
                return new \Opis\Database\Compiler\NuoDB();
            default:
                if(isset(static::$compilers[$this->prefix]))
                {
                    return static::$compilers[$this->prefix]();
                }
        }
        return new \Opis\Database\SQL\Compiler();
    }
    
    public static function registerCompiler($prefix, Closure $closure)
    {
        static::$compilers[$prefix] = $closure;
    }
    
    public static function get($name = null)
    {
        if($name == null)
        {
            if(static::$defaultConnection != null)
            {
                return static::$connections[static::$defaultConnection];
            }
            return reset(static::$connections);
        }
        return static::$connections[$name];
    }
    
    public static function other($prefix, $name, $default = false)
    {
        $connection = new Connection($prefix);
        static::$connections[$name] = $connection;
        if($default === true)
        {
            static::$defaultConnection = $name;
        }
        return $connection;
    }
    
    
    public static function dblib($name, $default = false)
    {
        return static::other('dblib', $name, $default);
    }
    
    public static function sybase($name, $default = false)
    {
        return static::other('sybase', $name, $default);
    }
    
    public static function mssql($name, $default = false)
    {
        return static::other('mssql', $name, $default);
    }
    
    public static function firebird($name, $default = false)
    {
        return static::other('firebird', $name, $default);
    }
    
    public static function ibm($name, $default = false)
    {
        return static::other('ibm', $name, $default);
    }
    
    public static function mysql($name, $default = false)
    {
        return static::other('mysql', $name, $default);
    }
    
    public static function sqlsrv($name, $default = false)
    {
        return static::other('sqlsrv', $name, $default);
    }
    
    public static function oci($name, $default = false)
    {
        return static::other('oci', $name, $default);
    }
    
    public static function odbc($name, $default = false)
    {
        return static::other('odbc', $name, $default);
    }
    
    public static function pgsql($name, $default = false)
    {
        return static::other('pgsql', $name, $default);
    }
    
    public static function sqlite($name, $default = false)
    {
        return static::other('sqlite', $name, $default);
    }
    
    public static function nuodb($name, $default = false)
    {
        return static::other('nuodb', $name, $default);
    }
}