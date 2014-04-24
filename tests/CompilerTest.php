<?php
use \Opis\Database\Connection;
use \Opis\Database\Database;

class CompilerTest extends PHPUnit_Framework_TestCase
{
 
    protected function getDatabase()
    {
        return new Database($this->getConnection());
    }
    
    protected function getConnection()
    {
        return new FakeConnection('Compiler');
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
    
    public function testSelectExpressionColumns()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id}, {name} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->columns(array('id', 'name'));
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectExpressionColumnsAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} AS {uid}, {name} AS {username} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->columns(array('id' => 'uid', 'name' => 'username'));
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectExpressionColumnsMixed()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT {id} AS {uid}, {name} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->columns(array('id' => 'uid', 'name'));
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectExpressionCountAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT COUNT(*) AS {cnt} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->count('*', 'cnt');
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectExpressionMultipleAliased()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT COUNT(*) AS {cnt}, SUM({points}) AS {total}, AVG({points}) AS {average}, {email} FROM {users}';
        $query = $db->from('users')->select(function($expr){
            $expr->count('*', 'cnt');
            $expr->sum('points', 'total');
            $expr->avg('points', 'average');
            $expr->column('email');
        });
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhere()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} = ? AND {email} = ? OR {points} >= ?';
        $query = $db->from('users')
                    ->where('name', 'test')
                    ->andWhere('email', 'test')
                    ->orWhere('points', 0, '>=')
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    
    public function testSelectAllWhereAndGroup()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} = ? AND ({email} = ? OR {points} > ?)';
        $query = $db->from('users')
                    ->where('name', 'test')
                    ->andWhere(function($group){
                        $group->where('email', 'test')
                              ->orWhere('points', 100, '>');
                    })
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereOrGroup()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} = ? OR ({email} = ? AND {points} > ?)';
        $query = $db->from('users')
                    ->where('name', 'test')
                    ->orWhere(function($group){
                        $group->where('email', 'test')
                              ->andWhere('points', 100, '>');
                    })
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereGroup()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE ({name} = ? OR {email} = ?) AND {points} > ?';
        $query = $db->from('users')
                    ->where(function($group){
                        $group->where('name', 'test')
                              ->orWhere('email', 'test');
                    })
                    ->andWhere('points', 100, '>')
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereBetween()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {points} BETWEEN ? AND ? AND {position} BETWEEN ? AND ? OR {age} BETWEEN ? AND ?';
        $query = $db->from('users')
                    ->whereBetween('points', 1, 2)
                    ->andWhereBetween('position', 2, 4)
                    ->orWhereBetween('age', 20, 21)
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNotBetween()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {points} NOT BETWEEN ? AND ? AND {position} NOT BETWEEN ? AND ? OR {age} NOT BETWEEN ? AND ?';
        $query = $db->from('users')
                    ->whereNotBetween('points', 1, 2)
                    ->andWhereNotBetween('position', 2, 4)
                    ->orWhereNotBetween('age', 20, 21)
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNull()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} IS NULL AND {points} IS NULL OR {user} IS NULL';
        $query = $db->from('users')
                    ->whereNull('name')
                    ->andWhereNull('points')
                    ->orWhereNull('user')
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNotNull()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} IS NOT NULL AND {points} IS NOT NULL OR {user} IS NOT NULL';
        $query = $db->from('users')
                    ->whereNotNull('name')
                    ->andWhereNotNull('points')
                    ->orWhereNotNull('user')
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereLike()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} LIKE ? AND {points} LIKE ? OR {user} LIKE ?';
        $query = $db->from('users')
                    ->whereLike('name', '%')
                    ->andWhereLike('points', '%')
                    ->orWhereLike('user', '%')
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNotLike()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} NOT LIKE ? AND {points} NOT LIKE ? OR {user} NOT LIKE ?';
        $query = $db->from('users')
                    ->whereNotLike('name', '%')
                    ->andWhereNotLike('points', '%')
                    ->orWhereNotLike('user', '%')
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereIn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} IN (?) AND {points} IN (?, ?) OR {user} IN (?, ?, ?)';
        $query = $db->from('users')
                    ->whereIn('name', array(1))
                    ->andWhereIn('points', array(1, 2))
                    ->orWhereIn('user', array(1, 2, 3))
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNotIn()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} NOT IN (?) AND {points} NOT IN (?, ?) OR {user} NOT IN (?, ?, ?)';
        $query = $db->from('users')
                    ->whereNotIn('name', array(1))
                    ->andWhereNotIn('points', array(1, 2))
                    ->orWhereNotIn('user', array(1, 2, 3))
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereInSubquery()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} IN (SELECT {name} FROM {names})';
        $query = $db->from('users')
                    ->whereIn('name', function($query){
                        $query->from('names')->select('name');
                    })
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNotInSubquery()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} NOT IN (SELECT {name} FROM {names})';
        $query = $db->from('users')
                    ->whereNotIn('name', function($query){
                        $query->from('names')->select('name');
                    })
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereExists()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE EXISTS (SELECT {name} FROM {names} WHERE {id} = ?)';
        $query = $db->from('users')
                    ->whereExists(function($query){
                        $query->from('names')->where('id', 1)->select('name');
                    })
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllWhereNotExists()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE NOT EXISTS (SELECT {name} FROM {names} WHERE {id} = ?)';
        $query = $db->from('users')
                    ->whereNotExists(function($query){
                        $query->from('names')->where('id', 1)->select('name');
                    })
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
    }
    
    public function testSelectAllHaving()
    {
        $db = $this->getDatabase();
        $expect = 'SELECT * FROM {users} WHERE {name} = ? GROUP BY {name} HAVING COUNT({points}) < ? AND SUM(DISTINCT {points}) > ? OR AVG({points}) = ?';
        $query = $db->from('users')
                    ->where('name', 'test')
                    ->groupBy('name')
                    ->having(function($aggregate){
                        $aggregate->count('points');
                    }, 20, '<')
                    ->andHaving(function($aggregate){
                        $aggregate->sum('points', true);
                    }, 40, '>')
                    ->orHaving(function($aggregate){
                        $aggregate->avg('points');
                    }, 10)
                    ->select();
        $this->assertEquals($this->wrap($expect), $query, $query);
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
   
    public function query($sql, array $params = array())
    {
        return $sql;
    }
    
    public function count($sql, array $params = array())
    {
        return $sql;
    }
    
    public function command($sql, array $params = array())
    {
        return $sql;
    }
    
    public function column($sql, array $params = array())
    {
        return $sql;
    }
   
}
