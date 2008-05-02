<?php
//On recupere le template et on execute les actions wikini
$template = file_get_contents('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes/'.$this->config['favorite_squelette']);
$template_decoupe = explode("{WIKINI_PAGE}", $template);
   if ($act=preg_match_all ("/".'(\\{\\{)'.'(.*?)'.'(\\}\\})'."/is", $template_decoupe[1], $matches)) {
     $i = 0; $j = 0;
     foreach($matches as $valeur) {
       foreach($valeur as $val) {
         if ($matches[2][$j]!='') {
           $action= $matches[2][$j];
           $template_decoupe[1]=str_replace('{{'.$action.'}}', $this->Format('{{'.$action.'}}'), $template_decoupe[1]);
         }
         $j++;
       }
       $i++;
     }
   }

//on utilise la bibliotheque pear template it pour gerer les variables dans la template
require_once 'tools/templates/libs/IT.php';
$tpl = new HTML_Template_IT('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes');

$tpl->setTemplate($template_decoupe[1], true, true);

//action pour le footer de wikini
$wikini_barre_bas =
'<div class="footer">'."\n";
//echo $this->FormOpen("", "RechercheTexte", "get");
$wikini_barre_bas .=
$this->FormOpen("", "RechercheTexte", "get")."\n";
if ( $this->HasAccess("write") ) {
	$wikini_barre_bas .= "<a href=\"".$this->href("edit")."\" title=\"Cliquez pour &eacute;diter cette page.\">&Eacute;diter cette page</a> ::\n";
}
if ( $this->GetPageTime() ) {
	$wikini_barre_bas .= "<a href=\"".$this->href("revisions")."\" title=\"Cliquez pour voir les derni&egrave;res modifications sur cette page.\">".$this->GetPageTime()."</a> ::\n";
}
// if this page exists
if ($this->page)
{
	// if owner is current user
	if ($this->UserIsOwner())
	{
		$wikini_barre_bas .=
		"Propri&eacute;taire&nbsp;: vous :: \n".
		"<a href=\"".$this->href("acls")."\" title=\"Cliquez pour &eacute;diter les permissions de cette page.\">&Eacute;diter permissions</a> :: \n".
		"<a href=\"".$this->href("deletepage")."\">Supprimer</a> :: \n";
	}
	else
	{
		if ($owner = $this->GetPageOwner())
		{
			$wikini_barre_bas .= "Propri&eacute;taire : ".$this->Format($owner);
		}
		else
		{
			$wikini_barre_bas .= "Pas de propri&eacute;taire ";
			$wikini_barre_bas .= ($this->GetUser() ? "(<a href=\"".$this->href("claim")."\">Appropriation</a>)" : "");
		}
		$wikini_barre_bas .= " :: \n";
	}
}
$wikini_barre_bas .=
'<a href="'.$this->href("referrers").'" title="Cliquez pour voir les URLs faisant r&eacute;f&eacute;rence &agrave; cette page.">'."\n".
'R&eacute;f&eacute;rences</a> ::'."\n".
'Recherche : <input name="phrase" size="15" class="searchbox" />'."\n".
$this->FormClose()."\n".
'</div>'."\n";

$tpl->setVariable('WIKINI_BARRE_BAS', $wikini_barre_bas);
$tpl->setVariable('WIKINI_VERSION', $this->GetWikiNiVersion());
$tpl->setVariable('WIKINI_BASE_URL', $this->GetConfigValue('base_url'));
$plugin_output_new = $tpl->show();
?>