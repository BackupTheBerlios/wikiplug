<?php

// Partie publique

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
$wiki  = new WikiTools($wakkaConfig);
$wikiClasses [] = 'Tags';
$wikiClassesContent [] = '

	function SaveTags($page, $liste_tags)
    {
		$tags = explode(" ", mysql_escape_string($liste_tags));
		//on récupère les anciens tags de la page courante
		$tabtagsexistants = $this->GetAllTriplesValues($page, \'http://outils-reseaux.org/_vocabulary/tag\', \'\', \'\');
		if (is_array($tabtagsexistants))
		{
			foreach ($tabtagsexistants as $tab)
			{
				$tags_restants_a_effacer[] = $tab["value"];
			}
		}

		//on ajoute le tag s il n existe pas déjà
		foreach ($tags as $tag)
		{
			trim($tag);
			if ($tag!=\'\')
			{
				if (!$this->TripleExists($page, \'http://outils-reseaux.org/_vocabulary/tag\', $tag, \'\', \'\'))
				{
					$this->InsertTriple($page, \'http://outils-reseaux.org/_vocabulary/tag\', $tag, \'\', \'\');
				}
			}

			//on supprime ce tag du tableau des tags restants à effacer
			if (isset($tags_restants_a_effacer)) unset($tags_restants_a_effacer[array_search($tag, $tags_restants_a_effacer)]);
		}

		//on supprime les tags restants a effacer
		if (isset($tags_restants_a_effacer))
		{
			foreach ($tags_restants_a_effacer as $tag)
			{
				$this->DeleteTriple($page, \'http://outils-reseaux.org/_vocabulary/tag\', $tag, \'\', \'\');
			}
		}
		return;
	}

	function GetAllTags($page=\'\')
	{
		if ($page==\'\')
		{
			$sql = \'SELECT DISTINCT value FROM \'.$this->config[\'table_prefix\'].\'triples WHERE property="http://outils-reseaux.org/_vocabulary/tag"\';
			return $this->LoadAll($sql);
		}
		else
		{
			return $this->GetAllTriplesValues($this->GetPageTag(), \'http://outils-reseaux.org/_vocabulary/tag\', \'\', \'\');
		}
	}

';


?>
