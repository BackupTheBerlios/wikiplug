<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


//ajout du style css pour la syndication
$ajout = '<link rel="stylesheet" type="text/css" href="tools/syndication/presentation/styles/syndication.css" media="screen" />'."\n";

//ajout du javascript
$ajout .= '<script type="text/javascript" src="tools/syndication/presentation/javascripts/syndication.js"></script>'."\n";

if ($this->GetMethod() == "show") {
	$plugin_output_new = preg_replace ('/<\/head>/', $ajout."\n".'</head>', $plugin_output_new);	
}
