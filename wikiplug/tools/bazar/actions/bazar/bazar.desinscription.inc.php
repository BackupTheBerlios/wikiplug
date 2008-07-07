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
// CVS : $Id: bazar.desinscription.inc.php,v 1.1 2008/07/07 18:00:40 mrflos Exp $
/**
* 
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@ecole-et-nature.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2005
*@version       $Revision: 1.1 $
// +------------------------------------------------------------------------------------------------------+
*/
/* Ce programme est appelé depuis inscription.php et
 * se situe dans la fonction afficherContenuCorps()
 */
// +------------------------------------------------------------------------------------------------------+
// |                             LES CONSTANTES DES ACTIONS DE BAZAR                                      |
// +------------------------------------------------------------------------------------------------------+

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

// Suppression des entrée dans bazar_droits
$requete = 'DELETE FROM bazar_droits WHERE bd_id_utilisateur='.$id_utilisateur ;
$resultat = $GLOBALS['ins_db']->query($requete) ;

if (DB::isError($resultat)) {
	echo 'Erreur dans la requete sur bazar_droits<br />'.$requete.'<br />'.$resultat->getMessage() ;	
}
// +------------------------------------------------------------------------------------------------------+
// |                                           LISTE de FONCTIONS                                         |
// +------------------------------------------------------------------------------------------------------+

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.desinscription.inc.php,v $
* Revision 1.1  2008/07/07 18:00:40  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:46  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.3  2007-04-11 08:30:12  neiluj
* remise en Ã©tat du CVS...
*
* Revision 1.1.4.1  2007/03/07 16:53:45  jp_milcent
* Mise en majuscule des mots réservés par SQL
*
* Revision 1.1  2005/09/29 16:14:17  alexandre_tb
* version initiale
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
