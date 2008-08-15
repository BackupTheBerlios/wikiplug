<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if (!empty($dblclic)&&$dblclic=="1") {
	$plugin_output_new='<div style="display:block;width:100%;height:100%;" ondblclick="document.location=\''.$this->Href("edit", $incPageName).'\';">'."\n".$plugin_output_new."\n".'<div style="clear:both;display:block;"></div>'."\n".'</div>'."\n";
}

?>
