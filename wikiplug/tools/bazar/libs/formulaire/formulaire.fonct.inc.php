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
// CVS : $Id: formulaire.fonct.inc.php,v 1.10 2010/03/04 14:19:02 mrflos Exp $
/**
* Formulaire
*
* Les fonctions de mise en page des formulaire
*
*@package bazar
//Auteur original :
*@author        Florian SCHMITT <florian@ecole-et-nature.org>
//Autres auteurs :
*@author        Aleandre GRANIER <alexandre@tela-botanica.org>
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.10 $ $Date: 2010/03/04 14:19:02 $
// +------------------------------------------------------------------------------------------------------+
*/

//TODO : gestion multilinguisme
include_once 'langues/formulaire.langue.fr.inc.php';

//comptatibilité PHP4...
if (version_compare(phpversion(), '5.0') < 0)
{
    eval('
    function clone($object) {
      return $object;
    }
    ');
}

function redimensionner_image($image_src, $image_dest, $largeur, $hauteur)
{
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
function formulaire_valeurs_template_champs($template)
{
	//Parcours du template, pour mettre les champs du formulaire avec leurs valeurs specifiques
	$tableau_template= array();
	$nblignes=0;
	//on traite le template ligne par ligne
	$chaine = explode ("\n", $template);
	foreach ($chaine as $ligne)
	{
		if ($ligne!='')
		{
			//on découpe chaque ligne par le séparateur *** (c'est historique)
			$tableau_template[$nblignes] = array_map("trim", explode ("***", $ligne));
			$nblignes++;
		}
	}
	return $tableau_template;
}

function formulaire_insertion_texte($champs, $valeur)
{
	//on supprime les anciennes valeurs
	$requetesuppression='DELETE FROM bazar_fiche_valeur_texte WHERE bfvt_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvt_id_element_form="'.$champs.'"';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
	//on insere les nouvelles valeurs
	if ($valeur!='')
	{
		$requeteinsertion = 'INSERT INTO bazar_fiche_valeur_texte (bfvt_ce_fiche, bfvt_id_element_form, bfvt_texte) VALUES ';
		$requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$champs.'", "'.mysql_escape_string(addslashes($valeur)).'")';
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
		$requete =  'SELECT * FROM bazar_liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
					' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat))
		{
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$select[0]=CHOISIR;
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
		//on supprime les anciennes valeurs de la table bazar_fiche_valeur_liste
		$requetesuppression='DELETE FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		//echo 'suppression : '.$requetesuppression.'<br />';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (DB::isError($resultat))
		{
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO bazar_fiche_valeur_liste (bfvl_ce_fiche, bfvl_ce_liste, bfvl_valeur) VALUES ';
			$requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", '.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].')';
			//echo 'insertion : '.$requeteinsertion.'<br />';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat))
			{
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT * FROM bazar_liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat))
			{
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}

			while ($ligne = $resultat->fetchRow())
			{
				$select[$ligne[1]] = $ligne[2] ;
			}
			$select[0]=INDIFFERENT;
			$option = array('id' => $tableau_template[0].$tableau_template[1].$tableau_template[6]);
			require_once 'HTML/QuickForm/select.php';
			$select= new HTML_QuickForm_select($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2], $select, $option);
			if ($tableau_template[4] != '') $select->setSize($tableau_template[4]);
			$select->setMultiple(0);
			$select->setValue(0);
			$formtemplate->addElement($select) ;
		}
	}
	elseif ($mode == 'requete_recherche')
	{
		if ($tableau_template[9]==1 && isset($_POST[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $_POST[$tableau_template[0].$tableau_template[1].$tableau_template[6]] != 0)
		{
			return ' AND bf_id_fiche IN (SELECT bfvl_ce_fiche FROM bazar_fiche_valeur_liste WHERE bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'" AND bfvl_valeur='.$_POST[$tableau_template[0].$tableau_template[1].$tableau_template[6]].') ';
		}
	}
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvl_valeur FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		$resultat->fetchInto($res);
		if (is_array($res)) return array($tableau_template[0].$tableau_template[1].$tableau_template[6] => implode(', ', $res));
		else return;
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete = 'SELECT blv_label FROM bazar_liste_valeurs WHERE blv_valeur IN ('.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].') AND blv_ce_liste="'.$tableau_template[1].'" AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			$resultat->fetchInto($res);
			if (is_array($res))
			{
				$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
						'<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description">';
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
		$requete =  'SELECT * FROM bazar_liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
				' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
		$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		require_once 'HTML/QuickForm/checkbox.php' ;
		$i=0;
		$optioncheckbox = array('class' => 'element_checkbox');

		//valeurs par défauts
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) $tab = split( ', ', $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]] );
		else $tab = split( ', ', $tableau_template[5] );

		while ($ligne = $resultat->fetchRow()) {
			if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
			$checkbox[$i]= & HTML_Quickform::createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
			if (in_array($ligne[1],$tab)) {
					$defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[1].']']=true;
			} else $defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[1].']']=false;
			$i++;
		}

		$squelette_checkbox =& $formtemplate->defaultRenderer();
		$squelette_checkbox->setElementTemplate( '<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
	                                             '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
												 '</legend>'."\n".'{element}'."\n".'</fieldset> '."\n"."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
	  	$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
		$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].$bulledaide, "\n");
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addGroupRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].' obligatoire', 'required', null, 1, 'client');
		}
		$formtemplate->setDefaults($defaultValues);
	}
	elseif ( $mode == 'requete' )
	{
		//on supprime les anciennes valeurs de la table bazar_fiche_valeur_liste
		$requetesuppression='DELETE FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (DB::isError($resultat))
		{
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO bazar_fiche_valeur_liste (bfvl_ce_fiche, bfvl_ce_liste, bfvl_valeur) VALUES ';
			//pour les checkbox, les différentes valeurs sont dans un tableau
			if (is_array($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) {
				$nb=0;
				while (list($cle, $val) = each($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]])) {
					if ($nb>0) $requeteinsertion .= ', ';
					$requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", '.$cle.') ';
					$nb++;
				}
			}
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT * FROM bazar_liste_valeurs WHERE blv_ce_liste='.$tableau_template[1].
						' AND blv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY blv_label';
			$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			require_once 'HTML/QuickForm/checkbox.php' ;
			$i=0;
			$optioncheckbox = array('class' => 'element_checkbox');

			while ($ligne = $resultat->fetchRow()) {
				if ($i==0) $tab_chkbox=$tableau_template[2] ; else $tab_chkbox='&nbsp;';
				$checkbox[$i]= & HTML_Quickform::createElement($tableau_template[0], $ligne[1], $tab_chkbox, $ligne[2], $optioncheckbox) ;
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
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvl_valeur FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		$res = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		$tabres = array();
		while ($row =& $res->fetchRow()) { $tabres[]=$row[0]; }
		if (count($tabres)>0) return array($tableau_template[0].$tableau_template[1].$tableau_template[6] => implode(', ', $tabres));
		else return;
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete = 'SELECT blv_label FROM bazar_liste_valeurs WHERE blv_valeur IN ('.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].') AND blv_ce_liste='.$tableau_template[1].' AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			$tabres = array();
			while ($row =& $resultat->fetchRow()) { $tabres[]=$row[0]; }
			if (count($tabres)>0)
			{
				$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
						'<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description">';
				$html .= implode(', ', $tabres).'</span>'."\n".'</div>'."\n";
			}
		}
		return $html;
	}
}

/** newsletter() - Ajoute un élément de type case à cocher pour s'inscrire à une newsletter au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément case à cocher
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function newsletter(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		$checkbox = & HTML_Quickform::createElement('checkbox', $tableau_template[1], '', $tableau_template[2]);
		if ($tableau_template[5] == 1) $checkbox->setChecked(true);
		$formtemplate->addElement($checkbox);
	}
	elseif ( $mode == 'requete' )
	{
		return;
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'valeur')
	{

	}
	elseif ($mode == 'html')
	{

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
	elseif ($mode == 'valeur')
	{
		return array($tableau_template[1] => stripslashes($valeurs_fiche[$tableau_template[1]]));
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
						if ($val == 'bf_date_debut_evenement') continue;
						$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau_template[1].'_rubrique">'.BAZ_LE.':</span>'."\n";
						$res .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].'" id="'.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche['bf_date_debut_evenement'])).'</span>'."\n".'</div>'."\n";
						continue;
					} else {

						if ($val == 'bf_date_debut_evenement') {
							$res .= '<div class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".'<span class="BAZ_label" id="'.$tableau_template[1].'_rubrique">';
							$res .= BAZ_DU;
							$res .= '</span>'."\n".'<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche[$val])).'</span>'."\n";
						} else {
							$res .= '<span class="BAZ_label" id="'.$tableau_template[1].'_rubrique">'.BAZ_AU;
							$res .= '</span>'."\n".'<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[1].'_description"> '.strftime('%d.%m.%Y',strtotime($valeurs_fiche[$val])).'</span>'."\n".'</div>'."\n";
						}

						continue;
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
		//gestion des valeurs par défaut
		if (isset($valeurs_fiche[$tableau_template[1]])) $defauts = array( $tableau_template[1] => $valeurs_fiche[$tableau_template[1]] );
		else $defauts = array( $tableau_template[1] => stripslashes($tableau_template[5]) );
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
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvt_texte FROM bazar_fiche_valeur_texte WHERE bfvt_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvt_id_element_form="'.$tableau_template[1].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow();
		return array($tableau_template[1] => stripslashes($ligne[0]));
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='')
		{
			if ($tableau_template[1] == 'bf_titre')
			{
				// Le titre
				$html .= '<h1 class="BAZ_fiche_titre BAZ_fiche_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.htmlentities($valeurs_fiche[$tableau_template[1]]).'</h1>'."\n";
			}
			else
			{
				$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
						'<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
				$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description"> ';
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
				$formhtml= new HTML_QuickForm_html('<tr>'."\n".'<td>&nbsp;</td>'."\n".'<td style="text-align:left;"><a href="'.$GLOBALS['_BAZAR_']['wiki']->href('','ChangePassword','').'" target="_blank">Changer son mot de passe</a></td>'."\n".'</tr>'."\n");
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
		$resultat = $GLOBALS['_BAZAR_']['db']->query('SELECT name FROM '.$GLOBALS['_BAZAR_']['wiki']->config["table_prefix"].'users WHERE name="'.$valeurs_fiche['nomwiki'].'"');
		if ($resultat->numRows()==0)
		{
			$nomwiki = baz_nextWiki(genere_nom_wiki($valeurs_fiche['bf_titre']));
			$requeteinsertionuserwikini = 'INSERT INTO '.$GLOBALS['_BAZAR_']['wiki']->config["table_prefix"]."users SET ".
					"signuptime = now(), ".
					"name = '".mysql_escape_string($nomwiki)."', ".
					"email = '".mysql_escape_string($valeurs_fiche['bf_mail'])."', ".
					"password = md5('".mysql_escape_string($valeurs_fiche['mot_de_passe_wikini'])."')";
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertionuserwikini) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			return 'bf_nom_wikini="'.mysql_escape_string($nomwiki).'", ' ;
			//envoi mail nouveau mot de passe
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['_BAZAR_']['wiki']->config["base_url"]);
			$objetmail = '['.str_replace("http://","",$lien).'] Vos nouveaux identifiants sur le site '.$GLOBALS['_BAZAR_']['wiki']->config["wakka_name"];
			$messagemail = "Bonjour!\n\nVotre inscription sur le site a été finalisée, dorénavant vous pouvez vous identifier avec les informations suivantes :\n\nVotre identifiant NomWiki : ".$nomwiki."\nVotre mot de passe : ". $valeurs_fiche['mot_de_passe_wikini'] . "\n\nA très bientôt !\n\nSylvie Vernet, webmestre";
			$headers =   'From: '.BAZ_ADRESSE_MAIL_ADMIN . "\r\n" .
			     'Reply-To: '.BAZ_ADRESSE_MAIL_ADMIN . "\r\n" .
			     'X-Mailer: PHP/' . phpversion();
			mail($valeurs_fiche['bf_mail'], remove_accents($objetmail), $messagemail, $headers);
		} elseif (isset($valeurs_fiche['mot_de_passe_wikini'])) {
			$requetemodificationuserwikini = 'UPDATE '.$GLOBALS['_BAZAR_']['wiki']->config["table_prefix"]."users SET ".
					"email = '".mysql_escape_string($valeurs_fiche['bf_mail'])."', ".
					"password = md5('".mysql_escape_string($valeurs_fiche['mot_de_passe_wikini'])."') WHERE name=\"".$valeurs_fiche['bf_nom_wikini']."\"";
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requetemodificationuserwikini) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			//envoi mail nouveau mot de passe
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['_BAZAR_']['wiki']->config["base_url"]);
			$objetmail = '['.str_replace("http://","",$lien).'] Vos nouveaux identifiants sur le site '.$GLOBALS['_BAZAR_']['wiki']->config["wakka_name"];
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
	elseif ($mode == 'valeur')
	{
		return array('bf_nom_wikini' => $valeurs_fiche['bf_nom_wikini']);
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
	elseif ($mode == 'valeur')
	{
		return array($tableau_template[1] => stripslashes($tableau_template[5]));
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
		//gestion des valeurs par defaut
		$defs=array($tableau_template[1]=>$tableau_template[5]);
		$formtemplate->setDefaults($defs);
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
	elseif ($mode == 'valeur')
	{
		return array($tableau_template[1] => stripslashes($valeurs_fiche[$tableau_template[1]]));
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='')
		{
			$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
					'<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
			$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description"><a href="mailto:'.$valeurs_fiche[$tableau_template[1]].'" class="BAZ_lien_mail">';
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
	elseif ($mode == 'valeur')
	{
		return array($tableau_template[1] => stripslashes($valeurs_fiche[$tableau_template[1]]));
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
	list($type, $identifiant, $label, $nb_colonnes, $nb_lignes, $valeur_par_defaut, , , $obligatoire, $apparait_recherche, $bulle_d_aide) = $tableau_template;
	if ( $mode == 'saisie' )
	{
		$bulledaide = '';
		if ($bulle_d_aide!='') $bulledaide = ' <img class="tooltip_aide" title="'.htmlentities($bulle_d_aide).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		$formtexte= new HTML_QuickForm_textarea($identifiant, $label.$bulledaide, array('style'=>'white-space: normal;overflow:visible;', 'id' => $identifiant, 'class' => 'input_textarea'));
		$formtexte->setCols($nb_colonnes);
		$formtexte->setRows($nb_lignes);
		$formtemplate->addElement($formtexte) ;
		//gestion des valeurs par défaut
		if (isset($valeurs_fiche[$identifiant])) $defauts = array( $identifiant => $valeurs_fiche[$identifiant] );
		else $defauts = array( $identifiant => stripslashes($valeur_par_defaut) );
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
		$requetesuppression='DELETE FROM bazar_fiche_valeur_texte_long WHERE bfvtl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvtl_id_element_form="'.$identifiant.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		//on insere les nouvelles valeurs
		$requeteinsertion = 'INSERT INTO bazar_fiche_valeur_texte_long (bfvtl_ce_fiche, bfvtl_id_element_form, bfvtl_texte_long) VALUES ';
        $requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$identifiant.'", "'.addslashes($valeurs_fiche[$identifiant]).'")';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
		return;
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvtl_texte_long FROM bazar_fiche_valeur_texte_long WHERE bfvtl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvtl_id_element_form="'.$identifiant.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow();
		return array($identifiant => stripslashes($ligne[0]));
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$identifiant]) && $valeurs_fiche[$identifiant]!='')
		{
			$html = '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
					'<span class="BAZ_label '.$label.'_rubrique">'.$label.':</span>'."\n";
			$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$identifiant.'_description"> ';
			$html .= nl2br($valeurs_fiche[$identifiant]).'</span>'."\n".'</div>'."\n";
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
function url(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	if ( $mode == 'saisie' )
	{
		//recherche des URLs deja entrees dans la base
		$html_url= '';
		if (isset($GLOBALS['_BAZAR_']["id_fiche"]) && $GLOBALS['_BAZAR_']["id_fiche"]!=NULL) {
			$requete = 'SELECT bu_id_url, bu_url, bu_descriptif_url FROM bazar_url WHERE bu_ce_fiche='.$GLOBALS['_BAZAR_']["id_fiche"];
			$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				die ($GLOBALS['_BAZAR_']['db']->getMessage().$GLOBALS['_BAZAR_']['db']->getDebugInfo()) ;
			}
			if ($resultat->numRows()>0) {
				$html_url= '<strong>'.BAZ_LISTE_URL.'</strong>'."\n";
				$tableAttr = array("class" => "bazar_table") ;
				$table = new HTML_Table($tableAttr) ;
				$entete = array (BAZ_LIEN , BAZ_SUPPRIMER) ;
				$table->addRow($entete) ;
				$table->setRowType(0, "th") ;

				$lien_supprimer=$GLOBALS['_BAZAR_']['url'];
				$lien_supprimer->addQueryString('action', $_GET['action']);
				$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']["id_fiche"]);
				$lien_supprimer->addQueryString('typeannonce', $_REQUEST['typeannonce']);

				while ($ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT)) {
					$lien_supprimer->addQueryString('id_url', $ligne->bu_id_url);
					$table->addRow (array(
					'<a href="'.$ligne->bu_url.'" target="_blank"> '.$ligne->bu_descriptif_url.'</a>', // col 1 : le lien
					'<a href="'.$lien_supprimer->getURL().'" onclick="javascript:return confirm(\''.BAZ_CONFIRMATION_SUPPRESSION_LIEN.'\');" >'.BAZ_SUPPRIMER.'</a>'."\n")) ; // col 2 : supprimer
					$lien_supprimer->removeQueryString('id_url');
				}

				// Nettoyage de l'url
				$lien_supprimer->removeQueryString('action');
				$lien_supprimer->removeQueryString('id_fiche');
				$lien_supprimer->removeQueryString('typeannonce');

				$table->altRowAttributes(1, array("class" => "ligne_impaire"), array("class" => "ligne_paire"));
				$table->updateColAttributes(1, array("align" => "center"));
				$html_url.= $table->toHTML()."\n\n" ;
			}
		}
		$html ="\n".'<h4>'.$tableau_template[2].'</h4>'."\n";
		$formtemplate->addElement('html', $html) ;
		if ($html_url!='') $formtemplate->addElement('html', $html_url) ;
		$formtemplate->addElement('text', 'url_lien'.$tableau_template[1], BAZ_URL_LIEN) ;
		$defs=array('url_lien'.$tableau_template[1]=>'http://');
		$formtemplate->setDefaults($defs);

		$formtemplate->addElement('text', 'url_texte'.$tableau_template[1], BAZ_URL_TEXTE) ;
		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addRule('url_lien'.$tableau_template[1], BAZ_URL_LIEN_REQUIS, 'required', '', 'client') ;
			$formtemplate->addRule('url_texte'.$tableau_template[1], BAZ_URL_TEXTE_REQUIS, 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		// On affine les criteres pour l insertion d une url
		// il faut que le lien soit saisie, different de http:// ET que le texte du lien soit saisie aussi
		// et ce afin d eviter d avoir des liens vides
		if (isset($valeurs_fiche['url_lien'.$tableau_template[1]]) &&
						$valeurs_fiche['url_lien'.$tableau_template[1]]!='http://'
						&& isset($valeurs_fiche['url_texte'.$tableau_template[1]]) &&
						strlen ($valeurs_fiche['url_texte'.$tableau_template[1]]))
		{
				formulaire_insertion_texte('url_lien'.$tableau_template[1], $valeurs_fiche['url_lien'.$tableau_template[1]].'***'.$valeurs_fiche['url_texte'.$tableau_template[1]]) ;
		}
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'valeur')
	{

	}
	elseif ($mode == 'html')
	{
		//afficher les liens pour l'annonce
		$requete = 'SELECT  bu_url, bu_descriptif_url FROM bazar_url WHERE bu_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows()>0) {
			$res .= '<span class="BAZ_label BAZ_label_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LIEN_INTERNET.':</span>'."\n";
			$res .= '<span class="BAZ_description BAZ_description_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			$res .= '<ul class="BAZ_liste BAZ_liste_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			while ($ligne1 = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
				$res .= '<li class="BAZ_liste_lien BAZ_liste_lien_'.$GLOBALS['_BAZAR_']['class'].'"><a href="'.$ligne1['bu_url'].'" class="BAZ_lien" target="_blank">'.$ligne1['bu_descriptif_url'].'</a></li>'."\n";
			}
			$res .= '</ul></span>'."\n";
		}
	}
}


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
		$formtemplate->addElement('text', $tableau_template[1], $tableau_template[2], $option)	;
		if (isset($tableau_template[5])) $defs=array($tableau_template[1]=>$tableau_template[5]);
		else $defs=array($tableau_template[1]=>'http://');
		$formtemplate->setDefaults($defs);

		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addRule($tableau_template[1], URL_LIEN_REQUIS, 'required', '', 'client') ;
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
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvt_texte FROM bazar_fiche_valeur_texte WHERE bfvt_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvt_id_element_form="'.$tableau_template[1].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow();
		return array($tableau_template[1] => stripslashes($ligne[0]));
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[1]]) && $valeurs_fiche[$tableau_template[1]]!='')
		{
			$html .= '<div class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'."\n".
					 '<span class="BAZ_label '.$tableau_template[2].'_rubrique">'.$tableau_template[2].':</span>'."\n";
			$html .= '<span class="BAZ_texte BAZ_texte_'.$GLOBALS['_BAZAR_']['class'].' '.$tableau_template[2].'_description">'."\n".
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
	if ($mode == 'saisir')
	{
		//AJOUTER DES FICHIERS JOINTS
		$html_fichier= '';
		if (isset($GLOBALS['_BAZAR_']["id_fiche"]) && $GLOBALS['_BAZAR_']["id_fiche"]!=NULL) {
			$requete = 'SELECT * FROM bazar_fichier_joint WHERE bfj_ce_fiche='.$GLOBALS['_BAZAR_']["id_fiche"];
			$resultat = $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
			if (DB::isError ($resultat)) {
				die ($GLOBALS['_BAZAR_']['db']->getMessage().$GLOBALS['_BAZAR_']['db']->getDebugInfo()) ;
			}

			if ($resultat->numRows()>0) {
				$html_fichier = '<table><tr>'."\n".'<td colspan="2">'."\n".'<strong>'.BAZ_LISTE_FICHIERS_JOINTS.'</strong>'."\n";
				$tableAttr = array("class" => "bazar_table") ;
				$table = new HTML_Table($tableAttr) ;
				$entete = array (BAZ_FICHIER , BAZ_SUPPRIMER) ;
				$table->addRow($entete) ;
				$table->setRowType(0, "th") ;

				$lien_supprimer=$GLOBALS['_BAZAR_']['url'];
				$lien_supprimer->addQueryString('action', $_GET['action']);
				$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']["id_fiche"]);
				$lien_supprimer->addQueryString('typeannonce', $_REQUEST['typeannonce']);
				while ($ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT)) {
					$lien_supprimer->addQueryString('id_fichier', $ligne->bfj_id_fichier);
					$table->addRow(array('<a href="client/bazar/upload/'.$ligne->bfj_fichier.'"> '.$ligne->bfj_description.'</a>', // col 1 : le fichier et sa description
										 '<a href="'.$lien_supprimer->getURL().'" onclick="javascript:return confirm(\''.BAZ_CONFIRMATION_SUPPRESSION_FICHIER.'\');" >'.BAZ_SUPPRIMER.'</a>'."\n")) ; // col 2 : supprimer
					$lien_supprimer->removeQueryString('id_fichier');
				}
				$table->altRowAttributes(1, array("class" => "ligne_impaire"), array("class" => "ligne_paire"));
				$table->updateColAttributes(1, array("align" => "center"));
				$html_fichier .= $table->toHTML()."\n".'</td>'."\n".'</tr></table>'."\n" ;
			}
		}
		$html ='<div style="clear:both;">'."\n".'<h4>'.$tableau_template[2].'</h4>'."\n".'</div>'."\n";
		$formtemplate->addElement('html', $html) ;
		if ($html_fichier!='') $formtemplate->addElement('html', $html_fichier) ;
		$formtemplate->addElement('text', 'texte_fichier'.$tableau_template[1], BAZ_FICHIER_DESCRIPTION) ;
		$formtemplate->addElement('file', 'fichier'.$tableau_template[1], BAZ_FICHIER_JOINT) ;
		$formtemplate->addRule('image', BAZ_IMAGE_VALIDE_REQUIS, '', '', 'client') ; //a completer pour checker l'image
		$formtemplate->setMaxFileSize($tableau_template[3]);
		//gestion du champs obligatoire
		if (($tableau_template[9]==0) && isset($tableau_template[8]) && ($tableau_template[8]==1)) {
			$formtemplate->addRule('texte_fichier'.$tableau_template[1], BAZ_FICHIER_LABEL_REQUIS, 'required', '', 'client') ;
			$formtemplate->addRule('fichier'.$tableau_template[1], BAZ_FICHIER_JOINT_REQUIS, 'required', '', 'client') ;
		}
	}
	elseif ( $mode == 'requete' )
	{
		if (isset($valeurs_fiche['texte_fichier'.$tableau_template[1]]) && $valeurs_fiche['texte_fichier'.$tableau_template[1]]!='') {
				baz_insertion_fichier($valeurs_fiche['texte_fichier'.$tableau_template[1]], $GLOBALS['_BAZAR_']['id_fiche'], 'fichier'.$tableau_template[1]);
		}
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'valeur')
	{

	}
	elseif ($mode == 'html')
	{
		//afficher les fichiers pour l'annonce
		$requete = 'SELECT  bfj_description, bfj_fichier FROM bazar_fichier_joint WHERE bfj_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows()>0) {
			$res .= '<span class="BAZ_label BAZ_label_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LISTE_FICHIERS_JOINTS.':</span>'."\n";
			$res .= '<span class="BAZ_description BAZ_description_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			$res .= '<ul class="BAZ_liste BAZ_liste_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			while ($ligne2 = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
				$res .= '<li class="BAZ_liste_fichier BAZ_liste_fichier_'.$GLOBALS['_BAZAR_']['class'].'"><a href="tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.$ligne2['bfj_fichier'].'">'.$ligne2['bfj_description'].'</a></li>'."\n";
			}
			$res .= '</ul></span>'."\n";
		}
	}
}

/** image() - Ajoute un élément de type image au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    mixed   Le tableau des valeurs des différentes option pour l'élément image
* @param    string  Type d'action pour le formulaire : saisie, modification, vue,... saisie par défaut
* @return   void
*/
function image(&$formtemplate, $tableau_template, $mode, $valeurs_fiche)
{
	list($type, $identifiant, $label, $hauteur_vignette, $largeur_vignette, $hauteur_image, $largeur_image, $alignement, $obligatoire, $apparait_recherche, $bulle_d_aide) = $tableau_template;
	if ( $mode == 'saisie' )
	{
		//AJOUTER UNE IMAGE
		$html_image= '';
		if ($bulle_d_aide!='') $label = $label.' <img class="tooltip_aide" title="'.htmlentities($bulle_d_aide).'" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
		if (isset($valeurs_fiche[$type.$identifiant])) {
			$lien_supprimer=clone($GLOBALS['_BAZAR_']['url']);
			$lien_supprimer->addQueryString('action', $_GET['action']);
			$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']["id_fiche"]);
			//$lien_supprimer->addQueryString('typeannonce', $_REQUEST['typeannonce']);
			$lien_supprimer->addQueryString('image', 1);
			$html_image = '<img src="'.BAZ_CHEMIN.'actions/bazar/upload/'.$valeurs_fiche[$type.$identifiant].
			'" alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" width="130" height="130" />'."\n".
			'<a href="'.str_replace('&', '&amp;', $lien_supprimer->getURL()).'" onclick="javascript:return confirm(\''.
			BAZ_CONFIRMATION_SUPPRESSION_IMAGE.'\');" >'.BAZ_SUPPRIMER.'</a><br />'."\n".
			'<strong>'.BAZ_POUR_CHANGER_IMAGE.'</strong><br />'."\n";
		}
		$html = '';
		$formtemplate->addElement('html', $html) ;
		if ($html_image!='') $formtemplate->addElement('html', $html_image) ;
		$formtemplate->addElement('file', $type.$identifiant, $label) ;

		//gestion du champs obligatoire
		if (($apparait_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
			$formtemplate->addRule('image', IMAGE_VALIDE_REQUIS, 'required', '', 'client') ;
		}
		//TODO: la vérification du type de fichier ne marche pas
		$tabmime = array ('gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png');
		$formtemplate->addRule($type.$identifiant, 'Vous devez choisir une fichier de type image gif, jpg ou png', 'mimetype', $tabmime );
	}
	elseif ( $mode == 'requete' )
	{
			if (isset($_FILES[$type.$identifiant]['name']) && $_FILES[$type.$identifiant]['name']!='') {
				//on enleve les accents sur les noms de fichiers, et les espaces
				$nomimage = preg_replace("/&([a-z])[a-z]+;/i","$1", htmlentities($identifiant.$_FILES[$type.$identifiant]['name']));
				$nomimage = str_replace(' ', '_', $nomimage);
				$chemin_destination=BAZ_CHEMIN.'actions/bazar/upload/'.$nomimage;
				//verification de la presence de ce fichier
				if (!file_exists($chemin_destination)) {
					move_uploaded_file($_FILES[$type.$identifiant]['tmp_name'], $chemin_destination);
					chmod ($chemin_destination, 0755);
					if ($hauteur_vignette!='' && $largeur_vignette!='')
					{
						$adr_img = redimensionner_image($chemin_destination, 'cache/mini_'.$nomimage, $largeur_vignette, $hauteur_vignette);
						if (!file_exists($adr_img))	{ echo 'ERREUR'.$adr_img; }
					}
					if ($hauteur_image!='' && $largeur_image!='')
					{
						$adr_img = redimensionner_image($chemin_destination, 'cache/normal_'.$nomimage, $largeur_image, $hauteur_image);
						if (!file_exists($adr_img))	{ echo 'ERREUR'.$adr_img; }
					}
				}
				else echo 'Image déja existante<br />';
				formulaire_insertion_texte($type.$identifiant, $nomimage);
				return ;
			}
	}
	elseif ($mode == 'recherche')
	{

	}
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvt_texte FROM bazar_fiche_valeur_texte WHERE bfvt_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvt_id_element_form="'.$type.$identifiant.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow();
		return array($type.$identifiant => stripslashes($ligne[0]));
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$type.$identifiant]) && $valeurs_fiche[$type.$identifiant]!='')
		{
			$html = '<div class="BAZ_fiche_image BAZ_fiche_image_'.$GLOBALS['_BAZAR_']['class'].' image_align_'.$alignement.'">'."\n";

			//on prend les images vignettes et on associe les images redimensionnées si elles existent
			if ( ($hauteur_vignette!='' && $largeur_vignette!='') || ($hauteur_image!='' && $largeur_image!='') )
			{
				//la vignette pointe sur une image redimensionnée
				if ($hauteur_image!='' && $largeur_image!='')
				{
					if (!file_exists('cache/normal_'.$valeurs_fiche[$type.$identifiant]))
					{
						$adr_img = redimensionner_image('tools/bazar/actions/bazar/upload/'.$valeurs_fiche[$type.$identifiant], 'cache/normal_'.$valeurs_fiche[$type.$identifiant], $largeur_image, $hauteur_image);
						if (!file_exists($adr_img))	{ echo 'ERREUR'.$adr_img; }
					}
					$html .= '<a class="triggerimage" title="'.$label.'" href="cache/normal_'.$valeurs_fiche[$type.$identifiant].'">'."\n";
				}
				//la vignette pointe sur l'image de taille originale sinon
				else
				{
					$html .= '<a class="triggerimage" title="'.$label.'" href="tools/bazar/actions/bazar/upload/'.$valeurs_fiche[$type.$identifiant].'">'."\n";
				}
				//on teste l'existance de la vignette
				if ($hauteur_vignette!='' && $largeur_vignette!='')
				{
					if (!file_exists('cache/mini_'.$valeurs_fiche[$type.$identifiant]))
					{
						$adr_img = redimensionner_image('tools/bazar/actions/bazar/upload/'.$valeurs_fiche[$type.$identifiant], 'cache/mini_'.$valeurs_fiche[$type.$identifiant], $largeur_vignette, $hauteur_vignette);
						if (!file_exists($adr_img))	{ echo 'ERREUR'.$adr_img; }
					}
					$html .= '<img class="BAZ_image_mini" src="cache/mini_'.$valeurs_fiche[$type.$identifiant].'" border="0" alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" />'."\n";
					$html .= '</a>'."\n";
				}
				//on efface tout si pas de vignette
				else $html = '';

			}
			//on prend l'image originale sinon
			else
			{
				$html .= '<img class="BAZ_image" src="tools/bazar/actions/bazar/upload/'.$valeurs_fiche[$type.$identifiant].'" border="0" alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" />'."\n";
			}
			if ($html!='') $html .= '</div>'."\n";
		}
		return $html;
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
	elseif ($mode == 'valeur')
	{
		return;
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
		if (isset($valeurs_fiche[$lon])) {
			$defauts = array( $lat => $valeurs_fiche[$lat], $lon => $valeurs_fiche[$lon] );
			$formtemplate->setDefaults($defauts);
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
	  $script = '<script type="text/javascript">
				//<![CDATA[
				'.$scriptgoogle.'
				//]]>
				</script>';
		$formtemplate->addElement('html', $html_bouton);
		$formtemplate->addElement('html', '<div class="coordonnees_google">');
		$formtemplate->addElement('text', $lat, LATITUDE, array('id' => 'latitude', 'size' => 6, 'readonly' => 'readonly'));
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
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvt_texte FROM bazar_fiche_valeur_texte WHERE bfvt_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvt_id_element_form = "carte_google"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow();
		$tab=explode('|', $ligne[0]);
		if (count($tab)>1)
		{
			return array($lat => $tab[0], $lon => $tab[1]);
		}
		else
		{
			return;
		}
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
		$requete =  'SELECT bf_id_fiche, bf_titre FROM bazar_fiche WHERE bf_ce_nature='.$tableau_template[1].' ORDER BY bf_titre';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError ($resultat))
		{
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if ($tableau_template[9]==0)
		{
			$select[0]=CHOISIR;
		}
		else
		{
			$select[0]=INDIFFERENT;
		}
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

		if (isset($tableau_template[8]) && $tableau_template[8]==1)
		{
			$formtemplate->addRule($tableau_template[0].$tableau_template[1].$tableau_template[6], BAZ_CHOISIR_OBLIGATOIRE.' '.$tableau_template[2] , 'nonzero', '', 'client') ;
			$formtemplate->addRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[2].' obligatoire', 'required', '', 'client') ;
		}
	}
	elseif ($mode == 'requete')
	{
		//on supprime les anciennes valeurs de la table bazar_fiche_valeur_liste
		$requetesuppression='DELETE FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		//echo 'suppression : '.$requetesuppression.'<br />';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
		if (DB::isError($resultat))
		{
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && ($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!=0))
		{
			//on insere les nouvelles valeurs
			$requeteinsertion='INSERT INTO bazar_fiche_valeur_liste (bfvl_ce_fiche, bfvl_ce_liste, bfvl_valeur) VALUES ';
			$requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'", '.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]].')';
			//echo 'insertion : '.$requeteinsertion.'<br />';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
			if (DB::isError($resultat))
			{
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}
	elseif ($mode == 'formulaire_recherche')
	{
		if ($tableau_template[9]==1)
		{
			$requete =  'SELECT bf_id_fiche, bf_titre FROM bazar_fiche WHERE bf_ce_nature='.$tableau_template[1].' ORDER BY bf_titre';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat))
			{
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			$select[0]=INDIFFERENT;
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
	elseif ($mode == 'valeur')
	{
		$requete =  'SELECT bfvl_valeur FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].
					' AND  bfvl_ce_liste="'.$tableau_template[0].$tableau_template[1].$tableau_template[6].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		$resultat->fetchInto($res);
		if (is_array($res)) return array($tableau_template[0].$tableau_template[1].$tableau_template[6] => implode(', ', $res));
		else return;
	}
	elseif ($mode == 'html')
	{
		$html = '';
		if (isset($valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]) && $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]!='')
		{
			$requete = 'SELECT bf_titre FROM bazar_fiche WHERE bf_id_fiche='.$valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]];
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
				$url_voirfiche->addQueryString('wiki', $_GET['wiki'].'/bazarframe');
				$url_voirfiche->addQueryString('id_fiche', $valeurs_fiche[$tableau_template[0].$tableau_template[1].$tableau_template[6]]);
				$html .= '<a href="'.str_replace('&', '&amp;', $url_voirfiche->getUrl()).'" class="voir_fiche" title="Voir la fiche '.$res[0].'" rel="#overlay">'.$res[0].'</a></span>'."\n".'</div>'."\n";
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
			$requete  = 'SELECT bf_id_fiche, bf_titre FROM bazar_fiche WHERE bf_ce_nature='.$tableau_template[1];
			
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
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
				$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne[0] );
				$GLOBALS['_BAZAR_']['url']->addQueryString('wiki', $_GET['wiki'].'/bazarframe');
				$checkbox[$i]= & HTML_Quickform::createElement('checkbox', $ligne[0], $tab_chkbox, '<a class="voir_fiche" rel="#overlay" href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.$ligne[1].'</a>', $optioncheckbox) ;
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
				$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
				$GLOBALS['_BAZAR_']['url']->removeQueryString('wiki');
				if (in_array($ligne[0],$tab)) {
						$defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[0].']']=true;
				} else $defaultValues[$tableau_template[0].$tableau_template[1].$tableau_template[6].'['.$ligne[0].']']=false;
				$i++;
			}

			if (is_array($checkbox))
			{
				$squelette_checkbox =& $formtemplate->defaultRenderer();
				$squelette_checkbox->setElementTemplate( '<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
														 '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
														 '</legend>'."\n".'{element}'."\n".'</fieldset> '."\n"."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
				$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", $tableau_template[0].$tableau_template[1].$tableau_template[6]);
				$formtemplate->addGroup($checkbox, $tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[4], "\n");
				if (isset($tableau_template[8]) && $tableau_template[8]==1) {
					$formtemplate->addGroupRule($tableau_template[0].$tableau_template[1].$tableau_template[6], $tableau_template[4].' obligatoire', 'required', null, 1, 'client');
				}
				$formtemplate->setDefaults($defaultValues);
			}
			//ajout lien nouvelle saisie
			$url_checkboxfiche = clone($GLOBALS['_BAZAR_']['url']);
			$url_checkboxfiche->removeQueryString('vue');
			$url_checkboxfiche->removeQueryString('action');
			$url_checkboxfiche->addQueryString('wiki', $_GET['wiki'].'/bazarframe');
			$url_checkboxfiche->addQueryString('id_typeannonce', $tableau_template[1]);
			$html .= '<a class="ajout_fiche" href="'.str_replace('&', '&amp;', $url_checkboxfiche->getUrl()).'" rel="#overlay" title="'.htmlentities($tableau_template[2]).'">'.$tableau_template[2].'</a>'."\n";
			$formtemplate->addElement('html', $html);
		} else {
			$formtemplate->addElement('html', '<div class="BAZ_info">'.$tableau_template[3].'</div>');
		}
	}
	elseif ( $mode == 'requete' )
	{
		return;
	}
	elseif ($mode == 'recherche')
	{
		return;
	}
	elseif ($mode == 'valeur')
	{
		return;
	}
	elseif ($mode == 'html')
	{
		return;
	}
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: formulaire.fonct.inc.php,v $
* Revision 1.10  2010/03/04 14:19:02  mrflos
* nouvelle version bazar
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
