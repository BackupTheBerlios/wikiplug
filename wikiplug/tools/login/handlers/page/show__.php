<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

if ($contenu = $this->LoadPage("PageLogin")) {
	$plugin_output_new = str_replace ("<i>Vous n'&ecirc;tes pas autoris&eacute; &agrave; lire cette page</i>", $this->Format($contenu["body"]), $plugin_output_new);
}

?>
