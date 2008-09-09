<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

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
$lien_RSS=$GLOBALS['_BAZAR_']['url'];
$lien_RSS->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FLUX_RSS);
while ($ligne = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
	$lien_RSS->addQueryString('annonce', $ligne[bn_id_nature]);
	$liste .= '<link rel="alternate" type="application/rss+xml" title="'.$ligne['bn_label_nature'].'" href="'.$lien_RSS->getURL().'"  />'."\n";
	$lien_RSS->removeQueryString('annonce');
}
$liste = '<link rel="alternate" type="application/rss+xml" title="'.BAZ_FLUX_RSS_GENERAL.'" href="'.$lien_RSS->getURL().'" />'."\n".$liste."\n";


//ajout des styles css pour bazar, le calendrier, la google map
$style = '<link rel="stylesheet" type="text/css" href="tools/bazar/presentation/bazar.css" media="screen" />'."\n";

//on cherche l'action bazar ou l'action bazarcarto dans la page, pour voir s'il faut charger googlemaps
if ($_POST["submit"] == html_entity_decode('Aper&ccedil;u')) {
	$contenu["body"] = $_POST["body"];
} else $contenu=$this->LoadPage($this->tag);
//si l'on trouve des actions bazar
if ($act=preg_match_all ("/".'(\\{\\{bazar)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches)) {
	$style .= '<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.BAZ_GOOGLE_KEY.'" ></script>
<script type="text/javascript">
    // Variables globales
    var map = null;
	var geocoder = null;
	var lat = document.getElementById("latitude");
    var lon = document.getElementById("longitude");

    function load() {
    if (GBrowserIsCompatible()) {
      map = new GMap2(document.getElementById("map"));
      map.addControl(new GSmallMapControl());
	  map.addControl(new GMapTypeControl());
	  map.addControl(new GScaleControl());
	  map.enableContinuousZoom();
	  map.enableScrollWheelZoom();	

	  // On centre la carte
	  center = new GLatLng('.BAZ_GOOGLE_CENTRE_LAT.', '.BAZ_GOOGLE_CENTRE_LON.');
      map.setCenter(center, '.BAZ_GOOGLE_ALTITUDE.');
	  //marker = new GMarker(center, {draggable: true}) ;
      GEvent.addListener(map, "click", function(marker, point) {
	    if (marker) {
	      map.removeOverlay(marker);
	      var lat = document.getElementById("latitude");
          var lon = document.getElementById("longitude");
	      lat.value = "";
          lon.value = "";
	    } else {
	      // On ajoute un marqueur a l endroit du clic et on place les coordonnees dans les champs latitude et longitude
	      marker = new GMarker(point, {draggable: true}) ;
	      GEvent.addListener(marker, "dragend", function () {
            coordMarker = marker.getPoint() ;
            var lat = document.getElementById("latitude");
            var lon = document.getElementById("longitude");
            lat.value = coordMarker.lat();
            lon.value = coordMarker.lng();
          });
          map.addOverlay(marker);
          setLatLonForm(marker);
	    }
    });geocoder = new GClientGeocoder();
};}

function showAddress() {
  var adress_1 = document.getElementById("bf_adresse1").value ;
  if (document.getElementById("bf_adresse2")) 	var adress_2 = document.getElementById("bf_adresse2").value ; else var adress_2 = "";
  var ville = document.getElementById("bf_ville").value ;
  var cp = document.getElementById("bf_code_postal").value ;
  if (document.getElementById("bf_ce_pays").type == "select-one") {
  	var selectIndex = document.getElementById("bf_ce_pays").selectedIndex;
  	var pays = document.getElementById("bf_ce_pays").options[selectIndex].text ;
  } else {
  	var pays = document.getElementById("bf_ce_pays").value;
  }
  
  var address = adress_1 + \' \' + adress_2 + \' \' + \' \' + cp + \' \' + ville + \' \' +pays ;
  if (geocoder) {
    geocoder.getLatLng(
      address,
      function(point) {
        if (!point) {
          alert(address + " not found");
        } else {
          map.setCenter(point, 13);
          var marker = new GMarker(point, {draggable: true});
          GEvent.addListener(marker, "dragend", function () {
  coordMarker = marker.getPoint() ;
  var lat = document.getElementById("latitude");
  var lon = document.getElementById("longitude");
  lat.value = coordMarker.lat();
  lon.value = coordMarker.lng();
});

          map.addOverlay(marker);
          setLatLonForm(marker)
          marker.openInfoWindowHtml(address+ "<br />Si le point correspond &agrave; votre adresse,<br /> vous pouvez valider le formulaire en cliquant sur &laquo; valider &raquo; ci dessous.<br />Vous pouvez ajuster le marqueur pour le faire correspondre &agrave; votre adresse.");
        }
      }
    );
  }
}
function setLatLonForm(marker) {
  coordMarker = marker.getPoint() ;
  var lat = document.getElementById("latitude");
  var lon = document.getElementById("longitude");
  lat.value = coordMarker.lat();
  lon.value = coordMarker.lng();
}

</script>';
if ($this->GetMethod() == "show") {$plugin_output_new=preg_replace ('/<body /', '<body onload="load()" ',	$plugin_output_new, 1);}
}
$style .= '<script type="text/javascript"';

if ($this->GetMethod() == "show") {
	$plugin_output_new=preg_replace ('/<script type="text\/javascript"/', $liste.$style,	$plugin_output_new, 1);
}