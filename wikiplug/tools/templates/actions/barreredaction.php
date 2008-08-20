<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
//barre de r�daction

//action pour le footer de wikini
$wikini_barre_bas =
'<div class="footer">'."\n";
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
$this->Link($this->tag, "plugin", "Extensions")." ::\n".
'Recherche : <input name="phrase" size="15" class="searchbox" />'."\n".
$this->FormClose().
'</div>'."\n";

echo $wikini_barre_bas;
?>