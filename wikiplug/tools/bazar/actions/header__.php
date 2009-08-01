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
	$liste .= '<link rel="alternate" type="application/rss+xml" title="'.$ligne['bn_label_nature'].'" href="'.str_replace('&','&amp;',$lien_RSS->getURL()).'"  />'."\n";
	$lien_RSS->removeQueryString('annonce');
}
$liste = '<link rel="alternate" type="application/rss+xml" title="'.BAZ_FLUX_RSS_GENERAL.'" href="'.str_replace('&','&amp;',$lien_RSS->getURL()).'" />'."\n".$liste."\n";


//ajout des styles css pour bazar, le calendrier, la google map
$style = '<link rel="stylesheet" type="text/css" href="tools/bazar/presentation/bazar.css" media="screen" />'."\n";

//on cherche l'action bazar ou l'action bazarcarto dans la page, pour voir s'il faut charger googlemaps
if ($_POST["submit"] == html_entity_decode('Aper&ccedil;u')) {
	$contenu["body"] = $_POST["body"];
} else $contenu=$this->LoadPage($this->tag);
//si l'on trouve des actions bazar
if ($act=preg_match_all ("/".'(\\{\\{bazar)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches)) {
	$style .= '<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.BAZ_GOOGLE_KEY.'" ></script>
<script type="text/javascript" src="tools/bazar/libs/bazar.js"></script>';
if ($this->GetMethod() == "show") {$plugin_output_new=preg_replace ('/<body /', '<body onload="load()" ',	$plugin_output_new, 1);}
}

if ($this->GetMethod() == "show") {
	$plugin_output_new=preg_replace ('/<\/head>/', $liste.$style."\n".'</head>', $plugin_output_new);	
}