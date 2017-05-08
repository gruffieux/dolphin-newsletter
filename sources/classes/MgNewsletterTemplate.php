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

bx_import ('BxDolTwigTemplate');

class MgNewsletterTemplate extends BxDolTwigTemplate {
    
	function MgNewsletterTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTwigTemplate($oConfig, $oDb);
    }
	
	function getTableCampagnes(array $aCampagnes) {
		for ($i = 0; $i < count($aCampagnes); $i++) {
			$class = ($i + 2) % 2 ? "Unpair" : "Pair";
			switch ($aCampagnes[$i]['Active']) {
                case 1:
                    $class .= " Active";
                    $newState = 0;
                    $newStateIcon = $this->getIconUrl("status_busy.png");
                    $newStateTitle = _t("_categ_btn_disable");
                    break;
                case 2:
                    $class .= " Pending";
                    $newState = 1;
                    $newStateIcon = $this->getIconUrl("status_away.png");
                    $newStateTitle = _t("_categ_btn_activate");
                    break;
                default:
                    $class .= " Unactive";
                    $newState = 1;
                    $newStateIcon = $this->getIconUrl("status_away.png");
                    $newStateTitle = _t("_categ_btn_activate");
                    break;
            }
			$aCampagnes[$i]['rank'] = $i + 1;
			$aCampagnes[$i]['class'] = $class;
			$aCampagnes[$i]['newState'] = $newState;
			$aCampagnes[$i]['newStateIcon'] = $newStateIcon;
			$aCampagnes[$i]['newStateTitle'] = $newStateTitle;
            $aCampagnes[$i]['desc'] = strlen($aCampagnes[$i]['Descriptif']) <= 50 ? $aCampagnes[$i]['Descriptif'] : substr($aCampagnes[$i]['Descriptif'], 0, 47) . "...";
        }
        
        $aVars = array(
            'bx_repeat:campagnes' => $aCampagnes
        );
		
		return $this->parseHtmlByName('table_campagnes', $aVars);
	}
	
	function getTableContenus(array $aContenus) {
		for ($i = 0; $i < count($aContenus); $i++) {
			$aContenus[$i]['rank'] = $i + 1;
            $aContenus[$i]['class'] = ($i + 2) % 2 ? "Unpair" : "Pair";
            $aContenus[$i]['langue'] = $this->_oDb->getOne("SELECT `Title` FROM `sys_localization_languages` WHERE `ID`='" . $aContenus[$i]['IDLangue'] . "'");
        }
        
        $aVars = array(
            'bx_repeat:contenus' => $aContenus
        );
		
		return $this->parseHtmlByName('table_contenus', $aVars);
	}
	
	function getTableMembres(array $aMembres, $iTotal) {
		$aCampagnes = $this->_oDb->getCampagnes();
		$aLangues = MgNewsletterModule::getLanguageChooser();
		
		for ($i = 0; $i < count($aMembres); $i++) {
			$aMembres[$i]['rank'] = $i + 1;
            $aMembres[$i]['class'] = ($i + 2) % 2 ? "Unpair" : "Pair";
            $aMembres[$i]['langue'] = $this->_oDb->getOne("SELECT `Title` FROM `sys_localization_languages` WHERE `ID`='" . $aMembres[$i]['IDLangue'] . "'");
        }
        
        $aVars = array(
            'bx_repeat:langues' => $aLangues,
            'bx_repeat:campagnes' => $aCampagnes,
			'searchTotal' => $iTotal,
            'bx_repeat:membres' => $aMembres,
			'bx_repeat:actionCampagnes' => $aCampagnes
        );
		
		return $this->parseHtmlByName('table_membres', $aVars);
    }
	
	function getTableStats(array $aStats, $iTotal) {
		$aCampagnes = $this->_oDb->getCampagnes();
		
		for ($i = 0; $i < count($aStats); $i++) {
			$aStats[$i]['rank'] = $i + 1;
            $aStats[$i]['class'] = ($i + 2) % 2 ? "Unpair" : "Pair";
        }
        
        $aVars = array(
			'bx_repeat:campagnes' => $aCampagnes,
			'searchTotal' => $iTotal,
            'bx_repeat:stats' => $aStats,
        );
		
		return $this->parseHtmlByName('table_stats', $aVars);
    }
}
?>
