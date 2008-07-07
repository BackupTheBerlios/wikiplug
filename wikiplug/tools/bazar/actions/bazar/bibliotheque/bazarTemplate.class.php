<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */
// +------------------------------------------------------------------------------------------------------+
// | PHP version 4.1                                                                                      |
// +------------------------------------------------------------------------------------------------------+
// | Copyright (C) 2004 Tela Botanica (accueil@tela-botanica.org)                                         |
// +------------------------------------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or                                        |
// | modify it under the terms of the GNU General Public                                                  |
// | License as published by the Free Software Foundation; either                                         |
// | version 2.1 of the License, or (at your option) any later version.                                   |
// |                                                                                                      |
// | This library is distributed in the hope that it will be useful,                                      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                                       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                    |
// | General Public License for more details.                                                             |
// |                                                                                                      |
// | You should have received a copy of the GNU General Public                                            |
// | License along with this library; if not, write to the Free Software                                  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                            |
// +------------------------------------------------------------------------------------------------------+

// CVS : $Id: bazarTemplate.class.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $

/**
* Application projet
*
* La classe d acces aux templates du bazar
*
*@package bazar
//Auteur original :
*@author        Alexandre Granier <alexandre@tela-botanica.org>
//Autres auteurs :
*@author        Aucun
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

include_once PAP_CHEMIN_API_PEAR.'PEAR.php' ;

// +------------------------------------------------------------------------------------------------------+
// |                                            CONSTANTE DES TEMPLATES                                   |
// +------------------------------------------------------------------------------------------------------+

define ('BAZ_TEMPLATE_CONSULTER', 1); // Squelette de l onglet "consulter"
define ('BAZ_TEMPLATE_LISTE_DES_FICHES', 2); // Modele de la liste des fiches

define ('BAZ_TEMPLATE_MAIL_NOUVELLE_FICHE_SUJET', 3); // Modele du mail pour prevenir les admins d une nouvelle fiche (sujet)
define ('BAZ_TEMPLATE_MAIL_NOUVELLE_FICHE_CORPS', 4); // Modele du mail pour prevenir les admins d une nouvelle fiche (corps)

define ('BAZ_TEMPLATE_MESSAGE_LOGIN', 5); // Modele du message lorsque l utilisateur n est pas logue

define ('BAZ_TEMPLATE_ACCUEIL_CARTE_GOOGLE', 6); // Modele de la page d accueil de l appli bazar.carte_google.php
define ('BAZ_TEMPLATE_FORMULAIRE_ACCUEIL', 7); // Modele, de la page saisie formulaire ou l'on choisit le type de fiche a saisir

define ('BAZ_TEMPLATE_FORMULAIRE', 8); // Modele du formulaire (different de celui ci-dessus)

class bazarTemplate extends PEAR {


	var $_db ;
	
    function bazarTemplate(&$objetDB) {
    		$this->_db = $objetDB ;
    }
    
    function getTemplate ($id_template, $lang='fr-FR', $categorie_nature = 'toutes') {
    		$requete = 'select bt_template from bazar_template where bt_id_template='.$id_template.
    					' and bt_id_i18n like "'.$lang.'%" ' ;
    		if($categorie_nature != 'toutes')
    			$requete .= ' and bt_categorie_nature='.$categorie_nature ;
    		else  //si toutes les catégories natures sont afficher, on affiche par défaut le template du numéro 1
    			$requete .= ' and bt_categorie_nature=1' ;
    		$resultat = $this->_db->query($requete) ;
    		if (DB::isError($resultat)) return $this->raiseError ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
    		if ($resultat->numRows() == 0) return $this->raiseError ('Aucun template avec l\'identifiant: '.$id_template.
				' et la langue: '.$lang.'<br />'.$requete) ;
    		$ligne = $resultat->fetchRow (DB_FETCHMODE_OBJECT) ;

    		return $ligne->bt_template ;
    }
}
?>