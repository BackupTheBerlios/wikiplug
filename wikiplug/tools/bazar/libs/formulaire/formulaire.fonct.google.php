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
// CVS : $Id: formulaire.fonct.google.php,v 1.2 2008/07/29 17:32:25 mrflos Exp $
/**
* Formulaire
*
* Les fonctions et script specifique a un carto google
*
*@package api/formulaire
//Auteur original :
*@author        Aleandre GRANIER <alexandre@tela-botanica.org>
*@copyright     Tela-Botanica 2000-2007
*@version       $Revision: 1.2 $
// +------------------------------------------------------------------------------------------------------+
*/

$script = '
    // Variables globales
    var map = null;
	var geocoder = null;
	var lat = document.getElementById("latitude");
    var lon = document.getElementById("longitude");
    
    function load() {
    if (GBrowserIsCompatible()) {
	  
      map = new GMap2(document.getElementById("map"), G_HYBRID_MAP);
      
      map.addControl(new GSmallMapControl());
	  map.addControl(new GMapTypeControl());
	  map.addControl(new GScaleControl());
	  map.enableContinuousZoom();
	
	  // On centre la carte sur le languedoc roussillon
	  center = new GLatLng(43.84245116699036, 3.768310546875);
      map.setCenter(center, 7);map.setMapType(G_HYBRID_MAP);
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
    });' ;
	if ($formtemplate->getElementValue ('latitude') != '' && $formtemplate->getElementValue('longitude') != '') {
		$script .= '
				point = new GLatLng('.$formtemplate->getElementValue('latitude').', '.$formtemplate->getElementValue('longitude').');
				marker = new GMarker(point, {draggable: true});
				map.addOverlay(marker);' ;
	} 
    $script .= 'geocoder = new GClientGeocoder();
  }
};
function showAddress() {
  var adresse = document.getElementById("bf_adresse").value ;
  if (document.getElementById("bf_ville")) {
  	var ville = document.getElementById("bf_ville").value ;
  } else {
  	var ville = "";
  }
  var cp = document.getElementById("bf_cp_lieu_evenement").value ;
  if (document.getElementById("liste30")) {
  	var selectIndex = document.getElementById("liste30").selectedIndex;
  	var pays = document.getElementById("liste30").options[selectIndex].text ;
  } else {
  	var pays = document.getElementById("bf_pays").value;
  }
  var address = adresse + \' \' + \' \' + cp + \' \' + ville + \' \' +pays ;
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
          marker.openInfoWindowHtml(address+ "'.BAZ_GOOGLE_MSG.'");
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
';

/*
* +--Fin du code ----------------------------------------------------------------------------------------+
*
* $Log: formulaire.fonct.google.php,v $
* Revision 1.2  2008/07/29 17:32:25  mrflos
* maj générale
*
* Revision 1.3  2007-08-27 12:20:06  alexandre_tb
* ajout d un  test sur la ville
*
* Revision 1.2  2007-07-04 11:52:55  alexandre_tb
* fonctionne mais en cours de développement
*
* Revision 1.1  2007-06-25 09:52:36  alexandre_tb
* version intiale -- en cours
*
*
* +-- Fin du code ----------------------------------------------------------------------------------------+
*/