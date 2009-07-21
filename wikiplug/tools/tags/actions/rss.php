<?php
/*
rsstag.php

Copyright 2009  Florian SCHMITT
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ($this->GetMethod() != 'xml')
{
	echo '<a href="'.$this->Href('xml').'">pages <img src="tools/tags/presentation/rss.png" alt="logo rss" /></a>'."\n";
	return;
} else
{
	$titrerss = $this->GetParameter('titrerss');
	$tags = $this->GetParameter("tags");
	if (empty($tags))
	{
		die ('<span class="error">Action rss : param&egrave;tre with obligatoire.</span>');
	}
	
	$notags = $this->GetParameter("notags");
	
	//on fait les tableaux pour les tags, puis on met des virgules et des guillemets
	$tags=trim($tags);
	$tab_tags = explode(" ", $tags);
	$nbdetags = count($tab_tags);
	$tags = implode(",", array_filter($tab_tags, "trim"));
	$tags = '"'.str_replace(',','","',$tags).'"';
	
	if (!empty($notags))
	{
		$notags=trim($notags);
		$tab_notags = explode(" ", $notags);
		$nbdenotags = count($tab_notags);
		$notags = implode(",", array_filter(explode(" ", $tab_notags), "trim"));
		$notags = '"'.str_replace(',','","',$notags).'"';
	}
	
	$req =' AND value IN ('.$tags.')';
	if (!empty($notags))
	{
		$req .= ' AND value NOT IN ('.$notags.')';
	}
	$req .= ' AND property="http://outils-reseaux.org/_vocabulary/tag" AND resource=tag GROUP BY tag HAVING COUNT(tag)='.$nbdetags.' ';
	
	$nb_caracteres_rss - $this->GetParameter("nbcar");
	
	//gestion du tri de l'affichage
	if (!empty($tri))
	{
		if ($tri == "alpha")
		{
			$req .= ' ORDER BY tag ASC';
		}
		elseif ($tri == "date")
		{
			$req .= ' ORDER BY time DESC';
		}
	}
	//par defaut on tri par date
	else
	{
			$req .= ' ORDER BY time DESC';
	}
	
	$requete = "SELECT DISTINCT tag, time, user, owner, body FROM ".$this->config["table_prefix"]."pages, ".$this->config["table_prefix"]."triples WHERE latest = 'Y' and comment_on = '' ".$req;	
	if ($pages = $this->LoadAll($requete)) {
		if (!($link = $this->GetParameter("link"))) {$link=$this->config["root_page"];}
		//header("Content-Type: application/rss+xml");
		$output = "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		$output .= "<channel>\n<title>";		
		if (empty($titrerss)) {
			$output .= "Derniers changements sur ". $this->config["wakka_name"]  ;		
			$output .= ' contenant les tags '.$tags;
			if (!empty($notags))
			{
				$output .= ' et sans les tags '.$notags;
			}
		} else { 
			$output .= $titrerss;
		}
		$output .= "</title>\n";
		$output .= "<link>" . $this->config["base_url"] . $link . "</link>\n";
		$output .= "<description>Derniers changements sur " . $this->config["wakka_name"] ;
		$output .= ' contenant les tags '.$tags;
		if (!empty($notags))
		{
			$output .= ' et sans les tags '.$notags;
		}
		$output .= "</description>\n";
		$output .= "<atom:link href=\"". $this->Href('xml') ."\" rel=\"self\" type=\"application/rss+xml\" />\n";
		$items = '';
		foreach ($pages as $page)
		{
			$items .= "<item>\r\n";
	        $items .= "<title>".$page['tag']."</title>\r\n";
	        $items .= "<link>" . $this->config["base_url"] . $page["tag"] . "</link>\r\n";
	        $items .= "<description><![CDATA[";
	
			//on enleve les actions recentchangesrssplus pour eviter les boucles infinies, avant de formater en HTML le texte
			$page["body"] = preg_replace("/\{\{recentchangesrssplus(.*?)\}\}/s", '', $page["body"]);
	
			$texteformat = $this->Format($page['body']);
	
			//on tronque le texte apres le prochain espace
			if (!empty($nb_caracteres_rss) && (strlen($texteformat) > $nb_caracteres_rss) )
			{
				$texteformat = substr($texteformat, 0, $nb_caracteres_rss);
				$last_space = strrpos($texteformat, " ");
				$texteformat = substr($texteformat, 0, $last_space)."<br /><a href=\"".$this->config["base_url"] . $page["tag"] . "\" title=\"Lire la suite\">Lire la suite</a>";
			}
	
			$items .= $texteformat . "]]></description>\r\n";
	        $items .= "<dc:creator>by ".htmlspecialchars($page["user"])."</dc:creator>\r\n";
			$items .= "<pubDate>" . gmdate('D, d M Y H:i:s \G\M\T', strtotime($page['time'])) . "</pubDate>\r\n";
			$itemurl = $this->href(false, $page["tag"], "time=" . htmlspecialchars(rawurlencode($page["time"])));
			$items .= '<guid>' . $itemurl . "</guid>\n";
	        $items .= "</item>\r\n";
		}
	
		$output .= $items;
	    $output .= "</channel>\n";
	    $output .= "</rss>\n";
		die("\n".$output);
	}
}
?>