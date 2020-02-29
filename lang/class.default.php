<?php

/*

# Taskmaster

Taskmaster Language Pack
Language: English
Original name: English

This file is part of the Taskmaster project. Taskmaster is a simple task, project, and information tracking application.

# Copyright

Copyright 2013-2015 David Rodgers - <https://github.com/caressofsteel/taskmaster>
Copyright 2012-2013 Alexander Reichardt - <https://github.com/alex-LE/yourTinyTodo>
Copyright 2009-2010 Max Pozdeev - <https://github.com/maxpozdeev/mytinytodo>

This project is distributed under the GNU General Public License. Please see the included COPYRIGHT and LICENSE-GPL3 for more information. Copyrights for portions of this file are retained by their owners.

*/

class DefaultLang
{
	protected static $instance;
	private $default_js = array(
		'confirmDelete' => "Are you sure you want to delete the task?",
		'confirmLeave' => "There might be unsaved data. Do you really want to leave?",
		'actionNoteSave' => "save",
		'actionNoteCancel' => "cancel",
		'error' => "ERROR (click for details)",
		'denied' => "Access Denied",
		'invalidpass' => "Wrong Password",
		'tagfilter' => "Tag:",
		'addList' => "Create New List",
		'addListDefault' => "Tasks",
		'renameList' => "Rename List",
		'deleteList' => "This will delete the current list and all tasks within.\\nAre you sure?",
		'clearCompleted' => "This will delete all completed tasks in the list.\\nAre you sure?",
		'settingsSaved' => "Settings Saved. Reloading...",
	);

	private $default_inc = array(
		'Taskmaster' => "Taskmaster",
		'htab_newtask' => "New Task",
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
		'tagfilter_cancel' => "Cancel Filter",
		'sortByHand' => "Sort: Manual",
		'sortByPriority' => "Sort: Priority",
		'sortByDueDate' => "Sort: Due Date",
		'sortByDateCreated' => "Sort: Date Created",
		'sortByDateModified' => "Sort: Date Modified",
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
		'f_today' => "Today and Tomorrow",
		'f_soon' => "Soon",
		'action_edit' => "Edit",
		'action_note' => "Edit Note",
		'action_delete' => "Delete",
		'action_priority' => "Priority",
		'action_move' => "Move to",
		'notes' => "Notes:",
		'notes_show' => "Show",
		'notes_hide' => "Hide",
		'list_new' => "New List",
		'list_rename' => "Rename List",
		'list_delete' => "Delete List",
		'list_publish' => "Publish List",
		'list_showcompleted' => "Show Completed Tasks",
		'list_clearcompleted' => "Clear Completed Tasks",
		'list_select' => "Select List",
		'list_export' => "Export",
		'list_export_csv' => "CSV",
		'list_export_ical' => "iCalendar",
		'list_rssfeed' => "RSS Feed",
		'alltags' => "All Tags:",
		'alltags_show' => "Show All",
		'alltags_hide' => "Hide All",
		'a_settings' => "Settings",
		'rss_feed' => "RSS Feed",
		'feed_title' => "%s",
		'feed_completed_tasks' => "Completed Tasks",
		'feed_modified_tasks' => "Modified Tasks",
		'feed_new_tasks' => "New Tasks",
		'alltasks' => "All Tasks",

		/* Settings */
		'set_header' => "Settings",
		'set_template' => "Template",
		'set_title' => "Title",
		'set_title_descr' => "(specify if you want to change default title)",
		'set_language' => "Language",
		'set_protection' => "Password Protection",
		'set_enabled' => "Enabled",
		'set_disabled' => "Disabled",
		'set_newpass' => "New Password",
		'set_newpass_descr' => "(leave blank if won't change current password)",
		'set_smartsyntax' => "Smart Syntax",
		'set_smartsyntax_descr' => "(/priority/ task /tags/)",
		'set_timezone' => "Time Zone",
		'set_autotag' => "Autotagging",
		'set_autotag_descr' => "(automatically adds tag of current tag filter to newly created task)",
		'set_sessions' => "Session Handling",
		'set_sessions_php' => "PHP",
		'set_sessions_files' => "Files",
		'set_firstdayofweek' => "First Day of Week",
		'set_custom' => "Custom",
		'set_date' => "Date Format",
		'set_date2' => "Short Date Format",
		'set_shortdate' => "Short Date (Current Year)",
		'set_clock' => "Clock Format",
		'set_12hour' => "12-hour",
		'set_24hour' => "24-hour",
		'set_submit' => "Submit Changes",
		'set_cancel' => "Cancel",
		'set_showdate' => "Show Task Date In List",
	);

	var $js = array();
	var $inc = array();

	function makeJS()
	{
		$a = array();
		foreach ($this->default_js as $k => $v) {
			if (isset($this->js[$k])) $v = $this->js[$k];

			if (is_array($v)) {
				$a[] = "$k: " . $v[0];
			} else {
				$a[] = "$k: \"" . str_replace('"', '\\"', $v) . "\"";
			}
		}
		$t = array();
		foreach ($this->get('days_min') as $v) {
			$t[] = '"' . str_replace('"', '\\"', $v) . '"';
		}
		$a[] = "daysMin: [" . implode(',', $t) . "]";
		$t = array();
		foreach ($this->get('days_long') as $v) {
			$t[] = '"' . str_replace('"', '\\"', $v) . '"';
		}
		$a[] = "daysLong: [" . implode(',', $t) . "]";
		$t = array();
		foreach ($this->get('months_long') as $v) {
			$t[] = '"' . str_replace('"', '\\"', $v) . '"';
		}
		$a[] = "monthsLong: [" . implode(',', $t) . "]";
		$a[] = $this->_2js('tags');
		$a[] = $this->_2js('tasks');
		$a[] = $this->_2js('f_past');
		$a[] = $this->_2js('f_today');
		$a[] = $this->_2js('f_soon');
		return "{\n" . implode(",\n", $a) . "\n}";
	}

	function _2js($v)
	{
		return "$v: \"" . str_replace('"', '\\"', $this->get($v)) . "\"";
	}

	function get($key)
	{
		if (isset($this->inc[$key])) return $this->inc[$key];
		if (isset($this->default_inc[$key])) return $this->default_inc[$key];
		return $key;
	}

	public static function instance()
	{
		if (!isset(self::$instance)) {
			//$c = __CLASS__;
			$c = 'Lang';
			self::$instance = new $c;
		}
		return self::$instance;
	}
}
