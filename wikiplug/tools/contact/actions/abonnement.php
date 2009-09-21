<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */
// +------------------------------------------------------------------------------------------------------+
// | PHP version 5.1                                                                                      |
// +------------------------------------------------------------------------------------------------------+
// | Copyright (C) 1999-2006 Kaleidos-coop.org                                                            |
// +------------------------------------------------------------------------------------------------------+
// | This file is part of wkbazar.                                                                     |
// |                                                                                                      |
// | Foobar is free software; you can redistribute it and/or modify                                       |
// | it under the terms of the GNU General Public License as published by                                 |
// | the Free Software Foundation; either version 2 of the License, or                                    |
// | (at your option) any later version.                                                                  |
// |                                                                                                      |
// | Foobar is distributed in the hope that it will be useful,                                            |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                                       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                        |
// | GNU General Public License for more details.                                                         |
// |                                                                                                      |
// | You should have received a copy of the GNU General Public License                                    |
// | along with Foobar; if not, write to the Free Software                                                |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                            |
// +------------------------------------------------------------------------------------------------------+
// CVS : $Id: abonnement.php,v 1.1 2009/09/21 14:54:47 mrflos Exp $
/**
* abonnement.php
*
* Description :
*
*@package wkcontact
//Auteur original :
*@author        Florian SCHMITT <florian@outils-reseaux.org>
//Autres auteurs :
*@author        Aucun
*@copyright     Kaleidos-coop.org 2008
*@version       $Revision: 1.1 $ $Date: 2009/09/21 14:54:47 $
// +------------------------------------------------------------------------------------------------------+
*/
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

//recuperation des parametres
$mail = $this->GetParameter('mail');
if (empty($mail)) {die('Action abonnement : parametre mail obligatoire');}
echo '<div class="formulairemail">
<div class="note"></div>
<form id="ajax-abonne-form" action="javascript:alert(\'success!\');">
	<label>Votre adresse mail</label>
	<input class="textbox" type="text" name="email" value="" />
	<input class="button" type="submit" name="submitnewsletter" value="S\'abonner" />
	<input type="hidden" name="mail" value="'.$mail.'" />
	<input type="hidden" name="type" value="abonne" />	
</form>
</div>
';
?>
