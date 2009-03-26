<?php
/*
*/
if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	//on récupère les tags de la page courante
	$tabtagsexistants = $this->GetAllTags($this->GetPageTag());
	foreach ($tabtagsexistants as $tab)
	{
		$tagspage[] = $tab["value"];
	}
	if (is_array($tagspage))
	{
		sort($tagspage);
		$tagsexistants = implode(' ', $tagspage).' ';
	}
	else
	{
		$tagsexistants = '';
	}
	$formtag = '<div id="formtags">
			<label for="tags">Mots cl&eacute;s : </label>
            <input class="wide" type="text" name="tags" value="'.$tagsexistants.'" id="tags" />
            </div>
	';
	$plugin_output_new=preg_replace ('/\<input name=\"submit\" type=\"submit\" value=\"Sauver\"/',
	$formtag.'<input name="submit" type="submit" value="Sauver"', $plugin_output_new);
}

?>
