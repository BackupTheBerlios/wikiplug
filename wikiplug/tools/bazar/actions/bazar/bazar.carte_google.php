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
// CVS : $Id: bazar.carte_google.php,v 1.1 2008/07/07 18:00:40 mrflos Exp $
/**
* 
*@package bazar
//Auteur original :
*@author        Alexandre GRANIER <alexandre@tela-botanica.org>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2007
*@version       $Revision: 1.1 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+


include_once 'configuration/baz_config.inc.php';
include_once BAZ_CHEMIN_APPLI.'bibliotheque/bazar.fonct.php';
// Inclusion d'une classe personnalise si elle existe

GEN_stockerFichierScript('googleMapScript', 'http://maps.google.com/maps?file=api&amp;v=2&amp;key='.BAZ_GOOGLE_KEY);

if (defined('PAP_VERSION')) { //si on est dans Papyrus
	GEN_stockerStyleExterne( 'bazar_interne', 'client/bazar/bazar.interne.css');
}
$GLOBALS['_BAZAR_']['id_typeannonce']=$GLOBALS['_GEN_commun']['info_application']->id_nature;
$GLOBALS['_BAZAR_']['categorie_nature']=$GLOBALS['_GEN_commun']['info_application']->categorie_nature;
// requete sur le bazar pour recuperer les evenements

$requete = 'select * from bazar_fiche where ';
if ($GLOBALS['_BAZAR_']['id_typeannonce'] != '') $requete .= 'bf_ce_nature in ('.$GLOBALS['_BAZAR_']['id_typeannonce'].') and ' ;
$requete .= ' ((bf_date_debut_validite_fiche<=now() and bf_date_fin_validite_fiche>=now()) or (bf_date_fin_validite_fiche="0000-00-00"' .
		' and date_add(bf_date_fin_evenement,interval 15 day)>now()))'.
			' and bf_statut_fiche=1';
$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete);

if (DB::isError($resultat)) {
	return BOG_afficherErreurSql(__FILE__, __LINE__, $resultat->getMessage().'<br />'.$resultat->getDebugInfo(), $requete);echo $requete;
}

// Le code complique avec ces 2 tableaux sert
// a ne pas mettre 2 points aux memes coordonnees
// car dans ce cas la seul le second serait visible

$donnees = array();

if ($resultat->numRows() != 0) {
	$script_marker = '';
	while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		if ($ligne['bf_latitude'] == 0 && $ligne['bf_longitude'] == 0) continue;
		$cle = $ligne['bf_latitude'].'-'.$ligne['bf_longitude'];
		$donnees[$cle][] = $ligne; 
	}
	foreach ($donnees as $valeur) {
		// cas un : une seule entree pour le point de coordonnees
		if (count ($valeur) == 1) {
			$chaine = $valeur[0];
			$script_marker .= "\t".'point = new GLatLng('.$chaine['bf_latitude'].','.$chaine['bf_longitude'].');'."\n"
				."\t".'map.addOverlay(createMarker(point, \''.'<div class="BAZ_cadre_map">'.
				preg_replace ('/\n/', '', str_replace ("\r\n", '', 
					str_replace ("'", "\'", baz_voir_fiche(0, $chaine['bf_id_fiche'])))).'</div>\'));'."\n";
		} else { // Cas 2 plusieurs entrees
			$tableau_id = array();
			foreach ($valeur as $val) {
				array_push ($tableau_id, $val['bf_id_fiche']);
			}
			$script_marker .= "\t".'point = new GLatLng('.$val['bf_latitude'].','.$val['bf_longitude'].');'."\n"
				."\t".'map.addOverlay(createMarker(point, \''.'<div class="BAZ_cadre_map">'.
				preg_replace ('/\n/', '', str_replace ("\r\n", '', 
					str_replace ("'", "\'", baz_voir_fiches(0, $tableau_id)))).'</div>\'));'."\n";
		}	
	}
} else {
	$script_marker = '';
}

$script = '
    // Variables globales
    var map = null;
	var lat = document.getElementById("latitude");
    var lon = document.getElementById("longitude");';
if (BAZ_GOOGLE_MAXIMISE_TAILLE) $script .= '
    // Pour gerer la taille  
    var winW = 630, winH = 560;
    var deltaH = 220;
    var deltaW = 270;

    function setWinHW() {
	if (window.innerHeight) {
	    winW = window.innerWidth  - deltaW;
	    winH = window.innerHeight - deltaH;
        } else {
	    winW = document.documentElement.offsetWidth  - 20 - deltaW;
	    winH = document.documentElement.offsetHeight - 20 - deltaH ; 
        }

	var me = document.getElementById("map");
	if (me != null) {
	    me.style.width= \'\' + winW + \'px\';
	    me.style.height= \'\' + winH + \'px\';
        }
    }

    window.onresize = function () {
	setWinHW();
	if (map)  map.checkResize();
    }';
$script .= '
    
    function createMarker(point, chaine) {
	  	var icon = new GIcon();
		icon.image = "http://connaisciences.fr/sites/connaisc/fr/images/marker.png";
		icon.shadow = "http://www.google.com/mapfiles/shadow50.png";
		icon.iconSize = new GSize(20, 34);
		icon.shadowSize = new GSize(37, 34);
		icon.iconAnchor = new GPoint(6, 34);
		icon.infoWindowAnchor = new GPoint(5, 1);
	  	var marker = new GMarker(point, icon);
	  	GEvent.addListener(marker, "click", function() {
	    	marker.openInfoWindowHtml(chaine);
	  	});
	  	return marker;
	}
    function load() {';
if (BAZ_GOOGLE_MAXIMISE_TAILLE) $script .= '
    setWinHW();';
$script .= '
    if (GBrowserIsCompatible()) {
      map = new GMap2(document.getElementById("map"));
      map.addControl(new GSmallMapControl());
	  map.addControl(new GMapTypeControl());
	  map.addControl(new GScaleControl());
	  map.enableContinuousZoom();
	
	  // On centre la carte sur le languedoc roussillon
	  center = new GLatLng('.BAZ_GOOGLE_CENTRE_LAT.', '.BAZ_GOOGLE_CENTRE_LON.');
      map.setCenter(center, '.BAZ_GOOGLE_ALTITUDE.');
	  map.setMapType(G_HYBRID_MAP);' ;
	  if (BAZ_GOOGLE_FOND_KML != '') {
	  	$script .= 'var geoXml = new GGeoXml("'.BAZ_GOOGLE_FOND_KML.'");';
	  }
      
      $script .= $script_marker;
	  if (BAZ_GOOGLE_FOND_KML != '') {
	  	$script .= 'map.addOverlay(geoXml);';
	  }
	  $script .= '
   }
	};
	// Creates a marker at the given point with the given number label
	
';
GEN_stockerCodeScript($script);
// On ajoute l attribut load a la balise body
GEN_AttributsBody('onload', 'load()');

function afficherContenuCorps() {
	
	include_once BAZ_CHEMIN_APPLI.'bibliotheque/bazarTemplate.class.php';
    $modele = new bazarTemplate($GLOBALS['_BAZAR_']['db']);
    $html = $modele->getTemplate(BAZ_TEMPLATE_ACCUEIL_CARTE_GOOGLE, $GLOBALS['_BAZAR_']['langue'], $GLOBALS['_BAZAR_']['categorie_nature']);
    if (PEAR::isError($html)) return $html->getMessage();
	$res = str_replace ('{CARTE}', '<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.
							'px; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.'px"></div>', $html);

	return $res;
}


/* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: bazar.carte_google.php,v $
* Revision 1.1  2008/07/07 18:00:40  mrflos
* maj carto plus calendrier
*
* Revision 1.1  2008/02/18 09:12:46  mrflos
* Premiere release de 3 extensions en version alpha (bugs nombreux!) des plugins bazar, e2gallery, et templates
*
* Revision 1.5.2.3  2008-01-29 09:41:11  alexandre_tb
* utilisation des constantes BAZ_GOOGLE_FOND_KML
*
* Revision 1.5.2.2  2007-12-14 09:57:15  alexandre_tb
* utilisation des constantes de la carte google
*
* Revision 1.5.2.1  2007-12-04 16:19:32  jp_milcent
* Ajout de la prise en charge de l'applette body_attributs
*
* Revision 1.5  2007-10-01 12:07:03  alexandre_tb
* utilisation de constantes du fichier de conf pour centrer la carte
*
* Revision 1.4  2007-08-27 12:27:34  alexandre_tb
* mise en place d un icone personnalise
* et de l affichage de plusieurs donnees sur un meme point
*
* Revision 1.3  2007-07-04 10:08:41  alexandre_tb
* Appel du template carte_google
*
* Revision 1.2  2007-06-13 10:02:47  alexandre_tb
* le carte s adapte a la taille du conteneur
*
* Revision 1.1  2007-06-04 15:26:57  alexandre_tb
* version initiale
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/