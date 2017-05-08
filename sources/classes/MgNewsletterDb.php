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

bx_import('BxDolModuleDb');

class MgNewsletterDb extends BxDolModuleDb {

	function MgNewsletterDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }
    
    function deleteCampagne($id) {
		$query1 = "DELETE FROM `mg_newsl_campagneMembres` WHERE `IDCampagne`='" . $id . "'";
		$query2 = "DELETE FROM `mg_newsl_campagneContenus` WHERE `IDCampagne`='" . $id . "'";	
		$query3 = "DELETE FROM `mg_newsl_campagnes` WHERE `ID` = '" . $id . "'";
		
		$this->query($query1);
		$this->query($query2);
		
		return $this->query($query3);
    }
    
    function deleteCampagneContenu($idCampagne, $idContenu) {
		$query = "DELETE FROM `mg_newsl_campagneContenus` WHERE `IDCampagne` = '" . $idCampagne . "' AND `IDContenu`='" . $idContenu . "'";
		
		return $this->query($query);
    }
    
    function deleteCampagneMembre($idCampagne, $idMembre) {
		$query = "DELETE FROM `mg_newsl_campagneMembres` WHERE `IDCampagne` = '" . $idCampagne . "' AND `IDMembre`='" . $idMembre . "'";
		
		return $this->query($query);
    }
    
    function deleteContenu($id) {
		$query1 = "DELETE FROM `mg_newsl_campagneContenus` WHERE `IDContenu`='" . $id . "'";
		$query2 = "DELETE FROM `mg_newsl_contenus` WHERE `ID` = '" . $id . "'";
		
		$this->query($query1);
		
		return $this->query($query2);
    }
    
    function deleteMembre($id) {
		$query1 = "DELETE FROM `mg_newsl_campagneMembres` WHERE `IDMembre`='" . $id . "'";
		$query2 = "DELETE FROM `mg_newsl_membres` WHERE `ID` = '" . $id . "'";
		
		$this->query($query1);
		
		return $this->query($query2);
    }
    
    function getCampagne($id) {
		$query = "SELECT * FROM `mg_newsl_campagnes` WHERE `ID`='" . $id . "'";
		
		return $this->getRow($query);
    }
    
    function getCampagneContenu($idCampagne, $idLangue) {
		$query = "SELECT * FROM `mg_newsl_contenus`
			INNER JOIN `mg_newsl_campagneContenus` ON `mg_newsl_campagneContenus`.`IDContenu`=`mg_newsl_contenus`.`ID`
			WHERE `mg_newsl_campagneContenus`.`IDCampagne`='" . $idCampagne . "' AND `mg_newsl_contenus`.`IDLangue`='" . $idLangue . "'";
		
		return $this->getRow($query);
    }
    
    function getCampagneContenus($idCampagne) {
		$query = "SELECT DISTINCT * FROM `mg_newsl_contenus`
			INNER JOIN `mg_newsl_campagneContenus` ON `mg_newsl_campagneContenus`.`IDContenu`=`mg_newsl_contenus`.`ID`
			WHERE `mg_newsl_campagneContenus`.`IDCampagne`='" . $idCampagne . "'";
		
		return $this->getAll($query);
    }
    
    function getCampagnes($activeAndUnexpired=false) {
		$query = "SELECT * FROM `mg_newsl_campagnes`";
		
		if ($activeAndUnexpired) {
			$query .= " WHERE `Active` != '0' AND `DateEcheance` > NOW()";
		}
		
		$query .= " ORDER BY `Nom`";
		
		return $this->getAll($query);
    }
    
    function getCampagneMembres($idCampagne) {
		$query = "SELECT DISTINCT `mg_newsl_membres`.* FROM `mg_newsl_membres`
			INNER JOIN `mg_newsl_campagneMembres` ON `mg_newsl_campagneMembres`.`IDMembre`=`mg_newsl_membres`.`ID`
			WHERE `mg_newsl_campagneMembres`.`IDCampagne`='" . $idCampagne . "'
			ORDER BY `mg_newsl_membres`.`Email`";
		
		return $this->getAll($query);
    }
	
	function getCampagneParNom($nom) {
		$query = "SELECT * FROM `mg_newsl_campagnes` WHERE `Nom`='" . $nom . "'";
		
		return $this->getRow($query);
    }
	
	function getContenu($idContenu) {
		$query = "SELECT * FROM `mg_newsl_contenus` WHERE `ID`='" . $idContenu . "'";
		
		return $this->getRow($query);
    }
    
    function getContenuCampagnes($idContenu) {
	$query = "SELECT DISTINCT * FROM `mg_newsl_campagnes`
		INNER JOIN `mg_newsl_campagneContenus` ON `mg_newsl_campagneContenus`.`IDCampagne`=`mg_newsl_campagnes`.`ID`
		WHERE `mg_newsl_campagneContenus`.`IDContenu`='" . $idContenu . "'";
		
	return $this->getAll($query);
    }
    
    function getContenus() {
		$query = "SELECT * FROM `mg_newsl_contenus` ORDER BY `Titre`";
		
		return $this->getAll($query);
    }
	
	function getMembre($id) {
		$query = "SELECT * FROM `mg_newsl_membres` WHERE `ID`='" . $id . "'";
		
		return $this->getRow($query);
    }
    
    function getMembreByEmail($email) {
		$query = "SELECT * FROM `mg_newsl_membres` WHERE `Email`='" . strtolower($email) . "'";
		
		return $this->getRow($query);
    }
	
	function getMembreCampagnes($idMembre) {
		$query = "SELECT DISTINCT * FROM `mg_newsl_campagnes`
			INNER JOIN `mg_newsl_campagneMembres` ON `mg_newsl_campagneMembres`.`IDCampagne`=`mg_newsl_campagnes`.`ID`
			WHERE `mg_newsl_campagneMembres`.`IDMembre`='" . $idMembre . "'
			ORDER BY `DateInscription` ASC";
		
		return $this->getAll($query);
    }
    
    function getMembres($bDummy=false) {
		$query = "SELECT * FROM `mg_newsl_membres`";
		
		if ($bDummy) {
			$query .= " GROUP BY `IDLangue`";
		}
		
		$query .= " ORDER BY `Email`";
		
		return $this->getAll($query);
    }
	
	function getMembresByEmails(array $emails) {
		$query = "SELECT * FROM `mg_newsl_membres` WHERE `Email` IN (" . implode(",", $emails) . ")";
		
		return $this->getAll($query);
	}
	
	function getPaginateCampagnes($sLimitFrom, $iPerPage, $activeAndUnexpired=false) {
		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";
		$query = "SELECT * FROM `mg_newsl_campagnes`";
		
		if ($activeAndUnexpired) {
			$query .= " WHERE `Active` != '0' AND `DateEcheance` > NOW()";
		}
		
		$query .= " ORDER BY `Nom` {$sqlLimit}";
		
		return $this->getAll($query);
    }
	
	function getPaginateContenus($sLimitFrom, $iPerPage) {
		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";
		$query = "SELECT * FROM `mg_newsl_contenus` ORDER BY `Titre` {$sqlLimit}";
		
		return $this->getAll($query);
    }
	
	function getPaginateMembres($sLimitFrom, $iPerPage, $sortCol, $sortOrder) {
		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";
		$sqlOrder = $sortOrder ? "ASC" : "DESC";
		$query = "SELECT * FROM `mg_newsl_membres` ORDER BY `$sortCol` $sqlOrder {$sqlLimit}";
		
		return $this->getAll($query);
    }
	
	function getPaginateStats($sLimitFrom, $iPerPage, $type='click') {
		$sqlLimit = "LIMIT {$sLimitFrom}, {$iPerPage}";
		$query = "SELECT *, COUNT(`ID`) AS `Hits` FROM `mg_newsl_stats`
			WHERE `Type`='$type' GROUP BY `IDCampagne`, `Email`
			ORDER BY `Hits` DESC {$sqlLimit}";
		
		return $this->getAll($query);
    }
	
	function getSearchMembres($str, $idLangue, $idCampagne, $sortCol, $sortOrder) {
		$sqlOrder = $sortOrder ? "ASC" : "DESC";
		$query = "SELECT DISTINCT * FROM `mg_newsl_membres`";
		
		if ($idCampagne) {
			$query .= " INNER JOIN `mg_newsl_campagneMembres` ON `mg_newsl_campagneMembres`.`IDMembre`=`mg_newsl_membres`.`ID`";
		}
		
		if ($str) {
			$words = explode(" ", $str);
			for ($i = 0; $i < count($words); $i++) {
				$s1 = $words[$i];
				$s2 = strlen($s1) > 2 ? "%$s1%" : $s1;
				if ($i) {
					$query .= " OR";
				}
				else {
					$query .= " WHERE";
				}
				$query .= " (`mg_newsl_membres`.`ID`='$s1' OR `mg_newsl_membres`.`Email` LIKE '$s2' OR `mg_newsl_membres`.`Nom` LIKE '$s2' OR `mg_newsl_membres`.`Prenom` LIKE '$s2' OR `mg_newsl_membres`.`Pays` LIKE '$s2'
					OR `mg_newsl_membres`.`Ville` LIKE '$s2' OR `mg_newsl_membres`.`DateNaissance`='$s1' OR `mg_newsl_membres`.`Zip`='$s1' OR `mg_newsl_membres`.`IDSource` LIKE '$s2' OR `mg_newsl_membres`.`Adresse` LIKE '$s2')";
			}
		}
		
		if ($idLangue) {
			if ($i) {
				$query .= " AND";
			}
			else {
				$query .= " WHERE";
			}
			$query .= " `mg_newsl_membres`.`IDLangue`='$idLangue'";
			$i++;
		}
		
		if ($idCampagne) {
			if ($i) {
				$query .= " AND";
			}
			else {
				$query .= " WHERE";
			}
			$query .= " `mg_newsl_campagneMembres`.`IDCampagne`='$idCampagne' AND `mg_newsl_campagneMembres`.`DateInscription` > `mg_newsl_campagneMembres`.`DateDesinscription`";
		}
		
		$query .= " ORDER BY `$sortCol` $sqlOrder";
		
		return $this->getAll($query);
    }
	
	function getSearchStats($str, $idCampagne, $from) {
		$query = "SELECT *, COUNT(`ID`) AS `Hits` FROM `mg_newsl_stats` WHERE 0=0";
		
		if ($str) {
			$words = explode(" ", $str);
			for ($i = 0; $i < count($words); $i++) {
				$s1 = $words[$i];
				$s2 = strlen($s1) > 2 ? "%$s1%" : $s1;
				$query .= " OR (`Email` LIKE '$s2' OR `Type` LIKE '$s2')";
			}
		}
		
		if ($idCampagne) {
			$query .= " AND `IDCampagne`='$idCampagne'";
		}
		
		if ($from) {
			$query .= " AND `Date` >= '$from'";
		}
		
		$query .= " GROUP BY `IDCampagne`, `Email`
			ORDER BY `Hits` DESC";
		
		return $this->getAll($query);
    }
    
    function getSetting($name) {
	$query = "SELECT `VALUE` FROM `sys_options`
		INNER JOIN `sys_options_cats` ON `sys_options_cats`.`ID`=`sys_options`.`kateg`
		WHERE `sys_options_cats`.`name` = 'Newsletter' AND `sys_options`.`Name` = '" . $name . "' LIMIT 1";
		
        return $this->getOne($query);
    }
    
    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Newsletter' LIMIT 1");
    }
    
    function getSubscribedCampagnes($idMembre) {
	$query = "SELECT DISTINCT `mg_newsl_campagnes`.* FROM `mg_newsl_campagnes`
		INNER JOIN `mg_newsl_campagneMembres` ON `mg_newsl_campagneMembres`.`IDCampagne`=`mg_newsl_campagnes`.`ID`
		WHERE `mg_newsl_campagneMembres`.`IDMembre`='" . $idMembre . "'
		AND `mg_newsl_campagneMembres`.`DateInscription` > `mg_newsl_campagneMembres`.`DateDesinscription`";
	
	return $this->getAll($query);
    }
	
	function getSubscribedMembres($idCampagne, $bDummy=false) {
		$query = "SELECT DISTINCT `mg_newsl_membres`.* FROM `mg_newsl_membres`
			INNER JOIN `mg_newsl_campagneMembres` ON `mg_newsl_campagneMembres`.`IDMembre`=`mg_newsl_membres`.`ID`
			WHERE `mg_newsl_campagneMembres`.`IDCampagne`='" . $idCampagne . "'
			AND `mg_newsl_campagneMembres`.`DateInscription` > `mg_newsl_campagneMembres`.`DateDesinscription`";
			
		if ($bDummy) {
			$query .= " GROUP BY `mg_newsl_membres`.`IDLangue`";
		}
		
		return $this->getAll($query);
    }
	
	function getStatsTotal($type='click') {
		$query = "SELECT `ID` FROM `mg_newsl_stats`
			WHERE `Type`='$type' GROUP BY `IDCampagne`, `Email`";
		
		return count($this->getAll($query));
    }
	
	function importCSV($separator, $detectLang, $file, $maxLineLength=10000) {
		if (mime_content_type($file) != "text/plain") {
			return 2;
		}
		
		if (($handle = fopen($file, "r")) === false) {
			return 1;
		}
		
		$aLanguages = $this->getAll("SELECT `ID`, `Name` FROM `sys_localization_languages`");
		$iLangDefault = getLangIdByName(getParam('lang_default'));
		$aFields = $this->getFields('mg_newsl_membres');
		$aKeyMap = $aFields['original'];
		
		// Première ligne
		$columns = fgetcsv($handle, $maxLineLength, $separator);
		
		// Lignes suivantes
		while (($data = fgetcsv($handle, $maxLineLength, $separator)) !== false) {
			$bInsert = true;
			$bSet = $bLangSet = false;
			$query = "INSERT INTO `mg_newsl_membres`";
			foreach ($columns as $key => $column) {
				switch ($column) {
					case 'Email':
						$bInsert = !empty($data[$key]);
						if (!$bInsert) {
							break;
						}
						$bInsert = preg_match("/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/i", $data[$key]);
						if (!$bInsert) {
							break;
						}
						$bInsert = $this->getOne("SELECT `Email` FROM `mg_newsl_membres` WHERE `Email`='" . $data[$key] . "'") == false;
						if (!$bInsert) {
							break;
						}
						$email = $data[$key];
						$aParts = explode(".", $email);
						$total = count($aParts);
						$countryCode = strtolower($aParts[$total-1]);
						break;
				}
				$index = array_search($column, $aKeyMap);
				if ($index && ($column != 'IDLangue' OR $detectLang == false)) {
					$bSet = true;
					if (!$key) {
						$query .= " SET";
					}
					else {
						$query .= ",";
					}
					$value = $data[$key];
					$query .= " `" . $aKeyMap[$index] . "`='" . $this->escape($value) . "'";
				}
			}
			if ($detectLang && isset($countryCode)) {
				$value = $iLangDefault;
				foreach ($aLanguages as $aLanguage) {
					if ($aLanguage['Name'] == $countryCode) {
						$value = $aLanguage['ID'];
						break;
					}
				}
				$query .= ", `IDLangue`='" . $this->escape($value) . "'";
			}
			if ($bInsert && $bSet) {
				$this->query($query);
			}
		}
		
		fclose($handle);
		
		return 0;
    }
	
	function insertBlackMail(array $aMembre) {
		$query = "INSERT INTO `mg_newsl_blackList`
			SET `Email`='" . $aMembre['Email'] . "', `IDSource`='" . $aMembre['IDSource'] . "', `DateSuppression`=NOW()";
		
		return $this->query($query);
    }
    
    function insertCampagne($nom, $descriptif, $dateEcheance) {
		$query = "INSERT INTO `mg_newsl_campagnes`
			SET `Nom`='" . $nom . "', `Descriptif`='" . $descriptif . "', `DateEcheance`='" . $dateEcheance . "'";
		
		return $this->query($query);
    }
    
    function insertCampagneContenu($idCampagne, $idContenu) {
		$query = "INSERT INTO `mg_newsl_campagneContenus`
			SET `IDContenu`='" . $idContenu . "', `IDCampagne`='" . $idCampagne . "'";
			
		return $this->query($query);
    }
    
    function insertContenu($titre, $corps, $idLangue) {
		$query = "INSERT INTO `mg_newsl_contenus`
			SET `Titre`='" . $titre . "', `Corps`='" . $corps . "', `IDLangue`='" . $idLangue . "'";
		
		return $this->query($query);
    }
    
    function insertMembre(array $aMembre) {
		$query = "INSERT INTO `mg_newsl_membres`
			SET `Email`='" . $aMembre['Email'] . "', `Nom`='" . $aMembre['Nom'] . "', `Prenom`='" . $aMembre['Prenom'] . "', `DateNaissance`='" . $aMembre['DateNaissance'] . "', 
			`Pays`='" . $aMembre['Pays'] . "', `Ville`='" . $aMembre['Ville'] . "', `Zip`='" . $aMembre['Zip'] . "', `IDLangue`='" . $aMembre['IDLangue'] . "', `IDSource`='" . $aMembre['IDSource'] . "',
			`Sexe`='" . $aMembre['Sexe'] . "', `Adresse`='" . $aMembre['Adresse'] . "', `ComplementAdresse`='" . $aMembre['ComplementAdresse'] . "', `Telephone`='" . $aMembre['Telephone'] . "'";
		
		return $this->query($query);
    }
	
	function insertStat($idCampagne, $email, $type) {
		$query = "INSERT INTO `mg_newsl_stats`
			SET `IDCampagne`='$idCampagne', `Email`='$email', `Date`=NOW(), `Type`='$type'";
		
		return $this->query($query);
    }
	
	function isSubscribed($idMembre, $idCampagne) {
		$query = "SELECT * FROM `mg_newsl_campagneMembres`
			WHERE `IDMembre`='" . $idMembre . "' AND `IDCampagne`='" . $idCampagne . "'
			AND `DateInscription` > `DateDesinscription`";
		
		return $this->getRow($query) ? true : false;
    }
	
	function subscribeMembreCampagne($idCampagne, $idMembre) {
		$query1 = "SELECT * FROM `mg_newsl_campagneMembres` WHERE `IDCampagne`='" . $idCampagne . "' AND `IDMembre`='" . $idMembre . "'";
		
		if ($this->getRow($query1)) {
			$query2 = "UPDATE `mg_newsl_campagneMembres` SET `IDCampagne`='" . $idCampagne . "', `IDMembre`='" . $idMembre . "', `DateInscription`=NOW()
				WHERE `IDCampagne`='" . $idCampagne . "' AND `IDMembre`='" . $idMembre . "'";
		}
		else {
			$query2 = "INSERT INTO `mg_newsl_campagneMembres` SET `IDCampagne`='" . $idCampagne . "', `IDMembre`='" . $idMembre . "', `DateInscription`=NOW()";
		}
		
		return $this->query($query2);
    }
	
	function unsubscribeMembreCampagne($idCampagne, $idMembre) {
		$query = "UPDATE `mg_newsl_campagneMembres` SET `DateDesinscription`=NOW()
			WHERE `IDCampagne`='" . $idCampagne . "' AND `IDMembre`='" . $idMembre . "'";
		
		return $this->query($query);
    }
	
	function updateBotScore(array $emails) {
		$query = "UPDATE `mg_newsl_membres`
			SET `BotScore`=`BotScore`+1
			WHERE `Email` IN (" . implode(",", $emails) . ")";
		
		return $this->query($query);
	}
    
    function updateCampagne($id, $nom, $descriptif, $dateEcheance) {
		$query = "UPDATE `mg_newsl_campagnes`
			SET `Nom`='" . $nom . "', `Descriptif`='" . $descriptif . "', `DateEcheance`='" . $dateEcheance . "'
			WHERE `ID`='" . $id . "'";
		
		return $this->query($query);
    }
	
	function updateCampagneState($id, $state, $sent=false) {
		$query = "UPDATE `mg_newsl_campagnes` SET `Active`='" . $state . "'";
		
		if ($sent) {
			$query .= ", `DateEnvoi`=NOW()";
		}
		
		$query .= " WHERE `ID`='" . $id . "'";
		
		return $this->query($query);
    }
    
    function updateContenu($id, $titre, $corps, $idLangue) {
		$query = "UPDATE `mg_newsl_contenus`
			SET `Titre`='" . $titre . "', `Corps`='" . $corps . "', `IDLangue`='" . $idLangue . "'
			WHERE `ID`='" . $id . "'";
		
		return $this->query($query);
    }
    
    function updateMembre($id, array $aMembre) {
		foreach ($aMembre as $key => $value) {
			if (isset($query)) {
				$query .= ",";
			}
			else {
				$query = "UPDATE `mg_newsl_membres` SET";
			}
			$query .= " `" . $key . "`='" . $value . "'";
		}
	
		if (isset($query)) {
			$query .= " WHERE `ID`='" . $id . "'";
			return $this->query($query);
		}
		
		return false;
    }
}

?>
