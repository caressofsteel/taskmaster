﻿﻿<?php

/*

# Taskmaster

Taskmaster Language Pack
Language: Spanish
Original name: Español

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
		'confirmDelete' => "¿Estás seguro de querer eliminar la tarea?",
		'confirmLeave' => "Puede haber datos sin guardar ¿Seguro que quieres dejarlo?",
		'actionNoteSave' => "guardar",
		'actionNoteCancel' => "cancelar",
		'error' => "Ha ocurrido un error (click para ver detalles)",
		'denied' => "Acceso Denegado",
		'invalidpass' => "Contraseña Incorrecta",
		'invalidlogin' => "Credenciales Inválidos",
		'tagfilter' => "Etiqueta:",
		'addList' => "Crear nueva lista",
		'renameList' => "Renombrar lista",
		'deleteList' => "Se borrará la lista actual con todas sus tareas ¿Estás seguro?",
		'clearCompleted' => "Se borrarán todas las teareas completadas de la lista ¿Estás seguro?",
		'settingsSaved' => "Preferencias guardadas. Recargando...",
		'um_usercreated' => "Usuario creado",
		'um_userupdated' => "Usuario actualizado",
		'um_userdeleted' => "Usuario borrado",
		'um_createerror1' => "Datos no válidos",
		'um_createerror2' => "El nombre de usuario ya existe",
		'um_createerror3' => "No se puede crear el usuario",
		'um_updateerror1' => "No se puede crear el usuario",
		'um_deleteerror1' => "No se puede crear el usuario",
	);

	var $inc = array(
		'htab_newtask' => "Nueva tarea",
		'htab_search' => "Buscar",
		'btn_add' => "Añadir",
		'btn_search' => "Buscar",
		'advanced_add' => "Avanzado",
		'searching' => "Buscando",
		'tasks' => "Tareas",
		'taskdate_inline_created' => "creado el %s",
		'taskdate_inline_completed' => "Completado el %s",
		'taskdate_inline_duedate' => "Due %s",
		'taskdate_created' => "Creado",
		'taskdate_completed' => "Completado",
		'go_back' => "&lt;&lt; Atrás",
		'edit_task' => "Editar Tarea",
		'add_task' => "Nueva Tarea",
		'priority' => "Prioridad",
		'task' => "Tarea",
		'note' => "Nota",
		'tags' => "Etiquetas",
		'save' => "Guardar",
		'cancel' => "Cancelar",
		'password' => "Contraseña",
		'btn_login' => "Entrar",
		'a_login' => "Entrar",
		'a_logout' => "Salir",
		'public_tasks' => "Tareas Públicas",
		'tagcloud' => "Etiquetas",
		'tagfilter_cancel' => "cancelar filtro",
		'sortByHand' => "Ordenar a mano",
		'sortByPriority' => "Ordenar por prioridad",
		'sortByDueDate' => "Ordenar por fecha de vencimiento",
		'sortByDateCreated' => "Ordenar por fecha de creación",
		'sortByDateModified' => "Ordenar por fecha de modificación",
		'due' => "Due",
		'daysago' => "%d días atrás",
		'indays' => "en %d días",
		'months_short' => array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"),
		'months_long' => array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"),
		'days_min' => array("Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"),
		'days_long' => array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"),
		'today' => "hoy",
		'yesterday' => "ayer",
		'tomorrow' => "mañana",
		'f_past' => "Vencidos",
		'f_today' => "Hoy y mañana",
		'f_soon' => "Pronto",
		'action_edit' => "Editar",
		'action_note' => "Editar Nota",
		'action_delete' => "Borrar",
		'action_priority' => "Prioridad",
		'action_move' => "Mover a",
		'notes' => "Notas:",
		'notes_show' => "Mostrar",
		'notes_hide' => "Ocultar",
		'list_new' => "Nueva lista",
		'list_rename' => "Renombrar lista",
		'list_delete' => "Borrar lista",
		'list_publish' => "Publicar lista",
		'list_showcompleted' => "Mostrar tareas completadas",
		'list_clearcompleted' => "Limpiar tareas completadas",
		'list_select' => "Elegir lista",
		'list_export' => "Exportar",
		'list_export_csv' => "CSV",
		'list_export_ical' => "iCalendar",
		'list_rssfeed' => "RSS Feed",
		'alltags' => "Todas las etiquetas:",
		'alltags_show' => "Mostrar todo",
		'alltags_hide' => "Ocultar todo",
		'a_settings' => "Preferencias",
		'rss_feed' => "RSS Feed",
		'feed_title' => "%s",
		'feed_completed_tasks' => "Tareas completadas",
		'feed_modified_tasks' => "Tareas modificadas",
		'feed_new_tasks' => "Tareas nuevas",
		'alltasks' => "Todas las tareas",

		/* Settings */
		'set_header' => "Preferencias",
		'set_template' => "Template",
		'set_title' => "Título",
		'set_title_descr' => "(sólo si quieres cambiar el título por defecto)",
		'set_language' => "Idioma",
		'set_protection' => "Protección bajo Contraseña",
		'set_enabled_single' => "Usuario único (sólo una contraseña, ver abajo)",
		'set_enabled_multi' => "Varios Usuarios",
		'set_manage_users' => "Administrar usuarios",
		'set_enabled' => "Activado",
		'set_disabled' => "Desactivado",
		'set_newpass' => "Nueva Contraseña",
		'set_newpass_descr' => "(Déjalo en blanco si no quieres cambiar la contraseña actual)",
		'set_smartsyntax' => "Sintaxis inteligente",
		'set_smartsyntax_descr' => "(/prioridad/ tarea /etiquetas/)",
		'set_timezone' => "Zona Horaria",
		'set_autotag' => "Autoetiquetar",
		'set_autotag_descr' => "(añade etiquetas automáticamente a las nuevas tareas que crees)",
		'set_sessions' => "Session handling mechanism",
		'set_sessions_php' => "PHP",
		'set_sessions_files' => "Archivos",
		'set_firstdayofweek' => "Primer día de la semana",
		'set_custom' => "Personalizado",
		'set_date' => "Formato de Fecha",
		'set_date2' => "OrdenarFormato de Fecha",
		'set_shortdate' => "Ordenar Fecha (año actual)",
		'set_clock' => "Formato Horario",
		'set_12hour' => "12-horas",
		'set_24hour' => "24-horas",
		'set_submit' => "Guardar Cambios",
		'set_cancel' => "Cancelar",
		'set_showdate' => "Mostrar fecha en la lista de tareas",

		/* user management */
		'um_header' => "Administración de Usuarios",
		'um_username' => "Nombre",
		'um_email' => "Email",
		'um_role' => "Permisos",
		'um_password' => "Contraseña",
		'um_rolename_1' => "Administrador",
		'um_rolename_2' => "Leer/Escribir",
		'um_rolename_3' => "Solo lectura",
		'um_nousers' => "No hay usuarios disponibles",
		'um_createuser' => "Crear usuario",

	);
}
