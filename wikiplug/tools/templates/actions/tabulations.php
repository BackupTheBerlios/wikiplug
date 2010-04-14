<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

//on récupère les tabulations globales stockées
$tabs_globales = explode("\n", $this->GetTripleValue('tab_globale', 'http://outils-reseaux.org/_vocabulary/tabs', '', ''));

echo '<ul>'."\n";
echo '<li><a href="#content">'.$this->GetPageTag().'</a><span class="ui-icon ui-icon-close">Enlever l\'onglet</span></li>'."\n";
if (is_array($tabs_globales)) {
	foreach ($tabs_globales as $tab_globale) {
		if ($this->IsWikiName(trim($tab_globale))) {
			echo '<li><a href="'.$this->href('',$tab_globale).'">'.$tab_globale.'</a><span class="ui-icon ui-icon-close">Enlever l\'onglet</span></li>'."\n";
		}
	}
}
echo '</ul>'."\n";s

?>
