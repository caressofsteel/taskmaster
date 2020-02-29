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

require_once(TASKMASTERPATH . 'core/interfaces/databaseresult.interface.php');

abstract class Database
{
	public $prefix;
	public $dbh;
	public $error_str;
	public $last_result;
	public $affected = null;
	public $lastQuery;

	/**
	 * @abstract
	 * @param $host
	 * @param null $user
	 * @param null $pass
	 * @param null $db
	 * @return IDatabase
	 */
	abstract public function connect($host, $user = null, $pass = null, $db = null);

	/**
	 * @abstract
	 * @param null $tablename
	 * @return mixed
	 */
	abstract public function last_insert_id($tablename = null);
	abstract public function error();
	abstract public function sq($query, $p = NULL);
	abstract public function sqa($query, $p = NULL);
	/**
	 * @abstract
	 * @param $query
	 * @param null $p
	 * @return IDatabaseResult
	 */
	abstract public function dq($query, $p = NULL);
	abstract public function ex($query, $p = NULL);
	abstract public function affected();
	abstract public function quote($s);
	abstract public function quoteForLike($format, $s);
	abstract public function table_exists($table);
}
