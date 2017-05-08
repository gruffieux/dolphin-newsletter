<?php
class MgNewsletterFormView extends BxTemplFormView {

	function MgNewsletterFormView($aInfo) {
	    BxBaseFormView::BxBaseFormView($aInfo);
	}
}

class MgNewsletterFormCheckerHelper extends BxDolFormCheckerHelper {    
    function checkEmail($s) {
        if (!preg_match("/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/i", $s)) {
            return false;
        }
	
        // La vérification du DNS ne fonctionne pas sur le serveur
        // Il doit y avoir une sécurité empêchant la sortie
        // via le port utilisé par la fonction checkdnsrr().
        
        return checkdnsrr(array_pop(explode("@", $s)), "MX");
    }
    
    function checkInvite($s, $bPremier) {
        if ($s) {
			if (!self::checkEmail($s)) {
				return false;
			}
        }
		elseif ($bPremier) {
			return false;
		}
        
        return true;
    }
}
?>
