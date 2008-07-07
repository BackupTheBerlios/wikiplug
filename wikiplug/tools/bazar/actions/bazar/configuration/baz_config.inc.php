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
// CVS : $Id: baz_config.inc.php,v 1.1 2008/07/07 18:00:48 mrflos Exp $
/**
* Fichier de configuration du bazar
*
* A éditer de façon spécifique à chaque déploiement
*
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
*@author        Florian SCHMITT <florian@ecole-et-nature.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:48 $
// +------------------------------------------------------------------------------------------------------+
*/

//configuration pour papyrus
if (defined("PAP_VERSION")) {
	$GLOBALS['_BAZAR_']['db'] =& $GLOBALS['_GEN_commun']['pear_db'];
	$GLOBALS['AUTH'] =& $GLOBALS['_GEN_commun']['pear_auth'];
	$GLOBALS['_BAZAR_']['url'] = $GLOBALS['_GEN_commun']['url'];
	define ('BAZ_ANNUAIRE','annuaire'); //Table annuaire
	define ('BAZ_CHAMPS_ID','a_id'); //Champs index sur la table annuaire
	define ('BAZ_CHAMPS_NOM','a_nom'); //Champs nom sur la table annuaire
	define ('BAZ_CHAMPS_PRENOM','a_prenom'); //Champs prenom sur la table annuaire
	define ('BAZ_CHAMPS_EST_STRUCTURE','a_est_structure'); //Champs indiquant si c'est une structure qui est identifiée
	define ('BAZ_CHAMPS_EMAIL','a_mail'); //Champs prenom sur la table annuaire
	define ('BAZ_CHAMPS_NOM_WIKI','a_nom_wikini'); //Champs nom wikini sur la table annuaire
	/** Réglage de l'URL de l'annuaire */
	// Mettre l'URL correspondant à l'annuaire
	define ('BAZ_URL_ANNUAIRE', '/page:annuaire');
	//BAZ_CHEMIN_APPLI : chemin vers l'application bazar METTRE UN SLASH (/) A LA FIN!!!!
	define ('BAZ_CHEMIN_APPLI', PAP_CHEMIN_RACINE.'client/bazar/');
	define ('BAZ_CHEMIN_SQUELETTE', BAZ_CHEMIN_APPLI.'squelettes'.GEN_SEP);
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: baz_config.inc.php,v $
* Revision 1.1  2008/07/07 18:00:48  mrflos
* maj carto plus calendrier
*
* Revision 1.2  2008/03/06 00:15:40  mrflos
* correction des bugs bazar, ajout de fichiers d'images
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
