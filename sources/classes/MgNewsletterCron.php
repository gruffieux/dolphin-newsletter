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

require_once(BX_DIRECTORY_PATH_INC . 'classes/BxDolCron.php');
require_once('MgNewsletterModule.php');

class MgNewsletterCron extends BxDolCron {
    function processing() {
        set_time_limit(0); // temps du script illimité
        
        $oModule = BxDolModule::getInstance("MgNewsletterModule");
        $aCampagnes = $oModule->_oDb->getCampagnes(true);
        $aLangs = $oModule->_oDb->getAll("SELECT * FROM `sys_localization_languages`");
        
        foreach ($aCampagnes as $aCampagne) {
            if ($aCampagne['Active'] == 1) {
                $aContenus = $oModule->_oDb->getCampagneContenus($aCampagne['ID']);
                $aContenuPacks = array();
                foreach ($aContenus as $aContenu) {
                    $aContenuPacks[$aContenu['IDLangue']][] = $aContenu;
                }
                $bDummyEmail = $oModule->_oDb->getSetting("mg_newsl_test_dummy");
                $aMembres = $oModule->_oDb->getSubscribedMembres($aCampagne['ID'], $bDummyEmail);
                $sLog = '';
                foreach ($aMembres as $aMembre) {
                    $iLang = $aMembre['IDLangue'];
                    if (!$iLang) {
                        $iLang = $oModule->_oDb->getOne("SELECT `LangID` FROM `Profiles` WHERE `Email`='" . $aMembre['Email'] . "'");
                    }
                    if (!$iLang) {
                        $iLang = 1;
                    }
                    $total = count($aContenuPacks[$iLang]);
                    if (!$total) {
                        foreach($aLangs as $aLang) {
                            $total = count($aContenuPacks[$aLang['ID']]);
                            if ($total) {
                                $iLang = $aLang['ID'];
                                break;
                            }
                        }
                    }
                    $hash = $oModule->serviceMembreToHash($aMembre['Email']);
                    for ($i = 0; $i < $total; $i++) {
                        $sLog .= date("d.m.Y H:i:s");
                        $sLog .= "\t>" . $aMembre['Email'];
                        if ($oModule->serviceEnvoi($aCampagne['ID'], $aMembre['Email'], $aContenuPacks[$iLang][$i]['Titre'], $aContenuPacks[$iLang][$i]['Corps'])) {
                            $sLog .= "\t---->SUCCES Contenu \"" . $aContenuPacks[$iLang][$i]['Titre'] . "\" envoyé\n";
                        }
                        else {
                            $sLog .= "\t---->ECHEC Contenu \"" . $aContenuPacks[$iLang][$i]['Titre'] . "\" non envoyé\n";
                        }
                    }
                }
                
                // Ecriture du log
                if ($sLog) {
                    $fp = fopen(BX_DIRECTORY_PATH_MODULES . 'mensgo/newsletter/save/newsl_' . date("Y-m-d") . ".log", "a");
                    fwrite($fp, $sLog);
                    fclose($fp);
                    
                    // Pour ne pas envoyer plusieurs fois le même mail, on change l'état de la campagne.
                    $oModule->_oDb->updateCampagneState($aCampagne['ID'], 2, true);
                }
            }
        }
        
        unset($oModule);
    }
}

?>
