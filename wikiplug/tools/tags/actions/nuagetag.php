<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

//on récupère le nb maximum et le nb minimum d'occurences
$sql = 'SELECT COUNT(value) AS nb FROM '.$this->config['table_prefix'].'triples WHERE property="http://outils-reseaux.org/_vocabulary/tag" GROUP BY value';
$min_max = $this->LoadAll($sql);
$min=1000000;$max=0;
foreach ($min_max as $tab_min_max)
{
		if ($tab_min_max['nb']>$max)
		{
			$max=$tab_min_max['nb'];
		}
		elseif ($tab_min_max['nb']<$min)
		{
			$min=$tab_min_max['nb'];
		}
}
//permettra de fixer une classe pour la taille du tag
$nb_taille_tag=6;
$mult=$max/$nb_taille_tag;
if ($mult<1) $mult=1;

//on récupère tous les tags existants
$sql = 'SELECT value, resource FROM '.$this->config['table_prefix'].'triples WHERE property="http://outils-reseaux.org/_vocabulary/tag" ORDER BY value ASC, resource ASC';
$tab_tous_les_tags = $this->LoadAll($sql);

if (is_array($tab_tous_les_tags))
{	
	$i=1;$nb_pages=0;
	$liste_page = '';
	$tag_precedent = '';
	$tab_tous_les_tags[]='fin'; //on ajoute un élément au tableau pour bloucler une derniere fois
	foreach ($tab_tous_les_tags as $tab_les_tags)
	{
		if ($tab_les_tags['value']==$tag_precedent || $tag_precedent== '')
		{
			$nb_pages++;
			$liste_page .= '<li><a class="link_pagewiki" href="'.$this->href('',$tab_les_tags['resource']).'">'.$tab_les_tags['resource'].'</a></li>'."\n";

		}
		else
		{
			//on affiche les informations pour ce tag
			if ($nb_pages>1) $texte_page= $nb_pages.' pages associ&eacute;es';
			else $texte_page='Une page associ&eacute;e';
			$texte_liste  = '<li>'."\n".'<a class="size'.ceil($nb_pages/$mult).'" href="#" id="j'.$i.'">'.$tag_precedent.'</a>'."\n";
			$texte_liste .= '<ul style="display: block;" class="hovertip" target="j'.$i.'"><li class="texte_pages_assoc">'.$texte_page.' :</li>'."\n";
			$texte_liste .= $liste_page."\n";
			$texte_liste .= '</ul>'."\n";
			$texte_liste .= '</li>'."\n";
			$tab_tag[] = $texte_liste;

			//on réinitialise les variables
			$nb_pages = 1;
			$liste_page = '<li><a class="link_pagewiki" href="'.$this->href('',$tab_les_tags['resource']).'">'.$tab_les_tags['resource'].'</a></li>'."\n";
			$i++;
		}
		$tag_precedent = $tab_les_tags['value'];
	}

	if (is_array($tab_tag))
	{
		echo '<ul class="nuage">'."\n";
		//on regarde s'il faut trier alphabetiquement
		$tri = $this->GetParameter('tri');
		if (!empty($tri) && $tri=="alpha")
		{
		}
		else
		{
			shuffle($tab_tag);
		}
		foreach ($tab_tag as $tag) {
			echo $tag;
		}
		echo '</ul><div style="clear:both">&nbsp;</div>'."\n";
	}
}

?>
