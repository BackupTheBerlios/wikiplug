<?php
/**
* bazarcarto : programme affichant les fiches du bazar sous forme de Cartographie Google
*
*
*@package Bazar
//Auteur original :
*@author        Florian SCHMITT <florian@outils-reseaux.org>
*@version       $Revision: 1.5 $ $Date: 2010/03/04 14:19:03 $
// +------------------------------------------------------------------------------------------------------+
*/


// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

//récupération des paramètres wikini
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

// requete sur le bazar pour recuperer les fiches
// $requete = 'SELECT * FROM bazar_fiche WHERE ';
// if ($GLOBALS['_BAZAR_']['categorie_nature'] != 'toutes') $requete .= 'bf_categorie_fiche="'.$GLOBALS['_BAZAR_']['categorie_nature'].'" and ' ;
// if ($GLOBALS['_BAZAR_']['id_typeannonce'] != 'toutes') $requete .= 'bf_ce_nature="'.$GLOBALS['_BAZAR_']['id_typeannonce'].'" and ' ;
// $requete .= ' ((bf_date_debut_validite_fiche<=now() and bf_date_fin_validite_fiche>=now()) or (bf_date_fin_validite_fiche="0000-00-00"))'.
// 			' and bf_statut_fiche=1';		
// $resultat = $GLOBALS['_BAZAR_']['db']->query ($requete);

$tableau_resultat = baz_requete_recherche_fiches();

//var_dump($tableau_resultat);

$script_marker = '';
foreach ($tableau_resultat as $id)
{
	$GLOBALS['_BAZAR_']['id_fiche'] = $id[0];
	$GLOBALS['_BAZAR_']['template'] = $id[26];
	$GLOBALS['_BAZAR_']['label_typeannonce'] = $id[16];
	
	$chaine = baz_valeurs_fiche($id[0]);
	if ($chaine['bf_latitude'] == 0 && $chaine['bf_longitude'] == 0) continue;
	//$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
	//$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
	//$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $GLOBALS['_BAZAR_']['id_fiche']);
	$script_marker .= "\t".'var point = new google.maps.LatLng('.$chaine['bf_latitude'].','.$chaine['bf_longitude'].');'."\n"."\t".
			'var contentString'.$GLOBALS['_BAZAR_']['id_fiche'].' = \'<div class="BAZ_cadre_map">'.
		preg_replace("(\r\n|\n|\r)", '', addslashes(baz_voir_fiche(0, $GLOBALS['_BAZAR_']['id_fiche']))).'\';'."\n"."\t".
		//'<h1>'.addslashes($id[3]).'</h1>'.'<a href="'.str_replace('&', '&amp;', $GLOBALS['_BAZAR_']['url']->getUrl()).'">Voir la fiche complète</a>'.'</div>\';'."\n";        
	$script_marker .= 'var infowindow'.$GLOBALS['_BAZAR_']['id_fiche'].' = new google.maps.InfoWindow({
		content: contentString'.$GLOBALS['_BAZAR_']['id_fiche'].'
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
	
	var marker'.$GLOBALS['_BAZAR_']['id_fiche'].' = new google.maps.Marker({
		position: point,
		map: map,
		icon: image,
		shadow: shadow,
		title: \''.addslashes($id[3]).'\'
	});
	google.maps.event.addListener(marker'.$GLOBALS['_BAZAR_']['id_fiche'].', \'click\', function() {
	  infowindow'.$GLOBALS['_BAZAR_']['id_fiche'].'.open(map,marker'.$GLOBALS['_BAZAR_']['id_fiche'].');
	});';			
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
	if (defined('BAZ_GOOGLE_FOND_KML') && BAZ_GOOGLE_FOND_KML != '') {
		//rien de possible dans la v3 de google maps pour l'instant....
	};
      
    $script .= $script_marker.'
	};	
';


echo '<script type="text/javascript">'."\n".$script."\n".'</script>'."\n".
	 '<div id="map" style="width: '.BAZ_GOOGLE_IMAGE_LARGEUR.'; height: '.BAZ_GOOGLE_IMAGE_HAUTEUR.'"></div>'."\n";
	 
/*
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.google.com/earth/kml/2">
<Document>
  <name>kml_sample1.kml</name>
  <Placemark>
    <name>Google Inc.</name>
    <description><![CDATA[
      Google Inc.<br />
      1600 Amphitheatre Parkway<br />
      Mountain View, CA 94043<br />
      Phone: +1 650-253-0000<br />
      Fax: +1 650-253-0001<br />
      <p>Home page: <a href="http://www.google.com">www.google.com</a></p>
    ]]>
    </description>
    <Point>
      <coordinates>-122.0841430, 37.4219720, 0</coordinates>
    </Point>
  </Placemark>

  <Placemark>
    <name>Yahoo! Inc.</name>
    <description><![CDATA[
      Yahoo! Inc.<br />
      701 First Avenue<br />
      Sunnyvale, CA 94089<br />
      Tel: (408) 349-3300<br />
      Fax: (408) 349-3301<br />
      <p>Home page: <a href="http://yahoo.com">http://yahoo.com</a></p>
      ]]>
    </description>
    <Point>
      <coordinates>-122.0250403,37.4163228</coordinates>
    </Point>
  </Placemark>

  <Placemark>
    <name>Location 3</name>
    <description>This is location 3</description>
    <Point>
      <coordinates>-122.063,37.4063228</coordinates>
    </Point>
  </Placemark>

</Document>
</kml>
*/

?>
