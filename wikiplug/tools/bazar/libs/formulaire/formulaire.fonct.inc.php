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
// CVS : $Id: formulaire.fonct.inc.php,v 1.17 2010/09/27 17:54:58 mrflos Exp $
/**
* Formulaire
*
* Les fonctions de mise en page des formulaire
*
*@package bazar
//Auteur original :
*@author        Florian SCHMITT <florian@outils-reseaux.org>
//Autres auteurs :
*@author        Aleandre GRANIER <alexandre@tela-botanica.org>
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.17 $ $Date: 2010/09/27 17:54:58 $
// +------------------------------------------------------------------------------------------------------+
*/

//comptatibilité avec PHP4...
if (version_compare(phpversion(), '5.0') < 0) {
    eval('
    function clone($object) {
      return $object;
    }
    ');
}

/** afficher_image() - génère une image en cache (gestion taille et vignettes) et l'affiche comme il faut
*
* @param    string	nom du fichier image
* @param	string	label pour l'image
* @param    string	classes html supplémentaires
* @param    int		largeur en pixel de la vignette
* @param    int		hauteur en pixel de la vignette
* @param    int		largeur en pixel de l'image redimensionnée
* @param    int		hauteur en pixel de l'image redimensionnée
* @return   void
*/
function afficher_image($nom_image, $label, $class, $largeur_vignette, $hauteur_vignette, $largeur_image, $hauteur_image) {
	//faut il créer la vignette?
	if ($hauteur_vignette!='' && $largeur_vignette!='')	{
		//la vignette n'existe pas, on la génère
		if (!file_exists('cache/vignette_'.$nom_image)) {
			$adr_img = redimensionner_image(BAZ_CHEMIN_UPLOAD.$nom_image, 'cache/vignette_'.$nom_image, $largeur_vignette, $hauteur_vignette);
		}
		list($width, $height, $type, $attr) = getimagesize('cache/vignette_'.$nom_image);
		//faut il redimensionner l'image?
		if ($hauteur_image!='' && $largeur_image!='') {
			//l'image redimensionnée n'existe pas, on la génère
			if (!file_exists('cache/image_'.$nom_image)) {
				$adr_img = redimensionner_image(BAZ_CHEMIN_UPLOAD.$nom_image, 'cache/image_'.$nom_image, $largeur_image, $hauteur_image);
			}
			//on renvoit l'image en vignette, avec quand on clique, l'image redimensionnée
			$nomidimg = str_replace('.','', $nom_image);
			return  '<a class="triggerimage'.' '.$class.'" title="'.$label.'" href="cache/image_'.$nom_image.'">'."\n".
					'<img alt="'.$nom_image.'"'.' src="cache/vignette_'.$nom_image.'" rel="#'.$nomidimg.'" width="'.$width.'" height="'.$height.'" />'."\n".
					'</a>'."\n".
					'<div class="apple_overlay" id="'.$nomidimg.'">
						<img src="cache/image_'.$nom_image.'" alt="image '.$nom_image.' pour overlay" />
					</div>'	;
		}
		else {
			//on renvoit l'image en vignette, avec quand on clique, l'image originale
			$nomidimg = str_replace('.','', $nom_image);
			return  '<a class="triggerimage'.' '.$class.'" title="'.$label.'" href="'.BAZ_CHEMIN_UPLOAD.$nom_image.'">'."\n".
					'<img alt="'.$nom_image.'"'.' src="cache/vignette_'.$nom_image.'" rel="'.$nomidimg.'" width="'.$width.'" height="'.$height.'" />'."\n".
					'</a>'."\n".
					'<div class="apple_overlay" id="'.$nomidimg.'">
						<img src="cache/image_'.$nom_image.'" alt="image '.$nom_image.' pour overlay" />
					</div>'	;
		}
	}
	//pas de vignette, mais faut il redimensionner l'image?
	else if ($hauteur_image!='' && $largeur_image!='') {
		//l'image redimensionnée n'existe pas, on la génère
		if (!file_exists('cache/image_'.$nom_image)) {
			$adr_img = redimensionner_image(BAZ_CHEMIN_UPLOAD.$nom_image, 'cache/image_'.$nom_image, $largeur_image, $hauteur_image);
		}
		//on renvoit l'image redimensionnée
		list($width, $height, $type, $attr) = getimagesize('cache/image_'.$nom_image);
		return  '<img class="'.$class.'" alt="'.$nom_image.'"'.' src="cache/image_'.$nom_image.'" width="'.$width.'" height="'.$height.'" />'."\n";
		
	}
	//on affiche l'image originale sinon
	else {
		list($width, $height, $type, $attr) = getimagesize(BAZ_CHEMIN_UPLOAD.$nom_image);
		return  '<img class="'.$class.'" alt="'.$nom_image.'"'.' src="'.BAZ_CHEMIN_UPLOAD.$nom_image.'" width="'.$width.'" height="'.$height.'" />'."\n";
	}
}

function redimensionner_image($image_src, $image_dest, $largeur, $hauteur) {
	require_once 'tools/bazar/libs/class.imagetransform.php';
	$imgTrans = new imageTransform();
	$imgTrans->sourceFile = $image_src;
	$imgTrans->targetFile = $image_dest;
	$imgTrans->resizeToWidth = $largeur;
	$imgTrans->resizeToHeight = $hauteur;
	if (!$imgTrans->resize()) {
		// in case of error, show error code
		return $imgTrans->error;
	// if there were no errors
	} else {
		return $imgTrans->targetFile;
	}
}

//-------------------FONCTIONS DE TRAITEMENT DU TEMPLATE DU FORMULAIRE

/** formulaire_valeurs_template_champs() - Découpe le template et renvoie un tableau structure
*
* @param    string  Template du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément liste
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function formulaire_valeurs_template_champs($template) {
	//Parcours du template, pour mettre les champs du formulaire avec leurs valeurs specifiques
	$tableau_template= array();
	$nblignes=0;
	//on traite le template ligne par ligne
	$chaine = explode ("\n", $template);
	foreach ($chaine as $ligne) {
		if ($ligne!='') {
			//on découpe chaque ligne par le séparateur *** (c'est historique)
			$tableau_template[$nblignes] = array_map("trim", explode ("***", $ligne));
			$nblignes++;
		}
	}
	return $tableau_template;
}

function formulaire_insertion_texte($champs, $valeur) {
	//on supprime les anciennes valeurs
	$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$champs.'"';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
	//on insere les nouvelles valeurs
	if ($valeur!='') {
		$requeteinsertion = 'INSERT INTO '.BAZ_PREFIXE.'fiche_valeur_texte (bfvt_ce_fiche, bfvt_id_element_form, bfvt_texte) VALUES ';
		$requeteinsertion .= '("'.$GLOBALS['_BAZAR_']['id_fiche'].'", "'.$champs.'", "'.mysql_escape_string(addslashes($valeur)).'")';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
	}
	if ($champs == 'bf_titre') return $champs.'="'.mysql_escape_string(addslashes($valeur)).'", ';
	else return;
}

//-------------------FONCTIONS DE MISE EN PAGE DES FORMULAIRES

/** liste() - Ajoute un élément de type liste déroulante au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément liste
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function liste(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ($mode=='saisie')
	{
		$bulledaide = '';
		if (isset($tableau_template[10]) && $tableau_template[10]!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
					' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat))
		{
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$select[0]=BAZ_CHOISIR;
		while ($ligne = $resultat->fetchRow())
		{
			$select[$ligne[1]] = $ligne[2] ;
		}
		$option = array('id' => $tableau_template[0].$tableau_template[1].$tableau_template[6]);
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$def =	$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]];
		}
		else
		{
			$def = $tableau_template[5];
		}
		require_once 'HTML/QuickForm/select.php';
		$select= new HTML_QuickForm_select($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, $select, $option);
		if ($tableau_template[4] != '') $select->setSize($tableau_template[4]);
		$select->setMultiple(0);
		$select->setValue($def);
		$formtemplate->addElement($select) ;

		if (isset($tableau_template[8]) && $tableau_template[8]==1)
		{
			$formtemplate->addRule($tableau_template[0].$tableau_template[1].$tableau_template[6], BAZ_CHOISIR_OBLIGATOIRE.' '.$tableau_template[2] , 'nonzero', '', 'client') ;
			$formtemplate->addRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		}
	}
	elseif ($mode == 'requete')
	{
		//on supprime les anciennes valeurs de la table '.BAZ_PREFIXE.'fiche_valeur_texte
		$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		//echo 'suppression : '.$requetesuppression.'<br />';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO '.BAZ_PREFIXE.'fiche_valeur_texte (bfvt_ce_fiche, bfvt_id_element_form, bfvt_texte) VALUES ';
			$requeteinsertion .= '("'.$GLOBALS['_BAZAR_']['id_fiche'].'", "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", "'.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].'")';
			//echo 'insertion : '.$requeteinsertion.'<br />';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat))
			{
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		//on affiche la liste sous forme de liste déroulante
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat))
			{
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}

			while ($ligne = $resultat->fetchRow())
			{
				$select[$ligne[1]] = $ligne[2] ;
			}
			$select[0] = BAZ_INDIFFERENT;
			$option = array('id' => $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			require_once 'HTML/QuickForm/select.php';
			$select= new HTML_QuickForm_select($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2], $select, $option);
			if ($tableau_template[4] != '') $select->setSize($tableau_template[4]);
			$select->setMultiple(0);
			$select->setValue(0);
			$formtemplate->addElement($select) ;
		}
		//on affiche la liste sous forme de checkbox
		if ($tableau_template[9]==2)
		{
			$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
			$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			require_once 'HTML/QuickForm/checkbox.php' ;
			$i=0;
			$optioncheckbox = array('class' => 'element_checkbox');

			while ($ligne = $resultat->fetchRow()) {
				if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
				$checkbox[$i]= & HTML_QuickForm::createElement('checkbox', $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
				$i++;
			}

			$squelette_checkbox =& $formtemplate->defaultRenderer();
			$squelette_checkbox->setElementTemplate( '<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
													'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
													'</legend>'."\n".'{element}'."\n".'</fieldset> '."\n"."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			
			$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2], "\n");
		}
	}
	elseif ($mode == 'requete_recherche')
	{
		if ($tableau_template[9]==1 && isset($_REQUEST[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $_REQUEST[$tableau_template[0].$tableau_template[1].$tableau_template[6]] != 0)
		{
			return ' AND bf_id_fiche IN (SELECT bfvt_ce_fiche FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_id_element_form="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'" AND bfvt_texte="'.$_REQUEST[$tableau_template[0].$tableau_template[1].$tableau_template[6]].'") ';
		}
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete = 'SELECT blv_label FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_valeur IN ('.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].') AND blv_ce_liste="'.$tableau_template[1].'" AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			$resultat->fetchInto($res);
			if (is_array($res))
			{
				$html = '<div class="BAZ_rubrique">'."\n".
						'<span class="BAZ_label">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte">';
				$html .= implode(', ', $res).'</span>'."\n".'</div>'."\n";
			}
		}
		return $html;
	}
}

/** checkbox() - Ajoute un élément de type case à cocher au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément case à cocher
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function checkbox(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ($mode == 'saisie')
	{
		$bulledaide = '';
		if (isset($tableau_template[10]) && $tableau_template[10]!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
				' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
		$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
		if (DB::isError ($resultat)) {
			echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		require_once 'HTML/QuickForm/checkbox.php' ;
		$i=0;
		$optioncheckbox = array('class' => 'element_checkbox');
		
		//valeurs par défauts
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) {
			$tab = split( ',', $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]] );
		} else {
			$tab = split( ', ', $tableau_template[5] );
		}

		while ($ligne = $resultat->fetchRow()) {
			if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
			if (in_array($ligne[1],$tab)) {
				$defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[1].']'] = true;				
			} else {
				$defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[1].']'] = false;
			}
			//$checkbox[$i]= & HTML_QuickForm::createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
			$checkbox[$i] = $formtemplate->createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox);
			$i++;
		}		
		$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, "\n");
		if (isset($tableau_template[8]) && $tableau_template[8]==1) {
			$formtemplate->addGroupRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].' obligatoire', 'required', null, 1, 'client');
		}

		$formtemplate->setDefaults($defaultValues);
		//$formtemplate->updateAttributes($defaultValues);
	}
	elseif ( $mode == 'requete' )
	{
		//on supprime les anciennes valeurs de la table '.BAZ_PREFIXE.'fiche_valeur_texte
		$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (DB::isError($resultat))
		{
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO '.BAZ_PREFIXE.'fiche_valeur_texte (bfvt_ce_fiche, bfvt_id_element_form, bfvt_texte) VALUES ';
			//pour les checkbox, les différentes valeurs sont dans un tableau
			$tab_checkbox = explode(',',$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]);
			if (is_array($tab_checkbox)) {
				$nb=0;
				foreach ($tab_checkbox as $val) {
					if ($nb>0) $requeteinsertion .= ', ';
					$requeteinsertion .= '("'.$GLOBALS['_BAZAR_']['id_fiche'].'", "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", "'.$val.'") ';
					$nb++;
				}
			}
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
			$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			require_once 'HTML/QuickForm/checkbox.php' ;
			$i=0;
			$optioncheckbox = array('class' => 'element_checkbox');

			while ($ligne = $resultat->fetchRow()) {
				if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
				$checkbox[$i]= & HTML_QuickForm::createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
				$i++;
			}

			$squelette_checkbox =& $formtemplate->defaultRenderer();
			$squelette_checkbox->setElementTemplate( '<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
													'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
													'</legend>'."\n".'{element}'."\n".'</fieldset> '."\n"."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, "\n");
		}
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete = 'SELECT blv_label FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_valeur IN ('.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].') AND blv_ce_liste='.$tableau_template[1].' AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			$tabres = array();
			while ($row =& $resultat->fetchRow()) { $tabres[]=$row[0]; }
			if (count($tabres)>0)
			{
				$html = '<div class="BAZ_rubrique">'."\n".
						'<span class="BAZ_label">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte">';
				$html .= implode(', ', $tabres).'</span>'."\n".'</div>'."\n";
			}
		}
		return $html;
	}
}

/** listedatedeb() - Ajoute un élément de type date au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément date
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function listedatedeb(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie')
	{
		$optiondate = array('language' => BAZ_LANGUE_PAR_DEFAUT,
						'minYear' => date('Y')-4,
						'maxYear'=> (date('Y')+10),
						'format' => 'd m Y',
						'addEmptyOption' => BAZ_DATE_VIDE,
						);
		$formtemplate->addElement('date', $tableau_template[1], $tableau_template[2], $optiondate) ;
		//gestion des valeurs par défaut pour modification
		if (isset($valeurs_fiche[$tableau_template[1]]))
		{
			$tableau_date = explode ('-', $valeurs_fiche[$tableau_template[1]]);
			$defs = array($tableau_template[1] => array ('d'=> $tableau_date[2], 'm'=> $tableau_date[1], 'Y'=> $tableau_date[0]));
		}
		else
		{
			//gestion des valeurs par dèfaut (date du jour)
			if (isset($tableau_template[5]) && $tableau_template[5]!='') {
				$tableau_date = explode ('-', $tableau_template[5]);
				$defs = array($tableau_template[1] => array ('d'=> $tableau_date[2], 'm'=> $tableau_date[1], 'Y'=> $tableau_date[0]));
			}

			else {
				$defs = array($tableau_template[1] => array ('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')));
			}
		}

		$formtemplate->setDefaults($defs);
		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addRule($tableau_template[1], $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		// On construit la date selon le format YYYY-mm-dd
		$date = $valeurs_fiche[$tableau_template[1]]['Y'].'-'.$valeurs_fiche[$tableau_template[1]]['m'].'-'.$valeurs_fiche[$tableau_template[1]]['d'] ;

		// si la date de fin evenement est anterieure a la date de debut, on met la date de debut
		// pour eviter les incoherence

		if ($tableau_template[1] == 'bf_date_fin_evenement' &&
				mktime(0,0,0, $valeurs_fiche['bf_date_debut_evenement']['m'], $valeurs_fiche['bf_date_debut_evenement']['d'], $valeurs_fiche['bf_date_debut_evenement']['Y']) >
				mktime(0,0,0, $valeurs_fiche['bf_date_fin_evenement']['m'], $valeurs_fiche['bf_date_fin_evenement']['d'], $valeurs_fiche['bf_date_fin_evenement']['Y'])) {
			$val = $valeurs_fiche['bf_date_debut_evenement']['Y'].'-'.$valeurs_fiche['bf_date_debut_evenement']['m'].'-'.$valeurs_fiche['bf_date_debut_evenement']['d'] ;
		} else {
			$val = $valeurs_fiche[$tableau_template[1]]['Y'].'-'.$valeurs_fiche[$tableau_template[1]]['m'].'-'.$valeurs_fiche[$tableau_template[1]]['d'] ;
		}
		formulaire_insertion_texte($tableau_template[1], $val);
		return;
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{
		$res='';
		$val=$tableau_template[1];
		if (!in_array($val, array ('bf_date_debut_validite_fiche', 'bf_date_fin_validite_fiche'))) {
			if ($valeurs_fiche[$val] != '' && $valeurs_fiche[$val] != '0000-00-00') {
				// Petit test pour afficher la date de debut et de fin d evenement
				if ($val == 'bf_date_debut_evenement' || $val == 'bf_date_fin_evenement') {
					if ($valeurs_fiche['bf_date_debut_evenement'] == $valeurs_fiche['bf_date_fin_evenement']) {
						//if ($val == 'bf_date_debut_evenement') continue;
						$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau_template[1].'_rubrique">'.BAZ_LE.':</span>'."\n";
						$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche['bf_date_debut_evenement'])).'</span>'."\n".'</div>'."\n";
					} else {

						if ($val == 'bf_date_debut_evenement') {
							$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau_template[1].'_rubrique">';
							$res .= BAZ_DU;
							$res .= '</span>'."\n".'<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche[$val])).'</span>'."\n";
						} else {
							$res .= '<span class="BAZ_label" id="'.$tableau_template[1].'_rubrique">'.BAZ_AU;
							$res .= '</span>'."\n".'<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche[$val])).'</span>'."\n".'</div>'."\n";
						}
					}
				}

				$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label '.$tableau_template[1].'_rubrique">'.$tableau_template[2].':</span>'."\n";
				$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche[$val])).'</span>'."\n".'</div>'."\n";
			}
		}
		return $res;
	}
}

/** listedatefin() - Ajoute un élément de type date au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément date
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function listedatefin(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	listedatedeb($formtemplate, $tableau_template , $mode, $valeurs_fiche);
}

/** tags() - Ajoute un élément de type mot clés (tags)
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément texte
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @param    mixed   valeur par défaut du champs
* @return   void
*/
function tags(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$tags_javascript = '';
		//gestion des mots clés déja entrés
		if (isset($valeurs_fiche[$tableau_template[1]])) {
			$tags = explode(",", mysql_escape_string($valeurs_fiche[$tableau_template[1]]));
			if (is_array($tags))
			{
				sort($tags);
				foreach ($tags as $tag) 
				{
					$tags_javascript .= 't.add(\''.$tag.'\');'."\n";
				}
			}
		}		
		
		$formtag = '<script src="tools/tags/libs/GrowingInput.js" type="text/javascript" charset="utf-8"></script>
		<script src="tools/tags/libs/tags_suggestions.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			// Autocompletion des mots clés	
			var t = new $.TextboxList(\'#'.$tableau_template[1].'\', {unique:true, inBetweenEditableBits:false, plugins:{autocomplete: {
				minLength: 1,
				queryRemote: true,
				remote: {url: \''.$GLOBALS['wiki']->href('json',$GLOBALS['_BAZAR_']['pagewiki']).'\'}
			}}});
			
			
			'.$tags_javascript.'		
		});
		</script>';
		
		
		$option=array('size'=>$tableau_template[3],'maxlength'=>$tableau_template[4], 'id' => $tableau_template[1], 'class' => 'input_texte microblog_toustags');
		$bulledaide = '';
		if (isset($tableau_template[10]) && $tableau_template[10]!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		$formtemplate->addElement('text', $tableau_template[1], $tableau_template[2].$bulledaide, $option) ;
		$formtemplate->addElement('html',$formtag);
		
		//mots clés cachés
		$formtemplate->addElement('hidden', 'mots_cles_caches', trim($tableau_template[5]));
		
		$formtemplate->applyFilter($tableau_template[1], 'addslashes') ;
	}
	elseif ( $mode == 'requete' ) {
		//on supprime les tags existants
		$GLOBALS['wiki']->DeleteTriple($GLOBALS['_BAZAR_']['id_fiche'], 'http://outils-reseaux.org/_vocabulary/tag', NULL, '', '');
		//on découpe les tags pour les mettre dans un tableau
		$liste_tags = ($valeurs_fiche['mots_cles_caches'] ? $valeurs_fiche['mots_cles_caches'].',' : '').$valeurs_fiche[$tableau_template[1]];		
		$tags = explode(",", mysql_escape_string($liste_tags));
				
		//on ajoute les tags postés
		foreach ($tags as $tag) {
			trim($tag);
			if ($tag!='') {
				$GLOBALS['wiki']->InsertTriple($GLOBALS['_BAZAR_']['id_fiche'], 'http://outils-reseaux.org/_vocabulary/tag', $tag, '', '');
			}			
		}
		//on copie tout de meme dans les metadonnées
		return formulaire_insertion_texte($tableau_template[1], $valeurs_fiche[$tableau_template[1]]);
	}
	elseif ($mode == 'html') {
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='') {
			$html = '<div class="BAZ_rubrique">'."\n".
						'<span class="BAZ_label">'.$tableau_template[2].':</span>'."\n";
			$html .= '<div class="BAZ_texte"> ';
			$tags = explode(',',htmlentities($valeurs_fiche[$tableau_template[1]]));
			if (is_array($tags)) {
				$html .= '<ul class="liste_tags_en_ligne">'."\n";
				foreach ($tags as $tag) {
					$html .= '<li class="textboxlist-bit-box">'.$tag.'</li>'."\n";
				}	
				$html .= '</ul>'."\n";
			}
			
			$html .= '</div>'."\n".'</div>'."\n";
		}
		return $html;
	}
}



/** texte() - Ajoute un élément de type texte au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément texte
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function texte(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$option=array('size'=>$tableau_template[3],'maxlength'=>$tableau_template[4], 'id' => $tableau_template[1], 'class' => 'input_texte');
		$bulledaide = '';
		if (isset($tableau_template[10]) && $tableau_template[10]!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		$formtemplate->addElement('text', $tableau_template[1], $tableau_template[2].$bulledaide, $option) ;
		
		//gestion des valeurs par défaut : d'abord on regarde s'il y a une valeur à modifier,
		//puis s'il y a une variable passée en GET,
		//enfin on prend la valeur par défaut du formulaire sinon
		if (isset($valeurs_fiche[$tableau_template[1]])) {
			$defauts = array( $tableau_template[1] => $valeurs_fiche[$tableau_template[1]] );
		}
		elseif (isset($_GET[$tableau_template[1]])) {
			$defauts = array( $tableau_template[1] => stripslashes($_GET[$tableau_template[1]]) );
		} else {
			$defauts = array( $tableau_template[1] => stripslashes($tableau_template[5]) );
		}
		$formtemplate->setDefaults($defauts);
		$formtemplate->applyFilter($tableau_template[1], 'addslashes') ;
		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1))
		{
			$formtemplate->addRule($tableau_template[1],  $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		}
		//gestion du champs numerique
		if (($tableau_template[9]==0) && isset($tableau_template[6]) && ($tableau_template[6]==1))
		{
			$formtemplate->addRule($tableau_template[1],  $tableau_template[2].' doit etre numérique', 'numeric', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		return formulaire_insertion_texte($tableau_template[1], $valeurs_fiche[$tableau_template[1]]);
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='')
		{
			if ($tableau_template[1] == 'bf_titre')
			{
				// Le titre
				$html .= '<h1 class="BAZ_fiche_titre">'.htmlentities($valeurs_fiche[$tableau_template[1]]).'</h1>'."\n";
			}
			else
			{
				$html = '<div class="BAZ_rubrique">'."\n".
						'<span class="BAZ_label">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte"> ';
				$html .= htmlentities($valeurs_fiche[$tableau_template[1]]).'</span>'."\n".'</div>'."\n";
			}
		}
		//else
		//{
		//	$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
		//				'<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
		//	$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description"> ';
		//	$html .= NON_RENSEIGNE.'</span>'."\n".'</div>'."\n";
		//}
		return $html;
	}
}


/** utilisateur_wikini() - Ajoute un élément de type texte pour créer un utilisateur wikini au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément texte
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function utilisateur_wikini(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$option=array('size'=>$tableau_template[3],'maxlength'=>$tableau_template[4], 'id' => 'nomwiki');
		if (isset($tableau_template[5]) && $tableau_template[5]!='')
		{
			$option['readonly'] = 'readonly';
			//on entre le NomWiki
			$formtemplate->addElement('text', 'nomwiki', "NomWiki", $option) ;
			$defs=array('nomwiki'=>stripslashes($tableau_template[5]));
			$formtemplate->setDefaults($defs);
			$formtemplate->applyFilter('nomwiki', 'addslashes') ;
			$formtemplate->addRule('nomwiki',  'NomWiki obligatoire', 'required', '', 'client') ;
			//test nomWiki du connecté, pour savoir s'il peut changer son mot de passe
			if ($GLOBALS['_BAZAR_']['nomwiki']['name']==$tableau_template[5])
			{
				require_once 'HTML/QuickForm/html.php';
				$formhtml= new HTML_QuickForm_html('<tr>'."\n".'<td>&nbsp;</td>'."\n".'<td style="text-align:left;"><a href="'.$GLOBALS['wiki']->href('','ChangePassword','').'" target="_blank">Changer son mot de passe</a></td>'."\n".'</tr>'."\n");
				$formtemplate->addElement($formhtml) ;
			}
		}
		elseif (!isset($tableau_template[5]) || $tableau_template[5]=='')
		{
			//mot de passe
			$formtemplate->addElement('password', 'mot_de_passe_wikini', 'mot de passe', array('size' => $tableau_template[3])) ;
			$formtemplate->addElement('password', 'mot_de_passe_repete_wikini', 'mot de passe (v&eacute;rification)', array('size' => $tableau_template[3])) ;
			$formtemplate->addRule('mot_de_passe_wikini', 'mot de passe obligatoire', 'required', '', 'client') ;
			$formtemplate->addRule('mot_de_passe_repete_wikini', 'mot de passe r&eacute;p&eacute;t&eacute; obligatoire', 'required', '', 'client') ;
			$formtemplate->addRule(array ('mot_de_passe_wikini', 'mot_de_passe_repete_wikini'), 'Les mots de passe doivent être identiques', 'compare', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		//si bf_nom_wikini n'existe pas, on insére un nouvel utilisateur wikini
		$resultat = $GLOBALS['_BAZAR_']['db']->query('SELECT name FROM '.$GLOBALS['wiki']->config["table_prefix"].'users WHERE name="'.$valeurs_fiche['nomwiki'].'"');
		if ($resultat->numRows()==0)
		{
			$nomwiki = baz_nextWiki(genere_nom_wiki($valeurs_fiche['bf_titre']));
			$requeteinsertionuserwikini = 'INSERT INTO '.$GLOBALS['wiki']->config["table_prefix"]."users SET ".
					"signuptime = now(), ".
					"name = '".mysql_escape_string($nomwiki)."', ".
					"email = '".mysql_escape_string($valeurs_fiche['bf_mail'])."', ".
					"password = md5('".mysql_escape_string($valeurs_fiche['mot_de_passe_wikini'])."')";
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertionuserwikini) ;
			if (DB::isError($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			return 'bf_nom_wikini="'.mysql_escape_string($nomwiki).'", ' ;
			//envoi mail nouveau mot de passe
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['wiki']->config["base_url"]);
			$objetmail = '['.str_replace("http://","",$lien).'] Vos nouveaux identifiants sur le site '.$GLOBALS['wiki']->config["wakka_name"];
			$messagemail = "Bonjour!\n\nVotre inscription sur le site a été finalisée, dorénavant vous pouvez vous identifier avec les informations suivantes :\n\nVotre identifiant NomWiki : ".$nomwiki."\nVotre mot de passe : ". $valeurs_fiche['mot_de_passe_wikini'] . "\n\nA très bientôt !\n\nSylvie Vernet, webmestre";
			$headers =   'From: '.BAZ_ADRESSE_MAIL_ADMIN . "\r\n" .
			     'Reply-To: '.BAZ_ADRESSE_MAIL_ADMIN . "\r\n" .
			     'X-Mailer: PHP/' . phpversion();
			mail($valeurs_fiche['bf_mail'], remove_accents($objetmail), $messagemail, $headers);
		} elseif (isset($valeurs_fiche['mot_de_passe_wikini'])) {
			$requetemodificationuserwikini = 'UPDATE '.$GLOBALS['wiki']->config["table_prefix"]."users SET ".
					"email = '".mysql_escape_string($valeurs_fiche['bf_mail'])."', ".
					"password = md5('".mysql_escape_string($valeurs_fiche['mot_de_passe_wikini'])."') WHERE name=\"".$valeurs_fiche['bf_nom_wikini']."\"";
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requetemodificationuserwikini) ;
			if (DB::isError($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			//envoi mail nouveau mot de passe
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['wiki']->config["base_url"]);
			$objetmail = '['.str_replace("http://","",$lien).'] Vos nouveaux identifiants sur le site '.$GLOBALS['wiki']->config["wakka_name"];
			$messagemail = "Bonjour!\n\nVotre inscription sur le site a été modifiée, dorénavant vous pouvez vous identifier avec les informations suivantes :\n\nVotre identifiant NomWiki : ".$nomwiki."\nVotre mot de passe : ". $valeurs_fiche['mot_de_passe_wikini'] . "\n\nA très bientôt !\n\nSylvie Vernet, webmestre";
			$headers =   'From: '.BAZ_ADRESSE_MAIL_ADMIN . "\r\n" .
			     'Reply-To: '.BAZ_ADRESSE_MAIL_ADMIN . "\r\n" .
			     'X-Mailer: PHP/' . phpversion();
			mail($valeurs_fiche['bf_mail'], remove_accents($objetmail), $messagemail, $headers);
		}
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{

	}
}


/** champs_cache() - Ajoute un élément caché au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément caché
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @param    mixed   Le tableau des valeurs de la fiche
*
* @return   void
*/
function champs_cache(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$formtemplate->addElement('hidden', $tableau_template[1], $tableau_template[2], array ('id' => $tableau_template[1])) ;
		//gestion des valeurs par défaut
		$defs=array($tableau_template[1]=>$tableau_template[5]);
		$formtemplate->setDefaults($defs);
	}
	elseif ( $mode == 'requete' )
	{
		formulaire_insertion_texte($tableau_template[1], $valeurs_fiche[$tableau_template[1]]);
		return;
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{

	}
}


/** champs_mail() - Ajoute un élément texte formaté comme un mail au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément texte
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function champs_mail(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$option=array('size'=>$tableau_template[3],'maxlength'=>$tableau_template[4], 'id' => $tableau_template[1], 'class' => 'input_texte');
		$formtemplate->addElement('text', $tableau_template[1], $tableau_template[2], $option) ;
		//gestion des valeurs par défaut : d'abord on regarde s'il y a une valeur à modifier,
		//puis s'il y a une variable passée en GET,
		//enfin on prend la valeur par défaut du formulaire sinon
		if (isset($valeurs_fiche[$tableau_template[1]])) {
			$defauts = array( $tableau_template[1] => $valeurs_fiche[$tableau_template[1]] );
		}
		elseif (isset($_GET[$tableau_template[1]])) {
			$defauts = array( $tableau_template[1] => stripslashes($_GET[$tableau_template[1]]) );
		} else {
			$defauts = array( $tableau_template[1] => stripslashes($tableau_template[5]) );
		}
		$formtemplate->setDefaults($defauts);
		$formtemplate->applyFilter($tableau_template[1], 'addslashes') ;
		//$formtemplate->addRule($tableau_template[1],  $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		$formtemplate->addRule($tableau_template[1], 'Format de l\'adresse mail incorrect', 'email', '', 'client') ;
		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addRule($tableau_template[1],  $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		formulaire_insertion_texte($tableau_template[1], $valeurs_fiche[$tableau_template[1]]);
		return;
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='')
		{
			$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
					'<span class="BAZ_label BAZ_label_'.$GLOBALS['_BAZAR_']['class'].'">'.$tableau_template[2].':</span>'."\n";
			$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'"><a href="mailto:'.$valeurs_fiche[$tableau_template[1]].'" class="BAZ_lien_mail">';
			$html .= $valeurs_fiche[$tableau_template[1]].'</a></span>'."\n".'</div>'."\n";
		}
		return $html;
	}
}

/** mot_de_passe() - Ajoute un élément de type mot de passe au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément mot de passe
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function mot_de_passe(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$formtemplate->addElement('password', 'mot_de_passe', $tableau_template[2], array('size' => $tableau_template[3])) ;
		$formtemplate->addElement('password', 'mot_de_passe_repete', $tableau_template[7], array('size' => $tableau_template[3])) ;
		$formtemplate->addRule('mot_de_passe', $tableau_template[5], 'required', '', 'client') ;
		$formtemplate->addRule('mot_de_passe_repete', $tableau_template[5], 'required', '', 'client') ;
		$formtemplate->addRule(array ('mot_de_passe', 'mot_de_passe_repete'), $tableau_template[5], 'compare', '', 'client') ;
	}
	elseif ( $mode == 'requete' )
	{
		//on mets les slashes pour les saisies dans les champs texte et textearea
		$val=addslashes($valeurs_fiche['mot_de_passe']) ;
		return $tableau_template[1].'="'.$val.'", ' ;
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{

	}
}


/** textelong() - Ajoute un élément de type texte long (textarea) au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément texte long
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function textelong(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	list($type, $identifiant, $label, $nb_colonnes, $nb_lignes, $valeur_par_defaut, $longueurmax, $formatage , $obligatoire, $apparait_recherche, $bulle_d_aide) = $tableau_template;
	if ( $mode == 'saisie' )
	{
		$options = array('id' => $identifiant, 'class' => 'input_textarea');
		if ($longueurmax != '') $options['maxlength'] = $longueurmax;
		$longueurmax = ($longueurmax ? '<span class="charsRemaining"> ('.$longueurmax.' caract&egrave;res restants)</span>' : '' );
		$bulledaide = '';
		if ($bulle_d_aide!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($bulle_d_aide).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		$formtexte= new HTML_QuickForm_textarea($identifiant, $label.$longueurmax.$bulledaide, $options);
		$formtexte->setCols($nb_colonnes);
		$formtexte->setRows($nb_lignes);
		$formtemplate->addElement($formtexte) ;
		
		//gestion des valeurs par défaut : d'abord on regarde s'il y a une valeur à modifier,
		//puis s'il y a une variable passée en GET,
		//enfin on prend la valeur par défaut du formulaire sinon
		if (isset($valeurs_fiche[$identifiant])) {
			$defauts = array( $identifiant => $valeurs_fiche[$identifiant] );
		}
		elseif (isset($_GET[$identifiant])) {
			$defauts = array( $identifiant => stripslashes($_GET[$identifiant]) );
		} else {
			$defauts = array( $identifiant => stripslashes($tableau_template[5]) );
		}
		$formtemplate->setDefaults($defauts);
		
		$formtemplate->applyFilter($identifiant, 'addslashes') ;
		//gestion du champs obligatoire
		if (($apparait_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
			$formtemplate->addRule($identifiant,  $label.' obligatoire', 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		//on supprime les anciennes valeurs
		$requetesuppression = 'DELETE FROM '.BAZ_PREFIXE.'triples WHERE property="'.$identifiant.'" resource="'.$GLOBALS['_BAZAR_']['id_fiche'].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		//on insere les nouvelles valeurs
		$requeteinsertion = 'INSERT INTO '.BAZ_PREFIXE.'triples (resource, property, value) VALUES ';
        $requeteinsertion .= '("'.$GLOBALS['_BAZAR_']['id_fiche'].'", "'.$identifiant.'", "'.addslashes($valeurs_fiche[$identifiant]).'")';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
		return;
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$identifiant]) && $valeurs_fiche[$identifiant]!='')
		{
			$html = '<div class="BAZ_rubrique">'."\n".
					'<span class="BAZ_label '.$identifiant.'_rubrique">'.$label.':</span>'."\n";
			$html .= '<span class="BAZ_texte '.$identifiant.'_description"> ';
			if ($formatage == 'wiki') {
				//echo $GLOBALS['wiki']->Format($valeurs_fiche[$identifiant]);
				$html .= $GLOBALS['wiki']->Format($valeurs_fiche[$identifiant]);
				//$html .= $valeurs_fiche[$identifiant];
			}
			elseif ($formatage == 'nohtml') {
				$html .= htmlentities($valeurs_fiche[$identifiant]);
			}
			else {
				$html .= nl2br($valeurs_fiche[$identifiant]);
			}
			$html .= '</span>'."\n".'</div>'."\n";
		}
		return $html;
	}
}



/** url() - Ajoute un élément de type url internet au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément url internet
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/


/** lien_internet() - Ajoute un élément de type texte contenant une URL au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément texte url
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function lien_internet(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ($mode == 'saisie')
	{
		//recherche des URLs deja entrees dans la base
		$html_url= '';
		$option=array('size'=>$tableau_template[3],'maxlength'=>$tableau_template[4], 'id' => $tableau_template[1], 'class' => 'input_texte');
		$bulledaide = '';
		if (isset($tableau_template[10]) && $tableau_template[10]!='') {
			$bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		}
		$formtemplate->addElement('text', $tableau_template[1], $tableau_template[2].$bulledaide, $option)	;
		//gestion des valeurs par défaut : d'abord on regarde s'il y a une valeur à modifier,
		//puis s'il y a une variable passée en GET,
		//enfin on prend la valeur par défaut du formulaire sinon
		if (isset($valeurs_fiche[$tableau_template[1]])) {
			$defauts = array( $tableau_template[1] => $valeurs_fiche[$tableau_template[1]] );
		}
		elseif (isset($_GET[$tableau_template[1]])) {
			$defauts = array( $tableau_template[1] => stripslashes($_GET[$tableau_template[1]]) );
		} else {
			$defauts = array( $tableau_template[1] => stripslashes($tableau_template[5]) );
		}
		$formtemplate->setDefaults($defauts);
		
		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addRule($tableau_template[1], URL_LIEN_REQUIS, 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		//on supprime la valeur, si elle est restée par défaut
		if ($valeurs_fiche[$tableau_template[1]]=='http://') $valeurs_fiche[$tableau_template[1]]='';
		formulaire_insertion_texte($tableau_template[1], $valeurs_fiche[$tableau_template[1]]);
		return;
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='')
		{
			$html .= '<div class="BAZ_rubrique">'."\n".
					 '<span class="BAZ_label">'.$tableau_template[2].':</span>'."\n";
			$html .= '<span class="BAZ_texte">'."\n".
					 '<a href="'.$valeurs_fiche[$tableau_template[1]].'" class="BAZ_lien" target="_blank">';
			$html .= $valeurs_fiche[$tableau_template[1]].'</a></span>'."\n".'</div>'."\n";
		}
		return $html;
	}
}

/** fichier() - Ajoute un élément de type fichier au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément fichier
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function fichier(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	list($type, $identifiant, $label, $taille_maxi, $taille_maxi2, $hauteur, $largeur, $alignement, $obligatoire, $apparait_recherche, $bulle_d_aide) = $tableau_template;
	if ($mode == 'saisie')
	{
		//AJOUTER DES FICHIERS JOINTS
		$html= '';
		if ($bulle_d_aide!='') $label = $label.' <img class="tooltip_aide" title="'.htmlentities($bulle_d_aide).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		if (isset($valeurs_fiche[$type.$identifiant])) {
			$lien_supprimer=clone($GLOBALS['_BAZAR_']['url']);
			$lien_supprimer->addQueryString('action', $_GET['action']);
			$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']["id_fiche"]);
			//$lien_supprimer->addQueryString('typeannonce', $_REQUEST['typeannonce']);
			$lien_supprimer->addQueryString('fichier', 1);
			$html .= $valeurs_fiche[$type.$identifiant]."\n".
			'<a href="'.str_replace('&', '&amp;', $lien_supprimer->getURL()).'" onclick="javascript:return confirm(\''.BAZ_CONFIRMATION_SUPPRESSION_FICHIER.'\');" >'.BAZ_SUPPRIMER.'</a><br />'."\n";
		}
		$formtemplate->addElement('html', $html) ;
		$formtemplate->addElement('file', $type.$identifiant, $label) ;

		//gestion du champs obligatoire
		if (($apparait_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
			$formtemplate->addRule($type.$identifiant, FICHIER_REQUIS, 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
			if (isset($_FILES[$type.$identifiant]['name']) && $_FILES[$type.$identifiant]['name']!='') {
				//on enleve les accents sur les noms de fichiers, et les espaces
				$nomfichier = preg_replace("/&([a-z])[a-z]+;/i","$1", htmlentities($identifiant.'_'.$_FILES[$type.$identifiant]['name']));
				$nomfichier = str_replace(' ', '_', $nomfichier);
				$chemin_destination=BAZ_CHEMIN_UPLOAD.$nomfichier;
				//verification de la presence de ce fichier
				if (!file_exists($chemin_destination)) {
					move_uploaded_file($_FILES[$type.$identifiant]['tmp_name'], $chemin_destination);
					chmod ($chemin_destination, 0755);
				}
				else echo 'fichier déja existant<br />';
				formulaire_insertion_texte($type.$identifiant, $nomfichier);
				return ;
			}
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$type.$identifiant]) && $valeurs_fiche[$type.$identifiant]!='')
		{
			$html = '<div class="BAZ_fichier">T&eacute;l&eacute;charger le fichier : <a href="'.BAZ_CHEMIN_UPLOAD.$valeurs_fiche[$type.$identifiant].'">'.$valeurs_fiche[$type.$identifiant].'</a>'."\n";
		}
		if ($html!='') $html .= '</div>'."\n";
		return $html;
	}
}

/** image() - Ajoute un élément de type image au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément image
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function image(&$formtemplate, $tableau_template, $mode, $valeurs_fiche) {
	list($type, $identifiant, $label, $hauteur_vignette, $largeur_vignette, $hauteur_image, $largeur_image, $class, $obligatoire, $apparait_recherche, $bulle_d_aide) = $tableau_template;
	
	if ( $mode == 'saisie') {
		//on vérifie qu'il ne faut supprimer l'image
		if (isset($_GET['suppr_image']) && $valeurs_fiche[$type.$identifiant]==$_GET['suppr_image']) {
			//on efface le fichier s'il existe
			if (file_exists(BAZ_CHEMIN_UPLOAD.$valeurs_fiche[$type.$identifiant])) {
				unlink(BAZ_CHEMIN_UPLOAD.$valeurs_fiche[$type.$identifiant]);
			}
			
			//on efface une entrée de la base de données
			$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvt_id_element_form="'.$type.$identifiant.'" AND bfvt_texte="'.$valeurs_fiche[$type.$identifiant].'" LIMIT 1';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
			
			//on affiche les infos sur l'effacement du fichier, et on réinitialise la variable pour le fichier pour faire apparaitre le formulaire d'ajout par la suite
			echo '<div class="BAZ_info">'.BAZ_FICHIER.$valeurs_fiche[$type.$identifiant].BAZ_A_ETE_EFFACE.'</div>'."\n";
			$valeurs_fiche[$type.$identifiant] = '';
		}
		
		if ($bulle_d_aide!='') $labelbulle = $label.' <img class="tooltip_aide" title="'.htmlentities($bulle_d_aide).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		
		//cas ou il y a une image dans la base de données
		if (isset($valeurs_fiche[$type.$identifiant]) && $valeurs_fiche[$type.$identifiant] != '') {			
			
			//il y a bien le fichier image, on affiche l'image, avec possibilité de la supprimer ou de la modifier
			if (file_exists(BAZ_CHEMIN_UPLOAD.$valeurs_fiche[$type.$identifiant])) {
				
				require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'HTML/QuickForm/html.php';
				$formtemplate->addElement(new HTML_QuickForm_html("\n".'<fieldset class="bazar_fieldset">'."\n".'<legend>'.$labelbulle.'</legend>'."\n")) ;
				
				$lien_supprimer=clone($GLOBALS['_BAZAR_']['url']);
				$lien_supprimer->addQueryString('action', $_GET['action']);
				$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']["id_fiche"]);
				$lien_supprimer->addQueryString('suppr_image', $valeurs_fiche[$type.$identifiant]);
				
				$html_image = afficher_image($valeurs_fiche[$type.$identifiant], $label, $class, $largeur_vignette, $hauteur_vignette, $largeur_image, $hauteur_image);
				$lien_supprimer_image .= '<a class="BAZ_lien_supprimer" href="'.str_replace('&', '&amp;', $lien_supprimer->getURL()).'" onclick="javascript:return confirm(\''.
				BAZ_CONFIRMATION_SUPPRESSION_IMAGE.'\');" >'.BAZ_SUPPRIMER_IMAGE.'</a>'."\n";
				if ($html_image!='') $formtemplate->addElement('html', $html_image) ;
				$formtemplate->addElement('file', $type.$identifiant, $lien_supprimer_image.BAZ_MODIFIER_IMAGE) ;
				$formtemplate->addElement(new HTML_QuickForm_html("\n".'</fieldset>'."\n")) ;
			}
			
			//le fichier image n'existe pas, du coup on efface l'entrée dans la base de données
			else {
				echo '<div class="BAZ_error">'.BAZ_FICHIER.$valeurs_fiche[$type.$identifiant].BAZ_FICHIER_IMAGE_INEXISTANT.'</div>'."\n";
				//on efface une entrée de la base de données dont le fichier n'existe pas
				$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$type.$identifiant.'" AND bfvt_texte="'.$valeurs_fiche[$type.$identifiant].'" LIMIT 1';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
			}
		} 
		//cas ou il n'y a pas d'image dans la base de données, on affiche le formulaire d'envoi d'image
		else {
			$formtemplate->addElement('file', $type.$identifiant, $labelbulle) ;
			//gestion du champs obligatoire
			if (($apparait_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
				$formtemplate->addRule('image', IMAGE_VALIDE_REQUIS, 'required', '', 'client') ;
			}
			
			//TODO: la vérification du type de fichier ne marche pas
			$tabmime = array ('gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png');
			$formtemplate->addRule($type.$identifiant, 'Vous devez BAZ_CHOISIR une fichier de type image gif, jpg ou png', 'mimetype', $tabmime );
		}
	}
	elseif ( $mode == 'requete' ) {
			if (isset($_FILES[$type.$identifiant]['name']) && $_FILES[$type.$identifiant]['name']!='') {
				//dans le cas d'une modification, on vérifie l'existance d'une image précédente, que l'on supprime et remplace
				if (isset($GLOBALS['_BAZAR_']['id_fiche'])) {
					$requete_nom_ancienne_image = 'SELECT bfvt_texte FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$type.$identifiant.'"';
					$resultat = $GLOBALS['_BAZAR_']['db']->query($requete_nom_ancienne_image) ;
					$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
					$ancienne_image = $ligne['bfvt_texte'];
					
					//on efface le fichier s'il existe
					if (file_exists(BAZ_CHEMIN_UPLOAD.$ancienne_image)) {
						unlink(BAZ_CHEMIN_UPLOAD.$ancienne_image);
					}
				}
								
				//on enleve les accents sur les noms de fichiers, et les espaces
				$nomimage = preg_replace("/&([a-z])[a-z]+;/i","$1", htmlentities($identifiant.$_FILES[$type.$identifiant]['name']));
				$nomimage = str_replace(' ', '_', $nomimage);
				$chemin_destination=BAZ_CHEMIN_UPLOAD.$nomimage;
				//verification de la presence de ce fichier
				if (!file_exists($chemin_destination)) {
					move_uploaded_file($_FILES[$type.$identifiant]['tmp_name'], $chemin_destination);
					chmod ($chemin_destination, 0755);
					//génération des vignettes
					if ($hauteur_vignette!='' && $largeur_vignette!='' && !file_exists('cache/vignette_'.$nomimage)) {
						$adr_img = redimensionner_image($chemin_destination, 'cache/vignette_'.$nomimage, $largeur_vignette, $hauteur_vignette);
					}
					//génération des images
					if ($hauteur_image!='' && $largeur_image!='' && !file_exists('cache/image_'.'_'.$nomimage)) {
						$adr_img = redimensionner_image($chemin_destination, 'cache/image_'.$nomimage, $largeur_image, $hauteur_image);
					}
				}
				else {
					echo '<div class="BAZ_error">L\'image '.$nomimage.' existait d&eacute;ja, elle n\'a pas &eacute;t&eacute; remplac&eacute;e.</div>';
				}
				formulaire_insertion_texte($type.$identifiant, $nomimage);
				return ;
			}
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{
		if (isset($valeurs_fiche[$type.$identifiant]) && $valeurs_fiche[$type.$identifiant]!='' && file_exists(BAZ_CHEMIN_UPLOAD.$valeurs_fiche[$type.$identifiant]) )
		{
			return afficher_image($valeurs_fiche[$type.$identifiant], $label, $class, $largeur_vignette, $hauteur_vignette, $largeur_image, $hauteur_image);
		}
	}
}

/** labelhtml() - Ajoute du texte HTML au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour le texte HTML
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function labelhtml(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	list($type, $texte_saisie, $texte_recherche, $texte_fiche) = $tableau_template;

	if ( $mode == 'saisie' )
	{
		require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'HTML/QuickForm/html.php';
		$formtemplate->addElement(new HTML_QuickForm_html("\n".$texte_saisie."\n")) ;
	}
	elseif ( $mode == 'requete' )
	{
		return;
	}
	elseif ($mode == 'formulaire_recherche')
	{
		$formtemplate->addElement('html', $texte_recherche);
	}
	elseif ($mode == 'html')
	{
		return $texte_fiche."\n";
	}
}

/** carte_google() - Ajoute un élément de carte google au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour la carte google
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function carte_google(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	list($type, $lat, $lon, $classe, $obligatoire) = $tableau_template;
	
	if ( $mode == 'saisie' )
	{
		if (isset($valeurs_fiche['carte_google'])) {
			$tab=explode('|', $valeurs_fiche['carte_google']);
			if (count($tab)>1) {
				$defauts = array( $lat => $tab[0], $lon => $tab[1] );
				$formtemplate->setDefaults($defauts);
			}
		}

		$html_bouton = '<div class="titre_carte_google">'.METTRE_POINT.'</div>';

		$html_bouton .= '<input class="btn_adresse" onclick="showAddress();" name="chercher_sur_carte" value="'.VERIFIER_MON_ADRESSE.'" type="button" />
	<input class="btn_client" onclick="showClientAddress();" name="chercher_client" value="'.VERIFIER_MON_ADRESSE_CLIENT.'" type="button" />';

		$scriptgoogle = '//-----------------------------------------------------------------------------------------------------------
	//--------------------TODO : ATTENTION CODE FACTORISABLE-----------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------
	var geocoder;
	var map;
	var marker;
	var infowindow;

	function initialize() {
		geocoder = new google.maps.Geocoder();
		var myLatlng = new google.maps.LatLng('.BAZ_GOOGLE_CENTRE_LAT.', '.BAZ_GOOGLE_CENTRE_LON.');
		var myOptions = {
		  zoom: '.BAZ_GOOGLE_ALTITUDE.',
		  center: myLatlng,
		  mapTypeId: google.maps.MapTypeId.'.BAZ_TYPE_CARTO.',
		  navigationControl: '.BAZ_AFFICHER_NAVIGATION.',
		  navigationControlOptions: {style: google.maps.NavigationControlStyle.'.BAZ_STYLE_NAVIGATION.'},
		  mapTypeControl: '.BAZ_AFFICHER_CHOIX_CARTE.',
		  mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.'.BAZ_STYLE_CHOIX_CARTE.'},
		  scaleControl: '.BAZ_AFFICHER_ECHELLE.' ,
		  scrollwheel: '.BAZ_PERMETTRE_ZOOM_MOLETTE.'
		}
		map = new google.maps.Map(document.getElementById("map"), myOptions);

		//on pose un point si les coordonnées existent déja (cas d\'une modification de fiche)
		if (document.getElementById("latitude") && document.getElementById("latitude").value != \'\' &&
			document.getElementById("longitude") && document.getElementById("longitude").value != \'\' ) {
			var lat = document.getElementById("latitude").value;
			var lon = document.getElementById("longitude").value;
			latlngclient = new google.maps.LatLng(lat,lon);
			map.setCenter(latlngclient);
			infowindow = new google.maps.InfoWindow({
				content: "<h4>Votre emplacement<\/h4>'.TEXTE_POINT_DEPLACABLE.'",
				maxWidth: 250
			});
			//image du marqueur
			var image = new google.maps.MarkerImage(\''.BAZ_IMAGE_MARQUEUR.'\',
			//taille, point d\'origine, point d\'arrivee de l\'image
			new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_MARQUEUR.'));

			//ombre du marqueur
			var shadow = new google.maps.MarkerImage(\''.BAZ_IMAGE_OMBRE_MARQUEUR.'\',
			// taille, point d\'origine, point d\'arrivee de l\'image de l\'ombre
			new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_OMBRE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_OMBRE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_OMBRE_MARQUEUR.'));

			marker = new google.maps.Marker({
				position: latlngclient,
				map: map,
				icon: image,
				shadow: shadow,
				title: \'Votre emplacement\',
				draggable: true
			});
			infowindow.open(map,marker);
			google.maps.event.addListener(marker, \'click\', function() {
			  infowindow.open(map,marker);
			});
			google.maps.event.addListener(marker, "dragend", function () {
				var lat = document.getElementById("latitude");lat.value = marker.getPosition().lat();
				var lon = document.getElementById("longitude");lon.value = marker.getPosition().lng();
				map.setCenter(marker.getPosition());
			});
		}
	};

	function showClientAddress(){
		// If ClientLocation was filled in by the loader, use that info instead
		if (google.loader.ClientLocation) {
		  latlngclient = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
		  if(infowindow) {
			infowindow.close();
		  }
		  if(marker) {
			marker.setMap(null);
		  }
		  map.setCenter(latlngclient);
			var lat = document.getElementById("latitude");lat.value = map.getCenter().lat();
			var lon = document.getElementById("longitude");lon.value = map.getCenter().lng();

			infowindow = new google.maps.InfoWindow({
				content: "<h4>Votre emplacement<\/h4>'.TEXTE_POINT_DEPLACABLE.'",
				maxWidth: 250
			});
			//image du marqueur
			var image = new google.maps.MarkerImage(\''.BAZ_IMAGE_MARQUEUR.'\',
			//taille, point d\'origine, point d\'arrivee de l\'image
			new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_MARQUEUR.'));

			//ombre du marqueur
			var shadow = new google.maps.MarkerImage(\''.BAZ_IMAGE_OMBRE_MARQUEUR.'\',
			// taille, point d\'origine, point d\'arrivee de l\'image de l\'ombre
			new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_OMBRE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_OMBRE_MARQUEUR.'),
			new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_OMBRE_MARQUEUR.'));

			marker = new google.maps.Marker({
				position: latlngclient,
				map: map,
				icon: image,
				shadow: shadow,
				title: \'Votre emplacement\',
				draggable: true
			});
			infowindow.open(map,marker);
			google.maps.event.addListener(marker, \'click\', function() {
			  infowindow.open(map,marker);
			});
			google.maps.event.addListener(marker, "dragend", function () {
				var lat = document.getElementById("latitude");lat.value = marker.getPosition().lat();
				var lon = document.getElementById("longitude");lon.value = marker.getPosition().lng();
				map.setCenter(marker.getPosition());
			});
		}
		else {alert("Localisation par votre accès Internet impossible..");}
	};

	function showAddress() {

	  if (document.getElementById("bf_adresse1")) 	var adress_1 = document.getElementById("bf_adresse1").value ; else var adress_1 = "";
	  if (document.getElementById("bf_adresse2")) 	var adress_2 = document.getElementById("bf_adresse2").value ; else var adress_2 = "";
	  if (document.getElementById("bf_ville")) 	var ville = document.getElementById("bf_ville").value ; else var ville = "";
	  if (document.getElementById("bf_code_postal")) var cp = document.getElementById("bf_code_postal").value ; else var cp = "";
	  if (document.getElementById("bf_ce_pays")) var pays = document.getElementById("bf_ce_pays").value ; else if (document.getElementById("liste3").selectedIndex)  {
		   var selectIndex=document.getElementById("liste3").selectedIndex;
		   var pays = document.getElementById("liste3").options[selectIndex].text ;
	  } else {
		  var pays = "";
	  };



	  var address = adress_1 + \' \' + adress_2 + \' \'  + cp + \' \' + ville + \' \' +pays ;
	  address = address.replace(/\\("|\'|\\)/g, " ");
	  if (geocoder) {
		  geocoder.geocode( { \'address\': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
			  if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
				if(infowindow) {
				  infowindow.close();
				}
				if(marker) {
					marker.setMap(null);
				}
				map.setCenter(results[0].geometry.location);
				var lat = document.getElementById("latitude");lat.value = map.getCenter().lat();
				var lon = document.getElementById("longitude");lon.value = map.getCenter().lng();

				infowindow = new google.maps.InfoWindow({
					content: "<h4>Votre emplacement<\/h4>'.TEXTE_POINT_DEPLACABLE.'",
					maxWidth: 250
				});
				//image du marqueur
				var image = new google.maps.MarkerImage(\''.BAZ_IMAGE_MARQUEUR.'\',
				//taille, point d\'origine, point d\'arrivee de l\'image
				new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_MARQUEUR.'),
				new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_MARQUEUR.'),
				new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_MARQUEUR.'));

				//ombre du marqueur
				var shadow = new google.maps.MarkerImage(\''.BAZ_IMAGE_OMBRE_MARQUEUR.'\',
				// taille, point d\'origine, point d\'arrivee de l\'image de l\'ombre
				new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_OMBRE_MARQUEUR.'),
				new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_OMBRE_MARQUEUR.'),
				new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_OMBRE_MARQUEUR.'));

				marker = new google.maps.Marker({
					position: results[0].geometry.location,
					map: map,
					icon: image,
					shadow: shadow,
					title: \'Votre emplacement\',
					draggable: true
				});
				infowindow.open(map,marker);
				google.maps.event.addListener(marker, \'click\', function() {
				  infowindow.open(map,marker);
				});
				google.maps.event.addListener(marker, "dragend", function () {
					var lat = document.getElementById("latitude");lat.value = marker.getPosition().lat();
					var lon = document.getElementById("longitude");lon.value = marker.getPosition().lng();
					map.setCenter(marker.getPosition());
				});
			  } else {
				alert("Pas de résultats pour cette adresse: " + address);
			  }
			} else {
			  alert("Pas de résultats pour la raison suivante: " + status + ", rechargez la page.");
			}
		  });
		}
	  };';
	  if ( defined('BAZ_JS_INIT_MAP') && BAZ_JS_INIT_MAP != '' && file_exists(BAZ_JS_INIT_MAP) ) {
		$handle = fopen(BAZ_JS_INIT_MAP, "r");
		$scriptgoogle .= fread($handle, filesize(BAZ_JS_INIT_MAP));
		fclose($handle);
		$scriptgoogle .= 'var poly = createPolygon( Coords, "#002F0F");
		poly.setMap(map);
		
		';
	};		
	  $script = '<script type="text/javascript">
				//<![CDATA[
				'.$scriptgoogle.'
				//]]>
				</script>';
		$formtemplate->addElement('html', $html_bouton);
		$formtemplate->addElement('html', '<div class="coordonnees_google">');
		$formtemplate->addElement('text', $lat, LATITUDE, array('id' => 'latitude','size' => 6, 'readonly' => 'readonly'));
		$formtemplate->addElement('text', $lon, LONGITUDE, array('id' => 'longitude', 'size' => 6, 'readonly' => 'readonly'));
		$formtemplate->addElement('html', '</div>');
		$formtemplate->addElement('html', $script.'<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.'; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.';"></div>');


		if (isset($obligatoire) && $obligatoire==1)
		{
			$formtemplate->addRule ($lat, LATITUDE . ' obligatoire', 'required', '', 'client');
			$formtemplate->addRule ($lon, LONGITUDE . ' obligatoire', 'required', '', 'client');
		}
    }
	elseif ( $mode == 'requete' )
	{
		return formulaire_insertion_texte('carte_google', $valeurs_fiche[$lat].'|'.$valeurs_fiche[$lon]);
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'html')
	{

	}

}

/** listefiche() - Ajoute un élément de type liste déroulante correspondant à un autre type de fiche au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément liste
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function listefiche(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ($mode=='saisie')
	{
		$bulledaide = '';
		if (isset($tableau_template[10]) && $tableau_template[10]!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		//TODO: gestion multilinguisme
		$requete =  'SELECT bf_id_fiche, bf_titre FROM '.BAZ_PREFIXE.'fiche WHERE bf_ce_nature='.$tableau_template[1].' ORDER BY bf_titre';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat))
		{
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$select[0]=BAZ_CHOISIR;
		while ($ligne = $resultat->fetchRow())
		{
			$select[$ligne[0]] = $ligne[1] ;
		}

		$option = array('id' => $tableau_template[0].$tableau_template[1].$tableau_template[6]);
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$def =	$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]];
		}
		else
		{
			$def = $tableau_template[5];
		}
		require_once 'HTML/QuickForm/select.php';
		$select= new HTML_QuickForm_select($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, $select, $option);
		if ($tableau_template[4] != '') $select->setSize($tableau_template[4]);
		$select->setMultiple(0);
		$select->setValue($def);
		$formtemplate->addElement($select) ;

		if (isset($tableau_template[8]) && $tableau_template[8]==1 && $resultat->numRows()>0)
		{
			$formtemplate->addRule($tableau_template[0].$tableau_template[1].$tableau_template[6], BAZ_CHOISIR_OBLIGATOIRE.' '.$tableau_template[2] , 'nonzero', '', 'client') ;
			$formtemplate->addRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		}
	}
	elseif ($mode == 'requete')
	{
		//on supprime les anciennes valeurs de la table '.BAZ_PREFIXE.'fiche_valeur_texte
		$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		//echo 'suppression : '.$requetesuppression.'<br />';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (DB::isError($resultat))
		{
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO '.BAZ_PREFIXE.'fiche_valeur_texte (bfvt_ce_fiche, bfvt_id_element_form, bfvt_texte) VALUES ';
			$requeteinsertion .= '("'.$GLOBALS['_BAZAR_']['id_fiche'].'", "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", "'.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].'")';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat))
			{
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT bf_id_fiche, bf_titre FROM '.BAZ_PREFIXE.'fiche WHERE bf_ce_nature='.$tableau_template[1].' ORDER BY bf_titre';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat))
			{
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			$select[0] = INDIFFERENT;
			while ($ligne = $resultat->fetchRow())
			{
				$select[$ligne[0]] = $ligne[1] ;
			}

			$option = array('id' => $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			require_once 'HTML/QuickForm/select.php';
			$select= new HTML_QuickForm_select($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2], $select, $option);
			if ($tableau_template[4] != '') $select->setSize($tableau_template[4]);
			$select->setMultiple(0);
			$formtemplate->addElement($select) ;
		}
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete = 'SELECT bf_titre FROM '.BAZ_PREFIXE.'fiche WHERE bf_id_fiche="'.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].'"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			$resultat->fetchInto($res);
			if (is_array($res))
			{
				$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
						'<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description">';
				$url_voirfiche = clone($GLOBALS['_BAZAR_']['url']);
				$url_voirfiche->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
				$url_voirfiche->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
				$url_voirfiche->addQueryString('wiki', $_GET['wiki'].'/iframe');
				$url_voirfiche->addQueryString('id_fiche', $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]);
				$html .= '<a href="'.str_replace('&', '&amp;', $url_voirfiche->getUrl()).'" class="voir_fiche ouvrir_overlay" title="Voir la fiche '.$res[0].'" rel="#overlay">'.$res[0].'</a></span>'."\n".'</div>'."\n";
			}
		}
		return $html;
	}
} //fin listefiche()


/** checkboxfiche() - permet d'aller saisir et modifier un autre type de fiche
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour le texte HTML
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @param    mixed	Tableau des valeurs par défauts (pour modification)
*
* @return   void
*/
function checkboxfiche(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		if (isset($GLOBALS['_BAZAR_']['id_fiche']) && $GLOBALS['_BAZAR_']['id_fiche']!='') 
		{
			$html  = '';
			$bulledaide = '';
			if (isset($tableau_template[10]) && $tableau_template[10]!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($tableau_template[10]).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
			//TODO: gestion multilinguisme
			$requete  = 'SELECT bf_id_fiche, bf_titre FROM '.BAZ_PREFIXE.'fiche WHERE bf_ce_nature='.$tableau_template[1];
			
			//on affiche que les fiches saisie par un utilisateur donné
			if (isset($tableau_template[7]) && $tableau_template[7]==1) $requete .= ' AND bf_ce_utilisateur="'.$GLOBALS['_BAZAR_']['nomwiki']['name'].'"';
			
			//on classe par ordre alphabetique
			$requete .= ' ORDER BY bf_titre';
			
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat))
			{
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			require_once 'HTML/QuickForm/checkbox.php' ;
			$i=0;
			$optioncheckbox = array('class' => 'element_checkbox');

			//valeurs par défauts
			if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) $tab = split( ', ', $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]] );
			else $tab = split( ', ', $tableau_template[5] );

			while ($ligne = $resultat->fetchRow()) {
				if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
				$url_checkboxfiche = clone($GLOBALS['_BAZAR_']['url']);
				$url_checkboxfiche->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
				$url_checkboxfiche->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
				$url_checkboxfiche->addQueryString('id_fiche', $ligne[0] );
				$url_checkboxfiche->addQueryString('wiki', $_GET['wiki'].'/iframe');
				$checkbox[$i]= & HTML_QuickForm::createElement('checkbox', $ligne[0], $tab_chkbox, '<a class="voir_fiche ouvrir_overlay" rel="#overlay" href="'.str_replace('&','&amp;',$url_checkboxfiche->getURL()).'">'.$ligne[1].'</a>', $optioncheckbox) ;
				$url_checkboxfiche->removeQueryString(BAZ_VARIABLE_VOIR);
				$url_checkboxfiche->removeQueryString(BAZ_VARIABLE_ACTION);
				$url_checkboxfiche->removeQueryString('id_fiche');
				$url_checkboxfiche->removeQueryString('wiki');
				if (in_array($ligne[0],$tab)) {
						$defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[0].']']=true;
				} else $defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[0].']']=false;
				$i++;
			}

			if (is_array($checkbox))
			{
				$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[4], "\n");
				if (isset($tableau_template[8]) && $tableau_template[8]==1) {
					$formtemplate->addGroupRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[4].' obligatoire', 'required', null, 1, 'client');
				}
				$formtemplate->setDefaults($defaultValues);
			}
			//ajout lien nouvelle saisie
			$url_checkboxfiche = clone($GLOBALS['_BAZAR_']['url']);
			$url_checkboxfiche->removeQueryString('id_fiche');
			$url_checkboxfiche->addQueryString('vue', BAZ_VOIR_SAISIR);
			$url_checkboxfiche->addQueryString('action', BAZ_ACTION_NOUVEAU);
			$url_checkboxfiche->addQueryString('wiki', $_GET['wiki'].'/iframe');
			$url_checkboxfiche->addQueryString('id_typeannonce', $tableau_template[1]);
			$url_checkboxfiche->addQueryString('ce_fiche_liee', $_GET['id_fiche']);	
			$html .= '<a class="ajout_fiche ouvrir_overlay" href="'.str_replace('&', '&amp;', $url_checkboxfiche->getUrl()).'" rel="#overlay" title="'.htmlentities($tableau_template[2]).'">'.$tableau_template[2].'</a>'."\n";
			$formtemplate->addElement('html', $html);
		} else {
			$formtemplate->addElement('html', '<div class="BAZ_info">'.$tableau_template[3].'</div>');
		}
	}
	elseif ( $mode == 'requete' )
	{
		//on supprime les anciennes valeurs de la table '.BAZ_PREFIXE.'fiche_valeur_texte
		$requetesuppression='DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bfvt_id_element_form="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (DB::isError($resultat))
		{
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO '.BAZ_PREFIXE.'fiche_valeur_texte (bfvt_ce_fiche, bfvt_id_element_form, bfvt_texte) VALUES ';
			//pour les checkbox, les différentes valeurs sont dans un tableau
			if (is_array($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) {
				$nb=0;
				while (list($cle, $val) = each($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) {
					if ($nb>0) $requeteinsertion .= ', ';
					$requeteinsertion .= '("'.$GLOBALS['_BAZAR_']['id_fiche'].'", "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", "'.$cle.'") ';
					$nb++;
				}
			}
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
			$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			require_once 'HTML/QuickForm/checkbox.php' ;
			$i=0;
			$optioncheckbox = array('class' => 'element_checkbox');

			while ($ligne = $resultat->fetchRow()) {
				if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
				$checkbox[$i]= & HTML_QuickForm::createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
				$i++;
			}

			$squelette_checkbox =& $formtemplate->defaultRenderer();
			$squelette_checkbox->setElementTemplate( '<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
													'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
													'</legend>'."\n".'{element}'."\n".'</fieldset> '."\n"."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, "\n");
		}
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete  = 'SELECT bf_id_fiche, bf_titre FROM '.BAZ_PREFIXE.'fiche WHERE bf_id_fiche IN ('.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].') AND bf_ce_nature='.$tableau_template[1];
			
			//on classe par ordre alphabetique
			$requete .= ' ORDER BY bf_titre';
			
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat))
			{
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			$i=0;
			
			while ($ligne = $resultat->fetchRow()) {
				$url_checkboxfiche = clone($GLOBALS['_BAZAR_']['url']);
				$url_checkboxfiche->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
				$url_checkboxfiche->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
				$url_checkboxfiche->addQueryString('id_fiche', $ligne[0] );
				$url_checkboxfiche->addQueryString('wiki', $_GET['wiki'].'/iframe');
				$checkbox[$i]= '<a class="voir_fiche ouvrir_overlay" rel="#overlay" href="'.str_replace('&','&amp;',$url_checkboxfiche->getURL()).'">'.$ligne[1].'</a>';
				$url_checkboxfiche->removeQueryString(BAZ_VARIABLE_VOIR);
				$url_checkboxfiche->removeQueryString(BAZ_VARIABLE_ACTION);
				$url_checkboxfiche->removeQueryString('id_fiche');
				$url_checkboxfiche->removeQueryString('wiki');
				$i++;
			}

			if (is_array($checkbox))
			{
				$html .= '<ul>'."\n";
				foreach($checkbox as $lien_fiche)
				{
					$html .= '<li>'.$lien_fiche.'</li>'."\n";
				}
				$html .= '</ul>'."\n";
			}
		}

		return $html;
	}
}

/** listefiches() - permet d'aller saisir et modifier un autre type de fiche
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour le texte HTML
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @param    mixed	Tableau des valeurs par défauts (pour modification)
*
* @return   void
*/
function listefiches(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if (!isset($tableau_template[1])) 
	{
		return $GLOBALS['wiki']->Format('//Erreur sur listefiches : pas d\'identifiant de type de fiche passé...//');
	}
	if (isset($tableau_template[2]) && $tableau_template[2] != '' ) 
	{
		$query = $tableau_template[2].'|listefiche'.$valeurs_fiche['bf_ce_nature'].'='.$valeurs_fiche['bf_id_fiche'];
	}
	else 
	{
		$query = 'listefiche'.$valeurs_fiche['bf_ce_nature'].'='.$valeurs_fiche['bf_id_fiche'];
	}
	if (isset($tableau_template[3])) 
	{
		$ordre = $tableau_template[3];
	}
	else 
	{
		$ordre = 'alphabetique';
	}
	
	if (isset($valeurs_fiche['bf_id_fiche']) && $mode == 'saisie' )
	{
		$actionbazarliste = '{{bazarliste idtypeannonce="'.$tableau_template[1].'" query="'.$query.'" ordre="'.$ordre.'"}}';
		$html = $GLOBALS['wiki']->Format($actionbazarliste);	
		//ajout lien nouvelle saisie
		$url_checkboxfiche = clone($GLOBALS['_BAZAR_']['url']);
		$url_checkboxfiche->removeQueryString('id_fiche');
		$url_checkboxfiche->addQueryString('vue', BAZ_VOIR_SAISIR);
		$url_checkboxfiche->addQueryString('action', BAZ_ACTION_NOUVEAU);
		$url_checkboxfiche->addQueryString('wiki', $_GET['wiki'].'/iframe');
		$url_checkboxfiche->addQueryString('id_typeannonce', $tableau_template[1]);
		$url_checkboxfiche->addQueryString('ce_fiche_liee', $_GET['id_fiche']);	
		$html .= '<a class="ajout_fiche ouvrir_overlay" href="'.str_replace('&', '&amp;', $url_checkboxfiche->getUrl()).'" rel="#overlay" title="'.htmlentities($tableau_template[4]).'">'.$tableau_template[4].'</a>'."\n";
		$formtemplate->addElement('html', $html);
	}
	elseif ( $mode == 'requete' )
	{
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT * FROM '.BAZ_PREFIXE.'liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
			$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			require_once 'HTML/QuickForm/checkbox.php' ;
			$i=0;
			$optioncheckbox = array('class' => 'element_checkbox');

			while ($ligne = $resultat->fetchRow()) {
				if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
				$checkbox[$i]= & HTML_QuickForm::createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
				$i++;
			}

			$squelette_checkbox =& $formtemplate->defaultRenderer();
			$squelette_checkbox->setElementTemplate( '<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
													'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
													'</legend>'."\n".'{element}'."\n".'</fieldset> '."\n"."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, "\n");
		}
	}
	elseif ($mode == 'html')
	{
		$actionbazarliste = '{{bazarliste idtypeannonce="'.$tableau_template[1].'" query="'.$query.'" ordre="'.$ordre.'"}}';
		$html = $GLOBALS['wiki']->Format($actionbazarliste);
		return $html;
	}
}

function bookmarklet(&$formtemplate, $tableau_template, $mode, $valeurs_fiche) {
	$url_bookmarklet = clone($GLOBALS['_BAZAR_']['url']);
	$url_bookmarklet->removeQueryString('id_fiche');
	$url_bookmarklet->addQueryString('vue', BAZ_VOIR_SAISIR);
	$url_bookmarklet->addQueryString('action', BAZ_ACTION_NOUVEAU);
	$url_bookmarklet->addQueryString('wiki', $GLOBALS['_BAZAR_']['pagewiki'].'/bazarframe');
	$url_bookmarklet->addQueryString('id_typeannonce', $GLOBALS['_BAZAR_']['id_typeannonce']);
	$htmlbookmarklet = "<div class=\"BAZ_info\">
	<a href=\"javascript:var wleft = (screen.width-700)/2; var wtop=(screen.height-530)/2 ;window.open('".str_replace('&', '&amp;', $url_bookmarklet->getUrl())."&amp;bf_titre='+escape(document.title)+'&amp;url='+encodeURIComponent(location.href)+'&amp;description='+escape(document.getSelection()), 'Veille collective', 'height=530,width=700,left='+wleft+',top='+wtop+',toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,menubar=no');void 0;\">Veille Collective</a> << déplacer ce lien dans votre barre des favoris pour y accéder facilement.</div>";
	if ($mode == 'html') {
		//return $htmlbookmarklet;
	}
	elseif ($mode == 'saisie') {
		$formtemplate->addElement('html', $htmlbookmarklet);
	}
}


/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: formulaire.fonct.inc.php,v $
* Revision 1.17  2010/09/27 17:54:58  mrflos
* amÃ©lioration de la gestion des images
*
* Revision 1.16  2010-08-16 13:49:24  mrflos
* ajout de constante de langues en francais, correction bug mineurs
*
* Revision 1.15  2010-07-22 14:21:10  mrflos
* corrections de bugs et dÃ©but d'intÃ©gration du gestionnaire des formulaires
*
* Revision 1.14  2010/06/02 08:48:51  mrflos
* commit de transition
*
* Revision 1.13  2010/05/11 17:39:49  mrflos
* commit de passage : nuage tags
*
* Revision 1.12  2010/05/03 15:59:45  mrflos
* Un bazar par prÃ©fixe de table
*
* Revision 1.11  2010/05/03 08:36:15  mrflos
* maj gÃ©nÃ©rale des fonctions de bazar
*
* Revision 1.10  2010/03/04 14:19:02  mrflos
* nouvelle version bazar
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
