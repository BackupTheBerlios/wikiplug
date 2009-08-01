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
// CVS : $Id: bazar.php,v 1.4 2009/08/01 17:01:59 mrflos Exp $
/**
* wiki.php
*
* Description :
*
*@package wkbazar
//Auteur original :
*@author        florian SCHMITT <florian.schmitt@laposte.net>
//Autres auteurs :
*@author        Aucun
*@copyright     Kaleidos-coop.org 2008
*@version       $Revision: 1.4 $ $Date: 2009/08/01 17:01:59 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

//recuperation des parametres
$action = $this->GetParameter(BAZ_VARIABLE_ACTION);
if (!empty($action)) {
	$_GET[BAZ_VARIABLE_ACTION]=$action;
}


$vue = $this->GetParameter("vue");
if (!empty($vue) && !isset($_GET[BAZ_VARIABLE_VOIR])) {
	$_GET[BAZ_VARIABLE_VOIR]=$vue;
}
//si rien n'est donne, on met la vue de consultation
elseif (!isset($_GET[BAZ_VARIABLE_VOIR])) {
	$_GET[BAZ_VARIABLE_VOIR]=BAZ_VOIR_CONSULTER;
}

$GLOBALS['_BAZAR_']['affiche_menu'] = $this->GetParameter("voirmenu");

$categorie_nature = $this->GetParameter("categorienature");
if (!empty($categorie_nature)) {
	$GLOBALS['_BAZAR_']['categorie_nature']=$categorie_nature;
}
//si rien n'est donne, on affiche toutes les categories
else {
	$GLOBALS['_BAZAR_']['categorie_nature']='toutes';
}

$id_typeannonce = $this->GetParameter("idtypeannonce");
if (!empty($id_typeannonce)) {
	$GLOBALS['_BAZAR_']['id_typeannonce']=$id_typeannonce;
}
//si rien n'est donne, on affiche toutes les annonces
else {
	$GLOBALS['_BAZAR_']['id_typeannonce']='toutes';
}

//Recuperer les eventuelles variables passees en GET ou en POST
if (isset($_REQUEST['id_fiche'])) {
	$GLOBALS['_BAZAR_']['id_fiche']=$_REQUEST['id_fiche'];
	// recuperation du type d'annonce a partir de la fiche
	$requete = 'select bf_ce_nature from bazar_fiche where bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'] ;
	$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete) ;
	if (DB::isError($resultat)) {
		echo $resultat->getMessage().'<br />'.$resultat->getInfoDebug();
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT) ;
	$GLOBALS['_BAZAR_']['id_typeannonce'] = $ligne->bf_ce_nature ;
	$resultat->free();
} else {
	$GLOBALS['_BAZAR_']['id_fiche']=$this->GetParameter("numfiche");
}

if (isset($_POST['typeannonce'])) $GLOBALS['_BAZAR_']['id_typeannonce']=$_POST['typeannonce'];
if ($GLOBALS['_BAZAR_']['id_typeannonce']!='toutes') {
	$requete = 'SELECT * FROM bazar_nature WHERE bn_id_nature = '.$GLOBALS['_BAZAR_']['id_typeannonce'];
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
}

$GLOBALS['_BAZAR_']['nomwiki']=$this->getUser();
if ($this->UserIsInGroup('admins')) {
	$GLOBALS['_BAZAR_']['isAdmin']=true;
} 
else {
	$GLOBALS['_BAZAR_']['isAdmin']=false;
}

//variable d'affichage du bazar
$res = '';
// +------------------------------------------------------------------------------------------------------+
// |                                            CORPS du PROGRAMME                                        |
// +------------------------------------------------------------------------------------------------------+

if ($GLOBALS['_BAZAR_']['affiche_menu']!='0') {
	$res .= '<div class="BAZ_menu">'."\n".'<ul>'."\n";
	// Gestion de la vue par defaut
	if (!isset($_GET[BAZ_VARIABLE_VOIR])) {
		$_GET[BAZ_VARIABLE_VOIR] = BAZ_VOIR_DEFAUT;
	}

	//partie consultation d'annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_CONSULTER))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
		$res .= '<li id="menu_consulter"';
		if ((isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_CONSULTER)) $res .=' class="onglet_actif" ';
		$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_CONSULTER.'</a>'."\n".'</li>'."\n";
	}

	// Mes fiches
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_MES_FICHES))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_MES_FICHES);
		$res .= '<li id="menu_mes_fiches"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_MES_FICHES) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_VOIR_VOS_ANNONCES.'</a>'."\n".'</li>'."\n";
	}

	//partie abonnement aux annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_S_ABONNER))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_S_ABONNER);
		$res .= '<li id="menu_inscrire"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_S_ABONNER) $res .=' class="onglet_actif" ';
		$res .= '><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_S_ABONNER.'</a></li>'."\n" ;
	}

	//partie saisie d'annonces
	if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_SAISIR))) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
		$res .= '<li id="menu_deposer"';
		if (isset($_GET[BAZ_VARIABLE_VOIR]) && ($_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_SAISIR )) $res .=' class="onglet_actif" ';
		$res .='><a href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'">'.BAZ_SAISIR.'</a>'."\n".'</li>'."\n";
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
	$res.= '</ul>'."\n".'</div>'."\n".'<div style="clear:both;">&nbsp;</div>'."\n";
}

if (isset($_GET['message'])) {
	$res .= '<p class="BAZ_info">';
	if ($_GET['message']=='ajout_ok') $res.= BAZ_FICHE_ENREGISTREE;
	if ($_GET['message']=='modif_ok') $res.= BAZ_FICHE_MODIFIEE;
	if ($_GET['message']=='delete_ok') $res.= BAZ_FICHE_SUPPRIMEE;
	$res .= '</p>'."\n";
}

// La resolution des actions ci-dessous AVANT l affichage des vues afin
// d afficher des vues correctes
if (isset($_GET[BAZ_VARIABLE_ACTION])) {
	switch ($_GET[BAZ_VARIABLE_ACTION]) {
		case BAZ_ACTION_VOIR_VOS_ANNONCES : $res .= mes_fiches(); break;
		case BAZ_ANNONCES_A_VALIDER : $res .= fiches_a_valider(); break;
		case BAZ_ADMINISTRER_ANNONCES : $res .= baz_administrer_annonces(); break;
		case BAZ_SUPPRIMER_FICHE : $res .= baz_suppression(); break;
		case BAZ_VOIR_FICHE : $res .= baz_voir_fiche(1); break;
//		case BAZ_ACTION_NOUVEAU_V : $res .= baz_formulaire(BAZ_ACTION_NOUVEAU_V); break;
		case BAZ_ACTION_SUPPRESSION : $res .= baz_suppression(); break;
		case BAZ_ACTION_PUBLIER : $res .= publier_fiche(1).baz_voir_fiche(1); break;
		case BAZ_ACTION_PAS_PUBLIER : $res .= publier_fiche(0).baz_voir_fiche(1); break;
		case BAZ_S_INSCRIRE : $res .= baz_s_inscrire(); break;
		case BAZ_VOIR_FLUX_RSS : header('Content-type: text/xml; charset=UTF-8');afficher_flux_rss();exit(0);break;
	}
}

if (isset ($_GET[BAZ_VARIABLE_VOIR])) {
		switch ($_GET[BAZ_VARIABLE_VOIR]) {
			case BAZ_VOIR_CONSULTER:
			if (isset ($_GET[BAZ_VARIABLE_ACTION]) && $_GET[BAZ_VARIABLE_ACTION] != BAZ_VOIR_TOUTES_ANNONCES) {
				$res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ;
				if ($_GET[BAZ_VARIABLE_ACTION] == BAZ_ACTION_MODIFIER_V) $res .= baz_voir_fiche(1);
			} else $res .= baz_liste($GLOBALS['_BAZAR_']['id_typeannonce'],$GLOBALS['_BAZAR_']['categorie_nature']);
			break;
			case BAZ_VOIR_MES_FICHES :
			if (isset ($_GET[BAZ_VARIABLE_ACTION])) $res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ; else $res .= mes_fiches();
			break;
			case BAZ_VOIR_S_ABONNER : $res .= baz_s_inscrire();
			break;
			case BAZ_VOIR_SAISIR :
			if (isset ($_GET[BAZ_VARIABLE_ACTION])) $res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ; else $res .= baz_formulaire(BAZ_DEPOSER_ANNONCE);
			break;
			case BAZ_VOIR_FORMULAIRE : $res .= baz_gestion_formulaire();
			break;
			case BAZ_VOIR_ADMIN:
			if (isset($_GET[BAZ_VARIABLE_ACTION])) $res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ; else $res .= fiches_a_valider();
			break;
			case BAZ_VOIR_GESTION_DROITS: $res .= baz_gestion_droits();
			break;
			default :
			$res .= baz_liste($GLOBALS['_BAZAR_']['id_typeannonce']);
		}
}
//affichage de la page
echo $res ;


/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.php,v $
* Revision 1.4  2009/08/01 17:01:59  mrflos
* nouvelle action bazarcalendrier, correction bug typeannonce, validité html améliorée
*
* Revision 1.3  2008/09/09 12:46:42  mrflos
* sécurité: seuls les identifies peuvent supprimer une fiche ou un type de fiche
*
* Revision 1.2  2008/08/27 13:18:57  mrflos
* maj générale
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
* Revision 1.1  2006/12/13 17:06:36  florian
* Ajout de l'applette bazar.
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
