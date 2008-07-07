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
// CVS : $Id: bazar.fonct.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $
/**
*
* Fonctions du module bazar
*
*
*@package bazar
//Auteur original :
*@author        Alexandre Granier <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@ecole-et-nature.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'HTML/QuickForm.php' ;
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'HTML/QuickForm/checkbox.php' ;
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'HTML/QuickForm/textarea.php' ;
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'HTML/Table.php' ;
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'formulaire/formulaire.fonct.inc.php';
require_once 'bazar.fonct.rss.php';


/** fiches_a_valider () - Renvoie les annonces restant a valider par un administrateur
*
* @return   string  HTML
*/
function fiches_a_valider() {
	// Pour les administrateurs d'une rubrique, on affiche les fiches a valider de cette rubrique
	// On effectue une requete sur le bazar pour voir les fiches a administrer
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_ADMIN);
	$res= '<h2>'.BAZ_ANNONCES_A_ADMINISTRER.'</h2><br />'."\n";
	$requete = 'SELECT * FROM bazar_fiche, bazar_nature WHERE bf_statut_fiche=0 AND ' .
				'bn_id_nature=bf_ce_nature AND bn_ce_id_menu IN ('.$GLOBALS['_BAZAR_']['categorie_nature'].') ' ;
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
	}
	$requete .= 'ORDER BY bf_date_maj_fiche DESC' ;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	if ($resultat->numRows() != 0) {
		$tableAttr = array('id' => 'table_bazar') ;
		$table = new HTML_Table($tableAttr) ;
		$entete = array (BAZ_TITREANNONCE ,BAZ_ANNONCEUR, BAZ_TYPEANNONCE, BAZ_PUBLIER, BAZ_SUPPRIMER) ;
		$table->addRow($entete) ;
		$table->setRowType (0, 'th') ;

		// On affiche une ligne par proposition
		while ($ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC)) {
			//Requete pour trouver le nom et prenom de l'annonceur
			$requetenomprenom = 'SELECT '.BAZ_CHAMPS_PRENOM.', '.BAZ_CHAMPS_NOM.' FROM '.BAZ_ANNUAIRE.
								' WHERE '.BAZ_CHAMPS_ID.'='.$ligne['bf_ce_utilisateur'] ;
			$resultatnomprenom = $GLOBALS['_BAZAR_']['db']->query ($requetenomprenom) ;
			if (DB::isError($resultatnomprenom)) {
				echo ("Echec de la requete<br />".$resultatnomprenom->getMessage()."<br />".$resultatnomprenom->getDebugInfo()) ;
			}
			while ($lignenomprenom = $resultatnomprenom->fetchRow (DB_FETCHMODE_ASSOC)) {
				$annonceur=$lignenomprenom[BAZ_CHAMPS_PRENOM]." ".$lignenomprenom[BAZ_CHAMPS_NOM];
			}
			$lien_voir=$GLOBALS['_BAZAR_']['url'];
			$lien_voir->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien_voir->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_voir->addQueryString('typeannonce', $ligne['bn_id_nature']);

			// Nettoyage de l'url
			// NOTE (jpm - 23 mai 2007): pour �tre compatible avec PHP5 il faut utiliser tjrs $GLOBALS['_BAZAR_']['url'] car en php4 on
			// copie bien une variable mais pas en php5, cela reste une r�f�rence...
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
			$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');

			$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$GLOBALS['_BAZAR_']['url']->addQueryString('typeannonce', $ligne['bn_id_nature']);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien_voir = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_PUBLIER);
			$lien_publie_oui = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_PAS_PUBLIER);
			$lien_publie_non = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
			$lien_supprimer = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
			$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');

			$table->addRow (array(
			                '<a href="'.$lien_voir.'">'.$ligne['bf_titre'].'</a>'."\n", // col 1 : le nom
					$annonceur."\n", // col 2 : annonceur
					$ligne['bn_label_nature']."\n", // col 3 : type annonce
					"<a href=\"".$lien_publie_oui."\">".BAZ_OUI."</a> / \n".
					"<a href=\"".$lien_publie_non."\">".BAZ_NON."</a>", // col 4 : publier ou pas
					"<a href=\"".$lien_supprimer."\"".
					" onclick=\"javascript:return confirm('".BAZ_CONFIRMATION_SUPPRESSION."');\">".BAZ_SUPPRIMER."</a>\n")) ; // col 5 : supprimer

		}
		$table->altRowAttributes(1, array("class" => "ligne_impaire"), array("class" => "ligne_paire"));
		$table->updateColAttributes(1, array("align" => "center"));
		$table->updateColAttributes(2, array("align" => "center"));
		$table->updateColAttributes(3, array("align" => "center"));
		$table->updateColAttributes(4, array("align" => "center"));
		$res .= $table->toHTML() ;
	}
	else {
		$res .= '<p class="zone_info">'.BAZ_PAS_DE_FICHE_A_VALIDER.'</p>'."\n" ;
	}
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_TOUTES_ANNONCES);

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');

	// Les autres fiches, deja validees
	$res .= '<h2>'.BAZ_TOUTES_LES_FICHES.'</h2>'."\n";
    $requete = 'SELECT * FROM bazar_fiche, bazar_nature WHERE bf_statut_fiche=1 AND ' .
				'bn_id_nature=bf_ce_nature AND bn_ce_id_menu IN ('.$GLOBALS['_BAZAR_']['categorie_nature'].') ';
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
	}
	$requete .= 'ORDER BY bf_date_maj_fiche DESC' ;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	if ($resultat->numRows() != 0) {
		$tableAttr = array('id' => 'table_bazar') ;
		$table = new HTML_Table($tableAttr) ;
		$entete = array (BAZ_TITREANNONCE ,BAZ_ANNONCEUR, BAZ_TYPEANNONCE, BAZ_PUBLIER, BAZ_SUPPRIMER) ;
		$table->addRow($entete) ;
		$table->setRowType (0, 'th') ;

		// On affiche une ligne par proposition
		while ($ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC)) {
			//Requete pour trouver le nom et prenom de l'annonceur
			$requetenomprenom = 'SELECT '.BAZ_CHAMPS_PRENOM.', '.BAZ_CHAMPS_NOM.' FROM '.BAZ_ANNUAIRE.
								' WHERE '.BAZ_CHAMPS_ID.'='.$ligne['bf_ce_utilisateur'] ;
			$resultatnomprenom = $GLOBALS['_BAZAR_']['db']->query ($requetenomprenom) ;
			if (DB::isError($resultatnomprenom)) {
				echo ("Echec de la requete<br />".$resultatnomprenom->getMessage()."<br />".$resultatnomprenom->getDebugInfo()) ;
			}
			while ($lignenomprenom = $resultatnomprenom->fetchRow (DB_FETCHMODE_ASSOC)) {
				$annonceur=$lignenomprenom[BAZ_CHAMPS_PRENOM]." ".$lignenomprenom[BAZ_CHAMPS_NOM];
			}
			$lien_voir=$GLOBALS['_BAZAR_']['url'];
			$lien_voir->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien_voir->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_voir->addQueryString('typeannonce', $ligne['bn_id_nature']);

			// Nettoyage de l'url
			// NOTE (jpm - 23 mai 2007): pour �tre compatible avec PHP5 il faut utiliser tjrs $GLOBALS['_BAZAR_']['url'] car en php4 on
			// copie bien une variable mais pas en php5, cela reste une r�f�rence...
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
			$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');

			$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$GLOBALS['_BAZAR_']['url']->addQueryString('typeannonce', $ligne['bn_id_nature']);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien_voir = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_PUBLIER);
			$lien_publie_oui = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_PAS_PUBLIER);
			$lien_publie_non = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
			$lien_supprimer = $GLOBALS['_BAZAR_']['url']->getURL();
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);

			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
			$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');

			$table->addRow (array(
			                '<a href="'.$lien_voir.'">'.$ligne['bf_titre'].'</a>'."\n", // col 1 : le nom
					$annonceur."\n", // col 2 : annonceur
					$ligne['bn_label_nature']."\n", // col 3 : type annonce
					"<a href=\"".$lien_publie_oui."\">".BAZ_OUI."</a> / \n".
					"<a href=\"".$lien_publie_non."\">".BAZ_NON."</a>", // col 4 : publier ou pas
					"<a href=\"".$lien_supprimer."\"".
					" onclick=\"javascript:return confirm('".BAZ_CONFIRMATION_SUPPRESSION."');\">".BAZ_SUPPRIMER."</a>\n")) ; // col 5 : supprimer

		}
		$table->altRowAttributes(1, array("class" => "ligne_impaire"), array("class" => "ligne_paire"));
		$table->updateColAttributes(1, array("align" => "center"));
		$table->updateColAttributes(2, array("align" => "center"));
		$table->updateColAttributes(3, array("align" => "center"));
		$table->updateColAttributes(4, array("align" => "center"));
		$res .= $table->toHTML() ;
	}
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
	return $res;
}


/** mes_fiches () - Renvoie les fiches bazar d'un utilisateur
*
* @return   string  HTML
*/
function mes_fiches() {
	$res= '<h2>'.BAZ_VOS_ANNONCES.'</h2><br />'."\n";
	if (!BAZ_SANS_AUTH && $GLOBALS['AUTH']->getAuth()) {
		// requete pour voir si l'utilisateur a des fiches a son nom, classees par date de MAJ et nature d'annonce
		$requete = 'SELECT * FROM bazar_fiche, bazar_nature WHERE bf_ce_utilisateur='. $GLOBALS['id_user'].
		           ' AND bn_id_nature=bf_ce_nature AND bn_ce_id_menu IN ('.$GLOBALS['_BAZAR_']['categorie_nature'].') ';
		if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
		$requete .= ' ORDER BY bf_date_maj_fiche DESC,bf_ce_nature ASC';

		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows() != 0) {
			$tableAttr = array('id' => 'table_bazar') ;
			$table = new HTML_Table($tableAttr) ;
			$entete = array (BAZ_TITREANNONCE , BAZ_TYPEANNONCE, BAZ_ETATPUBLICATION, BAZ_MODIFIER, BAZ_SUPPRIMER) ;
			$table->addRow($entete) ;
			$table->setRowType (0, "th") ;

		// On affiche une ligne par proposition
		while ($ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC)) {
			if ($ligne['bf_statut_fiche']==1) $publiee=BAZ_PUBLIEE;
			elseif ($ligne['bf_statut_fiche']==0) $publiee=BAZ_ENCOURSDEVALIDATION;
			else $publiee=BAZ_REJETEE;

			$lien_voir = $GLOBALS['_BAZAR_']['url'];
			$lien_voir->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien_voir->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_voir->addQueryString('typeannonce', $ligne['bn_id_nature']);
			$lien_voir_url=$lien_voir->getURL();

			$lien_modifier = $GLOBALS['_BAZAR_']['url'];
			$lien_modifier->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
			$lien_modifier->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_modifier->addQueryString('typeannonce', $ligne['bn_id_nature']);
			$lien_modifier_url=$lien_modifier->getURL();

			$lien_supprimer = $GLOBALS['_BAZAR_']['url'];
			$lien_supprimer->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
			$lien_supprimer->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_supprimer->addQueryString('typeannonce', $ligne['bn_id_nature']);
			$lien_supprimer_url=$lien_supprimer->getURL();

			$table->addRow (array(
			        '<a href="'.$lien_voir_url.'">'.$ligne['bf_titre'].'</a>'."\n", // col 1 : le nom
					$ligne['bn_label_nature']."\n", // col 2: type annonce
					$publiee."\n", // col 3 : publiee ou non
					'<a href="'.$lien_modifier_url.'">'.BAZ_MODIFIER.'</a>'."\n", // col 4 : modifier
					'<a href="'.$lien_supprimer_url.'" onclick="javascript:return '.
					'confirm(\''.BAZ_CONFIRMATION_SUPPRESSION.'\');" >'.BAZ_SUPPRIMER.'</a>'."\n")) ; // col 5 : supprimer
		}
		$table->altRowAttributes(1, array("class" => "ligne_impaire"), array("class" => "ligne_paire"));
		$table->updateColAttributes(1, array("align" => "left"));
		$table->updateColAttributes(2, array("align" => "center"));
		$table->updateColAttributes(3, array("align" => "center"));
		$table->updateColAttributes(4, array("align" => "center"));
		$res .= $table->toHTML() ;
		}
	    else {
	    	$res .= '<p class="zone_info">'.BAZ_PAS_DE_FICHE.'</p>'."\n" ;
	    }
	}
	else  {
		if (BAZ_UTILISE_TEMPLATE) {
			require_once BAZ_CHEMIN_APPLI.'bibliotheque/bazarTemplate.class.php';
	    	$modele = new bazarTemplate($GLOBALS['_BAZAR_']['db']);
	    	$res .= $modele->getTemplate(BAZ_TEMPLATE_MESSAGE_LOGIN, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['categorie_nature']);			
		} else {
			$res .= '<p class="BAZ_info">'.BAZ_IDENTIFIEZ_VOUS_POUR_VOIR_VOS_FICHES.'</p>'."\n";
		}		
	}
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_DEPOSER_ANNONCE);
	$res .= '<br /><ul id="liste_liens"><li id="lien_saisir"><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'" title="'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'">'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'</a></li></ul>';
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	return $res;
}

/** baz_gestion_droits() interface de gestion des droits
*
*   return  string le code HTML
*/
function baz_gestion_droits() {
	$lien_formulaire=$GLOBALS['_BAZAR_']['url'];
	$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_GERER_DROITS);

	//contruction du squelette du formulaire
	$formtemplate = new HTML_QuickForm('formulaire', 'post', preg_replace ('/&amp;/', '&', $lien_formulaire->getURL()) );
	$squelette =& $formtemplate->defaultRenderer();
	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'<table style="border:0;">'."\n".'{content}'."\n".'</table>'."\n".'</form>'."\n");
	$squelette->setElementTemplate( '<tr>'."\n".'<td style="font-size:12px;width:150px;text-align:right;">'."\n".'{label} :</td>'."\n".'<td style="text-align:left;padding:5px;"> '."\n".'{element}'."\n".
                                '<!-- BEGIN required --><span class="symbole_obligatoire">*</span><!-- END required -->'."\n".
                                '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                '</td>'."\n".'</tr>'."\n");
	$squelette->setElementTemplate( '<tr>'."\n".'<td colspan="2" class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
                                '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</td>'."\n".'</tr>'."\n", 'accept_condition');
	$squelette->setElementTemplate( '<tr><td colspan="2" class="bouton">{label}{element}</td></tr>'."\n", 'valider');
	$squelette->setRequiredNoteTemplate("\n".'<tr>'."\n".'<td colspan="2" class="symbole_obligatoire">* {requiredNote}</td></tr>'."\n");
	//Traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);
	//Initialisation de la variable personne
	if ( isset($_POST['personnes']) ) {
		$personne=$_POST['personnes'];
	}
	else $personne=0;

	//Cas ou les droits ont etes changes
	if (isset($_GET['pers'])) {
		$personne=$_GET['pers'];
		//CAS DES DROITS POUR UN TYPE D'ANNONCE: On efface tous les droits de la personne pour ce type d'annonce
		if (isset($_GET['idtypeannonce'])) {
			$requete = 'DELETE FROM bazar_droits WHERE bd_id_utilisateur='.$_GET['pers'].
				   ' AND bd_id_nature_offre='.$_GET['idtypeannonce'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		//CAS DU SUPER ADMIN: On efface tous les droits de la personne en general
		else {
			$requete = 'DELETE FROM bazar_droits WHERE bd_id_utilisateur='.$_GET['pers'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		if ($_GET['droits']=='superadmin') {
			$requete = 'INSERT INTO bazar_droits VALUES ('.$_GET['pers'].',0,0)';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		elseif ($_GET['droits']=='redacteur') {
			$requete = 'INSERT INTO bazar_droits VALUES ('.$_GET['pers'].','.$_GET['idtypeannonce'].',1)';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		elseif ($_GET['droits']=='admin') {
			$requete = 'INSERT INTO bazar_droits VALUES ('.$_GET['pers'].','.$_GET['idtypeannonce'].',2)';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
	}

	//requete pour obtenir l'id, le nom et prenom des personnes inscrites a l'annuaire sauf soi meme
	$requete = 'SELECT '.BAZ_CHAMPS_ID.', '.BAZ_CHAMPS_NOM.', '.BAZ_CHAMPS_PRENOM.' FROM '.BAZ_ANNUAIRE.
		   ' WHERE '.BAZ_CHAMPS_ID." != ".$GLOBALS['id_user'].' ORDER BY '.BAZ_CHAMPS_NOM.' ASC';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		echo ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	$res='<h2>'.BAZ_GESTION_DES_DROITS.'</h2><br />'."\n";
	$res.=BAZ_DESCRIPTION_GESTION_DES_DROITS.'<br /><br />'."\n";
	$personnes_select[0]=BAZ_SELECTION;
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$personnes_select[$ligne[BAZ_CHAMPS_ID]] = $ligne[BAZ_CHAMPS_NOM]." ".$ligne[BAZ_CHAMPS_PRENOM] ;
	}
	$java=array ('style'=>'width:250px;','onchange'=>'this.form.submit();');
	$formtemplate->addElement ('select', 'personnes', BAZ_LABEL_CHOIX_PERSONNE, $personnes_select, $java) ;
	$defauts=array ('personnes'=>$personne);
	$formtemplate->setDefaults($defauts);
	$res.= $formtemplate->toHTML().'<br />'."\n" ;

	if ($personne!=0) {
		//cas du super utilisateur
		$utilisateur = new Utilisateur_bazar($personne) ;
		if ($utilisateur->isSuperAdmin()) {
			$res.= '<br />'.BAZ_EST_SUPERADMINISTRATEUR.'<br /><br />'."\n";
			$lien_enlever_superadmin=$GLOBALS['_BAZAR_']['url'];
			$lien_enlever_superadmin->addQueryString(BAZ_VARIABLE_ACTION, BAZ_GERER_DROITS);
			$lien_enlever_superadmin->addQueryString('pers', $personne);
			$lien_enlever_superadmin->addQueryString('droits', 'aucun');
			$res.= '<a href='.$lien_enlever_superadmin->getURL().'>'.BAZ_CHANGER_SUPERADMINISTRATEUR.'</a><br />'."\n";
		}
		else {
			$lien_passer_superadmin=$GLOBALS['_BAZAR_']['url'];
			$lien_passer_superadmin->addQueryString(BAZ_VARIABLE_ACTION, BAZ_GERER_DROITS);
			$lien_passer_superadmin->addQueryString('pers', $personne);
			$lien_passer_superadmin->addQueryString('droits', 'superadmin');
			$res.= '<a href='.$lien_passer_superadmin->getURL().'>'.BAZ_PASSER_SUPERADMINISTRATEUR.'</a><br />'."\n";

			//on cherche les differentes rubriques d'annonces
			$requete = 'SELECT bn_id_nature, bn_label_nature, bn_image_titre FROM bazar_nature';
			if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' where bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			$res.='<br /><b>'.BAZ_DROITS_PAR_TYPE.'</b><br /><br />';

			$table = new HTML_Table(array ('width' => '100%', 'class' => 'table_bazar')) ;
			$table->addRow(array ('<strong>'.BAZ_TYPE_ANNONCES.'</strong>',
			                      '<strong>'.BAZ_DROITS_ACTUELS.'</strong>',
					      '<strong>'.BAZ_PASSER_EN.'</strong>',
					      '<strong>'.BAZ_OU_PASSER_EN.'</strong>')) ;
			$table->setRowType (0, 'th') ;

			while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
				$lien_aucun_droit=$GLOBALS['_BAZAR_']['url'];
				$lien_aucun_droit->addQueryString(BAZ_VARIABLE_ACTION, BAZ_GERER_DROITS);
				$lien_aucun_droit->addQueryString('pers', $personne);
				$lien_aucun_droit->addQueryString('droits', 'aucun');
				$lien_aucun_droit->addQueryString('idtypeannonce', $ligne["bn_id_nature"]);

				$lien_passer_redacteur=$GLOBALS['_BAZAR_']['url'];
				$lien_passer_redacteur->addQueryString(BAZ_VARIABLE_ACTION, BAZ_GERER_DROITS);
				$lien_passer_redacteur->addQueryString('pers', $personne);
				$lien_passer_redacteur->addQueryString('droits', 'redacteur');
				$lien_passer_redacteur->addQueryString('idtypeannonce', $ligne["bn_id_nature"]);

				$lien_passer_admin=$GLOBALS['_BAZAR_']['url'];
				$lien_passer_admin->addQueryString(BAZ_VARIABLE_ACTION, BAZ_GERER_DROITS);
				$lien_passer_admin->addQueryString('pers', $personne);
				$lien_passer_admin->addQueryString('droits', 'admin');
				$lien_passer_admin->addQueryString('idtypeannonce', $ligne["bn_id_nature"]);
				if (isset($ligne['bn_image_titre'])) {
					$titre='&nbsp;<img src="client/bazar/images/'.$ligne['bn_image_titre'].'" alt="'.$ligne['bn_label_nature'].'" />'."\n";
				} else {
					$titre='<strong>&nbsp;'.$ligne['bn_label_nature'].'</strong>'."\n";
				}
				if ($utilisateur->isAdmin($ligne['bn_id_nature'])) {
					$table->addRow(array($titre,
							     BAZ_DROIT_ADMIN,
							     '<a href='.$lien_aucun_droit->getURL().'>'.BAZ_AUCUN_DROIT.'</a>',
							     '<a href='.$lien_passer_redacteur->getURL().'>'.BAZ_LABEL_REDACTEUR.'</a>'));
				}
				elseif ($utilisateur->isRedacteur($ligne['bn_id_nature'])) {
					$table->addRow(array($titre,
					                     BAZ_LABEL_REDACTEUR,
					                     '<a href='.$lien_aucun_droit->getURL().'>'.BAZ_AUCUN_DROIT.'</a>',
							     '<a href='.$lien_passer_admin->getURL().'>'.BAZ_DROIT_ADMIN.'</a>'));
				}
				else {
					$table->addRow(array($titre,
					                     BAZ_AUCUN_DROIT,
					                     '<a href='.$lien_passer_redacteur->getURL().'>'.BAZ_LABEL_REDACTEUR.'</a>',
							     '<a href='.$lien_passer_admin->getURL().'>'.BAZ_DROIT_ADMIN.'</a>'));

				}
			}

			$table->altRowAttributes(1, array('class' => 'ligne_impaire'), array('class' => 'ligne_paire'));
			$table->updateColAttributes(0, array('align' => 'left'));
			$table->updateColAttributes(1, array('align' => 'left'));
			$table->updateColAttributes(2, array('align' => 'left'));
			$table->updateColAttributes(3, array('align' => 'left'));
			$res.=$table->toHTML() ;
		}
	}

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('pers');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('droits');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('idtypeannonce');

	return $res;
}

/** baz_formulaire() - Renvoie le menu pour les saisies et modification des annonces
*
* @param   string choix du formulaire a afficher (soit formulaire personnalise de
* 			l'annonce, soit choix du type d'annonce)
*
* @return   string  HTML
*/
function baz_formulaire($mode) {
	$res = '';
	if ( ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth()) || (BAZ_SANS_AUTH==true) ) {
       	$lien_formulaire=$GLOBALS['_BAZAR_']['url'];

		//Definir le lien du formulaire en fonction du mode de formulaire choisi
		if ($mode == BAZ_DEPOSER_ANNONCE) {
			$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU);
			if (isset($GLOBALS['_BAZAR_']['id_typeannonce']) && $GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') {
				$mode = BAZ_ACTION_NOUVEAU ;
			}
		}
		if ($mode == BAZ_ACTION_NOUVEAU) {
			if ((!isset($_POST['accept_condition']))and($GLOBALS['_BAZAR_']['condition']!=NULL)) {
				$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU);
			} else {
				$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU_V);
			}
		}
		if ($mode == BAZ_ACTION_MODIFIER) {
			if (!isset($_POST['accept_condition'])and($GLOBALS['_BAZAR_']['condition']!=NULL)) {
				$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
			} else {
				$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER_V);
				if (isset ($_SESSION['_BAZAR_']['fichier'])) unset($_SESSION['_BAZAR_']['fichier']) ;
				if (isset ($_SESSION['_BAZAR_']['image'])) unset($_SESSION['_BAZAR_']['image']);
				if (isset ($_SESSION['_BAZAR_']['lien'])) unset($_SESSION['_BAZAR_']['lien']);
			}
			$lien_formulaire->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		}
		if ($mode == BAZ_ACTION_MODIFIER_V) {
			$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER_V);
			$lien_formulaire->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		}

		//contruction du squelette du formulaire
		$formtemplate = new HTML_QuickForm('formulaire', 'post', preg_replace ('/&amp;/', '&', $lien_formulaire->getURL()) );
		$squelette =& $formtemplate->defaultRenderer();
   		$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'<table style="border:0;width:100%;">'."\n".'{content}'."\n".'</table>'."\n".'</form>'."\n");
    	$squelette->setElementTemplate( '<tr>'."\n".'<td>'."\n".'{label}'.
    		                        '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
    								' </td>'."\n".'<td style="text-align:left;padding:5px;"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</td>'."\n".'</tr>'."\n");
 	  	$squelette->setElementTemplate( '<tr>'."\n".'<td colspan="2" class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
                                    '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</td>'."\n".'</tr>'."\n", 'accept_condition');
  	  	$squelette->setElementTemplate( '<tr><td colspan="2" class="bouton">{label}{element}</td></tr>'."\n", 'valider');

 	   	$squelette->setRequiredNoteTemplate("\n".'<tr>'."\n".'<td colspan="2" class="symbole_obligatoire">* {requiredNote}</td></tr>'."\n");
		//Traduction de champs requis
		$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
		$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);

		//------------------------------------------------------------------------------------------------
		//AFFICHAGE DU FORMULAIRE GENERAL DE CHOIX DU TYPE D'ANNONCE
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_DEPOSER_ANNONCE) {
			$res = '';
			if (BAZ_UTILISE_TEMPLATE) {
				require_once BAZ_CHEMIN_APPLI.'bibliotheque/bazarTemplate.class.php';
	        	$modele = new bazarTemplate($GLOBALS['_BAZAR_']['db']);
	        	$res .= $modele->getTemplate(BAZ_TEMPLATE_FORMULAIRE_ACCUEIL, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['categorie_nature']);
			} else {
				$res.='<h2>'.BAZ_DEPOSE_UNE_NOUVELLE_ANNONCE.'</h2>'."\n";
			}
			
			//requete pour obtenir le nom et la description des types d'annonce
			$requete = 'SELECT * FROM bazar_nature WHERE bn_type_fiche IN ('.$GLOBALS['_BAZAR_']['categorie_nature'].') ';
			if (isset($GLOBALS['_BAZAR_']['langue'])) {
				$requete .= 'AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
			}
			$requete .= 'ORDER BY bn_label_nature ASC';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			if ($resultat->numRows()==1) {
				$ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC);
				$GLOBALS['_BAZAR_']['id_typeannonce']=$ligne['bn_id_nature'];
				$GLOBALS['_BAZAR_']['typeannonce']=$ligne['bn_label_nature'];
				$GLOBALS['_BAZAR_']['condition']=$ligne['bn_condition'];
    			$GLOBALS['_BAZAR_']['template']=$ligne['bn_template'];
				$GLOBALS['_BAZAR_']['commentaire']=$ligne['bn_commentaire'];
				$GLOBALS['_BAZAR_']['appropriation']=$ligne['bn_appropriation'];
				$GLOBALS['_BAZAR_']['image_titre']=$ligne['bn_image_titre'];
				$GLOBALS['_BAZAR_']['image_logo']=$ligne['bn_image_logo'];
				$mode = BAZ_ACTION_NOUVEAU;
				$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU_V);
			} else {
				if (!BAZ_SANS_AUTH) {
					$utilisateur = new Administrateur_bazar($GLOBALS['AUTH']);
				}
				while ($ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC)) {				
					if ( (!BAZ_SANS_AUTH && (($utilisateur->isRedacteur($ligne['bn_id_nature'])) || ($utilisateur->isAdmin($ligne['bn_id_nature']))
					|| ($utilisateur->isSuperAdmin() || !BAZ_RESTREINDRE_DEPOT) ) ) || BAZ_SANS_AUTH==true ) {
						if ($ligne['bn_image_titre']!='') {
							$titre='&nbsp;<img src="client/bazar/images/'.$ligne['bn_image_titre'].'" alt="'.
											$ligne['bn_label_nature'].'" />'."\n";
						} else {
							$titre='<span class="BAZ_titre_liste">'.$ligne['bn_label_nature'].' : </span>'."\n";
						}
						$formtemplate->addElement('radio', 'typeannonce', '',$titre.$ligne['bn_description'].'<br /><br />'."\n",
								$ligne['bn_id_nature'], array("id" => 'select'.$ligne['bn_id_nature']));
					}
				}
				
				if (BAZ_UTILISE_TEMPLATE) {
					$squelette->setElementTemplate( '<div class="listechoix">'."\n".'{element}'."\n".'</div>'."\n");
				} else {
					$res .= '<br />'.BAZ_CHOIX_TYPEANNONCE.'<br /><br />'."\n";
				}
				
				// Bouton d annulation
				$lien_formulaire->removeQueryString(BAZ_VARIABLE_ACTION);
				$lien_formulaire->removeQueryString(BAZ_VARIABLE_VOIR);
				$lien_formulaire->removeQueryString('typeannonce');
				$lien_formulaire->removeQueryString('id_fiche');
				$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER,
                            preg_replace ("/&amp;/", "&", $lien_formulaire->getURL()), BAZ_ANNULER); // Le preg_replace contourne un pb de QuickForm et Net_URL
                                                                                                            // qui remplacent deux fois les & par des &amp;
				//Bouton de validation du formulaire                                                                                                            // ce qui fait �chouer le lien
        		$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER);
        		$formtemplate->addGroup($buttons, null, null, '&nbsp;');

				//Affichage a l'ecran
				$res .= $formtemplate->toHTML()."\n";
			}
		}

		//------------------------------------------------------------------------------------------------
		//AFFICHAGE DU FORMULAIRE CORRESPONDANT AU TYPE DE L'ANNONCE CHOISI PAR L'UTILISATEUR
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_ACTION_NOUVEAU) {
			$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU_V);
			$formtemplate->updateAttributes(array(BAZ_VARIABLE_ACTION => str_replace('&amp;', '&', $lien_formulaire->getURL())));
			// Appel du modele
			if (BAZ_UTILISE_TEMPLATE) {				
				require_once BAZ_CHEMIN_APPLI.'bibliotheque/bazarTemplate.class.php';
	        	$modele = new bazarTemplate($GLOBALS['_BAZAR_']['db']);
	        	$html = $modele->getTemplate(BAZ_TEMPLATE_FORMULAIRE, $GLOBALS['_BAZAR_']['langue'],$GLOBALS['_BAZAR_']['categorie_nature']);
	        	if (!PEAR::isError($html)) {
					$res = str_replace ('{FORMULAIRE}', baz_afficher_formulaire_fiche('insertion',$formtemplate), $html);
	        	}
			} else {
	        	$res = baz_afficher_formulaire_fiche('insertion',$formtemplate);
	        }
		}

		//------------------------------------------------------------------------------------------------
		//CAS DE LA MODIFICATION D'UNE ANNONCE (FORMULAIRE DE MODIFICATION)
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_ACTION_MODIFIER) {
			$res=baz_afficher_formulaire_fiche('modification',$formtemplate);
		}

		//------------------------------------------------------------------------------------------------
		//CAS DE L'INSCRIPTION D'UNE ANNONCE
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_ACTION_NOUVEAU_V) {
			if ($formtemplate->validate()) {
				$formtemplate->process('baz_insertion', false) ;
				// Redirection vers mes_fiches pour eviter la revalidation du formulaire
				$GLOBALS['_BAZAR_']['url']->addQueryString ('message', 'ajout_ok') ;
				$GLOBALS['_BAZAR_']['url']->removeQueryString (BAZ_VARIABLE_VOIR) ;
				header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
				exit;
			}
		}

		//------------------------------------------------------------------------------------------------
		//CAS DE LA MODIFICATION D'UNE ANNONCE (VALIDATION ET MAJ)
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_ACTION_MODIFIER_V) {
			if ($formtemplate->validate()) {
				$formtemplate->process('baz_mise_a_jour', false) ;
				// Redirection vers mes_fiches pour eviter la revalidation du formulaire
				$GLOBALS['_BAZAR_']['url']->addQueryString ('message', 'modif_ok') ;
				$GLOBALS['_BAZAR_']['url']->removeQueryString (BAZ_VARIABLE_VOIR) ;
				header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
				exit;
			}
		}
    }
	else {

        require_once 'tools/bazar/actions/bazar/bibliotheque/bazarTemplate.class.php';
        $modele = new bazarTemplate($GLOBALS['_BAZAR_']['db']);
        $res .= $modele->getTemplate(BAZ_TEMPLATE_MESSAGE_LOGIN, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['categorie_nature']);
	}

	return $res;
}

/** baz_afficher_formulaire_fiche() - Genere le formulaire de saisie d'une annonce
*
* @param   string type de formulaire: insertion ou modification
* @param   mixed objet quickform du formulaire
*
* @return   string  code HTML avec formulaire
*/
function baz_afficher_formulaire_fiche($mode='insertion',$formtemplate) {
	//cas de la modification d'une fiche
	if ($mode=='modification') {
		//initialisation de la variable globale id_fiche
		$GLOBALS['_BAZAR_']['id_fiche'] = $_REQUEST['id_fiche'];

		//suppression eventuelle d'une url, d'un fichier ou d'une image
		if (isset($_GET['id_url'])) {
			baz_suppression_url($_GET['id_url']);
		}
		if (isset($_GET['id_fichier'])) {
			baz_suppression_fichier($_GET['id_fichier']);
		}
		if (isset($_GET['image'])) {
			baz_suppression_image($GLOBALS['_BAZAR_']['id_fiche']);
		}
	}
	$res = '';
	//titre de la rubrique
	if (!BAZ_UTILISE_TEMPLATE) $res .= '<h2>'.BAZ_TITRE_SAISIE_ANNONCE.'&nbsp;'.$GLOBALS['_BAZAR_']['typeannonce'].'</h2><br />'."\n";
	
	//si le type de formulaire requiert une acceptation des conditions on affiche les conditions
	if (($GLOBALS['_BAZAR_']['condition']!='')AND(!isset($_POST['accept_condition']))AND(!isset($_GET['url'])OR(!isset($_GET['fichier']))OR(!isset($_GET['image'])))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, $_GET[BAZ_VARIABLE_ACTION]);
		$formtemplate->updateAttributes(array(BAZ_VARIABLE_ACTION => str_replace('&amp;', '&', $GLOBALS['_BAZAR_']['url']->getURL())));
		require_once 'HTML/QuickForm/html.php';
		$conditions= new HTML_QuickForm_html('<tr><td colspan="2">'.$GLOBALS['_BAZAR_']['condition'].'</td>'."\n".'</tr>'."\n");
		$formtemplate->addElement($conditions);
		$formtemplate->addElement('checkbox', 'accept_condition',BAZ_ACCEPTE_CONDITIONS) ;
		$formtemplate->addElement('hidden', 'typeannonce', $GLOBALS['_BAZAR_']['id_typeannonce']);
		$formtemplate->addRule('accept_condition', BAZ_ACCEPTE_CONDITIONS_REQUIS, 'required', '', 'client') ;
		$formtemplate->addElement('submit', 'valider', BAZ_VALIDER);
	}
	//affichage du formulaire si conditions acceptees
	else {
		//Parcours du fichier de templates, pour mettre les valeurs des champs
		$tableau=baz_valeurs_template($GLOBALS['_BAZAR_']['template']);
		if ($mode=='modification') {
			//Ajout des valeurs par defaut
			$valeurs_par_defaut = baz_valeurs_fiche($GLOBALS['_BAZAR_']['id_fiche']) ;

			for ($i=0; $i<count($tableau); $i++) {
				if ( $tableau[$i]['type']=='liste' || $tableau[$i]['type']=='checkbox') {
					$def=$tableau[$i]['type'].$tableau[$i]['nom_bdd'];
				}
				elseif ( $tableau[$i]['type']=='texte' || $tableau[$i]['type']=='textelong' || $tableau[$i]['type']=='listedatedeb'
							|| $tableau[$i]['type']=='listedatefin' || $tableau[$i]['type']=='champs_cache'
							|| $tableau[$i]['type']=='labelhtml' ) {
					$def=$tableau[$i]['nom_bdd'];
				} elseif ($tableau[$i]['type']=='carte_google') {
					$def = 'carte_google';
					$valeurs_par_defaut[$def] = array ('latitude' => $valeurs_par_defaut['bf_latitude'], 'longitude' => $valeurs_par_defaut['bf_longitude']);
				}
				// certain type n ont pas de valeur par defaut (labelhtml par exemple)
				// on teste l existence de $valeur_par_defaut[$def] avant de le passer en parametre
				$tableau[$i]['type']($formtemplate, $tableau[$i]['nom_bdd'], $tableau[$i]['label'], $tableau[$i]['limite1'],
			                         $tableau[$i]['limite2'],
			                         isset ($valeurs_par_defaut[$def]) ? $valeurs_par_defaut[$def] : '',
			                         $tableau[$i]['table_source'], $tableau[$i]['obligatoire']) ;
				if ($tableau[$i]['type']=='carte_google') {
					require_once 'formulaire/formulaire.fonct.google.php';    				
				}
			}
		}
		else {
			for ($i=0; $i<count($tableau); $i++) {
				$tableau[$i]['type']($formtemplate, $tableau[$i]['nom_bdd'], $tableau[$i]['label'], $tableau[$i]['limite1'],
			                         $tableau[$i]['limite2'], $tableau[$i]['defaut'], $tableau[$i]['table_source'], $tableau[$i]['obligatoire']) ;
			    if ($tableau[$i]['type'] == 'carte_google') {
			    	require_once 'formulaire/formulaire.fonct.google.php';
			    }
			 }
		}
		$formtemplate->addElement('hidden', 'typeannonce', $GLOBALS['_BAZAR_']['id_typeannonce']);
	
		// Nettoyage de l'url avant les return
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
		$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
		$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
 		$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER, preg_replace("/&amp;/", "&", $GLOBALS['_BAZAR_']['url']->getURL()), BAZ_ANNULER); // Le preg_replace contourne un pb de QuickForm et Net_URL
                                                                                                    // qui remplacent deux fois les & par des &amp;
		//Bouton de validation du formulaire                                                                                                            // ce qui fait �chouer le lien
		$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER);
		$formtemplate->addGroup($buttons, null, null, '&nbsp;');

	}

	//Affichage a l'ecran
	$res .= $formtemplate->toHTML()."\n";
	return $res;
}


/** requete_bazar_fiche() - preparer la requete d'insertion ou de MAJ de la table bazar_fiche a partir du template
*
* @global   mixed L'objet contenant les valeurs issues de la saisie du formulaire
* @return   void
*/
function requete_bazar_fiche($valeur) {
	$requete=NULL;
	//l'annonce est directement publi�e pour les admins
	if (!BAZ_SANS_AUTH) $utilisateur = new Administrateur_bazar($GLOBALS['AUTH']);
	if (!BAZ_SANS_AUTH && ( $utilisateur->isAdmin( $GLOBALS['_BAZAR_']['id_typeannonce']) ||
	    $utilisateur->isSuperAdmin() ) ) {
		$requete.='bf_statut_fiche=1, ';
	}
	//sinon on met la constante du fichier de configuration 
	else {
		$requete.='bf_statut_fiche="'.BAZ_ETAT_VALIDATION.'", ';
	}
	$tableau=baz_valeurs_template($GLOBALS['_BAZAR_']['template']);
	for ($i=0; $i<count($tableau); $i++) {
		//cas des checkbox et des listes
		if ($tableau[$i]['type']=='checkbox' || $tableau[$i]['type']=='liste') {
			//on supprime les anciennes valeurs de la table bazar_fiche_valeur_liste
			$requetesuppression='DELETE FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND bfvl_ce_liste='.$tableau[$i]['nom_bdd'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requetesuppression) ;
			if (DB::isError($resultat)) {
					die ($resultat->getMessage().$resultat->getDebugInfo()) ;
				}
			if (isset($valeur[$tableau[$i]['type'].$tableau[$i]['nom_bdd']]) && ($valeur[$tableau[$i]['type'].$tableau[$i]['nom_bdd']]!=0)) {
				//on insere les nouvelles valeurs
				$requeteinsertion='INSERT INTO bazar_fiche_valeur_liste (bfvl_ce_fiche, bfvl_ce_liste, bfvl_valeur) VALUES ';
                //pour les checkbox, les diff�rentes valeurs sont dans un tableau
                if (is_array($valeur[$tableau[$i]['type'].$tableau[$i]['nom_bdd']])) {
                	$nb=0;
                	while (list($cle, $val) = each($valeur[$tableau[$i]['type'].$tableau[$i]['nom_bdd']])) {

                		if ($nb>0) $requeteinsertion .= ', ';
                		$requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', '.$tableau[$i]['nom_bdd'].', '.$cle.') ';
                		$nb++;
                	}
                }
                //pour les listes, une insertion de la valeur suffit
                else {
                	$requeteinsertion .= '('.$GLOBALS['_BAZAR_']['id_fiche'].', '.$tableau[$i]['nom_bdd'].', '.$valeur[$tableau[$i]['type'].$tableau[$i]['nom_bdd']].')';
                }
                $resultat = $GLOBALS['_BAZAR_']['db']->query($requeteinsertion) ;
				if (DB::isError($resultat)) {
					die ($resultat->getMessage().$resultat->getDebugInfo()) ;
				}
			}
		}
		//cas des fichiers
		elseif ($tableau[$i]['type']=='fichier') {
			if (isset($valeur['texte_fichier'.$tableau[$i]['nom_bdd']]) && $valeur['texte_fichier'.$tableau[$i]['nom_bdd']]!='') {
				baz_insertion_fichier($valeur['texte_fichier'.$tableau[$i]['nom_bdd']], $GLOBALS['_BAZAR_']['id_fiche'], 'fichier'.$tableau[$i]['nom_bdd']);
			}
		}
		//cas des urls
		// On affine les criteres pour l insertion d une url
		// il faut que le lien soit saisie, different de http:// ET que le texte du lien soit saisie aussi
		// et ce afin d eviter d avoir des liens vides
		elseif ($tableau[$i]['type']=='url') {
			if (isset($valeur['url_lien'.$tableau[$i]['nom_bdd']]) &&
						$valeur['url_lien'.$tableau[$i]['nom_bdd']]!='http://'
						&& isset($valeur['url_texte'.$tableau[$i]['nom_bdd']]) &&
						strlen ($valeur['url_texte'.$tableau[$i]['nom_bdd']])) {
				baz_insertion_url($valeur['url_lien'.$tableau[$i]['nom_bdd']], $valeur['url_texte'.$tableau[$i]['nom_bdd']], $GLOBALS['_BAZAR_']['id_fiche']);
			}
		}
		//cas des images
		elseif ($tableau[$i]['type']=='image') {
			if (isset($_FILES['image']['name']) && $_FILES['image']['name']!='') {
				$requete .= baz_insertion_image($GLOBALS['_BAZAR_']['id_fiche']);
			}
		}
		//cas des dates
		elseif ( $tableau[$i]['type']=='listedatedeb' || $tableau[$i]['type']=='listedatefin' ) {

			// On construit la date selon le format YYYY-mm-dd
			$date = $valeur[$tableau[$i]['nom_bdd']]['Y'].'-'.$valeur[$tableau[$i]['nom_bdd']]['m'].'-'.$valeur[$tableau[$i]['nom_bdd']]['d'] ;

			// si la date de fin evenement est anterieure a la date de debut, on met la date de debut
			// pour eviter les incoherence

			if ($tableau[$i]['nom_bdd'] == 'bf_date_fin_evenement' &&
					mktime(0,0,0, $valeur['bf_date_debut_evenement']['m'], $valeur['bf_date_debut_evenement']['d'], $valeur['bf_date_debut_evenement']['Y']) >
					mktime(0,0,0, $valeur['bf_date_fin_evenement']['m'], $valeur['bf_date_fin_evenement']['d'], $valeur['bf_date_fin_evenement']['Y'])) {
				$val = $valeur['bf_date_debut_evenement']['Y'].'-'.$valeur['bf_date_debut_evenement']['m'].'-'.$valeur['bf_date_debut_evenement']['d'] ;
			} else {
				$val = $valeur[$tableau[$i]['nom_bdd']]['Y'].'-'.$valeur[$tableau[$i]['nom_bdd']]['m'].'-'.$valeur[$tableau[$i]['nom_bdd']]['d'] ;
			}
			$requete .= $tableau[$i]['nom_bdd'].'="'.$val.'", ' ;
		}
		//cas des champs texte
		elseif ( $tableau[$i]['type']=='texte' || $tableau[$i]['type']=='textelong' || $tableau[$i]['type']=='champs_cache' ) {
			//on mets les slashes pour les saisies dans les champs texte et textearea
			$val=addslashes($valeur[$tableau[$i]['nom_bdd']]) ;
			$requete .= $tableau[$i]['nom_bdd'].'="'.$val.'", ' ;
		}
		//cas des wikinis
		elseif ( $tableau[$i]['type']=='wikini' && $_REQUEST[BAZ_VARIABLE_ACTION]==BAZ_ACTION_NOUVEAU_V ) {
			//on appelle les pages des apis et de l'integrateur wikini
			require_once 'bazar.fonct.wikini.php' ;
			//generation du titre du wiki, sous la forme id-titre du projet
			$titre=baz_titre_wiki($valeur["bf_titre"]);
			//cr�ation du wiki
			$valeur=array ("action"=> "nouveau_v", "code_alpha_wikini"=>$titre, "page"=>"AccueiL", "bdd_hote"=> "",
			        "bdd_nom"=> "", "bdd_utilisateur"=> "", "bdd_mdp" => "", "table_prefix"=> "", "chemin" => "wikini/".$titre, "valider"=> "Valider");
			$val = insertion($valeur, $GLOBALS['_BAZAR_']['db']);
		}
		// Cas de la carte google
		elseif ($tableau[$i]['type'] == 'carte_google') {
			$requete .= 'bf_latitude="'.$valeur['latitude'].'", bf_longitude="'.$valeur['longitude'].'",';
		}
	}
	$requete.=' bf_date_maj_fiche=NOW()';
	return $requete;
}

/** baz_insertion() - inserer une nouvelle fiche
*
* @array   Le tableau des valeurs a inserer
* @integer Valeur de l'identifiant de la fiche
* @return   void
*/
function baz_insertion($valeur) {
        // ===========  Insertion d'une nouvelle fiche ===================
        //requete d'insertion dans bazar_fiche
        $GLOBALS['_BAZAR_']['id_fiche'] = baz_nextid('bazar_fiche', 'bf_id_fiche', $GLOBALS['_BAZAR_']['db']) ;
        $requete = 'INSERT INTO bazar_fiche SET bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].', ';
		if (!BAZ_SANS_AUTH) $requete .= 'bf_ce_utilisateur='.$GLOBALS['id_user'].', ';
		$requete .= 'bf_ce_nature='.$GLOBALS['_BAZAR_']['id_typeannonce'].', '.
		   'bf_date_creation_fiche=NOW(), ';
		if (!isset($_REQUEST['bf_date_debut_validite_fiche'])) {
			$requete .= 'bf_date_debut_validite_fiche=now(), bf_date_fin_validite_fiche="0000-00-00", ' ;
		}
		$requete .=requete_bazar_fiche(&$valeur) ;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		// Envoie d un mail aux administrateurs
		if (!BAZ_SANS_AUTH) {
			$utilisateur = new Administrateur_bazar($GLOBALS['AUTH']);
			if ($utilisateur->isRedacteur($GLOBALS['_BAZAR_']['id_typeannonce'])) {
				$mails = bazar::getMailAdmin($GLOBALS['_BAZAR_']['id_typeannonce']);
				require_once 'tools/bazar/actions/bazar/bibliotheque/bazarTemplate.class.php';
				$template = new bazarTemplate($GLOBALS['_BAZAR_']['db']);
				$sujet = $template->getTemplate(BAZ_TEMPLATE_MAIL_NOUVELLE_FICHE_SUJET, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['id_typeannonce']);
				$corps = $template->getTemplate(BAZ_TEMPLATE_MAIL_NOUVELLE_FICHE_CORPS, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['id_typeannonce']);
				if (is_array ($mails)) {
					foreach ($mails as $mail) {
						mail ($mail, $sujet, $corps);
					}
				}
			}	
		}
				
		//on nettoie l'url, on retourne � la consultation des fiches
		$GLOBALS['_BAZAR_']['url']->addQueryString('message', 'ajout_ok') ;
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
		header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
		exit;
		return ;
}


/** baz_insertion_url() - inserer un lien URL a une fiche
*
* @global   string L'url du lien
* @global   string Le texte du lien
* @global   integer L'identifiant de la fiche
* @return   void
*/
function baz_insertion_url($url_lien, $url_texte, $idfiche) {
	//requete d'insertion dans bazar_url
	if (!isset($_SESSION['_BAZAR_']['lien'])) {
		$id_url = baz_nextId('bazar_url', 'bu_id_url', $GLOBALS['_BAZAR_']['db']) ;
		$requete = 'INSERT INTO bazar_url SET bu_id_url='.$id_url.', bu_ce_fiche='.$idfiche.', '.
			   'bu_url="'.$url_lien.'", bu_descriptif_url="'.addslashes($url_texte).'"';

		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	    if (DB::isError($resultat)) {
	        return $resultat->getMessage().$resultat->getDebugInfo() ;
	    }
	    $_SESSION['_BAZAR_']['lien'] = 1;
	    return;
	}
}


/** baz_insertion_fichier() - inserer un fichier a une fiche
*
* @global   string Le label du fichier
* @global   string La description du fichier
* @global   integer L'identifiant de la fiche
* @return   void
*/
function baz_insertion_fichier($fichier_description, $idfiche, $nom_fichier='fichier_joint') {
	//verification de la presence de ce fichier
	$requete = 'SELECT bfj_id_fichier FROM bazar_fichier_joint WHERE bfj_fichier="'.$_FILES[$nom_fichier]['name'].'"';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
    if (DB::isError($resultat)) {
        die ($resultat->getMessage().$resultat->getDebugInfo()) ;
    }
	if (!isset ($_SESSION['_BAZAR_']['fichier'])) {
		if ($resultat->numRows()==0) {
			$chemin_destination=BAZ_CHEMIN_APPLI.'upload/'.$_FILES[$nom_fichier]['name'];
			move_uploaded_file($_FILES[$nom_fichier]['tmp_name'], $chemin_destination);
			chmod ($chemin_destination, 0755);
		}
		$id_fichier_joint = baz_nextId('bazar_fichier_joint', 'bfj_id_fichier', $GLOBALS['_BAZAR_']['db']) ;
		$requete = 'INSERT INTO bazar_fichier_joint SET bfj_id_fichier='.$id_fichier_joint.', bfj_ce_fiche='.$idfiche.
		           ', bfj_description="'.addslashes($fichier_description).'", bfj_fichier="'.$_FILES[$nom_fichier]['name'].'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
        if (DB::isError($resultat)) {
            return $resultat->getMessage().$resultat->getDebugInfo() ;
        }
	}
    $_SESSION['_BAZAR_']['fichier'] = 1;
	return;
}


/** baz_insertion_image() - inserer une image a une fiche
*
* @global   integer L'identifiant de la fiche
* @return   string requete SQL
*/
function baz_insertion_image($idfiche) {
	//verification de la presence de ce fichier
	$requete = 'SELECT bf_id_fiche FROM bazar_fiche WHERE bf_url_image="'.$_FILES['image']['name'].'" AND bf_id_fiche!='.$idfiche;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
        if (DB::isError($resultat)) {
            die ($resultat->getMessage().$resultat->getDebugInfo()) ;
        }
	if ($resultat->numRows()==0) {
		$chemin_destination=BAZ_CHEMIN_APPLI.'upload/'.$_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'], $chemin_destination);
		chmod ($chemin_destination, 0755);
	}
	$_SESSION['_BAZAR_']['image'] = 1;
	return 'bf_url_image="'.$_FILES['image']['name'].'", ' ;
}


/** baz_mise_a_jour() - Mettre a jour une fiche
*
* @global   Le contenu du formulaire de saisie de l'annonce
* @return   void
*/
function baz_mise_a_jour($valeur) {
	//MAJ de bazar_fiche
	$requete = 'UPDATE bazar_fiche SET '.requete_bazar_fiche(&$valeur,$GLOBALS['_BAZAR_']['id_typeannonce']);
	$requete.= ' WHERE bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	return;
}


/** baz_suppression() - Supprime une fiche
*
* @global   L'identifiant de la fiche a supprimer
* @return   void
*/
function baz_suppression() {
	$valeurs=baz_valeurs_fiche($_GET['id_fiche']);

	//suppression des wikinis associes
	//generation du titre du wiki, sous la forme id-titre du projet
	//$titre=baz_titre_wiki($valeurs["bf_titre"]);
	//$requete = 'SELECT bw_id_wikini FROM bazar_wikini WHERE bw_code_alpha_wikini = "'.$titre.'"';
	//$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	//if ($resultat->numRows()>0) {
	//	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
	//		$id_wikini=$ligne['bw_id_wikini'];
	//	}
	//	include_once 'bazar.fonct.wikini.php' ;
	//	adwi_supprimer_wikini($id_wikini, $GLOBALS['_BAZAR_']['db']);
	//}

	// suppression des valeurs des listes et des cases � cocher
	$requete = 'DELETE FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$_GET['id_fiche'];
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}

	//suppression des urls associes
	$requete = 'DELETE FROM bazar_url WHERE bu_ce_fiche = '.$_GET['id_fiche'];
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
	}

	//suppression des fichiers associes
	$requete = 'SELECT bfj_id_fichier FROM bazar_fichier_joint WHERE bfj_ce_fiche = '.$_GET['id_fiche'];
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
	}
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		baz_suppression_fichier($ligne['bfj_id_fichier']);
	}

	//suppression dans bazar_fiche
	$requete = 'DELETE FROM bazar_fiche WHERE bf_id_fiche = '.$_GET['id_fiche'];
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
	}
	
	//on nettoie l'url, on retourne � la consultation des fiches
	$GLOBALS['_BAZAR_']['url']->addQueryString ('message', 'delete_ok') ;
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
	$GLOBALS['_BAZAR_']['url']->removeQueryString (BAZ_VARIABLE_VOIR) ;
	$GLOBALS['_BAZAR_']['url']->removeQueryString ('id_fiche') ;
	header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
	exit;
	
	return ;
}


/** baz_suppression_url() - Supprimer un lien d'une fiche
*
* @global   integer L'identifiant du lien
* @return   void
*/
function baz_suppression_url($id_url) {
	//suppression dans bazar_url
	$requete = 'DELETE FROM bazar_url WHERE bu_id_url = '.$id_url;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	return;
}


/** baz_suppression_fichier() - Supprimer un fichier d'une fiche
*
* @global   integer L'identifiant du fichier
* @return   void
*/
function baz_suppression_fichier($id_fichier) {
	//verification de l'utilisation du fichier joint pour une autre annonce
	$requete = 'SELECT bfj_fichier FROM bazar_fichier_joint WHERE bfj_id_fichier='.$id_fichier;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
	$requete = 'SELECT bfj_fichier FROM bazar_fichier_joint WHERE bfj_fichier="'.$ligne['bfj_fichier'].'"';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	//si le fichier n'est que utilise dans cette fiche, on le supprime, on le laisse sinon
	if ($resultat->numRows()==1) {
		$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
		unlink(BAZ_CHEMIN_APPLI.'upload/'.$ligne['bfj_fichier']);
	}

	//suppression dans la table bazar_fichier
	$requete = 'DELETE FROM bazar_fichier_joint WHERE bfj_id_fichier = '.$id_fichier;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	return;
}


/** baz_suppression_image() - Supprimer une image d'une fiche
*
* @global   integer L'identifiant de la fiche
* @return   void
*/
function baz_suppression_image($id_fiche) {
	//verification de l'utilisation de l'image pour une autre annonce
	$requete = 'SELECT bf_url_image FROM bazar_fiche WHERE bf_id_fiche='.$id_fiche;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
	$requete = 'SELECT bf_url_image FROM bazar_fiche WHERE bf_url_image="'.$ligne['bf_url_image'].'"';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	//si le fichier n'est que utilise dans cette fiche, on le supprime, on le laisse sinon
	if ($resultat->numRows()==1) {
		$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
		unlink(BAZ_CHEMIN_APPLI.'upload/'.$ligne['bf_url_image']);
	}

	//suppression dans la table bazar_fiche
	$requete = 'UPDATE bazar_fiche SET bf_url_image=NULL WHERE bf_id_fiche = '.$id_fiche;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	return;
}


/** publier_fiche () - Publie ou non dans les fichiers XML la fiche bazar d'un utilisateur
*
* @global boolean Valide: oui ou non
* @return   void
*/
function publier_fiche($valid) {
	if (isset($_GET['id_fiche'])) $GLOBALS['_BAZAR_']['id_fiche']=$_GET['id_fiche'];
	if (isset($_GET['typeannonce'])) $typeannonce=$_GET['typeannonce'];
	if ($valid==0) {
		$requete = 'UPDATE bazar_fiche SET  bf_statut_fiche=2 WHERE bf_id_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'"' ;
	}
	else {
		$requete = 'UPDATE bazar_fiche SET  bf_statut_fiche=1 WHERE bf_id_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'"' ;
	}

	// ====================Mise a jour de la table bazar_fiche====================
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	unset ($resultat) ;
	//TODO envoie mail annonceur
	return;
}


/** baz_s_inscrire() affiche le formulaire qui permet de s'inscrire pour recevoir des annonces d'un type
*
*   @return  string    le code HTML
*/
function baz_s_inscrire() {
	$res= '<h2>'.BAZ_S_INSCRIRE_AUX_ANNONCES.'</h2>'."\n";
	//requete pour obtenir l'id et le label des types d'annonces
	$requete = 'SELECT bn_id_nature, bn_label_nature '.
	           'FROM bazar_nature WHERE 1';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}

	// Nettoyage de l url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
	$liste='';
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$lien_RSS=$GLOBALS['_BAZAR_']['url'];
		$lien_RSS->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FLUX_RSS);
		$lien_RSS->addQueryString('annonce', $ligne[bn_id_nature]);
		$liste .= '<li><a href="'.$lien_RSS->getURL().'"><img src="tools/bazar/actions/bazar/images/BAZ_rss.png" alt="'.BAZ_RSS.'"></a>&nbsp;';
		$liste .= $ligne['bn_label_nature'];
		$liste .= '</li>'."\n";
		$lien_RSS->removeQueryString('annonce');
	}
	if ($liste!='') $res .= '<ul class="BAZ_liste_rss">'."\n".'<li><a href="'.$lien_RSS->getURL().'"><img src="tools/bazar/actions/bazar/images/BAZ_rss.png" alt="'.BAZ_RSS.'"></a>&nbsp;<strong>Flux RSS de toutes les fiches</strong></li>'."\n".$liste.'</ul>'."\n";
	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('idtypeannonce');
	return $res;
}


/** baz_formulaire_des_formulaires() retourne le formulaire de saisie des formulaires
*
*   @return  Object    le code HTML
*/
function baz_formulaire_des_formulaires($mode) {
	$GLOBALS['_BAZAR_']['url']->addQueryString('action_formulaire', $mode);
	//contruction du squelette du formulaire
	$formtemplate = new HTML_QuickForm('formulaire', 'post', preg_replace ('/&amp;/', '&', $GLOBALS['_BAZAR_']['url']->getURL()) );
	$GLOBALS['_BAZAR_']['url']->removeQueryString('action_formulaire');
	$squelette =& $formtemplate->defaultRenderer();
	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'<table style="border:0;width:100%;">'."\n".'{content}'."\n".'</table>'."\n".'</form>'."\n");
	$squelette->setElementTemplate( '<tr>'."\n".'<td style="width:150px;text-align:right;padding:5px;">'."\n".'{label}'.
		                        '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
								' </td>'."\n".'<td style="text-align:left;padding:5px;">'."\n".'{element}'."\n".
                                '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                '</td>'."\n".'</tr>'."\n");
  	$squelette->setElementTemplate( '<tr>'."\n".'<td colspan="2" class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
                                '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</td>'."\n".'</tr>'."\n", 'accept_condition');
  	$squelette->setElementTemplate( '<tr><td colspan="2" class="bouton">{label}{element}</td></tr>'."\n", 'valider');
   	$squelette->setRequiredNoteTemplate("\n".'<tr>'."\n".'<td colspan="2" class="symbole_obligatoire">* {requiredNote}</td></tr>'."\n");
	//traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);
	//champs du formulaire
	if (isset($_GET['idformulaire'])) $formtemplate->addElement('hidden', 'bn_id_nature', $_GET['idformulaire']);
	$formtemplate->addElement('text', 'bn_label_nature', BAZ_NOM_FORMULAIRE);
	$formtemplate->addElement('textarea', 'bn_description', BAZ_DESCRIPTION);
	$formtemplate->addElement('textarea', 'bn_condition', BAZ_CONDITION);
	$formtemplate->addElement('checkbox', 'bn_commentaire', BAZ_AUTORISER_COMMENTAIRE);
	$formtemplate->addElement('checkbox', 'bn_appropriation', BAZ_AUTORISER_APPROPRIATION);
	$formtemplate->addElement('text', 'bn_label_class', BAZ_NOM_CLASSE_CSS);
	$formtemplate->addElement('text', 'bn_type_fiche', BAZ_NOUVELLE_CATEGORIE_FORMULAIRE);
	$formtemplate->addElement('textarea', 'bn_template', BAZ_TEMPLATE, array('style'=>'height:300px; overflow:auto;'));
	//champs obligatoires
	$formtemplate->addRule('bn_label_nature', BAZ_CHAMPS_REQUIS.' : '.BAZ_FORMULAIRE, 'required', '', 'client');
	$formtemplate->addRule('bn_template', BAZ_CHAMPS_REQUIS.' : '.BAZ_TEMPLATE, 'required', '', 'client');	
	//bouton d'annulation
	$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER,
    preg_replace ("/&amp;/", "&", $GLOBALS['_BAZAR_']['url']->getURL()), BAZ_ANNULER);
	//bouton de validation
	$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER);
	$formtemplate->addGroup($buttons, null, null, '&nbsp;');	
	return $formtemplate;
}

/** baz_gestion_formulaire() affiche le listing des formulaires et permet de les modifier
*
*   @return  string    le code HTML
*/
function baz_gestion_formulaire() {
	$res= '<h2>'.BAZ_MODIFIER_FORMULAIRES.'</h2>'."\n";
	
	// il y a un formulaire � modifier
	if (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='modif') {
		//recuperation des informations du type de formulaire
		$requete = 'SELECT * FROM bazar_nature WHERE bn_id_nature='.$_GET['idformulaire'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;		
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
		$formulaire=baz_formulaire_des_formulaires('modif_v');
		$formulaire->setDefaults($ligne);
		$res .= $formulaire->toHTML();
		
	//il y a un nouveau formulaire a saisir
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='new') {
		$formulaire=baz_formulaire_des_formulaires('new_v');
		$res .= $formulaire->toHTML();	
	
	//il y a des donn�es pour ajouter un nouveau formulaire
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='new_v') {		
		$requete = 'INSERT INTO bazar_nature (`bn_id_nature` ,`bn_ce_i18n` ,`bn_label_nature` ,`bn_template` ,`bn_description` ,`bn_condition` ,`bn_commentaire` ,`bn_appropriation` ,`bn_label_class` ,`bn_type_fiche`)' .
				   ' VALUES ('.baz_nextId('bazar_nature', 'bn_id_nature', $GLOBALS['_BAZAR_']['db']).
                   ', "fr-FR", "'.$_POST["bn_label_nature"].'", "'.$_POST["bn_template"].
				   '", "'.$_POST["bn_description"].'", "'.$_POST["bn_condition"].
				   '", ';
		if (isset($_POST["bn_commentaire"])) $requete .='1';
		else $requete .='0';
		$requete .= ', ';				
		if (isset($_POST["bn_appropriation"])) $requete .='1';
		else $requete .='0';
		$requete .= ', "'.$_POST["bn_label_class"].'", "'.$_POST["bn_type_fiche"].'")';		
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;		
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$res .= '<p class="BAZ_info">'.BAZ_NOUVEAU_FORMULAIRE_ENREGISTRE.'</p>'."\n";
	
	//il y a des donn�es pour modifier un formulaire
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='modif_v') {
		$requete = 'UPDATE bazar_nature SET `bn_label_nature`="'.$_POST["bn_label_nature"].
				   '" ,`bn_template`="'.$_POST["bn_template"].
				   '" ,`bn_description`="'.$_POST["bn_description"].
				   '" ,`bn_condition`="'.$_POST["bn_condition"].
				   '" ,`bn_commentaire`=';
		if (isset($_POST["bn_commentaire"])) $requete .='1';
		else $requete .='0';
		$requete .= ' ,`bn_appropriation`=';
		if (isset($_POST["bn_appropriation"])) $requete .='1';
		else $requete .='0';
		$requete .= ' ,`bn_label_class`="'.$_POST["bn_label_class"].
				    '" ,`bn_type_fiche`="'.$_POST["bn_type_fiche"].'"'.
				    ' WHERE `bn_id_nature`='.$_POST["bn_id_nature"];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;		
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$res .= '<p class="BAZ_info">'.BAZ_FORMULAIRE_MODIFIE.'</p>'."\n";	
		
	// il y a un id de formulaire � supprimer
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='delete') {
		//suppression de l'entree dans bazar_nature
		$requete = 'DELETE FROM bazar_nature WHERE bn_id_nature='.$_GET['idformulaire'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;		
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		
		//suppression des fiches associees dans bazar_fiche
		$requete = 'DELETE FROM bazar_fiche WHERE bf_ce_nature='.$_GET['idformulaire'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;		
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		
		$res .= '<p class="BAZ_info">'.BAZ_FORMULAIRE_ET_FICHES_SUPPRIMES.'</p>'."\n";
	}
	
	// affichage de la liste des templates � modifier ou supprimer (on l'affiche dans tous les cas, sauf cas de modif de formulaire)
	if (!isset($_GET['action_formulaire']) || ($_GET['action_formulaire']!='modif' && $_GET['action_formulaire']!='new') ) {
		$res .= '<p class="BAZ_info">'.BAZ_INTRO_MODIFIER_FORMULAIRE.'</p>'."\n";
		 
		//requete pour obtenir l'id et le label des types d'annonces
		$requete = 'SELECT bn_id_nature, bn_label_nature, bn_type_fiche '.
		           'FROM bazar_nature WHERE 1 ORDER BY bn_type_fiche';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;		
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}		
		$liste=''; $type_formulaire='';
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			if ($type_formulaire!=$ligne['bn_type_fiche']) {
				if ($type_formulaire!='') $liste .= '</ul><br />'."\n";
				$liste .= '<h3>'.$ligne['bn_type_fiche'].'</h3>'."\n".'<ul class="BAZ_liste_formulaire">';
				$type_formulaire = $ligne['bn_type_fiche'];
			}			
			$lien_formulaire=$GLOBALS['_BAZAR_']['url'];		
			$liste .= '<li>';
			$lien_formulaire->addQueryString('action_formulaire', 'delete');
			$lien_formulaire->addQueryString('idformulaire', $ligne[bn_id_nature]);
			$liste .= '<a href="'.$lien_formulaire->getURL().'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FORMULAIRE.' ?\');">'.
                      '<img src="'.BAZ_CHEMIN_APPLI.'images/delete.gif" alt="'.BAZ_EFFACER.'"></a>'."\n";
			$lien_formulaire->removeQueryString('action_formulaire');
			$lien_formulaire->addQueryString('action_formulaire', 'modif');
			$liste .= '<a href="'.$lien_formulaire->getURL().'"><img src="'.BAZ_CHEMIN_APPLI.'images/modify.gif" alt="'.BAZ_MODIFIER.'">'.
					  '&nbsp;'.$ligne['bn_label_nature'].'</a>'."\n";
			$lien_formulaire->removeQueryString('action_formulaire');
			$lien_formulaire->removeQueryString('idformulaire');
		
			$liste .='</li>'."\n";			
		}
		if ($liste!='') $res .= $liste.'</ul><br />'."\n";
		
		//ajout du lien pour cr�er un nouveau formulaire
		$lien_formulaire->addQueryString('action_formulaire', 'new');
		$res .= '<a href="'.$lien_formulaire->getURL().'"><img src="'.BAZ_CHEMIN_APPLI.'images/new.gif" alt="new">'.
					  '&nbsp;'.BAZ_NOUVEAU_FORMULAIRE.'</a>'."\n";;
	}
	return $res;
}


/** baz_valeurs_fiche() - Renvoie un tableau avec les valeurs par defaut du formulaire d'inscription
*
* @param    integer Identifiant de la fiche
*
* @return   array   Valeurs enregistrees pour cette fiche
*/
function baz_valeurs_fiche($idfiche) {
	$requete = 'SELECT * FROM bazar_fiche WHERE bf_id_fiche='.$idfiche;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC) ;
	$valeurs_fiche = array() ;
	$tableau = baz_valeurs_template($GLOBALS['_BAZAR_']['template']);
	for ($i=0; $i<count($tableau); $i++) {
     	if ($tableau[$i]['type']=='liste' || $tableau[$i]['type']=='checkbox') {
     		$requete = 'SELECT bfvl_valeur FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$idfiche.
			' AND  bfvl_ce_liste='.$tableau[$i]['nom_bdd'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError ($resultat)) {
				die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
			}
			$nb=0;$val='';
            while ($result = $resultat->fetchRow()) {
            	if ($nb>0) $val .= ', ';
            	$val .= $result[0];
            	$nb++;
            }
     		$valeurs_fiche[$tableau[$i]['type'].$tableau[$i]['nom_bdd']] = $val;
     	}
     	elseif ($tableau[$i]['type']=='champs_cache' || $tableau[$i]['type']=='texte' || $tableau[$i]['type']=='textelong' || $tableau[$i]['type']=='listedatedeb' || $tableau[$i]['type']=='listedatefin') {
     		$valeurs_fiche[$tableau[$i]['nom_bdd']] = stripslashes($ligne[$tableau[$i]['nom_bdd']]);
     	} elseif ($tableau[$i]['type']=='carte_google') {
     		$valeurs_fiche['bf_latitude'] = $ligne['bf_latitude'];
     		$valeurs_fiche['bf_longitude'] = $ligne['bf_longitude'];
     	}
	}
	return $valeurs_fiche;
}

/** function baz_nextId () Renvoie le prochain identifiant numerique libre d'une table
*
*   @param  string  Nom de la table
*   @param  string  Nom du champs identifiant
*   @param  mixed   Objet DB de PEAR pour la connexion a la base de donnees
*
*   return  integer Le prochain numero d'identifiant disponible
*/
function baz_nextId($table, $colonne_identifiant, $bdd) {
	$requete = 'SELECT MAX('.$colonne_identifiant.') AS maxi FROM '.$table;
	$resultat = $bdd->query($requete) ;
	if (DB::isError($resultat)) {
		die (__FILE__ . __LINE__ . $resultat->getMessage() . $requete);
		return $bdd->raiseError($resultat) ;
	}

	if ($resultat->numRows() > 1) {
		return $bdd->raiseError('<br />La table '.$table.' a un identifiant non unique<br />') ;
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT) ;
	return $ligne->maxi + 1 ;
}

/** function baz_titre_wiki () Renvoie la chaine de caractere sous une forme compatible avec wikini
*
*   @param  string  mot � transformer (enlever accents, espaces)
*
*   return  string  mot transform�
*/
function baz_titre_wiki($nom) {
	$titre=trim($nom);
	for ($j = 0; $j < strlen ($titre); $j++) {
		if (!preg_match ('/[a-zA-Z0-9]/', $titre[$j])) {
			$titre[$j] = '_' ;
		}
	}
	return $titre;
}

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.fonct.php,v $
* Revision 1.1  2008/07/07 18:00:39  mrflos
* maj carto plus calendrier
*
* Revision 1.2  2008/03/06 00:15:40  mrflos
* correction des bugs bazar, ajout de fichiers d'images
*
* Revision 1.1  2008/02/18 09:12:47  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.74.2.7  2008-01-29 14:35:22  alexandre_tb
* suppression de l identification pour l abonnement au fluxRSS
*
* Revision 1.74.2.6  2008-01-29 09:55:07  alexandre_tb
* suppression de l identification pour l abonnement au fluxRSS
*
* Revision 1.74.2.5  2008-01-29 09:35:36  alexandre_tb
* remplacement des variables action par une constante
* Utilisation d un redirection pour eviter que les formulaires soient valides 2 fois
* simplification de la suppression d un lien associe a une liste
*
* Revision 1.74.2.4  2008-01-11 14:10:12  alexandre_tb
* Remplacement de la variable action ecrite en dur par la constante BAZ_VARIABLE_ACTION
*
* Revision 1.74.2.3  2007-12-14 09:55:05  alexandre_tb
* suppression de style dans le formulaire
*
* Revision 1.74.2.2  2007-12-06 15:36:07  alexandre_tb
* appel de la fonction GEN_AttributsBody dans le composant carte_google
*
* Revision 1.74.2.1  2007-12-04 09:00:08  alexandre_tb
* corrections importantes sur baz_s_inscrire, simplification de l'application qui ne fonctionnait pas.
*
* Revision 1.74  2007-10-25 09:41:31  alexandre_tb
* mise en place de variable de session pour eviter que les formulaires soit valider 2 fois, pour les url, fichiers et image
*
* Revision 1.73  2007-10-24 13:27:00  alexandre_tb
* bug : double saisie d url
* suppression de warning sur variable
*
* Revision 1.72  2007-10-22 10:09:21  florian
* correction template
*
* Revision 1.71  2007-10-22 09:18:39  alexandre_tb
* prise en compte de la langue dans les requetes sur bazar_nature
*
* Revision 1.70  2007-10-10 13:26:36  alexandre_tb
* utilisation de la classe Administrateur_bazar a la place de niveau_droit
* suppression de fonction niveau_droit
*
* Revision 1.69  2007-09-18 07:39:42  alexandre_tb
* correction d un bug lors d une insertion
*
* Revision 1.68  2007-08-27 12:31:31  alexandre_tb
* mise en place de modele
*
* Revision 1.67  2007-07-04 10:01:30  alexandre_tb
* mise en place de divers templates :
*  - mail pour admin (sujet et corps)
*  - modele carte_google
* ajout de lignes dans bazar_template
*
* Revision 1.66  2007-06-25 12:15:06  alexandre_tb
* merge from narmer
*
* Revision 1.65  2007-06-25 08:31:17  alexandre_tb
* utilisation de la bibliotheque generale api/formulaire/formulaire.fonct.inc.php a la place de bazar.fonct.formulaire.php
*
* Revision 1.64  2007-06-04 15:25:39  alexandre_tb
* ajout de la carto google
*
* Revision 1.63  2007/04/11 08:30:12  neiluj
* remise en état du CVS...
*
* Revision 1.57.2.12  2007/03/16 14:49:24  alexandre_tb
* si la date de debut d evenement est superieure a la date de fin alors on met
* la meme date dans les deux champs (coherence)
*
* Revision 1.57.2.11  2007/03/07 17:40:57  jp_milcent
* Ajout d'id sur les colonnes et gestion par les CSS des styles du tableau des abonnements.
*
* Revision 1.57.2.10  2007/03/07 17:20:19  jp_milcent
* Ajout du nettoyage syst�matique des URLs.
*
* Revision 1.57.2.9  2007/03/06 16:23:24  jp_milcent
* Nettoyage de l'url pour la gestion des droits.
*
* Revision 1.57.2.8  2007/03/05 14:33:44  jp_milcent
* Suppression de l'appel � Mes_Fiches dans la fonction baz_formulaire
*
* Revision 1.57.2.7  2007/03/05 10:28:03  alexandre_tb
* correction d un commentaire
*
* Revision 1.57.2.6  2007/02/15 13:42:16  jp_milcent
* Utilisation de IN � la place du = dans les requ�tes traitant les cat�gories de fiches.
* Permet d'utiliser la syntaxe 1,2,3 dans la configuration de categorie_nature.
*
* Revision 1.57.2.5  2007/02/12 16:16:31  alexandre_tb
* suppression du style clear:both dans les attribut du formulaire d identification
*
* Revision 1.57.2.4  2007/02/01 16:19:30  alexandre_tb
* correction erreur de requete sur insertion bazar_fiche
*
* Revision 1.57.2.3  2007/02/01 16:11:05  alexandre_tb
* correction erreur de requete sur insertion bazar_fiche
*
* Revision 1.57.2.2  2007/01/22 16:05:39  alexandre_tb
* insertion de la date du jour dans bf_date_debut_validite_fiche quand il n'y a pas ce champs dans le formulaire (�vite le 0000-00-00)
*
* Revision 1.57.2.1  2006/12/13 13:23:03  alexandre_tb
* Remplacement de l appel d une constante par un appel direct. -> warning
*
* Revision 1.58  2006/12/13 13:20:16  alexandre_tb
* Remplacement de l appel d une constante par un appel direct. -> warning
*
* Revision 1.57  2006/10/05 08:53:50  florian
* amelioration moteur de recherche, correction de bugs
*
* Revision 1.56  2006/09/28 15:41:36  alexandre_tb
* Le formulaire pour se logguer dans l'action saisir reste sur l'action saisir apr�s
*
* Revision 1.55  2006/09/21 14:19:39  florian
* amélioration des fonctions liés au wikini
*
* Revision 1.54  2006/09/14 15:11:23  alexandre_tb
* suppression temporaire de la gestion des wikinis
*
* Revision 1.53  2006/07/25 13:24:44  florian
* correction bug image
*
* Revision 1.52  2006/07/25 13:05:00  alexandre_tb
* Remplacement d un die par un echo
*
* Revision 1.51  2006/07/18 14:17:32  alexandre_tb
* Ajout d'un formulaire d identification
*
* Revision 1.50  2006/06/21 08:37:59  alexandre_tb
* Correction de bug, d'un appel constant (....) qui ne fonctionnais plus.
*
* Revision 1.49  2006/06/02 09:29:07  florian
* debut d'integration de wikini
*
* Revision 1.48  2006/05/19 13:54:11  florian
* stabilisation du moteur de recherche, corrections bugs, lien recherche avancee
*
* Revision 1.47  2006/04/28 12:46:14  florian
* integration des liens vers annuaire
*
* Revision 1.46  2006/03/29 13:04:35  alexandre_tb
* utilisation de la classe Administrateur_bazar
*
* Revision 1.45  2006/03/24 09:28:02  alexandre_tb
* utilisation de la variable globale $GLOBALS['_BAZAR_']['categorie_nature']
*
* Revision 1.44  2006/03/14 17:10:21  florian
* ajout des fonctions de syndication, changement du moteur de recherche
*
* Revision 1.43  2006/03/02 20:36:52  florian
* les entrees du formulaire de saisir ne sont plus dans les constantes mias dans des tables qui gerent le multilinguisme.
*
* Revision 1.42  2006/03/01 16:23:22  florian
* modifs textes fr et correction bug "undefined index"
*
* Revision 1.41  2006/03/01 16:05:51  florian
* ajout des fichiers joints
*
* Revision 1.40  2006/02/06 09:33:00  alexandre_tb
* correction de bug
*
* Revision 1.39  2006/01/30 17:25:38  alexandre_tb
* correction de bugs
*
* Revision 1.38  2006/01/30 10:27:04  florian
* - ajout des entrées de formulaire fichier, url, et image
* - correction bug d'affichage du mode de saisie
*
* Revision 1.37  2006/01/24 14:11:11  alexandre_tb
* correction de bug sur l'ajout d'une image et d'un fichier
*
* Revision 1.36  2006/01/19 17:42:11  florian
* ajout des cases à cocher pré-cochées pour les maj
*
* Revision 1.35  2006/01/18 11:06:51  florian
* correction erreur saisie date
*
* Revision 1.34  2006/01/18 10:53:28  florian
* corrections bugs affichage fiche
*
* Revision 1.33  2006/01/18 10:07:34  florian
* recodage de l'insertion et de la maj des données relatives aux listes et checkbox dans des formulaires
*
* Revision 1.32  2006/01/18 10:03:36  florian
* recodage de l'insertion et de la maj des données relatives aux listes et checkbox dans des formulaires
*
* Revision 1.31  2006/01/17 10:07:08  alexandre_tb
* en cours
*
* Revision 1.30  2006/01/16 09:42:57  alexandre_tb
* en cours
*
* Revision 1.29  2006/01/13 14:12:51  florian
* utilisation des temlates dans la table bazar_nature
*
* Revision 1.28  2006/01/05 16:28:24  alexandre_tb
* prise en chage des checkbox, reste la mise � jour � g�rer
*
* Revision 1.27  2006/01/04 15:30:56  alexandre_tb
* mise en forme du code
*
* Revision 1.26  2006/01/03 10:19:31  florian
* Mise à jour pour accepter des parametres dans papyrus: faire apparaitre ou non le menu, afficher qu'un type de fiches, définir l'action par défaut...
*
* Revision 1.25  2005/12/20 14:49:35  ddelon
* Fusion Head vers Livraison
*
* Revision 1.24  2005/12/16 15:44:40  alexandre_tb
* ajout de l'option restreindre d�p�t
*
* Revision 1.23  2005/12/01 17:03:34  florian
* changement des chemins pour appli Pear
*
* Revision 1.22  2005/12/01 16:05:41  florian
* changement des chemins pour appli Pear
*
* Revision 1.21  2005/12/01 15:31:30  florian
* correction bug modifs et saisies
*
* Revision 1.20  2005/11/30 13:58:45  florian
* ajouts graphisme (logos, boutons), changement structure SQL bazar_fiche
*
* Revision 1.19  2005/11/24 16:17:13  florian
* corrections bugs, ajout des cases à cocher
*
* Revision 1.18  2005/11/18 16:03:23  florian
* correction bug html entites
*
* Revision 1.17  2005/11/17 18:48:02  florian
* corrections bugs + amélioration de l'application d'inscription
*
* Revision 1.16  2005/11/07 17:30:36  florian
* ajout controle sur les listes pour la saisie
*
* Revision 1.15  2005/11/07 17:05:45  florian
* amélioration validation conditions de saisie, ajout des règles spécifiques de saisie des formulaires
*
* Revision 1.14  2005/11/07 08:48:02  florian
* correction pb guillemets pour saisie et modif de fiche
*
* Revision 1.13  2005/10/21 16:15:04  florian
* mise a jour appropriation
*
* Revision 1.11  2005/10/12 17:20:33  ddelon
* Reorganisation calendrier + applette
*
* Revision 1.10  2005/10/12 15:14:06  florian
* amélioration de l'interface de bazar, de manière a simplifier les consultations, et à harmoniser par rapport aux Ressources
*
* Revision 1.9  2005/10/10 16:22:52  alexandre_tb
* Correction de bug. Lorsqu'on revient en arri�re apr�s avoir valid� un formulaire.
*
* Revision 1.8  2005/09/30 13:50:07  alexandre_tb
* correction bug date parution ressource
*
* Revision 1.7  2005/09/30 13:15:58  ddelon
* compatibilit� php5
*
* Revision 1.6  2005/09/30 13:00:05  ddelon
* Fiche bazar generique
*
* Revision 1.5  2005/09/30 12:22:54  florian
* Ajouts commentaires pour fiche, modifications graphiques, maj SQL
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
* Revision 1.10  2004/07/08 17:25:25  florian
* ajout commentaires + petits debuggages
*
* Revision 1.8  2004/07/07 14:30:19  florian
* d�buggage RSS
*
* Revision 1.7  2004/07/06 16:22:01  florian
* d�buggage modification + MAJ flux RSS
*
* Revision 1.6  2004/07/06 09:28:26  florian
* changement interface de modification
*
* Revision 1.5  2004/07/05 15:10:23  florian
* changement interface de saisie
*
* Revision 1.4  2004/07/02 14:51:14  florian
* ajouts divers pour faire fonctionner l'insertion de fiches
*
* Revision 1.3  2004/07/01 16:37:42  florian
* ajout de fonctions pour les templates
*
* Revision 1.2  2004/07/01 13:00:13  florian
* modif Florian
*
* Revision 1.1  2004/06/23 09:58:32  alex
* version initiale
*
* Revision 1.1  2004/06/18 09:00:37  alex
* version initiale
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/

?>
