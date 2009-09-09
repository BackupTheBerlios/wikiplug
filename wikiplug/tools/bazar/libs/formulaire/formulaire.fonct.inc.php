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
// CVS : $Id: formulaire.fonct.inc.php,v 1.6 2009/09/09 15:36:37 mrflos Exp $
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
*@version       $Revision: 1.6 $ $Date: 2009/09/09 15:36:37 $
// +------------------------------------------------------------------------------------------------------+
*/

if (isset($GLOBALS[GEN_URL_CLE_I18N])){
	include_once 'langues/formulaire.langue.'.$GLOBALS[GEN_URL_CLE_I18N].'.inc.php';
} else {
	include_once 'langues/formulaire.langue.fr.inc.php';
}


 /**
  * Fonction principale de cette bibliotheque,
  * 
  * decoupe le template et renvoie un tableau structure
  */
 
function formulaire_valeurs_template_champs($valeur_template) {
	//Parcours du template, pour mettre les champs du formulaire avec leurs valeurs specifiques
	$tableau= array();
	$nblignes=0;
	$chaine = explode ("\n", $valeur_template);
	array_pop($chaine);
	foreach ($chaine as $ligne)  {
		$souschaine = explode ("***", $ligne) ;
		$tableau[$nblignes]['type'] = trim($souschaine[0]) ;
		if (isset($souschaine[1])) {$tableau[$nblignes]['nom_bdd'] = trim($souschaine[1]);}
		else {$tableau[$nblignes]['nom_bdd'] ='';}
		if (isset($souschaine[2])) $tableau[$nblignes]['label'] = trim($souschaine[2]);
		else {$tableau[$nblignes]['label'] ='';}
		if (isset($souschaine[3])) $tableau[$nblignes]['limite1'] = trim($souschaine[3]);
		else {$tableau[$nblignes]['limite1'] ='';}
		if (isset($souschaine[4])) $tableau[$nblignes]['limite2'] = trim($souschaine[4]);
		else {$tableau[$nblignes]['limite2'] ='';}
		if (isset($souschaine[5])) $tableau[$nblignes]['defaut'] = trim($souschaine[5]);
		else {$tableau[$nblignes]['defaut'] ='';}
		if (isset($souschaine[6])) $tableau[$nblignes]['table_source'] = trim($souschaine[6]);
		else {$tableau[$nblignes]['table_source'] ='';}
		if (isset($souschaine[7])) $tableau[$nblignes]['id_source'] = trim($souschaine[7]);
		else {$tableau[$nblignes]['id_source'] ='';}
		if (isset($souschaine[8])) $tableau[$nblignes]['obligatoire'] = trim($souschaine[8]);
		else {$tableau[$nblignes]['obligatoire'] ='';}
		if (isset($souschaine[9])) $tableau[$nblignes]['recherche'] = trim($souschaine[9]);
		else {$tableau[$nblignes]['recherche'] ='';}

		// traitement des cases à cocher, dans ce cas la, on a une table de jointure entre la table
		// de liste et la table bazar_fiche (elle porte un nom du genre bazar_ont_***)
		// dans le template, a la place d'un nom de champs dans 'nom_bdd', on a un nom de table
		// et 2 noms de champs separes par un virgule ex : bazar_ont_theme,bot_id_theme,bot_id_fiche
		if (isset($tableau[$nblignes]['nom_bdd']) && preg_match('/,/', $tableau[$nblignes]['nom_bdd'])) {
			$tableau_info_jointe = explode (',', $tableau[$nblignes]['nom_bdd']) ;
			$tableau[$nblignes]['table_jointe'] = $tableau_info_jointe[0] ;
			$tableau[$nblignes]['champs_id_fiche'] = $tableau_info_jointe[1] ;
			$tableau[$nblignes]['champs_id_table_jointe'] = $tableau_info_jointe[2] ;
		}
		$nblignes++;
	}
	return $tableau;
}

//-------------------FONCTIONS DE MISE EN PAGE DES FORMULAIRES

/** liste() - Ajoute un èlèment de type liste au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    int     identifiant de la liste sur bazar_liste
* @param    string  label è afficher dans le formulaire
* @param    string  première restriction de la taille des champs du formulaire
* @param    string  deuxième restriction de la taille des champs du formulaire
* @param    string  valeur par dèfaut du formulaire
* @param    string  table source pour les valeurs de la liste
* @param    string  ce champs est il obligatoire? (required)
* @param    boolean sommes nous dans le moteur de recherche?
* @param string le nom de l appli, ou le nom du repertoire de l appli
* @return   void
*/
function liste(&$formtemplate, $id_liste , $label, $limite1, $limite2, $defaut, $source,
					 $obligatoire, $dans_moteur_de_recherche=0,$appli = 'bazar') {
	
	// Deux cas, soit avec la table xxx_liste et xxx_liste_valeurs
	// soit avec les champs source et colonne identifiant
	
	// Le nom de l appli permet de retrouver le nom de la table liste
	// ex : bazar => bazar_liste_valeurs
	// pour le nom des champs, on utilise la premiere lettre du nom de l appli
	// bazar => b
	
	if (intval($id_liste) != 0) {
		$l = $appli[0];
		$requete = 'SELECT * FROM '.$appli.'_liste_valeurs WHERE '.$l.'lv_ce_liste='.$id_liste.
					' AND '.$l.'lv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
		$nom_liste = 'liste'.$id_liste;
		
	} else {
		list ($table, $col_id, $col_label, $col_langue, $langue) = explode (',', $source);
		$requete = 'select "", '.$col_id.', '.$col_label.' from '.$table;
		if (isset($col_langue)) $requete .= ' where '.$col_langue.'="'.$langue.'"';
		$nom_liste = $id_liste;
	}
	
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError ($resultat)) {
		return ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	if ($dans_moteur_de_recherche==0) {
		$select[0]=CHOISIR;
	}
	else {
		$select[0]=INDIFFERENT;
	}
	while ($ligne = $resultat->fetchRow()) {
		$select[$ligne[1]] = $ligne[2] ;		
	}
	$option = array('id' => $nom_liste);
	require_once 'HTML/QuickForm/select.php';
	$select= new HTML_QuickForm_select($nom_liste, $label, $select, $option);
	if ($limite2 != '') $select->setSize($limite2); 
	$select->setMultiple(0);
	//$select->setSelected($defaut);
	
	//echo '<br>defaut : '.$defaut.' / nom liste '.$nom_liste;
	$formtemplate->addElement($select) ;
	
	$formtemplate->setDefaults(array($nom_liste, $defaut));
	
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('liste'.$id_liste, BAZ_CHOISIR_OBLIGATOIRE.' '.$label , 'nonzero', '', 'client') ;
		$formtemplate->addRule('liste'.$id_liste, $label.' obligatoire', 'required', '', 'client') ;}
}


/** checkbox() - Ajoute un èlèment de type checkbox au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    int     identifiant de la liste sur bazar_liste
* @param    string  label è afficher dans le formulaire
* @param    string  première restriction de la taille des champs du formulaire
* @param    string  deuxième restriction de la taille des champs du formulaire
* @param    string  valeur par dèfaut du formulaire
* @param    string  table source pour les valeurs de la liste
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function checkbox(&$formtemplate, $id_liste , $label, $limite1, $limite2, $defaut, 
						$source, $obligatoire, $dans_moteur_de_recherche=0, $appli = 'bazar') {
	if (intval ($id_liste) != 0) {
	 	$l = $appli[0];
		$requete = 'SELECT * FROM '.$appli.'_liste_valeurs WHERE '.$l.'lv_ce_liste='.$id_liste.
					' AND '.$l.'lv_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ORDER BY '.$l.'lv_label';
		$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
		if (DB::isError ($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}		
		require_once 'HTML/QuickForm/checkbox.php' ;
		$i=0;
		if (isset($defaut)) $tab=split(', ', $defaut);
		//var_dump($tab);
		while ($ligne = $resultat->fetchRow()) {
			if ($i==0) $labelchkbox=$label ; else $labelchkbox='&nbsp;';
			$checkbox[$i]= & HTML_Quickform::createElement('checkbox', $ligne[1], $labelchkbox, $ligne[2], 
							array ('style'=>'display:inline;margin:2px;')) ;		
			foreach ($tab as $val) {
				//echo '<br>val : '.$val.'  / ligne :'.$ligne[1];
	            if ($ligne[1]==$val) {
	            	$checkbox[$i]->setChecked(true);
					$listechk[$ligne[1]]=true;
				}			        
	        }			
			$i++;
		}
		$squelette_checkbox =& $formtemplate->defaultRenderer();
		$squelette_checkbox->setElementTemplate( '<tr><td colspan="2" style="text-align:left;">'."\n".'<fieldset class="bazar_fieldset">'."\n".'<legend>{label}'.
	                                             '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
												 '</legend>'."\n".'{element}'."\n".'</fieldset> '."\n".'</td></tr>'."\n", 'checkbox'.$id_liste);
	  	$squelette_checkbox->setGroupElementTemplate( "\n".'<div class="bazar_checkbox">'."\n".'{element}'."\n".'</div>'."\n", 'checkbox'.$id_liste);
		$formtemplate->addGroup($checkbox, 'checkbox'.$id_liste, $label, "\n");
		if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
			$formtemplate->addGroupRule('checkbox'.$id_liste, $label.' obligatoire', 'required', null, 1, 'client');
		}	
		//var_dump($listechk);
		$defaultValues['checkbox'.$id_liste] = $listechk;
		$formtemplate->setDefaults($defaultValues);	
	} else {
		$checkbox = & HTML_Quickform::createElement('checkbox', $id_liste, $label);
		if ($defaut == 1) $checkbox->setChecked(true);
		$formtemplate->addElement($checkbox);
		
	}
	
}

function newsletter(&$formtemplate, $champs , $label, $mail_inscription, $mail_desinscription, $defaut, 
						$source, $obligatoire, $dans_moteur_de_recherche=0, $appli = 'bazar') {
	
	$checkbox = & HTML_Quickform::createElement('checkbox', $champs, '', $label);
	if ($defaut == 1) $checkbox->setChecked(true);
	$formtemplate->addElement($checkbox);
}

/** listedatedeb() - Ajoute un èlèment de type date sous forme de liste au formulaire pour designer une date de dèbut
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnèe
* @param    string  label è afficher dans le formulaire
* @param    string  première restriction de la taille des champs du formulaire
* @param    string  deuxième restriction de la taille des champs du formulaire
* @param    string  valeur par dèfaut du formulaire
* @param    string  table source pour les valeurs de la date
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function listedatedeb(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$optiondate = array('language' => BAZ_LANGUE_PAR_DEFAUT,
						'minYear' => date('Y')-4,
						'maxYear'=> (date('Y')+10),
						'format' => 'd m Y',
						'addEmptyOption' => BAZ_DATE_VIDE,
						);
	$formtemplate->addElement('date', $nom_bdd, $label, $optiondate) ;
	//gestion des valeurs par dèfaut (date du jour)	
	if (isset($defaut) && $defaut!='') {
		$tableau_date = explode ('-', $defaut);
		$formtemplate->setDefaults(array($nom_bdd => array ('d'=> $tableau_date[2], 'm'=> $tableau_date[1], 'Y'=> $tableau_date[0])));
	}
	
	else {
		$defauts=array($nom_bdd => array ('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')));
		$formtemplate->setDefaults($defauts);
	}
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule($nom_bdd, $label.' obligatoire', 'required', '', 'client') ;
	}
}

/** listedatefin() - Ajoute un èlèment de type date sous forme de liste au formulaire pour designer une date de fin
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnèe
* @param    string  label è afficher dans le formulaire
* @param    string  première restriction de la taille des champs du formulaire
* @param    string  deuxième restriction de la taille des champs du formulaire
* @param    string  valeur par dèfaut du formulaire
* @param    string  table source pour les valeurs de la date
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function listedatefin(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	listedatedeb($formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche);
}


/** texte() - Ajoute un élément de type texte au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnèe
* @param    string  label à afficher dans le formulaire
* @param    string  première restriction de la taille des champs du formulaire
* @param    string  deuxième restriction de la taille des champs du formulaire
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function texte(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$option=array('size'=>$limite1,'maxlength'=>$limite2, 'id' => $nom_bdd);
	$formtemplate->addElement('text', $nom_bdd, $label, $option) ;
	//gestion des valeurs par défaut
	$defauts=array($nom_bdd=>stripslashes($defaut));
	$formtemplate->setDefaults($defauts);
	$formtemplate->applyFilter($nom_bdd, 'addslashes') ;
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule($nom_bdd,  $label.' obligatoire', 'required', '', 'client') ;
	}
}

/** utilisateur_wikini() - Ajoute un utilisateur wikini
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnèe
* @param    string  label à afficher dans le formulaire
* @param    string  première restriction de la taille des champs du formulaire
* @param    string  deuxième restriction de la taille des champs du formulaire
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function utilisateur_wikini(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$option=array('size'=>$limite1,'maxlength'=>$limite2, 'id' => 'nomwiki');
	if (isset($defaut) && $defaut!='') 
	{
		$option['readonly'] = 'readonly';
	}
	//on entre le NomWiki
	$formtemplate->addElement('text', 'nomwiki', "NomWiki", $option) ;	
	$defauts=array('nomwiki'=>stripslashes($defaut));
	$formtemplate->setDefaults($defauts);
	$formtemplate->applyFilter('nomwiki', 'addslashes') ;
	$formtemplate->addRule('nomwiki',  'NomWiki obligatoire', 'required', '', 'client') ;
	
	if (!isset($defaut))
	{
		//mot de passe
		$formtemplate->addElement('password', 'mot_de_passe_wikini', 'mot de passe', array('size' => $limite1)) ;
	    $formtemplate->addElement('password', 'mot_de_passe_repete_wikini', 'mot de passe (v&eacute;rification)', array('size' => $limite1)) ;
	    $formtemplate->addRule('mot_de_passe_wikini', 'mot de passe obligatoire', 'required', '', 'client') ;
	    $formtemplate->addRule('mot_de_passe_repete_wikini', 'mot de passe r&eacute;p&eacute;t&eacute; obligatoire', 'required', '', 'client') ;
	    $formtemplate->addRule(array ('mot_de_passe_wikini', 'mot_de_passe_repete_wikini'), 'Les mots de passe doivent être identiques', 'compare', '', 'client') ;
	}
}


/** champs_cache() - Ajoute un élément de type texte au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnée
* @param    string  label é afficher dans le formulaire
* @param    string  premiére restriction de la taille des champs du formulaire
* @param    string  deuxiéme restriction de la taille des champs du formulaire
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function champs_cache(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$formtemplate->addElement('hidden', $nom_bdd, $label, array ('id' => $nom_bdd)) ;
	//gestion des valeurs par défaut
	$defauts=array($nom_bdd=>$defaut);
	$formtemplate->setDefaults($defauts);
}

/** champs_mail() - Ajoute un élément de type mail
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnée
* @param    string  label é afficher dans le formulaire
* @param    string  premiére restriction de la taille des champs du formulaire
* @param    string  deuxiéme restriction de la taille des champs du formulaire
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function champs_mail(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$option=array('size'=>$limite1,'maxlength'=>$limite2, 'id' => $nom_bdd);
	$formtemplate->addElement('text', $nom_bdd, $label, $option) ;
	//gestion des valeurs par defaut
	$defauts=array($nom_bdd=>$defaut);
	$formtemplate->setDefaults($defauts);
	$formtemplate->applyFilter($nom_bdd, 'addslashes') ;
	$formtemplate->addRule($nom_bdd,  $label.' obligatoire', 'required', '', 'client') ;
	$formtemplate->addRule($nom_bdd, 'Format de l\'adresse mail incorrect', 'email', '', 'client') ; 
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule($nom_bdd,  $label.' obligatoire', 'required', '', 'client') ;
	}	
}

function mot_de_passe(&$formtemplate, $nom_bdd , $label1, $limite1, $limite2, $erreur1, $label2, $obligatoire) {
	$formtemplate->addElement('password', 'mot_de_passe', $label1, array('size' => $limite1)) ;
    $formtemplate->addElement('password', 'mot_de_passe_repete', $label2, array('size' => $limite1)) ;
    $formtemplate->addRule('mot_de_passe', $erreur1, 'required', '', 'client') ;
    $formtemplate->addRule('mot_de_passe_repete', $erreur1, 'required', '', 'client') ;
    $formtemplate->addRule(array ('mot_de_passe', 'mot_de_passe_repete'), $erreur1, 'compare', '', 'client') ;
}


/** textelong() - Ajoute un élément de type textearea au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnée
* @param    string  label é afficher dans le formulaire
* @param    string  taille des colonnes de l'élément
* @param    string  taille des lignes de l'élément
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function textelong(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$formtexte= new HTML_QuickForm_textarea($nom_bdd, $label, array('style'=>'white-space: normal;overflow:visible;', 'id' => $nom_bdd));
	$formtexte->setCols($limite1);
	$formtexte->setRows($limite2);
	$formtemplate->addElement($formtexte) ;
	//gestion des valeurs par défaut
	$defauts=array($nom_bdd=>stripslashes($defaut));
	$formtemplate->setDefaults($defauts);
	$formtemplate->applyFilter($nom_bdd, 'addslashes') ;
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule($nom_bdd,  $label.' obligatoire', 'required', '', 'client') ;
	}
}

/** url() - Ajoute un élément de type url internet au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le différencier d'autres champs du meme type dans ce formulaire)
* @param    string  label é afficher dans le formulaire
* @param    string  taille des colonnes de l'élément
* @param    string  taille des lignes de l'élément
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function url(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0, $appli = 'bazar') {
	//recherche des URLs deja entrees dans la base
	$html_url= '';
	if (isset($GLOBALS['_BAZAR_']["id_fiche"]) && $GLOBALS['_BAZAR_']["id_fiche"]!=NULL) {
		$requete = 'SELECT bu_id_url, bu_url, bu_descriptif_url FROM bazar_url WHERE bu_ce_fiche='.$GLOBALS['_BAZAR_']["id_fiche"];
		$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
		if (DB::isError ($resultat)) {
			die ($GLOBALS['_BAZAR_']['db']->getMessage().$GLOBALS['_BAZAR_']['db']->getDebugInfo()) ;
		}
		if ($resultat->numRows()>0) {
			$html_url= '<tr>'."\n".'<td colspan="2">'."\n".'<strong>'.BAZ_LISTE_URL.'</strong>'."\n";
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
			$html_url.= $table->toHTML()."\n".'</td>'."\n".'</tr>'."\n" ;
		}
	}		
	$html ='<tr>'."\n".'<td colspan="2">'."\n".'<h4>'.$label.'</h4>'."\n".'</td>'."\n".'</tr>'."\n";
	$formtemplate->addElement('html', $html) ;
	if ($html_url!='') $formtemplate->addElement('html', $html_url) ;
	$formtemplate->addElement('text', 'url_lien'.$nom_bdd, BAZ_URL_LIEN) ;
	$defauts=array('url_lien'.$nom_bdd=>'http://');
	$formtemplate->setDefaults($defauts);
	
	$formtemplate->addElement('text', 'url_texte'.$nom_bdd, BAZ_URL_TEXTE) ;
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('url_lien'.$nom_bdd, BAZ_URL_LIEN_REQUIS, 'required', '', 'client') ;
		$formtemplate->addRule('url_texte'.$nom_bdd, BAZ_URL_TEXTE_REQUIS, 'required', '', 'client') ;
	}
}		


function lien_internet (&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $id_fiche, $obligatoire, $dans_moteur_de_recherche=0) {
	//recherche des URLs deja entrees dans la base
	$html_url= '';	
	$formtemplate->addElement('text', $nom_bdd, $label)	;
	$defauts=array($nom_bdd=>'http://');
	$formtemplate->setDefaults($defauts);
	
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('url', URL_LIEN_REQUIS, 'required', '', 'client') ;
	}
}
/** fichier() - Ajoute un élément de type fichier au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le différencier d'autres champs du meme type dans ce formulaire)
* @param    string  label é afficher dans le formulaire
* @param    string  taille des colonnes de l'élément
* @param    string  taille des lignes de l'élément
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function fichier(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	//AJOUTER DES FICHIERS JOINTS
	$html_fichier= '';
	if (isset($GLOBALS['_BAZAR_']["id_fiche"]) && $GLOBALS['_BAZAR_']["id_fiche"]!=NULL) {
		$requete = 'SELECT * FROM bazar_fichier_joint WHERE bfj_ce_fiche='.$GLOBALS['_BAZAR_']["id_fiche"];
		$resultat = $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
		if (DB::isError ($resultat)) {
			die ($GLOBALS['_BAZAR_']['db']->getMessage().$GLOBALS['_BAZAR_']['db']->getDebugInfo()) ;
		}
		
		if ($resultat->numRows()>0) {
			$html_fichier = '<tr>'."\n".'<td colspan="2">'."\n".'<strong>'.BAZ_LISTE_FICHIERS_JOINTS.'</strong>'."\n";
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
			$html_fichier .= $table->toHTML()."\n".'</td>'."\n".'</tr>'."\n" ;
		}
	}
	$html ='<tr>'."\n".'<td colspan="2">'."\n".'<h4>'.$label.'</h4>'."\n".'</td>'."\n".'</tr>'."\n";
	$formtemplate->addElement('html', $html) ;
	if ($html_fichier!='') $formtemplate->addElement('html', $html_fichier) ;
	$formtemplate->addElement('text', 'texte_fichier'.$nom_bdd, BAZ_FICHIER_DESCRIPTION) ;
	$formtemplate->addElement('file', 'fichier'.$nom_bdd, BAZ_FICHIER_JOINT) ;
	$formtemplate->addRule('image', BAZ_IMAGE_VALIDE_REQUIS, '', '', 'client') ; //a completer pour checker l'image
	$formtemplate->setMaxFileSize($limite1);
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('texte_fichier'.$nom_bdd, BAZ_FICHIER_LABEL_REQUIS, 'required', '', 'client') ;
		$formtemplate->addRule('fichier'.$nom_bdd, BAZ_FICHIER_JOINT_REQUIS, 'required', '', 'client') ;
	}
}		

/** image() - Ajoute un élément de type image au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le différencier d'autres champs du meme type dans ce formulaire)
* @param    string  label é afficher dans le formulaire
* @param    string  taille maximum du fichier colonnes de l'élément
* @param    string  taille des lignes de l'élément
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function image(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	//AJOUTER UNE IMAGE
	$html_image= '';
	if (isset($GLOBALS['_BAZAR_']["id_fiche"]) && $GLOBALS['_BAZAR_']["id_fiche"]!=NULL) {
		$requete = 'SELECT bf_url_image FROM bazar_fiche WHERE bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
		$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
		if (DB::isError ($resultat)) {
			die ($GLOBALS['_BAZAR_']['db']->getMessage().$GLOBALS['_BAZAR_']['db']->getDebugInfo()) ;
		}
		
		if ($resultat->numRows()>0) {
			while ($ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT)) {
				$image=$ligne->bf_url_image;
			}
			if ($image!=NULL) {
				$lien_supprimer=$GLOBALS['_BAZAR_']['url'];
				$lien_supprimer->addQueryString('action', $_GET['action']);
				$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']["id_fiche"]);
				$lien_supprimer->addQueryString('typeannonce', $_REQUEST['typeannonce']);
				$lien_supprimer->addQueryString('image', 1);
				$html_image = '<tr>'."\n".
							  '<td>'."\n".'<img src="'.BAZ_CHEMIN.'actions/bazar/upload/'.$image.'" alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" width="130" height="130" />'."\n".'</td>'."\n".
							  '<td>'."\n".'<a href="'.$lien_supprimer->getURL().'" onclick="javascript:return confirm(\''.BAZ_CONFIRMATION_SUPPRESSION_IMAGE.'\');" >'.BAZ_SUPPRIMER.'</a><br /><br />'."\n".
							  '<strong>'.BAZ_POUR_CHANGER_IMAGE.'</strong><br />'."\n".'</td>'."\n".'</tr>'."\n";
			}
		}		
	}	
	$html ='<tr>'."\n".'<td colspan="2">'."\n".'<h4>'.$label.'</h4>'."\n".'</td>'."\n".'</tr>'."\n";
	$formtemplate->addElement('html', $html) ;
	if ($html_image!='') $formtemplate->addElement('html', $html_image) ;
	$formtemplate->addElement('file', 'image', IMAGE) ;
	//TODO: controler si c'est une image
	$formtemplate->setMaxFileSize($limite1);
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('image', IMAGE_VALIDE_REQUIS, 'required', '', 'client') ;
	}
}		

/** image_unique() - Ajoute un élément de type image au formulaire, l information est stockee dans un champs
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le différencier d'autres champs du meme type dans ce formulaire)
* @param    string  label é afficher dans le formulaire
* @param    string  taille maximum du fichier colonnes de l'élément
* @param    string  taille des lignes de l'élément
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function image_unique(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$formtemplate->addElement ('file', $nom_bdd, $label);
	$formtemplate->setMaxFileSize($limite1);
}	
/** wikini() - Ajoute un wikini au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le différencier d'autres champs du meme type dans ce formulaire)
* @param    string  label é afficher dans le formulaire
* @param    string  taille maximum du fichier colonnes de l'élément
* @param    string  taille des lignes de l'élément
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function wikini(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	return;
}

function formulaire_mail(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	return;
}

/** labelhtml() - Ajoute un élément de type textearea au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donnée (pas utilisé)
* @param    string  label é afficher dans le formulaire
* @param    string  taille des colonnes de l'élément (pas utilisé)
* @param    string  taille des lignes de l'élément (pas utilisé)
* @param    string  valeur par défaut du formulaire (pas utilisé)
* @param    string  table source pour les valeurs du texte (pas utilisé)
* @param    string  ce champs est il obligatoire? (required) (pas utilisé)
* @return   void
*/
function labelhtml(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	require_once 'HTML/QuickForm/html.php';
	$formhtml= new HTML_QuickForm_html('<tr>'."\n".'<td colspan="2" style="text-align:left;">'."\n".$label."\n".'</td>'."\n".'</tr>'."\n");
	$formtemplate->addElement($formhtml) ;
}

/** carte_google() - Ajoute un élément de carte google au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  l url vers la script google
* @param    string  label é afficher dans le formulaire
* @param    string  premiére restriction de la taille des champs du formulaire
* @param    string  deuxiéme restriction de la taille des champs du formulaire
* @param    string  valeur par défaut du formulaire
* @param    string  table source pour les valeurs de la liste
* @param    string  ce champs est il obligatoire? (required)
* @param    boolean sommes nous dans le moteur de recherche?
* @return   void
*/
function carte_google(&$formtemplate, $url_google_script , $label, $champs_latitude, $champs_longitude, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	
	if (is_array ($defaut)) {
    	$formtemplate->setDefaults(array('latitude' => $defaut['latitude'], 'longitude' => $defaut['longitude']));
    }

    if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
	
    $html_bouton = '<tr>
<td style="text-align:left;padding:5px;" colspan="2"> 
<input onclick="showAddress();" name="chercher_sur_carte" value="'.VERIFIER_MON_ADRESSE.'" type="button" /><span class="symbole_obligatoire">&nbsp;*</span></td>
</tr>';

    }
    else {  
    $html_bouton = '<tr>
<td style="text-align:left;padding:5px;" colspan="2"> 
'.METTRE_POINT.'
<input id="btn_adresse" onclick="showAddress();" name="chercher_sur_carte" value="'.VERIFIER_MON_ADRESSE.'" type="button" />
<input id="btn_client" onclick="showClientAddress();" name="chercher_client" value="'.VERIFIER_MON_ADRESSE_CLIENT.'" type="button" />
</td>
</tr>';
    }
$script = '<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
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
  	  scaleControl: '.BAZ_AFFICHER_ECHELLE.'     
    }
    map = new google.maps.Map(document.getElementById("map"), myOptions);
};

function showClientAddress(){
	// If ClientLocation was filled in by the loader, use that info instead
    if (google.loader.ClientLocation) {      
      latlngclient = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
      if(infowindow) {
	    infowindow.close();
	  }
	  if(marker) {
	   	marker.set_map(null);
	  } 
      map.set_center(latlngclient);                  
            var lat = document.getElementById("latitude");lat.value = map.get_center().lat();
  			var lon = document.getElementById("longitude");lon.value = map.get_center().lng();           
            
  			infowindow = new google.maps.InfoWindow({
		        content: "<h4>Votre emplacement</h4>'.TEXTE_POINT_DEPLACABLE.'",
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
			    var lat = document.getElementById("latitude");lat.value = marker.get_position().lat();
	  			var lon = document.getElementById("longitude");lon.value = marker.get_position().lng();
	  			map.set_center(marker.get_position()); 
	  		});
    }
    else {alert("Localisation par votre accès Internet impossible..");}
};
	
function showAddress() {
  var adress_1 = document.getElementById("bf_adresse1").value ;
  if (document.getElementById("bf_adresse2")) 	var adress_2 = document.getElementById("bf_adresse2").value ; else var adress_2 = "";
  var ville = document.getElementById("bf_ville").value ;
  var cp = document.getElementById("bf_code_postal").value ;
  if (document.getElementById("bf_ce_pays").type == "select-one") {
  	var selectIndex = document.getElementById("bf_ce_pays").selectedIndex;
  	var pays = document.getElementById("bf_ce_pays").options[selectIndex].text ;
  } else {
  	var pays = document.getElementById("bf_ce_pays").value;
  }
  
  var address = adress_1 + \' \' + adress_2 + \' \'  + cp + \' \' + ville + \' \' +pays ;
  if (geocoder) {
      geocoder.geocode( { \'address\': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
            if(infowindow) {
		      infowindow.close();
		    }
		    if(marker) {
		    	marker.set_map(null);
		    } 
          	map.set_center(results[0].geometry.location);             
            var lat = document.getElementById("latitude");lat.value = map.get_center().lat();
  			var lon = document.getElementById("longitude");lon.value = map.get_center().lng();           
            
  			infowindow = new google.maps.InfoWindow({
		        content: "<h4>Votre emplacement</h4>'.TEXTE_POINT_DEPLACABLE.'",
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
			    var lat = document.getElementById("latitude");lat.value = marker.get_position().lat();
	  			var lon = document.getElementById("longitude");lon.value = marker.get_position().lng();
	  			map.set_center(marker.get_position()); 
	  		});		
          } else {
            alert("Pas de résultats pour cette adresse: " + address);
          }
        } else {
          alert("Pas de résultats pour la raison suivante: " + status + ", rechargez la page.");
        }
      });
    }
  };
  
function setLatLonForm(marker) {
  coordMarker = marker.getPoint() ;
  var lat = document.getElementById("latitude");
  var lon = document.getElementById("longitude");
  lat.value = coordMarker.lat();
  lon.value = coordMarker.lng();
};
</script>';
	$formtemplate->addElement('html', $html_bouton);   
    $formtemplate->addElement('html', '<tr><td colspan="2">'.$script.'<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.'; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.';"></div></td></tr>');
    $formtemplate->addElement('text', 'latitude', LATITUDE, array('id' => 'latitude', 'size' => 6, 'readonly' => 'readonly'));
    $formtemplate->addElement('text', 'longitude', LONGITUDE, array('id' => 'longitude', 'size' => 6, 'readonly' => 'readonly'));
    
    if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
    	$formtemplate->addRule ('latitude', LATITUDE . ' obligatoire', 'required', '', 'client');
    	$formtemplate->addRule ('longitude', LONGITUDE . ' obligatoire', 'required', '', 'client');
    }
    
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: formulaire.fonct.inc.php,v $
* Revision 1.6  2009/09/09 15:36:37  mrflos
* maj css
* ajout de la google api v3
* possibilitÃ© d'insÃ©rer des utilisateurs wikini par bazar
* installation automatique du fichier sql avec type d'annonces par dÃ©faut
*
* Revision 1.5  2009/08/01 17:01:58  mrflos
* nouvelle action bazarcalendrier, correction bug typeannonce, validitÃ© html amÃ©liorÃ©e
*
* Revision 1.4  2008/08/28 14:49:52  mrflos
* amÃ©lioration des performances de bazar : google map pas chargÃ©e systematiquement
* correction bug flux rss
* correction bug calendrier
*
* Revision 1.3  2008/08/28 12:23:39  mrflos
* amÃ©rioration de la gestion des categories de fiches
*
* Revision 1.2  2008/07/29 17:32:25  mrflos
* maj gÃ©nÃ©rale
*
* Revision 1.12.2.1  2007-12-06 10:12:01  alexandre_tb
* appel de la fonction GEN_AttributsBody dans le composant carte_google
*
* Revision 1.12  2007-10-22 09:15:16  alexandre_tb
* modification de l include pour qu il prenne en compte la langue
*
* Revision 1.11  2007-10-17 08:16:40  alexandre_tb
* correction multilinguisme
*
* Revision 1.10  2007-10-12 09:58:12  alexandre_tb
* ajout de la fonction stripslashes pour les fonctions texte et textelong, dans la valeur par defaut, pour eviter l apparition de slash
*
* Revision 1.9  2007-10-10 13:59:24  alexandre_tb
* ajout de la fonction newsletter
* et utilisation du fichier de langue
*
* Revision 1.8  2007-09-18 08:00:42  alexandre_tb
* la valeur par defaut d un lien est http://pour eviter les erreurs
*
* Revision 1.7  2007-08-27 12:24:52  alexandre_tb
* correction encodage
*
* Revision 1.3  2007-07-04 11:53:37  alexandre_tb
* ajout du type champs_cache
*
* Revision 1.2  2007-06-25 09:54:33  alexandre_tb
* ajou des entetes (cecill),
* modif fonctin liste, ajout de carte_google et champs_mail
*
* 
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
