<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


if ($this->GetMethod() == "edit") {
	

	$plugin_output_new=preg_replace ('/<\/head>/',
	'<script type="text/javascript" src="tools/chatmot/ChatMotACeditor.js"></script>
	</head>', $plugin_output_new);
		
}	