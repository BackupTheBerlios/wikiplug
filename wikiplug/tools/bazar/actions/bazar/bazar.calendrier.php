<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */ 
// Copyright (C) 1999-2004 Tela Botanica (accueil@tela-botanica.org)
//
// Ce logiciel est un programme informatique servant à gérer du contenu et des
// applications web.
                                                                                                      
// Ce logiciel est régi par la licence CeCILL soumise au droit français et
// respectant les principes de diffusion des logiciels libres. Vous pouvez
// utiliser, modifier et/ou redistribuer ce programme sous les conditions
// de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA 
// sur le site "http://www.cecill.info".

// En contrepartie de l'accessibilité au code source et des droits de copie,
// de modification et de redistribution accordés par cette licence, il n'est
// offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
// seule une responsabilité restreinte pèse sur l'auteur du programme,  le
// titulaire des droits patrimoniaux et les concédants successifs.

// A cet égard  l'attention de l'utilisateur est attirée sur les risques
// associés au chargement,  à l'utilisation,  à la modification et/ou au
// développement et à la reproduction du logiciel par l'utilisateur étant 
// donné sa spécificité de logiciel libre, qui peut le rendre complexe à 
// manipuler et qui le réserve donc à des développeurs et des professionnels
// avertis possédant  des  connaissances  informatiques approfondies.  Les
// utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
// logiciel à leurs besoins dans des conditions permettant d'assurer la
// sécurité de leurs systèmes et ou de leurs données et, plus généralement, 
// à l'utiliser et l'exploiter dans les mêmes conditions de sécurité. 

// Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
// pris connaissance de la licence CeCILL, et que vous en avez accepté les
// termes.
// ----
// CVS : $Id: bazar.calendrier.php,v 1.1 2008/07/07 18:00:40 mrflos Exp $
/**
* bazar_calendrier : programme affichant les evenements du bazar sous forme de Calendrier
*
*
*@package Bazar
//Auteur original :
*@author        David DELON <david.delon@clapas.net>
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:40 $
// +------------------------------------------------------------------------------------------------------+
*/
if (!defined('BAZ_VOIR_FICHE')) {
	define ('BAZ_VOIR_FICHE', 8); 
}
include_once 'configuration/baz_config.inc.php'; //fichier de configuration de Bazar
include_once 'bibliotheque/bazar.fonct.php'; //fichier des fonctions de Bazar


include_once 'bibliotheque/bazar.fonct.cal.php'; //fichier des fonctions de Bazar


function afficherContenuCorps() {
	
	return GestionAffichageCalendrier('calendrier');
	
}
	
?>
