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
// CVS : $Id: bazar.fonct.php,v 1.10 2010/03/04 14:19:03 mrflos Exp $
/**
*
* Fonctions du module bazar
*
*
*@package bazar
//Auteur original :
*@author        Alexandre Granier <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@outils-reseaux.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.10 $ $Date: 2010/03/04 14:19:03 $
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

/** afficher_menu () - Prépare les boutons du menu de bazar et renvoie le html
*
* @return   string  HTML
*/
function afficher_menu() {
	$res = '<div id="BAZ_menu">'."\n".'<ul>'."\n";
	// Gestion de la vue par defaut
	if (!isset($_GET[BAZ_VARIABLE_VOIR])) {
		$_GET[BAZ_VARIABLE_VOIR] = BAZ_VOIR_DEFAUT;
	}

	// Mes fiches
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_MES_FICHES))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_MES_FICHES);
		$res .= '<li id="menu_mes_fiches"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_MES_FICHES) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="btn">'.BAZ_VOIR_VOS_FICHES.'</a>'."\n".'</li>'."\n";
	}

	//partie consultation d'annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_CONSULTER))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$res .= '<li id="menu_consulter"';
		if ((isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_CONSULTER)) $res .=' class="onglet_actif" ';
		$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="btn">'.BAZ_CONSULTER.'</a>'."\n".'</li>'."\n";
	}

	//partie saisie d'annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_SAISIR))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
		$res .= '<li id="menu_deposer"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && ($_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_SAISIR )) $res .=' class="onglet_actif" ';
		$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="btn">'.BAZ_SAISIR.'</a>'."\n".'</li>'."\n";
	}

	//partie abonnement aux annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_S_ABONNER))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_S_ABONNER);
		$res .= '<li id="menu_inscrire"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_S_ABONNER) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="btn">'.BAZ_S_ABONNER.'</a></li>'."\n" ;
	}

	//partie affichage formulaire
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_FORMULAIRE))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_FORMULAIRE);
		$res .= '<li id="menu_formulaire"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_FORMULAIRE) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="btn">'.BAZ_FORMULAIRE.'</a></li>'."\n" ;
	}

	//partie affichage listes
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_LISTES))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_LISTES);
		$res .= '<li id="menu_listes"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_LISTES) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="btn">'.BAZ_LISTES.'</a></li>'."\n" ;
	}
	
	// Au final, on place dans l url, l action courante
	if (isset($_GET[BAZ_VARIABLE_VOIR])) $GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, $_GET[BAZ_VARIABLE_VOIR]);
	$res.= '</ul>'."\n".'<div style="display:block;clear:left"></div>'."\n".'</div>'."\n";
	return $res;
}

/** fiches_a_valider () - Renvoie les annonces restant a valider par un administrateur
*
* @return   string  HTML
*/
function fiches_a_valider() {
	// Pour les administrateurs d'une rubrique, on affiche les fiches a valider de cette rubrique
	// On effectue une requete sur le bazar pour voir les fiches a administrer
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_ADMIN);
	$res= '<h2>'.BAZ_ANNONCES_A_ADMINISTRER.'</h2><br />'."\n";
	$requete = 'SELECT * FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature WHERE bf_statut_fiche=0 AND ' .
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
		$entete = array (BAZ_TITREANNONCE ,BAZ_ANNONCEUR, BAZ_TYPE_FICHE, BAZ_PUBLIER, BAZ_SUPPRIMER) ;
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
			//$lien_voir->addQueryString('typeannonce', $ligne['bn_id_nature']);

			// Nettoyage de l'url
			// NOTE (jpm - 23 mai 2007): pour ï¿½tre compatible avec PHP5 il faut utiliser tjrs $GLOBALS['_BAZAR_']['url'] car en php4 on
			// copie bien une variable mais pas en php5, cela reste une rï¿½fï¿½rence...
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
			//$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');

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
    $requete = 'SELECT * FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature WHERE bf_statut_fiche=1 AND ' .
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
		$tableAttr = array('class' => 'table_bazar') ;
		$table = new HTML_Table($tableAttr) ;
		$entete = array (BAZ_TITREANNONCE ,BAZ_ANNONCEUR, BAZ_TYPE_FICHE, BAZ_PUBLIER, BAZ_SUPPRIMER) ;
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
			// NOTE (jpm - 23 mai 2007): pour ï¿½tre compatible avec PHP5 il faut utiliser tjrs $GLOBALS['_BAZAR_']['url'] car en php4 on
			// copie bien une variable mais pas en php5, cela reste une rï¿½fï¿½rence...
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
	$res= '<h2 class="titre_mes_fiches">'.BAZ_VOS_FICHES.'</h2><br />'."\n";
	
	//test si l'on est identifié pour voir les fiches
	if ( baz_a_le_droit('voir_mes_fiches') ) {
		$nomwiki = $GLOBALS['wiki']->getUser();
		// requete pour voir si l'utilisateur a des fiches a son nom, classees par date de MAJ et nature d'annonce
		$requete = 'SELECT * FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature WHERE bf_ce_utilisateur="'. $nomwiki['name'].
		           '" AND bn_id_nature=bf_ce_nature ';
		if ($GLOBALS['_BAZAR_']['categorie_nature']!='toutes') $requete .= ' AND bn_type_fiche = "'.$GLOBALS['_BAZAR_']['categorie_nature'].'" ';
		if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
		$requete .= ' ORDER BY bf_ce_nature ASC, bf_titre';

		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows() != 0) {
			$tableAttr = array('class' => 'table_bazar', 'summary' => 'Tableau des fiches d\'une personne') ;
			$table = new HTML_Table($tableAttr) ;
			$entete = array (BAZ_TYPE_FICHE, BAZ_TITREANNONCE,  BAZ_ETATPUBLICATION, BAZ_MODIFIER, BAZ_SUPPRIMER) ;
			$table->addRow($entete) ;
			$table->setRowType (0, "th") ;

		// On affiche une ligne par proposition
		while ($ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC)) {
			if ($ligne['bf_statut_fiche']==1) $publiee=BAZ_PUBLIEE;
			elseif ($ligne['bf_statut_fiche']==0) $publiee=BAZ_ENCOURSDEVALIDATION;
			else $publiee=BAZ_REJETEE;

			$lien_voir = clone($GLOBALS['_BAZAR_']['url']);
			$lien_voir->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien_voir->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_voir->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
			$lien_voir_url=str_replace('&','&amp;',$lien_voir->getURL());

			$lien_modifier = clone($GLOBALS['_BAZAR_']['url']);
			$lien_modifier->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
			$lien_modifier->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_modifier->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
			$lien_modifier_url=str_replace('&','&amp;',$lien_modifier->getURL());

			$lien_supprimer = clone($GLOBALS['_BAZAR_']['url']);
			$lien_supprimer->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
			$lien_supprimer->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$lien_supprimer->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
			$lien_supprimer_url=str_replace('&','&amp;',$lien_supprimer->getURL());

			$table->addRow (array(
					$ligne['bn_label_nature']."\n", // col 1: type annonce
			        '<a href="'.$lien_voir_url.'" class="BAZ_lien_voir">'.$ligne['bf_titre'].'</a>'."\n", // col 2 : le nom
					$publiee."\n", // col 3 : publiee ou non
					'<a href="'.$lien_modifier_url.'" class="BAZ_lien_modifier">'.BAZ_MODIFIER.'</a>'."\n", // col 4 : modifier
					'<a href="'.$lien_supprimer_url.'" class="BAZ_lien_supprimer" onclick="javascript:return '.
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
	    	$res .= '<div class="BAZ_info">'.BAZ_PAS_DE_FICHE.'</div>'."\n" ;
	    }
	}
	else  {
		$res .= '<div class="BAZ_info">'.BAZ_IDENTIFIEZ_VOUS_POUR_VOIR_VOS_FICHES.'</div>'."\n";

	}
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
	$res .= '<ul class="BAZ_liste liste_action">
	<li><a class="ajout_fiche" href="'.str_replace('&','&amp;', $GLOBALS['_BAZAR_']['url']->getURL()).'" title="'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'">'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'</a></li></ul>';
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
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
			$requete = 'DELETE FROM '.BAZ_PREFIXE.'droits WHERE bd_id_utilisateur='.$_GET['pers'].
				   ' AND bd_id_nature_offre='.$_GET['idtypeannonce'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		//CAS DU SUPER ADMIN: On efface tous les droits de la personne en general
		else {
			$requete = 'DELETE FROM '.BAZ_PREFIXE.'droits WHERE bd_id_utilisateur='.$_GET['pers'];
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		if ($_GET['droits']=='superadmin') {
			$requete = 'INSERT INTO '.BAZ_PREFIXE.'droits VALUES ('.$_GET['pers'].',0,0)';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		elseif ($_GET['droits']=='redacteur') {
			$requete = 'INSERT INTO '.BAZ_PREFIXE.'droits VALUES ('.$_GET['pers'].','.$_GET['idtypeannonce'].',1)';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				die ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
		}
		elseif ($_GET['droits']=='admin') {
			$requete = 'INSERT INTO '.BAZ_PREFIXE.'droits VALUES ('.$_GET['pers'].','.$_GET['idtypeannonce'].',2)';
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
			$requete = 'SELECT bn_id_nature, bn_label_nature, bn_image_titre FROM '.BAZ_PREFIXE.'nature';
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
					$titre='&nbsp;<img src="'.BAZ_CHEMIN.'presentation/images/'.$ligne['bn_image_titre'].'" alt="'.$ligne['bn_label_nature'].'" />'."\n";
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

/** baz_formulaire() - Renvoie le formulaire pour les saisies ou modification des fiches
*
* @param	string	action du formulaire : soit formulaire de saisie, soit inscription dans la base de données, soit formulaire de modification, soit modification de la base de données
* @param	string	url de renvois du formulaire (facultatif)
* @param	array	valeurs de la fiche en cas de modification (facultatif)
*
* @return   string  HTML
*/
function baz_formulaire($mode, $url = '', $valeurs = '') {
	$res = '';
	if ($url == '') {
		$lien_formulaire = $GLOBALS['_BAZAR_']['url'];
		$lien_formulaire->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
		//Definir le lien du formulaire en fonction du mode de formulaire choisi
		if ($mode == BAZ_DEPOSER_ANNONCE) {
			$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU);
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
			}
			$lien_formulaire->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		}
		if ($mode == BAZ_ACTION_MODIFIER_V) {
			$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER_V);
			$lien_formulaire->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		}
	}

	//contruction du squelette du formulaire
	$formtemplate = new HTML_QuickForm('formulaire', 'post', preg_replace ('/&amp;/', '&', ($url ? $url : $lien_formulaire->getURL())) );
	$squelette = &$formtemplate->defaultRenderer();
   	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
    $squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label">'."\n".'<!-- BEGIN required --><span class="symbole_obligatoire">*&nbsp;</span><!-- END required -->'."\n".'{label} :</div>'."\n".
    							'<div class="formulaire_input"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</div>'."\n".'</div>'."\n");
   	$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".'<div class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
                                    '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</div>'."\n".'</div>'."\n", 'accept_condition');
    	$squelette->setElementTemplate( '<div class="groupebouton">{label}{element}</div>'."\n", 'groupe_boutons');
    	$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label_select">'."\n".'{label} :</div>'."\n".
									'<div class="formulaire_select"> '."\n".'{element}'."\n".'</div>'."\n".
									'</div>', 'select');
    	$squelette->setRequiredNoteTemplate("\n".'<div class="symbole_obligatoire">* {requiredNote}</div>'."\n");
	//Traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);
	
	//antispam
	$formtemplate->addElement('hidden', 'antispam', 0);

	//------------------------------------------------------------------------------------------------
	//AFFICHAGE DU FORMULAIRE GENERAL DE CHOIX DU TYPE D'ANNONCE
	//------------------------------------------------------------------------------------------------
	if ($mode == BAZ_DEPOSER_ANNONCE) {
		if (isset($GLOBALS['_BAZAR_']['id_typeannonce']) && $GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') {
			$mode = BAZ_ACTION_NOUVEAU ;
		} else {
			//titre
			$res.='<h2 class="titre_saisir_fiche">'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'</h2>'."\n";

			//requete pour obtenir le nom et la description des types d'annonce
			$requete = 'SELECT * FROM '.BAZ_PREFIXE.'nature WHERE ';
			if ($GLOBALS['_BAZAR_']['categorie_nature']!='toutes') $requete .= 'bn_type_fiche="'.$GLOBALS['_BAZAR_']['categorie_nature'].'" ';
			else $requete .= '1 ';
			if (isset($GLOBALS['_BAZAR_']['langue'])) {
				$requete .= 'AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
			}
			$requete .= 'ORDER BY bn_label_nature ASC';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			if (DB::isError($resultat)) {
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
			
			if ($resultat->numRows()==1) {
				$res = '';
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
				
				//on remplace l'attribut action du formulaire par l'action adéquate
				$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU_V);
				$attributes = array('action'=>str_replace ("&amp;", "&", $lien_formulaire->getURL()));
				$formtemplate->updateAttributes($attributes);
			}
			else {
				while ($ligne = $resultat->fetchRow (DB_FETCHMODE_ASSOC)) {
						if ($ligne['bn_image_titre']!='') {
							$titre='&nbsp;<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$ligne['bn_image_titre'].'" alt="'.
											$ligne['bn_label_nature'].'" />'."\n";
						} else {
							$titre='<span class="BAZ_titre_liste">'.$ligne['bn_label_nature'].' : </span>'."\n";
						}
						$formtemplate->addElement('radio', 'id_typeannonce', '',$titre.$ligne['bn_description']."\n",
								$ligne['bn_id_nature'], array("id" => 'select'.$ligne['bn_id_nature']));
				}

				$res .= '<br />'.BAZ_CHOIX_TYPE_FICHE.'<br /><br />'."\n";

				// Bouton d annulation
				$lien_formulaire->removeQueryString(BAZ_VARIABLE_ACTION);
				$lien_formulaire->removeQueryString(BAZ_VARIABLE_VOIR);
				$lien_formulaire->removeQueryString('id_typeannonce');
				$lien_formulaire->removeQueryString('id_fiche');

				// Nettoyage de l'url avant les return
				$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER, str_replace("&amp;", "&", $GLOBALS['_BAZAR_']['url']->getURL()), BAZ_ANNULER, array('class' => 'btn bouton_annuler'));
				$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER, array('class' => 'btn bouton_sauver'));
				$formtemplate->addGroup($buttons, 'groupe_boutons', null, '&nbsp;', 0);
				$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_input"> '."\n".'{element}'."\n".
									'<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
									'</div>'."\n".'</div>'."\n");

				//Affichage a l'ecran
				$res .= $formtemplate->toHTML()."\n";
			}
		}
	}

	//------------------------------------------------------------------------------------------------
	//AFFICHAGE DU FORMULAIRE CORRESPONDANT AU TYPE DE L'ANNONCE CHOISI PAR L'UTILISATEUR
	//------------------------------------------------------------------------------------------------

	if ($mode == BAZ_ACTION_NOUVEAU) {
		// Affichage du modele de formulaire
		$res .= baz_afficher_formulaire_fiche('saisie', $formtemplate, $url);
	}


	//------------------------------------------------------------------------------------------------
	//CAS DE LA MODIFICATION D'UNE ANNONCE (FORMULAIRE DE MODIFICATION)
	//------------------------------------------------------------------------------------------------
	if ($mode == BAZ_ACTION_MODIFIER) {
		$res .= baz_afficher_formulaire_fiche('modification', $formtemplate, $url, $valeurs);
	}

	//------------------------------------------------------------------------------------------------
	//CAS DE L'INSCRIPTION D'UNE ANNONCE
	//------------------------------------------------------------------------------------------------
	if ($mode == BAZ_ACTION_NOUVEAU_V && $_POST['antispam']==1) {
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
	if ($mode == BAZ_ACTION_MODIFIER_V && $_POST['antispam']==1) {
		if ($formtemplate->validate() && baz_a_le_droit( 'saisie_fiche', $_POST['bf_ce_utilisateur'] ) ) {
			$formtemplate->process('baz_mise_a_jour', false) ;
			// Redirection vers mes_fiches pour eviter la revalidation du formulaire
			$GLOBALS['_BAZAR_']['url']->addQueryString ('message', 'modif_ok') ;
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
			header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
			exit;
		}
	}

	return $res;
}

/** baz_afficher_formulaire_fiche() - Genere le formulaire de saisie d'une annonce
*
* @param	string type de formulaire: insertion ou modification
* @param	mixed objet quickform du formulaire
* @param	string	url de renvois du formulaire (facultatif)
* @param	array	valeurs de la fiche en cas de modification (facultatif)
*
* @return   string  code HTML avec formulaire
*/
function baz_afficher_formulaire_fiche($mode = 'saisie', $formtemplate, $url = '', $valeurs = '') {
	$res = '';
	//titre de la rubrique
	$res .= '<h2 class="titre_type_fiche">'.BAZ_TITRE_SAISIE_FICHE.'&nbsp;'.$GLOBALS['_BAZAR_']['typeannonce'].'</h2><br />'."\n";

	//si le type de formulaire requiert une acceptation des conditions on affiche les conditions
	if ($GLOBALS['_BAZAR_']['condition']!='' && !isset($_POST['accept_condition'])) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, $_GET[BAZ_VARIABLE_ACTION]);
		if (!empty($GLOBALS['_BAZAR_']['id_fiche'])) $GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
		$formtemplate->updateAttributes(array(BAZ_VARIABLE_ACTION => str_replace('&amp;', '&', ($url ? $url : $GLOBALS['_BAZAR_']['url']->getURL()))));
		require_once 'HTML/QuickForm/html.php';
		$conditions= new HTML_QuickForm_html('<tr><td colspan="2">'.$GLOBALS['_BAZAR_']['condition'].'</td>'."\n".'</tr>'."\n");
		$formtemplate->addElement($conditions);
		$formtemplate->addElement('checkbox', 'accept_condition',BAZ_ACCEPTE_CONDITIONS) ;
		$formtemplate->addElement('hidden', 'id_typeannonce', $GLOBALS['_BAZAR_']['id_typeannonce']);
		$formtemplate->addRule('accept_condition', BAZ_ACCEPTE_CONDITIONS_REQUIS, 'required', '', 'client') ;
		$formtemplate->addElement('submit', 'valider', BAZ_VALIDER);
	}
	//affichage du formulaire si conditions acceptees
	else {
		//Parcours du fichier de templates, pour mettre les valeurs des champs
		$tableau = formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']);
		if (!is_array($valeurs) && isset($GLOBALS['_BAZAR_']['id_fiche']) && $GLOBALS['_BAZAR_']['id_fiche']!='')
		{
			//Ajout des valeurs par defaut pour une modification
			$valeurs = baz_valeurs_fiche($GLOBALS['_BAZAR_']['id_fiche']);
			
		} elseif (isset($valeurs['id_fiche'])) {
			$GLOBALS['_BAZAR_']['id_fiche'] = $valeurs['id_fiche'];
		}
		for ($i=0; $i<count($tableau); $i++) {
			$tableau[$i][0]($formtemplate, $tableau[$i], 'saisie', $valeurs) ;
		}
		$formtemplate->addElement('hidden', 'id_typeannonce', $GLOBALS['_BAZAR_']['id_typeannonce']);
		
		//si on a passé une url, on est dans le cas d'une page de type fiche_bazar, il nous faut le nom
		if ($url != '') {
			$formtemplate->addElement('hidden', 'id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		}
		
		
		// Bouton d annulation : on retourne à la visualisation de la fiche saisie en cas de modification
		if ($mode == 'modification') {
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		// Bouton d annulation : on retourne à la page wiki sans aucun choix par defaut sinon
		} else {
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
			$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_typeannonce');
			$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
		}
		$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER, str_replace("&amp;", "&", ($url ? str_replace('/edit', '', $url) : $GLOBALS['_BAZAR_']['url']->getURL())), BAZ_ANNULER, array('class' => 'btn bouton_annuler'));
		$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER, array('class' => 'btn bouton_sauver'));
		$formtemplate->addGroup($buttons, 'groupe_boutons', null, '&nbsp;', 0);

	}
	
	//Affichage a l'ecran
	$res .= $formtemplate->toHTML()."\n";
	return $res;
}


/** baz_requete_bazar_fiche() - preparer la requete d'insertion ou de MAJ de la table '.BAZ_PREFIXE.'fiche a partir du template
*
* @global   mixed L'objet contenant les valeurs issues de la saisie du formulaire
* @return   void
*/
function baz_requete_bazar_fiche($valeur) {
	//on enleve les champs hidden pas nécéssaires à la fiche
	unset($valeur["valider"]);
	unset($valeur["MAX_FILE_SIZE"]);
	
	$valeur['id_fiche'] = $GLOBALS['_BAZAR_']['id_fiche'];
	
	//pour les checkbox, on met les résultats sur une ligne
	foreach ($valeur as $cle => $val) { 
		if (is_array($val)) {
			$valeur[$cle] = implode(',', array_keys($val));
		}
	}
		
	$requete = NULL;
	//l'annonce est directement publiee pour les admins
	if (!BAZ_SANS_AUTH) $utilisateur = new Administrateur_bazar($GLOBALS['AUTH']);
	if (!BAZ_SANS_AUTH && ( $utilisateur->isAdmin( $GLOBALS['_BAZAR_']['id_typeannonce']) ||
		$utilisateur->isSuperAdmin() ) ) {
		$requete.='bf_statut_fiche=1, ';
	}
	//sinon on met la constante du fichier de configuration
	else {
		$requete.='bf_statut_fiche="'.BAZ_ETAT_VALIDATION.'", ';
	}
	$tableau=formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']);
	for ($i=0; $i<count($tableau); $i++) {
		$requete .= $tableau[$i][0]($formtemplate, $tableau[$i], 'requete', $valeur);
	}
	
	$requete.=' bf_date_maj_fiche=NOW()';
	
	//on encode en utf-8 pour réussir à encoder en json
	$valeur = array_map("utf8_encode", $valeur);
	//on sauve les valeurs d'une fiche dans une PageWiki, pour garder l'historique
	$GLOBALS["wiki"]->SavePage($GLOBALS['_BAZAR_']['id_fiche'], json_encode($valeur));
	
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
        // l'identifiant (sous forme de NomWiki) est généré à partir du titre    
        $GLOBALS['_BAZAR_']['id_fiche'] = genere_nom_wiki($valeur['bf_titre']);
        $requete = 'INSERT INTO '.BAZ_PREFIXE.'fiche SET bf_id_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'", ';
		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && $GLOBALS['_BAZAR_']['nomwiki']!=NULL) $requete .= 'bf_ce_utilisateur="'.$GLOBALS['_BAZAR_']['nomwiki']['name'].'", ';
		$requete .= 'bf_categorie_fiche="'.$GLOBALS['_BAZAR_']['categorie_nature'].'", bf_ce_nature='.$GLOBALS['_BAZAR_']['id_typeannonce'].', '.
		   'bf_date_creation_fiche=NOW(), ';
		if (!isset($_REQUEST['bf_date_debut_validite_fiche'])) {
			$requete .= 'bf_date_debut_validite_fiche=now(), bf_date_fin_validite_fiche="0000-00-00", ' ;
		}
		$requete .= baz_requete_bazar_fiche($valeur) ;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		
		//on cree un triple pour spécifier que la page wiki créée est une fiche bazar
		$GLOBALS["wiki"]->InsertTriple($GLOBALS['_BAZAR_']['id_fiche'], 'http://outils-reseaux.org/_vocabulary/type', 'fiche_bazar', '', '');
	
		// Envoie d un mail aux administrateurs
		if (BAZ_ENVOI_MAIL_ADMIN) {
			include_once('Mail.php');
			include_once('Mail/mime.php');
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['wiki']->config["base_url"]);
			$sujet = remove_accents('['.str_replace("http://","",$lien).'] nouvelle fiche ajoutee : '.$valeur['bf_titre']);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
			$text = 'Voir la fiche sur le site pour l\'administrer : '.$GLOBALS['_BAZAR_']['url']->getUrl();
			$texthtml = '<br /><br /><a href="'.$GLOBALS['_BAZAR_']['url']->getUrl().'" title="Voir la fiche">Voir la fiche sur le site pour l\'administrer</a>';
			$fichier = 'tools/bazar/presentation/bazar.css';
			$style = file_get_contents($fichier);
			$style = str_replace('url(', 'url('.$lien.'/tools/bazar/presentation/', $style);
			$fiche = str_replace('src="tools', 'src="'.$lien.'/tools', baz_voir_fiche(0, $GLOBALS['_BAZAR_']['id_fiche'])).$texthtml;
			$html = '<html><head><style type="text/css">'.$style.'</style></head><body>'.$fiche.'</body></html>';

			$crlf = "\n";
			$hdrs = array(
			              'From'    => BAZ_ADRESSE_MAIL_ADMIN,
			              'Subject' => $sujet
			              );

			$mime = new Mail_mime($crlf);

			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);

			//do not ever try to call these lines in reverse order
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);

			$mail =& Mail::factory('mail');

			//on va chercher les admins
			$requeteadmins = 'SELECT value FROM '.$GLOBALS['wiki']->config["table_prefix"].'triples WHERE resource="ThisWikiGroup:admins" AND property="http://www.wikini.net/_vocabulary/acls" LIMIT 1';
			$resultatadmins = $GLOBALS['_BAZAR_']['db']->query($requeteadmins);
			$ligne = $resultatadmins->fetchRow(DB_FETCHMODE_ASSOC);
			$tabadmin = explode("\n", $ligne['value']);
			foreach ($tabadmin  as $line) {
				$admin = $GLOBALS['wiki']->LoadUser(trim($line));
				$mail->send($admin['email'], $hdrs, $body);
			}
		}

		//on nettoie l'url, on retourne a la consultation des fiches
		$GLOBALS['_BAZAR_']['url']->addQueryString('message', 'ajout_ok') ;
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
		header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
		exit;
		return ;
}




/** baz_mise_a_jour() - Mettre a jour une fiche
*
* @global   Le contenu du formulaire de saisie de l'annonce
* @return   void
*/
function baz_mise_a_jour($valeur) {
	//MAJ de '.BAZ_PREFIXE.'fiche
	$requete = 'UPDATE '.BAZ_PREFIXE.'fiche SET '.baz_requete_bazar_fiche($valeur,$GLOBALS['_BAZAR_']['id_typeannonce']);
	$requete.= ' WHERE bf_id_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'"';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	
	// Envoie d un mail aux administrateurs
		if (BAZ_ENVOI_MAIL_ADMIN) {
			include_once('Mail.php');
			include_once('Mail/mime.php');
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['wiki']->config["base_url"]);
			$sujet = remove_accents('['.str_replace("http://","",$lien).'] fiche modifiee : '.$valeur['bf_titre']);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
			$text = 'Voir la fiche sur le site pour l\'administrer : '.$GLOBALS['_BAZAR_']['url']->getUrl();
			$texthtml = '<br /><br /><a href="'.$GLOBALS['_BAZAR_']['url']->getUrl().'" title="Voir la fiche">Voir la fiche sur le site pour l\'administrer</a>';
			$fichier = 'tools/bazar/presentation/bazar.css';
			$style = file_get_contents($fichier);
			$style = str_replace('url(', 'url('.$lien.'/tools/bazar/presentation/', $style);
			$fiche = str_replace('src="tools', 'src="'.$lien.'/tools', baz_voir_fiche(0, $GLOBALS['_BAZAR_']['id_fiche'])).$texthtml;
			$html = '<html><head><style type="text/css">'.$style.'</style></head><body>'.$fiche.'</body></html>';

			$crlf = "\n";
			$hdrs = array(
			              'From'    => BAZ_ADRESSE_MAIL_ADMIN,
			              'Subject' => $sujet
			              );

			$mime = new Mail_mime($crlf);

			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);

			//do not ever try to call these lines in reverse order
			$body = $mime->get();
			$hdrs = $mime->headers($hdrs);

			$mail =& Mail::factory('mail');

			//on va chercher les admins
			$requeteadmins = 'SELECT value FROM '.$GLOBALS['wiki']->config["table_prefix"].'triples WHERE resource="ThisWikiGroup:admins" AND property="http://www.wikini.net/_vocabulary/acls" LIMIT 1';
			$resultatadmins = $GLOBALS['_BAZAR_']['db']->query($requeteadmins);
			$ligne = $resultatadmins->fetchRow(DB_FETCHMODE_ASSOC);
			$tabadmin = explode("\n", $ligne['value']);
			foreach ($tabadmin  as $line) {
				$admin = $GLOBALS['wiki']->LoadUser(trim($line));
				$mail->send($admin['email'], $hdrs, $body);
			}
		}
	return;
}


/** baz_suppression() - Supprime une fiche
*
* @global   L'identifiant de la fiche a supprimer
* @return   void
*/
function baz_suppression($idfiche) {
	$valeur = baz_valeurs_fiche($idfiche);
	if ( baz_a_le_droit( 'saisie_fiche', $valeur['bf_ce_utilisateur'] ) ) {
		//suppression des valeurs des champs texte, checkbox et liste
		$requete = 'DELETE FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche = "'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
		}

		//suppression des valeurs des champs texte long
		$requete = 'DELETE FROM '.BAZ_PREFIXE.'triples WHERE resource = "'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
		}
		
		//TODO:suppression des fichiers et images associées

		//suppression de la fiche dans '.BAZ_PREFIXE.'fiche
		$requete = 'DELETE FROM '.BAZ_PREFIXE.'fiche WHERE bf_id_fiche = "'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
		}
		
		//on supprime les pages wiki crées
		$GLOBALS['wiki']->DeleteOrphanedPage($idfiche);		

		//on nettoie l'url, on retourne à la consultation des fiches
		$GLOBALS['_BAZAR_']['url']->addQueryString ('message', 'delete_ok') ;
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$GLOBALS['_BAZAR_']['url']->removeQueryString (BAZ_VARIABLE_VOIR) ;
		$GLOBALS['_BAZAR_']['url']->removeQueryString ('id_fiche') ;
		header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
		exit;
	}
	else {
		echo '<div class="BAZ_error">'.BAZ_PAS_DROIT_SUPPRIMER.'</div>'."\n";
	}

	return ;
}


/** publier_fiche () - Publie ou non dans les fichiers XML la fiche bazar d'un utilisateur
*
* @global boolean Valide: oui ou non
* @return   void
*/
function publier_fiche($valid) {
	//l'utilisateur à t'il le droit de valider
	if ( baz_a_le_droit( 'valider_fiche' ) ) {
		if ($valid==0) {
			$requete = 'UPDATE '.BAZ_PREFIXE.'fiche SET  bf_statut_fiche=2 WHERE bf_id_fiche="'.$_GET['id_fiche'].'"' ;
			echo '<div class="BAZ_info">'.BAZ_FICHE_PAS_VALIDEE.'</div>'."\n";
		}
		else {
			$requete = 'UPDATE '.BAZ_PREFIXE.'fiche SET  bf_statut_fiche=1 WHERE bf_id_fiche="'.$_GET['id_fiche'].'"' ;
			echo '<div class="BAZ_info">'.BAZ_FICHE_VALIDEE.'</div>'."\n";
		}

		// ====================Mise a jour de la table '.BAZ_PREFIXE.'fiche====================
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		unset ($resultat) ;
		//TODO envoie mail annonceur
	}
	return;
}


/** baz_liste_rss() affiche le formulaire qui permet de s'inscrire pour recevoir des annonces d'un type
*
*   @return  string    le code HTML
*/
function baz_liste_rss() {
	$res= '<h2>'.BAZ_S_ABONNER_AUX_FICHES.'</h2>'."\n";
	//requete pour obtenir l'id et le label des types d'annonces
	$requete = 'SELECT bn_id_nature, bn_label_nature '.
	           'FROM '.BAZ_PREFIXE.'nature WHERE 1';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}

	// Nettoyage de l url
	$lien_RSS=$GLOBALS['_BAZAR_']['url'];
	$lien_RSS->addQueryString('wiki', $GLOBALS['wiki']->minihref('xmlutf8',$_GET['wiki']));
	$lien_RSS->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FLUX_RSS);
	$liste='';
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$lien_RSS->addQueryString('annonce', $ligne['bn_id_nature']);
		$liste .= '<li><a href="'.str_replace('&', '&amp;', $lien_RSS->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'BAZ_rss.png" alt="'.BAZ_RSS.'" /></a>&nbsp;';
		$liste .= $ligne['bn_label_nature'];
		$liste .= '</li>'."\n";
		$lien_RSS->removeQueryString('annonce');
	}
	if ($liste!='') $res .= '<ul class="BAZ_liste">'."\n".'<li><a href="'.str_replace('&', '&amp;', $lien_RSS->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'BAZ_rss.png" alt="'.BAZ_RSS.'" /></a>&nbsp;<strong>Flux RSS de toutes les fiches</strong></li>'."\n".$liste.'</ul>'."\n";
	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('idtypeannonce');
	return $res;
}


/** baz_formulaire_des_formulaires() retourne le formulaire de saisie des formulaires
*
*   @return  Object    le code HTML
*/
function baz_formulaire_des_formulaires($mode, $valeursformulaire = '') {
	$GLOBALS['_BAZAR_']['url']->addQueryString('action_formulaire', $mode);
	
	//contruction du squelette du formulaire
	$formtemplate = new HTML_QuickForm('formulaire', 'post', preg_replace ('/&amp;/', '&', $GLOBALS['_BAZAR_']['url']->getURL()) );
	$GLOBALS['_BAZAR_']['url']->removeQueryString('action_formulaire');
	$squelette =& $formtemplate->defaultRenderer();
	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
    $squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label">'."\n".'{label}'.
    		                        '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
    								' </div>'."\n".'<div class="formulaire_input"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</div>'."\n".'</div>'."\n");
	$squelette->setElementTemplate( '<div class="groupebouton">{label}{element}</div>'."\n", 'groupe_boutons');
 	$squelette->setRequiredNoteTemplate("\n".'<div class="symbole_obligatoire">* {requiredNote}</div>'."\n");
	//traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);
	
	//champs du formulaire
	if (isset($_GET['idformulaire'])) $formtemplate->addElement('hidden', 'bn_id_nature', $_GET['idformulaire']);
	$formtemplate->addElement('text', 'bn_label_nature', BAZ_NOM_FORMULAIRE, array('class' => 'input_texte'));
	$formtemplate->addElement('text', 'bn_type_fiche', BAZ_CATEGORIE_FORMULAIRE, array('class' => 'input_texte'));
	$formtemplate->addElement('textarea', 'bn_description', BAZ_DESCRIPTION, array('class' => 'input_textarea', 'cols' => "20", 'rows'=> "3"));
	$formtemplate->addElement('textarea', 'bn_condition', BAZ_CONDITION, array('class' => 'input_textarea', 'cols' => "20", 'rows'=> "3"));
	$formtemplate->addElement('text', 'bn_label_class', BAZ_NOM_CLASSE_CSS, array('class' => 'input_texte'));
	$formtemplate->addElement('textarea', 'bn_template', BAZ_TEMPLATE, array('class' => 'input_textarea', 'style' => 'width:100%;height:100px;font-size:.8em;', 'cols' => "20", 'rows'=> "3"));
	
	$html_valeurs_listes =  '<div class="formulaire_ligne">'."\n".	
							'<ul class="valeur_formulaire">'."\n";
	if (is_array($valeursformulaire)) {
		$i = 0;
		foreach($valeursformulaire as $ligneliste) {
			$i++;
			$html_valeurs_listes .= 
								'<li class="liste_ligne" id="row'.$i.'">'.
								'<img src="tools/bazar/presentation/images/arrow.png" alt="D&eacute;placer" width="16" height="16" class="handle" />'.
								'<input type="text" name="label['.$i.']" value="'.htmlspecialchars($ligneliste).'" class="input_texte" />'.
								'<input type="hidden" name="ancienlabel['.$i.']" value="'.htmlspecialchars($ligneliste).'" class="input_texte" />'.
								'<a href="#" class="BAZ_lien_supprimer suppression_label_liste"></a>'.
								'</li>'."\n";
		}
	} else {
		$html_valeurs_listes .= '<li class="liste_ligne" id="row1">'.
								'<div class="formulaire_ligne">
									<div class="formulaire_label">
										<img src="tools/bazar/presentation/images/arrow.png" alt="D&eacute;placer" width="16" height="16" class="handle" />Titre
									 </div>
									<div class="formulaire_input"> 
										<input type="text" value="" name="titre" class="input_texte" /><a href="#" class="BAZ_lien_modifier modifier_formulaire" rel="#overlay"></a>
									</div>
								</div>'.
								'</li>'."\n";
	}
						
	$html_valeurs_listes .= '</ul><a href="#" class="ajout_champs_formulaire" title="'.BAZ_AJOUTER_CHAMPS_FORMULAIRE.'" rel="#champs_formulaire">'.BAZ_AJOUTER_CHAMPS_FORMULAIRE.'</a>'."\n".
							'</div>'."\n".
							'<div id="champs_formulaire">
								<h2 class="titre_overlay"></h2>
								<div class="formulaire_ligne">
									<div class="formulaire_label">
										Type de champs
									 </div>
									<div class="formulaire_input"> 
										<select name="type_champs" id="type_champs">
											<option value="0">Choisir...</option>
											<option value="texte">texte</option>
											<option value="textelong">texte long</option>
											<option value="liste">liste d&eacute;roulante</option>
											<option value="checkbox">case &agrave; cocher</option>
											<option value="champs_mail">adresse mail</option>
											<option value="lien_internet">lien internet</option>
											<option value="date">date</option>
											<option value="tags">mot cl&eacute;s</option>
											<option value="fichier">fichier</option>
											<option value="image">image</option>
											<option value="mot_de_passe">mot de passe</option>								
											<option value="champs_cache">champs cach&eacute;</option>
											<option value="carte_google">carte google</option>
											<option value="bookmarklet">bookmarklet</option>
											<option value="utilisateur_wikini">utilisateur yeswiki</option>
										</select>
									</div>
								</div>
								<div class="groupebouton">
									<a href="#" name="annuler" class="btn bouton_annuler bouton_annuler_formulaire">Annuler</a>&nbsp;
									<input type="submit" value="Valider" name="valider" class="btn bouton_sauver">
								</div>
							</div>'."\n".
							'<div class="spacer"></div>'."\n".
							'<script type="text/javascript" src="tools/bazar/libs/jquery-ui-1.8.2.custom.min.js"></script>
							<script type="text/javascript">
							  $(document).ready(function() {
							    $(".valeur_formulaire").sortable({
							      handle : \'.handle\',
							      update : function () {
									$("#formulaire .valeur_formulaire input.input_texte[name^=\'label\']").each(function(i) {
										$(this).attr(\'name\', \'label[\'+(i+1)+\']\').
										parent(\'.liste_ligne\').attr(\'id\', \'row\'+(i+1)).
										find("input:hidden").attr(\'name\', \'ancienlabel[\'+(i+1)+\']\');
									});
							      }
							    });
							});
							</script>'."\n";
	$formtemplate->addElement('html', $html_valeurs_listes);
	
	
	//champs obligatoires
	$formtemplate->addRule('bn_label_nature', BAZ_CHAMPS_REQUIS.' : '.BAZ_FORMULAIRE, 'required', '', 'client');
	$formtemplate->addRule('bn_template', BAZ_CHAMPS_REQUIS.' : '.BAZ_TEMPLATE, 'required', '', 'client');
	// Nettoyage de l'url avant les return
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
 	$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER, str_replace("&amp;", "&", $GLOBALS['_BAZAR_']['url']->getURL()), BAZ_ANNULER, array('class' => 'btn bouton_annuler'));
	$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER, array('class' => 'btn bouton_sauver'));
	$formtemplate->addGroup($buttons, 'groupe_boutons', null, '&nbsp;', 0);
	return $formtemplate;
}

/** baz_formulaire_des_listes() retourne le formulaire de saisie des listes
*
*   @return  Object    le code HTML
*/
function baz_formulaire_des_listes($mode, $valeursliste = '') {
	$GLOBALS['_BAZAR_']['url']->addQueryString('action_listes', $mode);
	
	//contruction du squelette du formulaire
	$formtemplate = new HTML_QuickForm('formulaire', 'post', preg_replace ('/&amp;/', '&', $GLOBALS['_BAZAR_']['url']->getURL()) );
	$GLOBALS['_BAZAR_']['url']->removeQueryString('action_listes');
	$squelette =& $formtemplate->defaultRenderer();
	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
    $squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label">'."\n".'{label}'.
    		                        '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
    								' </div>'."\n".'<div class="formulaire_input"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</div>'."\n".'</div>'."\n");
	$squelette->setElementTemplate( '<div class="groupebouton">{label}{element}</div>'."\n", 'groupe_boutons');
 	$squelette->setRequiredNoteTemplate("\n".'<div class="symbole_obligatoire">* {requiredNote}</div>'."\n");
 	
	//traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);
	
	//champs du formulaire
	if (isset($_GET['idliste'])) $formtemplate->addElement('hidden', 'NomWiki', $_GET['idliste']);
	$formtemplate->addElement('text', 'titre_liste', BAZ_NOM_LISTE, array('class' => 'input_texte'));
	$formtemplate->addRule('titre_liste', BAZ_CHAMPS_REQUIS.' : '.BAZ_NOM_LISTE, 'required', '', 'client');
	$html_valeurs_listes =  '<div class="formulaire_ligne">'."\n".
							'<div class="formulaire_label">'.BAZ_VALEURS_LISTE.'</div>'."\n".
							'<ul class="valeur_liste formulaire_input">'."\n";
	if (is_array($valeursliste)) {
		$i = 0;
		foreach($valeursliste as $ligneliste) {
			$i++;
			$html_valeurs_listes .= 
								'<li class="liste_ligne" id="row'.$i.'">'.
								'<img src="tools/bazar/presentation/images/arrow.png" alt="D&eacute;placer" width="16" height="16" class="handle" />'.
								'<input type="text" name="label['.$i.']" value="'.htmlspecialchars($ligneliste).'" class="input_texte" />'.
								'<input type="hidden" name="ancienlabel['.$i.']" value="'.htmlspecialchars($ligneliste).'" class="input_texte" />'.
								'<a href="#" class="BAZ_lien_supprimer suppression_label_liste"></a>'.
								'</li>'."\n";
		}
	} else {
		$html_valeurs_listes .= '<li class="liste_ligne" id="row1">'.
								'<img src="tools/bazar/presentation/images/arrow.png" alt="D&eacute;placer" width="16" height="16" class="handle" />'.
								'<input type="text" name="label[1]" class="input_texte" />'.
								'<a href="#" class="BAZ_lien_supprimer suppression_label_liste"></a>'.
								'</li>'."\n";
	}
						
	$html_valeurs_listes .= '</ul><a href="#" class="ajout_label_liste" title="'.BAZ_AJOUTER_LABEL_LISTE.'">'.BAZ_AJOUTER_LABEL_LISTE.'</a>'."\n".
							'</div>'."\n".
							'<div class="spacer"></div>'."\n".
							'<script type="text/javascript" src="tools/bazar/libs/jquery-ui-1.8.2.custom.min.js"></script>
							<script type="text/javascript">
							  $(document).ready(function() {
							    $(".valeur_liste").sortable({
							      handle : \'.handle\',
							      update : function () {
									$("#formulaire .valeur_liste input.input_texte[name^=\'label\']").each(function(i) {
										$(this).attr(\'name\', \'label[\'+(i+1)+\']\').
										parent(\'.liste_ligne\').attr(\'id\', \'row\'+(i+1)).
										find("input:hidden").attr(\'name\', \'ancienlabel[\'+(i+1)+\']\');
									});
							      }
							    });
							});
							</script>'."\n";
	$formtemplate->addElement('html', $html_valeurs_listes);
	// Nettoyage de l'url avant les return
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
 	$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER, str_replace("&amp;", "&", $GLOBALS['_BAZAR_']['url']->getURL()), BAZ_ANNULER, array('class' => 'btn bouton_annuler'));
	$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER, array('class' => 'btn bouton_sauver'));
	$formtemplate->addGroup($buttons, 'groupe_boutons', null, '&nbsp;', 0);
	return $formtemplate;
}


/** baz_gestion_formulaire() affiche le listing des formulaires et permet de les modifier
*
*   @return  string    le code HTML
*/
function baz_gestion_formulaire() {
	$res= '<h2>'.BAZ_MODIFIER_FORMULAIRES.'</h2>'."\n";

	// il y a un formulaire a modifier
	if (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='modif') {
		//recuperation des informations du type de formulaire
		$requete = 'SELECT * FROM '.BAZ_PREFIXE.'nature WHERE bn_id_nature='.$_GET['idformulaire'];
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

	//il y a des donnees pour ajouter un nouveau formulaire
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='new_v') {
		$requete = 'INSERT INTO '.BAZ_PREFIXE.'nature (`bn_id_nature` ,`bn_ce_i18n` ,`bn_label_nature` ,`bn_template` ,`bn_description` ,`bn_condition`, `bn_label_class` ,`bn_type_fiche`)' .
				   ' VALUES ('.baz_nextId(BAZ_PREFIXE.'nature', 'bn_id_nature', $GLOBALS['_BAZAR_']['db']).
                   ', "fr-FR", "'.$_POST["bn_label_nature"].'", "'.addslashes($_POST["bn_template"]).
				   '", "'.addslashes($_POST["bn_description"]).'", "'.addslashes($_POST["bn_condition"]).
				   '", "'.$_POST["bn_label_class"].'", "'.$_POST["bn_type_fiche"].'")';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$res .= '<div class="BAZ_info">'.BAZ_NOUVEAU_FORMULAIRE_ENREGISTRE.'</div>'."\n";

	//il y a des donnees pour modifier un formulaire
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='modif_v' && baz_a_le_droit('saisie_formulaire') ) {
		$requete =  'UPDATE '.BAZ_PREFIXE.'nature SET `bn_label_nature`="'.$_POST["bn_label_nature"].
				    '" ,`bn_template`="'.addslashes($_POST["bn_template"]).
				    '" ,`bn_description`="'.$_POST["bn_description"].
				    '" ,`bn_condition`="'.$_POST["bn_condition"].
					'" ,`bn_label_class`="'.$_POST["bn_label_class"].
				    '" ,`bn_type_fiche`="'.$_POST["bn_type_fiche"].'"'.
				    ' WHERE `bn_id_nature`='.$_POST["bn_id_nature"];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$res .= '<div class="BAZ_info">'.BAZ_FORMULAIRE_MODIFIE.'</div>'."\n";

	// il y a un id de formulaire à supprimer
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='delete' && baz_a_le_droit('saisie_formulaire')) {
		//suppression de l'entree dans '.BAZ_PREFIXE.'nature
		$requete = 'DELETE FROM '.BAZ_PREFIXE.'nature WHERE bn_id_nature='.$_GET['idformulaire'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}

		//suppression des fiches associees dans '.BAZ_PREFIXE.'fiche
		$requete = 'DELETE FROM '.BAZ_PREFIXE.'fiche WHERE bf_ce_nature='.$_GET['idformulaire'];
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}

		$res .= '<div class="BAZ_info">'.BAZ_FORMULAIRE_ET_FICHES_SUPPRIMES.'</div>'."\n";
	}

	// affichage de la liste des templates à modifier ou supprimer (on l'affiche dans tous les cas, sauf cas de modif de formulaire)
	if (!isset($_GET['action_formulaire']) || ($_GET['action_formulaire']!='modif' && $_GET['action_formulaire']!='new') ) {
		$res .= '<div class="BAZ_info">'.BAZ_INTRO_MODIFIER_FORMULAIRE.'</div>'."\n";

		//requete pour obtenir l'id et le label des types d'annonces
		$requete = 'SELECT bn_id_nature, bn_label_nature, bn_type_fiche '.
		           'FROM '.BAZ_PREFIXE.'nature WHERE 1 ORDER BY bn_type_fiche';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$liste=''; $type_formulaire='';
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			if ($type_formulaire!=$ligne['bn_type_fiche']) {
				if ($type_formulaire!='') $liste .= '</ul><br />'."\n";
				$liste .= '<h3>'.$ligne['bn_type_fiche'].'</h3>'."\n".
				'<ul class="BAZ_liste">'."\n";
				$type_formulaire = $ligne['bn_type_fiche'];
			}
			$lien_formulaire=clone($GLOBALS['_BAZAR_']['url']);
			$liste .= '<li>';
			$lien_formulaire->addQueryString('action_formulaire', 'delete');
			$lien_formulaire->addQueryString('idformulaire', $ligne['bn_id_nature']);
			if (baz_a_le_droit('saisie_formulaire'))  {
				$liste .= '<a class="BAZ_lien_supprimer" href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FORMULAIRE.' ?\');"></a>'."\n";
			}
			$lien_formulaire->removeQueryString('action_formulaire');
			$lien_formulaire->addQueryString('action_formulaire', 'modif');
			if (baz_a_le_droit('saisie_formulaire'))  {
				$liste .= '<a class="BAZ_lien_modifier" href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'">'.$ligne['bn_label_nature'].'</a>'."\n";
			} else {
				$liste .= $ligne['bn_label_nature']."\n";
			}
			$lien_formulaire->removeQueryString('action_formulaire');
			$lien_formulaire->removeQueryString('idformulaire');

			$liste .='</li>'."\n";
		}
		if ($liste!='') $res .= $liste.'</ul><br />'."\n";

		//ajout du lien pour creer un nouveau formulaire
		if (baz_a_le_droit('saisie_formulaire')) {
			$lien_formulaire=clone($GLOBALS['_BAZAR_']['url']);
			$lien_formulaire->addQueryString('action_formulaire', 'new');
			$res .= '<a class="BAZ_lien_nouveau" href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'">'.BAZ_NOUVEAU_FORMULAIRE.'</a>'."\n";
		}

	}
	return $res;
}


/** baz_gestion_listes() affiche le listing des listes et permet de les modifier
*
*   @return  string    le code HTML
*/
function baz_gestion_listes() {
	$res= '<h2>'.BAZ_MODIFIER_LISTES.'</h2>'."\n";

	// il y a un formulaire a modifier
	if (isset($_GET['action_listes']) && $_GET['action_listes']=='modif') {
		//recuperation des informations de la liste
		$page = $GLOBALS["wiki"]->LoadPage($_GET['idliste']);
		$ligne = json_decode( $page['body'], true);
		$valeursliste = array_map('utf8_decode', $ligne['label']);
		$formulaire = baz_formulaire_des_listes('modif_v', $valeursliste);		
		$formulaire->setDefaults(array("titre_liste" => utf8_decode($ligne['titre_liste'])));
		$res .= $formulaire->toHTML();

	//il y a une nouvelle liste a saisir
	} elseif (isset($_GET['action_listes']) && $_GET['action_listes']=='new') {
		$formulaire = baz_formulaire_des_listes('new_v');
		$res .= $formulaire->toHTML();

	//il y a des donnees pour ajouter une nouvelle liste
	} elseif (isset($_GET['action_listes']) && $_GET['action_listes']=='new_v') {
		unset($_POST["valider"]);
		$nomwikiliste = genere_nom_wiki($_POST['titre_liste']);
		//on supprime les valeurs vides et on encode en utf-8 pour réussir à encoder en json
		$i = 1;
		$valeur["label"] = array();
		foreach ($_POST["label"] as $label) {
			if ($label!=NULL || $label!='') {
				$valeur["label"][$i] = $label;
				$i++;
			}
		}
		$valeur["label"] = array_map("utf8_encode", $valeur["label"]);
		$valeur["titre_liste"] = utf8_encode($_POST["titre_liste"]);
		
		//on sauve les valeurs d'une liste dans une PageWiki, pour garder l'historique
		$GLOBALS["wiki"]->SavePage($nomwikiliste, json_encode($valeur));
		//on cree un triple pour spécifier que la page wiki créée est une liste
		$GLOBALS["wiki"]->InsertTriple($nomwikiliste, 'http://outils-reseaux.org/_vocabulary/type', 'liste', '', '');
	
		$res .= '<div class="BAZ_info">'.BAZ_NOUVELLE_LISTE_ENREGISTREE.'</div>'."\n";

	//il y a des donnees pour modifier une liste
	} elseif (isset($_GET['action_listes']) && $_GET['action_listes']=='modif_v' && baz_a_le_droit('saisie_liste') ) {
		unset($_POST["valider"]);
		//on supprime les valeurs vides et on encode en utf-8 pour réussir à encoder en json
		$i = 1;
		$valeur["label"] = array();
		foreach ($_POST["label"] as $label) {
			if ($label!=NULL || $label!='') {
				$valeur["label"][$i] = $label;
				$i++;
			}
		}
		$valeur["label"] = array_map("utf8_encode", $valeur["label"]);
		$valeur["titre_liste"] = utf8_encode($_POST["titre_liste"]);

		//on vérifie si les valeurs des listes ont changées afin de garder de l'intégrité de la base des fiches
		foreach ($_POST["ancienlabel"] as $key => $value) {
			//si la valeur de la liste a été changée, on répercute les changements pour les fiches contenant cette valeur
			if ( isset($_POST["label"][$key]) && $value != $_POST["label"][$key] ) {
				//TODO: fonction baz_modifier_metas_liste($_POST['NomWiki'], $value, $_POST['label'][$key]);
			}		
		}
		
		//on supprime les valeurs des listes supprimées des fiches possédants ces valeurs
		foreach ($_POST["a_effacer_ancienlabel"] as $key => $value) {
			//TODO: fonction baz_effacer_metas_liste($_POST['NomWiki'], $value);
		}
			
		//on sauve les valeurs d'une liste dans une PageWiki, pour garder l'historique
		$GLOBALS["wiki"]->SavePage($_POST['NomWiki'], json_encode($valeur));
	
		$res .= '<div class="BAZ_info">'.BAZ_LISTE_MODIFIEE.'</div>'."\n";

	// il y a un id de liste à supprimer
	} elseif (isset($_GET['action_listes']) && $_GET['action_listes']=='delete' && baz_a_le_droit('saisie_liste')) {
		$GLOBALS["wiki"]->DeleteOrphanedPage($_GET['idliste']);
		$sql = 'DELETE FROM ' . BAZ_PREFIXE . 'triples '
			. 'WHERE resource = "' . addslashes($_GET['idliste']) . '" ';
		$GLOBALS["wiki"]->Query($sql);
		
		$res .= '<div class="BAZ_info">'.BAZ_LISTES_SUPPRIMEES.'</div>'."\n";
	}

	// affichage de la liste des templates à modifier ou supprimer (on l'affiche dans tous les cas, sauf cas de modif de formulaire)
	if (!isset($_GET['action_listes']) || ($_GET['action_listes']!='modif' && $_GET['action_listes']!='new') ) {
		$res .= '<div class="BAZ_info">'.BAZ_INTRO_MODIFIER_LISTE.'</div>'."\n";

		//requete pour obtenir l'id et le label des types d'annonces
		$requete = 'SELECT resource FROM '.BAZ_PREFIXE.'triples WHERE property="http://outils-reseaux.org/_vocabulary/type" AND value="liste" ORDER BY resource';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$liste = '';
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			$page = $GLOBALS["wiki"]->LoadPage( $ligne['resource']);
			$valliste = json_decode( $page['body'], true);
			$valeursliste = array_map('utf8_decode', $valliste['label']);

			$lien_formulaire = clone($GLOBALS['_BAZAR_']['url']);
			$liste .= '<li>';
			$lien_formulaire->addQueryString('action_listes', 'delete');
			$lien_formulaire->addQueryString('idliste', $ligne['resource']);
			if (baz_a_le_droit('saisie_liste'))  {
				$liste .= '<a class="BAZ_lien_supprimer" href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_LISTE.' ?\');"></a>'."\n";
			}
			$lien_formulaire->removeQueryString('action_listes');
			$lien_formulaire->addQueryString('action_listes', 'modif');
			$elements_liste = '';
			foreach ($valeursliste as $val) { 
				$elements_liste .= '<option>'.$val.'</option>';
			}
			if ($elements_liste != '') {
				$affichage_liste = '&nbsp;- '.BAZ_VALEURS_LISTE.' :&nbsp;<select id="liste_'.$ligne['resource'].'">'."\n".
				'<option>'.BAZ_CHOISIR.'</option>'."\n".
				$elements_liste."\n".
				'</select>'."\n";
			} else {
				$affichage_liste = '';
			}
			if (baz_a_le_droit('saisie_liste'))  {
				$liste .= '<a class="BAZ_lien_modifier" href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'">'.utf8_decode($valliste['titre_liste']).'</a>'.$affichage_liste."\n";
			} else {
				$liste .= utf8_decode($valliste['titre_liste']).$affichage_liste."\n";
			}
			$lien_formulaire->removeQueryString('action_listes');
			$lien_formulaire->removeQueryString('idliste');

			$liste .='</li>'."\n";
		}
		if ($liste!='') $res .= '<ul class="BAZ_liste">'.$liste.'</ul>'."\n";

		//ajout du lien pour creer un nouveau formulaire
		if (baz_a_le_droit('saisie_liste')) {
			$lien_formulaire=clone($GLOBALS['_BAZAR_']['url']);
			$lien_formulaire->addQueryString('action_listes', 'new');
			$res .= '<a class="BAZ_lien_nouveau" href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'">'.BAZ_NOUVELLE_LISTE.'</a>'."\n";
		}

	}
	return $res;
}


/** baz_valeurs_fiche() - Renvoie un tableau avec les valeurs par defaut du formulaire d'inscription
*
* @param    integer Identifiant de la fiche
*
* @return   array   Valeurs enregistrees pour cette fiche
*/
function baz_valeurs_fiche($idfiche = '') {
	if ($idfiche != '') {
		
		//infos dans bazar fiche
		$requete = 'SELECT * FROM '.BAZ_PREFIXE.'fiche WHERE bf_id_fiche="'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$valeurs_fiche = $resultat->fetchRow(DB_FETCHMODE_ASSOC) ;
		
		//metadonnees textelong
		$requete = 'SELECT property, value FROM '.BAZ_PREFIXE.'triples WHERE resource = "'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$valeurs_meta_textelong = array();
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			$valeurs_meta_textelong[$ligne['property']] = stripslashes($ligne['value']);
		}
		$valeurs_fiche = array_merge($valeurs_fiche, $valeurs_meta_textelong);
		
		//metadonnees texte
		$requete = 'SELECT bfvt_id_element_form, bfvt_texte FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_ce_fiche="'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		$valeurs_meta_texte = array();
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			//pour les checkbox, il peut y avoir plusieures clé identiques, on les regroupe
			if (array_key_exists($ligne['bfvt_id_element_form'], $valeurs_meta_texte))  {
				$valeurs_meta_texte[$ligne['bfvt_id_element_form']] = $valeurs_meta_texte[$ligne['bfvt_id_element_form']].','.stripslashes($ligne['bfvt_texte']);
			} else {
				$valeurs_meta_texte[$ligne['bfvt_id_element_form']] = stripslashes($ligne['bfvt_texte']);
			}
		}
		$valeurs_fiche = array_merge($valeurs_fiche, $valeurs_meta_texte);
		
		//cas ou on ne trouve pas les valeurs id_fiche et id_typeannonce
		if (!isset($valeurs_fiche['id_fiche'])) $valeurs_fiche['id_fiche'] = $idfiche;
		if (!isset($valeurs_fiche['id_typeannonce'])) $valeurs_fiche['id_typeannonce'] = $valeurs_fiche['bf_ce_nature'];
		return $valeurs_fiche;
	} 
	else {
		return false;
	}
	
}

/** baz_valeurs_type_de_fiche() - Initialise les valeurs globales pour le type de fiche choisi
*
* @param    integer Identifiant du type de fiche
*
* @return   void
*/
function baz_valeurs_type_de_fiche($idtypefiche) {
	$requete = 'SELECT * FROM '.BAZ_PREFIXE.'nature WHERE bn_id_nature = '.$idtypefiche;
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
	}
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
	return $ligne;
}


/** baz_nextId () Renvoie le prochain identifiant numerique libre d'une table
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

/** baz_titre_wiki() Renvoie la chaine de caractere sous une forme compatible avec wikini
*
*   @param  string  mot à transformer (enlever accents, espaces)
*
*   return  string  mot transformé
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



/**  baz_voir_fiches() - Permet de visualiser en detail une liste de fiche  au format XHTML
*
* @global boolean Rajoute des informations internes a l'application (date de modification, lien vers la page de départ de l'appli)
* @global integer Tableau d(Identifiant des fiches a afficher
*
* @return   string  HTML
*/
function baz_voir_fiches($danslappli, $idfiches=array()) {
	$res='';
	foreach($idfiches as $idfiche) {
			$res.=baz_voir_fiche($danslappli, $idfiche);
	}
	return $res;
}


/**  baz_voir_fiche() - Permet de visualiser en detail une fiche  au format XHTML
*
* @global boolean Rajoute des informations internes a l'application (date de modification, lien vers la page de depart de l'appli) si a 1
* @global integer Identifiant de la fiche a afficher ou mixed un tableau avec toutes les valeurs stockées pour la fiche
*
* @return   string  HTML
*/
function baz_voir_fiche($danslappli, $idfiche) {
	//si c'est un tableau avec les valeurs de la fiche
	if (is_array($idfiche)) {
		//on déplace le tableau et on donne la bonne valeur à id fiche
		$valeurs_fiche = $idfiche;
		$idfiche = $valeurs_fiche['id_fiche'];
		$tab_nature = baz_valeurs_type_de_fiche($valeurs_fiche["id_typeannonce"]);
	}
	else {
		//on récupere les valeurs de la fiche
		$valeurs_fiche = baz_valeurs_fiche($idfiche);
		//on récupere les infos du type de fiche
		$tab_nature = baz_valeurs_type_de_fiche($valeurs_fiche["bf_ce_nature"]);
	}
	$res='';
	
	//pour les stats, on ajoute une vue pour la fiche
	if ($danslappli==1) {
		$requete = 'UPDATE '.BAZ_PREFIXE.'fiche SET bf_nb_consultations=bf_nb_consultations+1 WHERE bf_id_fiche="'.$idfiche.'"';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	}
	
	$url= clone($GLOBALS['_BAZAR_']['url']);
	$url->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
	$url->addQueryString('id_fiche', $idfiche);
	$url = preg_replace ('/&amp;/', '&', $url->getURL()) ;

	//debut de la fiche
	$res .= '<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$tab_nature['bn_label_class'].'">'."\n";
	
	//affiche le type de fiche pour la vue consulter
	if ($danslappli==1) {$res .= '<h2 class="BAZ_titre BAZ_titre_'.$tab_nature['bn_label_class'].'">'.$tab_nature['bn_label_nature'].'</h2>'."\n";}

	//Partie la plus importante : apres avoir récupéré toutes les valeurs de la fiche, on génére l'affichage html de cette dernière
	$tableau = formulaire_valeurs_template_champs($tab_nature['bn_template']);
	for ($i=0; $i<count($tableau); $i++) {
		$res .= $tableau[$i][0]($formtemplate, $tableau[$i], 'html', $valeurs_fiche);
	}

	//informations complementaires (id fiche, etat publication,... )
	if ($danslappli==1) {
		$res .= '<div class="BAZ_fiche_info BAZ_fiche_info_'.$tab_nature['bn_label_class'].'">'."\n";
		$res .= '<span class="BAZ_nb_vues BAZ_nb_vues_'.$tab_nature['bn_label_class'].'">'.BAZ_FICHE_NUMERO.$idfiche;
		//affichage du redacteur de la fiche
		if ($valeurs_fiche['bf_ce_utilisateur']!='')
		{
			$res .= '<span class="auteur_fiche">'.BAZ_ECRITE.$valeurs_fiche['bf_ce_utilisateur'].'</span>'."\n";
		} 
		$res .= BAZ_NB_VUS.$valeurs_fiche['bf_nb_consultations'].BAZ_FOIS.'</span>'."\n";
		
		//affichage de l'état de validation
		$res .= '<br />';
		if ($valeurs_fiche['bf_statut_fiche']==1) {
			if ($valeurs_fiche['bf_date_debut_validite_fiche'] != '0000-00-00' && $valeurs_fiche['bf_date_fin_validite_fiche'] != '0000-00-00') {
			$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$tab_nature['bn_label_class'].'">'.BAZ_PUBLIEE.':</span> '.BAZ_DU.
					' '.strftime('%d.%m.%Y &agrave; %H:%M', strtotime($valeurs_fiche['bf_date_debut_validite_fiche'])).' '.
					BAZ_AU.' '.strftime('%d.%m.%Y &agrave; %H:%M', strtotime($valeurs_fiche['bf_date_fin_validite_fiche'])).'<br />'."\n";
			}
		}
		else {
			$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$tab_nature['bn_label_class'].'">'.BAZ_PUBLIEE.':</span> '.BAZ_NON.'<br />'."\n";
		}
		
		//affichage des infos et du lien pour la mise a jour de la fiche
		$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$tab_nature['bn_label_class'].' date_creation">'.BAZ_DATE_CREATION.':</span> '.strftime('%d.%m.%Y &agrave; %H:%M',strtotime($valeurs_fiche['bf_date_creation_fiche'])).'.'."\n";
		$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$tab_nature['bn_label_class'].' date_mise_a_jour">'.BAZ_DATE_MAJ.':</span> '.strftime('%d.%m.%Y &agrave; %H:%M',strtotime($valeurs_fiche['bf_date_maj_fiche'])).'.'."\n";

		if ( baz_a_le_droit( 'saisie_fiche', $valeurs_fiche['bf_ce_utilisateur'] ) ) {
			$res .= '<div class="BAZ_actions_fiche BAZ_actions_fiche_'.$tab_nature['bn_label_class'].'">'."\n";
			$res .= '<ul>'."\n";
			
			//ajouter action de validation (pour les admins)
			if ( baz_a_le_droit( 'valider_fiche' ) ) {
				$lien_publie = clone($GLOBALS['_BAZAR_']['url']);
				$lien_publie->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);				
				$lien_publie->addQueryString('id_fiche', $idfiche);
				if ($valeurs_fiche['bf_statut_fiche']==0||$valeurs_fiche['bf_statut_fiche']==2) {
					$lien_publie->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_PUBLIER);
					$label_publie=BAZ_VALIDER_LA_FICHE;
					$class_publie='_valider';
				} elseif ($valeurs_fiche['bf_statut_fiche']==1) {
					$lien_publie->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_PAS_PUBLIER);
					$label_publie=BAZ_INVALIDER_LA_FICHE;
					$class_publie='_invalider';
				}
				$res .= '<li><a class="BAZ_lien'.$class_publie.'" href="'.str_replace('&', '&amp;', $lien_publie->getURL()).'">'.$label_publie.'</a></li>'."\n";
				$lien_publie->removeQueryString('publiee');
			}
			//lien modifier la fiche
			$lien_modifier = clone($GLOBALS['_BAZAR_']['url']);
			$lien_modifier->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
			$lien_modifier->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
			$lien_modifier->addQueryString('id_fiche', $idfiche);
			$res .= '<li><a class="BAZ_lien_modifier" href="'.str_replace('&', '&amp;', $lien_modifier->getURL()).'">'.BAZ_MODIFIER_LA_FICHE.'</a></li>'."\n";
			
			//lien supprimer la fiche
			$lien_supprimer=$GLOBALS['_BAZAR_']['url'];
			$lien_supprimer->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
			$lien_supprimer->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
			$lien_supprimer->addQueryString('id_fiche', $idfiche);
			$res .= '<li><a class="BAZ_lien_supprimer" href="'.str_replace('&', '&amp;', $lien_supprimer->getURL()).'" onclick="javascript:return confirm(\''.
				BAZ_CONFIRM_SUPPRIMER_FICHE.'\');">'.BAZ_SUPPRIMER_LA_FICHE.'</a></li>'."\n";
			$res .= '</ul>'."\n";
			$res .= '</div><!-- fin div BAZ_actions_fiche -->'."\n";
		}
		$res .= '</div><!-- fin div BAZ_fiche_info -->'."\n";
		
	}
	
	//fin de la fiche
	$res .= '</div><!-- fin div BAZ_cadre_fiche  -->'."\n";	
	
	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_commentaire');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
	return $res ;
}


/** baz_a_le_droit() Renvoie true si la personne à le droit d'accèder à la fiche
*
*   @param  string  type de demande (voir, saisir, modifier)
*   @param  string  identifiant, soit d'un formulaire, soit d'une fiche, soit d'un type de fiche
*
*   return  boolean	vrai si l'utilisateur a le droit, faux sinon
*/
function baz_a_le_droit( $demande = 'saisie_fiche', $id = '' ) {
    //cas d'une personne identifiée
    $nomwiki = $GLOBALS['wiki']->getUser();
    if (is_array($nomwiki)) {
		//l'administrateur peut tout faire
		if ($GLOBALS['wiki']->UserIsInGroup('admins')) {
			return true;
		}
		else {
			//pour la saisie d'une fiche, si la personne identifiée est l'auteur ou que la fiche n'a pas d'auteur, on peut l'éditer
			if ($demande == 'saisie_fiche') {
				if ($id == $nomwiki['name'] || $id == '' ) return true;
				else return false;
			}
			//pour la validation d'une fiche, pour l 'instant seul les admins peuvent valider une fiche
			elseif ($demande == 'valider_fiche') {
				return false;
			}
			//pour la saisie d'un formulaire ou d'une liste, pour l 'instant seul les admins ont le droit
			elseif ($demande == 'saisie_formulaire' || $demande == 'saisie_liste') {
				return false;
			}
			//pour la liste des fiches saisies, il suffit d'être identifié
			elseif ($demande == 'voir_mes_fiches') {
				return true;
			}
			//les autres demandes sont réservées aux admins donc non!
			else {
				return false;
			}
		}
	} 
	//cas d'une personne non identifiée
	else {
		return false;
	}
	
    
    
}


function remove_accents( $string )
{
    $string = htmlentities($string);
    return preg_replace("/&([a-z])[a-z]+;/i","$1",$string);
}

function genere_nom_wiki($nom, $occurence=1)
{	
	//les noms wiki ne doivent pas dépasser les 50 caracteres, on coupe à 48, histoire de pouvoir ajouter un chiffre derrière si nom wiki déja existant
	//plus traitement des accents
	//plus on met des majuscules au début de chaque mot et on fait sauter les espaces
	$temp = explode(" ", ucwords(strtolower(remove_accents(substr($nom, 0, 47)))));

	$final='';
	foreach($temp as $mot)
	{
		//on vire d'éventuels autres caractères spéciaux
		$final .= ereg_replace("[^a-zA-Z0-9]","",trim($mot));
	}

	//on verifie qu'il y a au moins 2 majuscules, sinon on en rajoute une à la fin
	$var = ereg_replace('[^A-Z]','',$final);
	if (strlen($var)<2)
	{
		$last = ucfirst(substr($final, strlen($final) - 1));
		$final = substr($final, 0, -1).$last;
	}

 	// sinon retour du nom formaté
	if (!is_array($GLOBALS['wiki']->LoadPageById($final))) {
		return $final;
	} else {
		$occurence++;
		return genere_nom_wiki($final, $occurence);
	}
	
}

/** gen_RSS() - generer un fichier de flux RSS par type d'annonce
*
* @param   string Le type de l'annonce (laisser vide pour tout type d'annonce)
* @param   integer Le nombre d'annonces a regrouper dans le fichier XML (laisser vide pour toutes)
* @param   integer L'identifiant de l'emetteur (laisser vide pour tous)
* @param   integer L'etat de validation de l'annonce (laisser 1 pour les annonces validees, 0 pour les non-validees)
* @param   string La requete SQL personnalisee
* @param   integer La categorie des fiches bazar
*
* @return  string Le code du flux RSS
*/
function gen_RSS($typeannonce='', $nbitem='', $emetteur='', $valide=1, $requeteSQL='', $requeteSQLFrom = '', $requeteWhereListe = '', $categorie_nature='') {
	// generation de la requete MySQL personnalisee
	$req_where=0;
	$requete = 'SELECT DISTINCT bf_id_fiche, bf_titre, bf_date_debut_validite_fiche, bf_description,  bn_label_nature, bf_date_creation_fiche '.
				'FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature '.$requeteSQLFrom.' WHERE '.$requeteWhereListe;
	if ($valide!=2) {
		$requete .= 'bf_statut_fiche='.$valide;
		$req_where=1;
	}
	$nomflux=html_entity_decode(BAZ_DERNIERE_ACTU);
	if (!is_array ($typeannonce) && $typeannonce!='' and $typeannonce!='toutes') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_nature='.$typeannonce.' and bf_ce_nature=bn_id_nature ';;
		$req_where=1;
		//le nom du flux devient le type d'annonce
		$requete_nom_flux = 'select bn_label_nature from '.BAZ_PREFIXE.'nature where bn_id_nature = '.$typeannonce;
		$nomflux = $GLOBALS['_BAZAR_']['db']->getOne($requete_nom_flux) ;
	}
	// Cas ou il y plusieurs type d annonce demande
	if (is_array ($typeannonce)) {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_nature IN (' ;
		$chaine = '';
		foreach ($typeannonce as $valeur) $chaine .= '"'.$valeur.'",' ;
		$requete .= substr ($chaine, 0, strlen ($chaine)-1) ;
		$requete .= ') and bf_ce_nature=bn_id_nature ';
	}
	if ($valide!=0) {
		if ($req_where==1) {
			$requete .= ' AND ';
		}
		$requete .= '(bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00")'.
						' AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") AND bn_id_nature=bf_ce_nature';
	}
	else $nomflux .= BAZ_A_MODERER;
	if ($emetteur!='' && $emetteur!='tous') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bf_ce_utilisateur='.$emetteur;
		$req_where=1;
		//requete pour afficher le nom de la structure
		$requetenom = 'SELECT '.BAZ_CHAMPS_NOM.', '.BAZ_CHAMPS_PRENOM.' FROM '.
						BAZ_ANNUAIRE.' WHERE '.BAZ_CHAMPS_ID.'='.$emetteur;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requetenom) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
		$nomflux .= ' ('.$ligne[BAZ_CHAMPS_NOM].' '.$ligne[BAZ_CHAMPS_PRENOM].')';
	}
	if ($requeteSQL!='') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= '('.$requeteSQL.')';
		$req_where=1;
	}
	if ($categorie_nature!='toutes') {
		if ($req_where==1) {$requete .= ' AND ';}
		$requete .= 'bn_type_fiche ="'.$categorie_nature.'" AND bf_ce_nature=bn_id_nature ';
		$req_where=1;
	}

	$requete .= ' ORDER BY   bf_date_creation_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC';
	if ($nbitem!='') {$requete .= ' LIMIT 0,'.$nbitem;}
	else {$requete .= ' LIMIT 0,50';}
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	//echo $requete;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}

	require_once 'XML/Util.php' ;

	// passage en utf-8 --julien
	// --

	// setlocale() pour avoir les formats de date valides (w3c) --julien
	setlocale(LC_TIME, "C");

	$xml = XML_Util::getXMLDeclaration('1.0', 'UTF-8', 'yes') ;
	$xml .= "\r\n  ";
	$xml .= XML_Util::createStartElement ('rss', array('version' => '2.0', 'xmlns:atom' => "http://www.w3.org/2005/Atom")) ;
	$xml .= "\r\n    ";
	$xml .= XML_Util::createStartElement ('channel');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode($nomflux)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('link', null, utf8_encode(html_entity_decode(BAZ_RSS_ADRESSESITE)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('description', null, utf8_encode(html_entity_decode(BAZ_RSS_DESCRIPTIONSITE)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('language', null, 'fr-FR');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('copyright', null, 'Copyright (c) '. date('Y') .' '. utf8_encode(html_entity_decode(BAZ_RSS_NOMSITE)));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('lastBuildDate', null, strftime('%a, %d %b %Y %H:%M:%S GMT'));
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('docs', null, 'http://www.stervinou.com/projets/rss/');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('category', null, BAZ_RSS_CATEGORIE);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('managingEditor', null, BAZ_RSS_MANAGINGEDITOR);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('webMaster', null, BAZ_RSS_WEBMASTER);
	$xml .= "\r\n      ";
	$xml .= XML_Util::createTag ('ttl', null, '60');
	$xml .= "\r\n      ";
	$xml .= XML_Util::createStartElement ('image');
	$xml .= "\r\n        ";
		$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode($nomflux)));
		$xml .= "\r\n        ";
		$xml .= XML_Util::createTag ('url', null, BAZ_RSS_LOGOSITE);
		$xml .= "\r\n        ";
		$xml .= XML_Util::createTag ('link', null, BAZ_RSS_ADRESSESITE);
		$xml .= "\r\n      ";
	$xml .= XML_Util::createEndElement ('image');
	if ($resultat->numRows() > 0) {
		// Creation des items : titre + lien + description + date de publication
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			$xml .= "\r\n      ";
			$xml .= XML_Util::createStartElement ('item');
			$xml .= "\r\n        ";
			$xml .= XML_Util::createTag('title', null, encoder_en_utf8(html_entity_decode(stripslashes($ligne['bf_titre']))));
			$xml .= "\r\n        ";
			$lien=$GLOBALS['_BAZAR_']['url'];
			$lien->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
			$lien->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
			$lien->addQueryString('id_fiche', $ligne['bf_id_fiche']);
			$xml .= XML_Util::createTag ('link', null, '<![CDATA['.$lien->getURL().']]>' );
			$xml .= "\r\n        ";
			$xml .= XML_Util::createTag ('guid', null, '<![CDATA['.$lien->getURL().']]>' );
			$xml .= "\r\n        ";
			$tab = explode("wakka.php?wiki=",$lien->getURL());
			$xml .= XML_Util::createTag ('description', null, '<![CDATA['.encoder_en_utf8(html_entity_decode(baz_voir_fiche(0, $ligne['bf_id_fiche']))).']]>' );
			$xml .= "\r\n        ";
			if ($ligne['bf_date_debut_validite_fiche'] != '0000-00-00' &&
			$ligne['bf_date_debut_validite_fiche']>$ligne['bf_date_creation_fiche']) {
				$date_pub =  $ligne['bf_date_debut_validite_fiche'];
			} else $date_pub = $ligne['bf_date_creation_fiche'] ;
			$xml .= XML_Util::createTag ('pubDate', null, strftime('%a, %d %b %Y %H:%M:%S GMT',strtotime($date_pub)));
			$xml .= "\r\n      ";
			$xml .= XML_Util::createEndElement ('item');
		}
	}
	else {//pas d'annonces
		$xml .= "\r\n      ";
		$xml .= XML_Util::createStartElement ('item');
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode(BAZ_PAS_DE_FICHES)));
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('link', null, '<![CDATA['.$GLOBALS['_BAZAR_']['url']->getUrl().']]>' );
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('guid', null, '<![CDATA['.$GLOBALS['_BAZAR_']['url']->getUrl().']]>' );
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('description', null, utf8_encode(html_entity_decode(BAZ_PAS_DE_FICHES)));
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('pubDate', null, strftime('%a, %d %b %Y %H:%M:%S GMT',strtotime("01/01/%Y")));
		$xml .= "\r\n      ";
		$xml .= XML_Util::createEndElement ('item');
	}
	$xml .= "\r\n    ";
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FLUX_RSS);
//	$xml .= utf8_encode(html_entity_decode('<atom:link href="'.$GLOBALS['_BAZAR_']['url']->getUrl().'" rel="self" type="application/rss+xml" />'."\r\n  "));
	$xml .= XML_Util::createEndElement ('channel');
	$xml .= "\r\n  ";
	$xml .= XML_Util::createEndElement('rss') ;

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');

	return $xml;
}


/** baz_rechercher() Formate la liste de toutes les annonces actuelles
*
*   @return  string    le code HTML a afficher
*/
function baz_rechercher($typeannonce='toutes',$categorienature='toutes') {
	//creation du lien pour le formulaire de recherche
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_MOTEUR_RECHERCHE);
	if (isset($_REQUEST['recherche_avancee'])) $GLOBALS['_BAZAR_']['url']->addQueryString ('recherche_avancee', $_REQUEST['recherche_avancee']);
	$lien_formulaire = preg_replace ('/&amp;/', '&', $GLOBALS['_BAZAR_']['url']->getURL()) ;
	$formtemplate = new HTML_QuickForm('formulaire', 'post', $lien_formulaire) ;
	
/*
	$formtemplate = new HTML_QuickForm('formulaire', 'get', $_SERVER["PHP_SELF"]) ;
	$formtemplate->addElement ('hidden', 'wiki', $_GET['wiki']) ;
	$formtemplate->addElement ('hidden', BAZ_VARIABLE_ACTION, BAZ_MOTEUR_RECHERCHE) ;
	$formtemplate->addElement ('hidden', BAZ_VARIABLE_VOIR, $_GET[BAZ_VARIABLE_VOIR]) ;
*/
	
	$squelette =& $formtemplate->defaultRenderer();
	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
	$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label">'."\n".'<!-- BEGIN required --><span class="symbole_obligatoire">*&nbsp;</span><!-- END required -->'."\n".'{label} :</div>'."\n".
								'<div class="formulaire_input"> '."\n".'{element}'."\n".
								'<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
								'</div>'."\n".'</div>'."\n");
	$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".'<div class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
								'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</div>'."\n".'</div>'."\n", 'accept_condition');
	$squelette->setElementTemplate( '<div class="grouperecherche">{label}{element}</div>'."\n", 'groupe_recherche');
	$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label_select">'."\n".'{label} :</div>'."\n".
									'<div class="formulaire_select"> '."\n".'{element}'."\n".'</div>'."\n".
									'</div>', 'select');
	$squelette->setRequiredNoteTemplate("\n".'<div class="symbole_obligatoire">* {requiredNote}</div>'."\n");
	//Traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);


	
	//cas du formulaire de recherche proposant de chercher parmis tous les types d'annonces
	//requete pour obtenir l'id et le label des types d'annonces
	$requete = 'SELECT bn_id_nature, bn_label_nature, bn_template FROM '.BAZ_PREFIXE.'nature WHERE ';
	if ($categorienature!='toutes') $requete .= 'bn_type_fiche="'.$categorienature.'" ';
	else $requete .= '1 ';
	if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
	$requete .=' ORDER BY bn_label_nature ASC';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	//on recupere le nb de types de fiches, pour plus tard
	$nb_type_de_fiches=$resultat->numRows();
	$type_annonce_select['toutes']=BAZ_TOUS_TYPES_FICHES;
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$type_annonce_select[$ligne['bn_id_nature']] = $ligne['bn_label_nature'];
		$tableau_typeannonces[] = $ligne['bn_id_nature'] ;
	}
	if ($nb_type_de_fiches>1 && $typeannonce=='toutes' && BAZ_AFFICHER_FILTRE_MOTEUR) {
		$res = '<h2 class="titre_consulter">'.BAZ_RECHERCHE_AVANCEE.'</h2>'."\n";
	}
	//cas du type d'annonces predefini
	else {
		if ($nb_type_de_fiches==1) {
			$GLOBALS['_BAZAR_']['id_typeannonce']=end(array_keys($type_annonce_select));
			$tab_nature = baz_valeurs_type_de_fiche($GLOBALS['_BAZAR_']['id_typeannonce']);
			$GLOBALS['_BAZAR_']['typeannonce']=$tab_nature['bn_label_nature'];
			$GLOBALS['_BAZAR_']['condition']=$tab_nature['bn_condition'];
			$GLOBALS['_BAZAR_']['template']=$tab_nature['bn_template'];
			$GLOBALS['_BAZAR_']['commentaire']=$tab_nature['bn_commentaire'];
			$GLOBALS['_BAZAR_']['appropriation']=$tab_nature['bn_appropriation'];
			$GLOBALS['_BAZAR_']['class']=$tab_nature['bn_label_class'];
		}
		$res = '<h2 class="titre_consulter">'.BAZ_RECHERCHER_2POINTS.' '.$GLOBALS['_BAZAR_']['typeannonce'].'</h2>'."\n";
	}

	if ($nb_type_de_fiches>1)
	{
	  $option=array('onchange' => 'javascript:this.form.submit();');
	  $formtemplate->addElement ('select', 'id_typeannonce', BAZ_TYPE_FICHE, $type_annonce_select, $option) ;
	  if (isset($_REQUEST['id_typeannonce'])) {
		  $defauts=array('id_typeannonce'=>$_REQUEST['id_typeannonce']);
		  $formtemplate->setDefaults($defauts);
	  }
	}

	// Ajout des options si un type de fiche a ete choisie
	if ( (isset($_REQUEST['id_typeannonce']) && $_REQUEST['id_typeannonce'] != 'toutes') ||
	     (isset($GLOBALS['_BAZAR_']['id_typeannonce']) && $nb_type_de_fiches==1) ) 
	{
		if ( BAZ_MOTEUR_RECHERCHE_AVANCEE || ( isset($_REQUEST['recherche_avancee'])&&$_REQUEST['recherche_avancee']==1) ) {
			if ($GLOBALS['_BAZAR_']['categorie_nature'] != '') {
				$champs_requete = '' ;
				if (!isset($_REQUEST['nature']) || $_REQUEST['nature'] == '') {
					$_REQUEST['nature'] = $tableau_typeannonces[0];
				}
			}

			if (isset($_REQUEST['recherche_avancee']) && $_REQUEST['recherche_avancee']==1) {
				foreach(array_merge($_POST, $_GET) as $cle => $valeur) $GLOBALS['_BAZAR_']['url']->addQueryString($cle, $valeur);
				$GLOBALS['_BAZAR_']['url']->addQueryString('recherche_avancee', '0');
				$lien_recherche_de_base = '<a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_RECHERCHE_DE_BASE.'</a><br />';
				//lien recherche de base
				labelhtml($formtemplate,'',$lien_recherche_de_base,'','','','','');
			}

			$tableau = formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']) ;
			for ($i=0; $i<count($tableau); $i++) {
				if (($tableau[$i][0] == 'liste' || $tableau[$i][0] == 'checkbox' ||$tableau[$i][0] == 'listefiche' || $tableau[$i][0] == 'checkboxfiche' || $tableau[$i][0] == 'labelhtml')) {
					$tableau[$i][0]($formtemplate, $tableau[$i], 'formulaire_recherche', '') ;
				}
			}

		}
		//lien recherche avancee
		else
		{
			$url_rech_avance = $GLOBALS['_BAZAR_']['url'];
			foreach(array_merge($_POST, $_GET) as $cle => $valeur) $url_rech_avance->addQueryString($cle, $valeur);
			$url_rech_avance->addQueryString('recherche_avancee', '1');
			$lien_recherche_avancee = '<a href="'.$url_rech_avance->getURL().'">'.BAZ_RECHERCHE_AVANCEE.'</a><br />';
			unset ($url_rech_avance);
			labelhtml($formtemplate,'',$lien_recherche_avancee,'','','','','');
		}
	}

	//requete pour obtenir l'id, le nom et prenom de toutes les personnes ayant depose une fiche
	// dans le but de construire l'element de formulaire select avec les noms des emetteurs de fiche
	if (BAZ_RECHERCHE_PAR_EMETTEUR) {
		$requete = 'SELECT DISTINCT '.BAZ_CHAMPS_ID.', '.BAZ_CHAMPS_NOM.', '.BAZ_CHAMPS_PRENOM.' '.
		           'FROM '.BAZ_PREFIXE.'fiche,'.BAZ_ANNUAIRE.' WHERE ' ;

		$requete .= ' bf_date_debut_validite_fiche<=NOW() AND bf_date_fin_validite_fiche>=NOW() and';

		$requete .= ' bf_ce_utilisateur='.BAZ_CHAMPS_ID.' ';
	    if (!isset($_REQUEST['nature'])) {
	    		if (isset($GLOBALS['_BAZAR_']['id_typeannonce'])) {
	    			$requete .= 'AND bf_ce_nature="'.$GLOBALS['_BAZAR_']['id_typeannonce'].'" ';
	    		}
		}
		else {
	    		if ($_REQUEST['nature']!='toutes') {
	    			$requete .= 'AND bf_ce_nature='.$_REQUEST['nature'].' ';
	    		}
	    }

	    $requete .= 'ORDER BY '.BAZ_CHAMPS_NOM.' ASC';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		$personnes_select['tous']=BAZ_TOUS_LES_EMETTEURS;
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			$personnes_select[$ligne[BAZ_CHAMPS_ID]] = $ligne[BAZ_CHAMPS_NOM]." ".$ligne[BAZ_CHAMPS_PRENOM] ;
		}
		$formtemplate->addElement ('select', 'personnes', BAZ_EMETTEUR, $personnes_select) ;
	} else {
		$formtemplate->addElement ('hidden', 'personnes', 'tous') ;
	}

	//teste si le user est admin, dans ce cas, il peut voir les fiches perimees
	if ($GLOBALS['wiki']->UserIsAdmin()) {
			//$valide_select[0] = BAZ_FICHES_PERIMEES;
			//$valide_select[1] = BAZ_FICHES_PAS_PERIMEES;
			//$valide_select[2] = BAZ_TOUTES_LES_DATES;
			//$formtemplate->addElement ('select', 'perime', BAZ_DATE, $valide_select,'') ;
			//$defauts = array('perime'=>1);
			//$formtemplate->setDefaults($defauts);
	}

	//champs texte pour entrer les mots cles
	$option = array('maxlength'=>255, 'class'=>'boite_recherche', 'value' => BAZ_MOT_CLE, 'onfocus'=>'if (this.value==\''.BAZ_MOT_CLE.'\') {this.value=\'\';}');
	$groupe_rech[] = &HTML_QuickForm::createElement('text', 'recherche_mots_cles', '', $option) ;

	//bouton de validation du formulaire
	$option = array('class'=>'btn bouton_recherche');
	$groupe_rech[] = &HTML_QuickForm::createElement('submit', 'rechercher', BAZ_RECHERCHER, $option);

	$formtemplate->addGroup($groupe_rech, 'groupe_recherche', null, '&nbsp;', 0);

	//option cachee pour savoir si le formulaire a ete appele deja
	$formtemplate->addElement('hidden', 'recherche_effectuee', 1) ;

	//affichage du formulaire
	$res.=$formtemplate->toHTML()."\n";

	//si la recherche n'a pas encore été effectué, on affiche les 10 dernières fiches
    if (!isset($_REQUEST['recherche_effectuee'])) {
		    $requete = 'SELECT DISTINCT bf_id_fiche, bf_titre, bf_ce_utilisateur, bf_date_debut_validite_fiche, bf_description, '.
		               'bn_label_nature, bf_date_creation_fiche FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature '.
		               'WHERE bn_id_nature=bf_ce_nature ';
		    if ($GLOBALS['_BAZAR_']['categorie_nature'] != 'toutes') $requete .= 'AND bn_type_fiche = "'.$GLOBALS['_BAZAR_']['categorie_nature'].'" ';
			if ($GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') $requete .= 'AND bn_id_nature='.$GLOBALS['_BAZAR_']['id_typeannonce'].' ' ;

			if (isset($_POST['perime'])&& $_POST['perime']==0) {$requete .= 'AND (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") ';
			} elseif  (isset($_POST['perime'])&& $_POST['perime']==2) {
				$requete .= '';
			} else {
            	$requete .= 'AND (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") ';
			}
			$requete .= ' ORDER BY bf_date_creation_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC LIMIT 10';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete);
			if (DB::isError($resultat)) {
				return ($resultat->getMessage().$resultat->getDebugInfo()) ;
			}
	        if($resultat->numRows() != 0) {
			$res .= '<h2>'.BAZ_DERNIERES_FICHES.'</h2>';
			$res .= '<ul class="BAZ_liste">';
			while($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		    		$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne['bf_id_fiche']);
		    		$res .= '<li class="BAZ_titre_fiche">'."\n";
		    		if (baz_a_le_droit('saisir_fiche', $ligne['bf_ce_utilisateur'])) {
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
						$res .= '<a class="BAZ_lien_supprimer" href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');"></a>'."\n";
		    		}
		    		if (baz_a_le_droit('saisir_fiche', $ligne['bf_ce_utilisateur'])) {
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
						$res .= '<a class="BAZ_lien_modifier" href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"></a>'."\n";
		    		}
		    		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		    		$res .= '<a class="BAZ_lien_voir" href="'. str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()) .'" title="Voir la fiche">'. stripslashes($ligne['bf_titre']).'</a></li>'."\n";

					//réinitialisation de l'url
					$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
		    		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
				}
				$res .= '</ul>';
			}
	}
	//la recherche a été effectuée, on établie la requete SQL
	else
	{
		$tableau_fiches = baz_requete_recherche_fiches();
		$res .= baz_afficher_liste_resultat($tableau_fiches);
	}

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('annonce');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('categorie_nature');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('recherche_avancee');

	return $res;
}

/**
 * Cette fonction récupère tous les parametres passés pour la recherche, et retourne un tableau de valeurs des fiches
 */
function baz_requete_recherche_fiches($tableau = '', $tri = '', $id_typeannonce = '', $categorie_nature = '') 
{
	$nb_jointures=0;
	$requeteSQL='';
	$requeteFrom = '' ;
	$requeteWhere = '1 ' ;
	//si les parametres ne sont pas rentrés, on prend les variables globales
	if ($id_typeannonce == '') $id_typeannonce = $GLOBALS['_BAZAR_']['id_typeannonce'];
	if ($categorie_nature == '') $categorie_nature = $GLOBALS['_BAZAR_']['categorie_nature'];
	
	
	if ( $categorie_nature != 'toutes') $requeteWhere .= ' AND bn_type_fiche = "'.$categorie_nature.'" ';	
	
	if ($id_typeannonce != 'toutes') 
	{
		$requeteWhere .= ' AND bn_id_nature='.$id_typeannonce ;
	}
	
	$requeteWhere .= ' AND bn_id_nature=bf_ce_nature ' ;
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requeteWhere .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
	}
	
	//on parcourt le tableau post pour agrémenter la requete les valeurs passées dans les champs liste et checkbox du moteur de recherche
	if ($tableau == '') {
		$tableau = array();
		reset($_POST);
		while (list($nom, $val) = each($_POST)) {		
			if ($nom != 'recherche_mots_cles' && $nom != 'rechercher' && $nom != 'personnes' && $nom != 'recherche_effectuee' &&
			    $nom != 'id_typeannonce' && $val != 0) {			
				if (is_array($val)) {
					$val = implode(',', array_keys($val));
				}
				$tableau[$nom] = $val;
			}
		}
	}
	
	$requeteWhereListe = '';
	reset($tableau);
	while (list($nom, $val) = each($tableau)) {		
			$requeteWhereListe .= ' AND bf_id_fiche IN (SELECT bfvt_ce_fiche FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_id_element_form="'.$nom.'" AND bfvt_texte IN ('.$val.')) ';
	}
	
	if ($id_typeannonce!='toutes') {
		$requeteWhere .= ' AND bf_ce_nature="'.$id_typeannonce.'" '.$requeteWhereListe;
	}

	//preparation de la requete pour trouver les mots cles
	if ( isset($_REQUEST['recherche_mots_cles']) && $_REQUEST['recherche_mots_cles'] != BAZ_MOT_CLE ) {
		//decoupage des mots cles
		$recherche = split(' ', $_REQUEST['recherche_mots_cles']) ;
		$nbmots=count($recherche);
		$requeteSQL='';
		for ($i=0; $i<$nbmots; $i++) {
			if ($i>0) $requeteSQL.=' OR ';
			$requeteSQL.=' bf_id_fiche IN ( SELECT bfvt_ce_fiche FROM '.BAZ_PREFIXE.'fiche_valeur_texte WHERE bfvt_texte LIKE "%'.$recherche[$i].'%" ) OR bf_id_fiche IN ( SELECT resource FROM '.BAZ_PREFIXE.'triples WHERE value LIKE "%'.$recherche[$i].'%" ) ';
			
		}
	}
	
	if (!isset($_REQUEST['nature'])) {
		if (!isset($id_typeannonce)) {
			$typedefiches = $tableau_typeannonces;
		}
		else {
			$typedefiches = $id_typeannonce;
		}
	} 
	else {
		$typedefiches = $_REQUEST['nature'] ;
		if ($typedefiches == 'toutes') $typedefiches = $tableau_typeannonces ;
	}
	
	if (isset($_REQUEST['valides'])) {
		$valides = $_REQUEST['valides'];
	}
	else {
		$valides = 1;
	}
	
	//generation de la liste de flux a afficher
	if (!isset($_REQUEST['personnes'])) $_REQUEST['personnes']='tous';
	
	// generation de la requete MySQL personnalisee
	$requete = 'SELECT * '.
				'FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature '.$requeteFrom.' WHERE '.$requeteWhere;
	if ($valides!=2) {
		$requete .= ' AND bf_statut_fiche='.$valides;
	}

	if ($valides!=0) {
		if (isset($_POST['perime'])&& $_POST['perime']==0) {
				$requete .= ' AND NOT (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") OR NOT (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") ';
			} elseif  (isset($_POST['perime'])&& $_POST['perime']==2) {	} else {
            	$requete .= ' AND (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") ';
			}
	}
	
	if ( isset($_POST['emetteur']) && $_POST['emetteur'] != 'tous' ) {
		$requete .= ' AND bf_ce_utilisateur='.$_POST['emetteur'];		
	}
	
	if ($requeteSQL!='') {
		$requete .= ' AND ('.$requeteSQL.')';
	}
	
	if ($tri == 'alphabetique') {
		$requete .= ' ORDER BY bf_titre ASC';
	}
	else {
		$requete .= ' ORDER BY  bf_date_debut_validite_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC';
	}

	if ( isset($_POST['nbitem']) ) {
		$requete .= ' LIMIT 0,'.$_POST['nbitem'];
	}
	
	//echo '<textarea style="width:100%;height:100px;">'.$requete.'</textarea>';
	return $GLOBALS['_BAZAR_']['db']->getAll($requete);
}

function baz_afficher_liste_resultat($tableau_fiches) {
	$res = '<div class="BAZ_info">'.BAZ_IL_Y_A;
	$nb_result=count($tableau_fiches);
	if ($nb_result<=1) $res .= $nb_result.' '.BAZ_FICHE_CORRESPONDANTE.'</div>'."\n";
	else $res .= $nb_result.' '.BAZ_FICHES_CORRESPONDANTES.'</div>'."\n";

	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
	
	// Mise en place du Pager
	require_once 'Pager/Pager.php';
	$params = array(
    'mode'       => BAZ_MODE_DIVISION,
    'perPage'    => BAZ_NOMBRE_RES_PAR_PAGE,
    'delta'      => BAZ_DELTA,
    'httpMethod' => 'GET',
    'extraVars' => array_merge($_POST, $_GET),
    'altNext' => BAZ_SUIVANT,
    'altPrev' => BAZ_PRECEDENT,
    'nextImg' => BAZ_SUIVANT,
    'prevImg' => BAZ_PRECEDENT,
    'itemData'   => $tableau_fiches
	);
	$pager = & Pager::factory($params);
	$data  = $pager->getPageData();
	$links = $pager->getLinks();	
	$res .= '<div class="bazar_numero">'.$pager->links.'</div>'."\n";
	$res .= '<ul class="BAZ_liste">'."\n" ;
	foreach ($data as $valeur) {
		$res .='<li class="BAZ_'.$valeur[29].'">'."\n";
		$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $valeur[0]) ;
		if (baz_a_le_droit('saisir_fiche', $valeur[1])) {
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
				$res .= '<a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="BAZ_lien_supprimer" onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');"></a>'."\n";
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		}
		if (baz_a_le_droit('saisir_fiche', $valeur[1])) {
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
				$res .= '<a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'" class="BAZ_lien_modifier"></a>'."\n";
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		}
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		$res .= '<a href="'. str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()) .'" class="BAZ_lien_voir" title="Voir la fiche">'. stripslashes($valeur[3]).'</a>'."\n".'</li>'."\n";
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	}
	$res .= '</ul>'."\n".'<div class="bazar_numero">'.$pager->links.'</div>'."\n";

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('recherche_avancee');

	return $res ;
}

function encoder_en_utf8($txt) {
	// Nous remplacons l'apostrophe de type RIGHT SINGLE QUOTATION MARK et les & isolés qui n'auraient pas été
	// remplacés par une entité HTML.
	$cp1252_map = array(
	   "\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
	   "\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
	   "\xc2\x83" => "\xc6\x92",     /* LATIN SMALL LETTER F WITH HOOK */
	   "\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
	   "\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
	   "\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
	   "\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
	   "\xc2\x88" => "\xcb\x86",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
	   "\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
	   "\xc2\x8a" => "\xc5\xa0",     /* LATIN CAPITAL LETTER S WITH CARON */
	   "\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
	   "\xc2\x8c" => "\xc5\x92",     /* LATIN CAPITAL LIGATURE OE */
	   "\xc2\x8e" => "\xc5\xbd",     /* LATIN CAPITAL LETTER Z WITH CARON */
	   "\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
	   "\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
	   "\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
	   "\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
	   "\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
	   "\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
	   "\xc2\x97" => "\xe2\x80\x94", /* EM DASH */
	   "\xc2\x98" => "\xcb\x9c",     /* SMALL TILDE */
	   "\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
	   "\xc2\x9a" => "\xc5\xa1",     /* LATIN SMALL LETTER S WITH CARON */
	   "\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
	   "\xc2\x9c" => "\xc5\x93",     /* LATIN SMALL LIGATURE OE */
	   "\xc2\x9e" => "\xc5\xbe",     /* LATIN SMALL LETTER Z WITH CARON */
	   "\xc2\x9f" => "\xc5\xb8"      /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
	);
	return  strtr(preg_replace('/ \x{0026} /u', ' &#38; ', mb_convert_encoding($txt, 'UTF-8','HTML-ENTITIES')), $cp1252_map);
}

/** baz_affiche_flux_RSS() - affiche le flux rss Ã  partir de parametres
*
*
* @return  string Le flux RSS, avec les headers et tout et tout
*/
function baz_affiche_flux_RSS() {
if (isset($_GET['annonce'])) {
	$annonce=$_GET['annonce'];
}
else {
	$annonce='';
}

if (isset($_GET['categorie_nature'])) {
	$categorie_nature=$_GET['categorie_nature'];
}
else {
	$categorie_nature='';
}

if (isset($_GET['nbitem'])) {
	$nbitem=$_GET['nbitem'];
}
else {
	$nbitem='';
}

if (isset($_GET['emetteur'])) {
	$emetteur=$_GET['emetteur'];
}
else {
	$emetteur='';
}

if (isset($_GET['valide'])) {
	$valide=$_GET['valide'];
}
else {
	$valide=1;
}

if (isset($_GET['sql'])) {
	$requeteSQL=$_GET['sql'];
}
else {
	$requeteSQL='';
}
// Gestion de la langue
if (isset($_GET['langue'])) {
	$requeteWhere = ' bn_ce_i18n like "'.$_GET['langue'].'%" and ';
} else {
	$requeteWhere = '';
}
return html_entity_decode(gen_RSS($annonce, $nbitem, $emetteur, $valide, $requeteSQL, '', $requeteWhere, $categorie_nature));

}

function afficher_flux_rss() {
	if (isset($_GET['annonce'])) {
		$annonce=$_GET['annonce'];
	}
	else {
		$annonce='';
	}

	if (isset($_GET['categorie_nature'])) {
		$categorie_nature=$_GET['categorie_nature'];
	}
	else {
		$categorie_nature=$GLOBALS['_BAZAR_']['id_typeannonce'];
	}

	if (isset($_GET['nbitem'])) {
		$nbitem=$_GET['nbitem'];
	}
	else {
		$nbitem='';
	}

	if (isset($_GET['emetteur'])) {
		$emetteur=$_GET['emetteur'];
	}
	else {
		$emetteur='';
	}

	if (isset($_GET['valide'])) {
		$valide=$_GET['valide'];
	}
	else {
		$valide=1;
	}

	if (isset($_GET['sql'])) {
		$requeteSQL=$_GET['sql'];
	}
	else {
		$requeteSQL='';
	}
	// Gestion de la langue
	if (isset($_GET['langue'])) {
		$requeteWhere = ' bn_ce_i18n like "'.$_GET['langue'].'%" and ';
	} else {
		$requeteWhere = '';
	}
	echo html_entity_decode(gen_RSS($annonce, $nbitem, $emetteur, $valide, $requeteSQL, '', $requeteWhere, $categorie_nature));
}

?>
