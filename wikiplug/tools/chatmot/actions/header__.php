<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


if ($this->GetMethod() == "edit") {
	

	$plugin_output_new=preg_replace ('#</script>#',
	'
	</script>
	<script type="text/javascript" src="tools/chatmot/ACeditor.js"></script> 
	',
	$plugin_output_new);
		
}	