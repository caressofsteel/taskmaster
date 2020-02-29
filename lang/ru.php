<?php

/*

# Taskmaster

Taskmaster Language Pack
Language: Russian
Original name: Русский

This file is part of the Taskmaster project. Taskmaster is a simple task, project, and information tracking application.

# Copyright

Copyright 2013-2015 David Rodgers - <https://github.com/caressofsteel/taskmaster>
Copyright 2012-2013 Alexander Reichardt - <https://github.com/alex-LE/yourTinyTodo>
Copyright 2009-2010 Max Pozdeev - <https://github.com/maxpozdeev/mytinytodo>

This project is distributed under the GNU General Public License. Please see the included COPYRIGHT and LICENSE-GPL3 for more information. Copyrights for portions of this file are retained by their owners.

*/

class Lang extends DefaultLang
{
	var $js = array(
		'confirmDelete' => 'Вы действительно хотите удалить задачу?',
		'confirmLeave' => 'На странице могут быть несохраненные данные. Вы действительно хотите закрыть страницу?',
		'actionNoteSave' => 'сохранить',
		'actionNoteCancel' => 'отмена',
		'error' => 'Ошибка',
		'denied' => 'Доступ запрещен',
		'invalidpass' => 'Неверный пароль',
		'invalidlogin' => 'Неверное имя',
		'tagfilter' => 'Тег:',
		'addList' => 'Новый список',
		'renameList' => 'Переименовать список',
		'deleteList' => 'Вы действительно хотите удалить этот список со всеми задачами?',
		'clearCompleted' => 'Удалить все выполненные задачи из списка?',
		'settingsSaved' => 'Настройки сохранены. Перезагрузка...',
		'um_usercreated' => 'Пользователь создан',
		'um_userupdated' => 'Пользователь обновлен',
		'um_userdeleted' => 'Пользователь удален',
		'um_createerror1' => 'Неправильные данные',
		'um_createerror2' => 'Такой пользователь уже есть',
		'um_createerror3' => 'Невозможно создать пользователя',
		'um_updateerror1' => 'Невозможно обновить данные пользователя',
		'um_deleteerror1' => 'Невозможно удалить пользователя',
	);

	var $inc = array(
		'htab_newtask' => 'Новая задача',
		'htab_search' => 'Поиск',
		'btn_add' => 'Добавить',
		'btn_search' => 'Искать',
		'advanced_add' => 'Расширенная форма',
		'searching' => 'Поиск',
		'tasks' => 'Задачи',
		'taskdate_inline_created' => 'добавлена %s',
		'taskdate_inline_completed' => 'Завершена %s',
		'taskdate_inline_duedate' => 'В срок %s',
		'taskdate_created' => 'Дата создания',
		'taskdate_completed' => 'Дата завершения',
		'go_back' => '&lt;&lt; Назад',
		'edit_task' => 'Редактирование задачи',
		'add_task' => 'Новая задача',
		'priority' => 'Приоритет',
		'task' => 'Задача',
		'note' => 'Заметка',
		'tags' => 'Теги',
		'save' => 'Сохранить',
		'cancel' => 'Отмена',
		'password' => 'Пароль',
		'btn_login' => 'Войти',
		'a_login' => 'Вход',
		'a_logout' => 'Выйти',
		'public_tasks' => 'Опубликованные задачи',
		'tagcloud' => 'Теги',
		'tagfilter_cancel' => 'отменить фильтр по тегу',
		'sortByHand' => 'Сортировка вручную',
		'sortByPriority' => 'Сортировка по приоритету',
		'sortByDueDate' => 'Сортировка по сроку',
		'sortByDateCreated' => 'Сортировка по дате добавления',
		'sortByDateModified' => 'Сортировка по дате изменения',
		'due' => 'Срок',
		'daysago' => '%d дн. назад',
		'indays' => 'через %d дн.',
		'months_short' => array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'),
		'months_long' => array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'),
		'days_min' => array('Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'),
		'days_long' => array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'),
		'today' => 'сегодня',
		'yesterday' => 'вчера',
		'tomorrow' => 'завтра',
		'f_past' => 'Просроченные',
		'f_today' => 'Сегодня и завтра',
		'f_soon' => 'Скоро',
		'action_edit' => 'Редактировать',
		'action_note' => 'Заметка',
		'action_delete' => 'Удалить',
		'action_priority' => 'Приоритет',
		'action_move' => 'Переместить в',
		'notes' => 'Заметки:',
		'notes_show' => 'Показать',
		'notes_hide' => 'Скрыть',
		'list_new' => 'Новый список',
		'list_rename' => 'Переименовать список',
		'list_delete' => 'Удалить список',
		'list_publish' => 'Опубликовать список',
		'list_showcompleted' => 'Показать завершенные задачи',
		'list_clearcompleted' => 'Удалить завершенные задачи',
		'list_select' => 'Выбрать список',
		'list_export' => 'Экспортировать',
		'list_export_csv' => 'CSV',
		'list_export_ical' => 'iCalendar',
		'list_rssfeed' => 'RSS-лента',
		'alltags' => 'Все теги:',
		'alltags_show' => 'Показать все',
		'alltags_hide' => 'Скрыть все',
		'a_settings' => 'Настройки',
		'rss_feed' => 'RSS-лента',
		'feed_title' => '%s',
		'feed_completed_tasks' => 'Завершенные задачи',
		'feed_modified_tasks' => 'Изменившиеся задачи',
		'feed_new_tasks' => 'Новые задачи',
		'alltasks' => 'Все задачи',

		/* Settings */
		'set_header' => 'Настройки',
		'set_template' => "Template",
		'set_title' => 'Заголовок страницы',
		'set_title_descr' => '(если поле не заполнено, будет использован заголовок по-умолчанию)',
		'set_language' => 'Язык (Language)',
		'set_protection' => 'Парольная защита',
		'set_enabled_single' => 'Однопользовательский режим (только один пароль, см ниже)',
		'set_enabled_multi' => 'Многопользовательский режим',
		'set_manage_users' => 'Редактировать пользователей',
		'set_enabled' => 'Включено',
		'set_disabled' => 'Выключено',
		'set_newpass' => 'Новый пароль',
		'set_newpass_descr' => '(не заполняйте поле если не хотите менять текущий пароль)',
		'set_smartsyntax' => 'Smart syntax',
		'set_smartsyntax_descr' => '(возможность использовать синтаксис: /приоритет/ задача /теги/)',
		'set_timezone' => 'Часовой пояс',
		'set_autotag' => 'Autotagging',
		'set_autotag_descr' => '(автодобавление текущего тега из фильтра в новую задачу)',
		'set_sessions' => 'Хранилище сессий',
		'set_sessions_php' => 'PHP',
		'set_sessions_files' => 'Файлы',
		'set_firstdayofweek' => 'Первый день недели',
		'set_custom' => 'другой',
		'set_date' => 'Формат даты',
		'set_date2' => 'Формат короткой даты',
		'set_shortdate' => 'Короткая дата (в текущем году)',
		'set_clock' => 'Формат часов',
		'set_12hour' => '12-часовой',
		'set_24hour' => '24-часовой',
		'set_submit' => 'Сохранить изменения',
		'set_cancel' => 'Отмена',
		'set_showdate' => 'Показывать дату создания задачи',

		/* user management */
		'um_header' => 'Управление пользователями',
		'um_username' => 'Имя пользователя',
		'um_email' => 'E-Mail',
		'um_role' => 'Уровень доступа',
		'um_password' => 'Пароль',
		'um_rolename_1' => 'Администратор',
		'um_rolename_2' => 'Чтение и запись',
		'um_rolename_3' => 'Только чтение',
		'um_nousers' => 'Нет доступных пользователей',
		'um_createuser' => 'Создать пользователя',

	);
}
