<?php
/**
* bazarcarto : programme affichant les fiches du bazar sous forme de Cartographie Google
*
*
*@package Bazar
//Auteur original :
*@author        Florian SCHMITT <florian.schmitt@laposte.net>
*@version       $Revision: 1.3 $ $Date: 2009/08/01 17:01:59 $
// +------------------------------------------------------------------------------------------------------+
*/


// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

//récupération des paramêtres wikini
$categorie_nature = $this->GetParameter("categorienature");
if (!empty($categorie_nature)) {
	$GLOBALS['_BAZAR_']['categorie_nature']=$categorie_nature;
}
//si rien n'est donne, on affiche la categorie 0
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

// requete sur le bazar pour recuperer les evenements
$requete = 'SELECT * FROM bazar_fiche WHERE ';
if ($GLOBALS['_BAZAR_']['categorie_nature'] != 'toutes') $requete .= 'bf_categorie_fiche="'.$GLOBALS['_BAZAR_']['categorie_nature'].'" and ' ;
if ($GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') $requete .= 'bf_ce_nature="'.$GLOBALS['_BAZAR_']['id_typeannonce'].'" and ' ;
$requete .= ' ((bf_date_debut_validite_fiche<=now() and bf_date_fin_validite_fiche>=now()) or (bf_date_fin_validite_fiche="0000-00-00"))'.
			' and bf_statut_fiche=1';
	//echo $requete;		
$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete);

// Le code complique avec ces 2 tableaux sert
// a ne pas mettre 2 points aux memes coordonnees
// car dans ce cas la seul le second serait visible

$donnees = array();

if ($resultat->numRows() != 0) {
	$script_marker = '';
	while ($chaine = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		//var_dump($chaine);
		if ($chaine['bf_latitude'] == 0 && $chaine['bf_longitude'] == 0) continue;
	//	$cle = $ligne['bf_latitude'].'-'.$ligne['bf_longitude'];
	//	$donnees[$cle][] = $ligne; 
	//}
	//foreach ($donnees as $valeur) {
		// cas un : une seule entree pour le point de coordonnees
		//if (count ($valeur) == 1) {
			//$chaine = $valeur[0];
			$script_marker .= "\t".'point = new GLatLng('.$chaine['bf_latitude'].','.$chaine['bf_longitude'].');'."\n"
				."\t".'map.addOverlay(createMarker(point, \''.'<div class="BAZ_cadre_map">'.
				preg_replace ('/\n/', '', str_replace ("\r\n", '', 
					str_replace ("'", "\'", baz_voir_fiche(0, $chaine['bf_id_fiche'])))).'</div>\'));'."\n";
		//} else { // Cas 2 plusieurs entrees
		//	$tableau_id = array();
		//	foreach ($valeur as $val) {
		//		array_push ($tableau_id, $val['bf_id_fiche']);
		//	}
		//	$script_marker .= "\t".'point = new GLatLng('.$val['bf_latitude'].','.$val['bf_longitude'].');'."\n"
		//		."\t".'map.addOverlay(createMarker(point, \''.'<div class="BAZ_cadre_map">'.
		//		preg_replace ('/\n/', '', str_replace ("\r\n", '', 
		//			str_replace ("'", "\'", baz_voir_fiches(0, $tableau_id)))).'</div>\'));'."\n";
		//}	
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
	
	  // On centre la carte
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

echo '<script type="text/javascript">'."\n".$script."\n".'</script>'."\n".'<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.
							'px; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.'px"></div>';
?>
