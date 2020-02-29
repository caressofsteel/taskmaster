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

interface IDatabase
{
	/**
	 * @abstract
	 * @param $host
	 * @param null $user
	 * @param null $pass
	 * @param null $db
	 * @return IDatabase
	 */
	public function connect($host, $user = null, $pass = null, $db = null);

	/**
	 * @abstract
	 * @param null $tablename
	 * @return mixed
	 */
	public function last_insert_id($tablename = null);
	public function error();
	public function sq($query, $p = NULL);
	public function sqa($query, $p = NULL);
	/**
	 * @abstract
	 * @param $query
	 * @param null $p
	 * @return IDatabaseResult
	 */
	public function dq($query, $p = NULL);
	public function ex($query, $p = NULL);
	public function affected();
	public function quote($s);
	public function quoteForLike($format, $s);
	public function table_exists($table);
}
