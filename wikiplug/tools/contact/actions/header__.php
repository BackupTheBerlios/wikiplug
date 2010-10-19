<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}
$plugin_output_new = str_replace ('</head>','<link rel="stylesheet" type="text/css" href="tools/contact/presentation/styles/style.css" />'."\n".'</head>', $plugin_output_new);

?>	
