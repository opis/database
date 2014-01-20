<?php
use \Opis\Database\Connection;
use \Opis\Database\Database;

class CompilerTest extends PHPUnit_Framework_TestCase
{
 
    protected function getDatabase()
    {
        return new FakeDB(new FakeConnection('Compiler'));
    }
    
    protected function wrap($text, $a = '"', $b = '"')
    {
        $text = str_replace('{', $a, $text);
        $text = str_replace('}', $b, $text);
        return $text;
    }
 
    public function testSelectAll()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users}';
        $query = $db->from('users')->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectDistinctAll()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT DISTINCT * FROM {users}';
        $query = $db->from('users')->distinct()->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectSingleColumn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} FROM {users}';
        $query = $db->from('users')->select('id');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectDistinctSingleColumn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT DISTINCT {id} FROM {users}';
        $query = $db->from('users')->distinct()->select('id');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectMutipleColumns()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id}, {name} FROM {users}';
        $query = $db->from('users')->select(array('id', 'name'));
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectDistinctMutipleColumns()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT DISTINCT {id}, {name} FROM {users}';
        $query = $db->from('users')->distinct()->select(array('id', 'name'));
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectMutipleColumnsAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} AS {uid}, {name} FROM {users}';
        $query = $db->from('users')->select(array('id' => 'uid', 'name'));
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectFromMultipleTables()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users}, {profiles}';
        $query = $db->from(array('users', 'profiles'))->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectFromMultipleTablesAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} AS {u}, {profiles} AS {p}';
        $query = $db->from(array('users' => 'u', 'profiles' => 'p'))->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectColumnsFromMultipleTablesAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {p}.{id} FROM {users} AS {u}, {profiles} AS {p}';
        $query = $db->from(array('users' => 'u', 'profiles' => 'p'))->select('p.id');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectCount()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT COUNT(*) FROM {users}';
        $query = $db->from('users')->count();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectDistinctCount()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT DISTINCT COUNT(*) FROM {users}';
        $query = $db->from('users')->distinct()->count();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectCountColumn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT COUNT({id}) FROM {users}';
        $query = $db->from('users')->count('id');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectCountColumnDistinct()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT COUNT(DISTINCT {id}) FROM {users}';
        $query = $db->from('users')->count('id', true);
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectDistinctCountColumnDistinct()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT DISTINCT COUNT(DISTINCT {id}) FROM {users}';
        $query = $db->from('users')->distinct()->count('id', true);
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectColumn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} FROM {users}';
        $query = $db->from('users')->column('id');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAvg()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT AVG({points}) FROM {users}';
        $query = $db->from('users')->avg('points');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAvgDistinct()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT AVG(DISTINCT {points}) FROM {users}';
        $query = $db->from('users')->avg('points', true);
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectSum()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT SUM({points}) FROM {users}';
        $query = $db->from('users')->sum('points');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectSumDistinct()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT SUM(DISTINCT {points}) FROM {users}';
        $query = $db->from('users')->sum('points', true);
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectMax()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT MAX({points}) FROM {users}';
        $query = $db->from('users')->max('points');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectMaxDistinct()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT MAX(DISTINCT {points}) FROM {users}';
        $query = $db->from('users')->max('points', true);
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectMin()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT MIN({points}) FROM {users}';
        $query = $db->from('users')->min('points');
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectMinDistinct()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT MIN(DISTINCT {points}) FROM {users}';
        $query = $db->from('users')->min('points', true);
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectExpressionColumn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->column('id');
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectExpressionColumnAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} AS {uid} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->column('id', 'uid');
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    
}

class FakeDB extends Database
{
    public function query($sql, array $params)
    {
        return $sql;
    }
    
    public function count($sql, array $params)
    {
        return $sql;
    }
    
    public function success($sql, array $params)
    {
        return $sql;
    }
    
    public function column($sql, array $params)
    {
        return $sql;
    }
}

class FakeConnection extends Connection
{
    
   public function compiler()
   {
        if($this->prefix === 'Compiler')
        {
            return new \Opis\Database\SQL\Compiler();
        }
        $class = '\\Opis\\Database\\Compiler\\' . $this->prefix;
        return new $class();
   }
   
   public function pdo()
   {
      return null;
   }
}