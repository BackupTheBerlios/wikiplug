<?php
/*
$Id: include.php,v 1.1 2009/07/21 12:32:04 mrflos Exp $
Permet d'inclure une page Wiki dans un autre page

Copyright 2003  Eric FELDSTEIN
Copyright 2003, 2004, 2006  Charles NEPOTE
Copyright 2004  Jean Christophe ANDRE
Copyright 2005  Didier Loiseau
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

/* Parametres :
 -- page : nom wiki de la page a inclure (obligatoire)
 -- class : nom de la classe de style a inclure (facultatif)
 -- auth : option d'affichage dans le cas d'un utilisateur non autorise (facultatif)
    -- par defaut : ne fait rien
    -- valeur "noError" : n'affiche aucun message d'erreur
 -- edit : option d'acces en edition a la page incluse (facultatif)
    -- par defaut : ne fait rien
    -- valeur "show" :  ajoute un lien "[edition]" en haut a droite de la boite
*/ 

// recuperation du nom de la page a inclure
$incPageName = trim($this->GetParameter('page'));

/**
* @todo ameliorer le traitement des classes css
*/
if ($this->GetParameter('class'))
{
	$array_classes = explode(' ', $this->GetParameter('class'));
	$classes = '';
	foreach ($array_classes as $c)
	{
		if ($c && preg_match('`^[A-Za-z0-9-_]+$`', $c))
		{
			$classes .= ($classes ? ' ':'') . "include_$c";
		}
	} 
} 

// Affichage de la page ou d'un message d'erreur
//
if (empty($incPageName))
{
	echo $this->Format('//**Erreur ActionInclude**: Le param&egrave;tre "page" est manquant.//');
}
elseif ($this->IsIncludedBy($incPageName))
{
	$inclusions = $this->GetAllInclusions();
	$pg = strtolower($incPageName); // on l'effectue avant le for sinon il sera recalcule a chaque pas
	$err = '[[' . $pg . ']]';
	for($i = 0; $inclusions[$i] != $pg; $i++)
	{
		$err = '[[' . $inclusions[$i] . ']] > ' . $err;
	} 
	echo $this->Format("//**Erreur ActionInclude**: Impossible pour la page '[[$incPageName]]' de s'inclure en elle m&ecirc;me//"
		 . ($i ? ":---**Chaine d'inclusions**: [[$pg]] > $err": '.')); // si $i = 0, alors c'est une page qui s'inclut elle-meme directement...
}
elseif (!$this->HasAccess('read', $incPageName) && $this->GetParameter('auth')!='noError')
{
	echo $this->Format("//**Erreur ActionInclude**: Lecture de la page inclue '\"\"$incPageName\"\"' non autoris&eacute;e.//");
}
elseif (!$incPage = $this->LoadPage($incPageName))
{
	echo $this->Format("//**Erreur ActionInclude**: La page inclue '[[$incPageName]]' ne semble pas exister...//");
} 
// Affichage de la page quand il n'y a pas d'erreur
elseif ($this->HasAccess('read', $incPageName))
{
	$this->RegisterInclusion($incPageName);
	$output = $this->Format($incPage['body']);
	if ($this->HasAccess('write', $incPageName)
		 || $this->UserIsOwner($incPageName)
		 || $this->UserIsAdmin($incPageName))
		{
			$page = '<div class="pageeditlink">';
			if ($this->HasAccess('write', $incPageName))
			{
				$page .= '<a class="lien_edit_page" href="'.$this->href('edit',$incPageName).'">&Eacute;diter</a>';
			}
			if ($this->UserIsOwner($incPageName)
			 || $this->UserIsAdmin())
			{
				$page .= '<br />'.'<a class="lien_suppr_paget" href="'.$this->href('deletepage',$incPageName).'">Supprimer</a>';
			}
			$page .= "</div>\n";
		}
	if (isset($classes))
	{
		// Affichage
		echo "<div class=\"include " . $classes . "\">\n" . $page . $output . "</div>\n";
	}
	else echo $output;
	$this->UnregisterLastInclusion();
}

?>