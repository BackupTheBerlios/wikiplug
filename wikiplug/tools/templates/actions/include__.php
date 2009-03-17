<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if (!empty($dblclic)&&$dblclic=="1") {
	$pattern = '/<span class="missingpage">(.*)<\/span><a href="(.*)">\?<\/a>/U';
	$replacement = '<span class="missingpage">\\1</span><form name="editform'.$pageincluded.'\\1" action="\\2" method="post" style="display:inline;margin-left:-5px;">
	<a href="javascript:document.editform'.$pageincluded.'\\1.submit();" title="Editer cette nouvelle page Wikini">?</a>
	<input type="hidden" name="theme" value="'.$this->config['favorite_theme'].'" />		
	<input type="hidden" name="squelette" value="'.$this->config['favorite_squelette'].'" />
	<input type="hidden" name="style" value="'.$this->config['favorite_style'].'" />
	</form>'."\n";
	
	$plugin_output_new=preg_replace($pattern, $replacement, $plugin_output_new );

	$plugin_output_new='<div style="display:block;width:100%;height:100%;" ondblclick="document.location=\''.$this->Href("edit", $incPageName).'\';">'."\n".$plugin_output_new."\n".'<div style="clear:both;display:block;"></div>'."\n".'</div>'."\n";
}



?>
