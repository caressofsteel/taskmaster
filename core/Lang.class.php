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

class DefaultLang
{
	protected $js = array();
	protected $inc = array();
	private $lang;

	static private $instance = null;

	private $default_js = array(
		'confirmDelete' => "Are you sure you want to delete the task?",
		'confirmLeave' => "There can be unsaved data. Do you really want to leave?",
		'actionNoteSave' => "save",
		'actionNoteCancel' => "cancel",
		'error' => "ERROR (click for details)",
		'error_silent' => "AN ERROR HAS OCCURRED",
		'denied' => "Access denied",
		'invalidpass' => "Wrong password",
		'invalidlogin' => "Invalid credentials",
		'tagfilter' => "Tag:",
		'addList' => "Create new list",
		'addListDefault' => "Tasks",
		'renameList' => "Rename list",
		'deleteList' => "This will delete current list with all tasks in it.\nAre you sure?",
		'clearCompleted' => "This will delete all completed tasks in the list.\nAre you sure?",
		'settingsSaved' => "Settings saved. Reloading...",
		'um_usercreated' => "User created",
		'um_userupdated' => "User updated",
		'um_userdeleted' => "User deleted",
		'um_createerror1' => "Invalid data",
		'um_createerror2' => "Username already exists",
		'um_createerror3' => "Unable to create user",
		'um_updateerror1' => "Unable to update user",
		'um_deleteerror1' => "Unable to delete user",
		'um_notification_on' => "on",	// needs to be the same like in default_inc!!
		'um_notification_off' => "off",	// needs to be the same like in default_inc!!
		'n_deleteerror1' => "Unable to mark notification as read",
		'start_timer' => "Start timer",
		'log_time' => "Log time",
		'progress' => "Progress",
		'in_progress' => "In progress",
		'time_spent' => "Time spent",
		'time_today' => "today",
		'save' => "Save",
		'cancel' => "Cancel",
		'time_min' => 'min.',
		'progress_spent' => 'Time spent',
		'progress_estimated' => 'Time estimated',
		'loggedin_as' => 'Logged in as:',
	);

	private $default_inc = array(
		'Taskmaster' => "Taskmaster",
		'htab_newtask' => "New task",
		'htab_search' => "Search",
		'btn_add' => "Add",
		'btn_search' => "Search",
		'advanced_add' => "Advanced",
		'searching' => "Searching for",
		'tasks' => "Tasks",
		'taskdate_inline_created' => "created at %s",
		'taskdate_inline_completed' => "Completed at %s",
		'taskdate_inline_duedate' => "Due %s",
		'taskdate_created' => "Created",
		'taskdate_completed' => "Completed",
		'go_back' => "&lt;&lt; Back",
		'edit_task' => "Edit Task",
		'add_task' => "New Task",
		'priority' => "Priority",
		'task' => "Task",
		'note' => "Note",
		'tags' => "Tags",
		'save' => "Save",
		'cancel' => "Cancel",
		'password' => "Password",
		'btn_login' => "Login",
		'a_login' => "Login",
		'a_logout' => "Logout",
		'public_tasks' => "Public Tasks",
		'tagcloud' => "Tags",
		'tagfilter_cancel' => "cancel filter",
		'sortByHand' => "Sort by hand",
		'sortByPriority' => "Sort by priority",
		'sortByDueDate' => "Sort by due date",
		'sortByDateCreated' => "Sort by date created",
		'sortByDateModified' => "Sort by date modified",
		'due' => "Due",
		'daysago' => "%d days ago",
		'indays' => "in %d days",
		'months_short' => array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"),
		'months_long' => array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"),
		'days_min' => array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"),
		'days_long' => array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"),
		'today' => "today",
		'yesterday' => "yesterday",
		'tomorrow' => "tomorrow",
		'f_past' => "Overdue",
		'f_today' => "Today and tomorrow",
		'f_soon' => "Soon",
		'duration' => "Duration",
		'hour_sign' => "h",
		'action_edit' => "Edit",
		'action_note' => "Edit Note",
		'action_delete' => "Delete",
		'action_priority' => "Priority",
		'action_move' => "Move to",
		'timer_stop' => "Stop timer",
		'timer_pause' => "Pause timer",
		'timer_continue' => "Continue timer",
		'notes' => "Notes:",
		'notes_show' => "Show",
		'notes_hide' => "Hide",
		'list_new' => "New list",
		'list_rename' => "Rename list",
		'list_delete' => "Delete list",
		'list_publish' => "Publish list",
		'list_archive' => "Archive list",
		'list_private' => "Private list",
		'list_showcompleted' => "Show completed tasks",
		'list_clearcompleted' => "Clear completed tasks",
		'list_select' => "Select list",
		'list_export' => "Export",
		'list_export_csv' => "CSV",
		'list_export_ical' => "iCalendar",
		'list_rssfeed' => "RSS Feed",
		'list_notifications' => "Notify on change",
		'list_timetable' => "Timetable",
		'alltags' => "All tags:",
		'alltags_show' => "Show all",
		'alltags_hide' => "Hide all",
		'a_settings' => "Settings",
		'a_notifications' => "Notifications",
		'rss_feed' => "RSS Feed",
		'feed_title' => "%s",
		'feed_completed_tasks' => "Completed tasks",
		'feed_modified_tasks' => "Modified tasks",
		'feed_new_tasks' => "New tasks",
		'alltasks' => "All lists",
		'archivelists' => "Archived lists",

		/* Settings */
		'set_header' => "Settings",
		'set_title' => "Title",
		'set_title_descr' => "(specify if you want to change default title)",
		'set_language' => "Language",
		'set_protection' => "Password protection",
		'set_enabled_single' => "Single user (only one password, see below)",
		'set_enabled_multi' => "Multi user",
		'set_manage_users' => "Manage Users",
		'set_enabled' => "Enabled",
		'set_disabled' => "Disabled",
		'set_newpass' => "New password",
		'set_newpass_descr' => "(leave blank if won't change current password)",
		'set_smartsyntax' => "Smart syntax",
		'set_smartsyntax_descr' => "(/priority/ task /tags/)",
		'set_timetable_day' => "Timetable - Day",
		'set_timetable_day_descr' => "(set the hours per day for the time table)",
		'set_markdown' => "Markdown support",
		'set_markdown_descr' => 'Have a look <a href="http://softwaremaniacs.org/playground/showdown-highlight/" target="_blank">here</a> for examples',
		'set_timezone' => "Time zone",
		'set_autotag' => "Autotagging",
		'set_autotag_descr' => "(automatically adds tag of current tag filter to newly created task)",
		'set_sessions' => "Session handling mechanism",
		'set_sessions_php' => "PHP",
		'set_sessions_files' => "Files",
		'set_firstdayofweek' => "First day of week",
		'set_custom' => "Custom",
		'set_date' => "Date format",
		'set_date2' => "Short Date format",
		'set_shortdate' => "Short Date (current year)",
		'set_clock' => "Clock format",
		'set_12hour' => "12-hour",
		'set_24hour' => "24-hour",
		'set_submit' => "Submit changes",
		'set_cancel' => "Cancel",
		'set_showdate' => "Show task date in list",

		/* user management */
		'um_header' => "User Management",
		'um_username' => "Username",
		'um_email' => "E-Mail",
		'um_role' => "Access-Level",
		'um_notification' => "Global Notifications",
		'um_notification_on' => "on",
		'um_notification_off' => "off",
		'um_password' => "Password",
		'um_rolename_1' => "Administrator",
		'um_rolename_2' => "Read/Write",
		'um_rolename_3' => "read only",
		'um_nousers' => "No users available",
		'um_createuser' => "Create user",
		'access_denied' => "ACCESS DENIED: Verify your DB, disable Multi-User in the config file, and try again.",

		/* notifications */
		'n_task_created' => 'New Task "%s" created',
		'n_task_deleted' => 'Task "%s" deleted',
		'n_task_completed' => 'Task "%s" completed',
		'n_task_changed_priority' => 'Task "%s" changed (Priority)',
		'n_task_changed_note' => 'Task "%s" changed (Comment)',
		'n_task_changed_all' => 'Task "%s" changed',
		'n_list_added' => 'List "%s" created',
		'n_list_renamed' => 'List "%s" renamed in "%s"',
		'n_list_deleted' => 'List "%s" deleted',
		'n_created' => 'Created',
		'n_description' => 'Description',
		'n_user' => 'Username',
		'n_mark_read' => 'mark as read',
		'n_mark_all_read' => 'mark all as read',
		'n_header' => 'Notifications',

		'tt_total' => 'Total',
		'tt_days' => 'days'
	);

	/**
	 * @static
	 * @return DefaultLang
	 */
	static public function instance()
	{
		if (null === self::$instance) {
			self::$instance = new Lang();
		}
		return self::$instance;
	}

	function makeJS()
	{
		$a = array();
		foreach ($this->default_js as $k => $v) {
			if (isset($this->js[$k]))
				$v = $this->js[$k];
			while (is_array($v))
				$v = $v[0];
			$a[$k] = $v;
		}
		$a['daysMin'] = $this->get('days_min');
		$a['daysLong'] = $this->get('days_long');
		$a['monthsLong'] = $this->get('months_long');
		foreach (array('tags', 'tasks', 'f_past', 'f_today', 'f_soon') as $v)
			$a[$v] = $this->get($v);
		return json_encode($a);
	}

	function get($key)
	{
		if (isset($this->inc[$key]))
			return $this->inc[$key];
		if (isset($this->default_inc[$key]))
			return $this->default_inc[$key];
		return $key;
	}
}
