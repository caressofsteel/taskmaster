﻿﻿<?php

/*

# Taskmaster

Taskmaster Language Pack
Language: Português
Original name: Português

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
		'confirmDelete' => "Você tem certeza que deseja apagar esta tarefa?",
		'confirmLeave' => "Existem informações que não foram salvas. Você quer realmente sair?",
		'actionNoteSave' => "salvar",
		'actionNoteCancel' => "cancelar",
		'error' => "Ocorreu um erro (clique para mais detalhes)",
		'denied' => "Acesso negado",
		'invalidpass' => "Senha inválida",
		'invalidlogin' => "Credenciais inválidos",
		'tagfilter' => "Tag:",
		'addList' => "Criar nova lista",
		'renameList' => "Renomear lista",
		'deleteList' => "Isto irá apagar a lista atual com todas as tarefas.\nVocê quer continuar?",
		'clearCompleted' => "Isto irá apagar todas as tarefas completas nesta lista.\nVocê quer continuar?",
		'settingsSaved' => "Configurações salvas. Recarregando...",
		'um_usercreated' => "Usuário criado",
		'um_userupdated' => "Usuário atualizado",
		'um_userdeleted' => "Usuário apagado",
		'um_createerror1' => "Informação inválida",
		'um_createerror2' => "Nome de usuário já existente",
		'um_createerror3' => "Não foi possível criar o usuário",
		'um_updateerror1' => "Não foi possível atualizar o usuário",
		'um_deleteerror1' => "Não foi possível apagar o usuário",
	);

	var $inc = array(
		'htab_newtask' => "Nova tarefa",
		'htab_search' => "Pesquisar",
		'btn_add' => "Adicionar",
		'btn_search' => "Pesquisar",
		'advanced_add' => "Avançado",
		'searching' => "Pesquisar por",
		'tasks' => "Tarefas",
		'taskdate_inline_created' => "criado em %s",
		'taskdate_inline_completed' => "Completado em %s",
		'taskdate_inline_duedate' => "Atraso %s",
		'taskdate_created' => "Criado",
		'taskdate_completed' => "Completado",
		'go_back' => "&lt;&lt; Voltar",
		'edit_task' => "Editar tarefa",
		'add_task' => "Nova tarefa",
		'priority' => "Prioridade",
		'task' => "Tarefa",
		'note' => "Nota",
		'tags' => "Tags",
		'save' => "Salvar",
		'cancel' => "Cancelar",
		'password' => "Senha",
		'btn_login' => "Login",
		'a_login' => "Login",
		'a_logout' => "Sair",
		'public_tasks' => "Tarefas públicas",
		'tagcloud' => "Tags",
		'tagfilter_cancel' => "cancelar filtro",
		'sortByHand' => "Ordenar manualmente",
		'sortByPriority' => "Ordenar por prioridade",
		'sortByDueDate' => "Ordenar por data de vencimento",
		'sortByDateCreated' => "Ordenar por data de criação",
		'sortByDateModified' => "Ordenar por data de modificação",
		'due' => "Atraso",
		'daysago' => "%d dias atrás",
		'indays' => "em %d dias",
		'months_short' => array("Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"),
		'months_long' => array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"),
		'days_min' => array("Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"),
		'days_long' => array("Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"),
		'today' => "hoje",
		'yesterday' => "ontem",
		'tomorrow' => "amanhã",
		'f_past' => "Atrasado",
		'f_today' => "Hoje e amanhã",
		'f_soon' => "Breve",
		'action_edit' => "Editar",
		'action_note' => "Editar Nota",
		'action_delete' => "Apagar",
		'action_priority' => "Prioridade",
		'action_move' => "Mover para",
		'notes' => "Notas:",
		'notes_show' => "Mostrar",
		'notes_hide' => "Esconder",
		'list_new' => "Nova lista",
		'list_rename' => "Renomear lista",
		'list_delete' => "Apagar lista",
		'list_publish' => "Publicar lista",
		'list_showcompleted' => "Mostrar tarefas completadas",
		'list_clearcompleted' => "Limpar tarefas completadas",
		'list_select' => "Selecionar lista",
		'list_export' => "Exportar",
		'list_export_csv' => "CSV",
		'list_export_ical' => "iCalendar",
		'list_rssfeed' => "Feed RSS",
		'alltags' => "Todas as tags:",
		'alltags_show' => "Mostrar tudo",
		'alltags_hide' => "Esconder tudo",
		'a_settings' => "Configurações",
		'rss_feed' => "Feed RSS",
		'feed_title' => "%s",
		'feed_completed_tasks' => "Tarefas completadas",
		'feed_modified_tasks' => "Tarefas modificadas",
		'feed_new_tasks' => "Novas tarefas",
		'alltasks' => "Todas as tarefas",

		/* Settings */
		'set_header' => "Configurações",
		'set_template' => "Template",
		'set_title' => "Título",
		'set_title_descr' => "(caso queira modificar o título padrão)",
		'set_language' => "Idioma",
		'set_protection' => "Proteção por senha",
		'set_enabled_single' => "Usuário único (apenas uma senha, veja abaixo)",
		'set_enabled_multi' => "Vários usuários",
		'set_manage_users' => "Gerenciar usuários",
		'set_enabled' => "Habilitado",
		'set_disabled' => "Disabilitado",
		'set_newpass' => "Nova senha",
		'set_newpass_descr' => "(deixe em branco caso não queira mudar a senha atual)",
		'set_smartsyntax' => "Sintaxe inteligente",
		'set_smartsyntax_descr' => "(/pioridade/ tarefa /tags/)",
		'set_timezone' => "Fuso horário",
		'set_autotag' => "Tag automática",
		'set_autotag_descr' => "(adicionar tags automaticamente no filtro das novas tarefas que são criadas)",
		'set_sessions' => "Mecanismo de gerenciamento de sessões",
		'set_sessions_php' => "PHP",
		'set_sessions_files' => "Arquivo",
		'set_firstdayofweek' => "Primeiro dia da semana",
		'set_custom' => "Personalizado",
		'set_date' => "Formato de data",
		'set_date2' => "Formato de data abreviada",
		'set_shortdate' => "Data abreviada (ano atual)",
		'set_clock' => "Formato da hora",
		'set_12hour' => "12-horas",
		'set_24hour' => "24-horas",
		'set_submit' => "Salvar alterações",
		'set_cancel' => "Cancelar",
		'set_showdate' => "Mostrar a data da tarefa na lista",

		/* user management */
		'um_header' => "Gerenciamento de usuários",
		'um_username' => "Nome de usuário",
		'um_email' => "E-Mail",
		'um_role' => "Nível de acesso",
		'um_password' => "Senha",
		'um_rolename_1' => "Administrador",
		'um_rolename_2' => "Ler/Escrever",
		'um_rolename_3' => "apenas leitura",
		'um_nousers' => "Nenhum usuário disponível",
		'um_createuser' => "Criar usuário",

	);
}
