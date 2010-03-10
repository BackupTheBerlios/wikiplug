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
// CVS : $Id: contact.php,v 1.2 2010/03/10 14:49:28 ddelon Exp $
/**
* contact.php
*
* Description :
*
*@package wkcontact
//Auteur original :
*@author        Florian SCHMITT <florian@outils-reseaux.org>
//Autres auteurs :
*@author        Aucun
*@copyright     Kaleidos-coop.org 2008
*@version       $Revision: 1.2 $ $Date: 2010/03/10 14:49:28 $
// +------------------------------------------------------------------------------------------------------+
*/
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

//recuperation des parametres
$mail = $this->GetParameter('mail');
if (empty($mail)) {die('Action contact : parametre mail obligatoire');}
$entete = $this->GetParameter('entete');
if (empty($entete)) {$entete=$this->config['wakka_name'];}
echo '<div class="formulairemail">
<div class="note"></div>
<form id="ajax-contact-form" action="javascript:alert(\'success!\');">
	<label>Votre nom</label><input class="textbox" type="text" name="name" value="" /><br />
	<label>Votre adresse mail</label><input class="textbox" type="text" name="email" value="" /><br />
	<label>Sujet du message</label><input class="textbox" type="text" name="subject" value="" /><br />
	<label>Corps du message</label><textarea class="textbox" name="message" rows="5" cols="25"></textarea><br />
	<label>&nbsp;</label><input class="button" type="submit" name="submit" value="Envoyer" />
	<input type="hidden" name="mail" value="'.$mail.'" />
	<input type="hidden" name="entete" value="'.$entete.'" />	
	<input type="hidden" name="type" value="contact" />	
</form>
</div>
';
?>
