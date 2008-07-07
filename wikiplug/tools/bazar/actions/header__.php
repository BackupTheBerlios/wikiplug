<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

//ajout des styles css pour bazar, le calendrier, la google map
$style = '<link rel="stylesheet" type="text/css" href="tools/bazar/presentation/bazar.css" media="screen" />
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.BAZ_GOOGLE_KEY.'" ></script>
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

</script>
<script type="text/javascript"';

if ($this->GetMethod() == "show") {
	$plugin_output_new=preg_replace ('/<script type="text\/javascript"/', $style,	$plugin_output_new, 1);
	$plugin_output_new=preg_replace ('/<body /', '<body onload="load()" ',	$plugin_output_new, 1);
}
