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

class TimeTracker
{

	/**
	 * @static
	 * @param $taskid integer
	 * @param $time	integer
	 */
	public static function trackTime($taskid, $time, $date = null)
	{

		// Fix empty session ID if multi-user not enabled		
		if (empty($current_user_id)) {
			$current_user_id = 1;
		} else {
			$current_user_id = (int) $_SESSION['userid'];
		}
		// DR 2020

		$db = DBConnection::instance();
		$lang = Lang::instance();
		if (empty($date) || $date == $lang->get('today')) {
			$date = date("Y-m-d H:i");
		} else {
			$date = date("Y-m-d 00:00", strtotime($date));
		}

		$db->dq("INSERT INTO {$db->prefix}time_tracker (task_id, user_id, minutes, created) VALUES (?, ?, ?, ?)", array($taskid, $current_user_id, $time, $date));
	}

	public static function getTaskTotal($taskid)
	{
		if (intval($taskid) <= 0) {
			return 0;
		}
		$db = DBConnection::instance();
		return $db->sq("SELECT SUM(minutes) FROM {$db->prefix}time_tracker WHERE task_id = " . intval($taskid));
	}
}
