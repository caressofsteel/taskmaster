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

if (Config::get('markdown')) { ?>

	<script src="showdown.js"></script>
	<script type="text/javascript" src="markitup/jquery.markitup.js"></script>
	<script type="text/javascript" src="markitup/sets/markdown/set.js"></script>
	<link rel="stylesheet" type="text/css" href="markitup/skins/markitup/style.css" />
	<link rel="stylesheet" type="text/css" href="markitup/sets/markdown/style.css" />
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

<?php } ?>

<script type="text/javascript">
	$().ready(function() {

		taskmaster.taskmasterUrl = "<?php taskmasterinfo('taskmaster_url'); ?>";
		taskmaster.templateUrl = "<?php taskmasterinfo('template_url'); ?>";
		taskmaster.db = new taskmasterStorageAjax(taskmaster);
		taskmaster.init({
			needAuth: <?php echo $needAuth ? "true" : "false"; ?>,
			multiUser: <?php echo $multiUser ? "true" : "false"; ?>,
			admin: <?php echo is_admin() ? "true" : "false"; ?>,
			readOnly: <?php echo is_readonly() ? "true" : "false"; ?>,
			<?php if (isset($_SESSION['userid'])) { ?>
				globalNotifications: <?php echo (NotificationListener::hasGlobalNotifications($_SESSION['userid'])) ? "true" : "false"; ?>,
				userId: <?php echo (!empty($_SESSION['userid'])) ? $_SESSION['userid'] : 'null'; ?>,
				userRole: <?php echo (!empty($_SESSION['role'])) ? $_SESSION['role'] : 'null'; ?>,
				userName: '<?php echo (isset($_SESSION['userid'])) ? getUserName($_SESSION['userid']) : ''; ?>',
			<?php } ?>
			isLogged: <?php echo ($needAuth && is_logged()) ? "true" : "false"; ?>,
			showdate: <?php echo (Config::get('showdate') && !isset($_GET['pda'])) ? "true" : "false"; ?>,
			singletab: <?php echo (isset($_GET['singletab']) || isset($_GET['pda'])) ? "true" : "false"; ?>,
			duedatepickerformat: "<?php echo htmlspecialchars(Config::get('dateformat2')); ?>",
			dateformatshort: "<?php echo htmlspecialchars(Config::get('dateformatshort')); ?>",
			firstdayofweek: <?php echo (int) Config::get('firstdayofweek'); ?>,
			autotag: <?php echo Config::get('autotag') ? "true" : "false"; ?>,
			authbypass: <?php echo Config::get('auth_bypass') == 'none' ? "false" : "true"; ?>,
			debugmode: <?php echo Config::get('debugmode') ? "true" : "false"; ?>,
			markdown: <?php echo Config::get('markdown') ? "true" : "false"; ?>,
			notification_count: <?php echo ($notifications_count === false) ? "false" : $notifications_count ?>,
			show_edit_options: <?php echo (!isset($_SESSION['role']) || $_SESSION['role'] < 3) ? 'true' : 'false'; ?>
			<?php if (isset($_GET['list'])) echo ",openList: " . (int) $_GET['list']; ?>
			<?php if (isset($_GET['pda'])) echo ", touchDevice: true"; ?>
		}).loadLists(1);

	});
</script>