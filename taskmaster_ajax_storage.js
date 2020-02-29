/*

# Taskmaster

This file is part of the Taskmaster project. Taskmaster is a simple task, project, and information tracking application.

# Copyright

Copyright 2013-2015 David Rodgers - <https://github.com/caressofsteel/taskmaster>
Copyright 2012-2013 Alexander Reichardt - <https://github.com/alex-LE/yourTinyTodo>
Copyright 2009-2010 Max Pozdeev - <https://github.com/maxpozdeev/mytinytodo>

This project is distributed under the GNU General Public License. Please see the included COPYRIGHT and LICENSE-GPL3 for more information. Copyrights for portions of this file are retained by their owners.

*/ 

(function(){

var taskmaster;

function taskmasterStorageAjax(ataskmaster)
{
	this.taskmaster = taskmaster = ataskmaster;
}

window.taskmasterStorageAjax = taskmasterStorageAjax;

taskmasterStorageAjax.prototype =
{
	/* required method */
	request:function(action, params, callback)
	{
		if(!this[action]) throw "Unknown storage action: "+action;

		this[action](params, function(json){
			if(json.denied) taskmaster.errorDenied();
			if(callback) callback.call(taskmaster, json)
		});
	},


	loadLists: function(callback)
	{
		$.getJSON(this.taskmaster.taskmasterUrl+'ajax.php?loadLists&rnd='+Math.random(), callback);
	},


	loadTasks: function(params, callback)
	{
		var q = '';
		if(params.search && params.search != '') q += '&s='+encodeURIComponent(params.search);
		if(params.tag && params.tag != '') q += '&t='+encodeURIComponent(params.tag);
		if(params.setCompl && params.setCompl != 0) q += '&setCompl=1';
		if(params.notification != null && params.notification == 1) q += '&setNotification=1';
		if(params.notification != null && params.notification == 0) q += '&setNotification=0';
		q += '&rnd='+Math.random();

/*		$.getJSON(taskmaster.taskmasterUrl+'ajax.php?loadTasks&list='+params.list+'&compl='+params.compl+'&sort='+params.sort+'&tz='+params.tz+q, function(json){
			callback.call(taskmaster, json);
		})
*/

		$.getJSON(this.taskmaster.taskmasterUrl+'ajax.php?loadTasks&list='+params.list+'&compl='+params.compl+'&sort='+params.sort+q, callback);
	},


	newTask: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?newTask',
			{ list:params.list, title: params.title, tag:params.tag }, callback, 'json');
	},
	

	fullNewTask: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?fullNewTask',
			{ list:params.list, title:params.title, note:params.note, prio:params.prio, tags:params.tags, duedate:params.duedate, duedate_h:params.duedate_h, duedate_m:params.duedate_m, duration_h:params.duration_h, duration_m:params.duration_m },
			callback, 'json');
	},


	editTask: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?editTask='+params.id,
			{ id:params.id, title:params.title, note:params.note, prio:params.prio, tags:params.tags, duedate:params.duedate, duedate_h:params.duedate_h, duedate_m:params.duedate_m, duration_h:params.duration_h, duration_m:params.duration_m },
			callback, 'json');
	},


	editNote: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?editNote='+params.id, {id:params.id, note: params.note}, callback, 'json');
	},


	completeTask: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?completeTask='+params.id, { id:params.id, compl:params.compl }, callback, 'json');
	},


	deleteTask: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?deleteTask='+params.id, { id:params.id }, callback, 'json');
	},


	setPrio: function(params, callback)
	{
		$.getJSON(this.taskmaster.taskmasterUrl+'ajax.php?setPrio='+params.id+'&prio='+params.prio+'&rnd='+Math.random(), callback);
	},

	
	setSort: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?setSort', { list:params.list, sort:params.sort }, callback, 'json');
	},

	changeOrder: function(params, callback)
	{
		var order = '';
		for(var i in params.order) {
			order += params.order[i].id +'='+ params.order[i].diff + '&';
		}
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?changeOrder', { order:order }, callback, 'json');
	},

	tagCloud: function(params, callback)
	{
		$.getJSON(this.taskmaster.taskmasterUrl+'ajax.php?tagCloud&list='+params.list+'&rnd='+Math.random(), callback);
	},

	moveTask: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?moveTask', { id:params.id, from:params.from, to:params.to }, callback, 'json');
	},

	parseTaskStr: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?parseTaskStr', { list:params.list, title:params.title, tag:params.tag }, callback, 'json');
	},
	

	// Lists
	addList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?addList', { name:params.name }, callback, 'json'); 

	},

	renameList:  function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?renameList', { list:params.list, name:params.name }, callback, 'json');
	},

	deleteList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?deleteList', { list:params.list }, callback, 'json');
	},

	publishList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?publishList', { list:params.list, publish:params.publish },  callback, 'json');
	},

	archiveList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?archiveList', { list:params.list, archive:params.archive },  callback, 'json');
	},

	privateList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?privateList', { list:params.list, private:params.private },  callback, 'json');
	},
	
	setShowNotesInList: function(params, callback)
	{
	    $.post(this.taskmaster.taskmasterUrl+'ajax.php?setShowNotesInList', { list:params.list, shownotes:params.shownotes },  callback, 'json');
	},
	
	setHideList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?setHideList', { list:params.list, hide:params.hide }, callback, 'json');
	},

	changeListOrder: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?changeListOrder', { order:params.order }, callback, 'json');
	},

	clearCompletedInList: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?clearCompletedInList', { list:params.list }, callback, 'json');
	},

	createUser: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?createuser', { taskmasterusername:params.username, taskmasterpassword:params.password, taskmasteremail:params.email, taskmasterrole:params.role }, callback, 'json');
	},

	editUser: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?edituser', { taskmasteruserid:params.userid, taskmasterusername:params.username, taskmasterpassword:params.password, taskmasteremail:params.email, taskmasterrole:params.role, taskmasternotification:params.notification }, callback, 'json');
	},

	deleteUser: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?deleteuser', { taskmasteruserid:params.userid }, callback, 'json');
	},

    markread: function(params, callback)
	{
		$.post(this.taskmaster.taskmasterUrl+'ajax.php?markread', { taskmasternotificationid:params.taskmasternotificationid }, callback, 'json');
	},

    markallasread: function(params, callback)
	{
        $.getJSON(this.taskmaster.taskmasterUrl+'ajax.php?markallasread&rnd='+Math.random(), callback);
	},

    countNotifications: function(params, callback)
    {
        $.getJSON(this.taskmaster.taskmasterUrl+'ajax.php?countNotifications&rnd='+Math.random(), callback);
    },

    trackWorkTime: function(params, callback)
    {
        $.post(this.taskmaster.taskmasterUrl+'ajax.php?trackWorkTime', { taskmaster_taskId:params.task_id, taskmaster_time:params.time, taskmaster_date:params.date }, callback, 'json');
    },

    addComment: function(params, callback)
    {
        $.post(this.taskmaster.taskmasterUrl+'ajax.php?addComment', { taskmaster_taskId:params.task_id, taskmaster_comment:params.comment }, callback, 'json');
    }
};

})();