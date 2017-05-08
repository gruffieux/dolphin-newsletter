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
    '_mg_newsl_settings' => 'Réglages',
    '_mg_newsl_campagnes' => 'Campagnes',
    '_mg_newsl_membres' => 'Membres',
    '_mg_newsl_contenus' => 'Contenus',
    '_mg_newsl_campagneMembres' => 'Membres de la campagne',
    '_mg_newsl_campagneContenus' => 'Contenus de la campagne',
    '_mg_newsl_campagne_insert' => 'Nouvelle campagne',
    '_mg_newsl_campagne_nom' => 'Nom',
    '_mg_newsl_campagne_desc' => 'Descriptif',
    '_mg_newsl_campagne_dateEcheance' => 'Date d\'échéance',
    '_mg_newsl_campagne_active' => 'Active',
    '_mg_newsl_campagne_modify' => 'Modifier campagnes',
    '_mg_newsl_addContenu' => 'Ajouter un contenu',
    '_mg_newsl_addCampagneMembre' => 'Associer des membres',
    '_mg_newsl_addCampagneContenu' => 'Associer des contenus',
    '_mg_newsl_membre_insert' => 'Nouveau membre',
    '_mg_newsl_membre_modify' => 'Modifier membre',
    '_mg_newsl_contenu_modify' => 'Modifier contenu',
    '_mg_newsl_contenu_titre' => 'Titre',
    '_mg_newsl_contenu_corps' => 'Corps',
    '_mg_newsl_err_campagne_nom' => 'Veuillez indiquer le nom de la campagne',
    '_mg_newsl_err_contenu_titre' => 'Veuillez indiquer le titre du contenu',
    '_mg_newsl_inscription' => 'Inscription aux Newsletters',
    '_mg_newsl_membre_import' => 'Importation',
    '_mg_newsl_membre_import_file' => 'Fichier CSV',
    '_mg_newsl_err_membre_import_file' => 'Impossible d\'ouvrir le fichier CSV.',
    '_mg_newsl_err_membre_import_format' => 'Le format du fichier est incorrecte.',
    '_mg_newsl_contenu_campagne' => 'Campagne',
    '_mg_newsl_membre_source' => 'ID source',
    '_mg_newsl_membre_adresse' => 'Adresse',
    '_mg_newsl_membre_complAdr' => 'Complément d\'adresse',
    '_mg_newsl_send' => 'Envoyer les newsletters',
    '_mg_newsl_subscribe' => 'Inscrire',
    '_mg_newsl_campagneMembre_dateInscription' => 'Dernière inscription',
    '_mg_newsl_campagneMembre_dateDesinscription' => 'Dernière désinscription',
    '_mg_newsl_subscriptionLog' => 'Historique des inscriptions',
    '_mg_newsl_info_subscriptionLog' => 'Voir l\'historique des inscriptions',
    '_mg_newsl_mail_infoText' => 'Vous avez reçu ce message car votre adresse e-mail {0} est enregistrée pour recevoir des informations de ' . $_SERVER['HTTP_HOST'] . '. Pour accéder aux préférences de langue ou se désinscrire, suivez <a href="http://' . $_SERVER['HTTP_HOST'] . '/m/newsletter/home/{1}">ce lien</a>.',
    '_mg_newsl_confirmDelCampagne' => 'Supprimer la campagne?',
    '_mg_newsl_confirmDelMembre' => 'Supprimer le membre?',
    '_mg_newsl_confirmDelContenu' => 'Supprimer le contenu?',
    '_mg_newsl_confirmDelMe' => 'Voulez-vous vraiment supprimer votre e-mail de toutes les inscriptions newsletter?',
    '_mg_newsl_deleted' => 'Votre e-mail a été supprimé de notre base de données, vous ne recevrez plus de newsletter.',
    '_mg_newsl_settingsAndLanguages' => 'Paramètres & langues',
    '_mg_newsl_invitation' => "Inscrivez vos amis à la newsletter",
    '_mg_newsl_invitation_brief' => 'Vous recevez cet e-mail car %s %s vous a invité à suivre une newsletter ' . $_SERVER['HTTP_HOST'],
    '_mg_newsl_subscribe_success' => "Inscription réussie",
    '_mg_newsl_subscribe_failure' => "Inscription échouée",
    '_mg_newsl_membre_botScore' => "Bot score",
    '_mg_newsl_regards' => 'Meilleures salutations',
    '_mg_newsl_warning' => "Cet e-mail est généré et envoyé automatiquement, svp n'y répondez pas",
    '_mg_newsl_me' => 'Préférences Newsletters',
    '_mg_newsl_contenu_insert' => 'Nouveau contenu',
    '_mg_newsl_connection' => 'me connecter',
    '_mg_newsl_ajouteInvite' => 'Ajouter un ami',
    '_mg_newsl_sep_tab' => 'Tabulation',
    '_mg_newsl_sep_comma' => 'Virgule',
    '_mg_newsl_sep_semicolon' => 'Point virgule',
    '_mg_newsl_sep_space' => 'Espace',
    '_mg_newsl_sep_other' => 'Autre',
    '_mg_newsl_sep_info' => 'La première ligne du CSV contient les noms des champs, les lignes suivantes les valeurs respectivement. Example pour ce séparateur:<br />{0}<br />{1}',
    '_mg_newsl_sep_other_info' => 'Entrez le caractère séparateur ci-dessous',
    '_mg_newsl_header_sep' => 'Caractère séparateur',
    '_mg_newsl_header_options' => 'Options',
    '_mg_newsl_detectLang' => 'Détecter la langue',
    '_mg_newsl_detectLang_info' => 'Tente de détecter la langue dans l\'e-mail',
    '_mg_newsl_save_success' => "Sauvegarde réussie",
    '_mg_newsl_confirmAction' => "Etes-vous sûre?",
    '_mg_newsl_blackList' => "Conserver dans la black-list",
    '_mg_newsl_confirmSubscribe' => "Les membres seront inscrit à la campagne sélectionnée",
    '_mg_newsl_botsCheck_info' => "Effectue une analyse de la boîte mail en prennant en compte les sujets des mails échoués retournés par votre serveur IMAP (voir réglages). Chaque fois qu'un email a échoué, son Bot score est incrémenté de 1.",
    '_mg_newsl_campagne_dateEnvoi' => 'Dernier envoi',
    '_mg_newsl_confirmSend' => 'Envoyer les mailing? ATTENTION vous n\'êtes pas en mode teste!',
    '_mg_newsl_stats' => 'Statistiques',
    '_mg_newsl_stats_from' => 'Depuis',
    '_mg_newsl_stats_hits' => 'Hits',
    '_mg_newsl_unsubscribe' => 'Se désabonner',
    '_mg_newsl_unsubscribe_success' => 'Vous avez été désabonné de la campagne',
    '_mg_newsl_campagne_echeance_info' => "Date à laquelle la campagne arrête d'être envoyée",
);

?>
