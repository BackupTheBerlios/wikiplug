<?php
/**
* bazarcarto : programme affichant les fiches du bazar sous forme de Cartographie Google
*
*
*@package Bazar
//Auteur original :
*@author        Florian SCHMITT <florian.schmitt@laposte.net>
*@version       $Revision: 1.4 $ $Date: 2009/09/09 15:36:37 $
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
$resultat = $GLOBALS['_BAZAR_']['db']->query ($requete);

$script_marker = '';
if ($resultat->numRows() != 0) {	
	while ($chaine = $resultat->fetchRow(DB_FETCHMODE_ASSOC)) {
		if ($chaine['bf_latitude'] == 0 && $chaine['bf_longitude'] == 0) continue;
		$script_marker .= "\t".'point = new google.maps.LatLng('.$chaine['bf_latitude'].','.$chaine['bf_longitude'].');'."\n"."\t".
				'var contentString'.$chaine['bf_id_fiche'].' = \'<div class="BAZ_cadre_map">'.
			preg_replace("(\r\n|\n|\r)", '', addslashes(baz_voir_fiche(0, $chaine['bf_id_fiche']))).'</div>\';'."\n";        
	    $script_marker .= 'var infowindow'.$chaine['bf_id_fiche'].' = new google.maps.InfoWindow({
	        content: contentString'.$chaine['bf_id_fiche'].'
	    });
		//image du marqueur
	    var image = new google.maps.MarkerImage(\''.BAZ_IMAGE_MARQUEUR.'\',		
		//taille, point d\'origine, point d\'arrivee de l\'image
		new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_MARQUEUR.'),
		new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_MARQUEUR.'),
		new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_MARQUEUR.'));
		
		//ombre du marqueur
		var shadow = new google.maps.MarkerImage(\''.BAZ_IMAGE_OMBRE_MARQUEUR.'\',
		// taille, point d\'origine, point d\'arrivee de l\'image de l\'ombre
		new google.maps.Size('.BAZ_DIMENSIONS_IMAGE_OMBRE_MARQUEUR.'),
		new google.maps.Point('.BAZ_COORD_ORIGINE_IMAGE_OMBRE_MARQUEUR.'),
		new google.maps.Point('.BAZ_COORD_ARRIVEE_IMAGE_OMBRE_MARQUEUR.'));
	    
	    var marker'.$chaine['bf_id_fiche'].' = new google.maps.Marker({
	        position: point,
	        map: map,
	        icon: image,
	        shadow: shadow,
	        title: \''.$chaine['bf_titre'].'\'
	    });
	    google.maps.event.addListener(marker'.$chaine['bf_id_fiche'].', \'click\', function() {
	      infowindow'.$chaine['bf_id_fiche'].'.open(map,marker'.$chaine['bf_id_fiche'].');
	    });';			
	}
}

$script = '    
function initialize() { 
	var myLatlng = new google.maps.LatLng('.BAZ_GOOGLE_CENTRE_LAT.', '.BAZ_GOOGLE_CENTRE_LON.');
    var myOptions = {
      zoom: '.BAZ_GOOGLE_ALTITUDE.',
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.'.BAZ_TYPE_CARTO.',
      navigationControl: '.BAZ_AFFICHER_NAVIGATION.',
	  navigationControlOptions: {style: google.maps.NavigationControlStyle.'.BAZ_STYLE_NAVIGATION.'},
  	  mapTypeControl: '.BAZ_AFFICHER_CHOIX_CARTE.',
  	  mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.'.BAZ_STYLE_CHOIX_CARTE.'},  	  
  	  scaleControl: '.BAZ_AFFICHER_ECHELLE.'     
    }
    var map = new google.maps.Map(document.getElementById("map"), myOptions);        	
	';
	if (BAZ_GOOGLE_FOND_KML != '') {
		//rien de possible dans la v3 de google maps pour l'instant....
	};
      
    $script .= $script_marker.'
	};	
';

echo '<script type="text/javascript">'."\n".$script."\n".'</script>'."\n".
	 '<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.'; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.'"></div>'."\n";
?>