<?php
/**
* bazarcarto : programme affichant les fiches du bazar sous forme de Cartographie Google
*
*
*@package Bazar
//Auteur original :
*@author        Florian SCHMITT <florian.schmitt@laposte.net>
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
// +------------------------------------------------------------------------------------------------------+
*/


// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

define ('GEN_CHEMIN_API', str_replace('wakka.php', '', $_SERVER["SCRIPT_FILENAME"]).'tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR);
define ('PAP_CHEMIN_API_PEAR', GEN_CHEMIN_API);
define ('PAP_CHEMIN_RACINE', '');
define ('GEN_SEP', DIRECTORY_SEPARATOR);
define ('PAP_CHEMIN_API_PEARDB', PAP_CHEMIN_API_PEAR);
set_include_path(PAP_CHEMIN_API_PEAR.PATH_SEPARATOR.get_include_path());
require_once 'DB.php' ;
include_once 'Net'.DIRECTORY_SEPARATOR.'URL.php' ;
include_once 'bazar/configuration/baz_config.inc.php'; //fichier de configuration de Bazar
include_once 'bazar/bibliotheque/bazar.fonct.php'; //fichier des fonctions de Bazar
include_once 'bazar/bibliotheque/bazar.fonct.cal.php'; //fichier des fonctions de Bazar

//TODO: transformer en parametres wikinis
$GLOBALS['_BAZAR_']['id_typeannonce']=$GLOBALS['_GEN_commun']['info_application']->id_nature;
$GLOBALS['_BAZAR_']['categorie_nature']=$GLOBALS['_GEN_commun']['info_application']->categorie_nature;

// requete sur le bazar pour recuperer les evenements

$requete = 'SELECT * FROM bazar_fiche WHERE ';
if ($GLOBALS['_BAZAR_']['id_typeannonce'] != '') $requete .= 'bf_ce_nature in ('.$GLOBALS['_BAZAR_']['id_typeannonce'].') and ' ;
$requete .= ' ((bf_date_debut_validite_fiche<=now() and bf_date_fin_validite_fiche>=now()) or (bf_date_fin_validite_fiche="0000-00-00"' .
		' and date_add(bf_date_fin_evenement,interval 15 day)>now()))'.
			' and bf_statut_fiche=1';
$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete);

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
//GEN_stockerCodeScript($script);
// On ajoute l attribut load a la balise body
//GEN_AttributsBody('onload', 'load()');

echo '<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.
							'px; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.'px"></div>';
?>
