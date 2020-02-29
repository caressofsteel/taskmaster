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

if (!defined('TASKMASTERPATH')) define('TASKMASTERPATH', dirname(__FILE__) . '/');

require_once(TASKMASTERPATH . 'db/config.php');
require_once(TASKMASTERPATH . 'core/Lang.class.php');
require_once(TASKMASTERPATH . 'lang/' . $config['lang'] . '.php');

header('Content-type: text/javascript; charset=utf-8');
?>
taskmaster.lang.init(<?= Lang::instance()->makeJS() ?>);