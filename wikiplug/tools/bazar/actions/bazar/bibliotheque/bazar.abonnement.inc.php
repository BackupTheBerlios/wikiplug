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
// CVS : $Id: bazar.abonnement.inc.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $
/**
* Fonctions d'abonnement a afficher dans la fiche du bottin
*
*
*@package inscription
//Auteur original :
*@author        Florian Schmitt <florian@ecole-et-nature.org>
//Autres auteurs :
*
*@copyright     Outils-Reseaux 2006-2010
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

include_once PAP_CHEMIN_RACINE.'client/bazar/configuration/baz_config.inc.php' ;
include_once PAP_CHEMIN_RACINE.'client/bazar/bibliotheque/bazar.fonct.php' ;

// +------------------------------------------------------------------------------------------------------+
// |                                           LISTE de FONCTIONS                                         |
// +------------------------------------------------------------------------------------------------------+
$GLOBALS['id_user']=$id;
if (isset($_GET['action']) && $_GET['action']==BAZ_VOIR_FLUX_RSS) {
	header('Content-type: text/xml; charset=ISO-8859-1');
	include(PAP_CHEMIN_RACINE.'client/bazar/bazarRSS.php');exit(0);break;
} else {
	$abonnement = baz_s_inscrire();
}

    	
//-- Fin du code source    ------------------------------------------------------------
/*
* $Log: bazar.abonnement.inc.php,v $
* Revision 1.1  2008/07/07 18:00:39  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:47  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.2  2006-10-05 08:53:50  florian
* amelioration moteur de recherche, correction de bugs
*
* Revision 1.1  2006/04/28 12:46:14  florian
* integration des liens vers annuaire
*
*
*/
?>