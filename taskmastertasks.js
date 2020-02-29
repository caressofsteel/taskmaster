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

var taskList = new Array(), taskOrder = new Array();
var filter = { compl:0, search:'', due:'' };
var sortOrder; //save task order before dragging
var searchTimer;
var objPrio = {};
var selTask = 0;
var flag = { needAuth:false, isLogged:false, tagsChanged:true, readOnly:false, editFormChanged:false, multiUser:false, userRole:0, userId:0, admin:false, globalNotifications:false, authbypass:false, debugmode:false, notification_count:0, show_edit_options:false, markdown:false };
var taskCnt = { total:0, past: 0, today:0, soon:0 };
var tabLists = {
	_lists: {},
	_length: 0,
	_order: [],
	_alltasks: {},
	clear: function(){
		this._lists = {}; this._length = 0; this._order = [];
		this._alltasks = { id:-1, showCompl:0, sort:3 }; 
	},
	length: function(){ return this._length; },
	exists: function(id){ if(this._lists[id] || id==-1) return true; else return false; },
	add: function(list){ this._lists[list.id] = list; this._length++; this._order.push(list.id); },
	replace: function(list){ this._lists[list.id] = list; },
	get: function(id){ if(id==-1) return this._alltasks; else return this._lists[id]; },
	getAll: function(){ var r = []; for(var i in this._order) { r.push(this._lists[this._order[i]]); }; return r; },
	reorder: function(order){ this._order = order; }
};
var curList = 0;
var tagsList = [];

var worktimer = {};

var taskmastertasks = window.taskmastertasks = _taskmaster = {

	theme: {
		newTaskFlashColor: '#ffffaa',
		editTaskFlashColor: '#bbffaa',
		msgFlashColor: '#ffffff'
	},

	actions: {},
	menus: {},
	taskmasterUrl: '',
	templateUrl: '',
	options: {
		openList: 0,
		singletab: false,
		autotag: false,
		tagPreview: true,
		tagPreviewDelay: 700, //milliseconds
		saveShowNotes: false,
		firstdayofweek: 1,
		touchDevice: false
	},

	show_archived_lists: 0,

	timers: {
		previewtag: 0
	},

	lang: {
		__lang: null,

		daysMin: [],
		daysLong: [],
		monthsShort: [],
		monthsLong: [],

		get: function(v) {
			if(this.__lang[v]) return this.__lang[v];
			else return v;
		},
		
		init: function(lang)
		{
			this.__lang = lang;
			this.daysMin = this.__lang.daysMin;
			this.daysLong = this.__lang.daysLong;
			this.monthsShort = this.__lang.monthsMin;
			this.monthsLong = this.__lang.monthsLong;
		}
	},

	pages: { 
		current: { page:'tasks', pageClass:'' },
		prev: []
	},

	// procs
	init: function(options)
	{
		jQuery.extend(this.options, options);
        flag.needAuth = options.needAuth ? true : false;
		flag.isLogged = options.isLogged ? true : false;
		flag.multiUser = options.multiUser ? true : false;
		flag.globalNotifications = options.globalNotifications ? true : false;
		flag.readOnly = options.readOnly ? true : false;
		flag.admin = options.admin ? true : false;
		flag.authbypass = options.authbypass ? true : false;
		flag.userId = options.userId;
		flag.userRole = options.userRole;
		flag.debugmode = options.debugmode;
        flag.markdown = options.markdown;
		flag.notification_count = options.notification_count;
		flag.show_edit_options = options.show_edit_options;

		if(this.options.showdate) $('#page_tasks').addClass('show-inline-date');
		if(this.options.singletab) $('#lists .taskmaster-tabs').addClass('taskmaster-tabs-only-one');

        if(flag.markdown) {
            $('.tasknote').removeClass('in500').markItUp(mySettings);
        }

        if(!flag.multiUser) {
			$('#btnNotifications').hide();
			$('#loggedinuser').hide();
			$('.login_multiuser').remove();
		} else {
			$('#loggedinuser').html(this.lang.get('loggedin_as') + ' ' + this.options.userName);
			$('.login_singleuser').remove();
		}

		this.parseAnchor();

		// handlers
		$('.taskmaster-tabs-add-button').click(function(){
			addList();
		});

		$('.taskmaster-tabs-select-button').click(function(event){
			if(event.metaKey || event.ctrlKey) {
				// toggle singetab interface
				_taskmaster.applySingletab(!_taskmaster.options.singletab);
				return false;
			}
			if(!_taskmaster.menus.selectlist) _taskmaster.menus.selectlist = new taskmasterMenu('slmenucontainer', {onclick:slmenuSelect});
			_taskmaster.menus.selectlist.show(this);
		});


		$('#newtask_form').submit(function(){
			submitNewTask(this);
			return false;
		});
		
		$('#newtask_submit').click(function(){
			$('#newtask_form').submit();
		});

		$('#newtask_adv').click(function(){
			showEditForm(1);
			return false;
		});
		
		$('#task').keydown(function(event){
			if(event.keyCode == 27) {
				$(this).val('');
			}
		}).focusin(function(){
			$('#task_placeholder').removeClass('placeholding');
			$('#toolbar').addClass('taskmaster-intask');
		}).focusout(function(){
			if('' == $(this).val()) $('#task_placeholder').addClass('placeholding');
			$('#toolbar').removeClass('taskmaster-intask');
		});


		$('#search_form').submit(function(){
			searchTasks(1);
			return false;
		});

		$('#search_close').click(function(){
			liveSearchToggle(0);
			return false;
		});

		$('#search').keyup(function(event){
			if(event.keyCode == 27) return;
			if($(this).val() == '') $('#search_close').hide();	//actual value is only on keyup
			else $('#search_close').show();
			clearTimeout(searchTimer);
			searchTimer = setTimeout(function(){searchTasks()}, 400);
		})
		.keydown(function(event){
			if(event.keyCode == 27) { // cancel on Esc (NB: no esc event on keypress in Chrome and on keyup in Opera)
				if($(this).val() != '') {
					$(this).val('');
					$('#search_close').hide();
					searchTasks();
				}
				else {
					liveSearchToggle(0);
				}
				return false; //need to return false in firefox (for AJAX?)
			}		
		}).focusin(function(){
			$('#toolbar').addClass('taskmaster-insearch');
		}).focusout(function(){
			$('#toolbar').removeClass('taskmaster-insearch');
		});


		$('#taskview').click(function(){
			if(!_taskmaster.menus.taskview) _taskmaster.menus.taskview = new taskmasterMenu('taskviewcontainer');
			_taskmaster.menus.taskview.show(this);
		});

		$('#taskmaster_filters .tag-filter .taskmaster-filter-close').live('click', function(){
			cancelTagFilter($(this).attr('tagid'));
		});

		$('#tagcloudbtn').click(function(){
            if(!_taskmaster.menus.tagcloud) _taskmaster.menus.tagcloud = new taskmasterMenu('tagcloud', {
				beforeShow: function(){
                    if(flag.tagsChanged) {
						$('#tagcloudcontent').html('');
						$('#tagcloudload').show();
						loadTags(curList.id, function(){$('#tagcloudload').hide();});
					}
				}, adjustWidth:true
            });
			_taskmaster.menus.tagcloud.show(this);
		});

		$('#tagcloudcancel').click(function(){
			if(_taskmaster.menus.tagcloud) _taskmaster.menus.tagcloud.close();
		});

		$('#tagcloudcontent .tag').live('click', function(){
			addFilterTag($(this).attr('tag'), $(this).attr('tagid'));
			if(_taskmaster.menus.tagcloud) _taskmaster.menus.tagcloud.close();
			return false;
		});	

		$('#taskmaster-notes-show').click(function(){
			toggleAllNotes(1);
			this.blur();
			return false;
		});

		$('#taskmaster-notes-hide').click(function(){
			toggleAllNotes(0);
			this.blur();
			return false;
		});

		$('#taskviewcontainer li').click(function(){
			if(this.id == 'view_tasks') setTaskview(0);
			else if(this.id == 'view_past') setTaskview('past');
			else if(this.id == 'view_today') setTaskview('today');
			else if(this.id == 'view_soon') setTaskview('soon');
		});

		
		// Tabs
		$('#lists li.taskmaster-tab').live('click', function(event){
			if(event.metaKey || event.ctrlKey) {
				// hide the tab
				hideTab(this);
				return false;
			}
			tabSelect(this);
			return false;
		});
		
		$('#list_all').click(function(event){
			if(event.metaKey || event.ctrlKey) {
				// hide the tab
				hideTab(-1);
				return false;
			}
			tabSelect(-1);
			return false;
		});

		$('#lists li.taskmaster-tab .list-action').live('click', function(){
			listMenu(this);
			return false;	//stop bubble to tab click
		});
		
		$('#list_all .list-action').click(function(event){
			listMenu(this);
			return false;	//stop bubble to tab click
		});

		//Priority popup
		$('#priopopup .prio-neg-1').click(function(){
			prioClick(-1,this);
		});

		$('#priopopup .prio-zero').click(function(){
			prioClick(0,this);
		});

		$('#priopopup .prio-pos-1').click(function(){
			prioClick(1,this);
		});

		$('#priopopup .prio-pos-2').click(function(){
			prioClick(2,this);
		});
		
		$('#priopopup').mouseleave(function(){
			prioPopup(2);
		});

		// edit form handlers
        $('#page_taskedit').live('keydown', function(e) {
            if (e.keyCode === 13 && e.ctrlKey) {
                $('#taskedit_form').submit();
                e.preventDefault();
            }
        });

		$('#alltags_show').click(function(){
			toggleEditAllTags(1);
			return false;
		});

		$('#alltags_hide').click(function(){
			toggleEditAllTags(0);
			return false;
		});

		$('#taskedit_form').submit(function(){
			return saveTask(this);
		});

		$('#alltags .tag').live('click', function(){
			addEditTag($(this).attr('tag'));
			return false;
		});

		$("#duedate").datepicker({
			dateFormat: _taskmaster.duedatepickerformat(),
			firstDay: _taskmaster.options.firstdayofweek,
			showOn: 'button',
			buttonImage: _taskmaster.templateUrl + 'images/calendar.png', buttonImageOnly: true,
			constrainInput: false,
			duration:'',
			dayNamesMin:_taskmaster.lang.daysMin, dayNames:_taskmaster.lang.daysLong, monthNamesShort:_taskmaster.lang.monthsLong
		});

		$("#edittags").autocomplete('ajax.php?suggestTags', {scroll: false, multiple: true, selectFirst:false, max:8, extraParams:{list:function(){ var taskId = document.getElementById('taskedit_form').id.value; return taskList[taskId].listId; }}});

		$('#taskedit_form').find('select,input,textarea').bind('change keypress', function(){
			flag.editFormChanged = true;
		});

		// tasklist handlers
		$("#tasklist").bind("click", tasklistClick);
		
		$('#tasklist li').live('dblclick', function(){
			//clear selection
			if(document.selection && document.selection.empty && document.selection.createRange().text) document.selection.empty();
			else if(window.getSelection) window.getSelection().removeAllRanges();

            if(flag.readOnly) return false;

			var li = findParentNode(this, 'LI');
			if(li && li.id) {
				var id = li.id.split('_',2)[1];
				if(id) editTask(parseInt(id));
			}
		});

		$('#tasklist .taskactionbtn').live('click', function(){
			var id = parseInt(getLiTaskId(this));
			if(id) taskContextMenu(this, id);
			return false;
		});

		$('#tasklist input[type=checkbox]').live('click', function(){
			var id = parseInt(getLiTaskId(this));
			if(id) completeTask(id, this);
			//return false;
		});

		$('#tasklist .task-toggle').live('click', function(){
			var id = getLiTaskId(this);
			if(id) $('#taskrow_'+id).toggleClass('task-expanded');
			return false;
		});

        $('#tasklist .task-through').live('click', function(){
            var id = getLiTaskId(this);
            if(id) $('#taskrow_'+id).toggleClass('task-expanded');
            return false;
        });

		$('#tasklist .tag').live('click', function(event){
			clearTimeout(_taskmaster.timers.previewtag);
			$('#tasklist li').removeClass('not-in-tagpreview');
			addFilterTag($(this).attr('tag'), $(this).attr('tagid'), (event.metaKey || event.ctrlKey ? true : false) );
			return false;
		});

		if(!this.options.touchDevice) {
			$('#tasklist .task-prio').live('mouseover mouseout', function(event){
                if(flag.readOnly) return false;
                var id = parseInt(getLiTaskId(this));
				if(!id) return;
				if(event.type == 'mouseover') prioPopup(1, this, id);
				else prioPopup(0, this);
			});
		}

		$('#tasklist .taskmaster-action-note-cancel').live('click', function(){
			var id = parseInt(getLiTaskId(this));
			if(id) cancelTaskNote(id);
			return false;
		});

		$('#tasklist .taskmaster-action-note-save').live('click', function(){
			var id = parseInt(getLiTaskId(this));
			if(id) saveTaskNote(id);
			return false;
		});

        $('#tasklist .taskmaster-action-logtime-cancel').live('click', function(){
            //var id = parseInt(getLiTaskId(this));
            $('.logtimePanel').hide();
            return false;
        });

        $('#tasklist .taskmaster-action-logtime-save').live('click', function(){
            var id = parseInt(getLiTaskId(this));
            var total_minutes = parseFloat($(this).parent().find("input[name='hours']").val());
            var target_date = $(this).parent().find('.taskmaster-action-logtime-date').html();

            if(id && total_minutes > 0) {
                _taskmaster.db.request('trackWorkTime', { task_id:id, time:total_minutes, date:target_date }, function(json){
                    loadTasks();
                    $('.logtimePanel').hide();
                });
            }
            return false;
        });

        if(this.options.tagPreview) {
			$('#tasklist .tag').live('mouseover mouseout', function(event){
				var cl = 'tag-id-' + $(this).attr('tagid');
				var sel = (event.metaKey || event.ctrlKey) ? 'li.'+cl : 'li:not(.'+cl+')';
				if(event.type == 'mouseover') {
					_taskmaster.timers.previewtag = setTimeout( function(){$('#tasklist '+sel).addClass('not-in-tagpreview');}, _taskmaster.options.tagPreviewDelay);
				}
				else {
					clearTimeout(_taskmaster.timers.previewtag);
					$('#tasklist li').removeClass('not-in-tagpreview');
				}
			});
		}

		$("#tasklist").sortable({
				items:'> :not(.task-completed)', cancel:'span,input,a,textarea',
		 		delay:150, start:sortStart, update:orderChanged, 
				placeholder:'taskmaster-task-placeholder'
		});

		$("#lists ul").sortable({delay:150, update:listOrderChanged}); 
		this.applySingletab();


		// AJAX Errors
		$('#msg').ajaxSend(function(r,s){
			$("#msg").hide().removeClass('taskmaster-error taskmaster-info').find('.msg-details').hide();
			$("#loading").show();
		});

		$('#msg').ajaxStop(function(r,s){
			$("#loading").fadeOut();
		});

		$('#msg').ajaxError(function(event, request, settings){
			var errtxt;
			if(request.status == 0) errtxt = 'Bad connection';
			else if(request.status != 200) errtxt = 'HTTP: '+request.status+'/'+request.statusText;
			else errtxt = request.responseText;
            if(flag.debugmode) {
			    flashError(_taskmaster.lang.get('error'), errtxt);
            } else {
			    flashError(_taskmaster.lang.get('error_silent'), errtxt);
            }
		});


		// Error Message details
        $("#msg>.msg-text").click(function(){
            if(flag.debugmode) {
                $("#msg>.msg-details").toggle();
            }
        });



		// Authorization
		$('#bar_login').click(function(){
			showAuth(this);
			return false;
		});

		$('#bar_logout').click(function(){
			logout();
			return false;
		});

		$('#login_form').submit(function(){
			doAuth(this);
			return false;
		});


		// Settings
		$("#settings").click(showSettings);
		$("#settings_form").live('submit', function() {
			saveSettings(this);
			return false;
		});
		
		$(".taskmaster-back-button").live('click', function(){ _taskmaster.pageBack(); this.blur(); return false; } );

		$(window).bind('beforeunload', function() {
			if(_taskmaster.pages.current.page == 'taskedit' && flag.editFormChanged) {
				return _taskmaster.lang.get('confirmLeave');
			}
		});

        // Notifications
        $("#notifications").live('click', showNotificationList);
        $('.markread').live('click', function() {
            var notification_id = $(this).attr('rel');
            markNotificationRead(notification_id);
        });
        $('#markallasread').live('click', function() {
            markAllNotificationRead();
        });


        // User management
        $("#manageusers").live('click', showUserManagement);
        $('#createuserBtn').live('click', function(){
            if(!_taskmaster.menus.createuser) _taskmaster.menus.createuser = new taskmasterMenu('taskmaster-createuser', {adjustWidth:true, modal:true});
            _taskmaster.menus.createuser.show(this);
            $("#um_role").removeAttr("disabled");
            $('#um_userid').val('');
            $('#um_username').val('');
            $('#um_password').val('');
            $('#um_email').val('');
            $('#um_role').val('');
            return false;
        });
        $('#createuserSubmit').live('click', function() {
            if($('#um_userid').val() == '') {
                createUser();
            } else {
                editUser(this, 1);
            }
            return false;
        });
        $('.edituser').live('click', function() {
            editUser(this, 0);
            return false;
        });
        $('.deleteuser').live('click', function() {
            deleteUser($(this).attr('rel'));
            return false;
        });

		// tab menu
		this.addAction('listSelected', tabmenuOnListSelected);

		// task context menu
		this.addAction('listsLoaded', cmenuOnListsLoaded);
		this.addAction('listRenamed', cmenuOnListRenamed);
		this.addAction('listAdded', cmenuOnListAdded);
		this.addAction('listSelected', cmenuOnListSelected);
		this.addAction('listOrderChanged', cmenuOnListOrderChanged);
		this.addAction('listHidden', cmenuOnListHidden);

		// select list menu
		this.addAction('listsLoaded', slmenuOnListsLoaded);
		this.addAction('listRenamed', slmenuOnListRenamed);
		this.addAction('listAdded', slmenuOnListAdded);
		this.addAction('listSelected', slmenuOnListSelected);
		this.addAction('listHidden', slmenuOnListHidden);

        $('.task-note-block .icon').live('click', function() {
            toggleTaskNote(getLiTaskId(this));
        });

        $('.task-comment-block .taskmaster-newcomment').live('keydown', function(event) {
            if (event.which == 13) {
                var parent_ul = $(this).parent().parent();
                var comment = $(this).val();
                $(this).val('');
                _taskmaster.db.request('addComment', { task_id:getLiTaskId(parent_ul.get(0)), comment:comment }, function(json) {
                    if(json.done == 1) {
                        var new_item =  '    <li class="existingcomment">' +
                                        '       <span class="subicon"></span>' +
                                        '       <span class="author">'+json.user+'</span>'+
                                        '       <span class="created">'+json.date+'</span>'+
                                        '       ' + comment +
                                        '   </li>';
                        parent_ul.find('.taskmaster-newcomment').parent().before(new_item);
                    }
                });
            }
        });

                // work timer
        $('#taskmaster-timer-pause').live('click', function() {
           pauseTimer();
        });
        $('#taskmaster-timer-continue').live('click', function() {
            continueTimer();
        });
        $('#taskmaster-timer-stop').live('click', function() {
            stopTimer(false);
        });
        $('#taskmaster-timer-finish').live('click', function() {
            stopTimer(true);
        });
        $('.startWork').live('click', function() {
            var taskId = $(this).attr('rel');
            startWorkTimer(taskId);
        });
        $('.logTime').live('click', function() {
            $(this).parent().parent().parent().find('.logtimePanel').show();
        });

		return this;
	},

    converter: function(content) {
        if(flag.markdown) {
              var showdownconv = new Showdown.converter();
              return showdownconv.makeHtml(content);
        } else {
              return prepareHtml(content);
        }
    },

	log: function(v)
	{
		console.log.apply(this, arguments);
	},

	addAction: function(action, proc)
	{
		if(!this.actions[action]) this.actions[action] = new Array();
		this.actions[action].push(proc);
	},

	doAction: function(action, opts)
	{
		if(!this.actions[action]) return;
		for(var i in this.actions[action]) {
			this.actions[action][i](opts);
		}
	},

	setOptions: function(opts) {
		jQuery.extend(this.options, opts);
	},

	loadLists: function(onInit)
	{
		if(filter.search != '') {
			filter.search = '';
			$('#searchbarkeyword').text('');
			$('#searchbar').hide();
		}
		$('#page_tasks').hide();
		$('#tasklist').html('');
		
		tabLists.clear();

        this.db.loadLists(function(res)
		{
			var ti = '';
			var openListId = 0;
            var openArchivedListId = 0;
			if(res && res.total)
			{
				// open required or first non-hidden list
				for(var i=0; i<res.list.length; i++) {
					if(_taskmaster.options.openList) {
						if(_taskmaster.options.openList == res.list[i].id) {
							openListId = res.list[i].id;
							break;
						}
					}
					else if(!res.list[i].hidden && (parseInt(res.list[i].private) == 0 || parseInt(res.list[i].private) == parseInt(flag.userId))) {
						openListId = res.list[i].id;
						break;
					}
				}
				
				// open all tasks tab
				if(_taskmaster.options.openList == -1) openListId = -1;
				
                var span_class = "";
				$.each(res.list, function(i,item){
					tabLists.add(item);
                    if(parseInt(item.private) == 0 || parseInt(item.private) == parseInt(flag.userId)) {

                        if(!_taskmaster.show_archived_lists && item.archive == 1) {
                          return true;
                        }

                        if(_taskmaster.show_archived_lists && item.archive == 0) {
                          return true;
                        }

                        // or open first if all list are hidden
                            if(!openListId) openListId = item.id;

                        span_class = (item.private > 0) ? 'class="private"' : '';
                        ti += '<li id="list_'+item.id+'" class="taskmaster-tab'+(item.hidden?' taskmaster-tabs-hidden':'')+'">'+
                            '<a href="#list/'+item.id+'" title="'+item.name+'"><span ' + span_class + '>'+item.name+'</span>'+
                            '<div class="list-action"></div></a></li>';
                    }
				});
			}
			
			if(openListId) {
				$('#taskmaster_body').removeClass('no-lists');
				$('.taskmaster-need-list').removeClass('taskmaster-item-disabled');
			}
			else {
				curList = 0;
				$('#taskmaster_body').addClass('no-lists');
				$('.taskmaster-need-list').addClass('taskmaster-item-disabled');
			}

			_taskmaster.options.openList = 0;
			$('#lists ul').html(ti);
			$('#lists').show();
			_taskmaster.doAction('listsLoaded');

            if(openArchivedListId) {
                tabSelect(openArchivedListId);
            } else {
                tabSelect(openListId);
            }

            $('#page_tasks').show();

		});

		if(onInit) updateAccessStatus();
	},

	duedatepickerformat: function()
	{
		if(!this.options.duedatepickerformat) return 'yy-mm-dd';
	
		var s = this.options.duedatepickerformat.replace(/(.)/g, function(t,s) {
			switch(t) {
				case 'Y': return 'yy';
				case 'y': return 'y';
				case 'd': return 'dd';
				case 'j': return 'd';
				case 'm': return 'mm';
				case 'n': return 'm';
				case '/':
				case '.':
				case '-': return t;
				default: return '';
			}
		});

		if(s == '') return 'yy-mm-dd';
		return s;
	},

	errorDenied: function()
	{
		flashError(this.lang.get('denied'));
	},
	
	pageSet: function(page, pageClass)
	{
		var prev = this.pages.current;
		prev.lastScrollTop = $(window).scrollTop();
		this.pages.prev.push(this.pages.current);
		this.pages.current = {page:page, pageClass:pageClass};
        if(this.pages.current.page == 'ajax') {
            $('#page_'+ this.pages.current.page).removeClass();
        }
		showhide($('#page_'+ this.pages.current.page).addClass('taskmaster-page-'+ this.pages.current.pageClass), $('#page_'+ prev.page));
	},
	
	pageBack: function()
	{
		if(this.pages.current.page == 'tasks') return false;
		var prev = this.pages.current;
		this.pages.current = this.pages.prev.pop();
        if(this.pages.current.pageClass == 'settings') {
            this.pages.current = this.pages.prev.pop(); // do it a second time, usermanagement will als be opened as ajax page, so go back will close both
        }
		showhide($('#page_'+ this.pages.current.page), $('#page_'+ prev.page).removeClass('taskmaster-page-'+prev.page.pageClass));
		$(window).scrollTop(this.pages.current.lastScrollTop);
	},
	
	applySingletab: function(yesno)
	{
		if(yesno == null) yesno = this.options.singletab;
		else this.options.singletab = yesno;
		
		if(yesno) {
			$('#lists .taskmaster-tabs').addClass('taskmaster-tabs-only-one');
			$("#lists ul").sortable('disable');
		}
		else {
			$('#lists .taskmaster-tabs').removeClass('taskmaster-tabs-only-one');
			$("#lists ul").sortable('enable');
		}
	},
	
	filter: {
		_filters: [],
		clear: function() {
			this._filters = [];
			$('#taskmaster_filters').html('');
		},
		addTag: function(tagId, tag, exclude)
		{
			for(var i in this._filters) {
				if(this._filters[i].tagId && this._filters[i].tagId == tagId) return false;
			}
			this._filters.push({tagId:tagId, tag:tag, exclude:exclude});
			$('#taskmaster_filters').append('<span class="tag-filter tag-id-'+tagId+
				(exclude ? ' tag-filter-exclude' : '')+'"><span class="taskmaster-filter-header">'+
				_taskmaster.lang.get('tagfilter')+'</span>'+tag+'<span class="taskmaster-filter-close" tagid="'+tagId+'"></span></span>');
			return true;
		},
		cancelTag: function(tagId)
		{
			for(var i in this._filters) {
				if(this._filters[i].tagId && this._filters[i].tagId == tagId) {
					this._filters.splice(i,1);
					$('#taskmaster_filters .tag-filter.tag-id-'+tagId).remove();
					return true;
				}
			}
			return false;
		},
		getTags: function(withExcluded)
		{
			var a = [];
			for(var i in this._filters) {
				if(this._filters[i].tagId) {
					if(this._filters[i].exclude && withExcluded) a.push('^'+ this._filters[i].tag);
					else if(!this._filters[i].exclude) a.push(this._filters[i].tag)
				}
			}
			return a.join(', ');
		}
	},
	
	parseAnchor: function()
	{
		if(location.hash == '') return false;
		var h = location.hash.substr(1);
		var a = h.split("/");
		var p = {};
		var s = '';
		
		for(var i=0; i<a.length; i++)
		{
			s = a[i];
			switch(s) {
				case "list": if(a[++i].match(/^-?\d+$/)) { p[s] = a[i]; } break;
				case "alltasks": p.list = '-1'; break;
			}
		}

		if(p.list) this.options.openList = p.list;
		
		return p;
	}

};

function addList()
{
	var r = prompt(_taskmaster.lang.get('addList'), _taskmaster.lang.get('addListDefault'));
	if(r == null) return;

	_taskmaster.db.request('addList', {name:r}, function(json){
		if(!parseInt(json.total)) return;
		var item = json.list[0];
		var i = tabLists.length();
		tabLists.add(item);
		if(i > 0) {
			$('#lists ul').append('<li id="list_'+item.id+'" class="taskmaster-tab">'+
					'<a href="#" title="'+item.name+'"><span>'+item.name+'</span>'+
					'<div class="list-action"></div></a></li>');
			taskmastertasks.doAction('listAdded', item);
		}
		else _taskmaster.loadLists();
	});
};

function renameCurList()
{
	if(!curList) return;
	var r = prompt(_taskmaster.lang.get('renameList'), dehtml(curList.name));
	if(r == null || r == '') return;

	_taskmaster.db.request('renameList', {list:curList.id, name:r}, function(json){
		if(!parseInt(json.total)) return;
		var item = json.list[0];
		curList = item;
		tabLists.replace(item); 
		$('#lists ul>.taskmaster-tabs-selected>a').attr('title', item.name).find('span').html(item.name);
		taskmastertasks.doAction('listRenamed', item);
	});
};

function deleteCurList()
{
	if(!curList) return false;
	var r = confirm(_taskmaster.lang.get('deleteList'));
	if(!r) return;

	_taskmaster.db.request('deleteList', {list:curList.id}, function(json){
		if(!parseInt(json.total)) return;
		_taskmaster.loadLists();
	})
};

function publishCurList()
{
	if(!curList) return false;
	_taskmaster.db.request('publishList', { list:curList.id, publish:curList.published?0:1 }, function(json){
		if(!parseInt(json.total)) return;
		curList.published = curList.published?0:1;
		if(curList.published) {
			$('#btnPublish').addClass('taskmaster-item-checked');
			$('#btnRssFeed').removeClass('taskmaster-item-disabled');
		}
		else {
			$('#btnPublish').removeClass('taskmaster-item-checked');
			$('#btnRssFeed').addClass('taskmaster-item-disabled');
		}
	});
};

function archiveCurList()
{
	if(!curList) return false;
	_taskmaster.db.request('archiveList', { list:curList.id, archive:curList.archive?0:1 }, function(json){
		if(!parseInt(json.total)) return;
		curList.archive = curList.archive?0:1;
		if(curList.archive) {
			$('#btnArchive').addClass('taskmaster-item-checked');
		}
		else {
			$('#btnArchive').removeClass('taskmaster-item-checked');
		}
		_taskmaster.loadLists();
	});
};

function privateCurList()
{
	if(!curList) return false;
	_taskmaster.db.request('privateList', { list:curList.id, private:curList.private?0:1 }, function(json){
		if(!parseInt(json.total)) return;
		curList.private = curList.private?0:1;
		if(curList.private) {
			$('#btnPrivate').addClass('taskmaster-item-checked');
		}
		else {
			$('#btnPrivate').removeClass('taskmaster-item-checked');
		}
		_taskmaster.loadLists();
	});
};


function loadTasks(opts)
{
	if(!curList) return false;
	setSort(curList.sort, 1);
	opts = opts || {};
	if(opts.clearTasklist) {
		$('#tasklist').html('');
		$('#taskajax').html('');
		$('#total').html('0');
        $('#page_tasks > h3').show();
	}

	_taskmaster.db.request('loadTasks', {
		list: curList.id,
		compl: curList.showCompl,
		sort: curList.sort,
		search: filter.search,
		tag: _taskmaster.filter.getTags(true),
		setCompl: opts.setCompl,
        notification: opts.notification
	}, function(json){
		taskList.length = 0;
		taskOrder.length = 0;
		taskCnt.total = taskCnt.past = taskCnt.today = taskCnt.soon = 0;
		var tasks = '';
		$.each(json.list, function(i,item){
			tasks += prepareTaskStr(item);
			taskList[item.id] = item;
			taskOrder.push(parseInt(item.id));
			changeTaskCnt(item, 1);
		});
		if(opts.beforeShow && opts.beforeShow.call) {
			opts.beforeShow();
		}
		refreshTaskCnt();
		$('#tasklist').html(tasks);
        tasksLoaded();
	});
};

function tasksLoaded() {
    if($.cookie('worktimer_start') != null) {
        worktimer.startTime = Date.parse($.cookie('worktimer_start'));
        worktimer.taskId = $.cookie('worktimer_task');
        continueTimer();
    }

	initDateSelectForTimeTracker();
}

function initDateSelectForTimeTracker() {
	$(".taskmaster-action-logtime-dateselect").datepicker({
		onSelect: function(dateText, inst) {
			$(this).parent().find('.taskmaster-action-logtime-date').html(dateText);
		},
		dateFormat: _taskmaster.duedatepickerformat(),
		firstDay: _taskmaster.options.firstdayofweek,
		duration:'',
		dayNamesMin:_taskmaster.lang.daysMin, dayNames:_taskmaster.lang.daysLong, monthNamesShort:_taskmaster.lang.monthsLong
	});
	$(".taskmaster-action-logtime-date").click(function(){
		$(this).parent().find('.taskmaster-action-logtime-dateselect').datepicker("show");
	});
}

function prepareTaskStr(item, noteExp)
{
	// &mdash; = &#8212; = —
	var id = item.id;
	var prio = item.prio;
	return  '    <li id="taskrow_'+id+'" class="' + (item.compl?'task-completed ':'') + item.dueClass + (item.note!=''?' task-has-note':'') + (item.comments.length>0?' task-has-comments':'') +
				        ((curList.showNotes && item.note != '') || noteExp ? ' task-expanded' : '') + prepareTagsClass(item.tags_ids) + '">' +
		    '       <div class="task-actions"><a href="#" class="taskactionbtn"></a></div>'+"\n"+
		    '       <div class="task-left">' +
            '           <div class="task-toggle"></div>'+
		    '           <input type="checkbox" '+(flag.readOnly?'disabled="disabled"':'')+(item.compl?'checked="checked"':'')+'/>' +
            '       </div>'+"\n"+
		    '       <div class="task-middle">' +
            '           <div class="task-through-right">'+prepareDuedate(item)+
		    '               <span class="task-date-completed">' +
            '                   <span title="'+item.dateInlineTitle+'">'+item.dateInline+'</span>&#8212;'+
		    '                   <span title="'+item.dateCompletedInlineTitle+'">'+item.dateCompletedInline+'</span>' +
            '               </span>' +
            '           </div>'+"\n"+
		    '           <div class="task-through">'+
                            preparePrio(prio,id)+'<span class="task-title">'+prepareHtml(item.title)+'</span> '+
		                    ((curList.id == -1 && item.listId == 1) ? '<span class="task-listname">'+ tabLists.get(item.listId).name +'</span>' : '') +	"\n" +
							prepareAuthor(item.author) +
							prepareProgressMini(item)+
		                    prepareTagsStr(item)+'<span class="task-date">'+item.dateInlineTitle+'</span>' +
            '           </div>'+
		    '           <div class="task-note-block">'+
			'				<span class="icon"></span>'+
            '               <div id="tasknote'+id+'" class="task-note">'+_taskmaster.converter(item.note)+'</div>'+
			'               <div id="tasknotearea'+id+'" class="task-note-area">' +
            '          <div id="wmd-button-bar-'+id+'"></div>' +
            '                   <textarea id="notetext'+id+'"></textarea>'+
			'                   <span class="task-note-actions">' +
            '                       <a href="#" class="taskmaster-action-note-save">'+_taskmaster.lang.get('actionNoteSave')+
		    '                       </a> | <a href="#" class="taskmaster-action-note-cancel">'+_taskmaster.lang.get('actionNoteCancel')+'</a>' +
            '                   </span>' +
            '               </div>'+
		    '           </div>'+
                        prepareProgress(item)+
                        prepareComments(item)+
		    "       </div>" +
            "</li>\n";

};

function prepareAuthor(s) {
	if(s == 'null') {
		return '';
	}

	return '<span class="task-author"> by '+ s + '</span>';
}

function prepareHtml(s)
{
	// make URLs clickable
	s = s.replace(/(^|\s|>)(www\.([\w\#$%&~\/.\-\+;:=,\?\[\]@]+?))(,|\.|:|)?(?=\s|&quot;|&lt;|&gt;|\"|<|>|$)/gi, '$1<a href="http://$2" target="_blank">$2</a>$4');
	return s.replace(/(^|\s|>)((?:http|https|ftp):\/\/([\w\#$%&~\/.\-\+;:=,\?\[\]@]+?))(,|\.|:|)?(?=\s|&quot;|&lt;|&gt;|\"|<|>|$)/ig, '$1<a href="$2" target="_blank">$2</a>$4');
};

function preparePrio(prio,id)
{
	var cl =''; var v = '';
	if(prio < 0) { cl = 'prio-neg prio-neg-'+Math.abs(prio); v = '&#8722;'+Math.abs(prio); }	// &#8722; = &minus; = −
	else if(prio > 0) { cl = 'prio-pos prio-pos-'+prio; v = '+'+prio; }
	else { cl = 'prio-zero'; v = '&#177;0'; }													// &#177; = &plusmn; = ±
	return '<span class="task-prio '+cl+'">'+v+'</span>';
};

function prepareTagsStr(item)
{
	if(!item.tags || item.tags == '') return '';
	var a = item.tags.split(',');
	if(!a.length) return '';
	var b = item.tags_ids.split(',')
	for(var i in a) {
		a[i] = '<a href="#" class="tag" tag="'+a[i]+'" tagid="'+b[i]+'">'+a[i]+'</a>';
	}
	return '<span class="task-tags">'+a.join(', ')+'</span>';
};

function prepareTagsClass(ids)
{
	if(!ids || ids == '') return '';
	var a = ids.split(',');
	if(!a.length) return '';
	for(var i in a) {
		a[i] = 'tag-id-'+a[i];
	}
	return ' '+a.join(' ');
};

function prepareDuedate(item)
{
	if(!item.duedate) return '';
	return '<span class="duedate" title="'+item.dueTitle+'">'+item.dueStr+'<span class="duedate-edge">&nbsp;</span></span>';
};

function prepareComments(item) {
    var result =    '<div class="task-comment-block">' +
                    '	<span class="icon"></span>' +
                    '   <ul>';

    for(var i = 0; i < item.comments.length; i++) {
        result +=   '       <li class="existingcomment">' +
                    '           <span class="subicon"></span>' +
                    '           <span class="author">'+item.comments[i].user+'</span>'+
                    '           <span class="created">'+item.comments[i].date+'</span>'+
                    '           <span class="comment">'+item.comments[i].comment+'</span>' +
                    '       </li>';
    }

    result +=       '       <li class="newcomment">' +
                    '           <span class="subicon"></span>' +
                    '           <input type="text" class="taskmaster-newcomment" maxlength="255" />'+
                    '       </li>' +
                    '   </ul>' +
                    '</div>';
    return result;
}

function prepareProgress(item)
{
    if((!item.duration_h && !item.duration_m) || (item.duration_h == 0 && duration_m == 0)) {
		return '';
	}
    if(item.progress >= 100) {
        var bar_color = '#ff3333';
        var text_color = '#ff3333';
    } else if(item.progress >= 80) {
        var bar_color = '#ffcd20';
        var text_color = '#edb801';
    } else {
        var bar_color = '#99d25c';
        var text_color = '#59a901';
    }

    return  '<div class="task-progress-block">' +
    		'	<span class="icon"></span>' +
            '   <ul class="timercontrols">' +
            '       <li><a class="startWork" rel="'+item.id+'">'+_taskmaster.lang.get('start_timer')+'</a></li>' +
            '       <li><a class="logTime" rel="'+item.id+'">'+_taskmaster.lang.get('log_time')+'</a></li>' +
            '   </ul>' +
            '   <div class="inprogress">' +
            '       <span>'+_taskmaster.lang.get('in_progress')+'</span><br/>' +
            '       <span class="minitimer"></span>'+
            '   </div>'+
            '   <div class="progress">'+
            '       <span class="spent">'+_taskmaster.lang.get('progress_spent')+': '+formatHours(item.progress_current)+'</span>'+
            '       <span class="estimated">'+_taskmaster.lang.get('progress_estimated')+': '+formatHours(item.progress_total)+'</span>'+
            '   </div>'+
            '   <div class="progressbar">' +
            '       <span class="taskmaster-progress">'+_taskmaster.lang.get('progress')+':&nbsp;<span style="color:'+text_color+'">'+item.progress+'%</span></span>' +
            '       <span class="taskmaster-progress-bar">' +
            '           <span class="taskmaster-progress-percentbar '+((item.progress >= 100)?'percent-full':'')+'" style="width:'+((item.progress > 100)?200:(item.progress*2))+'px;background-color:'+bar_color+'"></span>' +
            '       </span>' +
            '   </div>' +
            '   <div class="logtimePanel">'+
            '       '+_taskmaster.lang.get('time_spent')+' <input type="hidden" class="taskmaster-action-logtime-dateselect" /><span class="taskmaster-action-logtime-date">'+_taskmaster.lang.get('time_today')+'</span>:&nbsp;<input type="text" name="hours" class="in35 textright">&nbsp;'+_taskmaster.lang.get('time_min')+'&nbsp;<a href="#" class="taskmaster-action-logtime-save">'+_taskmaster.lang.get('save')+'</a> | <a href="#" class="taskmaster-action-logtime-cancel"">'+_taskmaster.lang.get('cancel')+'</a> '+
            '   </div>'+
            '</div>';
};

function formatHours(value) {
    value = Math.abs(value);
    var hours = Math.floor(value);
    var minutes = Math.round((value-Math.floor(value))*60);
    if(hours > 0 && minutes > 0) {
        return hours+'h '+minutes+'m';
    } else if(hours > 0) {
        return hours+'h';
    } else if(minutes > 0) {
        return minutes+'m';
    }
    return '';
}

function prepareProgressMini(item)
{
    if(!item.progress) return '';
    if(item.progress >= 100) {
        var bar_color = '#ff3333';
    } else if(item.progress >= 80) {
        var bar_color = '#ffcd20';
    } else {
        var bar_color = '#99d25c';
    }

    return  '   <span class="taskmaster-miniprogress">' +
    		'		<span class="taskmaster-miniprogress-bar">' +
            '       	<span class="taskmaster-miniprogress-percentbar '+((item.progress >= 100)?'percent-full':'')+'" style="width:'+((item.progress > 100)?50:(item.progress/2))+'px;'+'background-color:'+bar_color+'"></span>' +
            '   	</span>'+
            '		<img class="inprogress_icon" />'+
             ' 	</span>';
};

function submitNewTask(form)
{
	if(form.task.value == '') return false;
	_taskmaster.db.request('newTask', { list:curList.id, title: form.task.value, tag:_taskmaster.filter.getTags() }, function(json){
		if(!json.total) return;
		$('#total').text( parseInt($('#total').text()) + 1 );
		taskCnt.total++;
		form.task.value = '';
		var item = json.list[0];
		taskList[item.id] = item;
		taskOrder.push(parseInt(item.id));
		$('#tasklist').append(prepareTaskStr(item));
		changeTaskOrder(item.id);
		$('#taskrow_'+item.id).effect("highlight", {color:_taskmaster.theme.newTaskFlashColor}, 2000);
		refreshTaskCnt();
	}); 
	flag.tagsChanged = true;
	return false;
};


function changeTaskOrder(id)
{
	id = parseInt(id);
	if(taskOrder.length < 2) return;
	var oldOrder = taskOrder.slice();
	// sortByHand
	if(curList.sort == 0) taskOrder.sort( function(a,b){ 
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			return taskList[a].ow-taskList[b].ow
		});
	// sortByPrio
	else if(curList.sort == 1) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].prio != taskList[b].prio) return taskList[b].prio-taskList[a].prio;
			if(taskList[a].dueInt != taskList[b].dueInt) return taskList[a].dueInt-taskList[b].dueInt;
			return taskList[a].ow-taskList[b].ow; 
		});
	// sortByPrio (reverse)
	else if(curList.sort == 101) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].prio != taskList[b].prio) return taskList[a].prio-taskList[b].prio;
			if(taskList[a].dueInt != taskList[b].dueInt) return taskList[b].dueInt-taskList[a].dueInt;
			return taskList[b].ow-taskList[a].ow; 
		});		
	// sortByDueDate
	else if(curList.sort == 2) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].dueInt != taskList[b].dueInt) return taskList[a].dueInt-taskList[b].dueInt;
			if(taskList[a].prio != taskList[b].prio) return taskList[b].prio-taskList[a].prio;
			return taskList[a].ow-taskList[b].ow; 
		});
	// sortByDueDate (reverse)
	else if(curList.sort == 102) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].dueInt != taskList[b].dueInt) return taskList[b].dueInt-taskList[a].dueInt;
			if(taskList[a].prio != taskList[b].prio) return taskList[a].prio-taskList[b].prio;
			return taskList[b].ow-taskList[a].ow; 
		});		
	// sortByDateCreated
	else if(curList.sort == 3) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].dateInt != taskList[b].dateInt) return taskList[a].dateInt-taskList[b].dateInt;
			if(taskList[a].prio != taskList[b].prio) return taskList[b].prio-taskList[a].prio;
			return taskList[a].ow-taskList[b].ow; 
		});
	// sortByDateCreated (reverse)
	else if(curList.sort == 103) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].dateInt != taskList[b].dateInt) return taskList[b].dateInt-taskList[a].dateInt;
			if(taskList[a].prio != taskList[b].prio) return taskList[a].prio-taskList[b].prio;
			return taskList[b].ow-taskList[a].ow; 
		});
	// sortByDateModified
	else if(curList.sort == 4) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].dateEditedInt != taskList[b].dateEditedInt) return taskList[a].dateEditedInt-taskList[b].dateEditedInt;
			if(taskList[a].prio != taskList[b].prio) return taskList[b].prio-taskList[a].prio;
			return taskList[a].ow-taskList[b].ow; 
		});
	// sortByDateModified (reverse)
	else if(curList.sort == 104) taskOrder.sort( function(a,b){
			if(taskList[a].compl != taskList[b].compl) return taskList[a].compl-taskList[b].compl;
			if(taskList[a].dateEditedInt != taskList[b].dateEditedInt) return taskList[b].dateEditedInt-taskList[a].dateEditedInt;
			if(taskList[a].prio != taskList[b].prio) return taskList[a].prio-taskList[b].prio;
			return taskList[b].ow-taskList[a].ow; 
		});		
	else return;
	if(oldOrder.toString() == taskOrder.toString()) return;
	if(id && taskList[id])
	{
		// optimization: determine where to insert task: top or after some task
		var indx = $.inArray(id,taskOrder);
		if(indx ==0) {
			$('#tasklist').prepend($('#taskrow_'+id))
		} else {
			var after = taskOrder[indx-1];
			$('#taskrow_'+after).after($('#taskrow_'+id));
		}
	}
	else {
		var o = $('#tasklist');
		for(var i in taskOrder) {
			o.append($('#taskrow_'+taskOrder[i]));
		}
	}
};


function prioPopup(act, el, id)
{
	clearTimeout(objPrio.timerLeave);
	var priopopup=$('#priopopup');
	if(act==2) //leave
	{
		objPrio.timerLeave = setTimeout(function(){
			priopopup.fadeOut('slow');
		}, 300);
		return;
	}
	if(act == 0) {
		clearTimeout(objPrio.timer);
		return;
	}
	var offset = $(el).position();
	var prio=taskList[id].prio;
	prio=parseInt(prio)+1;
	priopopup.css({ position: 'absolute', top: offset.top-6, left: offset.left - 5 - (prio*20) });
	objPrio.taskId = id;
	objPrio.el = el;
	objPrio.timer = setTimeout(function(){
		priopopup.fadeIn('fast');
	}, 100);
};

function prioClick(prio, el)
{
	el.blur();
	prio = parseInt(prio);
	$('#priopopup').fadeOut('fast'); //.hide();
	setTaskPrio(objPrio.taskId, prio);
};

function setTaskPrio(id, prio)
{
	_taskmaster.db.request('setPrio', {id:id, prio:prio});
	taskList[id].prio = prio;
	var $t = $('#taskrow_'+id);
	$t.find('.task-prio').replaceWith(preparePrio(prio, id));
	if(curList.sort != 0) changeTaskOrder(id);
	$t.effect("highlight", {color:_taskmaster.theme.editTaskFlashColor}, 'normal');
};

function setSort(v, init)
{
	$('#listmenucontainer .sort-item').removeClass('taskmaster-item-checked').children('.taskmaster-sort-direction').text('');
	if(v == 0) $('#sortByHand').addClass('taskmaster-item-checked');
	else if(v==1 || v==101) $('#sortByPrio').addClass('taskmaster-item-checked').children('.taskmaster-sort-direction').text(v==1 ? '↑' : '↓');
	else if(v==2 || v==102) $('#sortByDueDate').addClass('taskmaster-item-checked').children('.taskmaster-sort-direction').text(v==2 ? '↑' : '↓');
	else if(v==3 || v==103) $('#sortByDateCreated').addClass('taskmaster-item-checked').children('.taskmaster-sort-direction').text(v==3 ? '↓' : '↑');
	else if(v==4 || v==104) $('#sortByDateModified').addClass('taskmaster-item-checked').children('.taskmaster-sort-direction').text(v==4 ? '↓' : '↑');
	else return;

	curList.sort = v;
	if(v == 0 && !flag.readOnly) $("#tasklist").sortable('enable');
	else $("#tasklist").sortable('disable');
	
	if(!init)
	{
		changeTaskOrder();
		if(!flag.readOnly) _taskmaster.db.request('setSort', {list:curList.id, sort:curList.sort});
	}
};


function changeTaskCnt(task, dir, old)
{
	if(dir > 0) dir = 1;
	else if(dir < 0) dir = -1;
	if(dir == 0 && old != null && task.dueClass != old.dueClass) //on saveTask
	{
		if(old.dueClass != '') taskCnt[old.dueClass]--;
		if(task.dueClass != '') taskCnt[task.dueClass]++;
	}
	else if(dir == 0 && old == null) //on comleteTask
	{
		if(!curList.showCompl && task.compl) taskCnt.total--;
		if(task.dueClass != '') taskCnt[task.dueClass] += task.compl ? -1 : 1;
	}
	if(dir != 0) {
		if(task.dueClass != '' && !task.compl) taskCnt[task.dueClass] += dir;
		taskCnt.total += dir;
	}
};

function refreshTaskCnt()
{
	$('#cnt_total').text(taskCnt.total);
	$('#cnt_past').text(taskCnt.past);
	$('#cnt_today').text(taskCnt.today);
	$('#cnt_soon').text(taskCnt.soon);
	if(filter.due == '') $('#total').text(taskCnt.total);
	else if(taskCnt[filter.due] != null) $('#total').text(taskCnt[filter.due]);
};


function setTaskview(v)
{
	if(v == 0)
	{
		if(filter.due == '') return;
		$('#taskview .btnstr').text(_taskmaster.lang.get('tasks'));
		$('#tasklist').removeClass('filter-'+filter.due);
		filter.due = '';
		$('#total').text(taskCnt.total);
	}
	else if(v=='past' || v=='today' || v=='soon')
	{
		if(filter.due == v) return;
		else if(filter.due != '') {
			$('#tasklist').removeClass('filter-'+filter.due);
		}
		$('#tasklist').addClass('filter-'+v);
		$('#taskview .btnstr').text(_taskmaster.lang.get('f_'+v));
		$('#total').text(taskCnt[v]);
		filter.due = v;
	}
};


function toggleAllNotes(show)
{
	for(var id in taskList)
	{
		if(taskList[id].note == '' && taskList[id].comments.length == 0) continue;
		if(show) $('#taskrow_'+id).addClass('task-expanded');
		else $('#taskrow_'+id).removeClass('task-expanded');
	}
	curList.showNotes = show;
	if(_taskmaster.options.saveShowNotes) _taskmaster.db.request('setShowNotesInList', {list:curList.id, shownotes:show}, function(json){});
};


function tabSelect(elementOrId)
{
	var id;
	if(typeof elementOrId == 'number') id = elementOrId;
	else if(typeof elementOrId == 'string') id = parseInt(elementOrId);
	else {
		id = $(elementOrId).attr('id');
		if(!id) return;
		id = parseInt(id.split('_', 2)[1]);
	}
	if(!tabLists.exists(id)) return;
	$('#lists .taskmaster-tabs-selected').removeClass('taskmaster-tabs-selected');
	$('#list_all').removeClass('taskmaster-tabs-selected');
	
	if(id == -1) {
		$('#list_all').addClass('taskmaster-tabs-selected').removeClass('taskmaster-tabs-hidden');
		$('#listmenucontainer .taskmaster-need-real-list').addClass('taskmaster-item-hidden');
	}
	else {
		$('#list_'+id).addClass('taskmaster-tabs-selected').removeClass('taskmaster-tabs-hidden');
		$('#listmenucontainer .taskmaster-need-real-list').removeClass('taskmaster-item-hidden');
	}

	if(curList.id != id)
	{
		if(id == -1) $('#taskmaster_body').addClass('show-all-tasks');
		else $('#taskmaster_body').removeClass('show-all-tasks');
		if(filter.search != '') liveSearchToggle(0, 1);
		taskmastertasks.doAction('listSelected', tabLists.get(id));
	}
	curList = tabLists.get(id);
	if(curList.hidden) {
		curList.hidden = false;
		if(curList.id > 0) _taskmaster.db.request('setHideList', {list:curList.id, hide:0});
	}
	flag.tagsChanged = true;
	cancelTagFilter(0, 1);
	setTaskview(0);
	loadTasks({clearTasklist:1});
    window.location.hash="list/"+id;
};



function listMenu(el)
{
	if(!taskmastertasks.menus.listMenu) taskmastertasks.menus.listMenu = new taskmasterMenu('listmenucontainer', {onclick:listMenuClick});
	taskmastertasks.menus.listMenu.show(el);
};

function listMenuClick(el, menu)
{
	if(!el.id) return;
	switch(el.id) {
		case 'btnAddList': addList(); break;
		case 'btnRenameList': renameCurList(); break;
		case 'btnDeleteList': deleteCurList(); break;
		case 'btnPublish': publishCurList(); break;
		case 'btnArchive': archiveCurList(); break;
		case 'btnPrivate': privateCurList(); break;
		case 'btnExportCSV': exportCurList('csv'); break;
		case 'btnExportICAL': exportCurList('ical'); break;
		case 'btnRssFeed': feedCurList(); break;
		case 'btnShowCompleted': showCompletedToggle(); break;
		case 'btnNotifications': showNotificationsToggle(); break;
		case 'btnClearCompleted': clearCompleted(); break;
		case 'btnTimeTable': showTimeTable(); break;
		case 'sortByHand': setSort(0); break;
		case 'sortByPrio': setSort(curList.sort==1 ? 101 : 1); break;
		case 'sortByDueDate': setSort(curList.sort==2 ? 102 : 2); break;
		case 'sortByDateCreated': setSort(curList.sort==3 ? 103 : 3); break;
		case 'sortByDateModified': setSort(curList.sort==4 ? 104 : 4); break;
	}
};

function deleteTask(id)
{
	if(!confirm(_taskmaster.lang.get('confirmDelete'))) {
		return false;
	}
	_taskmaster.db.request('deleteTask', {id:id}, function(json){
		if(!parseInt(json.total)) return;
		var item = json.list[0];
		taskOrder.splice($.inArray(id,taskOrder), 1);
		$('#taskrow_'+id).fadeOut('normal', function(){ $(this).remove() });
		changeTaskCnt(taskList[id], -1);
		refreshTaskCnt();
		delete taskList[id];
	});
	flag.tagsChanged = true;
	return false;
};

function completeTask(id, ch)
{
	if(!taskList[id]) return; //click on already removed from the list while anim. effect
	var compl = 0;
	if(ch.checked) compl = 1;
	_taskmaster.db.request('completeTask', {id:id, compl:compl, list:curList.id}, function(json){
		if(!parseInt(json.total)) return;
		var item = json.list[0];
		if(item.compl) $('#taskrow_'+id).addClass('task-completed');
		else $('#taskrow_'+id).removeClass('task-completed');
		taskList[id] = item;
		changeTaskCnt(taskList[id], 0);
		if(item.compl && !curList.showCompl) {
			delete taskList[id];
			taskOrder.splice($.inArray(id,taskOrder), 1);
			$('#taskrow_'+id).fadeOut('normal', function(){ $(this).remove() });
		}
		else if(curList.showCompl) {
			$('#taskrow_'+item.id).replaceWith(prepareTaskStr(taskList[id]));
			$('#taskrow_'+id).fadeOut('fast', function(){	
				changeTaskOrder(id);				
				$(this).effect("highlight", {color:_taskmaster.theme.editTaskFlashColor}, 'normal', function(){$(this).css('display','')});
			});
		}
		refreshTaskCnt();
	});
	return false;
};

function toggleTaskNote(id)
{
	var aArea = '#tasknotearea'+id;
	if($(aArea).css('display') == 'none')
	{
		$('#notetext'+id).val(taskList[id].noteText);
		$(aArea).show();
		$('#tasknote'+id).hide();
		$('#taskrow_'+id).addClass('task-expanded');
		$('#notetext'+id).focus();
    } else {
		cancelTaskNote(id)
	}
	return false;
};

function cancelTaskNote(id)
{
	//if(taskList[id].note == '') $('#taskrow_'+id).removeClass('task-expanded');
	$('#tasknotearea'+id).hide();
	$('#tasknote'+id).show();
	return false;
};

function saveTaskNote(id)
{
	_taskmaster.db.request('editNote', {id:id, note:$('#notetext'+id).val()}, function(json){
		if(!parseInt(json.total)) return;
		var item = json.list[0];
		taskList[id].note = item.note;
		taskList[id].noteText = item.noteText;
        $('#tasknote'+id+'>span').html(_taskmaster.converter(prepareHtml(item.note)));
		if(item.note == '') $('#taskrow_'+id).removeClass('task-has-note task-expanded');
		else $('#taskrow_'+id).addClass('task-has-note task-expanded');
		cancelTaskNote(id);
	});
	return false;
};

function editTask(id)
{
	var item = taskList[id];
	if(!item) return false;
	// no need to clear form
	var form = document.getElementById('taskedit_form');
	form.task.value = dehtml(item.title);
	form.note.value = item.noteText;
	form.id.value = item.id;
	form.tags.value = item.tags.split(',').join(', ');
	form.duedate.value = item.duedate;
	form.duedate_h.value = item.duedate_h;
	form.duedate_m.value = item.duedate_m;
	form.prio.value = item.prio;
	form.duration_h.value = item.duration_h;
	form.duration_m.value = item.duration_m;
	$('#taskedit-date .date-created>span').text(item.date);
	if(item.compl) $('#taskedit-date .date-completed').show().find('span').text(item.dateCompleted);
	else $('#taskedit-date .date-completed').hide();
	toggleEditAllTags(0);
	showEditForm();
	return false;
};

function clearEditForm()
{
	var form = document.getElementById('taskedit_form');
	form.task.value = '';
	form.note.value = '';
	form.tags.value = '';
	form.duedate.value = '';
	form.prio.value = '0';
	form.id.value = '';
	form.duration_h.value = '';
	form.duration_m.value = '';
	toggleEditAllTags(0);
	$('#duedate_h > option:eq(0)').attr('selected','selected');
	$('#duedate_m > option:eq(0)').attr('selected','selected');
};

function showEditForm(isAdd)
{
	var form = document.getElementById('taskedit_form');
	if(isAdd)
	{
		clearEditForm();
		$('#page_taskedit').removeClass('taskmaster-inedit').addClass('taskmaster-inadd');
		form.isadd.value = 1;
		if(_taskmaster.options.autotag) form.tags.value = _taskmaster.filter.getTags();
		if($('#task').val() != '')
		{
			_taskmaster.db.request('parseTaskStr', { list:curList.id, title:$('#task').val(), tag:_taskmaster.filter.getTags() }, function(json){
				if(!json) return;
				form.task.value = json.title
				form.tags.value = (form.tags.value != '') ? form.tags.value +', '+ json.tags : json.tags;
				form.prio.value = json.prio;
				$('#task').val('');

			});
		}
	}
	else {
		$('#page_taskedit').removeClass('taskmaster-inadd').addClass('taskmaster-inedit');
		form.isadd.value = 0;
	}

	flag.editFormChanged = false;
	_taskmaster.pageSet('taskedit');
};

function saveTask(form)
{
	if(flag.readOnly) return false;
	if(form.isadd.value != 0)
		return submitFullTask(form);

	_taskmaster.db.request('editTask', {id:form.id.value, title: form.task.value, note:form.note.value,
		prio:form.prio.value, tags:form.tags.value, duedate:form.duedate.value, duedate_h:form.duedate_h.value, duedate_m:form.duedate_m.value, duration_h:form.duration_h.value, duration_m:form.duration_m.value},
		function(json){
			if(!parseInt(json.total)) return;
			var item = json.list[0];
			changeTaskCnt(item, 0, taskList[item.id]);
			taskList[item.id] = item;
			var noteExpanded = (item.note != '' && $('#taskrow_'+item.id).is('.task-expanded')) ? 1 : 0;
			$('#taskrow_'+item.id).replaceWith(prepareTaskStr(item, noteExpanded));
			if(curList.sort != 0) changeTaskOrder(item.id);
			_taskmaster.pageBack(); //back to list
			refreshTaskCnt();
			initDateSelectForTimeTracker();
			$('#taskrow_'+item.id).effect("highlight", {color:_taskmaster.theme.editTaskFlashColor}, 'normal', function(){$(this).css('display','')});
	});
	$("#edittags").flushCache();
	flag.tagsChanged = true;

	return false;
};

function toggleEditAllTags(show)
{
	if(show)
	{
		if(curList.id == -1) {
			var taskId = document.getElementById('taskedit_form').id.value;
			loadTags(taskList[taskId].listId, fillEditAllTags);
		}
		else if(flag.tagsChanged) loadTags(curList.id, fillEditAllTags);
		else fillEditAllTags();
		showhide($('#alltags_hide'), $('#alltags_show'));
	}
	else {
		$('#alltags').hide();
		showhide($('#alltags_show'), $('#alltags_hide'))
	}
};

function fillEditAllTags()
{
	var a = [];
	for(var i=tagsList.length-1; i>=0; i--) { 
		a.push('<a href="#" class="tag" tag="'+tagsList[i].tag+'">'+tagsList[i].tag+'</a>');
	}
	$('#alltags .tags-list').html(a.join(', '));
	$('#alltags').show();
};

function addEditTag(tag)
{
	var v = $('#edittags').val();
	if(v == '') { 
		$('#edittags').val(tag);
		return;
	}
	var r = v.search(new RegExp('(^|,)\\s*'+tag+'\\s*(,|$)'));
	if(r < 0) $('#edittags').val(v+', '+tag);
};

function loadTags(listId, callback)
{
	_taskmaster.db.request('tagCloud', {list:listId}, function(json){
		if(!parseInt(json.total)) tagsList = [];
		else tagsList = json.cloud;
		var cloud = '';
		$.each(tagsList, function(i,item){
			cloud += ' <a href="#" tag="'+item.tag+'" tagid="'+item.id+'" class="tag w'+item.w+'" >'+item.tag+'</a>';
		});
		$('#tagcloudcontent').html(cloud)
		flag.tagsChanged = false;
		callback();
	});
};

function cancelTagFilter(tagId, dontLoadTasks)
{
	if(tagId)  _taskmaster.filter.cancelTag(tagId);
	else _taskmaster.filter.clear();
	if(dontLoadTasks==null || !dontLoadTasks) loadTasks();
};

function addFilterTag(tag, tagId, exclude)
{
	if(!_taskmaster.filter.addTag(tagId, tag, exclude)) return false;
	loadTasks();
};

function liveSearchToggle(toSearch, dontLoad)
{
    if(toSearch)
	{
		$('#search').focus();
	}
	else
	{
		if($('#search').val() != '') {
			filter.search = '';
			$('#search').val('');
			$('#searchbarkeyword').text('');
			$('#searchbar').hide();
			$('#search_close').hide();
			if(!dontLoad) loadTasks();
		}
		
		$('#search').blur();
	}
};

function searchTasks(force)
{
	var newkeyword = $('#search').val();
	if(newkeyword == filter.search && !force) return false;
	filter.search = newkeyword;
	$('#searchbarkeyword').text(filter.search);
	if(filter.search != '') $('#searchbar').fadeIn('fast');
	else $('#searchbar').fadeOut('fast');
	loadTasks();
	return false;
};


function submitFullTask(form)
{
	if(flag.readOnly) return false;

	_taskmaster.db.request('fullNewTask', { list:curList.id, tag:_taskmaster.filter.getTags(), title: form.task.value, note:form.note.value,
		prio:form.prio.value, tags:form.tags.value, duedate:form.duedate.value, duedate_h:form.duedate_h.value, duedate_m:form.duedate_m.value, duration_h:form.duration_h.value, duration_m:form.duration_m.value }, function(json){
		if(!parseInt(json.total)) return;
		form.task.value = '';
		var item = json.list[0];
		taskList[item.id] = item;
		taskOrder.push(parseInt(item.id));
		$('#tasklist').append(prepareTaskStr(item));
		changeTaskOrder(item.id);
		_taskmaster.pageBack();
		$('#taskrow_'+item.id).effect("highlight", {color:_taskmaster.theme.newTaskFlashColor}, 2000);
		changeTaskCnt(item, 1);
		refreshTaskCnt();
	});

	$("#edittags").flushCache();
	flag.tagsChanged = true;
	return false;
};


function sortStart(event,ui)
{
	// remember initial order before sorting
	sortOrder = $(this).sortable('toArray');
};

function orderChanged(event,ui)
{
	if(!ui.item[0]) return;
	var itemId = ui.item[0].id;
	var n = $(this).sortable('toArray');

	// remove possible empty id's
	for(var i=0; i<sortOrder.length; i++) {
		if(sortOrder[i] == '') { sortOrder.splice(i,1); i--; }
	}
	if(n.toString() == sortOrder.toString()) return;

	// make index: id=>position
	var h0 = {}; //before
	for(var j=0; j<sortOrder.length; j++) {
		h0[sortOrder[j]] = j;
	}
	var h1 = {}; //after
	for(var j=0; j<n.length; j++) {
		h1[n[j]] = j;
		taskOrder[j] = parseInt(n[j].split('_')[1]);
	}

	// prepare param
	var o = [];
	var diff;
	var replaceOW = taskList[sortOrder[h1[itemId]].split('_')[1]].ow;
	for(var j in h0)
	{
		diff = h1[j] - h0[j];
		if(diff != 0) {
			var a = j.split('_');
			if(j == itemId) diff = replaceOW - taskList[a[1]].ow;
			o.push({id:a[1], diff:diff});
			taskList[a[1]].ow += diff;
		}
	}

	_taskmaster.db.request('changeOrder', {order:o});
};


function taskmasterMenu(container, options)
{
	var menu = this;
	this.container = document.getElementById(container);
    this.$container = $(this.container);
	this.menuOpen = false;
	this.options = options || {};
	this.submenu = [];
	this.curSubmenu = null;
	this.showTimer = null;
	this.ts = (new Date).getTime();
	this.container.taskmastermenu = this.ts;

	this.$container.find('li').click(function(){
		menu.onclick(this, menu);
		return false;
	})
	.each(function(){

		var submenu = 0;
		if($(this).is('.taskmaster-menu-indicator'))
		{
			submenu = new taskmasterMenu($(this).attr('submenu'));
			submenu.$caller = $(this);
			submenu.parent = menu;
			if(menu.root) submenu.root = menu.root;	//!! be careful with circular references
			else submenu.root = menu;
			menu.submenu.push(submenu);
			submenu.ts = submenu.container.taskmastermenu = submenu.root.ts;

			submenu.$container.find('li').click(function(){
				submenu.root.onclick(this, submenu);
				return false;
			});
		}

		$(this).hover(
			function(){
				if(!$(this).is('.taskmaster-menu-item-active')) menu.$container.find('li').removeClass('taskmaster-menu-item-active');
				clearTimeout(menu.showTimer);
				if(menu.hideTimer && menu.parent) {
					clearTimeout(menu.hideTimer);
					menu.hideTimer = null;
					menu.$caller.addClass('taskmaster-menu-item-active');
					clearTimeout(menu.parent.showTimer);
				}

				if(menu.curSubmenu && menu.curSubmenu.menuOpen && menu.curSubmenu != submenu && !menu.curSubmenu.hideTimer)
				{
					menu.$container.find('li').removeClass('taskmaster-menu-item-active');
					var curSubmenu = menu.curSubmenu;
					curSubmenu.hideTimer = setTimeout(function(){
						curSubmenu.hide();
						curSubmenu.hideTimer = null;
					}, 300);
				}

				if(!submenu || menu.curSubmenu == submenu && menu.curSubmenu.menuOpen)
					return;
			
				menu.showTimer = setTimeout(function(){
					menu.curSubmenu = submenu;
					submenu.showSub();
				}, 400);
			},
			function(){}
		);

	});

	this.onclick = function(item, fromMenu)
	{
		if($(item).is('.taskmaster-item-disabled,.taskmaster-menu-indicator,.taskmaster-item-hidden')) return;
		menu.close();
		if(this.options.onclick) this.options.onclick(item, fromMenu);
	};

	this.hide = function()
	{
		for(var i in this.submenu) this.submenu[i].hide();
		clearTimeout(this.showTimer);
        if(this.options.modal)
        {
            $('.taskmaster-menu-modal').hide();
        }
		this.$container.hide();
		this.$container.find('li').removeClass('taskmaster-menu-item-active');
		this.menuOpen = false;
	};

	this.close = function(event)
	{
        if(!this.menuOpen) return;

        if(event)
		{
			// ignore if event (click) was on caller or container
			var t = event.target;
			if(t == this.caller || (t.taskmastermenu && t.taskmastermenu == this.ts)) return;
			while(t.parentNode) {
				if(t.parentNode == this.caller || (t.taskmastermenu && t.taskmastermenu == this.ts)) return;
				t = t.parentNode;
			}
		}

        if(this.options.modal)
        {
            $('.taskmaster-menu-modal').hide();
        }

		this.hide();
		$(this.caller).removeClass('taskmaster-menu-button-active');
		$(document).unbind('mousedown.taskmastermenuclose');
	};

    this.log = function(msg)
    {
        console.log(msg);
    }

	this.show = function(caller)
	{
        if(this.menuOpen)
		{
            this.close();
			if(this.caller && this.caller == caller) return;
		}

		$(document).triggerHandler('mousedown.taskmastermenuclose'); //close any other open menu
		this.caller = caller;
		var $caller = $(caller);
		
		// beforeShow trigger
		if(this.options.beforeShow && this.options.beforeShow.call)
			this.options.beforeShow();

        if(this.options.modal)
        {
            $('.taskmaster-menu-modal').show();
        }

		// adjust width
		if(this.options.adjustWidth && this.$container.outerWidth(true) > $(window).width())
			this.$container.width($(window).width() - (this.$container.outerWidth(true) - this.$container.width()));
		$caller.addClass('taskmaster-menu-button-active');
		var offset = $caller.offset();
		var x2 = $(window).width() + $(document).scrollLeft() - this.$container.outerWidth(true) - 1;
		var x = offset.left < x2 ? offset.left : x2;
		if(x<0) x=0;
		var y = offset.top+caller.offsetHeight-1;
		if(y + this.$container.outerHeight(true) > $(window).height() + $(document).scrollTop()) y = offset.top - this.$container.outerHeight();
		if(y<0) y=0;
        this.$container.css({ 'position': 'absolute', 'top': y, 'left': x, 'width':this.$container.width()}).show();
        var menu = this;
		$(document).bind('mousedown.taskmastermenuclose', function(e){ menu.close(e) });
		this.menuOpen = true;
	};

	this.showSub = function()
	{
		this.$caller.addClass('taskmaster-menu-item-active');
		var offset = this.$caller.offset();
		var x = offset.left+this.$caller.outerWidth();
		if(x + this.$container.outerWidth(true) > $(window).width() + $(document).scrollLeft()) x = offset.left - this.$container.outerWidth() - 1;
		if(x<0) x=0;
		var y = offset.top + this.parent.$container.offset().top-this.parent.$container.find('li:first').offset().top;
		if(y +  this.$container.outerHeight(true) > $(window).height() + $(document).scrollTop()) y = $(window).height() + $(document).scrollTop()- this.$container.outerHeight(true) - 1;
		if(y<0) y=0;
		this.$container.css({ position: 'absolute', top: y, left: x, width:this.$container.width() /*, 'min-width': this.$caller.outerWidth()*/ }).show();
		this.menuOpen = true;
	};

	this.destroy = function()
	{
        if(this.options.modal)
        {
            $('.taskmaster-menu-modal').hide();
        }
        for(var i in this.submenu) {
			this.submenu[i].destroy();
			delete this.submenu[i];
		}
		this.$container.find('li').unbind(); //'click mouseenter mouseleave'
	};
};


function taskContextMenu(el, id)
{
	if(!_taskmaster.menus.cmenu) _taskmaster.menus.cmenu = new taskmasterMenu('taskcontextcontainer', {
		onclick: taskContextClick,
		beforeShow: function() {
			$('#cmenupriocontainer li').removeClass('taskmaster-item-checked');
			$('#cmenu_prio\\:'+ taskList[_taskmaster.menus.cmenu.tag].prio).addClass('taskmaster-item-checked');
		} 
	});
	_taskmaster.menus.cmenu.tag = id;
	_taskmaster.menus.cmenu.show(el);
};

function taskContextClick(el, menu)
{
	if(!el.id) return;
	var taskId = parseInt(_taskmaster.menus.cmenu.tag);
	var id = el.id, value;
	var a = id.split(':');
	if(a.length == 2) {
		id = a[0];
		value = a[1];
	}
	switch(id) {
		case 'cmenu_edit': editTask(taskId); break;
		case 'cmenu_note': toggleTaskNote(taskId); break;
		case 'cmenu_delete': deleteTask(taskId); break;
		case 'cmenu_prio': setTaskPrio(taskId, parseInt(value)); break;
		case 'cmenu_list':
			if(menu.$caller && menu.$caller.attr('id')=='cmenu_move') moveTaskToList(taskId, value);
			break;
	}
};


function moveTaskToList(taskId, listId)
{
	if(curList.id == listId) return;
	_taskmaster.db.request('moveTask', {id:taskId, from:curList.id, to:listId}, function(json){
		if(!parseInt(json.total)) return;
		if(curList.id == -1)
		{
			// leave the task in current tab (all tasks tab)
			var item = json.list[0];
			changeTaskCnt(item, 0, taskList[item.id]);
			taskList[item.id] = item;
			var noteExpanded = (item.note != '' && $('#taskrow_'+item.id).is('.task-expanded')) ? 1 : 0;
			$('#taskrow_'+item.id).replaceWith(prepareTaskStr(item, noteExpanded));
			if(curList.sort != 0) changeTaskOrder(item.id);
			refreshTaskCnt();
			$('#taskrow_'+item.id).effect("highlight", {color:_taskmaster.theme.editTaskFlashColor}, 'normal', function(){$(this).css('display','')});
		}
		else {
			// remove the task from currrent tab
			changeTaskCnt(taskList[taskId], -1)
			delete taskList[taskId];
			taskOrder.splice($.inArray(taskId,taskOrder), 1);
			$('#taskrow_'+taskId).fadeOut('normal', function(){ $(this).remove() });
			refreshTaskCnt();
		}
	});

	$("#edittags").flushCache();
	flag.tagsChanged = true;
};


function cmenuOnListsLoaded()
{
	if(_taskmaster.menus.cmenu) _taskmaster.menus.cmenu.destroy();
	_taskmaster.menus.cmenu = null;
	var s = '';
	var all = tabLists.getAll();
	for(var i in all) {
        name = all[i].name;
        if(all[i].archive == 1) {
              name += ' (archived)';
            } else if(all[i].private > 0 && all[i].private != flag.userId) {
              name += ' (private)';
            }
        s += '<li id="cmenu_list:'+all[i].id+'" class="'+(all[i].hidden?'taskmaster-list-hidden':'')+'">'+name+'</li>';
	}
	$('#cmenulistscontainer ul').html(s);

	if(!flag.multiUser) {
		$('#btnPrivate').addClass('taskmaster-item-disabled');
	}

};

function cmenuOnListAdded(list)
{
	if(_taskmaster.menus.cmenu) _taskmaster.menus.cmenu.destroy();
	_taskmaster.menus.cmenu = null;
	$('#cmenulistscontainer ul').append('<li id="cmenu_list:'+list.id+'">'+list.name+'</li>');
};

function cmenuOnListRenamed(list)
{
	$('#cmenu_list\\:'+list.id).text(list.name);
};

function cmenuOnListSelected(list)
{
	$('#cmenulistscontainer li').removeClass('taskmaster-item-disabled');
	$('#cmenu_list\\:'+list.id).addClass('taskmaster-item-disabled').removeClass('taskmaster-list-hidden');
};

function cmenuOnListOrderChanged()
{
	cmenuOnListsLoaded();
	$('#cmenu_list\\:'+curList.id).addClass('taskmaster-item-disabled');
};

function cmenuOnListHidden(list)
{
	$('#cmenu_list\\:'+list.id).addClass('taskmaster-list-hidden');
};


function tabmenuOnListSelected(list)
{
	if(list.published) {
		$('#btnPublish').addClass('taskmaster-item-checked');
		$('#btnRssFeed').removeClass('taskmaster-item-disabled');
	}
	else {
		$('#btnPublish').removeClass('taskmaster-item-checked');
		$('#btnRssFeed').addClass('taskmaster-item-disabled');
	}
	if(list.private == 1) $('#btnPrivate').addClass('taskmaster-item-checked');
	if(list.archive) $('#btnArchive').addClass('taskmaster-item-checked');
	if(list.showCompl) $('#btnShowCompleted').addClass('taskmaster-item-checked');
	else $('#btnShowCompleted').removeClass('taskmaster-item-checked');
    if(list.notification) $('#btnNotifications').addClass('taskmaster-item-checked');
    else $('#btnNotifications').removeClass('taskmaster-item-checked');
    if(flag.globalNotifications) {
        $('#btnNotifications').addClass('taskmaster-item-disabled');
        $('#btnNotifications').removeClass('taskmaster-item-checked');
    } else {
        $('#btnNotifications').removeClass('taskmaster-item-disabled');
    }
};


function listOrderChanged(event, ui)
{
	var a = $(this).sortable("toArray");
	var order = [];
	for(var i in a) {
		order.push(a[i].split('_')[1]);
	}
	tabLists.reorder(order);
	_taskmaster.db.request('changeListOrder', {order:order});
	_taskmaster.doAction('listOrderChanged', {order:order});
};

function showCompletedToggle()
{
	var act = curList.showCompl ? 0 : 1;
	curList.showCompl = tabLists.get(curList.id).showCompl = act;
	if(act) $('#btnShowCompleted').addClass('taskmaster-item-checked');
	else $('#btnShowCompleted').removeClass('taskmaster-item-checked');
	loadTasks({setCompl:1});
};

function showNotificationsToggle()
{
	var act = curList.notification ? 0 : 1;
	curList.notification = tabLists.get(curList.id).notification = act;
	if(act) $('#btnNotifications').addClass('taskmaster-item-checked');
	else $('#btnNotifications').removeClass('taskmaster-item-checked');
	loadTasks({notification:act});
};

function clearCompleted()
{
	if(!curList) return false;
	var r = confirm(_taskmaster.lang.get('clearCompleted'));
	if(!r) return;
	_taskmaster.db.request('clearCompletedInList', {list:curList.id}, function(json){
		if(!parseInt(json.total)) return;
		flag.tagsChanged = true;
		if(curList.showCompl) loadTasks();
	});
};

function tasklistClick(e)
{
	var node = e.target.nodeName.toUpperCase();
	if(node=='SPAN' || node=='LI' || node=='DIV')
	{
		var li =  findParentNode(e.target, 'LI');
		if(li) {
			if(selTask && li.id != selTask) $('#'+selTask).removeClass('clicked doubleclicked');
			selTask = li.id;
			if($(li).is('.clicked')) $(li).toggleClass('doubleclicked');
			else $(li).addClass('clicked');
		}
	}
};


function showhide(a,b)
{
	// its the same page - do nothing
    if(a.attr('id') == b.attr('id')) {
        return;
    }
    a.show();
	b.hide();
};

function findParentNode(el, node)
{
	// in html nodename is in uppercase, in xhtml nodename in in lowercase
	if(el.nodeName.toUpperCase() == node) return el;
	if(!el.parentNode) return null;
	while(el.parentNode) {
		el = el.parentNode;
		if(el.nodeName.toUpperCase() == node) return el;
	}
};

function getLiTaskId(el)
{
	var li = findParentNode(el, 'LI');
	if(!li || !li.id) return 0;
	return li.id.split('_',2)[1];
};

function isParentId(el, id)
{
	if(el.id && $.inArray(el.id, id) != -1) return true;
	if(!el.parentNode) return null;
	return isParentId(el.parentNode, id);
};

function dehtml(str)
{
	return str.replace(/&quot;/g,'"').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
};


function slmenuOnListsLoaded()
{
	if(_taskmaster.menus.selectlist) {
		_taskmaster.menus.selectlist.destroy();
		_taskmaster.menus.selectlist = null;
	}

	var s = '';
	var all = tabLists.getAll();
	for(var i in all) {
		s += '<li id="slmenu_list:'+all[i].id+'" class="'+(all[i].id==curList.id?'taskmaster-item-checked':'')+' list-id-'+all[i].id+(all[i].hidden?' taskmaster-list-hidden':'')+'"><div class="menu-icon"></div><a href="#list/'+all[i].id+'">'+all[i].name+'</a></li>';
	}
	$('#slmenucontainer ul>.slmenu-lists-begin').nextAll().remove();
	$('#slmenucontainer ul>.slmenu-lists-begin').after(s);

	if(_taskmaster.show_archived_lists == 1) {
		$('.list-id--2').addClass('taskmaster-item-checked');
	}
};

function slmenuOnListRenamed(list)
{
	$('#slmenucontainer li.list-id-'+list.id).find('a').html(list.name);
};

function slmenuOnListAdded(list)
{
	if(_taskmaster.menus.selectlist) {
		_taskmaster.menus.selectlist.destroy();
		_taskmaster.menus.selectlist = null;
	}
	$('#slmenucontainer ul').append('<li id="slmenu_list:'+list.id+'" class="list-id-'+list.id+'"><div class="menu-icon"></div><a href="#list/'+list.id+'">'+list.name+'</a></li>');
};

function slmenuOnListSelected(list)
{
	$('#slmenucontainer li:not(.list-id--2)').removeClass('taskmaster-item-checked');
	$('#slmenucontainer li.list-id-'+list.id).addClass('taskmaster-item-checked').removeClass('taskmaster-list-hidden');

};

function slmenuOnListHidden(list)
{
	$('#slmenucontainer li.list-id-'+list.id).addClass('taskmaster-list-hidden');
};

function slmenuSelect(el, menu)
{
	if(!el.id) return;
	var id = el.id, value;
	var a = id.split(':');
	if(a.length == 2) {
		id = a[0];
		value = a[1];
	}
	if(id == 'slmenu_list' && value == -2) {
		if(_taskmaster.show_archived_lists == 0) {
			_taskmaster.show_archived_lists = 1;
			$('.list-id--2').addClass('taskmaster-item-checked');
		} else {
			_taskmaster.show_archived_lists = 0;
			$('.list-id--2').removeClass('taskmaster-item-checked');
		}

		_taskmaster.loadLists();
	}
	if(id == 'slmenu_list') {
		tabSelect(parseInt(value));
	}

	return false;
};


function exportCurList(format)
{
	if(!curList) return;
	if(!format.match(/^[a-z0-9-]+$/i)) return;
	window.location.href = _taskmaster.taskmasterUrl + 'export.php?list='+curList.id +'&format='+format;
};

function feedCurList()
{
	if(!curList) return;
	window.location.href = _taskmaster.taskmasterUrl + 'feed.php?list='+curList.id;
}

function hideTab(listId)
{
	if(typeof listId != 'number') {
		var id = $(listId).attr('id');
		if(!id) return;
		listId = parseInt(id.split('_', 2)[1]);
	}
	
	if(!tabLists.get(listId)) return false;

	// if we hide current tab
	var listIdToSelect = 0;
	if(curList.id == listId) {
		var all = tabLists.getAll();
		for(var i in all) {
			if(all[i].id != curList.id && !all[i].hidden) {
				listIdToSelect = all[i].id;
				break;
			}
		}
		// do not hide the tab if others are hidden
		if(!listIdToSelect) return false;
	}

	if(listId == -1) {
		$('#list_all').addClass('taskmaster-tabs-hidden').removeClass('taskmaster-tabs-selected');
	}
	else {
		$('#list_'+listId).addClass('taskmaster-tabs-hidden').removeClass('taskmaster-tabs-selected');
	}
	
	tabLists.get(listId).hidden = true;
	
	if(listId > 0) {
		_taskmaster.db.request('setHideList', {list:listId, hide:1});
		_taskmaster.doAction('listHidden', tabLists.get(listId));
	}
	
	if(listIdToSelect) {
		tabSelect(listIdToSelect);
	}
}

/*
	Errors and Info messages
*/

function flashError(str, details)
{
	$("#msg>.msg-text").text(str)
	if(flag.debugmode) {
        $("#msg>.msg-details").text(details);
    }
	$("#loading").hide();
	$("#msg").addClass('taskmaster-error').effect("highlight", {color:_taskmaster.theme.msgFlashColor}, 700);
}

function flashInfo(str, details)
{
	$("#msg>.msg-text").text(str)
	$("#msg>.msg-details").text(details);
	$("#loading").hide();
	$("#msg").addClass('taskmaster-info').effect("highlight", {color:_taskmaster.theme.msgFlashColor}, 700);
}

function toggleMsgDetails()
{
	var el = $("#msg>.msg-details");
	if(!el) return;
	if(el.css('display') == 'none') el.show();
	else el.hide()
}


/*
	Authorization
*/
function updateAccessStatus()
{
	// flag.needAuth is not changed after pageload
	if(flag.needAuth)
	{
		if(!flag.authbypass) {
            $('#bar_auth').show();
        }
		if(flag.isLogged) {
			showhide($("#bar_logout"),$("#bar_login"));
			if(flag.admin) {
                $('#bar .menu-owner').show();
			    $('#bar .bar-delim').show();
            }
		}
		else {
			showhide($("#bar_login"),$("#bar_logout"));
			$('#bar .menu-owner').hide();
			$('#bar .bar-delim').hide();
		}
	}
	else {
		$('#bar .menu-owner').show();
	}
	if(flag.needAuth && !flag.isLogged) {
        flag.readOnly = true;
		$("#bar_public").show();
		$('#taskmaster_body').addClass('readonly');
		liveSearchToggle(1);
		// remove some tab menu items
		$('#btnRenameList,#btnDeleteList,#btnClearCompleted,#btnPublish,#btnArchive,#btnPrivate').remove();
	}
    else if(flag.needAuth && flag.isLogged && flag.readOnly) {
        $('#taskmaster_body').addClass('readonly');
        liveSearchToggle(1);
        // remove some tab menu items
        $('#btnRenameList,#btnDeleteList,#btnClearCompleted,#btnPublish,#btnArchive,#btnPrivate').remove();
    }
	else {
        flag.readOnly = false;
		$('#taskmaster_body').removeClass('readonly');
		$("#bar_public").hide();
		liveSearchToggle(0);
	}
    $('#page_ajax').hide();

	if(flag.notification_count != false) {
		$('#notifications').show();
		$('#notification_counter-value').html(flag.notification_count);
		if(flag.notification_count > 0) {
			$('#notification_counter').addClass('hasone');
		} else {
			$('#notification_counter').addClass('nothing');
		}
	} else {
		$('#notifications').parent().hide();
	}

	if(flag.show_edit_options) {
		$('#btnRenameList').show();
		$('#btnDeleteList').show();
		$('#btnClearCompleted').show();
		$('#btnPublish').show();
		$('#btnArchive').show();
		$('#btnPrivate').show();
		$('#btnExport').show();
		$('#btnExport').next().show();
	} else {
		$('#btnRenameList').hide();
		$('#btnDeleteList').hide();
		$('#btnClearCompleted').hide();
		$('#btnPublish').hide();
		$('#btnArchive').hide();
		$('#btnPrivate').hide();
		$('#btnExport').hide();
		$('#btnExport').next().hide();
	}
}

function showAuth(el)
{
	var w = $('#authform');
	if(w.css('display') == 'none')
	{
		var offset = $(el).position();
		w.css({
			position: 'absolute',
			top: offset.top + el.offsetHeight + 3,
			left: offset.left + el.offsetWidth - w.outerWidth()
		}).show();
        if(flag.multiUser) {
		    $('#username').focus();
        } else {
            $('#password').focus();
        }
	}
	else {
		w.hide();
		el.blur();
	}
}

function doAuth(form)
{
	if(flag.multiUser)
    {
        $.post(taskmastertasks.taskmasterUrl+'ajax.php?login', { login:1, username: form.username.value, password: form.password.value }, function(json){
            form.password.value = '';
            form.username.value = '';
            if(json.logged)
            {
                flag.isLogged = true;
                flag.userRole = json.role;
                flag.userId = json.userid;
                if(json.role == 3) {
                    flag.readOnly = true;
                }
                window.location.reload();
            }
            else {
                flashError(_taskmaster.lang.get('invalidlogin'));
                $('#password').focus();
            }
        }, 'json');
    }
    else
    {
        $.post(taskmastertasks.taskmasterUrl+'ajax.php?login', { login:1, password: form.password.value }, function(json){
            form.password.value = '';
            if(json.logged)
            {
                flag.isLogged = true;
                window.location.reload();
            }
            else {
                flashError(_taskmaster.lang.get('invalidpass'));
                $('#password').focus();
            }
        }, 'json');
    }
	$('#authform').hide();
}

function logout()
{
	$.post(taskmastertasks.taskmasterUrl+'ajax.php?logout', { logout:1 }, function(json){
		flag.isLogged = false;
		window.location.reload();
	}, 'json');
	return false;
} 


/*
	Settings
*/

function showSettings()
{
	if(_taskmaster.pages.current.page == 'ajax' && _taskmaster.pages.current.pageClass == 'settings') return false;
	$('#page_ajax').load(_taskmaster.taskmasterUrl+'pages/settings.php?ajax=yes',null,function(){
		//showhide($('#page_ajax').addClass('taskmaster-page-settings'), $('#page_tasks'));
		_taskmaster.pageSet('ajax','settings');
	})
	return false;
}

function saveSettings(frm)
{
	if(!frm) return false;
	var params = { save:'ajax' };
	$(frm).find("input:text,input:password,input:checked,select").filter(":enabled").each(function() { params[this.name || '__'] = this.value; }); 
	$(frm).find(":submit").attr('disabled','disabled').blur();
	$.post(_taskmaster.taskmasterUrl+'pages/settings.php', params, function(json){
		if(json.saved) {
			flashInfo(_taskmaster.lang.get('settingsSaved'));
			setTimeout('window.location.reload();', 1000);
		}
	}, 'json');
}

/**
 * Timetable
 */
function showTimeTable() {
    if(_taskmaster.pages.current.page == 'ajax' && _taskmaster.pages.current.pageClass == 'timetable') return false;
    if(!curList) return false;
    $('#taskajax').load(_taskmaster.taskmasterUrl+'pages/timetable.php?listid='+curList.id,null,function(){
        $('#page_tasks > h3').hide();
        $('#tasklist').html('');
    })
    return false;
}

/**
 * Notifications
 */
function showNotificationList() {
    if(_taskmaster.pages.current.page == 'ajax' && _taskmaster.pages.current.pageClass == 'notifications') return false;
    $('#page_ajax').load(_taskmaster.taskmasterUrl+'pages/notifications.php?ajax=yes',null,function(){
        //showhide($('#page_ajax').addClass('taskmaster-page-settings'), $('#page_tasks'));
        _taskmaster.pageSet('ajax','notifications');
    })
    return false;
}

/*
    User Management
*/
function showUserManagement()
{
    if(_taskmaster.pages.current.page == 'ajax' && _taskmaster.pages.current.pageClass == 'manageusers') return false;
    $('#page_ajax').load(_taskmaster.taskmasterUrl+'pages/usermanagement.php?ajax=yes',null,function(){
        //showhide($('#page_ajax').addClass('taskmaster-page-settings'), $('#page_tasks'));
        _taskmaster.pageSet('ajax','manageusers');
    })
    return false;
}

function createUser()
{
    _taskmaster.db.request('createUser', { username:$('#um_username').val(), password: $('#um_password').val(), email: $('#um_email').val(), role:$('#um_role').val() }, function(json){
        switch(json.error)
        {
            case 1:
                flashError(_taskmaster.lang.get('um_createerror1'));
                break;

            case 2:
                flashError(_taskmaster.lang.get('um_createerror2'));
                break;

            case 3:
                flashError(_taskmaster.lang.get('um_createerror3'));
                break;

            case 0:
                _taskmaster.menus.createuser.close();
                $('#um_username').val('');
                $('#um_password').val('');
                $('#um_email').val('');
                $('#um_role').val('');
                flashInfo(_taskmaster.lang.get('um_usercreated'));
                $('#page_ajax').load(_taskmaster.taskmasterUrl+'pages/usermanagement.php?ajax=yes',null,function(){

                })
                break;
        }
    });
}

function editUser(clickeditem, step) {
    if(step == 0) { // load data to form
        if(!_taskmaster.menus.createuser) _taskmaster.menus.createuser = new taskmasterMenu('taskmaster-createuser', {adjustWidth:true, modal:true});
        _taskmaster.menus.createuser.show(clickeditem);

        $('#um_userid').val($(clickeditem).attr('rel'));
        $('#um_username').val($(clickeditem).parent().parent().find('td.username').html());
        $('#um_email').val($(clickeditem).parent().parent().find('td.email').html());
        $("#um_role > option:contains('"+$(clickeditem).parent().parent().find('td.role').html()+"')").attr('selected',true);
        if($(clickeditem).parent().parent().find('td.notification').html() == _taskmaster.lang.get('um_notification_on')) {
            $('#um_notification').attr('checked', 'checked');
        } else {
            $('#um_notification').removeAttr('checked');
        }

        if($(clickeditem).attr('rel') == flag.userId) {
            $("#um_role").attr('disabled','disabled');
        } else {
            $("#um_role").removeAttr("disabled");
        }

    } else {        // send form
        _taskmaster.db.request('editUser', { userid:$('#um_userid').val(), username:$('#um_username').val(), password: $('#um_password').val(), email: $('#um_email').val(), role:$('#um_role').val(), notification:$('#um_notification').is(':checked')?1:0 }, function(json){

            if($('#um_userid').val() == flag.userId) {
                if($('#um_notification').is(':checked')) {
                    flag.globalNotifications = true;
                    $('#btnNotifications').addClass('taskmaster-item-disabled');
                    $('#btnNotifications').removeClass('taskmaster-item-checked');
                } else {
                    flag.globalNotifications = false;
                    $('#btnNotifications').removeClass('taskmaster-item-disabled');
                }
            }

            switch(json.error)
            {
                case 1:
                    flashError(_taskmaster.lang.get('um_createerror1'));
                    break;

                case 2:
                    flashError(_taskmaster.lang.get('um_createerror2'));
                    break;

                case 4:
                    flashError(_taskmaster.lang.get('um_updateerror1'));
                    break;

                case 0:
                    $('#um_username').val('');
                    $('#um_password').val('');
                    $('#um_email').val('');
                    $('#um_role').val('');
                    $('#um_userid').val('');
                    _taskmaster.menus.createuser.close();
                    $('#page_ajax').load(_taskmaster.taskmasterUrl+'pages/usermanagement.php?ajax=yes',null,function(){
                        flashInfo(_taskmaster.lang.get('um_userupdated'));
                    })
                    break;
            }
        });
    }
}

function deleteUser(userid)
{
    _taskmaster.db.request('deleteUser', { userid:userid }, function(json){
        switch(json.error)
        {
            case 1:
                flashError(_taskmaster.lang.get('um_deleteerror1'));
                break;

            case 2:
                flashError(_taskmaster.lang.get('um_deleteerror1'));
                break;

            case 0:
                $('#page_ajax').load(_taskmaster.taskmasterUrl+'pages/usermanagement.php?ajax=yes',null,function(){
                    flashInfo(_taskmaster.lang.get('um_userdeleted'));
                })
                break;
        }
    });
}

function markNotificationRead(notificationid)
{
    _taskmaster.db.request('markread', { taskmasternotificationid:notificationid }, function(json){
        switch(json.error)
        {
            case 1:
                flashError(_taskmaster.lang.get('n_deleteerror1'));
                break;

            case 0:
                $('#notification_row_'+notificationid).fadeOut('normal', function(){ $(this).remove() });
                refreshNotificationCounter();
                break;
        }
    });
}

function markAllNotificationRead(notificationid)
{
    _taskmaster.db.request('markallasread', { }, function(json){
        switch(json.error)
        {
            case 1:
                flashError(_taskmaster.lang.get('n_deleteerror1'));
                break;

            case 0:
                $('.notification_row').fadeOut('normal', function(){ $(this).remove() });
                refreshNotificationCounter();
                break;
        }
    });
}

function refreshNotificationCounter() {
    _taskmaster.db.request('countNotifications', { }, function(json){
        if(parseInt(json.count) == 0) {
            $('#notification_counter').hide();
        }
        $('#notification_counter').html(json.count);
    });
}

function startWorkTimer(taskId) {
    worktimer.startTime = new Date();
    worktimer.timerActive = true;
    worktimer.timerTotal = 0;
    worktimer.taskId = taskId;

    $('#main').toggleClass('hastimer');
    $('.timercontrols').hide();
    $('.inprogress').hide();
    $('.startWork[rel='+worktimer.taskId+']').parent().parent().parent().find('.inprogress').show();
    $('.startWork[rel='+worktimer.taskId+']').parent().parent().parent().find('.inprogress_icon').css('display', 'inline-block');


    saveTimerInCookie();

    setTimeout(function() {
        showTime();
    }, 1000);
}

function stopTimer(finished) {
    var dDeltaTime = new Date();
    dDeltaTime.setTime( new Date() - worktimer.startTime );
    var sMin = dDeltaTime.getMinutes(); // Minutenanteil der Differenz
    var sSec = dDeltaTime.getSeconds(); // Sekundenanteil der Differenz
    var sHours = Math.floor( dDeltaTime / 3600000 );

    worktimer.timerActive = false;
    worktimer.timerTotal = sSec + (sMin*60) + (sHours*3600);
    var total_minutes = Math.ceil(worktimer.timerTotal / 60);

    _taskmaster.db.request('trackWorkTime', { task_id:worktimer.taskId, time:total_minutes }, function(json){
        $('#main').toggleClass('hastimer');

        $('#taskmaster-time').html('00:00:00');
        $('.inprogress').hide();
        $('.timercontrols').show();
        $.cookie('worktimer_start', null);
        if(finished) {
            $('#taskrow_'+worktimer.taskId).find('input[type=checkbox]').attr('checked', true);
            $('#taskrow_'+worktimer.taskId).find('input[type=checkbox]').click();
            worktimer.startTime = null;
            worktimer.taskId = null;
        } else {
            worktimer.startTime = null;
            worktimer.taskId = null;
            loadTasks();
        }
    });
}

function pauseTimer() {
    var dDeltaTime = new Date();
    dDeltaTime.setTime( new Date() - worktimer.startTime );
    var sMin = dDeltaTime.getMinutes(); // Minutenanteil der Differenz
    var sSec = dDeltaTime.getSeconds(); // Sekundenanteil der Differenz
    var sHours = Math.floor( dDeltaTime / 3600000 );

    worktimer.timerTotal = sSec + (sMin*60) + (sHours*3600);

    $('#taskmaster-timer-pause').toggle();
    $('#taskmaster-timer-stop').toggle();
    $('#taskmaster-timer-continue').toggle();
    $('#taskmaster-timer-finish').toggle();

    worktimer.timerActive = false;

}

function continueTimer() {
    if(worktimer.timerTotal && worktimer.timerTotal > 0) {
        var a = new Date();
        var mil_sec = a.getTime() - (worktimer.timerTotal * 1000);
        var b = new Date();
        b.setTime(mil_sec);
        worktimer.startTime = b;
        saveTimerInCookie();
        $('#taskmaster-timer-pause').toggle();
        $('#taskmaster-timer-stop').toggle();
        $('#taskmaster-timer-continue').toggle();
        $('#taskmaster-timer-finish').toggle();
    } else { // it might come out of the cookie
        $('.inprogress').hide();
        $('.startWork[rel='+worktimer.taskId+']').parent().parent().parent().find('.inprogress').show();
        $('#taskrow_'+worktimer.taskId).find('.inprogress_icon').css('display', 'inline-block');
        $('.timercontrols').hide();
        $('#main').toggleClass('hastimer');
    }

    worktimer.timerActive = true;

    setTimeout(function() {
        showTime();
    }, 1000);
}

function showTime() {
    if(!worktimer.timerActive) {
        return;
    }

    var dDeltaTime = new Date();
    dDeltaTime.setTime( new Date() - worktimer.startTime );

    var sMin = dDeltaTime.getMinutes(); // Minutenanteil der Differenz
    var sSec = dDeltaTime.getSeconds(); // Sekundenanteil der Differenz
    var sHours = Math.floor( dDeltaTime / 3600000 );

    sHours = ( sHours < 10 ) ? "0" + sHours : sHours;
    sMin = ( sMin < 10 ) ? "0" + sMin : sMin;
    sSec = ( sSec < 10 ) ? "0" + sSec : sSec;

    $('#taskmaster-time').html(sHours + ":" + sMin + ":" + sSec);
    $('.startWork[rel='+worktimer.taskId+']').parent().parent().parent().find('.minitimer').html(sHours + ":" + sMin + ":" + sSec);

    setTimeout(function() {
        showTime();
    }, 1000);
}

function saveTimerInCookie() {
    var a = new Date();
    a.setHours(a.getHours() + 10);

    $.cookie('worktimer_start', worktimer.startTime, {expires: a});
    $.cookie('worktimer_task', worktimer.taskId, {expires: a});
}

})();