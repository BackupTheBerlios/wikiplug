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
// CVS : $Id: bazar.php,v 1.11 2010/10/26 14:18:41 ddelon Exp $
/**
* bazar.php
*
* Description :
*
*@package wkbazar
//Auteur original :
*@author        Florian SCHMITT <florian@outils-reseaux.org>
*@copyright     Florian SCHMITT 2008
*@version       $Revision: 1.11 $ $Date: 2010/10/26 14:18:41 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+


if (!defined("WIKINI_VERSION")) {
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

//ordre d'affichage des fiches : chronologique ou alphab�tique
$GLOBALS['_BAZAR_']['tri'] = $this->GetParameter('tri');
if (empty($GLOBALS['_BAZAR_']['tri'])) {
	$GLOBALS['_BAZAR_']['tri']='chronologique';
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
	$requete = 'SELECT bf_ce_nature FROM '.BAZ_PREFIXE.'fiche, '.BAZ_PREFIXE.'nature WHERE bf_id_fiche="'.$GLOBALS['_BAZAR_']['id_fiche'].'" AND bf_ce_nature = bn_id_nature' ;
	$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete) ;
	if (DB::isError($resultat)) {
		echo $resultat->getMessage().'<br />'.$resultat->getInfoDebug();
	}
	if ($resultat->numRows()>0)
	{
		$ligne = $resultat->fetchRow(DB_FETCHMODE_OBJECT) ;
		$GLOBALS['_BAZAR_']['id_typeannonce'] = $ligne->bf_ce_nature ;		
	}
	else
	{
		$GLOBALS['_BAZAR_']['id_fiche']=NULL;
		exit('<div class="BAZ_error">la fiche que vous recherchez n\'existe plus (sans doute a t\'elle &eacute;t&eacute; supprim&eacute;e entre temps)...</div>');
	}
	$resultat->free();
} else {
	$GLOBALS['_BAZAR_']['id_fiche']=$this->GetParameter("numfiche");
}

if (isset($_REQUEST['id_typeannonce'])) $GLOBALS['_BAZAR_']['id_typeannonce']=$_REQUEST['id_typeannonce'];
if ($GLOBALS['_BAZAR_']['id_typeannonce']!='toutes') {
	$tab_nature = baz_valeurs_type_de_fiche($GLOBALS['_BAZAR_']['id_typeannonce']);
	$GLOBALS['_BAZAR_']['typeannonce']=$tab_nature['bn_label_nature'];
	$GLOBALS['_BAZAR_']['condition']=$tab_nature['bn_condition'];
	$GLOBALS['_BAZAR_']['template']=$tab_nature['bn_template'];
	$GLOBALS['_BAZAR_']['commentaire']=$tab_nature['bn_commentaire'];
	$GLOBALS['_BAZAR_']['appropriation']=$tab_nature['bn_appropriation'];
	$GLOBALS['_BAZAR_']['class']=$tab_nature['bn_label_class'];
}

//variable d'affichage du bazar
$res = '';
// +------------------------------------------------------------------------------------------------------+
// |                                            CORPS du PROGRAMME                                        |
// +------------------------------------------------------------------------------------------------------+

if ($GLOBALS['_BAZAR_']['affiche_menu']!='0') {
	$res .= afficher_menu();
}

if (isset($_GET['message'])) {
	$res .= '<div class="BAZ_info">';
	if ($_GET['message']=='ajout_ok') $res.= BAZ_FICHE_ENREGISTREE;
	if ($_GET['message']=='modif_ok') $res.= BAZ_FICHE_MODIFIEE;
	if ($_GET['message']=='delete_ok') $res.= BAZ_FICHE_SUPPRIMEE;
	$res .= '</div>'."\n";
}

if (isset ($_GET[BAZ_VARIABLE_VOIR])) {
		switch ($_GET[BAZ_VARIABLE_VOIR]) {
			case BAZ_VOIR_CONSULTER:
				if (isset ($_GET[BAZ_VARIABLE_ACTION])) {
					switch ($_GET[BAZ_VARIABLE_ACTION]) {
						case BAZ_MOTEUR_RECHERCHE : $res .= baz_rechercher($GLOBALS['_BAZAR_']['id_typeannonce'],$GLOBALS['_BAZAR_']['categorie_nature']); break;
						case BAZ_VOIR_FICHE : $res .= baz_voir_fiche(1, $GLOBALS['_BAZAR_']['id_fiche']); break;
					}
				}
				else
				{
					$res .= baz_rechercher($GLOBALS['_BAZAR_']['id_typeannonce'],$GLOBALS['_BAZAR_']['categorie_nature']);
				}
				break;
			case BAZ_VOIR_MES_FICHES :
				$res .= mes_fiches();
				break;
			case BAZ_VOIR_S_ABONNER :
				if (isset ($_GET[BAZ_VARIABLE_ACTION]))
				{
					switch ($_GET[BAZ_VARIABLE_ACTION])
					{
						case BAZ_LISTE_RSS : $res .= baz_liste_rss(); break;
						case BAZ_VOIR_FLUX_RSS : exit(afficher_flux_rss());break;
					}
				}
				else
				{
					$res .= baz_liste_rss();
				}
				break;
			case BAZ_VOIR_SAISIR :
				if (isset ($_GET[BAZ_VARIABLE_ACTION]))
				{
					switch ($_GET[BAZ_VARIABLE_ACTION])
					{						
						case BAZ_ACTION_SUPPRESSION : $res .= baz_suppression($_GET['id_fiche']); break;
						case BAZ_ACTION_PUBLIER : $res .= publier_fiche(1).baz_voir_fiche(1, $GLOBALS['_BAZAR_']['id_fiche']); break;
						case BAZ_ACTION_PAS_PUBLIER : $res .= publier_fiche(0).baz_voir_fiche(1, $GLOBALS['_BAZAR_']['id_fiche']); break;
						default : $res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ;break;
					}
				}
				else
				{
					$_GET[BAZ_VARIABLE_ACTION] = BAZ_CHOISIR_TYPE_FICHE;
					$res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]);
				}
				break;
			case BAZ_VOIR_FORMULAIRE :
				$res .= baz_gestion_formulaire();
				break;
			case BAZ_VOIR_LISTES :
				$res .= baz_gestion_listes();
				break;
			case BAZ_VOIR_ADMIN:
				if (isset($_GET[BAZ_VARIABLE_ACTION]))
				{
					$res .= baz_formulaire($_GET[BAZ_VARIABLE_ACTION]) ;
				}
				else
				{
					$res .= fiches_a_valider();
				}
				break;
			case BAZ_VOIR_GESTION_DROITS:
				$res .= baz_gestion_droits();
				break;
			default :
				$res .= baz_rechercher($GLOBALS['_BAZAR_']['id_typeannonce']);
		}
}
//affichage de la page
echo $res ;

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.php,v $
* Revision 1.11  2010/10/26 14:18:41  ddelon
* Remaniement des constantes
*
* Revision 1.10  2010-10-26 10:42:11  mrflos
* snapshot avant coding party
*
* Revision 1.9  2010-06-02 08:48:51  mrflos
* commit de transition
*
* Revision 1.8  2010/05/03 15:59:45  mrflos
* Un bazar par préfixe de table
*
* Revision 1.7  2010/05/03 08:36:15  mrflos
* maj générale des fonctions de bazar
*
* Revision 1.6  2010/03/04 14:19:03  mrflos
* nouvelle version bazar
*
* Revision 1.5  2009/09/09 15:36:37  mrflos
* maj css
* ajout de la google api v3
* possibilité d'insérer des utilisateurs wikini par bazar
* installation automatique du fichier sql avec type d'annonces par défaut
*
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
