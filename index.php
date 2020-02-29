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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create temp folders if they do not exist
if (!file_exists('./tmp/sessions')) {
	mkdir('./tmp/sessions', 775, true);
}

require_once('./init.php');

$lang = Lang::instance();

if (!is_int(Config::get('firstdayofweek')) || Config::get('firstdayofweek') < 0 || Config::get('firstdayofweek') > 6) Config::set('firstdayofweek', 1);

define('TEMPLATEPATH', TASKMASTERPATH . 'themes/' . Config::get('template') . '/');

require(TEMPLATEPATH . 'index.php');
