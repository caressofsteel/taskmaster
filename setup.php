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

if (!defined('TASKMASTER_VERSION')) {
	define('TASKMASTER_VERSION', 'v2.1');
}

set_exception_handler('myExceptionHandler');

# Check For Existing Config File
if (file_exists('./db/config.php')) {
	require_once('./db/config.php');
} else {
	require_once('./db/config.default.php');
}
if (!isset($config['db'])) {
	if (isset($config['mysql'])) {
		$config['db'] = 'mysql';
		$config['mysql.host'] = $config['mysql'][0];
		$config['mysql.db'] = $config['mysql'][3];
		$config['mysql.user'] = $config['mysql'][1];
		$config['mysql.password'] = $config['mysql'][2];
	} elseif (isset($config['postgres'])) {
		$config['db'] = 'mysql';
		$config['postgres.host'] = $config['postgres'][0];
		$config['postgres.db'] = $config['postgres'][3];
		$config['postgres.user'] = $config['postgres'][1];
		$config['postgres.password'] = $config['postgres'][2];
	} else {
		$config['db'] = 'sqlite';
	}
	if (isset($config['allow']) && $config['allow'] == 'read') $config['allowread'] = 1;
}

if ($config['db'] != '') {
	require_once('./init.php');
	if ($needAuth && !is_logged()) {		
		die("<div style='color: #f60000; font-weight: bold;'>ACCESS DENIED: Verify your DB, disable Multi-User in the config file, and try again.</div>");
	}
	if (strtolower(get_class($db)) == 'database_mysql') {
		$dbtype = 'mysql';
	} elseif (strtolower(get_class($db)) == 'database_postgres') {
		$dbtype = 'postgres';
	} else {
		$dbtype = 'sqlite';
	}
} else {
	if (!defined('TASKMASTERPATH')) define('TASKMASTERPATH', dirname(__FILE__) . '/');
	require_once(TASKMASTERPATH . 'common.php');
	Config::loadConfig($config);
	unset($config);

	$db = 0;
	$dbtype = '';
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta name="robots" content="noindex,nofollow" /><title>Taskmaster ' . TASKMASTER_VERSION . ' Setup</title></head><body>';
echo "<div style='color: green; font-weight: bold; font-size: 30px;'>Taskmaster " . TASKMASTER_VERSION . " Setup</div><hr/>";

# Determine Current Installed Version
$ver = get_ver($db, $dbtype);

# Make Sure We Can Write To The Db Folder
if (!is_writable('./db/')) {
	exitMessage("<span style='color: #ff6666; font-weight: bold;'>Config folder ('db/') is not writable.</span>");
}

# Choose A Db Type
if (!$ver) {
	if (!isset($_POST['installdb']) && !isset($_POST['install'])) {
		exitMessage('<form method="post" action=""><span style="color: black; font-size: 18px; font-weight: bold;">Select database type to use:</span><br/><br/>
		<label><input type="radio" name="installdb" value="sqlite" checked="checked" onclick="document.getElementById(\'mysqlsettings\').style.display=\'none\'" />SQLite</label><br/><br/>
		<label><input type="radio" name="installdb" value="mysql" onclick="document.getElementById(\'mysqlsettings\').style.display=\'\';document.getElementById(\'postgressettings\').style.display=\'none\'" />MySQL</label><br/><br/>
		<label><input type="radio" name="installdb" value="postgres" onclick="document.getElementById(\'postgressettings\').style.display=\'\';document.getElementById(\'mysqlsettings\').style.display=\'none\'" />Postgres</label><br/>
		<div id="mysqlsettings" style="display:none; margin-left:30px;"><br/><table><tr><td>Host:</td><td><input type="text" name="mysql_host" value="localhost" /></td></tr>
		<tr><td>Database:</td><td><input type="text" name="mysql_db" value="taskmaster_tasks" /></td></tr>
		<tr><td>User:</td><td><input type="text" name="mysql_user" value="root" /></td></tr>
		<tr><td>Password:</td><td><input type="password" name="mysql_password" /></td></tr>
		<tr><td>Table prefix:</td><td><input type="text" name="mysql_prefix" value="tm_" /></td></tr>
		</table></div><div id="postgressettings" style="display:none; margin-left:30px;"><br/><table><tr><td>Host:</td><td><input type="text" name="postgres_host" value="localhost" /></td></tr>
		<tr><td>Database:</td><td><input type="text" name="postgres_db" value="taskmaster_tasks" /></td></tr>
		<tr><td>User:</td><td><input type="text" name="postgres_user" value="root" /></td></tr>
		<tr><td>Password:</td><td><input type="password" name="postgres_password" /></td></tr>
		<tr><td>Table prefix:</td><td><input type="text" name="postgres_prefix" value="tm_" /></td></tr>
		</table></div><br/><input type=submit value="Next" /></form>');
	} elseif (isset($_POST['installdb'])) {
		# Save Configuration
		$dbtype = (in_array($_POST['installdb'], array('mysql', 'postgres', 'sqlite'))) ? $_POST['installdb'] : 'sqlite';
		Config::set('db', $dbtype);
		if ($dbtype == 'mysql') {
			Config::set('mysql.host', _post('mysql_host'));
			Config::set('mysql.db', _post('mysql_db'));
			Config::set('mysql.user', _post('mysql_user'));
			Config::set('mysql.password', _post('mysql_password'));
			Config::set('prefix', trim(_post('mysql_prefix')));
		}
		if ($dbtype == 'postgres') {
			Config::set('postgres.host', _post('postgres_host'));
			Config::set('postgres.db', _post('postgres_db'));
			Config::set('postgres.user', _post('postgres_user'));
			Config::set('postgres.password', _post('postgres_password'));
			Config::set('prefix', trim(_post('postgres_prefix')));
		}
		if ($dbtype == 'sqlite') {
			if (!is_writable('./db/')) {
				exitMessage("Database folder ('db/') is not writable.");
			}
			@copy(TASKMASTERPATH . 'db/taskmaster.default.db', TASKMASTERPATH . 'db/taskmaster.db');
		}
		if (!testConnect($error)) {
			exitMessage("Database connection error: $error");
		}

		Config::save();
		exitMessage("<h3>This will create the Taskmaster database.</h3><br /><form method=post><input type=hidden name=install value=1><input type=submit value='Install'></form>");
	}

	# Install Database
	if ($dbtype == 'mysql') {
		try {

			$db->ex(
				"CREATE TABLE {$db->prefix}lists (
						id INT UNSIGNED NOT NULL auto_increment,
						uuid CHAR(36) NOT NULL default '',
						ow INT NOT NULL default 0,
						name VARCHAR(50) NOT NULL default '',
						d_created INT UNSIGNED NOT NULL default 0,
						d_edited INT UNSIGNED NOT NULL default 0,
						sorting TINYINT UNSIGNED NOT NULL default 0,
						published TINYINT UNSIGNED NOT NULL default 0,
						taskview INT UNSIGNED NOT NULL default 0,
						archive INT UNSIGNED NOT NULL default 0,
						private_user_id INT UNSIGNED NOT NULL default 0,
						PRIMARY KEY(id),
						UNIQUE KEY(uuid)
						) CHARSET=utf8 "
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}tasks (
						id INT UNSIGNED NOT NULL auto_increment,
						uuid CHAR(36) NOT NULL default '',
						list_id INT UNSIGNED NOT NULL default 0,
						d_created INT UNSIGNED NOT NULL default 0,   /* time() timestamp */
						d_completed INT UNSIGNED NOT NULL default 0, /* time() timestamp */
						d_edited INT UNSIGNED NOT NULL default 0,    /* time() timestamp */
						compl TINYINT UNSIGNED NOT NULL default 0,
						title VARCHAR(250) NOT NULL,
						note TEXT,
						prio TINYINT NOT NULL default 0,			/* priority -,0,+ */
						ow INT NOT NULL default 0,				/* order weight */
						tags VARCHAR(600) NOT NULL default '',	/* for fast access to task tags */
						tags_ids VARCHAR(250) NOT NULL default '', /* no more than 22 tags (x11 chars) */
						duedate DATETIME default NULL,
						duration double DEFAULT NULL,
						author INT NULL default 0,
						PRIMARY KEY(id),
						KEY(list_id),
						UNIQUE KEY(uuid)
						) CHARSET=utf8 "
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}tags (
						id INT UNSIGNED NOT NULL auto_increment,
						name VARCHAR(50) NOT NULL,
						PRIMARY KEY(id),
						UNIQUE KEY name (name)
						) CHARSET=utf8 "
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}tag2task (
						tag_id INT UNSIGNED NOT NULL,
						task_id INT UNSIGNED NOT NULL,
						list_id INT UNSIGNED NOT NULL,
						KEY(tag_id),
						KEY(task_id),
						KEY(list_id)		/* for tagcloud */
						) CHARSET=utf8 "
			);


			$db->ex(
				"CREATE TABLE IF NOT EXISTS {$db->prefix}users (
						id int(10) unsigned NOT NULL auto_increment,
						uuid varchar(36) NOT NULL,
						username varchar(50) NOT NULL,
						password varchar(32) NOT NULL,
						email varchar(100) NOT NULL,
						d_created int(10) unsigned NOT NULL,
						role enum('1','2','3') NOT NULL default '3',
						PRIMARY KEY  (id)
						) CHARSET=utf8 "
			);


			$db->ex(
				"CREATE TABLE IF NOT EXISTS {$db->prefix}notifications (
						id int(11) NOT NULL auto_increment,
						user_id int(11) NOT NULL,
						creator_user_id int(11) NOT NULL,
						text varchar(255) NOT NULL,
						created timestamp NOT NULL default CURRENT_TIMESTAMP,
						shown tinyint(1) NOT NULL default '0',
						PRIMARY KEY  (id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
			);


			$db->ex(
				"CREATE TABLE IF NOT EXISTS {$db->prefix}notification_listeners (
						id int(11) NOT NULL auto_increment,
						user_id int(11) NOT NULL,
						type set('task','list','global') character set utf8 NOT NULL,
						value int(11) default NULL,
						PRIMARY KEY  (id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
			);


			$db->ex(
				"CREATE TABLE IF NOT EXISTS {$db->prefix}comments (
						id int(11) NOT NULL auto_increment,
						task_id int(11) NOT NULL,
						user_id int(11) NOT NULL,
						created timestamp NOT NULL default CURRENT_TIMESTAMP,
						comment varchar(255) NOT NULL,
						PRIMARY KEY  (id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
			);


			$db->ex(
				"CREATE TABLE IF NOT EXISTS {$db->prefix}time_tracker (
						id int(11) NOT NULL auto_increment,
						created timestamp NOT NULL default CURRENT_TIMESTAMP,
						task_id int(11) NOT NULL,
						user_id int(11) NOT NULL,
						minutes int(11) NOT NULL,
						PRIMARY KEY  (id)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
			);
		} catch (Exception $e) {
			exitMessage("<b>Error:</b> " . htmlarray($e->getMessage()));
		}
	} elseif ($dbtype == 'postgres') {
		try {

			$db->ex(
				"CREATE TABLE {$db->prefix}lists (
						id integer NOT NULL,
						uuid character varying(36) NOT NULL,
						ow integer DEFAULT 0 NOT NULL,
						name character varying(50) NOT NULL,
						d_created integer DEFAULT 0 NOT NULL,
						d_edited integer DEFAULT 0 NOT NULL,
						sorting integer DEFAULT 0 NOT NULL,
						published integer DEFAULT 0 NOT NULL,
						taskview integer DEFAULT 0 NOT NULL,
						archive integer DEFAULT 0 NOT NULL,
						private_user_id integer DEFAULT 0 NOT NULL
					);
						CREATE SEQUENCE {$db->prefix}lists_id_seq
							START WITH 1
							INCREMENT BY 1
							NO MAXVALUE
							NO MINVALUE
							CACHE 1;
						ALTER SEQUENCE {$db->prefix}lists_id_seq OWNED BY {$db->prefix}lists.id;
						ALTER TABLE {$db->prefix}lists ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}lists_id_seq'::regclass);
						ALTER TABLE ONLY {$db->prefix}lists
							ADD CONSTRAINT {$db->prefix}lists_pkey PRIMARY KEY (id);
						ALTER TABLE ONLY {$db->prefix}lists
							ADD CONSTRAINT {$db->prefix}lists_uuid_key UNIQUE (uuid);"
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}tasks (
						id integer NOT NULL,
						uuid character varying(36) NOT NULL,
						list_id integer DEFAULT 0 NOT NULL,
						d_created integer DEFAULT 0 NOT NULL,	-- time() timestamp
						d_completed integer DEFAULT 0 NOT NULL,	-- time() timestamp
						d_edited integer DEFAULT 0 NOT NULL,	-- time() timestamp
						compl integer DEFAULT 0 NOT NULL,
						title character varying(250) NOT NULL,
						note text,
						prio integer DEFAULT 0 NOT NULL,		-- priority -,0,+
						ow integer DEFAULT 0 NOT NULL,			-- order weight
						tags character varying(600),			-- for fast access to task tags
						tags_ids character varying(250),		-- no more than 22 tags (x11 chars)
						duedate datetime,
						author integer DEFAULT 0 NULL
					);
						CREATE SEQUENCE {$db->prefix}tasks_id_seq
							START WITH 1
							INCREMENT BY 1
							NO MAXVALUE
							NO MINVALUE
							CACHE 1;
						ALTER SEQUENCE {$db->prefix}tasks_id_seq OWNED BY {$db->prefix}tasks.id;
						ALTER TABLE {$db->prefix}tasks ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}tasks_id_seq'::regclass);
						ALTER TABLE ONLY {$db->prefix}tasks
							ADD CONSTRAINT {$db->prefix}tasks_pkey PRIMARY KEY (id);
						ALTER TABLE ONLY {$db->prefix}tasks
							ADD CONSTRAINT {$db->prefix}tasks_uuid_key UNIQUE (uuid);
						CREATE INDEX idx_list_id ON {$db->prefix}tasks USING btree (list_id);"
			);

			$db->ex(
				"CREATE TABLE {$db->prefix}tags (
						id integer NOT NULL,
						name character varying(50) NOT NULL
						);
						CREATE SEQUENCE {$db->prefix}tags_id_seq
							START WITH 1
							INCREMENT BY 1
							NO MAXVALUE
							NO MINVALUE
							CACHE 1;
						ALTER SEQUENCE {$db->prefix}tags_id_seq OWNED BY {$db->prefix}tags.id;
						ALTER TABLE {$db->prefix}tags ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}tags_id_seq'::regclass);
						ALTER TABLE ONLY {$db->prefix}tags
							ADD CONSTRAINT {$db->prefix}tags_name_key UNIQUE (name);
						ALTER TABLE ONLY {$db->prefix}tags
							ADD CONSTRAINT {$db->prefix}tags_pkey PRIMARY KEY (id);"
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}tag2task (
						id integer NOT NULL,
						tag_id integer NOT NULL,
						task_id integer NOT NULL,
						list_id integer NOT NULL			-- for tagcloud
						);
						CREATE SEQUENCE {$db->prefix}tag2task_id_seq
							START WITH 1
							INCREMENT BY 1
							NO MAXVALUE
							NO MINVALUE
							CACHE 1;
						ALTER SEQUENCE {$db->prefix}tag2task_id_seq OWNED BY {$db->prefix}tag2task.id;
						ALTER TABLE {$db->prefix}tag2task ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}tag2task_id_seq'::regclass);
						ALTER TABLE ONLY {$db->prefix}tag2task
							ADD CONSTRAINT {$db->prefix}tag2task_pkey PRIMARY KEY (id);
						CREATE INDEX {$db->prefix}idx_tag_id ON {$db->prefix}tag2task USING btree (tag_id);
						CREATE INDEX {$db->prefix}idx_task_id ON {$db->prefix}tag2task USING btree (task_id);
						CREATE INDEX {$db->prefix}tag2task_idx_list_id ON {$db->prefix}tag2task USING btree (list_id);"
			);

			$db->ex(
				"CREATE TABLE {$db->prefix}users (
					id integer NOT NULL,
					uuid character varying(36),
					username character varying(50),
					\"password\" character varying(32),
					email character varying(100),
					d_created integer,
					\"role\" integer
				);
				CREATE SEQUENCE {$db->prefix}users_id_seq
					INCREMENT BY 1
					NO MAXVALUE
					NO MINVALUE
					CACHE 1;
				ALTER SEQUENCE {$db->prefix}users_id_seq OWNED BY {$db->prefix}users.id;
				SELECT pg_catalog.setval('{$db->prefix}users_id_seq', 1, true);
				ALTER TABLE {$db->prefix}users ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}users_id_seq'::regclass);
				ALTER TABLE ONLY {$db->prefix}users
    				ADD CONSTRAINT {$db->prefix}users_pkey PRIMARY KEY (id);
				"
			);

			$db->ex(
				"CREATE TABLE {$db->prefix}notifications (
					id integer NOT NULL,
					user_id integer,
					creator_user_id integer,
					text character varying,
					created timestamp with time zone,
					shown integer
				);
				CREATE SEQUENCE {$db->prefix}notifications_id_seq
					START WITH 1
					INCREMENT BY 1
					NO MAXVALUE
					NO MINVALUE
					CACHE 1;
				ALTER SEQUENCE {$db->prefix}notifications_id_seq OWNED BY {$db->prefix}notifications.id;
				ALTER TABLE {$db->prefix}notifications ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}notifications_id_seq'::regclass);
				ALTER TABLE ONLY {$db->prefix}notifications
    				ADD CONSTRAINT {$db->prefix}notifications_pkey PRIMARY KEY (id);

				"
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}notification_listeners (
					id integer NOT NULL,
					user_id integer,
					\"type\" character varying,
					value integer
				);
				COMMENT ON COLUMN {$db->prefix}notification_listeners.\"type\" IS '''task'',''list'',''global''';
				CREATE SEQUENCE {$db->prefix}notification_listeners_id_seq
					START WITH 1
					INCREMENT BY 1
					NO MAXVALUE
					NO MINVALUE
					CACHE 1;
				ALTER SEQUENCE {$db->prefix}notification_listeners_id_seq OWNED BY {$db->prefix}notification_listeners.id;
				ALTER TABLE {$db->prefix}notification_listeners ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}notification_listeners_id_seq'::regclass);
				ALTER TABLE ONLY {$db->prefix}notification_listeners
    				ADD CONSTRAINT {$db->prefix}notification_listeners_pkey PRIMARY KEY (id);
				"
			);


			$db->ex(
				'CREATE TABLE ' . $db->prefix . 'comments (
					id integer NOT NULL,
					task_id integer,
					user_id integer,
					created timestamp with time zone DEFAULT now(),
					"comment" character varying
				);' . "
				CREATE SEQUENCE {$db->prefix}comments_id_seq
					INCREMENT BY 1
					NO MAXVALUE
					NO MINVALUE
					CACHE 1;
				ALTER SEQUENCE {$db->prefix}comments_id_seq OWNED BY {$db->prefix}comments.id;
				ALTER TABLE {$db->prefix}comments ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}comments_id_seq'::regclass);
				ALTER TABLE ONLY {$db->prefix}comments
    				ADD CONSTRAINT {$db->prefix}comments_pkey PRIMARY KEY (id);
				"
			);


			$db->ex(
				"CREATE TABLE {$db->prefix}time_tracker (
					id integer NOT NULL,
					created timestamp with time zone,
					task_id integer,
					user_id integer,
					minutes integer
				);
				CREATE SEQUENCE {$db->prefix}time_tracker_id_seq
					INCREMENT BY 1
					NO MAXVALUE
					NO MINVALUE
					CACHE 1;
				ALTER SEQUENCE {$db->prefix}time_tracker_id_seq OWNED BY {$db->prefix}time_tracker.id;
				ALTER TABLE {$db->prefix}time_tracker ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}time_tracker_id_seq'::regclass);
				ALTER TABLE ONLY {$db->prefix}time_tracker
    				ADD CONSTRAINT {$db->prefix}time_tracker_pkey PRIMARY KEY (id);
				"
			);

			// Using || to concatenate in Taskmaster is not recommeneded because there are
			// database drivers for Taskmaster that do not support the syntax, however
			// they do support CONCAT(item1, item2) which we can replicate in
			// PostgreSQL. PostgreSQL requires the function to be defined for each
			// different argument variation the function can handle.
			$db->ex('CREATE OR REPLACE FUNCTION "concat"(anynonarray, anynonarray) RETURNS text AS \'SELECT CAST($1 AS text) || CAST($2 AS text);\' LANGUAGE \'sql\'');
			$db->ex('CREATE OR REPLACE FUNCTION "concat"(text, anynonarray) RETURNS text AS \'SELECT $1 || CAST($2 AS text);\' LANGUAGE \'sql\'');
			$db->ex('CREATE OR REPLACE FUNCTION "concat"(anynonarray, text) RETURNS text AS \'SELECT CAST($1 AS text) || $2;\' LANGUAGE \'sql\'');
			$db->ex('CREATE OR REPLACE FUNCTION "concat"(text, text) RETURNS text AS \'SELECT $1 || $2;\' LANGUAGE \'sql\'');
		} catch (Exception $e) {
			exitMessage("<b>Error:</b> " . htmlarray($e->getMessage()));
		}
	} else #SQLite
	{
		try {

			$db->ex(
				"CREATE TABLE {$db->prefix}lists (
						id INTEGER PRIMARY KEY,
						uuid CHAR(36) NOT NULL,
						ow INTEGER NOT NULL default 0,
						name VARCHAR(50) NOT NULL,
						d_created INTEGER UNSIGNED NOT NULL default 0,
						d_edited INTEGER UNSIGNED NOT NULL default 0,
						sorting TINYINT UNSIGNED NOT NULL default 0,
						published TINYINT UNSIGNED NOT NULL default 0,
						taskview INTEGER UNSIGNED NOT NULL default 0,
						archive INTEGER UNSIGNED NOT NULL default 0,
						private_user_id INTEGER UNSIGNED NOT NULL default 0
						) "
			);
			$db->ex("CREATE UNIQUE INDEX lists_uuid ON {$db->prefix}lists (uuid)");

			$db->ex(
				"CREATE TABLE {$db->prefix}tasks (
						id INTEGER PRIMARY KEY,
						uuid CHAR(36) NOT NULL,
						list_id INTEGER UNSIGNED NOT NULL default 0,
						d_created INTEGER UNSIGNED NOT NULL default 0,
						d_completed INTEGER UNSIGNED NOT NULL default 0,
						d_edited INTEGER UNSIGNED NOT NULL default 0,
						compl TINYINT UNSIGNED NOT NULL default 0,
						title VARCHAR(250) NOT NULL,
						note TEXT,
						prio TINYINT NOT NULL default 0,
						ow INTEGER NOT NULL default 0,
						tags VARCHAR(600) NOT NULL default '',
						tags_ids VARCHAR(250) NOT NULL default '',
						duedate DATETIME default NULL,
						duration DOUBLE,
						author INTEGER NULL default 0
						) "
			);
			$db->ex("CREATE INDEX todo_list_id ON {$db->prefix}tasks (list_id)");
			$db->ex("CREATE UNIQUE INDEX todo_uuid ON {$db->prefix}tasks (uuid)");


			$db->ex(
				"CREATE TABLE {$db->prefix}tags (
					id INTEGER PRIMARY KEY AUTOINCREMENT,
					name VARCHAR(50) NOT NULL COLLATE NOCASE
					) "
			);
			$db->ex("CREATE UNIQUE INDEX tags_name ON {$db->prefix}tags (name COLLATE NOCASE)");

			$db->ex(
				"CREATE TABLE {$db->prefix}tag2task (
				tag_id INTEGER NOT NULL,
				task_id INTEGER NOT NULL,
				list_id INTEGER NOT NULL
				) "
			);
			$db->ex("CREATE INDEX tag2task_tag_id ON {$db->prefix}tag2task (tag_id)");
			$db->ex("CREATE INDEX tag2task_task_id ON {$db->prefix}tag2task (task_id)");
			$db->ex("CREATE INDEX tag2task_list_id ON {$db->prefix}tag2task (list_id)");	/* for tagcloud */
			$db->ex('CREATE TABLE ' . $db->prefix . 'users ("id" INTEGER PRIMARY KEY  NOT NULL , "uuid" VARCHAR, "username" VARCHAR, "password" VARCHAR, "email" VARCHAR, "d_created" INTEGER, "role" INTEGER)');
			$db->ex('CREATE TABLE "' . $db->prefix . 'notifications" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "user_id" INTEGER, "text" VARCHAR, "created" DATETIME DEFAULT CURRENT_TIMESTAMP, "shown" INTEGER)');
			$db->ex('CREATE  TABLE "' . $db->prefix . 'notification_listeners" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "user_id" INTEGER, "type" VARCHAR, "value" INTEGER)');
			$db->ex('CREATE TABLE "' . $db->prefix . 'comments" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "task_id" INTEGER, "user_id" INTEGER, "created" DATETIME DEFAULT CURRENT_TIMESTAMP, "comment" TEXT)');
			$db->ex('CREATE TABLE "' . $db->prefix . 'time_tracker" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "created" DATETIME, "task_id" INTEGER, "user_id" INTEGER, "minutes" INTEGER)');
		} catch (Exception $e) {
			exitMessage("<b>Error:</b> " . htmlarray($e->getMessage()));
		}
	}

	# Create Default List
	$db->ex("INSERT INTO {$db->prefix}lists (uuid,name,d_created) VALUES (?,?,?)", array(generateUUID(), 'Tasks', time()));

	// Create Default User For Multi User Support
	$uuid = generateUUID();
	$db->ex("INSERT INTO {$db->prefix}users (id, uuid, username, password, email, d_created, role) VALUES (1, '" . $uuid . "', 'admin', '" . hashPassword('admin', $uuid) . "', 'taskmaster@localhost', " . time() . ", '1')");
} elseif ($ver == TASKMASTER_VERSION) {
	echo "<h3>The database is currently " . $ver . " and does not require an update.</h3>";
	echo "<h2><a href='index.php'>Proceed to Taskmaster</a></h2>";
	echo "<em>NOTE: If Multi-User support is enabled, the default username and password is admin/admin.</em>";
	echo "<br /><br /><hr />";
	echo "<div style='color: #f66; font-weight: bold;'>ATTENTION: For increased security, delete SETUP.PHP from the root folder.</div>";
	printFooter();
	exitMessage("");
} else {
	if (!isset($_POST['update'])) {
		exitMessage('Update Database v' . $ver . '
		<form name="frm"" method="post"><input type="hidden" name="update" value="1"><input type="hidden" name="tz" value="-1"><input type="submit" value=" Update "></form>
		<script type="text/javascript">var tz = -1 * (new Date()).getTimezoneOffset(); document.frm.tz.value = tz;</script>
		');
	}

	# Update Required?
	if ($ver == 'Taskmaster 2.0') {
		update_20_21($db, $dbtype);
	}
}

// Finish Installation
echo "<h3>Installation Complete</h3>";
echo "<h2><a href='index.php'>Proceed to Taskmaster</a></h2>";
echo "<em>NOTE: If Multi-User support is enabled, the default username and password is admin/admin.</em>";
echo "<br /><br /><hr />";
echo "<div style='color: #f66; font-weight: bold;'>ATTENTION: For increased security, delete SETUP.PHP from the root folder.</div>";
printFooter();

// Verify Application Version
function get_ver($db, $dbtype)
{
	if (!$db || $dbtype == '') return '';

	// Is The Database Installed?
	if (!$db->table_exists($db->prefix . 'tasks')) return '';

	// Are We Running An Old Version?
	$v = 'v2.0';
	if ($dbtype == 'mysql') {
		if (!has_field_mysql($db, $db->prefix . 'tasks', 'd_edited')) return $v;
	} elseif ($dbtype == 'postgres') {
		if (!has_field_postgres($db, $db->prefix . 'tasks', 'd_edited')) return $v;
	} else {
		if (!has_field_sqlite($db, $db->prefix . 'tasks', 'd_edited')) return $v;
	}

	$v = 'v2.1';
	return $v;
}

function exitMessage($s)
{
	echo $s;
	printFooter();
	exit;
}

function printFooter()
{
	echo "</body></html>";
}

function has_field_sqlite($db, $table, $field)
{
	$q = $db->dq("PRAGMA table_info(" . $db->quote($table) . ")");
	while ($r = $q->fetch_row()) {
		if ($r[1] == $field) return true;
	}
	return false;
}

function has_field_mysql($db, $table, $field)
{
	$q = $db->dq("DESCRIBE $table");
	while ($r = $q->fetch_row()) {
		if ($r[0] == $field) return true;
	}
	return false;
}

function has_field_postgres($db, $table, $field)
{
	$table = addslashes($table);
	$q = $db->dq("select * from INFORMATION_SCHEMA.COLUMNS where column_name='$field' AND table_name = '$table';");
	if ($q->rows() > 0) return false;
	else return true;
}

function get_field_type_mysql($db, $table, $field)
{
	$q = $db->dq("DESCRIBE $table $field");
	$r = $q->fetch_row();
	if ($q && is_array($r)) {
		return strtolower($r[1]);
	} else {
		return false;
	}
}

function get_field_type_postgres($db, $table, $field)
{
	$q = $db->dq("SELECT data_type FROM information_schema.columns WHERE table_name ='$table' AND column_name = '$field'");
	$r = $q->fetch_row();
	if ($q && is_array($r)) {
		return strtolower($r[0]);
	} else {
		return false;
	}
}

function testConnect(&$error)
{
	try {
		require_once(TASKMASTERPATH . 'core/Database.class.php');
		require_once(TASKMASTERPATH . 'core/interfaces/databaseresult.interface.php');
		if (Config::get('db') == 'mysql') {
			require_once(TASKMASTERPATH . 'core/db/class.db.mysql.php');
			$db = new Database_Mysql;
			$db->connect(Config::get('mysql.host'), Config::get('mysql.user'), Config::get('mysql.password'), Config::get('mysql.db'));
		} else if (Config::get('db') == 'postgres') {
			require_once(TASKMASTERPATH . 'core/db/class.db.postgres.php');
			$db = new Database_Postgres;
			$db->connect(Config::get('postgres.host'), Config::get('postgres.user'), Config::get('postgres.password'), Config::get('postgres.db'));
		} else {
			if (false === $f = @fopen(TASKMASTERPATH . 'db/taskmaster.db', 'a+')) throw new Exception("database file is not readable/writable");
			else fclose($f);

			if (!is_writable(TASKMASTERPATH . 'db/')) throw new Exception("database directory ('db') is not writable");

			require_once(TASKMASTERPATH . 'core/db/class.db.sqlite3.php');
			$db = new Database_Sqlite3;
			$db->connect(TASKMASTERPATH . 'db/taskmaster.db');
		}
	} catch (Exception $e) {
		$error = $e->getMessage();
		return 0;
	}
	return 1;
}

function myExceptionHandler($e)
{
	echo '<br/><b>Fatal Error:</b> \'' . $e->getMessage() . '\' in <i>' . $e->getFile() . ':' . $e->getLine() . '</i>' .
		"\n<pre>" . $e->getTraceAsString() . "</pre>\n";
	exit;
}

### update v2.0 to v2.1 ##########
function update_20_21($db, $dbtype)
{
	$db->ex("BEGIN");
	if ($dbtype == 'mysql') {
		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}users (
			  id int(10) unsigned NOT NULL auto_increment,
			  uuid varchar(36) NOT NULL,
			  username varchar(50) NOT NULL,
			  password varchar(32) NOT NULL,
			  email varchar(100) NOT NULL,
			  d_created int(10) unsigned NOT NULL,
			  role enum('1','2','3') NOT NULL default '3',
			  PRIMARY KEY  (id)
			) CHARSET=utf8 "
		);

		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}notifications (
			  id int(11) NOT NULL auto_increment,
			  user_id int(11) NOT NULL,
			  creator_user_id int(11) NOT NULL,
			  text varchar(255) NOT NULL,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP,
			  shown tinyint(1) NOT NULL default '0',
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);


		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}notification_listeners (
			  id int(11) NOT NULL auto_increment,
			  user_id int(11) NOT NULL,
			  type set('task','list','global') character set utf8 NOT NULL,
			  value int(11) default NULL,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);


		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}comments (
			  id int(11) NOT NULL auto_increment,
			  task_id int(11) NOT NULL,
			  user_id int(11) NOT NULL,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP,
			  comment varchar(255) NOT NULL,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);


		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}time_tracker (
			  id int(11) NOT NULL auto_increment,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP,
			  task_id int(11) NOT NULL,
			  user_id int(11) NOT NULL,
			  minutes int(11) NOT NULL,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);
	} else if ($dbtype == 'postgres') {
		$db->ex(
			"CREATE TABLE {$db->prefix}users (
					id integer NOT NULL,
					uuid character varying(36),
					username character varying(50),
					\"password\" character varying(32),
					email character varying(100),
					d_created integer,
					\"role\" integer
				);
				CREATE SEQUENCE {$db->prefix}users_id_seq
					INCREMENT BY 1
					NO MAXVALUE
					NO MINVALUE
					CACHE 1;
				ALTER SEQUENCE {$db->prefix}users_id_seq OWNED BY {$db->prefix}users.id;
				SELECT pg_catalog.setval('{$db->prefix}users_id_seq', 1, true);
				ALTER TABLE {$db->prefix}users ALTER COLUMN id SET DEFAULT nextval('{$db->prefix}users_id_seq'::regclass);
				ALTER TABLE ONLY {$db->prefix}users
    				ADD CONSTRAINT {$db->prefix}users_pkey PRIMARY KEY (id);
				"
		);

		// Using || to concatenate in Taskmaster is not recommeneded because there are
		// database drivers for Taskmaster that do not support the syntax, however
		// they do support CONCAT(item1, item2) which we can replicate in
		// PostgreSQL. PostgreSQL requires the function to be defined for each
		// different argument variation the function can handle.
		$db->ex('CREATE OR REPLACE FUNCTION "concat"(anynonarray, anynonarray) RETURNS text AS \'SELECT CAST($1 AS text) || CAST($2 AS text);\' LANGUAGE \'sql\'');
		$db->ex('CREATE OR REPLACE FUNCTION "concat"(text, anynonarray) RETURNS text AS \'SELECT $1 || CAST($2 AS text);\' LANGUAGE \'sql\'');
		$db->ex('CREATE OR REPLACE FUNCTION "concat"(anynonarray, text) RETURNS text AS \'SELECT CAST($1 AS text) || $2;\' LANGUAGE \'sql\'');
		$db->ex('CREATE OR REPLACE FUNCTION "concat"(text, text) RETURNS text AS \'SELECT $1 || $2;\' LANGUAGE \'sql\'');

		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}notifications (
			  id int(11) NOT NULL auto_increment,
			  user_id int(11) NOT NULL,
			  creator_user_id int(11) NOT NULL,
			  text varchar(255) NOT NULL,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP,
			  shown tinyint(1) NOT NULL default '0',
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);


		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}notification_listeners (
			  id int(11) NOT NULL auto_increment,
			  user_id int(11) NOT NULL,
			  type set('task','list','global') character set utf8 NOT NULL,
			  value int(11) default NULL,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);


		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}comments (
			  id int(11) NOT NULL auto_increment,
			  task_id int(11) NOT NULL,
			  user_id int(11) NOT NULL,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP,
			  comment varchar(255) NOT NULL,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);


		$db->ex(
			"CREATE TABLE IF NOT EXISTS {$db->prefix}time_tracker (
			  id int(11) NOT NULL auto_increment,
			  created timestamp NOT NULL default CURRENT_TIMESTAMP,
			  task_id int(11) NOT NULL,
			  user_id int(11) NOT NULL,
			  minutes int(11) NOT NULL,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "
		);
	} else #sqlite
	{
		$db->ex('CREATE TABLE ' . $db->prefix . 'users ("id" INTEGER PRIMARY KEY  NOT NULL , "uuid" VARCHAR, "username" VARCHAR, "password" VARCHAR, "email" VARCHAR, "d_created" INTEGER, "role" INTEGER)');
		$db->ex('CREATE TABLE "' . $db->prefix . 'notifications" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "user_id" INTEGER, "creator_user_id" INTEGER, "text" VARCHAR, "created" DATETIME DEFAULT CURRENT_TIMESTAMP, "shown" INTEGER)');
		$db->ex('CREATE TABLE "' . $db->prefix . 'notification_listeners" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "user_id" INTEGER, "type" VARCHAR, "value" INTEGER)');
		$db->ex('CREATE TABLE "' . $db->prefix . 'comments" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "task_id" INTEGER, "user_id" INTEGER, "created" DATETIME DEFAULT CURRENT_TIMESTAMP, "comment" TEXT)');
		$db->ex('CREATE TABLE "' . $db->prefix . 'time_tracker" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "created" DATETIME, "task_id" INTEGER, "user_id" INTEGER, "minutes" INTEGER)');
	}

	// create default user - for multi user support
	$uuid = generateUUID();
	$db->ex("INSERT INTO {$db->prefix}users (id, uuid, username, password, email, d_created, role) VALUES (1, '" . $uuid . "', 'admin', '" . hashPassword('admin', $uuid) . "', 'mail@example.com', " . time() . ", '1')");

	$db->ex("COMMIT");
}
