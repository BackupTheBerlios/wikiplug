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
	  center = new GLatLng(43.60426186809618, 3.438720703125);
      map.setCenter(center, 8);
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
  
  var address = adress_1 + ' ' + adress_2 + ' ' + ' ' + cp + ' ' + ville + ' ' +pays ;
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

$(function () {
	$('.date_avec_evenements').each(function () {
		// options		
		var distance = 10;
		var time = 250;
		var hideDelay = 100;

		var hideDelayTimer = null;

		// tracker
		var beingShown = false;
		var shown = false;

		var trigger = $(this);
		var popup = $('.evenements ul', this).css('opacity', 0);

		// set the mouseover and mouseout on both element
		$([trigger.get(0), popup.get(0)]).mouseover(function () {
			$(this).addClass('date_hover');
			// stops the hide event if we move from the trigger to the popup element
			if (hideDelayTimer) clearTimeout(hideDelayTimer);

			// don't trigger the animation again if we're being shown, or already visible
			if (beingShown || shown) {
				return;
			} else {
				beingShown = true;

				// reset position of popup box
				popup.css({
					bottom: 20,
					left: -76,
					display: 'block' // brings the popup back in to view
				})

				// (we're using chaining on the popup) now animate it's opacity and position
				.animate({
					bottom: '+=' + distance + 'px',
					opacity: 1
				}, time, 'swing', function() {
					// once the animation is complete, set the tracker variables
					beingShown = false;
					shown = true;
				});
			}
		}).mouseout(function () {
			$(this).removeClass('date_hover');
			// reset the timer if we get fired again - avoids double animations
			if (hideDelayTimer) clearTimeout(hideDelayTimer);

			// store the timer so that it can be cleared in the mouseover if required
			hideDelayTimer = setTimeout(function () {
				hideDelayTimer = null;
				popup.animate({
					bottom: '-=' + distance + 'px',
					opacity: 0
				}, time, 'swing', function () {
					// once the animate is complete, set the tracker variables
					shown = false;
					// hide the popup entirely after the effect (opacity alone doesn't do the job)
					popup.css('display', 'none');
				});
			}, hideDelay);
		});
	});
});
