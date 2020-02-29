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

# Configuration goes here
$config = array();

# Database type: sqlite or mysql
$config['db'] = '';

# Specify these settings if you selected above to use Mysql
$config['mysql.host'] = "localhost";
$config['mysql.db'] = "tasks_taskmaster";
$config['mysql.user'] = "user";
$config['mysql.password'] = "";

# Tables prefix
$config['prefix'] = "taskmaster_";

# These two parameters are used when Taskmaster index.php called not from installation directory
# 'url' - URL where index.php is called from (ex.: http://site.com/todo.php)
# 'taskmaster_url' - directory URL where Taskmaster is installed (with trailing slash) (ex.: http://site.com/lib/Taskmaster/)
$config['url'] = '';
$config['taskmaster_url'] = '';

# Language pack
$config['lang'] = "en";

# Specify password here to protect your tasks from modification,
#  or leave empty that everyone could read/write tasks
$config['password'] = "";

# To disable smart syntax uncomment the line below
$config['smartsyntax'] = 0;

# multi user support
$config['multiuser'] = 0;

# Default Time zone
$config['timezone'] = 'UTC';

# To disable auto adding selected tag  comment out the line below or set value to 0
$config['autotag'] = 1;

# duedate calendar format: 1 => y-m-d (default), 2 => m/d/y, 3 => d.m.y
$config['duedateformat'] = 1;

# First day of week: 0-Sunday, 1-Monday, 2-Tuesday, .. 6-Saturday
$config['firstdayofweek'] = 1;

# select session handling mechanism: files or default (php default)
$config['session'] = 'files';

# Date/time formats
$config['clock'] = 24;
$config['dateformat'] = 'F j, Y';
$config['dateformat2'] = 'Y-m-d';
$config['dateformatshort'] = 'j M';

# Show task date in list
$config['showdate'] = 0;

#
$config['auth_bypass'] = 'none';
$config['debugmode'] = 0;

#
$config['template'] = 'tuxedo';
$config['timetable_day'] = 8;
$config['markdown'] = 1;
$config['title'] = 'Taskmaster';
