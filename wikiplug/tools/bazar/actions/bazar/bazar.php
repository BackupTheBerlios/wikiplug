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
// CVS : $Id: bazar.php,v 1.1 2008/07/07 18:00:40 mrflos Exp $
/**
*
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
*@author        Florian Schmitt <florian@ecole-et-nature.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:40 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

//error_reporting(E_ALL);
require_once PAP_CHEMIN_API_PEAR.'DB.php' ;
//require_once PAP_CHEMIN_API_PEAR.'Auth.php' ;
require_once 'configuration/baz_config.inc.php'; //fichier de configuration de Bazar
if (BAZ_SANS_AUTH!=true) echo 'toto';//require_once 'bibliotheque/bazar.class.php';
//require_once 'bibliotheque/bazar.fonct.php'; //fichier des fonctions de Bazar

if (defined('PAP_VERSION')) { //si on est dans Papyrus
	GEN_stockerStyleExterne( 'bazar_interne', 'client/bazar/bazar.interne.css');
}

//**********************************************************************************************************
//initialisation des param�tres papyrus
//**********************************************************************************************************
//si un parametre est pr�cis� dans le gestionnaire de menus papyrus, on le prends en compte

//parametre action pour lancer directement l'action indiqu�e
if (!isset($_GET[BAZ_VARIABLE_ACTION])and(isset($GLOBALS['_GEN_commun']['info_application']->action))) {
	$_GET[BAZ_VARIABLE_ACTION]=$GLOBALS['_GEN_commun']['info_application']->action;
}

//parametre vue pour afficher directement une vue
if (!isset($_GET[BAZ_VARIABLE_VOIR])and(isset($GLOBALS['_GEN_commun']['info_application']->vue))) {
	$_GET[BAZ_VARIABLE_VOIR]=$GLOBALS['_GEN_commun']['info_application']->vue;
}

// Si le parametre vue est vide on le positionne a 1
if (!isset($_GET[BAZ_VARIABLE_VOIR])) {
	$_GET[BAZ_VARIABLE_VOIR] = BAZ_VOIR_CONSULTER;
}


//parametre voir_menu pour afficher le menu ou pas (par d�faut, il l'affiche)
if ((isset($GLOBALS['_GEN_commun']['info_application']->voir_menu))and($GLOBALS['_GEN_commun']['info_application']->voir_menu==0)) {
	$GLOBALS['_BAZAR_']['affiche_menu']=0;
}
else $GLOBALS['_BAZAR_']['affiche_menu']=1;

//parametre categorie_nature pour pr�ciser quels types de fiches sont montrees (par d�faut, il affiche les id_menu=0)
if (isset($GLOBALS['_GEN_commun']['info_application']->categorie_nature)) {
	$GLOBALS['_BAZAR_']['categorie_nature']=$GLOBALS['_GEN_commun']['info_application']->categorie_nature;
}
elseif (isset($_REQUEST['categorie_nature'])) {
	$GLOBALS['_BAZAR_']['categorie_nature']=$_REQUEST['categorie_nature'];
}
else $GLOBALS['_BAZAR_']['categorie_nature']=0;

//parametre id_nature pour afficher un certain type de fiche (par d�faut, tous les types de fiches)
if (isset($GLOBALS['_GEN_commun']['info_application']->id_nature)) {
	$GLOBALS['_BAZAR_']['id_typeannonce']=$GLOBALS['_GEN_commun']['info_application']->id_nature;
}
elseif (!isset($GLOBALS['_BAZAR_']['typeannonce'])) $GLOBALS['_BAZAR_']['typeannonce']='toutes';

//**********************************************************************************************************
//initialisation de la variable globale de bazar
//**********************************************************************************************************
if (!BAZ_SANS_AUTH) $GLOBALS['id_user']=$GLOBALS['AUTH']->getAuthData(BAZ_CHAMPS_ID);

//Recuperer les eventuelles variables passees en GET ou en POST
if (isset($_REQUEST['id_fiche'])) {
	$GLOBALS['_BAZAR_']['id_fiche']=$_REQUEST['id_fiche'];
	// r�cup�ration du type d'annonce � partir de la fiche
	$requete = 'select bf_ce_nature from bazar_fiche where bf_id_fiche='.$GLOBALS['_BAZAR_']['id_fiche'] ;
	$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete) ;
	if (DB::isError($resultat)) {
		echo $resultat->getMessage().'<br />'.$resultat->getInfoDebug();
	}
	$ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT) ;
	$GLOBALS['_BAZAR_']['id_typeannonce'] = $ligne->bf_ce_nature ;
	$resultat->free();
}
if (isset($_REQUEST['typeannonce'])) {
	$GLOBALS['_BAZAR_']['id_typeannonce']=$_REQUEST['typeannonce'];
}

if ((isset($GLOBALS['_BAZAR_']['id_typeannonce']))and($GLOBALS['_BAZAR_']['id_typeannonce']!='toutes')) {
	$requete = 'SELECT bn_label_nature, bn_condition, bn_template, bn_commentaire, bn_appropriation, bn_image_titre, bn_image_logo';
	$requete .= ' FROM bazar_nature WHERE bn_id_nature = '.$GLOBALS['_BAZAR_']['id_typeannonce'];
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
	$GLOBALS['_BAZAR_']['image_titre']=$ligne['bn_image_titre'];
	$GLOBALS['_BAZAR_']['image_logo']=$ligne['bn_image_logo'];
}
if (!isset($GLOBALS['_BAZAR_']['id_typeannonce'])) $GLOBALS['_BAZAR_']['id_typeannonce'] = 'toutes' ;
// +------------------------------------------------------------------------------------------------------+
// |                                           LISTE de FONCTIONS                                         |
// +------------------------------------------------------------------------------------------------------+
if ($GLOBALS['_BAZAR_']['affiche_menu']) {
	//---------------le menu de l'appli-----------
	function afficherContenuNavigation () {
		$res ='<ul id="BAZ_menu">'."\n";

		// Gestion de la vue par d�faut
		if (!isset($_GET[BAZ_VARIABLE_VOIR])) {
			$_GET[BAZ_VARIABLE_VOIR] = BAZ_VOIR_DEFAUT;
		}

		//partie consultation d'annonces
		if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_CONSULTER))) {
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
			$res .= '<li id="consulter"';
			if ((isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_CONSULTER)) $res .=' class="onglet_actif" ';
			$res .='><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_CONSULTER.'</a>'."\n".'</li>'."\n";
		}

		// Mes fiches
		if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_MES_FICHES))) {
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_MES_FICHES);
			$res .= '<li id="mes_fiches"';
			if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR] == BAZ_VOIR_MES_FICHES) $res .=' class="onglet_actif" ';
			$res .= '><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_VOIR_VOS_ANNONCES.'</a>'."\n".'</li>'."\n";
		}

		//partie abonnement aux annonces
		if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_S_ABONNER))) {
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_S_ABONNER);
			$res .= '<li id="inscrire"';
			if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_S_ABONNER) $res .=' class="onglet_actif" ';
			$res .= '><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_S_ABONNER.'</a></li>'."\n" ;
		}

		//partie saisie d'annonces
		if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_SAISIR))) {
			$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
			$res .= '<li id="deposer"';
			if (isset($_GET[BAZ_VARIABLE_VOIR]) && ($_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_SAISIR )) $res .=' class="onglet_actif" ';
			$res .='><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_SAISIR.'</a>'."\n".'</li>'."\n";
		}

		//choix des administrateurs
		$utilisateur = new Administrateur_bazar($GLOBALS['AUTH']) ;
		$est_admin=0;
		if ($GLOBALS['AUTH']->getAuth()) {
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
					$res .='><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_ADMINISTRER.'</a></li>'."\n";
				}

				if ($utilisateur->isSuperAdmin()) {
					if (strstr(BAZ_VOIR_AFFICHER, strval(BAZ_VOIR_GESTION_DROITS))) {
						$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_GESTION_DROITS);
						$res .= '<li id="gerer"';
						if (isset($_GET[BAZ_VARIABLE_VOIR]) && $_GET[BAZ_VARIABLE_VOIR]==BAZ_VOIR_GESTION_DROITS) $res .=' class="onglet_actif" ';
						$res .='><a href="'.$GLOBALS['_BAZAR_']['url']->getURL().'">'.BAZ_GESTION_DES_DROITS.'</a></li>'."\n" ;
					}
				}
			}
		}
		// Au final, on place dans l url, l action courante
		if (isset($_GET[BAZ_VARIABLE_VOIR])) $GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, $_GET[BAZ_VARIABLE_VOIR]);
		$res.= '</ul>'."\n";
	    return $res ;
	}
}

function afficherContenuCorps() {
	$res = '';
	$res.='<h1 id="titre_bazar">'.$GLOBALS['_GEN_commun']['info_menu']->gm_titre.'</h1>'."\n";

	// La resolution des actions ci-dessous AVANT l afichage des vues afin
	// d afficher des vues correctes

	if (isset($_GET[BAZ_VARIABLE_ACTION])) {

		switch ($_GET[BAZ_VARIABLE_ACTION]) {
			case BAZ_ACTION_VOIR_VOS_ANNONCES : $res .= mes_fiches(); break;
			case BAZ_ANNONCES_A_VALIDER : $res .= fiches_a_valider(); break;
			case BAZ_ADMINISTRER_ANNONCES : $res .= baz_administrer_annonces(); break;
			case BAZ_SUPPRIMER_FICHE : $res .= baz_suppression().baz_liste('',$GLOBALS['id_user'],''); break;
			case BAZ_VOIR_FICHE : $res .= baz_voir_fiche(1); break;
			case BAZ_ACTION_NOUVEAU_V : $res .= baz_formulaire(BAZ_ACTION_NOUVEAU_V).mes_fiches(); break;
			case BAZ_ACTION_SUPPRESSION : $res .= baz_suppression(); unset ($_GET[BAZ_VARIABLE_ACTION]); break;
			case BAZ_ACTION_PUBLIER : publier_fiche(1) ; break;
			case BAZ_ACTION_PAS_PUBLIER : publier_fiche(0) ;$res .= fiches_a_valider(); break;
			case BAZ_S_INSCRIRE : $res .= baz_s_inscrire(); break;
			case BAZ_VOIR_FLUX_RSS : header('Content-type: text/xml; charset=UTF-8');include("bazarRSS.php");exit(0);break;
		}

	}
	if (isset ($_GET[BAZ_VARIABLE_VOIR])) {
			switch ($_GET[BAZ_VARIABLE_VOIR]) {
				case BAZ_VOIR_CONSULTER:
				if (isset ($_GET[BAZ_VARIABLE_ACTION]) && $_GET[BAZ_VARIABLE_ACTION] != BAZ_VOIR_TOUTES_ANNONCES) {
					$res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ;
					if ($_GET[BAZ_VARIABLE_ACTION] == BAZ_ACTION_MODIFIER_V) $res .= baz_voir_fiche(1);
				} else $res .= baz_liste($GLOBALS['_BAZAR_']['id_typeannonce']);
				break;
				case BAZ_VOIR_MES_FICHES :
				if (isset ($_GET[BAZ_VARIABLE_ACTION])) $res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ; else $res .= mes_fiches();
				break;
				case BAZ_VOIR_S_ABONNER : $res .= baz_s_inscrire();
				break;
				case BAZ_VOIR_SAISIR :
				if (isset ($_GET[BAZ_VARIABLE_ACTION])) $res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ; else $res .= baz_formulaire(BAZ_DEPOSER_ANNONCE);
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
	return $res ;
}


/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.php,v $
* Revision 1.1  2008/07/07 18:00:40  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:46  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.49.2.3  2008-01-29 09:43:19  alexandre_tb
* Utilisation d un redirection pour eviter que les formulaires soient valides 2 fois
*
* Revision 1.49.2.2  2008-01-11 14:07:39  alexandre_tb
* Remplacement de la variable action ecrite en dur par la constante BAZ_VARIABLE_ACTION
*
* Revision 1.49.2.1  2007-12-04 09:01:17  alexandre_tb
* suppression de vieux code commenté.
*
* Revision 1.49  2007-10-24 13:26:02  alexandre_tb
* bug : double saisie d url
*
* Revision 1.48  2007-10-17 08:24:59  alexandre_tb
* correction multilinguisme
*
* Revision 1.47  2007-10-01 12:13:53  alexandre_tb
* bug, affichage vue / action pour la vue "administrer"
*
* Revision 1.46  2007-09-28 14:29:13  jp_milcent
* Ajout de la gestion de l'affichage des vues ou pas.
*
* Revision 1.45  2007-09-18 14:24:01  alexandre_tb
* onglet administrer
*
* Revision 1.44  2007-09-06 15:39:28  alexandre_tb
* fixation d une valeur par defaut a la variable vue pour
* eviter un ecran vide si pas de parametres dans le menu
*
* Revision 1.43  2007-08-27 12:26:04  alexandre_tb
* Mise en place de l action BAZ_VOIR_ADMIN
*
* Revision 1.42  2007-07-05 08:29:24  alexandre_tb
* modification du charset iso-8859-1 vers utf8 lors l'envoie des entetes xml.
*
* Revision 1.41  2007-07-04 10:05:12  alexandre_tb
* ajout d une variable $_GET['vue'] en complement de la variable action.
* Elle correspond aux 6 vues du bazar (consulter, mes fiches, s'abonner, saisir, administrer, gestion des droits)
*
* Revision 1.40  2007/04/11 08:30:12  neiluj
* remise en état du CVS...
*
* Revision 1.35.2.2  2007/03/07 16:53:17  jp_milcent
* Suppression du query string "action" et non pas "nature"
*
* Revision 1.35.2.1  2007/02/15 13:42:16  jp_milcent
* Utilisation de IN � la place du = dans les requ�tes traitant les cat�gories de fiches.
* Permet d'utiliser la syntaxe 1,2,3 dans la configuration de categorie_nature.
*
* Revision 1.35  2006/10/05 08:53:50  florian
* amelioration moteur de recherche, correction de bugs
*
* Revision 1.34  2006/09/04 15:25:12  alexandre_tb
* ajout d'un id dans la balise HTML du titre
*
* Revision 1.33  2006/06/21 15:41:42  alexandre_tb
* r�tablissement du menu mes fiches
*
* Revision 1.32  2006/06/21 15:40:15  alexandre_tb
* r�tablissement du menu mes fiches
*
* Revision 1.31  2006/05/19 13:54:32  florian
* stabilisation du moteur de recherche, corrections bugs, lien recherche avancee
*
* Revision 1.30  2006/04/28 12:46:14  florian
* integration des liens vers annuaire
*
* Revision 1.29  2006/04/24 10:16:22  alexandre_tb
* ajout de la globale filtre.
* elle remplace (� terme) cat�gorie nature
*
* Revision 1.28  2006/03/29 13:05:41  alexandre_tb
* utilisation de la classe Administrateur_bazar
*
* Revision 1.27  2006/02/07 11:08:36  alexandre_tb
* utilisation de la classe Utilisateur_bazar pour la v�rification des droits
*
* Revision 1.26  2006/02/06 09:33:53  alexandre_tb
* modification de l'affichage lors de la saisie de fiche
*
* Revision 1.25  2006/01/30 17:25:38  alexandre_tb
* correction de bugs
*
* Revision 1.24  2006/01/26 11:06:43  alexandre_tb
* ajout d'une requete pour recupere l'id_nature si un id_fiche est pass� dans l'url
*
* Revision 1.23  2006/01/17 10:07:36  alexandre_tb
* en cours
*
* Revision 1.22  2006/01/16 15:11:28  alexandre_tb
* simplification code
*
* Revision 1.21  2006/01/13 14:12:52  florian
* utilisation des temlates dans la table bazar_nature
*
* Revision 1.20  2006/01/05 16:28:25  alexandre_tb
* prise en chage des checkbox, reste la mise � jour � g�rer
*
* Revision 1.19  2006/01/03 10:19:31  florian
* Mise à jour pour accepter des parametres dans papyrus: faire apparaitre ou non le menu, afficher qu'un type de fiches, définir l'action par défaut...
*
* Revision 1.18  2005/12/02 10:57:03  florian
* MAJ pour paramétrage dans gestion de menus papyrus
*
* Revision 1.17  2005/12/01 16:05:41  florian
* changement des chemins pour appli Pear
*
* Revision 1.16  2005/12/01 15:31:30  florian
* correction bug modifs et saisies
*
* Revision 1.15  2005/11/30 13:58:45  florian
* ajouts graphisme (logos, boutons), changement structure SQL bazar_fiche
*
* Revision 1.14  2005/11/24 16:17:13  florian
* corrections bugs, ajout des cases à cocher
*
* Revision 1.13  2005/11/14 16:04:54  florian
* maj bug affichage flux
*
* Revision 1.12  2005/11/07 17:05:46  florian
* amélioration validation conditions de saisie, ajout des règles spécifiques de saisie des formulaires
*
* Revision 1.11  2005/10/21 16:15:04  florian
* mise a jour appropriation
*
* Revision 1.10  2005/10/12 17:20:33  ddelon
* Reorganisation calendrier + applette
*
* Revision 1.9  2005/10/12 13:35:07  florian
* amélioration de l'interface de bazar, de manière a simplifier les consultations, et à harmoniser par rapport aux Ressources
*
* Revision 1.8  2005/09/30 13:00:05  ddelon
* Fiche bazar generique
*
* Revision 1.7  2005/09/30 12:34:44  ddelon
* petite modification pour integration bazar dans papyrus
*
* Revision 1.6  2005/09/30 12:22:54  florian
* Ajouts commentaires pour fiche, modifications graphiques, maj SQL
*
* Revision 1.4  2005/07/21 19:03:12  florian
* nouveautés bazar: templates fiches, correction de bugs, ...
*
* Revision 1.2  2005/02/22 15:33:32  florian
* integration dans Papyrus
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
* Revision 1.3  2004/07/05 15:10:14  florian
* changement interface de saisie
*
* Revision 1.2  2004/07/01 10:13:41  florian
* modif Florian
*
* Revision 1.1  2004/06/23 09:58:32  alex
* version initiale
*
* Revision 1.1  2004/06/18 09:00:07  alex
* version initiale
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/
?>
