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
// CVS : $Id: bazar.fonct.formulaire.controles.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $
/**
* Formulaire controles
*
* Les fonctions de controles de saisie des formulaires
*
*@package bazar
//Auteur original :
*@author        Florian SCHMITT <florian@ecole-et-nature.org>
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
// +------------------------------------------------------------------------------------------------------+
*/

//-------------------FONCTIONS DE MISE EN PAGE DES FORMULAIRES

/** liste_choisir() - Teste si une valeur à été choisie pour une liste
*
* 
*/
function liste_choisir($element_name,$element_value) {
	if ($element_value == 0) {
		return false;
	}
	else {
		return true;
	} 
}

?>
