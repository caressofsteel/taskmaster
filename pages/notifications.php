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

require_once('../init.php');

$lang = Lang::instance();
$db = DBConnection::instance();

if (($needAuth && !is_logged())) {
	echo _e('access_denied');
	exit();
}

function getNotificationListFromDB()
{
	global $db;
	$current_user_id = (int) $_SESSION['userid'];
	if ($current_user_id > 0) {
		$q = $db->dq("SELECT * FROM {$db->prefix}notifications WHERE shown = 0 AND user_id = " . $current_user_id . " ORDER BY created DESC");
		while ($r = $q->fetch_assoc()) {
			if ((int) $r['creator_user_id'] > 0) {
				$user_name = $db->sq("SELECT username FROM {$db->prefix}users WHERE id=" . $r['creator_user_id']);
			} else {
				$user_name = '';
			}
?>
			<tr class="notification_row" id="notification_row_<?= $r['id'] ?>">
				<td valign="left" class="col_created"><?php echo timestampToDatetime(strtotime($r['created'])) ?></td>
				<td valign="left" class="col_user"><?php echo $user_name ?></td>
				<td valign="left" class="col_desc"><?php echo $r['text'] ?></td>
				<td>
					<a href="#markread" rel="<?= $r['id'] ?>" class="markread"></a>
				</td>
			</tr>
<?php
		}
	}
}



header('Content-type:text/html; charset=utf-8');
?>
<div><a href="#" class="taskmaster-back-button"><?php _e('go_back'); ?></a></div>

<h3><?php _e('n_header'); ?></h3>

<table class="taskmaster-notification-table">
	<tr>
		<th><?php _e('n_created'); ?></th>
		<th><?php _e('n_user'); ?></th>
		<th><?php _e('n_description'); ?></th>
		<th><button id="markallasread"><?= _e('n_mark_all_read') ?></button></th>
	</tr>
	<?php getNotificationListFromDB(); ?>
</table>