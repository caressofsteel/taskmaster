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

// ---------------------------------------------------------------------------- //
class DatabaseResult_Mysql
{

	var $parent;
	var $q;
	var $query;
	var $rows = NULL;
	var $affected = NULL;
	var $prefix = '';

	function __construct($query, &$h, $resultless = 0)
	{
		$this->parent = $h;
		$this->query = $query;

		$this->q = mysqli_query($this->parent->dbh, $query);

		if(!$this->q)
		{
			throw new Exception($this->parent->error());
		}
	}

	function affected()
	{
		if(is_null($this->affected))
		{
			$this->affected = mysqli_affected_rows($this->parent->dbh);
		}
		return $this->affected;
	}

	function fetch_row()
	{
		return mysqli_fetch_row($this->q);
	}

	function fetch_assoc()
	{
		return mysqli_fetch_assoc($this->q);
	}

	function rows()
	{
		if (!is_null($this -> rows)) return $this->rows;
		$this->rows = mysqli_num_rows($this->q);
		return $this->rows;
	}
}

// ---------------------------------------------------------------------------- //
class Database_Mysql
{
	var $dbh;
	var $error_str;

	function __construct()
	{
	}

	function connect($host, $user, $pass, $db)
	{
		if(!$this->dbh = @mysqli_connect($host,$user,$pass))
		{
			throw new Exception(mysqli_connect_error());
		}
		if( @!mysqli_select_db($this->dbh, $db) )
		{
			throw new Exception($this->error());
		}
		return true;
	}

	function last_insert_id()
	{
		return mysqli_insert_id($this->dbh);
	}	
	
	function error()
	{
		return mysqli_error($this->dbh);
	}

	function sq($query, $p = NULL)
	{
		$q = $this->_dq($query, $p);

		if($q->rows()) $res = $q->fetch_row();
		else return NULL;

		if(sizeof($res) > 1) return $res;
		else return $res[0];
	}

	function sqa($query, $p = NULL)
	{
		$q = $this->_dq($query, $p);

		if($q->rows()) $res = $q->fetch_assoc();
		else return NULL;

		if(sizeof($res) > 1) return $res;
		else return $res[0];
	}

	function dq($query, $p = NULL)
	{
		return $this->_dq($query, $p);
	}

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
		return new DatabaseResult_Mysql($query, $this, $resultless);
	}

	function affected()
	{
		return mysqli_affected_rows($this->dbh);
	}

	function quote($s)
	{
		return '\''. addslashes($s). '\'';
	}

	function quoteForLike($format, $s)
	{
		$s = str_replace(array('%','_'), array('\%','\_'), addslashes($s));
		return '\''. sprintf($format, $s). '\'';
	}

	function table_exists($table)
	{
		$table = addslashes($table);
		$q = mysqli_query($this->dbh, "SELECT 1 FROM `$table` WHERE 1=0");
		if($q === false) return false;
		else return true;
	}
}
