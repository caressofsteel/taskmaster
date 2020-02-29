<?php

/*

# Taskmaster

This file is part of the Taskmaster project. Taskmaster is a simple task, project, and information tracking application.

# Copyright

Copyright 2013-2015 David Rodgers - <https://github.com/caressofsteel/taskmaster>
Copyright 2012-2013 Alexander Reichardt - <https://github.com/alex-LE/yourTinyTodo>
Copyright 2009-2010 Max Pozdeev - <https://github.com/maxpozdeev/mytinytodo>

This project is distributed under the GNU General Public License. Please see the included COPYRIGHT and LICENSE-GPL3 for more information. Copyrights for portions of this file are retained by their owners.

*/ 

class DatabaseResult_Sqlite3
{
	private $parent;
	private $q;
	var $query;
	var $prefix;

	function __construct($query, &$h, $resultless = 0)
	{
		$this->parent = $h;
		$this->parent->lastQuery = $this->query = $query;

		if($resultless)
		{
			$r = $this->parent->dbh->exec($query);
			if($r === false) {
				$ei = $this->parent->dbh->errorInfo();
				throw new Exception("SQLSTATE[$ei[0]] [$ei[1]] $ei[2]");	
			}
			$this->parent->affected = $r;
		}
		else
		{
			$this->q = $this->parent->dbh->query($query);
			if(!$this->q) {
				$ei = $this->parent->dbh->errorInfo();
				throw new Exception("SQLSTATE[$ei[0]] [$ei[1]] $ei[2]");
			}
			$this->parent->affected = $this->q->rowCount();
		}
	}

	function fetch_row()
	{
		return $this->q->fetch(PDO::FETCH_NUM);
	}

	function fetch_assoc()
	{
		return $this->q->fetch(PDO::FETCH_ASSOC);
	}

}

class Database_Sqlite3
{
	var $dbh;
	var $affected = null;
	var $lastQuery;

	function __construct()
	{
	}

	function connect($filename)
	{
		try {
			$this->dbh = new PDO("sqlite:$filename");
		}
		catch(PDOException $e) {
			throw new Exception($e->getMessage());
		}
		return true;
	}

	function sq($query, $p = NULL)
	{
		$q = $this->_dq($query, $p);

		$res = $q->fetch_row();
		if($res === false) return NULL;

		if(sizeof($res) > 1) return $res;
		else return $res[0];
	}

	function sqa($query, $p = NULL)
	{
		$q = $this->_dq($query, $p);

		$res = $q->fetch_assoc();
		if($res === false) return NULL;

		if(sizeof($res) > 1) return $res;
		else return $res[0];
	}
	
	function dq($query, $p = NULL)
	{
		return $this->_dq($query, $p);
	}

	/* 
		for resultless queries like INSERT,UPDATE
	*/
	function ex($query, $p = NULL)
	{
		return $this->_dq($query, $p, 1);
	}

	private function _dq($query, $p = NULL, $resultless = 0)
	{
		if(!isset($p)) $p = array();
		elseif(!is_array($p)) $p = array($p);

		$m = explode('?', $query);

		if(sizeof($p)>0)
		{
			if(sizeof($m)< sizeof($p)+1) {
				throw new Exception("params to set MORE than query params");
			}
			if(sizeof($m)> sizeof($p)+1) {
				throw new Exception("params to set LESS than query params");
			}
			$query = "";
			for($i=0; $i<sizeof($m)-1; $i++) {
				$query .= $m[$i]. (is_null($p[$i]) ? 'NULL' : $this->quote($p[$i]));
			}
			$query .= $m[$i];
		}
		return new DatabaseResult_Sqlite3($query, $this, $resultless);
	}

	function affected()
	{
		return $this->affected;
	}

	function quote($s)
	{
		return $this->dbh->quote($s);
	}

	function quoteForLike($format, $s)
	{
		$s = str_replace(array('\\','%','_'), array('\\\\','\%','\_'), $s);
		return $this->dbh->quote(sprintf($format, $s)). " ESCAPE '\'";
	}

	function last_insert_id()
	{
		return $this->dbh->lastInsertId();
	}

	function table_exists($table)
	{
		$table = $this->dbh->quote($table);
		$q = $this->dbh->query("SELECT 1 FROM $table WHERE 1=0");
		if($q === false) return false;
		else return true;
	}
}
