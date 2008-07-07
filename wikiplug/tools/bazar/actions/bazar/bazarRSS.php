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
// CVS : $Id: bazarRSS.php,v 1.1 2008/07/07 18:00:40 mrflos Exp $
/**
* G�n�rateur de flux RSS � partir du bazar
*
*@package bazar
//Auteur original :
*@author        Florian SCHMITT <florian@ecole-et-nature.org>
*@author        Alexandre Granier <alexandre@tela-botanica.org>
*
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

// +------------------------------------------------------------------------------------------------------+
// |                                            CORPS DU PROGRAMME                                        |
// +------------------------------------------------------------------------------------------------------+
function afficher_flux_rss($mysql_user,$mysql_password,$mysql_host,$mysql_database) {
	define ('GEN_CHEMIN_API', str_replace('bazar'.DIRECTORY_SEPARATOR.'bazarRSS.php', '', $_SERVER["SCRIPT_FILENAME"]).'tools'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR);
	define ('PAP_CHEMIN_API_PEAR', GEN_CHEMIN_API.'pear'.DIRECTORY_SEPARATOR);
	define ('PAP_CHEMIN_RACINE', '');
	define ('GEN_SEP', DIRECTORY_SEPARATOR);
	define ('PAP_CHEMIN_API_PEARDB', PAP_CHEMIN_API_PEAR);
	set_include_path(PAP_CHEMIN_API_PEAR.PATH_SEPARATOR.get_include_path());
	require_once 'DB.php' ;
	require_once 'Auth.php' ;
	include_once 'Net'.DIRECTORY_SEPARATOR.'URL.php' ;
	require_once str_replace('tools/libs/', '',GEN_CHEMIN_API).DIRECTORY_SEPARATOR.'bazar'.
			DIRECTORY_SEPARATOR.'configuration'.DIRECTORY_SEPARATOR.'baz_config.inc.php'; //fichier de configuration de Bazar
	require_once 'tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'bazar'.
			DIRECTORY_SEPARATOR.'bibliotheque'.DIRECTORY_SEPARATOR.'bazar.class.php';
	require_once 'tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'bazar'.
			DIRECTORY_SEPARATOR.'bibliotheque'.DIRECTORY_SEPARATOR.'bazar.fonct.php'; //fichier des fonctions de Bazar
	require_once 'tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'bazar'.
			DIRECTORY_SEPARATOR.'bibliotheque'.DIRECTORY_SEPARATOR.'bazar.fonct.php'; //fichier des fonctions de Bazar
	if (isset($_GET['annonce'])) {
		$annonce=$_GET['annonce'];
	}
	else {
		$annonce='';
	}

	if (isset($_GET['categorie_nature'])) {
		$categorie_nature=$_GET['categorie_nature'];
	}
	else {
		$categorie_nature='';
	}

	if (isset($_GET['nbitem'])) {
		$nbitem=$_GET['nbitem'];
	}
	else {
		$nbitem='';
	}

	if (isset($_GET['emetteur'])) {
		$emetteur=$_GET['emetteur'];
	}
	else {
		$emetteur='';
	}

	if (isset($_GET['valide'])) {
		$valide=$_GET['valide'];
	}
	else {
		$valide=1;
	}

	if (isset($_GET['sql'])) {
		$requeteSQL=$_GET['sql'];
	}
	else {
		$requeteSQL='';
	}
	// Gestion de la langue
	if (isset($_GET['langue'])) {
		$requeteWhere = ' bn_ce_i18n like "'.$_GET['langue'].'%" and ';
	} else {
		$requeteWhere = '';
	}
	echo html_entity_decode(gen_RSS($annonce, $nbitem, $emetteur, $valide, $requeteSQL, '', $requeteWhere, $categorie_nature));
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazarRSS.php,v $
* Revision 1.1  2008/07/07 18:00:40  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:46  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.8  2007-10-17 08:23:00  alexandre_tb
* ajout du parametre langue
*
* Revision 1.7  2007-04-11 08:30:12  neiluj
* remise en état du CVS...
*
* Revision 1.4  2006/05/19 13:54:32  florian
* stabilisation du moteur de recherche, corrections bugs, lien recherche avancee
*
* Revision 1.3  2005/07/21 19:03:12  florian
* nouveautés bazar: templates fiches, correction de bugs, ...
*
* Revision 1.1.1.1  2005/02/17 18:05:11  florian
* Import initial de Bazar
*
* Revision 1.1.1.1  2005/02/17 11:09:50  florian
* Import initial
*
* Revision 1.1.1.1  2005/02/16 18:06:35  florian
* import de la nouvelle version
*
* Revision 1.3  2004/07/08 15:06:48  alex
* modification de la date
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/

?>
