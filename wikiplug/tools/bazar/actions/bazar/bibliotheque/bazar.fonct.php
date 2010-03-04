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
*@author        Florian Schmitt <florian@ecole-et-nature.org>
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
$res .= '<div id="BAZ_menu">'."\n".'<ul>'."\n";
	// Gestion de la vue par defaut
	if (!isset($_GET[BAZ_VARIABLE_VOIR])) {
		$_GET[BAZ_VARIABLE_VOIR] = BAZ_VOIR_DEFAUT;
	}

	// Mes fiches
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_MES_FICHES))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_MES_FICHES);
		$res .= '<li id="menu_mes_fiches"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_MES_FICHES) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_VOIR_VOS_ANNONCES.'</a>'."\n".'</li>'."\n";
	}

	//partie consultation d'annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_CONSULTER))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$res .= '<li id="menu_consulter"';
		if ((isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_CONSULTER)) $res .=' class="onglet_actif" ';
		$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_CONSULTER.'</a>'."\n".'</li>'."\n";
	}

	//partie saisie d'annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_SAISIR))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
		$res .= '<li id="menu_deposer"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && ($_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_SAISIR )) $res .=' class="onglet_actif" ';
		$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_SAISIR.'</a>'."\n".'</li>'."\n";
	}

	//partie abonnement aux annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_S_ABONNER))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_S_ABONNER);
		$res .= '<li id="menu_inscrire"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_S_ABONNER) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_S_ABONNER.'</a></li>'."\n" ;
	}

	//partie affichage formulaire
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_FORMULAIRE))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_FORMULAIRE);
		$res .= '<li id="menu_formulaire"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_FORMULAIRE) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_FORMULAIRE.'</a></li>'."\n" ;
	}

	//choix des administrateurs
	//$utilisateur = new Administrateur_bazar($GLOBALS['AUTH']) ;
	//$est_admin=0;
	if ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth()) {
		$requete='SELECT bn_id_nature FROM bazar_nature';
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
			if ($utilisateur->isAdmin ($ligne['bn_id_nature'])) {
				$est_admin=1;
			}
		}
		if ($est_admin || $utilisateur->isSuperAdmin()) {
			//partie administrer
			if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_ADMIN))) {
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_ADMIN);
				$res .= '<li id="administrer"';
				if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_ADMIN) $res .=' class="onglet_actif" ';
				$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_ADMINISTRER.'</a></li>'."\n";
			}

			if ($utilisateur->isSuperAdmin()) {
				if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_GESTION_DROITS))) {
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_GESTION_DROITS);
					$res .= '<li id="gerer"';
					if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_GESTION_DROITS) $res .=' class="onglet_actif" ';
					$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_GESTION_DES_DROITS.'</a></li>'."\n" ;
				}
			}
		}
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
		$tableAttr = array('class' => 'table_bazar') ;
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
	$res= '<h2 class="titre_mes_fiches">'.BAZ_VOS_ANNONCES.'</h2><br />'."\n";
	if ($GLOBALS['_BAZAR_']['nomwiki']!='') {
		// requete pour voir si l'utilisateur a des fiches a son nom, classees par date de MAJ et nature d'annonce
		$requete = 'SELECT * FROM bazar_fiche, bazar_nature WHERE bf_ce_utilisateur="'. $GLOBALS['_BAZAR_']['nomwiki']['name'].
		           '" AND bn_id_nature=bf_ce_nature ';
		if ($GLOBALS['_BAZAR_']['categorie_nature']!='toutes') $requete .= ' AND bn_type_fiche = "'.$GLOBALS['_BAZAR_']['categorie_nature'].'" ';
		if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
		$requete .= ' ORDER BY bf_ce_nature ASC, bf_titre';

		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
		}
		if ($resultat->numRows() != 0) {
			$tableAttr = array('class' => 'table_bazar') ;
			$table = new HTML_Table($tableAttr) ;
			$entete = array (BAZ_TYPEANNONCE, BAZ_TITREANNONCE,  BAZ_ETATPUBLICATION, BAZ_MODIFIER, BAZ_SUPPRIMER) ;
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
			        '<a href="'.$lien_voir_url.'">'.$ligne['bf_titre'].'</a>'."\n", // col 2 : le nom
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
		$res .= '<p class="BAZ_info">'.BAZ_IDENTIFIEZ_VOUS_POUR_VOIR_VOS_FICHES.'</p>'."\n";

	}
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
	$res .= '<ul class="BAZ_liste liste_action">
	<li class="BAZ_titre_fiche lien_saisir"><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'" title="'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'">'.BAZ_SAISIR_UNE_NOUVELLE_FICHE.'</a></li></ul>';
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
		$lien_formulaire->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
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

		//------------------------------------------------------------------------------------------------
		//AFFICHAGE DU FORMULAIRE GENERAL DE CHOIX DU TYPE D'ANNONCE
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_DEPOSER_ANNONCE) {
			//titre
			$res.='<h2 class="titre_saisir_annonce">'.BAZ_DEPOSE_UNE_NOUVELLE_ANNONCE.'</h2>'."\n";

			//requete pour obtenir le nom et la description des types d'annonce
			$requete = 'SELECT * FROM bazar_nature WHERE ';
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
							$titre='&nbsp;<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$ligne['bn_image_titre'].'" alt="'.
											$ligne['bn_label_nature'].'" />'."\n";
						} else {
							$titre='<span class="BAZ_titre_liste">'.$ligne['bn_label_nature'].' : </span>'."\n";
						}
						$formtemplate->addElement('radio', 'id_typeannonce', '',$titre.$ligne['bn_description']."\n",
								$ligne['bn_id_nature'], array("id" => 'select'.$ligne['bn_id_nature']));
					}
				}

				$res .= '<br />'.BAZ_CHOIX_TYPEANNONCE.'<br /><br />'."\n";

				// Bouton d annulation
				$lien_formulaire->removeQueryString(BAZ_VARIABLE_ACTION);
				//$lien_formulaire->removeQueryString(BAZ_VARIABLE_VOIR);
				$lien_formulaire->removeQueryString('id_typeannonce');
				$lien_formulaire->removeQueryString('id_fiche');
				$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER,
                            preg_replace ("/&amp;/", "&", $lien_formulaire->getURL()), BAZ_ANNULER);// Le preg_replace contourne un pb de QuickForm et Net_URL

        		$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER);
        		$formtemplate->addGroup($buttons, null, null, '&nbsp;');
        		$squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_input"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</div>'."\n".'</div>'."\n");

				//Affichage a l'ecran
				$res .= $formtemplate->toHTML()."\n";
			}
		}

		//------------------------------------------------------------------------------------------------
		//AFFICHAGE DU FORMULAIRE CORRESPONDANT AU TYPE DE L'ANNONCE CHOISI PAR L'UTILISATEUR
		//------------------------------------------------------------------------------------------------

		if ($mode == BAZ_ACTION_NOUVEAU) {
			$lien_formulaire->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_NOUVEAU_V);
			//$formtemplate->updateAttributes(array(BAZ_VARIABLE_ACTION => str_replace('&amp;', '&', $lien_formulaire->getURL())));
			// Affichage du modele de formulaire
			$res .= baz_afficher_formulaire_fiche('modification', $formtemplate);
		}


		//------------------------------------------------------------------------------------------------
		//CAS DE LA MODIFICATION D'UNE ANNONCE (FORMULAIRE DE MODIFICATION)
		//------------------------------------------------------------------------------------------------
		if ($mode == BAZ_ACTION_MODIFIER) {
			$res .= baz_afficher_formulaire_fiche('modification', $formtemplate);
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
			//else
			//{
			//	$formtemplate->display();
			//}
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
function baz_afficher_formulaire_fiche($mode = 'saisie', $formtemplate) {
	$res = '';
	//titre de la rubrique
	$res .= '<h2 class="titre_type_annonce">'.BAZ_TITRE_SAISIE_ANNONCE.'&nbsp;'.$GLOBALS['_BAZAR_']['typeannonce'].'</h2><br />'."\n";

	//si le type de formulaire requiert une acceptation des conditions on affiche les conditions
	if (($GLOBALS['_BAZAR_']['condition']!='')AND(!isset($_POST['accept_condition']))AND(!isset($_GET['url'])OR(!isset($_GET['fichier']))OR(!isset($_GET['image'])))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, $_GET[BAZ_VARIABLE_ACTION]);
		if (!empty($GLOBALS['_BAZAR_']['id_fiche'])) $GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']) ;
		$formtemplate->updateAttributes(array(BAZ_VARIABLE_ACTION => str_replace('&amp;', '&', $GLOBALS['_BAZAR_']['url']->getURL())));
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
		$tableau=formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']);
		$valeurs_par_defaut = '';
		if (isset($GLOBALS['_BAZAR_']['id_fiche']) && $GLOBALS['_BAZAR_']['id_fiche']!='')
		{
			//Ajout des valeurs par defaut pour une modification
			$valeurs_par_defaut = baz_valeurs_fiche($GLOBALS['_BAZAR_']['id_fiche']) ;
		}
		for ($i=0; $i<count($tableau); $i++) {
			$tableau[$i][0]($formtemplate, $tableau[$i], 'saisie', $valeurs_par_defaut) ;
		}
		$formtemplate->addElement('hidden', 'id_typeannonce', $GLOBALS['_BAZAR_']['id_typeannonce']);

		// Nettoyage de l'url avant les return
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		//$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
		$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
		$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
 		$buttons[] = &HTML_QuickForm::createElement('link', 'annuler', BAZ_ANNULER, str_replace("&amp;", "&", $GLOBALS['_BAZAR_']['url']->getURL()), BAZ_ANNULER, array('class' => 'bouton_annuler'));
		$buttons[] = &HTML_QuickForm::createElement('submit', 'valider', BAZ_VALIDER, array('class' => 'bouton_sauver'));
		$formtemplate->addGroup($buttons, 'groupe_boutons', null, '&nbsp;', 0);

	}
	//Affichage a l'ecran
	$res .= $formtemplate->toHTML()."\n";
	return $res;
}


/** baz_requete_bazar_fiche() - preparer la requete d'insertion ou de MAJ de la table bazar_fiche a partir du template
*
* @global   mixed L'objet contenant les valeurs issues de la saisie du formulaire
* @return   void
*/
function baz_requete_bazar_fiche($valeur) {
	$requete=NULL;
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
		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && $GLOBALS['_BAZAR_']['nomwiki']!=NULL) $requete .= 'bf_ce_utilisateur="'.$GLOBALS['_BAZAR_']['nomwiki']['name'].'", ';
		$requete .= 'bf_categorie_fiche="'.$GLOBALS['_BAZAR_']['categorie_nature'].'", bf_ce_nature='.$GLOBALS['_BAZAR_']['id_typeannonce'].', '.
		   'bf_date_creation_fiche=NOW(), ';
		if (!isset($_REQUEST['bf_date_debut_validite_fiche'])) {
			$requete .= 'bf_date_debut_validite_fiche=now(), bf_date_fin_validite_fiche="0000-00-00", ' ;
		}
		$requete .= baz_requete_bazar_fiche(&$valeur) ;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}
		// Envoie d un mail aux administrateurs
		if (BAZ_ENVOI_MAIL_ADMIN) {
			include_once('Mail.php');
			include_once('Mail/mime.php');
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['_BAZAR_']['wiki']->config["base_url"]);
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
			$requeteadmins = 'SELECT value FROM '.$GLOBALS['_BAZAR_']['wiki']->config["table_prefix"].'triples WHERE resource="ThisWikiGroup:admins" AND property="http://www.wikini.net/_vocabulary/acls" LIMIT 1';
			$resultatadmins = $GLOBALS['_BAZAR_']['db']->query($requeteadmins);
			$ligne = $resultatadmins->fetchRow(DB_FETCHMODE_ASSOC);
			$tabadmin = explode("\n", $ligne['value']);
			foreach ($tabadmin  as $line) {
				$admin = $GLOBALS['_BAZAR_']['wiki']->LoadUser(trim($line));
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
			$chemin_destination=BAZ_CHEMIN.'actions/bazar/upload/'.$_FILES[$nom_fichier]['name'];
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





/** baz_mise_a_jour() - Mettre a jour une fiche
*
* @global   Le contenu du formulaire de saisie de l'annonce
* @return   void
*/
function baz_mise_a_jour($valeur) {
	//MAJ de bazar_fiche
	$requete = 'UPDATE bazar_fiche SET '.baz_requete_bazar_fiche(&$valeur,$GLOBALS['_BAZAR_']['id_typeannonce']);
	$requete.= ' WHERE bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	// Envoie d un mail aux administrateurs
		if (BAZ_ENVOI_MAIL_ADMIN) {
			include_once('Mail.php');
			include_once('Mail/mime.php');
			$lien = str_replace("/wakka.php?wiki=","",$GLOBALS['_BAZAR_']['wiki']->config["base_url"]);
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
			$requeteadmins = 'SELECT value FROM '.$GLOBALS['_BAZAR_']['wiki']->config["table_prefix"].'triples WHERE resource="ThisWikiGroup:admins" AND property="http://www.wikini.net/_vocabulary/acls" LIMIT 1';
			$resultatadmins = $GLOBALS['_BAZAR_']['db']->query($requeteadmins);
			$ligne = $resultatadmins->fetchRow(DB_FETCHMODE_ASSOC);
			$tabadmin = explode("\n", $ligne['value']);
			foreach ($tabadmin  as $line) {
				$admin = $GLOBALS['_BAZAR_']['wiki']->LoadUser(trim($line));
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
	if ($GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$ligne['bf_ce_utilisateur']))
	{
		// suppression des valeurs des listes et des cases à cocher
		$requete = 'DELETE FROM bazar_fiche_valeur_liste WHERE bfvl_ce_fiche='.$idfiche;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ($resultat->getMessage().$resultat->getDebugInfo()) ;
		}

		//suppression des valeurs des champs texte
		$requete = 'DELETE FROM bazar_fiche_valeur_texte WHERE bfvt_ce_fiche = '.$idfiche;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
		}

		//suppression des valeurs des champs texte
		$requete = 'DELETE FROM bazar_fiche_valeur_texte_long WHERE bfvtl_ce_fiche = '.$idfiche;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			return ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
		}
		
		//TODO:suppression des fichiers et images associées

		//suppression de la fiche dans bazar_fiche
		$requete = 'DELETE FROM bazar_fiche WHERE bf_id_fiche = '.$idfiche;
		$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		if (DB::isError($resultat)) {
			die ('Echec de la requete<br />'.$resultat->getMessage().'<br />'.$resultat->getDebugInfo().'<br />'."\n") ;
		}

		//on nettoie l'url, on retourne à la consultation des fiches
		$GLOBALS['_BAZAR_']['url']->addQueryString ('message', 'delete_ok') ;
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$GLOBALS['_BAZAR_']['url']->removeQueryString (BAZ_VARIABLE_VOIR) ;
		$GLOBALS['_BAZAR_']['url']->removeQueryString ('id_fiche') ;
		header ('Location: '.$GLOBALS['_BAZAR_']['url']->getURL()) ;
		exit;
	}
	else 
	{
		echo '<div class="BAZ_info">'.BAZ_PAS_DROIT_SUPPRIMER.'</div>'."\n";
	}

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
		unlink(BAZ_CHEMIN.'actions/bazar/upload/'.$ligne['bfj_fichier']);
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
		unlink(BAZ_CHEMIN.'actions/bazar/upload/'.$ligne['bf_url_image']);
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


/** baz_liste_rss() affiche le formulaire qui permet de s'inscrire pour recevoir des annonces d'un type
*
*   @return  string    le code HTML
*/
function baz_liste_rss() {
	$res= '<h2>'.BAZ_S_INSCRIRE_AUX_ANNONCES.'</h2>'."\n";
	//requete pour obtenir l'id et le label des types d'annonces
	$requete = 'SELECT bn_id_nature, bn_label_nature '.
	           'FROM bazar_nature WHERE 1';
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		return ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}

	// Nettoyage de l url
	//$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
	$lien_RSS=$GLOBALS['_BAZAR_']['url'];
	$lien_RSS->addQueryString('wiki', $GLOBALS['_BAZAR_']['wiki']->minihref('xmlutf8',$_GET['wiki']));
	$lien_RSS->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FLUX_RSS);
	$liste='';
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		$lien_RSS->addQueryString('annonce', $ligne['bn_id_nature']);
		$liste .= '<li><a href="'.str_replace('&', '&amp;', $lien_RSS->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'BAZ_rss.png" alt="'.BAZ_RSS.'" /></a>&nbsp;';
		$liste .= $ligne['bn_label_nature'];
		$liste .= '</li>'."\n";
		$lien_RSS->removeQueryString('annonce');
	}
	if ($liste!='') $res .= '<ul class="BAZ_liste_rss">'."\n".'<li><a href="'.str_replace('&', '&amp;', $lien_RSS->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'BAZ_rss.png" alt="'.BAZ_RSS.'" /></a>&nbsp;<strong>Flux RSS de toutes les fiches</strong></li>'."\n".$liste.'</ul>'."\n";
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
/*
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
*/
	$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
    $squelette->setElementTemplate( '<div class="formulaire_ligne">'."\n".
									'<div class="formulaire_label">'."\n".'{label}'.
    		                        '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
    								' </div>'."\n".'<div class="formulaire_input"> '."\n".'{element}'."\n".
                                    '<!-- BEGIN error --><span class="erreur">{error}</span><!-- END error -->'."\n".
                                    '</div>'."\n".'</div>'."\n");
 	$squelette->setElementTemplate( '<tr>'."\n".'<td colspan="2" class="liste_a_cocher"><strong>{label}&nbsp;{element}</strong>'."\n".
                                    '<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".'</td>'."\n".'</tr>'."\n", 'accept_condition');
  	$squelette->setElementTemplate( '<div class="bouton">{label}{element}</div>'."\n", 'valider');
 	$squelette->setRequiredNoteTemplate("\n".'<div class="symbole_obligatoire">* {requiredNote}</div>'."\n");
	//traduction de champs requis
	$formtemplate->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
	$formtemplate->setJsWarnings(BAZ_ERREUR_SAISIE,BAZ_VEUILLEZ_CORRIGER);
	//champs du formulaire
	if (isset($_GET['idformulaire'])) $formtemplate->addElement('hidden', 'bn_id_nature', $_GET['idformulaire']);
	$formtemplate->addElement('text', 'bn_label_nature', BAZ_NOM_FORMULAIRE, array('class' => 'input_texte'));
	$formtemplate->addElement('textarea', 'bn_description', BAZ_DESCRIPTION, array('class' => 'input_textarea', 'cols' => "20", 'rows'=> "3"));
	$formtemplate->addElement('textarea', 'bn_condition', BAZ_CONDITION, array('class' => 'input_textarea', 'cols' => "20", 'rows'=> "3"));
	$formtemplate->addElement('checkbox', 'bn_commentaire', BAZ_AUTORISER_COMMENTAIRE);
	$formtemplate->addElement('checkbox', 'bn_appropriation', BAZ_AUTORISER_APPROPRIATION);
	$formtemplate->addElement('text', 'bn_label_class', BAZ_NOM_CLASSE_CSS, array('class' => 'input_texte'));
	$formtemplate->addElement('text', 'bn_type_fiche', BAZ_NOUVELLE_CATEGORIE_FORMULAIRE, array('class' => 'input_texte'));
	$formtemplate->addElement('textarea', 'bn_template', BAZ_TEMPLATE, array('class' => 'input_textarea', 'style' => 'height:400px;', 'cols' => "20", 'rows'=> "3"));
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

	// il y a un formulaire a modifier
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

	//il y a des donnees pour ajouter un nouveau formulaire
	} elseif (isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='new_v') {
		$requete = 'INSERT INTO bazar_nature (`bn_id_nature` ,`bn_ce_i18n` ,`bn_label_nature` ,`bn_template` ,`bn_description` ,`bn_condition` ,`bn_commentaire` ,`bn_appropriation` ,`bn_label_class` ,`bn_type_fiche`)' .
				   ' VALUES ('.baz_nextId('bazar_nature', 'bn_id_nature', $GLOBALS['_BAZAR_']['db']).
                   ', "fr-FR", "'.$_POST["bn_label_nature"].'", "'.addslashes($_POST["bn_template"]).
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

	//il y a des donnees pour modifier un formulaire
	} elseif ($GLOBALS['_BAZAR_']['nomwiki']!='' && $GLOBALS['_BAZAR_']['isAdmin']  && isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='modif_v') {
		$requete = 'UPDATE bazar_nature SET `bn_label_nature`="'.$_POST["bn_label_nature"].
				   '" ,`bn_template`="'.addslashes($_POST["bn_template"]).
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

	// il y a un id de formulaire à supprimer
	} elseif ($GLOBALS['_BAZAR_']['nomwiki']!='' && $GLOBALS['_BAZAR_']['isAdmin'] && isset($_GET['action_formulaire']) && $_GET['action_formulaire']=='delete') {
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

	// affichage de la liste des templates ï¿½ modifier ou supprimer (on l'affiche dans tous les cas, sauf cas de modif de formulaire)
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
			$lien_formulaire->addQueryString('idformulaire', $ligne['bn_id_nature']);
			if ($GLOBALS['_BAZAR_']['nomwiki']!=''  && $GLOBALS['_BAZAR_']['isAdmin'])  {
				$liste .= '<a href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FORMULAIRE.' ?\');">'.
                      '<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'delete.gif" alt="'.BAZ_EFFACER.'" /></a>'."\n";
			}
			$lien_formulaire->removeQueryString('action_formulaire');
			$lien_formulaire->addQueryString('action_formulaire', 'modif');
			if ($GLOBALS['_BAZAR_']['nomwiki']!='' && $GLOBALS['_BAZAR_']['isAdmin'])  {
				$liste .= '<a href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'modify.gif" alt="'.BAZ_MODIFIER.'" />'.
						  '&nbsp;'.$ligne['bn_label_nature'].'</a>'."\n";
			} else {
				$liste .= $ligne['bn_label_nature']."\n";
			}
			$lien_formulaire->removeQueryString('action_formulaire');
			$lien_formulaire->removeQueryString('idformulaire');

			$liste .='</li>'."\n";
		}
		if ($liste!='') $res .= $liste.'</ul><br />'."\n";

		//ajout du lien pour creer un nouveau formulaire
		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && $GLOBALS['_BAZAR_']['isAdmin']) {
			$lien_formulaire->addQueryString('action_formulaire', 'new');
			$res .= '<a href="'.str_replace('&','&amp;',$lien_formulaire->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'new.gif" alt="new" />'.
					  '&nbsp;'.BAZ_NOUVEAU_FORMULAIRE.'</a>'."\n";
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
function baz_valeurs_fiche($idfiche) {
	$requete = 'SELECT * FROM bazar_fiche WHERE bf_id_fiche='.$idfiche;
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().'<br />'.$resultat->getDebugInfo()) ;
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC) ;
	$valeurs_fiche = array() ;
	$tableau = formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']);
	for ($i=0; $i<count($tableau); $i++) {
		$val = $tableau[$i][0]($formtemplate, $tableau[$i], 'valeur', $ligne);		
		if (is_array($val))	$valeurs_fiche = $valeurs_fiche + $val;
	}
	return $valeurs_fiche;
}

/** baz_valeurs_type_de_fiche() - Initialise les valeurs globales pour le type de fiche choisi
*
* @param    integer Identifiant du type de fiche
*
* @return   void
*/
function baz_valeurs_type_de_fiche($idtypefiche) {
	$requete = 'SELECT * FROM bazar_nature WHERE bn_id_nature = '.$idtypefiche;
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%"';
	}
	$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
	if (DB::isError($resultat)) {
		die ($resultat->getMessage().$resultat->getDebugInfo()) ;
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC);
	$GLOBALS['_BAZAR_']['typeannonce']=$ligne['bn_label_nature'];
	$GLOBALS['_BAZAR_']['condition']=$ligne['bn_condition'];
	$GLOBALS['_BAZAR_']['template']=$ligne['bn_template'];
	$GLOBALS['_BAZAR_']['commentaire']=$ligne['bn_commentaire'];
	$GLOBALS['_BAZAR_']['appropriation']=$ligne['bn_appropriation'];
	return;
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

/** function baz_nextWiki () Renvoie un id unique de NomWiki
*
*   @param  string  NomWiki proposé
*
*   return  string  NomWiki possible
*/
function baz_nextwiki($nomwiki) {
	if (!is_array($GLOBALS['_BAZAR_']['wiki']->LoadUser($nomwiki))) {
		return $nomwiki;
	} else {
		return baz_nextwiki($nomwiki.'bis');
	}
}

/** function baz_titre_wiki () Renvoie la chaine de caractere sous une forme compatible avec wikini
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
* @global integer Identifiant de la fiche a afficher
*
* @return   string  HTML
*/
function baz_voir_fiche($danslappli, $idfiche='') {
	$res='';
	if ($idfiche!='') $GLOBALS['_BAZAR_']['id_fiche']=$idfiche;
	if (isset($GLOBALS['_BAZAR_']['id_fiche']) && $GLOBALS['_BAZAR_']['id_fiche']>0) {
		$url= $GLOBALS['_BAZAR_']['url'];
		$url->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		$url->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
		$url = preg_replace ('/&amp;/', '&', $url->getURL()) ;

		//cas ou la fiche a ete validee
		if (isset($_GET['publiee'])) {
			publier_fiche($_GET['publiee']);
		}

		//cas on une structure s'approprie une ressource
		if ((BAZ_SANS_AUTH!=true) && isset($_GET['appropriation'])) {
			if ($_GET['appropriation']==1) {
				$requete = 'INSERT INTO bazar_appropriation VALUES ('.$GLOBALS['_BAZAR_']['id_fiche'].', '.$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID).')';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			}
			elseif ($_GET['appropriation']==0) {
				$requete = 'DELETE FROM bazar_appropriation WHERE  ba_ce_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND ba_ce_id_structure='.$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID);
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
			}
		}

		//cas ou un commentaire a ete entre
		if (isset($_POST['Nom'])) {
			$requete = 'INSERT INTO bazar_commentaires VALUES ('.
						baz_nextid('bazar_commentaires', 'bc_id_commentaire', $GLOBALS['_BAZAR_']['db']).
						', '.$GLOBALS['_BAZAR_']['id_fiche'].', "'.$_POST['Nom'].'", "'.$_POST['Commentaire'].
						'", NOW() )';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		}
		//cas ou un commentaire va etre supprime
		elseif (isset($_GET['id_commentaire'])) {
			$requete = 'DELETE FROM bazar_commentaires WHERE bc_id_commentaire='.$_GET['id_commentaire'].' LIMIT 1';
			$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		}
		else {
			if (isset($_GET[BAZ_VARIABLE_ACTION])) {
				if ($_GET[BAZ_VARIABLE_ACTION]==BAZ_VOIR_FICHE) {
					//sinon on met a jour le nb de visites pour la fiche, puisque c'est une simple consultation
					$requete = 'UPDATE bazar_fiche SET bf_nb_consultations=bf_nb_consultations+1 WHERE bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];
					$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				}
			}
		}

		//if (isset($GLOBALS['_BAZAR_']['id_fiche'])) $requete = 'SELECT * FROM bazar_fiche,bazar_nature WHERE bf_ce_nature=bn_id_nature and bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'];

		//if (isset($GLOBALS['_BAZAR_']['langue'])) $requete .= ' and bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'"';
		//$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
		//(DB::isError($resultat)) ? die ("Erreur : ".$resultat->getMessage()." pour la requete :<br>".$requete) : '';

		//$ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC) ;
		//if (!isset($GLOBALS['_BAZAR_']['typeannonce'])) $GLOBALS['_BAZAR_']['typeannonce'] = $ligne['bf_ce_nature'];
		//if (!isset($GLOBALS['_BAZAR_']['fiche_valide'])) $GLOBALS['_BAZAR_']['fiche_valide'] = $ligne['bf_statut_fiche'];
		//on verifie si l'utilisateur est administrateur
		//$est_admin=0;

		// Si on vient de l applette calendrier, $GLOBALS['_BAZAR_']['id_typeannonce'] est vide ...
		// mais on dispose de la constante BAZ_NUM_ANNONCE_CALENDRIER
		//if (!isset($GLOBALS['_BAZAR_']['id_typeannonce']) && defined('BAZ_NUM_ANNONCE_CALENDRIER'))  $GLOBALS['_BAZAR_']['id_typeannonce'] = BAZ_NUM_ANNONCE_CALENDRIER;

		//if (!isset($GLOBALS['_BAZAR_']['template'])) $GLOBALS['_BAZAR_']['template'] = $ligne['bn_template'];
		//if (!isset($GLOBALS['_BAZAR_']['commentaire'])) $GLOBALS['_BAZAR_']['commentaire'] = $ligne['bn_commentaire'];
		//if (!isset($GLOBALS['_BAZAR_']['class'])) $GLOBALS['_BAZAR_']['class'] = $ligne['bn_label_class'];

		//debut de la fiche
		$res .= '<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";

		//affiche le type de fiche sous forme d'image
		//if (isset ($GLOBALS['_BAZAR_']['image_titre']) && $GLOBALS['_BAZAR_']['image_titre']!='') {
		//	$res .= '<img class="BAZ_img_titre" src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$GLOBALS['_BAZAR_']['image_titre'].'" alt="'.$ligne['bn_label_nature'].'" />'.'<br />'."\n";
		//}
		
		
		//affiche le texte du type de fiche sinon
		//else {
			$res .= '<h2 class="BAZ_titre BAZ_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.$GLOBALS['_BAZAR_']['label_typeannonce'].'</h2>'."\n";
		//}

		//Partie la plus importante : apres avoir récupéré toutes les valeurs de la fiche, on génére l'affichage html de cette dernière
		$valeurs_fiche = baz_valeurs_fiche($GLOBALS['_BAZAR_']['id_fiche']);
		$tableau=formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']);
		for ($i=0; $i<count($tableau); $i++) {
			$res .= $tableau[$i][0]($formtemplate, $tableau[$i], 'html', $valeurs_fiche);
		}

		//informations complementaires (id fiche, etat publication,... )
		if ($danslappli==1) {
			$res .= '<div class="BAZ_bulle_corps BAZ_bulle_corps_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			$res .= '<div class="BAZ_info BAZ_infos_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
			$res .= '<span class="BAZ_nb_vues BAZ_nb_vues_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_FICHE_NUMERO.$GLOBALS['_BAZAR_']['id_fiche'].BAZ_NB_VUS.$ligne['bf_nb_consultations'].BAZ_FOIS.'</span><br />'."\n";

			//affichage du redacteur de la fiche
			if ($ligne['bf_ce_utilisateur']!='')
			{
				$sql= "SELECT bf_id_fiche FROM bazar_fiche WHERE bf_ce_nature IN (9,15) AND bf_ce_utilisateur ='".$ligne['bf_ce_utilisateur']."' LIMIT 1";
				$bazar = $GLOBALS['_BAZAR_']['wiki']->LoadSingle($sql);
				if ($bazar) {
					$id = $bazar['bf_id_fiche'];
					$res .= 'Ecrite par <a href="'.$GLOBALS['_BAZAR_']['url']->getUrl().'&amp;action=voir_fiche&id_fiche='.$id.'" title="Voir la fiche">'.$ligne['bf_ce_utilisateur'].'</a><br />'."\n";
				}
			}

					if ($GLOBALS['_BAZAR_']['fiche_valide']==1 && $GLOBALS['_BAZAR_']['appropriation']!=1) {
						if ($ligne['bf_date_debut_validite_fiche'] != '0000-00-00' && $ligne['bf_date_fin_validite_fiche'] != '0000-00-00') {
						$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_PUBLIEE.':</span> '.BAZ_DU.
								' '.strftime('%d.%m.%Y',strtotime($ligne['bf_date_debut_validite_fiche'])).' '.
								BAZ_AU.' '.strftime('%d.%m.%Y',strtotime($ligne['bf_date_fin_validite_fiche'])).'<br />'."\n";
						}
					}
					elseif ($GLOBALS['_BAZAR_']['appropriation']!=1 || $GLOBALS['_BAZAR_']['fiche_valide']!=1) {
						$res .= '<span class="BAZ_rubrique  BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_PUBLIEE.':</span> '.BAZ_NON.'<br />'."\n";
					}
					//affichage des infos et du lien pour la mise a jour de la fiche
					if ( $est_admin || ((BAZ_SANS_AUTH!=true) && $GLOBALS['_BAZAR_']['annonceur']==$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID)) ) {
						$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].' date_creation">'.BAZ_DATE_CREATION.'</span> '.strftime('%d.%m.%Y %H:%M',strtotime($ligne['bf_date_creation_fiche']))."\n";
						$res .= '<span class="BAZ_rubrique BAZ_rubrique_'.$GLOBALS['_BAZAR_']['class'].' date_mise_a_jour">'.BAZ_DATE_MAJ.'</span> '.strftime('%d.%m.%Y %H:%M',strtotime($ligne['bf_date_maj_fiche']))."\n";
					}
					$res .= '</div>'."\n";

					if ( $GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$ligne['bf_ce_utilisateur'] || $ligne['bf_ce_utilisateur']=='' || $ligne['bf_ce_utilisateur']==NULL )) {
						$res .= '<div class="BAZ_actions_fiche BAZ_actions_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
						$res .= '<ul>'."\n";
						if ( $GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$ligne['bf_ce_utilisateur'])) {
							$lien_publie = &$GLOBALS['_BAZAR_']['url'];
							$lien_publie->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
							$lien_publie->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
							$lien_publie->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
							$lien_publie->addQueryString('typeannonce', $ligne['bf_ce_nature']);
							if ($GLOBALS['_BAZAR_']['fiche_valide']==0||$GLOBALS['_BAZAR_']['fiche_valide']==2) {
								$lien_publie->addQueryString('publiee', 1);
								$label_publie=BAZ_VALIDER_LA_FICHE;
								$class_publie='_valider';
							} elseif ($GLOBALS['_BAZAR_']['fiche_valide']==1) {
								$lien_publie->addQueryString('publiee', 0);
								$label_publie=BAZ_INVALIDER_LA_FICHE;
								$class_publie='_invalider';
							}
							$res .= '<li class="BAZ_liste'.$class_publie.'"><a href="'.str_replace('&', '&amp;', $lien_publie->getURL()).'">'.$label_publie.'</a></li>'."\n";
							$lien_publie->removeQueryString('publiee');
						}
						$lien_modifier=$GLOBALS['_BAZAR_']['url'];
						$lien_modifier->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
						$lien_modifier->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
						$lien_modifier->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
						$lien_modifier->addQueryString('typeannonce', $ligne['bf_ce_nature']);
						$res .= '<li class="BAZ_liste_modifier"><a href="'.str_replace('&', '&amp;', $lien_modifier->getURL()).'" id="modifier_fiche">'.BAZ_MODIFIER_LA_FICHE.'</a></li>'."\n";
						if ( $GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$ligne['bf_ce_utilisateur'])) {
							$lien_supprimer=$GLOBALS['_BAZAR_']['url'];
							$lien_supprimer->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
							$lien_supprimer->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
							$lien_supprimer->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
							$lien_supprimer->addQueryString('typeannonce', $ligne['bf_ce_nature']);
							$res .= '<li class="BAZ_liste_supprimer"><a href="'.str_replace('&', '&amp;', $lien_supprimer->getURL()).'" id="supprimer_fiche">'.BAZ_SUPPRIMER_LA_FICHE.'</a></li>'."\n";
						}
						$res .= '</ul>'."\n";
						$res .= '</div>'."\n";
					}
				}
			$res .= '</div>'."\n";
			$res .= '</div>'."\n";
		}

		// Nous vérifions comment est appelée la fonction
		if ($danslappli == 0) {
		 $res .= '</div>'."\n";
		} else if ($danslappli == 1 ) {

			// Ajout des appropriations, s'il le faut
			if ($GLOBALS['_BAZAR_']['appropriation'] == 1) {
				$res .= '<br />'."\n".'<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<h2 class="BAZ_titre BAZ_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LES_STRUCTURES_POSSEDANT_UNE_RESSOURCE.'</h2>'."\n";
				$requete = 'SELECT '.BAZ_CHAMPS_ID.', '.BAZ_CHAMPS_NOM.' FROM bazar_appropriation,'.BAZ_ANNUAIRE.' WHERE ba_ce_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' AND ba_ce_id_structure='.BAZ_CHAMPS_ID.' ORDER BY '.BAZ_CHAMPS_NOM.' ASC';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				if (DB::isError ($resultat)) {
					return $resultat->getMessage().'<br />'.$resultat->getDebugInfo();
				}
				$possede_ressource=0;
				if ($resultat->numRows()>0) {
					$res .= BAZ_IL_Y_A.$resultat->numRows().' ';
					if ($resultat->numRows()==1) $res .= BAZ_STRUCTURE_POSSEDANT.'<br />'."\n";
					else $res .= BAZ_STRUCTURES_POSSEDANT.'<br />'."\n";
					$res .= '<ul>'."\n";
					while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
						$res .= '<li><a href="'.BAZ_URL_ANNUAIRE.'&amp;voir_fiche='.$ligne[BAZ_CHAMPS_ID].'" onclick="javascript:window.open(this.href);return false;">'.$ligne[BAZ_CHAMPS_NOM].'</a></li>'."\n";
						if ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth() && $GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID)==$ligne[BAZ_CHAMPS_ID]) $possede_ressource=1;
					}
					$res .= '</ul><br />'."\n";
				}
				else $res .= BAZ_PAS_D_APPROPRIATION.'<br /><br />'."\n";
				$res .='<p class="BAZ_bulle_corps BAZ_bulle_corps_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$lien_appropriation = $GLOBALS['_BAZAR_']['url'];
				$lien_appropriation->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
				$lien_appropriation->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
				if ($possede_ressource) {
					$lien_appropriation->addQueryString('appropriation', 0);
					$res .= BAZ_POSSEDE_DEJA_RESSOURCE.'<br />'."\n".'<a href="'.$lien_appropriation->getURL().'">'.BAZ_CLIQUER_POUR_VOUS_ENLEVER.'</a>'."\n";
					$lien_appropriation->removeQueryString('appropriation');
				}
				elseif ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth() && $GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_EST_STRUCTURE)) {
					$lien_appropriation->addQueryString('appropriation', 1);
					$res .= BAZ_SI_POSSEDE_RESSOURCE.'<br />'."\n".'<a href="'.$lien_appropriation->getURL().'">'.BAZ_CLIQUER_POUR_APPARAITRE.'</a>'."\n";
					$lien_appropriation->removeQueryString('appropriation');
				}
				elseif ((BAZ_SANS_AUTH!=true) && $GLOBALS['AUTH']->getAuth() && !$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_EST_STRUCTURE)) {
					$res .= BAZ_IL_FAUT_ETRE_STRUCTURE."\n";
				}
				elseif ((BAZ_SANS_AUTH!=true) && !$GLOBALS['AUTH']->getAuth()) {
					$res .= BAZ_IL_FAUT_ETRE_IDENTIFIE_STRUCTURE."\n";
				}
				$res .='</p>'."\n";
				$res .= '</div>'."\n";
			}

			// Ajout des commentaires, s'il le faut
			if ($GLOBALS['_BAZAR_']['commentaire'] == 1) {
				$res .= '<br />'."\n".'<div class="BAZ_cadre_fiche BAZ_cadre_fiche_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
				$res .= '<h2 class="BAZ_titre BAZ_titre_'.$GLOBALS['_BAZAR_']['class'].'">'.BAZ_LES_COMMENTAIRES.'</h2>'."\n";
				$requete = 'SELECT * FROM bazar_commentaires WHERE bc_ce_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'].' ORDER BY bc_date ASC';
				$resultat = $GLOBALS['_BAZAR_']['db']->query($requete) ;
				if (DB::isError ($resultat)) {
					return $resultat->getMessage().'<br />'.$resultat->getDebugInfo();
				}
				if ($resultat->numRows()>0) {
					$res .= BAZ_IL_Y_A.$resultat->numRows().' ';
					if ($resultat->numRows()==1) $res .= BAZ_COMMENTAIRE.'<br />'."\n";
					else $res .= BAZ_COMMENTAIRES.'<br />'."\n";
					while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
						$res .= '<div class="BAZ_bulle_corps BAZ_bulle_corps_'.$GLOBALS['_BAZAR_']['class'].'">'."\n";
						//affichage du commentaire
						$res .= $ligne['bc_commentaire'].'<br />'."\n";
						$res .= '<div class="BAZ_commentaire_admin">'.BAZ_PAR.' : <strong>'.$ligne['bc_nom'].'</strong>'.BAZ_ECRIT_LE.strftime('%d.%m.%Y %H:%M',strtotime($ligne['bc_date']));
						//pour les identifies seulement, administrateurs de la rubrique ou superadmins
						if ($est_admin==1) {
							$url_comment= $GLOBALS['_BAZAR_']['url'];
							$url_comment->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
							$url_comment->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
							$url_comment->addQueryString('id_commentaire', $ligne['bc_id_commentaire']);
							$res .= '&nbsp;&nbsp;<a href="'.$url_comment->getURL().'">'.BAZ_SUPPRIMER.'</a>'."\n";
						}
						$res .= '</div>'."\n";
						$res .= '</div>'."\n";
					}
				}
				else $res .= BAZ_PAS_DE_COMMENTAIRES.'<br /><br />'."\n";

				//formulaire des commentaires
				$form_commentaire = new HTML_QuickForm('bazar_commentaire', 'post', $url);
				$squelette =& $form_commentaire->defaultRenderer();
				$squelette->setFormTemplate("\n".'<form {attributes}>'."\n".'{content}'."\n".'</form>'."\n");
				$squelette->setElementTemplate( '<label style="width:200px;">{label}'.
												'<!-- BEGIN required --><span class="symbole_obligatoire">&nbsp;*</span><!-- END required -->'."\n".
											    '</label><br />'."\n".'{element}<br />'."\n");
				$squelette->setRequiredNoteTemplate("\n".'<span class="symbole_obligatoire"> *{requiredNote}</span>'."\n");
				$option=array('style'=>'width:300px;border:1px solid #000;', 'maxlength'=>100);
				$form_commentaire->addElement('text', 'Nom', BAZ_ENTREZ_VOTRE_NOM, $option);
				$option=array('style'=>'width:95%;height:100px;white-space: pre;padding:3px;border:1px solid #000;');
				require_once 'HTML/QuickForm/textarea.php';
				$formtexte= new HTML_QuickForm_textarea('Commentaire', BAZ_ENTREZ_VOTRE_COMMENTAIRE, $option);
				$form_commentaire->addElement($formtexte) ;
				$option=array('style'=>'border:1px solid #000;');
				$form_commentaire->addElement('submit', 'Envoyer', BAZ_ENVOYER, $option);
				$form_commentaire->addRule('Nom', BAZ_NOM_REQUIS, 'required', '', 'client') ;
				$form_commentaire->addRule('Commentaire', BAZ_COMMENTAIRE_REQUIS, 'required', '', 'client') ;
				$form_commentaire->setRequiredNote(BAZ_CHAMPS_REQUIS) ;
				$res .= $form_commentaire->toHTML();
				$res .= '</div>'."\n";
			}
		}

		// Nettoyage de l'url
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
		$GLOBALS['_BAZAR_']['url']->removeQueryString('id_commentaire');
		$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
		return $res ;
}

function remove_accents( $string )
{
    $string = htmlentities($string);
    return preg_replace("/&([a-z])[a-z]+;/i","$1",$string);
}

function genere_nom_wiki($nom)
{
	// traitement des accents
	$nom = remove_accents($nom);

	//on met des majuscules au début de chaque mot et on fait sauter les espaces
	$temp = explode(" ", ucwords(strtolower($nom)));

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
	return $final;
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
				'FROM bazar_fiche, bazar_nature '.$requeteSQLFrom.' WHERE '.$requeteWhereListe;
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
		$requete_nom_flux = 'select bn_label_nature from bazar_nature where bn_id_nature = '.$typeannonce;
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
	if (BAZ_SANS_AUTH!=true) $utilisateur = new Administrateur_bazar ($GLOBALS['AUTH']) ;
	if ($valide!=0) {
		if ((BAZ_SANS_AUTH!=true) && $utilisateur->isSuperAdmin()) {
			$req_where=1;
		} else {
			if ($req_where==1) {
				$requete .= ' AND ';
			}
			$requete .= '(bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00")'.
						' AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") AND bn_id_nature=bf_ce_nature';
		}
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
			$xml .= XML_Util::createTag('title', null, encoder_en_utf8(html_entity_decode($ligne['bf_titre'])));
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
			$xml .= XML_Util::createTag ('description', null, '<![CDATA['.encoder_en_utf8(html_entity_decode(baz_voir_fiche(1, $ligne['bf_id_fiche']))).']]>' );
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
		$xml .= XML_Util::createTag ('title', null, utf8_encode(html_entity_decode(BAZ_PAS_D_ANNONCES)));
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('link', null, '<![CDATA['.$GLOBALS['_BAZAR_']['url']->getUrl().']]>' );
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('guid', null, '<![CDATA['.$GLOBALS['_BAZAR_']['url']->getUrl().']]>' );
		$xml .= "\r\n          ";
		$xml .= XML_Util::createTag ('description', null, utf8_encode(html_entity_decode(BAZ_PAS_D_ANNONCES)));
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
	$requete = 'SELECT bn_id_nature, bn_label_nature, bn_template FROM bazar_nature WHERE ';
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
		$res = '<h2 class="titre_consulter">'.BAZ_TOUTES_LES_ANNONCES.'</h2>'."\n";
	}
	//cas du type d'annonces predefini
	else {
		if ($nb_type_de_fiches==1) {
			$GLOBALS['_BAZAR_']['id_typeannonce']=end(array_keys($type_annonce_select));
			baz_valeurs_type_de_fiche($GLOBALS['_BAZAR_']['id_typeannonce']);
		}
		$res = '<h2 class="titre_consulter">'.BAZ_TOUTES_LES_ANNONCES_DE_TYPE.' '.$GLOBALS['_BAZAR_']['typeannonce'].'</h2>'."\n";
	}

	if ($nb_type_de_fiches>1)
	{
	  $option=array('onchange' => 'javascript:this.form.submit();');
	  $formtemplate->addElement ('select', 'id_typeannonce', BAZ_TYPEANNONCE, $type_annonce_select, $option) ;
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
		           'FROM bazar_fiche,'.BAZ_ANNUAIRE.' WHERE ' ;

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
	if ($GLOBALS['_BAZAR_']['wiki']->UserIsAdmin()) {
			//$valide_select[0] = BAZ_FICHES_PERIMEES;
			//$valide_select[1] = BAZ_FICHES_PAS_PERIMEES;
			//$valide_select[2] = BAZ_TOUTES_LES_DATES;
			//$formtemplate->addElement ('select', 'perime', BAZ_DATE, $valide_select,'') ;
			//$defauts = array('perime'=>1);
			//$formtemplate->setDefaults($defauts);
	}

	//champs texte pour entrer les mots cles
	$option = array('maxlength'=>255, 'class'=>'boite_recherche', 'value'=>BAZ_MOT_CLE, 'onfocus'=>'if (this.value==\''.BAZ_MOT_CLE.'\') {this.value=\'\';}');
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
		               'bn_label_nature, bf_date_creation_fiche FROM bazar_fiche, bazar_nature '.
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
		    		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$ligne['bf_ce_utilisateur'])) {
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
						$res .= '<a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');">';
						$res .= '<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'delete.gif" alt="'.BAZ_EFFACER.'" /></a>'."\n";
		    		}
		    		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$ligne['bf_ce_utilisateur'] || $ligne['bf_ce_utilisateur']==''  || $ligne['bf_ce_utilisateur']==NULL )) {
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
						$res .= '<a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'modify.gif" alt="'.BAZ_MODIFIER.'" /></a>'."\n";
		    		}
		    		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
					$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		    		$res .= '<a href="'. str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()) .'" title="Voir la fiche">'. $ligne['bf_titre'].'</a></li>'."\n";

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
		$tableau_fiches = baz_requete_recherche_fiches($tableau);
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
function baz_requete_recherche_fiches() 
{
	//$typeannonce, $nbitem, $emetteur, $valide, $requeteSQL = '', $requeteFrom = '', $requeteWhere = '') {
	
	$nb_jointures=0;
	$requeteSQL='';
	$requeteFrom = '' ;
	$requeteWhere = '1 ' ;
	
	if ($GLOBALS['_BAZAR_']['categorie_nature'] != 'toutes') $requeteWhere .= ' AND bn_type_fiche = "'.$GLOBALS['_BAZAR_']['categorie_nature'].'" ';	
	
	if ($GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') 
	{
		$requeteWhere .= ' AND bn_id_nature='.$GLOBALS['_BAZAR_']['id_typeannonce'] ;
		$tableau = formulaire_valeurs_template_champs($GLOBALS['_BAZAR_']['template']) ;
	}
	
	$requeteWhere .= ' AND bn_id_nature=bf_ce_nature ' ;
	if (isset($GLOBALS['_BAZAR_']['langue'])) {
		$requeteWhere .= ' AND bn_ce_i18n like "'.$GLOBALS['_BAZAR_']['langue'].'%" ';
	}
	
	
	//on parcours le template pour ajouter les valeurs passées dans le moteur de recherche
	if ( isset($tableau) ) 
	{
		for ($i = 0; $i < count ($tableau); $i++) {
			if ($tableau[$i][0] == 'liste' || $tableau[$i][0] == 'checkbox' ||$tableau[$i][0] == 'listefiche' || $tableau[$i][0] == 'checkboxfiche') 
			{
					$requeteWhere .= $tableau[$i][0]($formtemplate, $tableau[$i], 'requete_recherche', '');
			}
		}
	}
	//$requeteWhere .= $requeteWhereListe;
	
	if ($GLOBALS['_BAZAR_']['id_typeannonce']!='toutes') {
		$requeteWhere .= ' AND bf_ce_nature="'.$GLOBALS['_BAZAR_']['id_typeannonce'].'" AND '.$requeteWhere;
	}

	//preparation de la requete pour trouver les mots cles
	if (($_REQUEST['recherche_mots_cles']!='')and($_REQUEST['recherche_mots_cles']!=BAZ_MOT_CLE)) {
		//decoupage des mots cles
		$recherche = split(' ', $_REQUEST['recherche_mots_cles']) ;
		$nbmots=count($recherche);
		$requeteSQL='';
		for ($i=0; $i<$nbmots; $i++) {
			if ($i>0) $requeteSQL.=' OR ';
			$requeteSQL.=' bf_id_fiche IN ( SELECT bfvt_ce_fiche FROM bazar_fiche_valeur_texte WHERE bfvt_texte LIKE "%'.$recherche[$i].'%" ) OR bf_id_fiche IN ( SELECT bfvtl_ce_fiche FROM bazar_fiche_valeur_texte_long WHERE bfvtl_texte_long LIKE "%'.$recherche[$i].'%" ) ';
			
		}
	}
	if (!isset($_REQUEST['nature'])) {
		if (!isset ($GLOBALS['_BAZAR_']['id_nature'])) $typedefiches = $tableau_typeannonces;
		else $typedefiches = $GLOBALS['_BAZAR_']['id_nature'] ;
	} else {
		$typedefiches = $_REQUEST['nature'] ;
		if ($typedefiches == 'toutes') $typedefiches = $tableau_typeannonces ;
	}
	if ($typeannonce!='toutes') $typedefiches=$typeannonce;
	
	if (isset($_REQUEST['valides'])) {$valides=$_REQUEST['valides'];}
	else {$valides=1;}
	
	//generation de la liste de flux a afficher
	if (!isset($_REQUEST['personnes'])) $_REQUEST['personnes']='tous';
	
	// generation de la requete MySQL personnalisee
	$requete = 'SELECT * '.
				'FROM bazar_fiche, bazar_nature '.$requeteFrom.' WHERE '.$requeteWhere;
	if ($valides!=2) {
		$requete .= ' AND bf_statut_fiche='.$valides;
	}

	if ($valides!=0) {
		if (isset($_POST['perime'])&& $_POST['perime']==0) {
				$requete .= ' AND NOT (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") OR NOT (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") ';
			} elseif  (isset($_POST['perime'])&& $_POST['perime']==2) {	} else {
            	$requete .= ' AND (bf_date_debut_validite_fiche<=NOW() or bf_date_debut_validite_fiche="0000-00-00") AND (bf_date_fin_validite_fiche>=NOW() or bf_date_fin_validite_fiche="0000-00-00") ';
			}
		$requete .= ' AND bn_id_nature=bf_ce_nature';
	}
	if ($emetteur!='' && $emetteur!='tous') {
		$requete .= ' AND bf_ce_utilisateur='.$emetteur;		
	}
	if ($requeteSQL!='') {
		$requete .= ' AND ('.$requeteSQL.')';
	}
	if ($GLOBALS['_BAZAR_']['tri']=='alphabetique') $requete .= ' ORDER BY bf_titre ASC';
	else $requete .= ' ORDER BY  bf_date_debut_validite_fiche DESC, bf_date_fin_validite_fiche DESC, bf_date_maj_fiche DESC';

	if ($nbitem!='') {$requete .= ' LIMIT 0,'.$nbitem;}
	
	//echo '<textarea style="width:100%;height:100px;">'.$requete.'</textarea>';
	
	return $GLOBALS['_BAZAR_']['db']->getAll($requete);
}

function baz_afficher_liste_resultat($tableau_fiches) {
	$res = '<p class="BAZ_info">'.BAZ_IL_Y_A;
	$nb_result=count($tableau_fiches);
	if ($nb_result<=1) $res .= $nb_result.' '.BAZ_FICHE_CORRESPONDANTE.'</p>'."\n";
	else $res .= $nb_result.' '.BAZ_FICHES_CORRESPONDANTES.'</p>'."\n";

	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);

	//$donnees = array();
	//while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
	//	$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $ligne['bf_id_fiche']) ;
	//	array_push ($donnees, $ligne);
	//}
	
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
		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$valeur[1])) {
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
				$res .= '<a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');">';
				$res .= '<img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'delete.gif" alt="'.BAZ_EFFACER.'" /></a>'."\n";
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		}
		if ($GLOBALS['_BAZAR_']['nomwiki']!='' && ($GLOBALS['_BAZAR_']['isAdmin'] || $GLOBALS['_BAZAR_']['nomwiki']['name']==$valeur[1] || $valeur[1]=='' || $ligne['bf_ce_utilisateur']==NULL )) {
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
				$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
				$res .= '<a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"><img src="'.BAZ_CHEMIN.'presentation'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'modify.gif" alt="'.BAZ_MODIFIER.'" /></a>'."\n";
				$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
		}
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
		$res .= '<a href="'. str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()) .'" title="Voir la fiche">'. $valeur[3].'</a>'."\n".'</li>'."\n";
		$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	}
	$res .= '</ul>'."\n".'<div class="bazar_numero">'.$pager->links.'</div>'."\n";

	// Nettoyage de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
	$GLOBALS['_BAZAR_']['url']->removeQueryString('id_fiche');
	$GLOBALS['_BAZAR_']['url']->removeQueryString('typeannonce');
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



/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.fonct.php,v $
* Revision 1.10  2010/03/04 14:19:03  mrflos
* nouvelle version bazar
*
* Revision 1.9  2009/10/14 10:14:02  ddelon
* menage chemin
*
* Revision 1.8  2009/09/09 15:36:37  mrflos
* maj css
* ajout de la google api v3
* possibilitÃ© d'insÃ©rer des utilisateurs wikini par bazar
* installation automatique du fichier sql avec type d'annonces par dÃ©faut
*
* Revision 1.7  2009/08/01 17:01:58  mrflos
* nouvelle action bazarcalendrier, correction bug typeannonce, validitÃ© html amÃ©liorÃ©e
*
* Revision 1.6  2008/09/09 12:46:42  mrflos
* sÃ©curitÃ©: seuls les identifies peuvent supprimer une fiche ou un type de fiche
*
* Revision 1.5  2008/08/28 14:49:52  mrflos
* amÃ©lioration des performances de bazar : google map pas chargÃ©e systematiquement
* correction bug flux rss
* correction bug calendrier
*
* Revision 1.4  2008/08/28 12:23:39  mrflos
* amÃ©rioration de la gestion des categories de fiches
*
* Revision 1.3  2008/08/27 13:18:57  mrflos
* maj gÃ©nÃ©rale
*
* Revision 1.2  2008/07/29 17:32:25  mrflos
* maj gÃ©nÃ©rale
*
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
* remise en Ã©tat du CVS...
*
* Revision 1.57.2.12  2007/03/16 14:49:24  alexandre_tb
* si la date de debut d evenement est superieure a la date de fin alors on met
* la meme date dans les deux champs (coherence)
*
* Revision 1.57.2.11  2007/03/07 17:40:57  jp_milcent
* Ajout d'id sur les colonnes et gestion par les CSS des styles du tableau des abonnements.
*
* Revision 1.57.2.10  2007/03/07 17:20:19  jp_milcent
* Ajout du nettoyage systï¿½matique des URLs.
*
* Revision 1.57.2.9  2007/03/06 16:23:24  jp_milcent
* Nettoyage de l'url pour la gestion des droits.
*
* Revision 1.57.2.8  2007/03/05 14:33:44  jp_milcent
* Suppression de l'appel ï¿½ Mes_Fiches dans la fonction baz_formulaire
*
* Revision 1.57.2.7  2007/03/05 10:28:03  alexandre_tb
* correction d un commentaire
*
* Revision 1.57.2.6  2007/02/15 13:42:16  jp_milcent
* Utilisation de IN ï¿½ la place du = dans les requï¿½tes traitant les catï¿½gories de fiches.
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
* insertion de la date du jour dans bf_date_debut_validite_fiche quand il n'y a pas ce champs dans le formulaire (ï¿½vite le 0000-00-00)
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
* Le formulaire pour se logguer dans l'action saisir reste sur l'action saisir aprï¿½s
*
* Revision 1.55  2006/09/21 14:19:39  florian
* amÃ©lioration des fonctions liÃ©s au wikini
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
* - ajout des entrÃ©es de formulaire fichier, url, et image
* - correction bug d'affichage du mode de saisie
*
* Revision 1.37  2006/01/24 14:11:11  alexandre_tb
* correction de bug sur l'ajout d'une image et d'un fichier
*
* Revision 1.36  2006/01/19 17:42:11  florian
* ajout des cases Ã  cocher prÃ©-cochÃ©es pour les maj
*
* Revision 1.35  2006/01/18 11:06:51  florian
* correction erreur saisie date
*
* Revision 1.34  2006/01/18 10:53:28  florian
* corrections bugs affichage fiche
*
* Revision 1.33  2006/01/18 10:07:34  florian
* recodage de l'insertion et de la maj des donnÃ©es relatives aux listes et checkbox dans des formulaires
*
* Revision 1.32  2006/01/18 10:03:36  florian
* recodage de l'insertion et de la maj des donnÃ©es relatives aux listes et checkbox dans des formulaires
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
* prise en chage des checkbox, reste la mise ï¿½ jour ï¿½ gï¿½rer
*
* Revision 1.27  2006/01/04 15:30:56  alexandre_tb
* mise en forme du code
*
* Revision 1.26  2006/01/03 10:19:31  florian
* Mise Ã  jour pour accepter des parametres dans papyrus: faire apparaitre ou non le menu, afficher qu'un type de fiches, dÃ©finir l'action par dÃ©faut...
*
* Revision 1.25  2005/12/20 14:49:35  ddelon
* Fusion Head vers Livraison
*
* Revision 1.24  2005/12/16 15:44:40  alexandre_tb
* ajout de l'option restreindre dï¿½pï¿½t
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
* corrections bugs, ajout des cases Ã  cocher
*
* Revision 1.18  2005/11/18 16:03:23  florian
* correction bug html entites
*
* Revision 1.17  2005/11/17 18:48:02  florian
* corrections bugs + amÃ©lioration de l'application d'inscription
*
* Revision 1.16  2005/11/07 17:30:36  florian
* ajout controle sur les listes pour la saisie
*
* Revision 1.15  2005/11/07 17:05:45  florian
* amÃ©lioration validation conditions de saisie, ajout des rÃ¨gles spÃ©cifiques de saisie des formulaires
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
* amÃ©lioration de l'interface de bazar, de maniÃ¨re a simplifier les consultations, et Ã  harmoniser par rapport aux Ressources
*
* Revision 1.9  2005/10/10 16:22:52  alexandre_tb
* Correction de bug. Lorsqu'on revient en arriï¿½re aprï¿½s avoir validï¿½ un formulaire.
*
* Revision 1.8  2005/09/30 13:50:07  alexandre_tb
* correction bug date parution ressource
*
* Revision 1.7  2005/09/30 13:15:58  ddelon
* compatibilitï¿½ php5
*
* Revision 1.6  2005/09/30 13:00:05  ddelon
* Fiche bazar generique
*
* Revision 1.5  2005/09/30 12:22:54  florian
* Ajouts commentaires pour fiche, modifications graphiques, maj SQL
*
* Revision 1.3  2005/07/21 19:03:12  florian
* nouveautÃ©s bazar: templates fiches, correction de bugs, ...
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
* dï¿½buggage RSS
*
* Revision 1.7  2004/07/06 16:22:01  florian
* dï¿½buggage modification + MAJ flux RSS
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
