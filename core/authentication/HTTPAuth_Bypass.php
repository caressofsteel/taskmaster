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

require_once(taskmasterCOREPATH . 'interfaces/authentication_bypass.interface.php');

class HTTPAuth_Bypass implements IAuthentication_Bypass
{

	/**
	 * fills session data
	 */
	public function setSession()
	{
		//session_regenerate_id(1);
		$_SESSION['logged'] = 1;
		$userdata = $this->getUserFromDB($_SERVER['PHP_AUTH_USER']);
		$_SESSION['userid'] = $userdata['id'];
		$_SESSION['role'] = $userdata['role'];
	}

	private function getUserFromDB($username)
	{
		$db = DBConnection::instance();
		$result = $db->dq("SELECT role,id FROM {$db->prefix}users WHERE username = ?", array($username));
		$row = $result->fetch_assoc();
		$return = array();
		$return['id'] = 0;		// default, empty user id
		$return['role'] = 3;	// default, readonly
		if ($result && is_array($row) && count($row) > 0) {
			$return['id'] = $row['id'];
			$return['role'] = $row['role'];
		}
		return $return;
	}
}
