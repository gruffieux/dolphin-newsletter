<?php
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

bx_import('BxDolModule');
bx_import('BxDolProfileFields');
require_once('MgNewsletterCron.php');
require_once('MgNewsletterFormView.php');

class MgNewsletterModule extends BxDolModule {
    const MAX_PER_PAGE = 100;
    const MIN_INVITATION = 3;
    const MAX_INVITATION = 10;
    
    private $_campagneFormCounter;
    private $_membreFormCounter;
    private $_contenuFormCounter;
    
    function MgNewsletterModule(&$aModule) {        
        parent::BxDolModule($aModule);
        
        $this->_campagneFormCounter = $this->_membreFormCounter = $this->_contenuFormCounter = 0;
    }
    
    function actionAddCampagneContenus($idCampagne, $contenuStr) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $ids = explode(",", $contenuStr);
        
        foreach ($ids as $idContenu) {
            $this->_oDb->insertCampagneContenu($idCampagne, $idContenu);
        }
    }
    
    function actionAddCampagneMembres($idCampagne, $membreStr) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $ids = explode(",", $membreStr);
        
        foreach ($ids as $idMembre) {
            $this->_oDb->subscribeMembreCampagne($idCampagne, $idMembre);
        }
    }
    
    function actionAdministration($sUrl="campagnes") {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        session_start();
        
        $this->_oTemplate->pageStart();
        
        $aMenu = array(
            'campagnes' => array(
                'title' => _t('_mg_newsl_campagnes'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/campagnes',
                '_func' => array (),
            ),
            'membres' => array(
                'title' => _t('_mg_newsl_membres'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/membres',
                '_func' => array (),
            ),
            'contenus' => array(
                'title' => _t('_mg_newsl_contenus'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/contenus',
                '_func' => array (),
            ),
            'stats' => array(
                'title' => _t('_mg_newsl_stats'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/stats',
                '_func' => array (),
            ),
            'settings' => array(
                'title' => _t('_mg_newsl_settings'),
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array (),
            )
        );

        if (empty($aMenu[$sUrl])) {
            $sUrl = 'campagnes';
        }

        $aMenu[$sUrl]['active'] = 1;
        //$sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
        
        $iPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $iPerPage = isset($_GET['per_page']) ? $_GET['per_page'] : self::MAX_PER_PAGE;
        $urlRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/" . $sUrl . "&page=" . $iPage . "&per_page=" . $iPerPage;
        $_SESSION['mg_newsl_admin']['perPage'] = $iPerPage;
        
        if (isset($_GET['err'])) {
            $sResult = '<p><font color="red">' . _t('_mg_newsl_err_' . $_GET['err']) . '</font></p>';
            echo $this->_oTemplate->adminBlock($sResult, _t('_Error')); // display box
        }
        
        if ($sUrl == "membres") {
            $aJs = array(
                BX_DOL_URL_MODULES.'mensgo/newsletter/js/adminMembres.js'
            );
            $this->_oTemplate->addJsAdmin($aJs);
            
            if (isset($_REQUEST['actionMembres']) && !empty($_REQUEST['membreStr'])) {
                $ids = explode(",", $_REQUEST['membreStr']);
                foreach ($ids as $idMembre) {
                    switch ($_REQUEST['actionMembres']) {
                        case "subscribeMembres":
                            $this->_oDb->subscribeMembreCampagne($_REQUEST['idCampagne'], $idMembre);
                            break;
                        case "deleteMembres":
                            if ($_REQUEST['blackList']) {
                                $aMembre = $this->_oDb->getMembre($idMembre);
                                $this->_oDb->insertBlackMail($aMembre);
                            }
                            $this->_oDb->deleteMembre($idMembre);
                            break;
                    }
                }
                header("Location: " . $urlRedirect);
                exit;
            }
            $sResult = '
            <div id="mg_newsl_membres">';
            if (isset($_REQUEST['q'])) {
                $sResult .= $this->getSearchMembres($_REQUEST['q'], $_REQUEST['idLangueSearch'], $_REQUEST['idCampagneSearch']);
            }
            else {
                $sResult .= $this->getPaginateMembres($iPage, $iPerPage);
            }
            $sResult .= '
            </div>';
            $aEditForm = $this->getMembreForm();
            $oEditForm = new BxTemplFormView($aEditForm);
            $oEditForm->initChecker();
            if ($oEditForm->isSubmittedAndValid()) {
                $aMembre = array(
                    'Email' => process_db_input($_REQUEST['Membre_Email']),
                    'Nom' => process_db_input($_REQUEST['Membre_Nom']),
                    'Prenom' => process_db_input($_REQUEST['Membre_Prenom']),
                    'DateNaissance' => process_db_input($_REQUEST['Membre_DateNaissance']),
                    'Pays' => process_db_input($_REQUEST['Membre_Pays']),
                    'Ville' => process_db_input($_REQUEST['Membre_Ville']),
                    'Zip' => process_db_input($_REQUEST['Membre_Zip']),
                    'IDLangue' => $_REQUEST['Membre_IDLangue'],
                    //'IDSource' => process_db_input($_REQUEST['Membre_IDSource']),
                    'Sexe' => process_db_input($_REQUEST['Membre_Sexe']),
                    'Adresse' => process_db_input($_REQUEST['Membre_Adresse']),
                    'ComplementAdresse' => process_db_input($_REQUEST['Membre_ComplAdr']),
                    'Telephone' => process_db_input($_REQUEST['Membre_Telephone']),
                );
                $this->_oDb->updateMembre($_REQUEST['Membre_ID'], $aMembre);
                header("Location: " . $urlRedirect);
                exit;
            }
            $aVars = array(
                'membreForm' => $oEditForm->getCode(),
            );
            unset($oEditForm);
            echo $this->_oTemplate->parseHtmlByName('membreForm', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('membreCampagne', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('confirmDelMembre', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('confirmAction', $aVars);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_membres'), $aMenu); // display box
            
            $aImportForm = $this->getMembreImportForm();
            $oImportForm = new BxTemplFormView($aImportForm);
            $oImportForm->initChecker();
            if($oImportForm->isSubmittedAndValid()) {
                $separator = empty($_REQUEST['Membre_Import_Separator']) ? $_REQUEST['Membre_Import_Sep_Char'] : $_REQUEST['Membre_Import_Separator'];
                $iResult = $this->_oDb->importCSV($separator, isset($_REQUEST['Membre_Import_DetectLang']), $_FILES['Membre_Import_File']['tmp_name']);
                switch ($iResult) {
                    case 1:
                        header("Location: " . $urlRedirect . "&err=membre_import_file");
                        break;
                    case 2:
                        header("Location: " . $urlRedirect . "&err=membre_import_format");
                        break;
                    default:
                        header("Location: " . $urlRedirect);
                        break;
                }
                exit;
            }
            $sResult = $oImportForm->getCode();
            unset($oImportForm);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_membre_import')); // display box
            
            $aInsertForm = $this->getMembreForm();
            $oInsertForm = new BxTemplFormView($aInsertForm);
            $oInsertForm->initChecker();
            if($oInsertForm->isSubmittedAndValid()) {
                $aMembre = array(
                    'Email' => process_db_input($_REQUEST['Membre_Email']),
                    'Nom' => process_db_input($_REQUEST['Membre_Nom']),
                    'Prenom' => process_db_input($_REQUEST['Membre_Prenom']),
                    'DateNaissance' => process_db_input($_REQUEST['Membre_DateNaissance']),
                    'Pays' => process_db_input($_REQUEST['Membre_Pays']),
                    'Ville' => process_db_input($_REQUEST['Membre_Ville']),
                    'Zip' => process_db_input($_REQUEST['Membre_Zip']),
                    'IDLangue' => $_REQUEST['Membre_IDLangue'],
                    //'IDSource' => process_db_input($_REQUEST['Membre_IDSource']),
                    'Sexe' => process_db_input($_REQUEST['Membre_Sexe']),
                    'Adresse' => process_db_input($_REQUEST['Membre_Adresse']),
                    'ComplementAdresse' => process_db_input($_REQUEST['Membre_ComplAdr']),
                    'Telephone' => process_db_input($_REQUEST['Membre_Telephone']),
                );
                $this->serviceInscription($_REQUEST['Membre_Email'], $aMembre);
                header("Location: " . $urlRedirect);
                exit;
            }
            $sResult = $oInsertForm->getCode();
            unset($oInsertForm);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_membre_insert')); // display box
        }
        elseif ($sUrl == "contenus") {
            $aJs = array(
                BX_DOL_URL_MODULES.'mensgo/newsletter/js/adminContenus.js'
            );
            $this->_oTemplate->addJsAdmin($aJs);
            
            $sResult = $this->getPaginateContenus($iPage, $iPerPage);
            $aContenuForm = $this->getContenuForm();
            $oContenuForm = new BxTemplFormView($aContenuForm);
            $oContenuForm->initChecker();
            if ($oContenuForm->isSubmittedAndValid()) {
                $this->_oDb->updateContenu($_REQUEST['Contenu_ID'], process_db_input($_REQUEST['Contenu_Titre']), process_db_input($_REQUEST['Contenu_Corps']), $_REQUEST['Contenu_IDLangue']);
                header("Location: " . $urlRedirect);
                exit;
            }
            $aVars = array(
                'contenuForm' => $oContenuForm->getCode(),
            );
            unset($oContenuForm);
            echo $this->_oTemplate->parseHtmlByName('contenuForm', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('confirmDelContenu', $aVars);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_contenus'), $aMenu); // display box
            
            $aInsertForm = $this->getContenuForm();
            $oInsertForm = new BxTemplFormView($aInsertForm);
            $oInsertForm->initChecker();
            if($oInsertForm->isSubmittedAndValid()) {
                $this->_oDb->insertContenu(process_db_input($_REQUEST['Contenu_Titre']), process_db_input($_REQUEST['Contenu_Corps']), $_REQUEST['Contenu_IDLangue']);
                header("Location: " . $urlRedirect);
                exit;
            }
            $sResult = $oInsertForm->getCode();
            unset($oInsertForm);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_contenu_insert')); // display box
        }
        elseif ($sUrl == "stats") {
            $aCss = array(
                BX_DOL_URL_PLUGINS.'jquery/themes/jquery-ui.css',
                BX_DOL_URL_PLUGINS.'jquery/themes/jquery-ui-timepicker-addon.css'
            );
            $this->_oTemplate->addCssAdmin($aCss);
            $aJs = array(
                BX_DOL_URL_PLUGINS.'jquery/jquery.ui.all.min.js',
                BX_DOL_URL_PLUGINS.'jquery/jquery-ui-timepicker-addon.js',
                BX_DOL_URL_MODULES.'mensgo/newsletter/js/adminStats.js'
            );
            $this->_oTemplate->addJsAdmin($aJs);
            $sResult = '
            <div id="mg_newsl_stats">';
            if (isset($_REQUEST['q']) OR isset($_REQUEST['IDCampagne']) OR isset($_REQUEST['from'])) {
                $str = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
                $idCampagne = isset($_REQUEST['IDCampagne']) ? $_REQUEST['IDCampagne'] : '';
                $from = isset($_REQUEST['from']) ? $_REQUEST['from'] : '';
                $sResult .= $this->getSearchStats($str, $idCampagne, $from);
            }
            else {
                $sResult .= $this->getPaginateStats($iPage, $iPerPage);
            }
            $sResult .= '
            </div>';
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_stats'), $aMenu); // display box
        }
        elseif ($sUrl == 'settings') {
            $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
            if(empty($iId)) { // if category is not found display page not found
                echo MsgBox(_t('_sys_request_page_not_found_cpt'));
                $this->_oTemplate->pageCodeAdmin (_t('_mg_newsl'));
                return;
            }
            bx_import('BxDolAdminSettings'); // import class
            $mixedResult = '';
            if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
                $oSettings = new BxDolAdminSettings($iId);
                $mixedResult = $oSettings->saveChanges($_POST);
            }
            $oSettings = new BxDolAdminSettings($iId); // get display form code
            $sResult = $oSettings->getForm();
            if($mixedResult !== true && !empty($mixedResult)) { // attach any resulted messages at the form beginning
                $sResult = $mixedResult . $sResult;
            }
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_settings'), $aMenu); // dsiplay box
        }
        else {
            $aJs = array(
                BX_DOL_URL_MODULES.'mensgo/newsletter/js/adminCampagnes.js'
            );
            $this->_oTemplate->addJsAdmin($aJs);
            
            $sResult = $this->getPaginateCampagnes($iPage, $iPerPage);
            $aEditForm = $this->getCampagneForm();
            $oEditForm = new BxTemplFormView($aEditForm);
            $oEditForm->initChecker();
            if($oEditForm->isSubmittedAndValid()) {
                $this->_oDb->updateCampagne($_REQUEST['Campagne_ID'], process_db_input($_REQUEST['Campagne_Nom']), process_db_input($_REQUEST['Campagne_Descriptif']),
                    process_db_input($_REQUEST['Campagne_DateEcheance']));
                header("Location: " . $urlRedirect);
                exit;
            }
            $aVars = array(
                'campagneForm' => $oEditForm->getCode(),
            );
            unset($oEditForm);
            echo $this->_oTemplate->parseHtmlByName('campagneForm', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('campagneContenuForm', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('campagneMembreForm', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('confirmDelCampagne', $aVars);
            $aVars = array();
            echo $this->_oTemplate->parseHtmlByName('confirmSend', $aVars);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_campagnes'), $aMenu); // display box
            
            $aInsertForm = $this->getCampagneForm();
            $oInsertForm = new BxTemplFormView($aInsertForm);
            $oInsertForm->initChecker();
            if($oInsertForm->isSubmittedAndValid()) {
                $this->_oDb->insertCampagne(process_db_input($_REQUEST['Campagne_Nom']), process_db_input($_REQUEST['Campagne_Descriptif']),
                    process_db_input($_REQUEST['Campagne_DateEcheance']));
                header("Location: " . $urlRedirect);
                exit;
            }
            $sResult = $oInsertForm->getCode();
            unset($oInsertForm);
            echo $this->_oTemplate->adminBlock($sResult, _t('_mg_newsl_campagne_insert')); // display box
        }
        
        // Ajout des css et js
        $this->_oTemplate->addCssAdmin('newsletter.css');
        
        // Affiche la page
        $this->_oTemplate->pageCodeAdmin(_t('_mg_newsl')); // output is completed, admin page will be displayed here
    }
    
    function actionBeforeSend() {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        echo $this->_oDb->getSetting("mg_newsl_test_dummy");
    }
    
    function actionBotsNew() {
        $start = 0;
        $aEmails = $aMembres = array();
        $imapBox = $this->_oDb->getSetting("mg_newsl_imap_box");
        $imapUser = $this->_oDb->getSetting("mg_newsl_imap_user");
        $imapPwd = $this->_oDb->getSetting("mg_newsl_imap_pwd");
        $sBotPattern =$this->_oDb->getSetting("mg_newsl_bot_pattern");
        $mbox = imap_open($imapBox, $imapUser, $imapPwd);
        
        // Lecture de l'indice
        $fileName = BX_DIRECTORY_PATH_MODULES . 'mensgo/newsletter/save/imapMsgIndex';
        $fp = @fopen($fileName, 'r');
        if ($fp) {
            $start = (int)fread($fp, filesize($fileName));
            fclose($fp);
        }
        
        if ($mbox) {
            $total = imap_num_msg($mbox);
            $counter = 0;
            for ($i = $start+1; $i <= $total; $i++) {
                $header = imap_header($mbox, $i);
                if (preg_match('/' . $sBotPattern . '/i', $header->Subject, $aSubjectMatches)) {
                    $body = imap_body($mbox, $i);
                    if (preg_match("/([a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4})/i", $body, $aMailMatches)) {
                        $sEmail = "'" . $aMailMatches[1] . "'";
                        if (!in_array($sEmail, $aEmails)) {
                            $aEmails[] = $sEmail;
                        }
                    }
                }
                $counter++;
            }
            imap_close($mbox);
            
            // Sauvegarde de l'indice
            if ($counter > 0) {
                $fp = fopen($fileName, 'w');
                if ($fp) {
                    fwrite($fp, $i);
                    fclose($fp);
                }
            }
        }
        
        if (!empty($aEmails)) {
            $this->_oDb->updateBotScore($aEmails);
            $aMembres = $this->_oDb->getMembresByEmails($aEmails);
        }
        
        $sResult = $this->_oTemplate->getTableMembres($aMembres, count($aMembres));
        
        echo $sResult;
    }
    
    function actionChangeCampagneState($idCampagne, $state) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $this->_oDb->updateCampagneState($idCampagne, $state);
        
        header("Location: " . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/campagnes");
    }
    
    function actionDeleteCampagne($idCampagne) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $this->_oDb->deleteCampagne($idCampagne);
    }
    
    function actionDeleteContenu($idContenu) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $this->_oDb->deleteContenu($idContenu);
    }
    
    function actionDeleteMe($hash) {
        $aMembre = $this->serviceHashToMembre($hash);
        
        if (empty($aMembre)) {
            $this->_oTemplate->displayAccessDenied();
            exit;
        }
        
        $this->_oDb->deleteMembre($aMembre['ID']);
    }
    
    function actionDeleteMembre($idMembre, $blackMail=0) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        if ($blackMail) {
            $aMembre = $this->_oDb->getMembre($idMembre);
            $this->_oDb->insertBlackMail($aMembre);
        }
        
        $this->_oDb->deleteMembre($idMembre);
    }
    
    function actionHome($hash="") {
        if (empty($hash) && $iLoggedId = getLoggedId()) {
            $aProfil = getProfileInfo($iLoggedId);
            $hash = $this->serviceMembreToHash($aProfil['Email']);
        }
        
        $this->_oTemplate->pageStart();
        
        if (isset($_GET['unsubscribe'])) {
            if ($_GET['unsubscribe'] == 'success') {
                echo MsgBox(_t('_mg_newsl_unsubscribe_success'));
            }
            else {
                echo MsgBox(_t('_Error Occured'));
            }
        }
        
        $this->serviceInscriptionBlock($hash);
        
        $this->_oTemplate->pageCode(_t('_mg_newsl_inscription'), true);
    }
    
    function actionInvitation() {
        $aCampagnes = $this->_oDb->getCampagnes(true);
        
        $this->_oTemplate->pageStart();
        
        for ($i = 0; $i < count($aCampagnes); $i++) {
            $aCampagnes[$i]['Desc'] = _t($aCampagnes[$i]['Descriptif']);
        }
        
        $aForm = $this->getInvitationForm();
        $oForm = new MgNewsletterFormView($aForm);
        $sActionText = '';
        
        // Inscription des e-mail amis
        $oForm->initChecker();
        if ($oForm->isSubmittedAndValid()) {
            for ($i = 0; $i < self::MAX_INVITATION; $i++) {
                $emailIndex = 'email' . $i;
                if (!empty($_REQUEST[$emailIndex])) {
                    $aInvite = array(
                        'Email' => $_REQUEST[$emailIndex],
                    );
                    $aIds = explode(",", $_REQUEST['CampagneStr']);
                    foreach ($aIds as $idCampagne) {
                        if ($this->serviceInscription($_REQUEST[$emailIndex], $aInvite, $idCampagne, false)) {
                            $sActionText = MsgBox( _t('_mg_newsl_subscribe_success') );
                        }
                        else {
                            $sActionText = MsgBox( _t('_mg_newsl_subscribe_failure') );
                        }
                    }
                }
            }
        }
        
        $sForm =  $oForm->getCode() . $sActionText;
        unset($oForm);
        
        $aVars = array (
            'bx_repeat:campagnes' => $aCampagnes,
            'Form' => $sForm,
        );
        
        echo $this->_oTemplate->parseHtmlByName('invitation', $aVars);
        
        $this->_oTemplate->addJs("invitation.js");
        $this->_oTemplate->pageCode(_t('_mg_newsl_invitation'), true);
    }
    
    function actionLoadCampagne($idCampagne) {
        $aCampagne = $this->_oDb->getCampagne($idCampagne);
        $json = sprintf("{\"ID\":\"%s\", \"Nom\":\"%s\", \"Descriptif\":\"%s\", \"DateEcheance\":\"%s\"}",
            $aCampagne['ID'], $aCampagne['Nom'], urlencode($aCampagne['Descriptif']), $aCampagne['DateEcheance']);
        
        echo $json;
    }
    
    function actionLoadChoosedContenus($idCampagne) {
        $sResult = '';
        
        $aContenus = $this->_oDb->getCampagneContenus($idCampagne);
        
        foreach ($aContenus as $aContenu) {
            $sResult .= '
            <option value="' . $aContenu['ID'] . '">' . $aContenu['Titre'] . '</option>';
        }
        
        echo $sResult;
    }
    
    function actionLoadChoosedMembres($idCampagne) {
        $sResult = '';
        
        $aMembres = $this->_oDb->getCampagneMembres($idCampagne);
        
        foreach ($aMembres as $aMembre) {
            //$caption = sprintf("%s %s &lt;%s&gt;", $aMembre['Prenom'], $aMembre['Nom'], $aMembre['Email']);
            $caption = $aMembre['Email'];
            $class = $this->_oDb->isSubscribed($aMembre['ID'], $idCampagne) ? "Active" : "Unactive";
            $sResult .= '
            <option class="' . $class . '" value="' . $aMembre['ID'] . '">' . $caption . '</option>';
        }
        
        echo $sResult;
    }
    
    function actionLoadContenu($idContenu) {
        $aContenu = $this->_oDb->getContenu($idContenu);
        $json = sprintf("{\"ID\":\"%s\", \"Titre\":\"%s\", \"Corps\":\"%s\", \"IDLangue\":\"%s\"}",
            $aContenu['ID'], urlencode($aContenu['Titre']), urlencode($aContenu['Corps']), $aContenu['IDLangue']);
        
        echo $json;
    }
    
    function actionLoadMembre($idMembre) {
        $aMembre = $this->_oDb->getMembre($idMembre);
        $json = sprintf("{\"ID\":\"%s\", \"Email\":\"%s\", \"Nom\":\"%s\", \"Prenom\":\"%s\", \"DateNaissance\":\"%s\", \"Pays\":\"%s\", \"IDLangue\":\"%s\", \"Ville\":\"%s\", \"Zip\":\"%s\", \"IDSource\":\"%s\", \"Sexe\":\"%s\", \"Adresse\":\"%s\", \"ComplAdr\":\"%s\", \"Telephone\":\"%s\"}",
            $aMembre['ID'], $aMembre['Email'], $aMembre['Nom'], $aMembre['Prenom'], $aMembre['DateNaissance'], $aMembre['Pays'], $aMembre['IDLangue'], $aMembre['Ville'], $aMembre['Zip'], $aMembre['IDSource'], $aMembre['Sexe'], $aMembre['Adresse'], $aMembre['ComplementAdresse'], $aMembre['Telephone']);
        
        echo $json;
    }
    
    function actionLoadMembreCampagnes($idMembre) {
        $aMembreCampagnes = $this->_oDb->getMembreCampagnes($idMembre);
        
        $sResult = '
        <table cellspacing="5">
            <th>' . _t("_mg_newsl_contenu_campagne") . '</th>
            <th>' . _t("_mg_newsl_campagneMembre_dateInscription") . '</th>
            <th>' . _t("_mg_newsl_campagneMembre_dateDesinscription") . '</th>';
            foreach ($aMembreCampagnes as $key => $aMembreCampagne) {
                $class = ($key + 2) % 2 ? "Unpair" : "Pair";
                $class .= $this->_oDb->isSubscribed($aMembreCampagne['IDMembre'], $aMembreCampagne['IDCampagne']) ? " Active" : " Unactive";
                $sResult .= '
                <tr class="' . $class . '">
                    <td>' . $aMembreCampagne['Nom'] . '</td>
                    <td>' . $aMembreCampagne['DateInscription'] . '</td>
                    <td>' . $aMembreCampagne['DateDesinscription'] . '</td>
                </tr>';
            }
            $sResult .= '
        </table>';
        
        echo $sResult;
    }
    
    function actionLoadPaginateMembres($iPage, $iPerPage) {
        $sResult = $this->getPaginateMembres($iPage, $iPerPage);
        
        echo $sResult;
    }
    
    function actionLoadSortedMembres($sortCol) {
        session_start();
        
        $_SESSION['mg_newsl_admin']['sortOrder'] = ($sortCol == $_SESSION['mg_newsl_admin']['sortCol']) ? !$_SESSION['mg_newsl_admin']['sortOrder'] : 1;
        $_SESSION['mg_newsl_admin']['sortCol'] = $sortCol;
        $sResult = $this->getPaginateMembres(1, $_SESSION['mg_newsl_admin']['perPage']);
        
        echo $sResult;
    }
    
    function actionLoadUnchoosedContenus($idCampagne) {
        $sResult = '';
        
        $aContenus = $this->_oDb->getContenus();
        $aChoosedContenus = $this->_oDb->getCampagneContenus($idCampagne);
        $total = count($aChoosedContenus);
        
        foreach ($aContenus as $aContenu) {
            for ($i = 0; $i < $total; $i++) {
                if ($aContenu['ID'] == $aChoosedContenus[$i]['ID']) {
                    break;
                }
            }
            if ($i == $total) {
                $sResult .= '
                <option value="' . $aContenu['ID'] . '">' . $aContenu['Titre'] . '</option>';
            }
        }
        
        echo $sResult;
    }
    
    function actionLoadUnchoosedMembres($idCampagne) {
        $sResult = '';
        
        $aMembres = $this->_oDb->getMembres();
        $aChoosedMembres = $this->_oDb->getCampagneMembres($idCampagne);
        $total = count($aChoosedMembres);
        
        foreach ($aMembres as $aMembre) {
            for ($i = 0; $i < $total; $i++) {
                if ($aMembre['ID'] == $aChoosedMembres[$i]['ID']) {
                    break;
                }
            }
            if ($i == $total) {
                //$caption = sprintf("%s %s &lt;%s&gt;", $aMembre['Prenom'], $aMembre['Nom'], $aMembre['Email']);
                $caption = $aMembre['Email'];
                $sResult .= '
                <option class="Active" value="' . $aMembre['ID'] . '">' . $caption . '</option>';
            }
        }
        
        echo $sResult;
    }
    
    function actionMe($hash="") {
        if (empty($hash) && $iLoggedId = getLoggedId()) {
            $aProfil = getProfileInfo($iLoggedId);
            $hash = $this->serviceMembreToHash($aProfil['Email']);
        }
        
        $this->_oTemplate->pageStart();
        
        $aMembre = $this->serviceHashToMembre($hash);
        
        // Génération du formulaire
        $aSubscribeForm = $this->getMembreForm($aMembre);
        if (getLoggedId()) {
            $aSubscribeForm['inputs']['Membre_Email']['attrs']['readonly'] = 1;
        }
        $oSubscribeForm = new BxTemplFormView($aSubscribeForm);
        $oSubscribeForm->initChecker();
        
        // Validation du formulaire
        // Inscription/Désinscription aux campagnes
        if($oSubscribeForm->isSubmittedAndValid()) {
            $aNewMembre = array(
                'Email' => process_db_input($_REQUEST['Membre_Email']),
                'Nom' => process_db_input($_REQUEST['Membre_Nom']),
                'Prenom' => process_db_input($_REQUEST['Membre_Prenom']),
                'DateNaissance' => process_db_input($_REQUEST['Membre_DateNaissance']),
                'Pays' => process_db_input($_REQUEST['Membre_Pays']),
                'Ville' => process_db_input($_REQUEST['Membre_Ville']),
                'Zip' => process_db_input($_REQUEST['Membre_Zip']),
                'IDLangue' => $_REQUEST['Membre_IDLangue'],
                //'IDSource' => process_db_input($_REQUEST['Membre_IDSource']),
                'Sexe' => process_db_input($_REQUEST['Membre_Sexe']),
                'Adresse' => process_db_input($_REQUEST['Membre_Adresse']),
                'ComplementAdresse' => process_db_input($_REQUEST['Membre_ComplAdr']),
                'Telephone' => process_db_input($_REQUEST['Membre_Telephone']),
            );
            $newHash = $this->serviceMembreToHash($aNewMembre['Email']);
            $urlRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "me/" . $newHash;
            $this->serviceInscription($aMembre['Email'], $aNewMembre);
            header("Location: " . $urlRedirect);
            exit;
        }
        
        echo $oSubscribeForm->getCode();
        
        $this->_oTemplate->pageCode(_t('_mg_newsl_me'), true);
    }
    
    function actionPreview($idCampagne) {
        $aCampagne = $this->_oDb->getCampagne($idCampagne);
        $aProfil = getProfileInfo(getLoggedId());
        
        $aContenus = $this->_oDb->getCampagneContenus($aCampagne['ID']);
        $aContenuPacks = array();
        foreach ($aContenus as $aContenu) {
            $aContenuPacks[$aContenu['IDLangue']][] = $aContenu;
        }
        
        $iLang = $aProfil['LangID'];
        if (!$iLang) {
            $iLang = 1;
        }
        
        $total = count($aContenuPacks[$iLang]);
        if (!$total) {
            $aLangs = $this->_oDb->getAll("SELECT * FROM `sys_localization_languages`");
            foreach($aLangs as $aLang) {
                $total = count($aContenuPacks[$aLang['ID']]);
                if ($total) {
                    $iLang = $aLang['ID'];
                    break;
                }
            }
        }
        
        $aContenu = $this->_oDb->getCampagneContenu($idCampagne, $iLang);
        $sResult = $aContenu['Corps'];
        
        $this->_oTemplate->pageStart();
        
        echo $sResult;
        
        $this->_oTemplate->pageCodeAdmin(_t('_mg_newsl'));
    }
    
    function actionRemoveCampagneContenus($idCampagne, $contenuStr) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $ids = explode(",", $contenuStr);
        
        foreach ($ids as $idContenu) {
            $this->_oDb->deleteCampagneContenu($idCampagne, $idContenu);
        }
    }
    
    function actionRemoveCampagneMembres($idCampagne, $membreStr) {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $ids = explode(",", $membreStr);
        
        foreach ($ids as $idMembre) {
            $this->_oDb->deleteCampagneMembre($idCampagne, $idMembre);
        }
    }
    
    function actionSend() {
        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
        
        $oCron = new MgNewsletterCron();
        
        $oCron->processing();
        
        unset($oCron);
    }
    
    function actionUnsubscribe($cid) {
        $aParts = explode('_', $cid, 2);
        $aMembre = $this->serviceHashToMembre($aParts[1]);
        $idMembre = $this->_oDb->getOne("SELECT `ID` FROM `mg_newsl_membres` WHERE `Email`='" . $aMembre['Email'] . "'");
        
        if ($aParts[0] && $idMembre) {
            $this->_oDb->deleteCampagneMembre($aParts[0], $idMembre);
            header('Location: ' . BX_DOL_URL_ROOT . 'm/newsletter/home/' . $aParts[1] . '?unsubscribe=success');
            exit;
        }
        
        header('Location: ' . BX_DOL_URL_ROOT . '?unsubscribe=failed');
        exit;
    }
    
    function getCampagneForm() {
        $submitName = 'Campagne_Save_' . $this->_campagneFormCounter;
        
        $aForm = array(
            'form_attrs' => array(
                'id' => 'Campagne_Form_' . $this->_campagneFormCounter,
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
                'db' => array(
                    'table' => 'mg_newsl_campagnes',
                    'key' => 'ID',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => $submitName,
                ),
            ),
            'inputs' => array ()
        );
        
        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'Campagne_ID' => array(
                'type' => 'hidden',
                'name' => 'Campagne_ID',
                'value' => 0,
                'db' => array (
                    'pass' => 'Int',
                ),
                'attrs' => array(
                    'id' => 'Campagne_ID',
                ),
            ),
            'Campagne_Nom' => array(
                'type' => 'text',
                'name' => 'Campagne_Nom',
                'caption' => _t("_mg_newsl_campagne_nom"),
                'value' => '',
                'db' => array (
                    'pass' => 'Xss',
                ),
                'required' => true,
                'checker' => array (  
                    'func' => 'length',
                    'params' => array(1, 50),
                    'error' => _t('_mg_newsl_err_campagne_nom'),
                ),
                'attrs' => array(
                    'id' => 'Campagne_Nom',
                ),
            ),
            'Campagne_Descriptif' => array(
                'type' => 'textarea',
                'name' => 'Campagne_Descriptif',
                'caption' => _t("_mg_newsl_campagne_desc"),
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Campagne_Descriptif',
                ),
            ),
            'Campagne_DateEcheance' => array(
                'type' => 'datetime',
                'name' => 'Campagne_DateEcheance',
                'caption' => _t("_mg_newsl_campagne_dateEcheance"),
                'info' => _t('_mg_newsl_campagne_echeance_info'),
                'db' => array (
                    'pass' => 'DateTime', 
                ),    
                'display' => 'filterDate',
                'attrs' => array(
                    'id' => 'Campagne_DateEcheance_' . $this->_campagneFormCounter, // Si un id unique pour toutes les instances ça marche pas
                    'class' => 'DatePicker',
                ),
            ),
        ));
        
        $aForm['inputs'][$submitName] = array(
            'type' => 'submit',
            'name' => $submitName,
            'value' => _t('_Save'),
        );
        
        $this->_campagneFormCounter++;
        
        return $aForm;
    }
    
    function getContenuForm() {
        $submitName = 'Contenu_Save_' . $this->_contenuFormCounter;
        
        $aForm = array(
            'form_attrs' => array(
                'id' => 'Contenu_Form_' . $this->_contenuFormCounter,
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
                'db' => array(
                    'table' => 'mg_newsl_contenus',
                    'key' => 'ID',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => $submitName
                ),
            ),
            'inputs' => array ()
        );
        
        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'Contenu_ID' => array(
                'type' => 'hidden',
                'name' => 'Contenu_ID',
                'value' => 0,
                'db' => array (
                    'pass' => 'Int',
                ),
                'attrs' => array(
                    'id' => 'Contenu_ID',
                ),
            ),
            'Contenu_Titre' => array(
                'type' => 'text',
                'name' => 'Contenu_Titre',
                'caption' => _t("_mg_newsl_contenu_titre"),
                'value' => '',
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Contenu_Titre',
                ),
            ),
            'Contenu_Corps' => array(
                'type' => 'textarea',
                'name' => 'Contenu_Corps',
                'html' => 2,
                'caption' => _t("_mg_newsl_contenu_corps"),
                'value' => '<p>' . _t('_mg_newsl_mail_infoText', '', '_hash_') . '</p>',
                'db' => array (
                    'pass' => 'XssHtml',
                ),
                'attrs' => array(
                    'id' => 'Contenu_Corps_' . $this->_contenuFormCounter,
                    'class' => 'ContenuEditor',
                ),
            ),
            'Contenu_IDLangue' => array(
                'type' => 'select',
                'name' => 'Contenu_IDLangue',
                'caption' => _t("_Language"),
                'value' => 0,
                'values' => $this->getLanguageChooser(),
                'db' => array (
                    'pass' => 'Int',
                ),
                'attrs' => array(
                    'id' => 'Contenu_IDLangue',
                ),
            ),
        ));
        
        $aForm['inputs'][$submitName] = array(
            'type' => 'submit',
            'name' => $submitName,
            'value' => _t('_Save'),
        );
        
        $this->_contenuFormCounter++;
        
        return $aForm;
    }
    
    function getInscriptionForm() {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'Inscription_Save',
                'class' => 'MembreCampagne_Form',
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
            ),
            'inputs' => array ()
        );
        
        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'MembreCampagne_Str' => array(
                'type' => 'hidden',
                'name' => 'MembreCampagne_Str',
                'value' => '',
                'attrs' => array(
                    'id' => 'MembreCampagne_Str',
                ),
            ),
            'Inscription_Save' => array(
                'type' => 'submit',
                'name' => 'Inscription_Save',
                'value' => _t('_Save'),
            )
        ));
        
        return $aForm;
    }
    
    function getInvitationForm() {
        $aForm = array(
            'form_attrs' => array(
                'id' => 'invitationForm',
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
                'db' => array(
                    'table' => '',
                    'key' => '',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => 'InvitationSend',
                ),
                'checker_helper' => 'MgNewsletterFormCheckerHelper',
            ),
            'inputs' => array(
            )
        );
        
        $iInviteCounter = isset($_REQUEST['inviteCounter']) ? $_REQUEST['inviteCounter'] : self::MIN_INVITATION;
        
        for ($i = 0; $i < self::MAX_INVITATION; $i++) {
            $aForm['inputs'] = array_merge($aForm['inputs'], array(
                'Email' . $i => array(
                    'type' => 'text',
                    'name' => 'email' . $i,
                    'caption' => _t("_Email"),
                    'checker' => array(
                        'func' => 'invite',
                        'params' => array($i == 0),
                        'error' => _t("_Incorrect Email"),
                    ),
                    'attrs' => array(
                        'class' => $i < $iInviteCounter ? 'Invite' : 'InviteSup',
                    ),
                ),
            ));
        }
        
        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'inviteCounter' => array(
                'type' => 'hidden',
                'name' => 'inviteCounter',
                'value' => $iInviteCounter,
                'attrs' => array(
                    'id' => 'inviteCounter',
                ),
            ),
            'AjouteInvite' => array(
                'type' => 'custom',
                'content' => '<a id="ajouteInvite" href="javascript:;"><img src="' . $GLOBALS['oSysTemplate']->getIconUrl('ps_add_members.png') . '" title="' . _t("_mg_newsl_ajouteInvite") . '" /></a>',
            ),
            'CampagneStr' => array(
                'type' => 'hidden',
                'name' => 'CampagneStr',
                'value' => '',
                'attrs' => array(
                    'id' => 'CampagneStr',
                ),
            ),
            'InvitationSend' => array(
                'type' => 'submit',
                'name' => 'InvitationSend',
                'value' => _t('_Send'),
            ),
        ));
        
        return $aForm;
    }
    
    function getLanguageChooser() {
        $aLanguageChooser = array();
        $aLanguages = $this->_oDb->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
        
        foreach($aLanguages as $aLanguage) {
            $aLanguageChooser[] = array('key' => $aLanguage['id'], 'value' => $aLanguage['title']);
        }
        
        return $aLanguageChooser;
    }
    
    function getMembreImportForm() {
        $aFields = $this->_oDb->getFields('mg_newsl_membres');
        $aNames = array_slice($aFields['original'], 1, 6);
        $aSepNames = array(
            'tab' => implode('&emsp;', $aNames),
            'comma' => implode(',', $aNames),
            'semicolon' => implode(';', $aNames),
            'space' => implode(' ', $aNames),
        );
        $aValues = array('toto@abc.com', 'Toto', 'Albert', 'CH', 'Lausanne', '1');
        $aSepValues = array(
            'tab' => implode('&emsp;', $aValues),
            'comma' => implode(',', $aValues),
            'semicolon' => implode(';', $aValues),
            'space' => implode(' ', $aValues),
        );
        
        $aForm = array(
            'form_attrs' => array(
                'id' => 'Membre_Import_Form',
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
                'db' => array(
                    'table' => 'mg_newsl_membres',
                    'key' => 'ID',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => 'Membre_Import_Save'
                ),
            ),
            'inputs' => array (
                'header_separator' => array(
                    'type' => 'block_header',
                    'caption' => _t('_mg_newsl_header_sep'),
                    'collapsable' => true,
                    'collapsed' => true,
                ),
                'Membre_Import_Sep_Tab' => array(
                    'type' => 'radio',
                    'name' => 'Membre_Import_Separator',
                    'caption' => _t("_mg_newsl_sep_tab"),
                    'info' => _t('_mg_newsl_sep_info', $aSepNames['tab'], $aSepValues['tab']),
                    'value' => '\t',
                    'attrs' => array(
                        'id' => 'Membre_Import_Sep_Tab',
                        'checked' => true,
                    ),
                ),
                'Membre_Import_Sep_Comma' => array(
                    'type' => 'radio',
                    'name' => 'Membre_Import_Separator',
                    'caption' => _t("_mg_newsl_sep_comma"),
                    'info' => _t('_mg_newsl_sep_info', $aSepNames['comma'], $aSepValues['comma']),
                    'value' => ',',
                    'attrs' => array(
                        'id' => 'Membre_Import_Sep_Comma',
                    ),
                ),
                'Membre_Import_Sep_Semicolon' => array(
                    'type' => 'radio',
                    'name' => 'Membre_Import_Separator',
                    'caption' => _t("_mg_newsl_sep_semicolon"),
                    'info' => _t('_mg_newsl_sep_info', $aSepNames['semicolon'], $aSepValues['semicolon']),
                    'value' => ';',
                    'attrs' => array(
                        'id' => 'Membre_Import_Sep_Semicolon',
                    ),
                ),
                'Membre_Import_Sep_Space' => array(
                    'type' => 'radio',
                    'name' => 'Membre_Import_Separator',
                    'caption' => _t("_mg_newsl_sep_space"),
                    'info' => _t('_mg_newsl_sep_info', $aSepNames['space'], $aSepValues['space']),
                    'value' => '\s',
                    'attrs' => array(
                        'id' => 'Membre_Import_Sep_Space',
                    ),
                ),
                'Membre_Import_Sep_Other' => array(
                    'type' => 'radio',
                    'name' => 'Membre_Import_Separator',
                    'caption' => _t("_mg_newsl_sep_other"),
                    'info' => _t('_mg_newsl_sep_other_info'),
                    'value' => '',
                    'attrs' => array(
                        'id' => 'Membre_Import_Sep_Other',
                    ),
                ),
                'Membre_Import_Sep_Char' => array(
                    'type' => 'text',
                    'name' => 'Membre_Import_Sep_Char',
                    'value' => '',
                    'attrs' => array(
                        'id' => 'Membre_Import_Sep_Char',
                        'maxlength' => 1,
                    ),
                ),
                'header_options' => array(
                    'type' => 'block_header',
                    'caption' => _t('_mg_newsl_header_options'),
                    'collapsable' => true,
                    'collapsed' => true,
                ),
                'Membre_Import_DetectLang' => array(
                    'type' => 'checkbox',
                    'name' => 'Membre_Import_DetectLang',
                    'caption' => _t("_mg_newsl_detectLang"),
                    'info' => _t("_mg_newsl_detectLang_info"),
                    'value' => '1',
                    'attrs' => array(
                        'id' => 'Membre_Import_DetectLang',
                    ),
                ),
                'header_file' => array(
                    'type' => 'block_header',
                    'caption' => _t('_mg_newsl_membre_import_file'),
                    'collapsable' => false,
                    'collapsed' => false,
                ),
                'Membre_Import_File' => array(
                    'type' => 'file',
                    'name' => 'Membre_Import_File',
                ),
                'Membre_Import_Save' => array(
                    'type' => 'submit',
                    'name' => 'Membre_Import_Save',
                    'value' => _t('_Submit'),
                ),
            )
        );
        
        return $aForm;
    }
    
    function getMembreForm($aMembre=array()) {
        $submitName = 'Membre_Save' . $this->_membreFormCounter;
        
        $aForm = array(
            'form_attrs' => array(
                'id' => 'Membre_Form_' . $this->_membreFormCounter,
                'class' => 'Membre_Form',
                'action' => '',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
                'db' => array(
                    'table' => 'mg_newsl_membres',
                    'key' => 'ID',
                    'uri' => '',
                    'uri_title' => '',
                    'submit_name' => $submitName
                ),
            ),
            'inputs' => array ()
        );
        
        $oPF = new BxDolProfileFields(0);
        $sexes = $oPF->convertValues4Input("#!Sex");
        $pays = $oPF->convertValues4Input("#!Country");
        unset($oPF);
        
        $aForm['inputs'] = array_merge($aForm['inputs'], array(
            'Membre_ID' => array(
                'type' => 'hidden',
                'name' => 'Membre_ID',
                'value' => $aMembre['ID'],
                'db' => array (
                    'pass' => 'Int',
                ),
                'attrs' => array(
                    'id' => 'Membre_ID',
                ),
            ),
            'Membre_Email' => array(
                'type' => 'text',
                'name' => 'Membre_Email',
                'caption' => _t("_FieldCaption_Email_Join"),
                'value' => $aMembre['Email'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'required' => true,
                'checker' => array (  
                    'func' => 'email',
                    'error' => _t('_Incorrect Email'),
                ),
                'attrs' => array(
                    'id' => 'Membre_Email',
                ),
            ),
            'Membre_Nom' => array(
                'type' => 'text',
                'name' => 'Membre_Nom',
                'caption' => _t("_FieldCaption_LastName_Join"),
                'value' => $aMembre['Nom'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Nom',
                ),
            ),
            'Membre_Prenom' => array(
                'type' => 'text',
                'name' => 'Membre_Prenom',
                'caption' => _t("_FieldCaption_FirstName_Join"),
                'value' => $aMembre['Prenom'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Prenom',
                ),
            ),
            'Membre_DateNaissance' => array(
                'type' => 'date',
                'name' => 'Membre_DateNaissance',
                'caption' => _t("_FieldCaption_DateOfBirth_Join"),
                'value' => $aMembre['DateNaissance'],
                'db' => array (
                    'pass' => 'Xss', 
                ),
                'attrs' => array(
                    'id' => 'Membre_DateNaissance_' . $this->_membreFormCounter,
                    'class' => 'DatePicker',
                ),
            ),
            'Membre_Pays' => array(
                'type' => 'select',
                'name' => 'Membre_Pays',
                'caption' => _t("_FieldCaption_Country_Join"),
                'value' => $aMembre['Pays'],
                'values' => $pays,
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Pays',
                ),
            ),
            'Membre_Ville' => array(
                'type' => 'text',
                'name' => 'Membre_Ville',
                'caption' => _t("_FieldCaption_City_Join"),
                'value' => $aMembre['Ville'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Ville',
                ),
            ),
            'Membre_Zip' => array(
                'type' => 'text',
                'name' => 'Membre_Zip',
                'caption' => _t("_FieldCaption_zip_Join"),
                'value' => $aMembre['Zip'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Zip',
                ),
            ),
            'Membre_IDLangue' => array(
                'type' => 'select',
                'name' => 'Membre_IDLangue',
                'caption' => _t("_Language"),
                'value' => $aMembre['IDLangue'],
                'values' => $this->getLanguageChooser(),
                'db' => array (
                    'pass' => 'Int',
                ),
                'attrs' => array(
                    'id' => 'Membre_IDLangue',
                ),
            ),
            //'Membre_IDSource' => array(
            //    'type' => 'text',
            //    'name' => 'Membre_IDSource',
            //    'caption' => _t("_mg_newsl_membre_source"),
            //    'value' => $aMembre['IDSource'],
            //    'db' => array (
            //        'pass' => 'Xss',
            //    ),
            //    'attrs' => array(
            //        'id' => 'Membre_IDSource',
            //    ),
            //),
            'Membre_Sexe' => array(
                'type' => 'select',
                'name' => 'Membre_Sexe',
                'caption' => _t("_FieldCaption_Sex_Join"),
                'value' => $aMembre['Sexe'],
                'values' => $sexes,
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Sexe',
                ),
            ),
            'Membre_Adresse' => array(
                'type' => 'text',
                'name' => 'Membre_Adresse',
                'caption' => _t("_mg_newsl_membre_adresse"),
                'value' => $aMembre['Adresse'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Adresse',
                ),
            ),
            'Membre_ComplAdr' => array(
                'type' => 'text',
                'name' => 'Membre_ComplAdr',
                'caption' => _t("_mg_newsl_membre_complAdr"),
                'value' => $aMembre['ComplementAdresse'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_ComplAdr',
                ),
            ),
            'Membre_Telephone' => array(
                'type' => 'text',
                'name' => 'Membre_Telephone',
                'caption' => _t("_Phone"),
                'value' => $aMembre['Telephone'],
                'db' => array (
                    'pass' => 'Xss',
                ),
                'attrs' => array(
                    'id' => 'Membre_Telephone',
                ),
            ),
        ));
        
        $aForm['inputs'][$submitName] = array(
            'type' => 'submit',
            'name' => $submitName,
            'value' => _t('_Save'),
        );
        
        $this->_membreFormCounter++;
        
        return $aForm;
    }
    
    function getPaginateCampagnes($iPage, $iPerPage) {
        // Pagination
        $urlPagination = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/campagnes&page={page}&per_page={per_page}";
        if (!$iPerPage) {
            $iPerPage = self::MAX_PER_PAGE;
        }
        $iTotalNum = db_value("SELECT COUNT(*) FROM `mg_newsl_campagnes`");
        if( $iPage < 1 ) {
            $iPage = 1;
        }
        $sLimitFrom = ($iPage - 1) * $iPerPage;
        $oPaginate = new BxDolPaginate(
            array(
                'page_url'	=> $urlPagination,
                'count'		=> $iTotalNum,
                'per_page'	=> $iPerPage,
                'page'		=> $iPage,
                'per_page_changer'	 => true,
                'page_reloader'		 => true,
                'on_change_page'	 => null,
                'on_change_per_page' => null,
            )
        );
        
        $aCampagnes = $this->_oDb->getPaginateCampagnes($sLimitFrom, $iPerPage);
        $sResult = $this->_oTemplate->getTableCampagnes($aCampagnes);
        $sResult .= $oPaginate->getPaginate();
        
        unset($oPaginate);
        
        return $sResult;
    }
    
    function getPaginateContenus($iPage, $iPerPage) {
        // Pagination
        $urlPagination = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/contenus&page={page}&per_page={per_page}";
        if (!$iPerPage) {
            $iPerPage = self::MAX_PER_PAGE;
        }
        $iTotalNum = db_value("SELECT COUNT(*) FROM `mg_newsl_contenus`");
        if( $iPage < 1 ) {
            $iPage = 1;
        }
        $sLimitFrom = ($iPage - 1) * $iPerPage;
        $oPaginate = new BxDolPaginate(
            array(
                'page_url'	=> $urlPagination,
                'count'		=> $iTotalNum,
                'per_page'	=> $iPerPage,
                'page'		=> $iPage,
                'per_page_changer'	 => true,
                'page_reloader'		 => true,
                'on_change_page'	 => null,
                'on_change_per_page' => null,
            )
        );
        
        $aContenus = $this->_oDb->getPaginateContenus($sLimitFrom, $iPerPage);
        $sResult = $this->_oTemplate->getTableContenus($aContenus);
        $sResult .= $oPaginate->getPaginate();
        
        unset($oPaginate);
        
        return $sResult;
    }
    
    function getPaginateMembres($iPage, $iPerPage) {
        // Pagination
        $iTotalNum = db_value("SELECT COUNT(*) FROM `mg_newsl_membres`");
        $urlPagination = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/membres&page={page}&per_page={per_page}";
        $urlViewAll = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/membres&page=1&per_page={$iTotalNum}";
        $sLimitFrom = ($iPage - 1) * $iPerPage;
        $oPaginate = new BxDolPaginate(
            array(
                'page_url'	        => $urlPagination,
                'view_all_url'      => $urlViewAll,
                'count'		        => $iTotalNum,
                'per_page'	        => $iPerPage,
                'range'             => 4,
                'page'		        => $iPage,
                'per_page_changer'  => true,
                'per_page_step'     => self::MAX_PER_PAGE,
                'per_page_interval' => 8,
                'page_reloader'		=> true,
                'view_all'          => true
            )
        );
        
        // Tri
        $sortCol = isset($_SESSION['mg_newsl_admin']['sortCol']) ? $_SESSION['mg_newsl_admin']['sortCol'] : "ID";
        $sortOrder = isset($_SESSION['mg_newsl_admin']['sortOrder']) ? $_SESSION['mg_newsl_admin']['sortOrder'] : 1;
        
        $aMembres = $this->_oDb->getPaginateMembres($sLimitFrom, $iPerPage, $sortCol, $sortOrder);
        $sResult = $this->_oTemplate->getTableMembres($aMembres, $iTotalNum);
        $sResult .= $oPaginate->getPaginate();
        
        unset($oPaginate);
        
        return $sResult;
    }
    
    function getPaginateStats($iPage, $iPerPage) {
        // Pagination
        $urlPagination = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/stats&page={page}&per_page={per_page}";
        if (!$iPerPage) {
            $iPerPage = self::MAX_PER_PAGE;
        }
        $iTotalNum = $this->_oDb->getStatsTotal();
        if( $iPage < 1 ) {
            $iPage = 1;
        }
        $sLimitFrom = ($iPage - 1) * $iPerPage;
        $oPaginate = new BxDolPaginate(
            array(
                'page_url'	=> $urlPagination,
                'count'		=> $iTotalNum,
                'per_page'	=> $iPerPage,
                'page'		=> $iPage,
                'per_page_changer'	 => true,
                'page_reloader'		 => true,
                'on_change_page'	 => null,
                'on_change_per_page' => null,
            )
        );
        
        $aStats = $this->_oDb->getPaginateStats($sLimitFrom, $iPerPage);
        $sResult = $this->_oTemplate->getTableStats($aStats, $iTotalNum);
        $sResult .= $oPaginate->getPaginate();
        
        unset($oPaginate);
        
        return $sResult;
    }
    
    function getSearchMembres($str, $idLangue, $idCampagne) {
        // Tri
        $sortCol = isset($_SESSION['mg_newsl_admin']['sortCol']) ? $_SESSION['mg_newsl_admin']['sortCol'] : "ID";
        $sortOrder = isset($_SESSION['mg_newsl_admin']['sortOrder']) ? $_SESSION['mg_newsl_admin']['sortOrder'] : 1;
        
        $aMembres = $this->_oDb->getSearchMembres($str, $idLangue, $idCampagne, $sortCol, $sortOrder);
        $sResult = $this->_oTemplate->getTableMembres($aMembres, count($aMembres));
        
        return $sResult;
    }
    
    function getSearchStats($str, $idCampagne, $from) {
        $aStats = $this->_oDb->getSearchStats($str, $idCampagne, $from);
        $sResult = $this->_oTemplate->getTableStats($aStats, count($aStats));
        
        return $sResult;
    }
    
    function profileToMembre($aProfile) {
        $aMembre = array(
            'Email' => $aProfile['Email'],
            'Nom' => $aProfile['LastName'],
            'Prenom' => $aProfile['FirstName'],
            'Pays' => $aProfile['Country'],
            'Ville' => $aProfile['City'],
            'IDLangue' => $aProfile['LangID'],
            'DateNaissance' => $aProfile['DateOfBirth'],
            'Zip' => $aProfile['zip']
        );
        
        return $aMembre;
    }
    
    function serviceHashToMembre($hash) {
        if (!$hash) {
            return false;
        }
        
        // On cherche le membre dans les inscriptions aux newsletter
        $aMembres = $this->_oDb->getMembres();
        foreach ($aMembres as $aMembre) {
            $hash2 = $this->serviceMembreToHash($aMembre['Email']);
            if (strcmp($hash, $hash2) == 0) {
                return $aMembre;
            }
        }
        
        // Si pas trouvé on cherche dans les profiles
        $aProfiles = $this->_oDb->getAll("SELECT `Email`, `LangID` FROM `Profiles`");
        foreach ($aProfiles as $aProfile) {
            $hash2 = $this->serviceMembreToHash($aProfile['Email']);
            if (strcmp($hash, $hash2) == 0) {
                return self::profileToMembre($aProfile);
            }
        }
        
        return false;
    }
    
    function serviceEnvoi($idCampagne, $destEmail, $subject, $body) {
        $hash = $this->serviceMembreToHash($destEmail);
        $aVals = array("_idCampagne_", "_hash_");
        $aReplaceVals = array($idCampagne, $hash);
        $body = str_replace($aVals, $aReplaceVals, $body);
        
        // Si envoi pour teste
        $bUseDummyEmail = $this->_oDb->getSetting("mg_newsl_test_dummy");
        if ($bUseDummyEmail) {
            $destEmail = $this->_oDb->getSetting("mg_newsl_test_email");
        }
        
        return sendMail($destEmail, $subject, $body, 0, $aPlus, 'html', false, true);
    }
    
    function serviceInscription($email, array $aMembre, $idCampagne=0, $bOthers=false) {
        $aMembreSubscribed = $this->_oDb->getMembreByEmail(trim($email));
        
        if (!$aMembreSubscribed) {
            if (!$this->_oDb->insertMembre($aMembre)) {
                return 0;
            }
            $idMembre = $this->_oDb->lastId();
        }
        else {
            $idMembre = $aMembreSubscribed['ID'];
            $this->_oDb->updateMembre($idMembre, $aMembre);
        }
        
        if ($idCampagne) {
            $this->_oDb->subscribeMembreCampagne($idCampagne, $idMembre);
        }

        if ($bOthers) {
            $aOthers = $this->_oDb->getCampagnes(true);
            foreach ($aOthers as $aCampagne) {
                if ($aCampagne['ID'] != $idCampagne) {
                    $this->_oDb->subscribeMembreCampagne($aCampagne['ID'], $idMembre);
                }
            }
        }
        
        return $idMembre;
    }
    
    function serviceInscriptionBlock($hash) {
        $aMembre = $this->serviceHashToMembre($hash);
        
        if (empty($aMembre)) {
            echo $this->_oTemplate->displayAccessDenied ();
            return;
        }
        
        $aCampagnes = $this->_oDb->getCampagnes(true);
        
        // On coche les champs des campagnes déjà inscrites
        $aSubscribedCampagnes = $this->_oDb->getSubscribedCampagnes($aMembre['ID']);
        for ($i = 0; $i < count($aCampagnes); $i++) {
            $aCampagnes[$i]['Desc'] = _t($aCampagnes[$i]['Descriptif']);
            $aCampagnes[$i]['checkedAttr'] = "";
            foreach ($aSubscribedCampagnes as $aSubscribedCampagne) {
                if ($aSubscribedCampagne['ID'] == $aCampagnes[$i]['ID']) {
                    $aCampagnes[$i]['checkedAttr'] = "checked";
                    break;
                }
            }
        }
        
        // Validation du formulaire
        // Inscription/Désinscription aux campagnes
        if(isset($_POST['MembreCampagne_Str'])) {
            $aIds = explode(",", $_POST['MembreCampagne_Str']);
            foreach ($aCampagnes as $key => $aCampagne) {
                $bSubscribed = false;
                foreach ($aSubscribedCampagnes as $aSubscribedCampagne) {
                    if ($aCampagne['ID'] == $aSubscribedCampagne['ID']) {
                        $bSubscribed = true;
                        break;
                    }
                }
                $total = count($aIds);
                for ($i = 0; $i < $total; $i++) {
                    if ($aCampagne['ID'] == $aIds[$i]) {
                        break;
                    }
                }
                $bChecked = $i < $total;
                if ($bSubscribed) {
                    if (!$bChecked) {
                        $aCampagnes[$key]['checkedAttr'] = "";
                        $this->_oDb->unsubscribeMembreCampagne($aCampagne['ID'], $aMembre['ID']);
                    }
                }
                else {
                    if ($bChecked) {
                        $aCampagnes[$key]['checkedAttr'] = "checked";
                        $this->serviceInscription($aMembre['Email'], $aMembre, $aCampagne['ID'], false);
                    }
                }
            }
            echo MsgBox(_t('_mg_newsl_save_success'), 3);
        }
        
        $aInscriptionForm = $this->getInscriptionForm();
        $oInscriptionForm = new BxTemplFormView($aInscriptionForm);
        $sInscriptionForm = $oInscriptionForm->getCode();
        unset($oInscriptionForm);
        
        $aVars = array (
            'email' => $aMembre['Email'],
            'hash' => $hash,
            'bx_repeat:campagnes' => $aCampagnes,
            'inscriptionForm' => $sInscriptionForm
        );
        
        echo $this->_oTemplate->addCss(BX_DOL_URL_PLUGINS . 'jquery/themes/jquery-ui.css', true);
        echo $this->_oTemplate->addJs(BX_DOL_URL_PLUGINS . 'jquery/jquery.ui.all.min.js', true);
        echo $this->_oTemplate->addJs("newsletter.js", true);
        echo $this->_oTemplate->parseHtmlByName('inscriptions', $aVars);
    }
    
    function serviceMembreToHash($email) {
        $pair = explode("@", $email, 2);
        $segs = array_merge(array_reverse(explode(".", $pair[1])), explode(".", $pair[0]));
        $str = implode("", $segs);
        
        return sha1($str);
    }
    
    function serviceStats($idCampagne, $hash, $type) {
        $aMembre = $this->serviceHashToMembre($hash);
        
        if (!$aMembre) {
            return false;
        }
        
        return $this->_oDb->insertStat($idCampagne, $aMembre['Email'], $type);
    }
}
?>
