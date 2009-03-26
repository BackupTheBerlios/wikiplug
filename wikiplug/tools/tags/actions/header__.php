<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


	//on récupère tous les tags existants
	$tab_tous_les_tags = $this->GetAllTags();
	$tags = '';
	if (is_array($tab_tous_les_tags))
	{
		foreach ($tab_tous_les_tags as $tab_les_tags)
		{
			$tags .= $tab_les_tags['value'].' ';
		}
		$tags = substr($tags,0,-1);
	}


	$tous_les_tags = split(' ', $tags);
	$remplacement ='
	<link rel="stylesheet" href="tools/tags/presentation/tags.css" type="text/css" media="screen" />
	<script type="text/javascript" src="tools/tags/lib/jquery-1.3.2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="tools/tags/lib/tag.js" type="text/javascript"></script>
    <script type="text/javascript" src="tools/tags/lib/hovertip.js"></script>
		<script type="text/javascript">
			// initialize tooltips in a seperate thread
			$(document).ready(function() {
			  window.setTimeout(hovertipInit, 1);
		     });
		</script>
	';

	if ($this->GetMethod() == "edit")
	{
		$remplacement .='
	    <script type="text/javascript">
	    <!--
	    $(function () {
	        $(\'#tags\').tagSuggest({
	            tags: '.json_encode($tous_les_tags).'
	        });
	    });
	    //-->
	    </script>
		 ';
	}

	$plugin_output_new=preg_replace ('/<\/head>/', $remplacement."\n".'</head>', $plugin_output_new);

?>
