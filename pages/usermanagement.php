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

if (($needAuth && !is_logged()) || !is_admin()) {
	echo _e('access_denied');
	exit();
}

function getUserListFromDB()
{
	global $db;
	$q = $db->dq("SELECT * FROM {$db->prefix}users");
	while ($r = $q->fetch_assoc($q)) {
?>
		<tr>
			<td valign="left" class="username"><?php echo $r['username'] ?></td>
			<td valign="left" class="email"><?php echo $r['email'] ?></td>
			<td valign="left" class="role"><?php echo _e('um_rolename_' . $r['role']) ?></td>
			<td valign="left" class="notification"><?php echo (NotificationListener::hasGlobalNotifications($r['id'])) ? _e('um_notification_on') : _e('um_notification_off') ?></td>
			<td>
				<a href="#" class="edituser" rel="<?php echo $r['id'] ?>"><?php _e('action_edit') ?></a>
				<?php if (isset($_SESSION['userid']) && $r['id'] != $_SESSION['userid']) { ?>
					&nbsp;|&nbsp;
					<a href="#" class="deleteuser" rel="<?php echo $r['id'] ?>"><?php _e('action_delete') ?></a>
				<?php } ?>
			</td>
		</tr>
<?php
	}
}



header('Content-type:text/html; charset=utf-8');
?>
<div><a href="#" class="taskmaster-back-button"><?php _e('go_back'); ?></a></div>

<h3><?php _e('um_header'); ?></h3>

<div id="usermanagement_msg" style="display:none"></div>

<a href="#" class="createuserBtn" id="createuserBtn"><?php _e('um_createuser'); ?></a>

<table class="taskmaster-usermanagement-table">
	<tr>
		<th><?php _e('um_username'); ?></th>
		<th><?php _e('um_email'); ?></th>
		<th><?php _e('um_role'); ?></th>
		<th><?php _e('um_notification'); ?></th>
		<th></th>
	</tr>
	<?php getUserListFromDB(); ?>
</table>