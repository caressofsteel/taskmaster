<?php

/*

# Taskmaster

Taskmaster Language Pack
Language: Français
Original name: Français

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
		'confirmDelete' => 'Êtes-vous sûr de vouloir supprimer la tâche ?',
		'confirmLeave' => 'Il peut y avoir des données non enregistrées. Voulez-vous vraiment quitter ?',
		'actionNoteSave' => 'sauvegarder',
		'actionNoteCancel' => 'annuler',
		'error' => 'Il y a eu des erreurs (cliquez pour plus de détails)',
		'denied' => 'Accès refusé',
		'invalidpass' => 'Mauvais mot de passe',
		'tagfilter' => 'Mots-clefs :',
		'addList' => 'Créer une nouvelle liste',
		'renameList' => 'Renommer la liste',
		'deleteList' => "Cela supprimera la liste actuelle avec toutes les tâches qu’elle contient.\nÊtes-vous sûr ?",
		'clearCompleted' => "Cela supprimera toutes les tâches achevées de la liste.\nÊtes-vous sûr ?",
		'settingsSaved' => 'Réglages sauvegardés. Chargement...',
	);

	var $inc = array(
		'htab_newtask' => 'Nouvelle tâche',
		'htab_search' => 'Recherche',
		'btn_add' => 'Ajouter',
		'btn_search' => 'Rechercher',
		'advanced_add' => 'Avancé',
		'searching' => 'Recherche de',
		'tasks' => 'Tâches',
		'taskdate_inline_created' => 'créée le %s',
		'taskdate_inline_completed' => 'Achevée le %s',
		'taskdate_inline_duedate' => 'Échéance %s',
		'taskdate_created' => 'Créée',
		'taskdate_completed' => 'Achevée',
		'go_back' => '&lt;&lt; Retour',
		'edit_task' => 'Éditer la tâche',
		'add_task' => 'Nouvelle tâche',
		'priority' => 'Priorité',
		'task' => 'Tâche',
		'note' => 'Note',
		'save' => 'Sauvegarder',
		'cancel' => 'Annuler',
		'password' => 'Mot de passe',
		'btn_login' => 'Connexion',
		'a_login' => 'Connexion',
		'a_logout' => 'Déconnexion',
		'public_tasks' => 'Tâches publiques',
		'tags' => 'Mots-clefs',
		'tagfilter_cancel' => 'Annuler le filtre',
		'sortByHand' => 'Trier manuellement',
		'sortByPriority' => 'Trier par priorité',
		'sortByDueDate' => 'Trier par date d’échéance',
		'sortByDateCreated' => 'Trier par date de création',
		'sortByDateModified' => 'Trier par date de modification',
		'due' => 'Échéance',
		'daysago' => 'il y a %d jours',
		'indays' => 'dans %d jours',
		'months_short' => array('Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'),
		'months_long' => array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'),
		'days_min' => array('Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'),
		'days_long' => array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'),
		'today' => 'aujourd’hui',
		'yesterday' => 'hier',
		'tomorrow' => 'demain',
		'f_past' => 'En retard',
		'f_today' => 'Aujourd’hui et demain',
		'f_soon' => 'Bientôt',
		'action_edit' => 'Éditer',
		'action_note' => 'Éditer la note',
		'action_delete' => 'Supprimer',
		'action_priority' => 'Priorité',
		'action_move' => 'Envoyer vers',
		'notes' => 'Notes :',
		'notes_show' => 'Montrer',
		'notes_hide' => 'Cacher',
		'list_new' => 'Nouvelle liste',
		'list_rename' => 'Renommer la liste',
		'list_delete' => 'Supprimer la liste',
		'list_publish' => 'Publier la liste',
		'list_showcompleted' => 'Montrer les tâches achevées',
		'list_clearcompleted' => 'Effacer les tâches achevées',
		'list_select' => 'Sélectionner la liste',
		'list_export' => 'Exporter',
		'list_export_csv' => 'CSV',
		'list_export_ical' => 'iCalendar',
		'list_rssfeed' => 'Flux RSS',
		'alltags' => 'Tous les mots-clefs :',
		'alltags_show' => 'Tout montrer',
		'alltags_hide' => 'Tout cacher',
		'a_settings' => 'Configuration',
		'rss_feed' => 'Flux RSS',
		'feed_title' => '%s',
		'feed_completed_tasks' => 'Tâches achevées',
		'feed_modified_tasks' => 'Tâches modifiées',
		'feed_new_tasks' => 'Nouvelles tâches',
		'alltasks' => 'Toutes les tâches',
		'set_header' => 'Configuration',
		'set_template' => "Template",
		'set_title' => 'Titre',
		'set_title_descr' => '(Spécifiez si vous souhaitez changer le titre par défaut)',
		'set_language' => 'Langue',
		'set_protection' => 'Protection par mot de passe',
		'set_enabled' => 'Activé',
		'set_disabled' => 'Désactivé',
		'set_newpass' => 'Nouveau mot de passe',
		'set_newpass_descr' => '(laissez blanc pour ne pas modifier le mot de passe actuel)',
		'set_smartsyntax' => 'Syntaxe rapide',
		'set_smartsyntax_descr' => '(/priorité/ tâche /mots-clefs/)',
		'set_timezone' => 'Fuseaux horaires',
		'set_autotag' => 'Mots-clefs automatiques',
		'set_autotag_descr' => '(ajoute automatiquement les mots-clefs aux nouvelles tâches parmis ceux que vous avez déjà définis)',
		'set_sessions' => 'Mécanisme de session',
		'set_sessions_php' => 'PHP',
		'set_sessions_files' => 'Fichiers',
		'set_firstdayofweek' => 'Premier jour de la semaine',
		'set_custom' => 'Personnalisé',
		'set_date' => 'Format de date',
		'set_date2' => 'Format de date court',
		'set_shortdate' => 'Date courte (année actuelle)',
		'set_clock' => 'Format de l’heure',
		'set_12hour' => '12 heures',
		'set_24hour' => '24 heures',
		'set_submit' => 'Sauvegarder la configuration',
		'set_cancel' => 'Annuler',
		'set_showdate' => 'Afficher la date dans la liste',
	);
}
