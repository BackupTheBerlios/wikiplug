<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if ($this->UserIsAdmin())
{

	if (isset($_GET['supprimer_tag']))
	{
		$sql = 'DELETE FROM '.$this->config['table_prefix'].'triples WHERE property="http://outils-reseaux.org/_vocabulary/tag" and value="'.mysql_escape_string($_GET['supprimer_tag']).'"';
		$this->Query($sql);
	}

	//on récupère tous les tags existants
	$sql = 'SELECT value, resource FROM '.$this->config['table_prefix'].'triples WHERE property="http://outils-reseaux.org/_vocabulary/tag" ORDER BY value ASC, resource ASC';
	$tab_tous_les_tags = $this->LoadAll($sql);

	if (is_array($tab_tous_les_tags))
	{
		echo '<ul class="taglist">'."\n";
		$nb_pages = 0;
		$liste_page = '';
		$tag_precedent = '';
		$tab_tous_les_tags[]='fin'; //on ajoute un élément au tableau pour bloucler une derniere fois
		foreach ($tab_tous_les_tags as $tab_les_tags)
		{
			if ($tab_les_tags['value']==$tag_precedent || $tag_precedent== '')
			{
				$nb_pages++;
				$liste_page .= '<a href="'.$this->href('',$tab_les_tags['resource']).'">'.$tab_les_tags['resource'].'</a>, ';

			}
			else
			{
				//on affiche les informations pour ce tag
				if ($nb_pages>1) $texte_page='ces '.$nb_pages.' pages';
				else $texte_page='la page';
				$texte_liste  = '<li class="taglistitem">'."\n".'<span class="tagname">'.$tag_precedent.'</span>'."\n";
				$texte_liste .= 'présent dans '.$texte_page.' :<br />'."\n";
				$texte_liste .= substr($liste_page, 0, -2)."\n";
				$texte_liste .= '<a class="linkdel" href="'.$this->href().'&supprimer_tag='.$tag_precedent.'">Supprimer tous les tags "'.$tag_precedent.'"</a>'."\n";
				$texte_liste .= '</li>'."\n";
				echo $texte_liste;

				//on réinitialise les variables
				$nb_pages = 1;
				$liste_page = '<a href="'.$this->href('',$tab_les_tags['resource']).'">'.$tab_les_tags['resource'].'</a>, ';
			}
			$tag_precedent = $tab_les_tags['value'];
		}
		echo '</ul>'."\n";
	}
}
else
{
	echo $this->Format("//L'action admintag est r&eacute;serv&eacute;e au groupe des administrateurs...//");
}

?>
