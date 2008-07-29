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
// CVS : $Id: bazar.fonct.formulaire.php,v 1.2 2008/07/29 17:32:25 mrflos Exp $
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
*@version       $Revision: 1.2 $ $Date: 2008/07/29 17:32:25 $
// +------------------------------------------------------------------------------------------------------+
*/

require_once 'bazar.fonct.formulaire.controles.php';

//-------------------FONCTIONS DE MISE EN PAGE DES FORMULAIRES

/** liste() - Ajoute un �l�ment de type liste au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    int     identifiant de la liste sur bazar_liste
* @param    string  label � afficher dans le formulaire
* @param    string  premi�re restriction de la taille des champs du formulaire
* @param    string  deuxi�me restriction de la taille des champs du formulaire
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs de la liste
* @param    string  ce champs est il obligatoire? (required)
* @param    boolean sommes nous dans le moteur de recherche?
* @return   void
*/
function liste(&$formtemplate, $id_liste , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$requete = 'SELECT * FROM bazar_liste_valeurs WHERE blv_ce_liste='.$id_liste.' AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'"';
	$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
	if (DB::isError ($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	if ($dans_moteur_de_recherche==0) {
		$select[0]=BAZ_CHOISIR;
	}
	else {
		$select[0]=BAZ_INDIFFERENT;
	}
	while ($ligne = $resultat->fetchRow()) {
		$select[$ligne[1]] = $ligne[2] ;		
	}
	$option=array('style'=>'width: '.$limite1.'px;', 'id' => 'liste'.$id_liste);
	require_once PAP_CHEMIN_API_PEAR.'HTML/QuickForm/select.php';
	$select= new HTML_QuickForm_select('liste'.$id_liste, $label, $select, $option);
	$select->setSize($limite2); 
	$select->setMultiple(0);
	$select->setSelected($defaut);
	$formtemplate->addElement($select) ;
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('liste'.$id_liste, BAZ_CHOISIR_OBLIGATOIRE.' '.$label , 'nonzero', '', 'client') ;
		$formtemplate->addRule('liste'.$id_liste, $label.' obligatoire', 'required', '', 'client') ;}
}


/** checkbox() - Ajoute un �l�ment de type checkbox au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    int     identifiant de la liste sur bazar_liste
* @param    string  label � afficher dans le formulaire
* @param    string  premi�re restriction de la taille des champs du formulaire
* @param    string  deuxi�me restriction de la taille des champs du formulaire
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs de la liste
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function checkbox(&$formtemplate, $id_liste , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$requete = 'SELECT * FROM bazar_liste_valeurs WHERE blv_ce_liste='.$id_liste.' AND blv_ce_i18n="'.$GLOBALS['_BAZAR_']['langue'].'" ORDER BY blv_label';
	$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
	if (DB::isError ($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}		
	require_once PAP_CHEMIN_API_PEAR.'HTML/QuickForm/checkbox.php' ;
	$i=0;
	if (isset($defaut)) $tab=split(', ', $defaut);
	while ($ligne = $resultat->fetchRow()) {
		if ($i==0) $labelchkbox=$label ; else $labelchkbox='&nbsp;';
		$checkbox[$i]= & HTML_Quickform::createElement('checkbox', $ligne[1], $labelchkbox, $ligne[2], 
						array ('style'=>'display:inline;margin:2px;')) ;		
		foreach ($tab as $val) {
            if ($ligne[1]==$val) $checkbox[$i]->setChecked(1);			        
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
}


/** listedatedeb() - Ajoute un �l�ment de type date sous forme de liste au formulaire pour designer une date de d�but
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donn�e
* @param    string  label � afficher dans le formulaire
* @param    string  premi�re restriction de la taille des champs du formulaire
* @param    string  deuxi�me restriction de la taille des champs du formulaire
* @param    string  valeur par d�faut du formulaire
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
	//gestion des valeurs par d�faut (date du jour)	
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

/** listedatefin() - Ajoute un �l�ment de type date sous forme de liste au formulaire pour designer une date de fin
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donn�e
* @param    string  label � afficher dans le formulaire
* @param    string  premi�re restriction de la taille des champs du formulaire
* @param    string  deuxi�me restriction de la taille des champs du formulaire
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs de la date
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function listedatefin(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	listedatedeb($formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche);
}


/** texte() - Ajoute un �l�ment de type texte au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donn�e
* @param    string  label � afficher dans le formulaire
* @param    string  premi�re restriction de la taille des champs du formulaire
* @param    string  deuxi�me restriction de la taille des champs du formulaire
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function texte(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$option=array('size'=>$limite1,'maxlength'=>$limite2, 'id' => $nom_bdd);
	$formtemplate->addElement('text', $nom_bdd, $label, $option) ;
	//gestion des valeurs par d�faut
	$defauts=array($nom_bdd=>$defaut);
	$formtemplate->setDefaults($defauts);
	$formtemplate->applyFilter($nom_bdd, 'addslashes') ;
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule($nom_bdd,  $label.' obligatoire', 'required', '', 'client') ;
	}
}


/** textelong() - Ajoute un �l�ment de type textearea au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donn�e
* @param    string  label � afficher dans le formulaire
* @param    string  taille des colonnes de l'�l�ment
* @param    string  taille des lignes de l'�l�ment
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function textelong(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	$formtexte= new HTML_QuickForm_textarea($nom_bdd, $label, array('style'=>'white-space: normal;', 'id' => $nom_bdd));
	$formtexte->setCols($limite1);
	$formtexte->setRows($limite2);
	$formtemplate->addElement($formtexte) ;
	//gestion des valeurs par d�faut
	$defauts=array($nom_bdd=>$defaut);
	$formtemplate->setDefaults($defauts);
	$formtemplate->applyFilter($nom_bdd, 'addslashes') ;
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule($nom_bdd,  $label.' obligatoire', 'required', '', 'client') ;
	}
}

/** url() - Ajoute un �l�ment de type url internet au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le diff�rencier d'autres champs du meme type dans ce formulaire)
* @param    string  label � afficher dans le formulaire
* @param    string  taille des colonnes de l'�l�ment
* @param    string  taille des lignes de l'�l�ment
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function url(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	//recherche des URLs deja entrees dans la base
	$html_url= '';
	if (isset($GLOBALS['_BAZAR_']["id_fiche"])) {
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
	$formtemplate->addElement('text', 'url_texte'.$nom_bdd, BAZ_URL_TEXTE) ;
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('url_lien'.$nom_bdd, BAZ_URL_LIEN_REQUIS, 'required', '', 'client') ;
		$formtemplate->addRule('url_texte'.$nom_bdd, BAZ_URL_TEXTE_REQUIS, 'required', '', 'client') ;
	}
}		

/** fichier() - Ajoute un �l�ment de type fichier au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le diff�rencier d'autres champs du meme type dans ce formulaire)
* @param    string  label � afficher dans le formulaire
* @param    string  taille des colonnes de l'�l�ment
* @param    string  taille des lignes de l'�l�ment
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function fichier(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	//AJOUTER DES FICHIERS JOINTS
	$html_fichier= '';
	if (isset($GLOBALS['_BAZAR_']["id_fiche"])) {
		$requete = 'SELECT * FROM bazar_fichier_joint WHERE bfj_ce_fiche='.$GLOBALS['_BAZAR_']["id_fiche"];
		$resultat = & $GLOBALS['_BAZAR_']['db'] -> query($requete) ;
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

/** image() - Ajoute un �l�ment de type image au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le diff�rencier d'autres champs du meme type dans ce formulaire)
* @param    string  label � afficher dans le formulaire
* @param    string  taille maximum du fichier colonnes de l'�l�ment
* @param    string  taille des lignes de l'�l�ment
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function image(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	//AJOUTER UNE IMAGE
	$html_image= '';
	if (isset($GLOBALS['_BAZAR_']["id_fiche"])) {
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
							  '<td>'."\n".'<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$image.'" alt="'.BAZ_TEXTE_IMG_ALTERNATIF.'" width="130" height="130" />'."\n".'</td>'."\n".
							  '<td>'."\n".'<a href="'.$lien_supprimer->getURL().'" onclick="javascript:return confirm(\''.BAZ_CONFIRMATION_SUPPRESSION_IMAGE.'\');" >'.BAZ_SUPPRIMER.'</a><br /><br />'."\n".
							  '<strong>'.BAZ_POUR_CHANGER_IMAGE.'</strong><br />'."\n".'</td>'."\n".'</tr>'."\n";
			}
		}		
	}	
	$html ='<tr>'."\n".'<td colspan="2">'."\n".'<h4>'.$label.'</h4>'."\n".'</td>'."\n".'</tr>'."\n";
	$formtemplate->addElement('html', $html) ;
	if ($html_image!='') $formtemplate->addElement('html', $html_image) ;
	$formtemplate->addElement('file', 'image', BAZ_IMAGE) ;
	//TODO: controler si c'est une image
	$formtemplate->setMaxFileSize($limite1);
	//gestion du champs obligatoire
	if (($dans_moteur_de_recherche==0) && isset($obligatoire) && ($obligatoire==1)) {
		$formtemplate->addRule('image', BAZ_IMAGE_VALIDE_REQUIS, 'required', '', 'client') ;
	}
}		

/** wikini() - Ajoute un wikini au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  numero du champs input du formulaire (pour le diff�rencier d'autres champs du meme type dans ce formulaire)
* @param    string  label � afficher dans le formulaire
* @param    string  taille maximum du fichier colonnes de l'�l�ment
* @param    string  taille des lignes de l'�l�ment
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs du texte (inutile)
* @param    string  ce champs est il obligatoire? (required)
* @return   void
*/
function wikini(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	return;
}

/** labelhtml() - Ajoute un �l�ment de type textearea au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  nom de la table dans la base de donn�e (pas utilis�)
* @param    string  label � afficher dans le formulaire
* @param    string  taille des colonnes de l'�l�ment (pas utilis�)
* @param    string  taille des lignes de l'�l�ment (pas utilis�)
* @param    string  valeur par d�faut du formulaire (pas utilis�)
* @param    string  table source pour les valeurs du texte (pas utilis�)
* @param    string  ce champs est il obligatoire? (required) (pas utilis�)
* @return   void
*/
function labelhtml(&$formtemplate, $nom_bdd , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	require_once PAP_CHEMIN_API_PEAR.'HTML/QuickForm/html.php';
	$formhtml= new HTML_QuickForm_html('<tr>'."\n".'<td colspan="2" style="text-align:left;">'."\n".$label."\n".'</td>'."\n".'</tr>'."\n");
	$formtemplate->addElement($formhtml) ;
}

/** carte_google() - Ajoute un �l�ment de carte google au formulaire
*
* @param    mixed   L'objet QuickForm du formulaire
* @param    string  l url vers la script google
* @param    string  label � afficher dans le formulaire
* @param    string  premi�re restriction de la taille des champs du formulaire
* @param    string  deuxi�me restriction de la taille des champs du formulaire
* @param    string  valeur par d�faut du formulaire
* @param    string  table source pour les valeurs de la liste
* @param    string  ce champs est il obligatoire? (required)
* @param    boolean sommes nous dans le moteur de recherche?
* @return   void
*/
function carte_google(&$formtemplate, $url_google_script , $label, $limite1, $limite2, $defaut, $source, $obligatoire, $dans_moteur_de_recherche=0) {
	
	if (is_array ($defaut)) {
    	$formtemplate->setDefaults(array('latitude' => $defaut['latitude'], 'longitude' => $defaut['longitude']));
    }
	
	GEN_stockerFichierScript('googleMapScript', $url_google_script);
        	
	$formtemplate->addElement('button', 'chercher_sur_carte', 'V�rifier mon adresse avec la carte', array("onclick" => "showAddress();"));
    $formtemplate->addElement('text', 'latitude', 'Latitude', array('id' => 'latitude', 'size' => 6, 'readonly' => 'readonly'));
    $formtemplate->addElement('text', 'longitude', 'longitude', array('id' => 'longitude', 'size' => 6, 'readonly' => 'readonly'));
    $formtemplate->addElement('html', '<tr><td colspan="2"	><div id="map" style="width: 600px; height: 450px"></div></td></tr>');
    
    include_once BAZ_CHEMIN_APPLI.'bibliotheque/bazar.fonct.google.php';
    GEN_stockerCodeScript($script);
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.fonct.formulaire.php,v $
* Revision 1.2  2008/07/29 17:32:25  mrflos
* maj générale
*
* Revision 1.1  2008/07/07 18:00:39  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:47  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.26  2007-06-04 15:24:31  alexandre_tb
* ajout du type carte_google
*
* Revision 1.25  2007/04/11 08:30:12  neiluj
* remise en état du CVS...
*
* Revision 1.22.2.3  2007/03/16 14:48:32  alexandre_tb
* on peut maintenant saisir une date par d�faut
* et 0000-00-00 pour des champs vide
*
* Revision 1.22.2.2  2007/03/07 17:20:19  jp_milcent
* Ajout du nettoyage syst�matique des URLs.
*
* Revision 1.22.2.1  2007/01/17 16:01:37  alexandre_tb
* les dates ne s'affichent pas si elles sont vides.
* les champs dates propose 4 ann�es avant l'ann�e actuelle
*
* Revision 1.22  2006/06/02 09:29:07  florian
* debut d'integration de wikini
*
* Revision 1.21  2006/05/19 13:54:11  florian
* stabilisation du moteur de recherche, corrections bugs, lien recherche avancee
*
* Revision 1.20  2006/04/28 12:46:14  florian
* integration des liens vers annuaire
*
* Revision 1.19  2006/03/02 20:36:52  florian
* les entrees du formulaire de saisir ne sont plus dans les constantes mias dans des tables qui gerent le multilinguisme.
*
* Revision 1.18  2006/03/01 16:01:46  florian
* correction bug déclaration variable $html
*
* Revision 1.17  2006/01/30 10:27:03  florian
* - ajout des entrées de formulaire fichier, url, et image
* - correction bug d'affichage du mode de saisie
*
* Revision 1.16  2006/01/24 14:11:44  alexandre_tb
* ajout du param�tre emptyOptionValue dans les champs de date
*
* Revision 1.15  2006/01/23 16:57:03  alexandre_tb
* correction d'un notice
*
* Revision 1.14  2006/01/19 17:42:11  florian
* ajout des cases à cocher pré-cochées pour les maj
*
* Revision 1.13  2006/01/13 14:12:51  florian
* utilisation des temlates dans la table bazar_nature
*
* Revision 1.12  2006/01/05 16:28:24  alexandre_tb
* prise en chage des checkbox, reste la mise � jour � g�rer
*
* Revision 1.11  2006/01/02 13:24:07  alexandre_tb
* correction de bug d'un mauvais retour en erreur
*
* Revision 1.10  2005/12/01 16:05:41  florian
* changement des chemins pour appli Pear
*
* Revision 1.9  2005/11/24 16:17:13  florian
* corrections bugs, ajout des cases à cocher
*
* Revision 1.8  2005/11/18 16:03:23  florian
* correction bug html entites
*
* Revision 1.7  2005/11/07 17:51:06  florian
* correction bug liste
*
* Revision 1.6  2005/11/07 17:30:36  florian
* ajout controle sur les listes pour la saisie
*
* Revision 1.5  2005/11/07 17:05:45  florian
* amélioration validation conditions de saisie, ajout des règles spécifiques de saisie des formulaires
*
* Revision 1.4  2005/10/25 13:53:02  alexandre_tb
* ajout d'un filtre dans les formulaire texte et textelong
*
* Revision 1.3  2005/07/21 19:03:12  florian
* nouveautés bazar: templates fiches, correction de bugs, ...
*
* Revision 1.1.1.1  2005/02/17 18:05:11  florian
* Import initial de Bazar
*
* Revision 1.1.1.1  2005/02/17 11:09:50  florian
* Import initial
*
* Revision 1.1.1.1  2005/02/16 18:06:35  florian
* import de la nouvelle version
*
* Revision 1.4  2004/07/08 11:17:59  alex
* ajout de commentaires et d'entete
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
