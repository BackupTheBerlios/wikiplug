<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */
// +------------------------------------------------------------------------------------------------------+
// | PHP version 4.1                                                                                      |
// +------------------------------------------------------------------------------------------------------+
// | Copyright (C) 2004 Tela Botanica (accueil@tela-botanica.org)                                         |
// +------------------------------------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or                                        |
// | modify it under the terms of the GNU Lesser General Public                                           |
// | License as published by the Free Software Foundation; either                                         |
// | version 2.1 of the License, or (at your option) any later version.                                   |
// |                                                                                                      |
// | This library is distributed in the hope that it will be useful,                                      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                                       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                    |
// | Lesser General Public License for more details.                                                      |
// |                                                                                                      |
// | You should have received a copy of the GNU Lesser General Public                                     |
// | License along with this library; if not, write to the Free Software                                  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                            |
// +------------------------------------------------------------------------------------------------------+
// CVS : $Id: bazar.class.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $
/**
*
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@ecole-et-nature.org>
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                             LES CONSTANTES DES NIVEAUX DE DROIT                                      |
// +------------------------------------------------------------------------------------------------------+

define ('BAZ_DROIT_SUPER_ADMINISTRATEUR', 0);
define ('BAZ_DROIT_ADMINISTRATEUR', 2);
define ('BAZ_DROIT_REDACTEUR', 1);

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

include_once PAP_CHEMIN_API_PEAR.'PEAR.php';

class Administrateur_bazar {

	var $_auth ;

	/**
	 * Identifiant de l'utilisateur
	 */

	var $_id_utilisateur ;

	/**
	 * 	Vaut true si l'utilisateur est un administrateur
	 */
	var $_isSuperAdmin ;

	/**	Constructeur
	 *
	 * @param	object Un objet authentification
	 * @return void
	 *
	 */

	 function Administrateur_bazar (&$AUTH) {
	 	$this->_auth = $AUTH ;
	 	if ($AUTH->getAuth())$this->_id_utilisateur = $this->_auth->getAuthData(BAZ_CHAMPS_ID) ;
	 }

	/**	isSuperAdmin () - Renvoie true si l'utilisateur est un super administrateur
	 *
	 */
	function isSuperAdmin() {

		if(empty($this->_id_utilisateur))
			return FALSE;

		// On court-circuite si la question a d�j� �t� pos� pour ne pas refaire la requete
		if (isset ($this->_isSuperAdmin)) return $this->_isSuperAdmin ;

		// On court-circuite si l'utilisateur n'est pas loggu�
		if (!$this->_auth->getAuth()) return false ;

		// Sinon on interroge la base
		$requete = 'SELECT bd_niveau_droit FROM bazar_droits WHERE bd_id_utilisateur='.
	 				$this->_id_utilisateur.
	           		' AND bd_niveau_droit=0';

		$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete) ;
		if (DB::isError($resultat)) {
			return ("Echec de la requete<br />".$resultat->getMessage()."<br />".$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows() != 0) {
			$this->_isSuperAdmin = true ;
		} else {
			$this->_isSuperAdmin = false ;
		}
		return $this->_isSuperAdmin;
	}

	/**	isAdmin () - Renvoie true si l'utilisateur est administrateur du type de fiche sp�cifi�
	 *
	 * @param interger type_annonce	Le type de l'annonce
	 *
	 */

	function isAdmin($id_nature) {
		// on court-circuite si l'utilisateur n'est pas loggu�
		if (!$this->_auth->getAuth()) return false ;

		return $this->_requeteDroit ($id_nature, 2) ;
	}

	/**	isRedacteur() - Renvoie true si l'utilisateur est redacteur du type de fiche specifie
	 *
	 */

	function isRedacteur($id_nature) {
		if (isset($GLOBALS['droit_depot']) && $GLOBALS['droit_depot'] == 3) return true;
		return $this->_requeteDroit ($id_nature, 1) ;
	}

	/** _requeteDroit() - fait une requete sur la table bazar_droit
	 *
	 */

	function _requeteDroit ($id_nature, $niveau) {

		if(empty($this->_id_utilisateur))
			return false;

		$requete = 'SELECT bd_niveau_droit FROM bazar_droits WHERE bd_id_utilisateur='
					.$this->_id_utilisateur.
	           		' AND bd_id_nature_offre="'.$id_nature.'" and bd_niveau_droit='.$niveau;

		$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete) ;
		if (DB::isError($resultat)) {
			return ("Echec de la requete<br />".$resultat->getMessage()."<br />".$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows() != 0) {
			return true ;
		}
		return false ;
	}
}

class Utilisateur_bazar extends Administrateur_bazar {

	function Utilisateur_bazar($id_utilisateur) {
		$this->_id_utilisateur = $id_utilisateur ;
	}

	function isAdmin($id_nature) {
		return $this->_requeteDroit ($id_nature, 2) ;
	}

	/**	isSuperAdmin () - Renvoie true si l'utilisateur est un super administrateur
	 *
	 */
	function isSuperAdmin() {

		if(empty($this->_id_utilisateur))
			return false;

		// On court-circuite si la question a d�j� �t� pos� pour ne pas refaire la requete
		if (isset ($this->_isSuperAdmin)) return $this->_isSuperAdmin ;

		// Sinon on interroge la base
		$requete = 'SELECT bd_niveau_droit FROM bazar_droits WHERE bd_id_utilisateur='.
	 				$this->_id_utilisateur.
	           		' AND bd_niveau_droit=0';

		$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete) ;
		if (DB::isError($resultat)) {
			return ("Echec de la requete<br />".$resultat->getMessage()."<br />".$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows() != 0) {
			$this->_isSuperAdmin = true ;
		} else {
			$this->_isSuperAdmin = false ;
		}
		return $this->_isSuperAdmin;
	}

}




class bazar extends PEAR {

	/**
	 * 	getMailAdmin	Renvoie un tableau de mail des administrateurs du type
	 * 					de fiche passe en parametre
	 *
	 * 	@global DB Un objet DB de PEAR $GLOBALS['_BAZAR_']['db']
	 * 	@param integer L identifiant de la nature
	 */
	function getMailAdmin($id_nature) {
		$requete = 'select '.BAZ_CHAMPS_EMAIL.' from '.BAZ_ANNUAIRE.', bazar_droits ' .
				'where bd_id_nature_offre="'.$id_nature.'" and bd_niveau_droit="'.BAZ_DROIT_ADMINISTRATEUR.'"' .
						' and '.BAZ_CHAMPS_ID.'= bd_id_utilisateur';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete);
		if (DB::isError($resultat)) $this->raiseError();
		$tableau_mail = array();
		if ($resultat->numRows() == 0) return false;
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			array_push ($tableau_mail, $ligne[BAZ_CHAMPS_EMAIL]) ;
		}
		return $tableau_mail;
	}
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.class.php,v $
* Revision 1.1  2008/07/07 18:00:39  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:47  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.9  2007-10-10 13:27:06  alexandre_tb
* encodage et remplacement de die en return
*
* Revision 1.8  2007-10-01 10:35:14  alexandre_tb
* petit hack pour tester la presence de $GLOBALS['droit_depot'] qui indique le niveau de droit minimum pour pouvoir deposer une fiche.
*
* Revision 1.7  2007-07-04 09:59:09  alexandre_tb
* ajout de la classe bazar, premices d une structuration du code
*
* Revision 1.6  2007/04/20 09:58:06  neiluj
* correction bug $this->_id_utilisateur
*
* Revision 1.5  2007/04/11 08:30:12  neiluj
* remise en état du CVS...
*
* Revision 1.3.2.1  2007/03/07 16:49:21  jp_milcent
* Mise  en majuscule de select
*
* Revision 1.3  2006/03/29 13:05:12  alexandre_tb
* ajout de la classe Administrateur_bazar
*
* Revision 1.2  2006/02/09 11:06:12  alexandre_tb
* changement dans les id des droit
* 0 => super administrateur
* 1 => redacteur
* 2 => administrateur
*
* Revision 1.1  2006/02/07 11:08:06  alexandre_tb
* version initiale
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
