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

header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
	<title><?php taskmasterinfo('title'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="<?php taskmasterinfo('template_url'); ?>style.css" media="all" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php taskmasterinfo('template_url'); ?>favicon.ico" />
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>jquery/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>jquery/jquery-ui-1.8.18.custom.min.js"></script>
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>jquery/jquery.autocomplete-1.1.js"></script>
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>jquery/jquery.cookie.js?v=<?= TASKMASTER_VERSION ?>"></script>
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>taskmaster.js?v=<?= TASKMASTER_VERSION ?>"></script>
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>taskmaster_lang.php?v=<?= TASKMASTER_VERSION ?>"></script>
	<script type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>taskmaster_ajax_storage.js?v=<?= TASKMASTER_VERSION ?>"></script>
	<script defer type="text/javascript" src="<?php taskmasterinfo('taskmaster_url'); ?>jquery/highcharts.js"></script>
	<?php require_once(TASKMASTERPATH . 'taskmaster_theme_init_js.php'); ?>
</head>

<body>

	<!--[if lt IE 7]>
	<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

	<div id="wrapper">
		<div id="container">
			<div id="taskmaster_body">

				<div id="function-bar">
					<h2 class="title"><?php taskmasterinfo('title'); ?></h2>

					<div id="bar">
						<div id="msg">
							<span class="msg-text"></span>
							<div class="msg-details"></div>
						</div>
						<div class="bar-menu">
							<span class="menu-owner menuitem" style="display:none;position: relative;">
								<a href="#notifications" id="notifications" style="display:none"><?php _e('a_notifications'); ?><span id="notification_counter">
										<span class="notification_counter-left"></span>
										<span class="notification_counter-value"></span>
										<span class="notification_counter-right"></span>
									</span>
								</a>
							</span>
							<span class="bar-delim" style="display:none"></span>
							<span class="menu-owner menuitem" style="display:none">
								<a href="#settings" id="settings"><?php _e('a_settings'); ?></a>
							</span>
							<span class="bar-delim" style="display:none"></span>
							<span id="bar_auth">
								<span id="bar_public" style="display:none"><?php _e('public_tasks'); ?></span>
								<a href="#login" id="bar_login" class="nodecor menuitem"><u><?php _e('a_login'); ?></u></a>
								<a href="#logout" id="bar_logout" class="menuitem"><?php _e('a_logout'); ?></a>
							</span>
						</div>
					</div>
				</div>

				<div id="main">

					<div id="header">
						<div id="taskmaster-work-timer">
							<span id="taskmaster-time">00:00:00</span>
							<a href="#" id="taskmaster-timer-pause" title="<?= _e('timer_pause') ?>"></a>
							<a href="#" id="taskmaster-timer-stop" title="<?= _e('timer_stop') ?>"></a>
							<a href="#" id="taskmaster-timer-finish" title="<?= _e('timer_finish') ?>"></a>
							<a href="#" id="taskmaster-timer-continue" title="<?= _e('timer_continue') ?>"></a>
						</div>
					</div>

					<div id="page_tasks" style="display:none">

						<div id="lists">
							<ul class="taskmaster-tabs"></ul>
							<div class="taskmaster-tabs-add-button" title="<?php _e('list_new'); ?>"><span></span></div>
							<div id="tabs_buttons">
								<div class="taskmaster-tabs-select-button taskmaster-tabs-button" title="<?php _e('list_select'); ?>"><span></span></div>
							</div>
							<div id="list_all" class="taskmaster-tab taskmaster-tabs-alltasks taskmaster-tabs-hidden"><a href="#alltasks"><span><?php _e('alltasks'); ?></span></a>
								<div class="list-action"></div>
							</div>
						</div>



						<div id="toolbar" class="taskmaster-htabs">

							<div id="htab_search">
								<table class="taskmaster-searchbox">
									<tr>
										<td>
											<div class="taskmaster-searchbox-c">
												<input type="text" name="search" value="" maxlength="250" id="search" autocomplete="off" />
												<div class="taskmaster-searchbox-icon taskmaster-icon-search"></div>
												<div id="search_close" class="taskmaster-searchbox-icon taskmaster-icon-cancelsearch"></div>
											</div>
										</td>
									</tr>
								</table>
							</div>

							<div id="htab_newtask">
								<table class="taskmaster-taskbox">
									<tr>
										<td class="taskmaster-tb-cell">
											<div class="taskmaster-tb-c">
												<form id="newtask_form" method="post" action="">
													<label id="task_placeholder" class="placeholding" for="task">
														<input type="text" name="task" value="" maxlength="250" id="task" autocomplete="off" />
														<span><?php _e('htab_newtask'); ?></span>
													</label>
													<div id="newtask_submit" class="taskmaster-taskbox-icon taskmaster-icon-submittask" title="<?php _e('btn_add'); ?>"></div>
												</form>
											</div>
										</td>
										<td><a href="#" id="newtask_adv" class="taskmaster-img-button" title="<?php _e('advanced_add'); ?>"><span></span></a></td>
									</tr>
								</table>
							</div>

							<div id="searchbar" style="display:none"><?php _e('searching'); ?> <span id="searchbarkeyword"></span></div>

							<div style="clear:both"></div>

						</div>


						<h3>
							<span id="taskview" class="taskmaster-menu-button"><span class="btnstr"><?php _e('tasks'); ?></span> (<span id="total">0</span>) <span class="arrdown"></span></span>
							<span class="taskmaster-notes-showhide"><?php _e('notes'); ?> <a href="#" id="taskmaster-notes-show"><?php _e('notes_show'); ?></a> / <a href="#" id="taskmaster-notes-hide"><?php _e('notes_hide'); ?></a></span>
							<span id="taskmaster_filters"></span>
							<span id="tagcloudbtn" class="taskmaster-menu-button"><?php _e('tagcloud'); ?> <span class="arrdown2"></span></span>
						</h3>

						<div id="taskcontainer">
							<ol id="tasklist" class="sortable"></ol>
							<div id="taskajax"></div>
						</div>

					</div> <!-- end of page_tasks -->


					<div id="page_taskedit" style="display:none">

						<div><a href="#" class="taskmaster-back-button"><?php _e('go_back'); ?></a></div>

						<h3 class="taskmaster-inadd"><?php _e('add_task'); ?></h3>
						<h3 class="taskmaster-inedit"><?php _e('edit_task'); ?>
							<span id="taskedit-date" class="taskmaster-inedit">
								(<span class="date-created" title="<?php _e('taskdate_created'); ?>"><span>&nbsp;</span></span><span class="date-completed" title="<?php _e('taskdate_completed'); ?>"> &mdash; <span>&nbsp;</span></span>)
							</span>
						</h3>

						<form id="taskedit_form" name="edittask" method="post" action="">
							<input type="hidden" name="isadd" value="0" />
							<input type="hidden" name="id" value="" />
							<div class="form-row form-row-short">
								<span class="h"><?php _e('priority'); ?></span>
								<select name="prio">
									<option value="10">Milestone</option>
									<option value="9">Note</option>
									<option value="8">Review</option>
									<option value="7">Work</option>
									<option value="6">Bug</option>
									<option value="5">+5</option>
									<option value="4">+4</option>
									<option value="3">+3</option>
									<option value="2">+2</option>
									<option value="1">+1</option>
									<option value="0" selected="selected">&plusmn;0</option>
									<option value="-1">Locked</option>
								</select>
							</div>
							<div class="form-row form-row-short">
								<span class="h"><?php _e('due'); ?> </span>
								<input name="duedate" id="duedate" value="" class="in100" title="Y-M-D, M/D/Y, D.M.Y, M/D, D.M" autocomplete="off" />
								<select name="duedate_h" id="duedate_h">
									<option value="0">0</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
								</select>
								&nbsp;<?php _e('hour_sign'); ?>
								<select name="duedate_m" id="duedate_m">
									<option value="0">0</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
									<option value="32">32</option>
									<option value="33">33</option>
									<option value="34">34</option>
									<option value="35">35</option>
									<option value="36">36</option>
									<option value="37">37</option>
									<option value="38">38</option>
									<option value="39">39</option>
									<option value="40">40</option>
									<option value="41">41</option>
									<option value="42">42</option>
									<option value="43">43</option>
									<option value="44">44</option>
									<option value="45">45</option>
									<option value="46">46</option>
									<option value="47">47</option>
									<option value="48">48</option>
									<option value="49">49</option>
									<option value="50">50</option>
									<option value="51">51</option>
									<option value="52">52</option>
									<option value="53">53</option>
									<option value="54">54</option>
									<option value="55">55</option>
									<option value="56">56</option>
									<option value="57">57</option>
									<option value="58">58</option>
									<option value="59">59</option>
								</select>
								&nbsp;<?php _e('minute_sign'); ?>
							</div>
							<div class="form-row form-row-short">
								<span class="h"><?php _e('duration'); ?> </span>
								<input name="duration_h" id="duration_h" value="" class="in35 textright" title="" autocomplete="off" />&nbsp;<?php _e('hour_sign'); ?>
								<input name="duration_m" id="duration_m" value="" class="in35 textright" title="" autocomplete="off" />&nbsp;<?php _e('minute_sign'); ?>
							</div>
							<div class="form-row-short-end"></div>
							<div class="form-row">
								<div class="h"><?php _e('task'); ?></div> <input type="text" name="task" value="" class="in500" maxlength="250" />
							</div>
							<div class="form-row">
								<div class="h"><?php _e('note'); ?></div> <textarea name="note" class="in500 tasknote"></textarea>
							</div>
							<div class="form-row">
								<div class="h"><?php _e('tags'); ?></div>
								<table cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td><input type="text" name="tags" id="edittags" value="" class="in500" maxlength="250" /></td>
										<td class="alltags-cell">
											<a href="#" id="alltags_show"><?php _e('alltags_show'); ?></a>
											<a href="#" id="alltags_hide" style="display:none"><?php _e('alltags_hide'); ?></a></td>
									</tr>
								</table>
							</div>
							<div class="form-row" id="alltags" style="display:none;"><?php _e('alltags'); ?> <span class="tags-list">&nbsp;</span></div>
							<div class="form-row form-bottom-buttons">
								<input type="submit" value="<?php _e('save'); ?>" />
								<input type="button" id="taskmaster_edit_cancel" class="taskmaster-back-button" value="<?php _e('cancel'); ?>" />
							</div>
						</form>

					</div> <!-- end of page_taskedit -->

					<div id="priopopup" style="display:none">
						<span class="prio-neg prio-neg-1"><i style="margin-left: 4px; margin-right: 4px;" class="fa fa-lock" /></i></span>
						<span class="prio-zero">&plusmn;0</span>
						<span class="prio-pos prio-pos-1">+1</span>
						<span class="prio-pos prio-pos-2">+2</span>
						<span class="prio-pos prio-pos-3">+3</span>
						<span class="prio-pos prio-pos-4">+4</span>
						<span class="prio-pos prio-pos-5">+5</span>
						<span class="prio-bug"><i style="margin-left: 4px; margin-right: 4px;" class="fa fa-bug" /></i></span>
						<span class="prio-work"><i style="margin-left: 6px; margin-right: 7px;" class="fa fa-gear" /></i></span>
						<span class="prio-review"><i style="margin-left: 4px; margin-right: 4px;" class="fa fa-flag-checkered" /></i></span>
						<span class="prio-note"><i style="margin-left: 4px; margin-right: 4px;" class="fa fa-info-circle" /></i></span>
						<span class="prio-mile"><i style="margin-left: 4px; margin-right: 4px;" class="fa fa-trophy" /></i></span>
					</div>

					<div id="page_ajax" style="display:none"></div>
				</div>

			</div>
			<div id="space"></div>
		</div>

		<div id="taskmaster-menu-modal" class="taskmaster-menu-modal"></div>

		<div id="footer">
			<div id="footer_content">
				Powered by <strong><a href="#">Taskmaster</a></strong> <?= TASKMASTER_VERSION ?>
				<div id="loggedinuser"></div>
			</div>
		</div>

	</div> <!-- end of main -->

	<div id="cmenupriocontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
			<li id="cmenu_prio:10">
				<div class="menu-icon"></div>M
			</li>
			<li id="cmenu_prio:9">
				<div class="menu-icon"></div>N
			</li>
			<li id="cmenu_prio:8">
				<div class="menu-icon"></div>R
			</li>
			<li id="cmenu_prio:7">
				<div class="menu-icon"></div>W
			</li>
			<li id="cmenu_prio:6">
				<div class="menu-icon"></div>Bug
			</li>
			<li id="cmenu_prio:5">
				<div class="menu-icon"></div>+5
			</li>
			<li id="cmenu_prio:4">
				<div class="menu-icon"></div>+4
			</li>
			<li id="cmenu_prio:3">
				<div class="menu-icon"></div>+3
			</li>
			<li id="cmenu_prio:2">
				<div class="menu-icon"></div>+2
			</li>
			<li id="cmenu_prio:1">
				<div class="menu-icon"></div>+1
			</li>
			<li id="cmenu_prio:0">
				<div class="menu-icon"></div>&plusmn;0
			</li>
			<li id="cmenu_prio:-1">
				<div class="menu-icon"></div>&minus;1
			</li>

		</ul>
	</div>

	<div id="cmenulistscontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
		</ul>
	</div>

	<div id="slmenucontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
			<li id="slmenu_list:-1" class="list-id--1 taskmaster-need-list" <?php if (is_readonly()) echo 'style="display:none"' ?>>
				<div class="menu-icon"></div><a href="#alltasks"><?php _e('alltasks'); ?></a>
			</li>
			<li class="taskmaster-menu-delimiter"></li>
			<li id="slmenu_list:-2" class="list-id--2" <?php if (is_readonly()) echo 'style="display:none"' ?>>
				<div class="menu-icon"></div><a href="#archivelists"><?php _e('archivelists'); ?></a>
			</li>
			<li class="taskmaster-menu-delimiter slmenu-lists-begin taskmaster-need-list" <?php if (is_readonly()) echo 'style="display:none"' ?>></li>
		</ul>
	</div>

	<div id="taskmaster-createuser" style="display:none" class="taskmaster-menu-container">
		<form method="post" action="" name="createuserForm">
			<label for="um_username"><?php _e('um_username'); ?></label>
			<input type="text" name="um_username" id="um_username" value="" />

			<label for="um_email"><?php _e('um_email'); ?></label>
			<input type="text" name="um_email" id="um_email" value="" />

			<label for="um_password"><?php _e('um_password'); ?></label>
			<input type="password" name="um_password" id="um_password" value="" />

			<label for="um_notification"><?php _e('um_notification'); ?></label>
			<input type="checkbox" name="um_notification" id="um_notification" value="1" />

			<label for="um_role" class="taskmaster-clear"><?php _e('um_role'); ?></label>
			<select name="um_role" id="um_role">
				<option value="1"><?php _e('um_rolename_1') ?></option>
				<option value="2"><?php _e('um_rolename_2') ?></option>
				<option value="3"><?php _e('um_rolename_3') ?></option>
			</select>

			<input type="hidden" value="" id="um_userid" name="um_userid" />

			<input type="button" id="createuserSubmit" value="<?php _e('save') ?>" />
		</form>
	</div>

	<div id="taskviewcontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
			<li id="view_tasks"><?php _e('tasks'); ?> (<span id="cnt_total">0</span>)</li>
			<li id="view_past"><?php _e('f_past'); ?> (<span id="cnt_past">0</span>)</li>
			<li id="view_today"><?php _e('f_today'); ?> (<span id="cnt_today">0</span>)</li>
			<li id="view_soon"><?php _e('f_soon'); ?> (<span id="cnt_soon">0</span>)</li>
		</ul>
	</div>

	<div id="tagcloud" style="display:none">
		<a id="tagcloudcancel" class="taskmaster-img-button"><span>&nbsp;</span></a>
		<div id="tagcloudload"></div>
		<div id="tagcloudcontent"></div>
	</div>

	<div id="listexportmenucontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnExportCSV"><?php _e('list_export_csv'); ?></li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnExportICAL"><?php _e('list_export_ical'); ?></li>
		</ul>
	</div>

	<div id="authform" style="display:none">
		<form id="login_form" action="" method="post">
			<!-- if multiuser is enabled -->
			<div class="login_multiuser"><label for="username"><?php _e('um_username'); ?></label><input type="text" name="username" id="username" /></div>
			<div class="login_multiuser"><label for="password"><?php _e('um_password'); ?></label><input type="password" name="password" id="password" /></div>
			<div class="login_multiuser"><input type="submit" value="<?php _e('btn_login'); ?>" /></div>

			<!-- if singleuser is enabled -->
			<div class="h login_singleuser"><?php _e('password'); ?></div>
			<div class="login_singleuser"><input type="password" name="password" id="password" /></div>
			<div class="login_singleuser"><input type="submit" value="<?php _e('btn_login'); ?>" /></div>

		</form>
	</div>

	<div id="taskcontextcontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
			<li id="cmenu_edit"><b><?php _e('action_edit'); ?></b></li>
			<li id="cmenu_note"><?php _e('action_note'); ?></li>
			<li id="cmenu_prio" class="taskmaster-menu-indicator" submenu="cmenupriocontainer">
				<div class="submenu-icon"></div><?php _e('action_priority'); ?>
			</li>
			<li id="cmenu_move" class="taskmaster-menu-indicator" submenu="cmenulistscontainer">
				<div class="submenu-icon"></div><?php _e('action_move'); ?>
			</li>
			<li id="cmenu_delete"><?php _e('action_delete'); ?></li>
		</ul>
	</div>

	<div id="listmenucontainer" class="taskmaster-menu-container" style="display:none">
		<ul>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnRenameList"><?php _e('list_rename'); ?></li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnDeleteList"><?php _e('list_delete'); ?></li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnClearCompleted"><?php _e('list_clearcompleted'); ?></li>
			<li class="taskmaster-need-list taskmaster-need-real-list taskmaster-menu-indicator" id="btnExport" submenu="listexportmenucontainer">
				<div class="submenu-icon"></div><?php _e('list_export'); ?>
			</li>
			<li class="taskmaster-menu-delimiter taskmaster-need-real-list"></li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnTimeTable"><?php _e('list_timetable'); ?></li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnPublish">
				<div class="menu-icon"></div><?php _e('list_publish'); ?>
			</li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnArchive">
				<div class="menu-icon"></div><?php _e('list_archive'); ?>
			</li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnPrivate">
				<div class="menu-icon"></div><?php _e('list_private'); ?>
			</li>
			<li class="taskmaster-need-list taskmaster-need-real-list" id="btnRssFeed">
				<div class="menu-icon"></div><?php _e('list_rssfeed'); ?>
			</li>
			<li class="taskmaster-menu-delimiter taskmaster-need-real-list"></li>
			<li class="taskmaster-need-list taskmaster-need-real-list sort-item" id="sortByHand">
				<div class="menu-icon"></div><?php _e('sortByHand'); ?> <span class="taskmaster-sort-direction">&nbsp;</span>
			</li>
			<li class="taskmaster-need-list sort-item" id="sortByDateCreated">
				<div class="menu-icon"></div><?php _e('sortByDateCreated'); ?> <span class="taskmaster-sort-direction">&nbsp;</span>
			</li>
			<li class="taskmaster-need-list sort-item" id="sortByPrio">
				<div class="menu-icon"></div><?php _e('sortByPriority'); ?> <span class="taskmaster-sort-direction">&nbsp;</span>
			</li>
			<li class="taskmaster-need-list sort-item" id="sortByDueDate">
				<div class="menu-icon"></div><?php _e('sortByDueDate'); ?> <span class="taskmaster-sort-direction">&nbsp;</span>
			</li>
			<li class="taskmaster-need-list sort-item" id="sortByDateModified">
				<div class="menu-icon"></div><?php _e('sortByDateModified'); ?> <span class="taskmaster-sort-direction">&nbsp;</span>
			</li>
			<li class="taskmaster-menu-delimiter"></li>
			<li class="taskmaster-need-list" id="btnShowCompleted">
				<div class="menu-icon"></div><?php _e('list_showcompleted'); ?>
			</li>
			<li class="taskmaster-menu-delimiter"></li>
			<li class="taskmaster-need-list" id="btnNotifications">
				<div class="menu-icon"></div><?php _e('list_notifications'); ?>
			</li>
		</ul>
	</div>

</body>

</html>