<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if ($this->GetMethod() == "show" || $this->GetMethod() == "bazarframe" || $this->GetMethod() == "edit") {
	$javascript = '<script type="text/javascript" src="tools/bazar/libs/bazar.js"></script>'."\n";
		
	//on cherche l'action bazar ou l'action bazarcarto dans la page, pour voir s'il faut charger googlemaps
	if (isset($_POST["submit"]) && $_POST["submit"] == html_entity_decode('Aper&ccedil;u')) {
		$contenu["body"] = $_POST["body"];
	} else $contenu=$this->LoadPage($this->tag);
	//si l'on trouve des actions bazar
	if (($this->GetMethod() == "show") && $act=preg_match_all ("/".'(\\{\\{bazar)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches)) {
		if (isset($_GET[BAZ_VARIABLE_ACTION])&&($_GET[BAZ_VARIABLE_ACTION]==BAZ_ACTION_MODIFIER||$_GET[BAZ_VARIABLE_ACTION]==BAZ_ACTION_NOUVEAU||$_GET[BAZ_VARIABLE_ACTION]==BAZ_DEPOSER_ANNONCE)
			||$act=preg_match_all ("/".'(\\{\\{bazarcarto)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches))
		{
			$javascript .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>'."\n";
			$javascript .= '<script type="text/javascript" src="http://www.google.com/jsapi"></script>'."\n";
		}
	}
	$plugin_output_new = preg_replace ('/<\/body>/', $javascript."\n".'</body>', $plugin_output_new);	
}