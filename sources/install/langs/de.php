<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

$sLangCategory = 'Newsletter';

$aLangContent = array(
    '_mg_newsl' => 'Newsletter',
    '_mg_newsl_settings' => 'Einstellungen',
    '_mg_newsl_campagnes' => 'Kampagnen',
    '_mg_newsl_membres' => 'Mitglieder',
    '_mg_newsl_contenus' => 'Inhalte',
    '_mg_newsl_campagneMembres' => 'Mitglieder der Kampagne',
    '_mg_newsl_campagneContenus' => 'Inhalte der Kampagne',
    '_mg_newsl_campagne_insert' => 'Neue Kampagne',
    '_mg_newsl_campagne_nom' => 'Name',
    '_mg_newsl_campagne_desc' => 'Beschreibung',
    '_mg_newsl_campagne_dateEcheance' => 'Ablaufdatum',
    '_mg_newsl_campagne_active' => 'Aktiv',
    '_mg_newsl_campagne_modify' => 'Kampagnen ändern',
    '_mg_newsl_addContenu' => 'Inhalt hinzufügen',
	'_mg_newsl_addCampagneMembre' => 'Mitglieder verknüpfen',
	'_mg_newsl_addCampagneContenu' => 'Inhalte verknüpfen',
	'_mg_newsl_membre_insert' => 'Neues Mitglied',
	'_mg_newsl_membre_modify' => 'Mitglied ändern',
	'_mg_newsl_contenu_modify' => 'Inhalt ändern',
    '_mg_newsl_contenu_titre' => 'Titel',
	'_mg_newsl_contenu_corps' => 'Nachricht',
	'_mg_newsl_err_campagne_nom' => 'Bitte den Namen der Kampagne angeben',
	'_mg_newsl_err_contenu_titre' => 'Bitte den Titel zu diesem Inhalt angeben',
	'_mg_newsl_inscription' => 'Newsletter-Anmeldung',
	'_mg_newsl_membre_import' => 'Import',
	'_mg_newsl_membre_import_file' => 'CSV-Datei',
    '_mg_newsl_err_membre_import_file' => 'Die CSV-Datei konnte nicht geöffnet werden.',
	'_mg_newsl_err_membre_import_format' => 'Falsches Dateiformat.',
	'_mg_newsl_contenu_campagne' => 'Kampagne',
	'_mg_newsl_membre_source' => 'Ursprungs-ID',
	'_mg_newsl_membre_adresse' => 'Adresse',
	'_mg_newsl_membre_complAdr' => 'Adresszusatz',
	'_mg_newsl_send' => 'Newsletter senden',
	'_mg_newsl_subscribe' => 'Anmeldung',
	'_mg_newsl_campagneMembre_dateInscription' => 'Letzte Anmeldung',
	'_mg_newsl_campagneMembre_dateDesinscription' => 'Letzte Abmeldung',
	'_mg_newsl_subscriptionLog' => 'Anmeldungsverlauf',
	'_mg_newsl_info_subscriptionLog' => 'Anmeldungsverlauf anzeigen',
	'_mg_newsl_mail_infoText' => 'Du erhältst diese Nachricht, weil deine E-Mail-Adresse {0} für Informationen von www.mensgo.com registriert ist. Über <a href=“http://www.mensgo.com/m/newsletter/home/{1}“>diesen Link</a> kannst du die Sprache auswählen oder dich abmelden.',
	'_mg_newsl_confirmDelCampagne' => 'Kampagne löschen?',
	'_mg_newsl_confirmDelMembre' => 'Mitglied löschen?',
    '_mg_newsl_confirmDelContenu' => 'Inhalt löschen?',
	'_mg_newsl_confirmDelMe' => 'Möchtest du deine E-Mail-Adresse wirklich von allen Newslettern abmelden?',
	'_mg_newsl_deleted' => 'Wir haben deine E-Mail-Adresse in unserer Datenbank gelöscht, und du wirst keine weiteren Newsletter mehr bekommen.',
	'_mg_newsl_settingsAndLanguages' => 'Einstellungen und Sprachen',
	'_mg_newsl_invitation' => 'Deine Freunde für den Newsletter eintragen',
	'_mg_newsl_invitation_brief' => 'Du erhältst diese E-Mail-Nachricht, weil %s %s dich für den Newsletter von www.mensgo.com eingetragen hat.',
	'_mg_newsl_subscribe_success' => 'Anmeldung erfolgreich',
	'_mg_newsl_subscribe_failure' => 'Anmeldung fehlgeschlagen',
	'_mg_newsl_membre_botScore' => 'Bot-Score',
	'_mg_newsl_regards' => 'Mit freundlichen Grüßen',
	'_mg_newsl_warning' => 'Diese E-Mail-Nachricht wurde automatisch erstellt und verschickt – bitte nicht darauf antworten.',
	'_mg_newsl_me' => 'Newsletter-Einstellungen',
	'_mg_newsl_contenu_insert' => 'Neuer Inhalt',
	'_mg_newsl_connection' => 'Mich einloggen',
	'_mg_newsl_ajouteInvite' => 'Einen Freund hinzufügen',
	'_mg_newsl_sep_tab' => 'Tabstoppzeichen',
	'_mg_newsl_sep_comma' => 'Komma',
	'_mg_newsl_sep_semicolon' => 'Semikolon',
	'_mg_newsl_sep_space' => 'Leerzeichen',
	'_mg_newsl_sep_other' => 'Andere',
	'_mg_newsl_sep_info' => 'Die erste Zeile einer CSV-Datei enthält die Feldbezeichnungen und die folgenden Zeilen die jeweiligen Werte. Beispiel für dieses Trennzeichen:<br />{0}<br />{1}',
	'_mg_newsl_sep_other_info' => 'Hier das Trennzeichen eingeben.',
	'_mg_newsl_header_sep' => 'Trennzeichen',
	'_mg_newsl_header_options' => 'Optionen',
	'_mg_newsl_detectLang' => 'Sprache erkennen',
    '_mg_newsl_detectLang_info' => 'Versuche, die Sprache der E-Mail zu erkennen',
	'_mg_newsl_save_success' => 'Datensicherung erfolgreich',
	'_mg_newsl_confirmAction' => 'Bist du sicher?',
	'_mg_newsl_blackList' => 'In der Black List abspeichern',
    '_mg_newsl_confirmSubscribe' => 'Die Mitglieder werden in der ausgewählten Kampagne gespeichert',
	'_mg_newsl_botsCheck_info' => 'Bitte analysiere deinen E-Mail-Eingang und berücksichtige dabei die Betreffzeilen der nicht zugestellten und von deinem IMAP-Server zurückgeschickten E-Mails (siehe Einstellungen). Bei jeder nicht zugestellten E-Mail wird ihr Bot-Score um 1 erhöht.',
	'_mg_newsl_campagne_dateEnvoi' => 'Letzter Versand',
	'_mg_newsl_confirmSend' => 'Mailings verschicken? ACHTUNG, du bist nicht im Testmodus!',
	'_mg_newsl_stats' => 'Statistiken',
    '_mg_newsl_stats_from' => 'Seit',
	'_mg_newsl_stats_hits' => 'Treffer',
	'_mg_newsl_unsubscribe' => 'Sich abmelden',
	'_mg_newsl_unsubscribe_success' => 'Du wurdest von der Kampagne abgemeldet',
    '_mg_newsl_campagne_echeance_info' => "Date when stop sending campaign",
);

?>