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
// CVS : $Id: wiki.php,v 1.1 2008/07/07 18:00:48 mrflos Exp $
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
*@copyright     outils-reseaux-coop.org 2008
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:48 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+
define ('BAZ_CHEMIN', 'tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR);

//bouh! c'est pas propre! c'est a cause de PEAR et de ses includes
set_include_path(BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.PATH_SEPARATOR.get_include_path());
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'DB.php' ;
require_once BAZ_CHEMIN.'libs'.DIRECTORY_SEPARATOR.'Net'.DIRECTORY_SEPARATOR.'URL.php' ;
require_once BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.
		'configuration'.DIRECTORY_SEPARATOR.'baz_config.inc.php'; //fichier de configuration de Bazar
require_once BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.
		'bibliotheque'.DIRECTORY_SEPARATOR.'bazar.fonct.php'; //fichier des fonctions de Bazar

// +------------------------------------------------------------------------------------------------------+
// |                                            CORPS du PROGRAMME                                        |
// +------------------------------------------------------------------------------------------------------+
// Variable d'url
$GLOBALS['_BAZAR_']['url'] = new Net_URL($wakkaConfig['base_url'].$_GET['wiki']);

// Connection a la base de donnee
$dsn='mysql://'.$wakkaConfig['mysql_user'].':'.$wakkaConfig['mysql_password'].'@'.$wakkaConfig['mysql_host'].'/'.$wakkaConfig['mysql_database'];
$GLOBALS['_BAZAR_']['db'] =& DB::connect($dsn) ;
if (DB::isError($GLOBALS['_BAZAR_']['db'])) {
	echo $GLOBALS['_BAZAR_']['db']->getMessage();
}

// +------------------------------------------------------------------------------------------------------+
// |                             LES CONSTANTES DES ACTIONS DE BAZAR                                      |
// +------------------------------------------------------------------------------------------------------+
define ('BAZ_VOIR_TOUTES_ANNONCES', 'recherche') ;
define ('BAZ_ACTION_VOIR_VOS_ANNONCES', 'vos_fiches');
define ('BAZ_DEPOSER_ANNONCE', 'choisir_type_fiche') ;
define ('BAZ_ANNONCES_A_VALIDER', 'voir_validation') ;
define ('BAZ_GERER_DROITS', 'droits') ;
define ('BAZ_ADMINISTRER_ANNONCES', 'voir_admin_fiches') ;
define ('BAZ_MODIFIER_FICHE', 'modif_fiches') ;
if (!defined('BAZ_VOIR_FICHE')) define ('BAZ_VOIR_FICHE', 'voir_fiche') ;
define ('BAZ_SUPPRIMER_FICHE', 'supprimer_fiche') ;
define ('BAZ_ACTION_NOUVEAU', 'saisir_fiche') ;
define ('BAZ_ACTION_NOUVEAU_V', 'sauver_fiche') ;
define ('BAZ_ACTION_MODIFIER', 'modif_fiche') ;
define ('BAZ_ACTION_MODIFIER_V', 'modif_sauver_fiche') ;
define ('BAZ_ACTION_SUPPRESSION', 'supprimer') ;
define ('BAZ_ACTION_PUBLIER', 'publier') ;
define ('BAZ_ACTION_PAS_PUBLIER', 'pas_publier') ;
define ('BAZ_S_INSCRIRE', 'rss');
define ('BAZ_VOIR_FLUX_RSS', 'affiche_rss');

// Constante des noms des variables
define ('BAZ_VARIABLE_VOIR', 'vue');
define ('BAZ_VARIABLE_ACTION', 'action');
/** Indique les onglets de vues à afficher.*/
define ('BAZ_VOIR_AFFICHER', 'consulter,rss,saisir,formulaire,administrer,droits');// Indiquer les numéros des vues à afficher séparées par des virgules.
/** Permet d'indiquer la vue par défaut si la variable vue n'est pas défini dans l'url ou dans les paramêtre du menu Papyrus.*/
define ('BAZ_VOIR_DEFAUT', 'consulter');
define ('BAZ_VOIR_CONSULTER', 'consulter');
define ('BAZ_VOIR_MES_FICHES', 'mes_fiches');
define ('BAZ_VOIR_S_ABONNER', 'rss');
define ('BAZ_VOIR_SAISIR', 'saisir');
define ('BAZ_VOIR_FORMULAIRE', 'formulaire');
define ('BAZ_VOIR_ADMIN', 'administrer');
define ('BAZ_VOIR_GESTION_DROITS', 'droits');

// Constante pour se passer d'identification
define ('BAZ_SANS_AUTH', true);


//==================================== LES FLUX RSS==================================
// Constantes liées aux flux RSS
//==================================================================================
define('BAZ_RSS_NOMSITE','Reseau Logement Jeunes');    //Nom du site indiqué dans les flux rss
define('BAZ_RSS_ADRESSESITE','http://kaleidos-coop.org/~reseaulo');   //Adresse Internet du site indiquÃƒÂ© dans les flux rss
define('BAZ_RSS_DESCRIPTIONSITE','Reseau Logement Jeunes');    //Description du site indiquée dans les flux rss
define('BAZ_RSS_LOGOSITE','http://www.umij.org/images/illustrations/services/rlj_passeport.jpg');     //Logo du site indiqué dans les flux rss
define('BAZ_RSS_MANAGINGEDITOR', 'labriet.pierre@nerim.net (Pierre Labriet)') ;     //Managing editor du site
define('BAZ_RSS_WEBMASTER', 'labriet.pierre@nerim.net (Pierre Labriet)') ;     //Mail Webmaster du site
define('BAZ_RSS_CATEGORIE', 'Immobilier'); //catégorie du flux RSS


//==================================== PARAMETRAGE =================================
// Pour régler certaines fonctionnalité de l'application
//==================================================================================

define ('BAZ_ETAT_VALIDATION', 1);
//Valeur par défaut d'état de la fiche annonce après saisie
//Mettre 0 pour 'en attente de validation d'un administrateur'
//Mettre 1 pour 'directement validée en ligne'

define ('BAZ_TAILLE_MAX_FICHIER', 2000*1024);
//Valeur maximale en octets pour la taille d'un fichier joint à télécharger

define ('BAZ_TYPE_AFFICHAGE_LISTE', 'jma');

/** Réglage des droits pour déposer des annonces */
// Mettre à true pour limiter le dépot aux rédacteurs
define ('BAZ_RESTREINDRE_DEPOT', false) ;

/** Réglage de l'affichage de la liste deroulante pour la saisie des dates */
// Mettre à true pour afficher une liste deroulante vide pour la saisie des dates
define ('BAZ_DATE_VIDE', false);

// Mettre à true pour faire apparaitre un champs texte déroulant dans le formulaire
// de recherche des fiches, pour choisir les émetteurs
define ('BAZ_RECHERCHE_PAR_EMETTEUR', false) ;

/**Choix de l'affichage (true) ou pas (false) de l'email du rédacteur dans la fiche.*/
define('BAZ_FICHE_REDACTEUR_MAIL', true);// true ou false

//==================================== LES LANGUES ==================================
// Constantes liées à l'utilisation des langues
//==================================================================================
$GLOBALS['_BAZAR_']['langue'] = 'fr-FR';
define ('BAZ_LANGUE_PAR_DEFAUT', 'fr') ; //Indique un code langue par défaut
define ('BAZ_VAR_URL_LANGUE', 'lang') ; //Nom de la variable GET qui sera passée dans l'URL (Laisser vide pour les sites monolingues)
//code pour l'inclusion des langues NE PAS MODIFIER
if (BAZ_VAR_URL_LANGUE != '' && isset (${BAZ_VAR_URL_LANGUE})) {
    include_once BAZ_CHEMIN.'actions/bazar/langues/baz_langue_'.${BAZ_VAR_URL_LANGUE}.'.inc.php';
} else {
    include_once BAZ_CHEMIN.'actions/bazar/langues/baz_langue_'.BAZ_LANGUE_PAR_DEFAUT.'.inc.php';
}

// Option concernant la division des resultats en pages
define ('BAZ_NOMBRE_RES_PAR_PAGE', 30);
define ('BAZ_MODE_DIVISION', 'Jumping'); 	// 'Jumping' ou 'Sliding' voir http://pear.php.net/manual/fr/package.html.pager.compare.php
define ('BAZ_DELTA', 12);		// Le nombre de page à afficher avant le 'next';

/** Réglage de l'affichage du formulaire de recherche avancee */
// Mettre à true pour afficher automatiquement le formulaire de recherche avancee, à false pour avoir un lien afficher la recherche avancee
define ('BAZ_MOTEUR_RECHERCHE_AVANCEE', true);

/** Réglage de l'utilisation ou non des templates */
// Mettre à true pour afficher les pages incluses dans la base bazar_template, à false sinon
define ('BAZ_UTILISE_TEMPLATE', false);

/** Mettre a 0 pour le pas proposer de filtre dans le moteur de recherche */
define ('BAZ_AFFICHER_FILTRE_MOTEUR', true);

// Mettre ici le type d'annonce qui va s'afficher dans les calendriers.
// Il est possible d'indiquer plusieurs identifiant de nature de fiche  (bn_id_nature) en séparant les nombre par des
// virgules : '1,2,3'
define ('BAZ_NUM_ANNONCE_CALENDRIER', 3);
define ('BAZ_SQUELETTE_DEFAUT', 'baz_cal.tpl.html');

define ('BAZ_GOOGLE_KEY', 'ABQIAAAAaNByewMifv3sp7csMhxt3xQ5Hpti9uskfJvbbDjOZ3hbd-4AbRRyujIHm2xkIXT1czSNsPmxKNVQEQ'); // Indiquer ici la cle google necessaire pour l appli bazar.carte_google.php
define ('BAZ_GOOGLE_CENTRE_LAT', '45.18');
define ('BAZ_GOOGLE_CENTRE_LON', '5.77');
define ('BAZ_GOOGLE_ALTITUDE', '11'); // de 1 a 15
define ('BAZ_GOOGLE_IMAGE_LARGEUR', 400);  // en pixel
define ('BAZ_GOOGLE_IMAGE_HAUTEUR', 400);
define ('BAZ_GOOGLE_MAXIMISE_TAILLE', false); // Si a true, la carte essaie de s etendre sur toute la largeur disponible
define ('BAZ_GOOGLE_FOND_KML', '');

/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: wiki.php,v $
* Revision 1.1  2008/07/07 18:00:48  mrflos
* maj carto plus calendrier
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
