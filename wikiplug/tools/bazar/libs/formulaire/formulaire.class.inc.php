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
// CVS : $Id: formulaire.class.inc.php,v 1.3 2010/03/04 14:19:02 mrflos Exp $
/**
* Formulaire
*
* Classe générique pour créer des formulaires
*
*@package Formulaire
//Auteur original :
*@author        Florian SCHMITT <florian@ecole-et-nature.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.3 $ $Date: 2010/03/04 14:19:02 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+
if (!function_exists('bugFixRequirePath')) {
   function bugFixRequirePath($newPath) {
       $stringPath = dirname(__FILE__);
       if (strstr($stringPath,":")) $stringExplode = "\\";
       else $stringExplode = "/";
       $paths = explode($stringExplode,$stringPath);
       $newPaths = explode("/",$newPath);
       if (count($newPaths) > 0) {
           for($i=0;$i<count($newPaths);$i++) {
               if ($newPaths[$i] == "..") array_pop($paths);
           }
           for($i=0;$i<count($newPaths);$i++) {
               if ($newPaths[$i] == "..") unset($newPaths[$i]);
           }
           reset($newPaths);
           $stringNewPath = implode($stringExplode,$paths).$stringExplode.implode($stringExplode,$newPaths);
           return $stringNewPath;
       }
   }
}
require_once bugFixRequirePath('../../../api/pear/HTML/QuickForm.php') ;
require_once bugFixRequirePath('../../../api/pear/HTML/QuickForm/html.php') ;
require_once bugFixRequirePath('../../../api/pear/HTML/QuickForm/textarea.php') ;
require_once 'formulaire.fonct.inc.php' ;

class formulaire {
	function formulaire($url, $langue, $template_champs_formulaire) {}	
	function formulaire_saisie() {}
	function formulaire_recherche() {}
	function affiche_enregistrement($id_fiche) {}
	function sauver_enregistrement($id_fiche='') {}
	function effacer_enregistrement($id_fiche) {}
	function valeurs_enregistrement($id_fiche) {}
	function tableau_valeurs_enregistrements($recherche) {}
	function genere_rss($tab_enregistrement) {}
	function genere_liste($tab_enregistrement) {}
	function genere_ical($tab_enregistrement) {}
	function genere_liste($tab_enregistrement) {}
}


class HTML_formulaire extends HTML_Quickform {
    /**
     *  Constructeur
     *
     * @param string formName Le nom du formulaire
     * @param string method Méthode post ou get
     * @param string action L'action du formulaire.
     * @param int target La cible.
     * @param Array attributes Les attributs HTML en plus.
     * @param bool trackSubmit ??
     * @return void
     * @access public
     */
    function HTML_formulaire( $formName, $action, $template_champs_formulaire, $method = "post", $target=NULL, $attributes=NULL, $trackSubmit=false ) {
		HTML_Quickform::HTML_Quickform($formName, $method, $action, $target, $attributes, $trackSubmit) ;
		$this->removeAttribute('name');
		$squelette =& $this->defaultRenderer();
		$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
		$squelette->setElementTemplate( '<p class="formulaire_element">'."\n".'<label>'."\n".
			'{label}'."\n".
			'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->&nbsp;:'."\n".
			'</label>'."\n".
			'{element}'."\n".
			'<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
			'</p>'."\n");
		$squelette->setRequiredNoteTemplate("\n".'<p class="symbole_obligatoire">*&nbsp;:&nbsp;{requiredNote}</p>'."\n");
		//Traduction de champs requis
		$this->setRequiredNote('champs obligatoire') ;
		$this->setJsWarnings('Erreur de saisie', 'Veuillez verifier vos informations saisies');
		$tableau=formulaire_valeurs_template_champs($template_champs_formulaire);
		for ($i=0; $i<count($tableau); $i++) {
			$tableau[$i]['type']($this, $tableau[$i]['nom_bdd'], $tableau[$i]['label'],
			$tableau[$i]['limite1'],
			$tableau[$i]['limite2'], $tableau[$i]['defaut'], $tableau[$i]['table_source'],
			$tableau[$i]['obligatoire']) ;
		}
	}


 /**
     *
     *
     * @return string
     * @access public
     */
    function toHTML( )
    {
        $res = HTML_QuickForm::toHTML() ;
        return $res ;
    } // end of member function toHTML
}

?>
