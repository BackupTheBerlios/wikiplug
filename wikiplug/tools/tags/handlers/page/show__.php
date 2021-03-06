<?php
/*
$Id: show__.php,v 1.1 2011/12/19 09:51:10 mrflos Exp $
Copyright (c) 2002, Florian Schmitt <florian@outils-reseaux.org>
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

// V�rification de s�curit�
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}

$output = '';
$pageouverte = $this->GetTripleValue($this->GetPageTag(),'http://outils-reseaux.org/_vocabulary/comments', '', '');
if ((COMMENTAIRES_OUVERTS_PAR_DEFAUT && $pageouverte!='0' ) || (!COMMENTAIRES_OUVERTS_PAR_DEFAUT && $pageouverte=='1')) {
	if ($HasAccessRead && (!$this->page || !$this->page["comment_on"]))
	{
		// load comments for this page
		$comments = $this->LoadComments($this->tag);
		
		// store comments display in session
		$tag = $this->GetPageTag();
		if (!isset($_SESSION["show_comments"][$tag]))
			$_SESSION["show_comments"][$tag] = ($this->UserWantsComments() ? "1" : "0");
		if (isset($_REQUEST["show_comments"])){	
		switch($_REQUEST["show_comments"])
		{
		case "0":
			$_SESSION["show_comments"][$tag] = 0;
			break;
		case "1":
			$_SESSION["show_comments"][$tag] = 1;
			break;
		}
		}
		// display comments!
		include_once('tools/tags/libs/tags.functions.php');
		$gestioncommentaire = '<strong class="lien_commenter">Commentaires sur cette page.'."\n";
		if (($this->UserIsOwner()) || ($this->UserIsAdmin()))
		{
			$gestioncommentaire .= '<a href="'.$this->href('closecomments').'" title="D&eacute;sactiver les commentaires sur cette page">D&eacute;sactiver les commentaires</a>'."\n";
		}
		$gestioncommentaire .= '.</strong>'."\n";
		$gestioncommentaire .= "<div class=\"commentaires_billet_microblog\">\n";
		$gestioncommentaire .= afficher_commentaires_recursif($this->getPageTag(), $this);
		$gestioncommentaire .= "</div>\n";
		$output .= $gestioncommentaire;
	
	}
}
else //commentaire pas ouverts
{
	if (($this->UserIsOwner()) || ($this->UserIsAdmin()))
	{
		$output .= '<div class="admin_commenter">Commentaires d&eacute;sactiv&eacute;s '."\n".'<a href="'.$this->href('opencomments').'" title="Activer les commentaires sur cette page">Activer les commentaires</a>.</div>'."\n";
	}
}

// on affiche la liste des mots cl�s disponibles pour cette page 
if (!CACHER_MOTS_CLES)
{
	$tabtagsexistants = $this->GetAllTags($this->GetPageTag());
	$tagspage = array();
	foreach ($tabtagsexistants as $tab)
	{
		$tagspage[] = $tab["value"];
	}
	if (count($tagspage)>0)
	{
		sort($tagspage);
		$tagsexistants = '<ul class="tagit ui-widget ui-widget-content ui-corner-all noborder">'."\n";
		foreach ($tagspage as $tag)
		{
			$tagsexistants .= '<li class="tagit-tag ui-widget-content ui-state-default ui-corner-all">
				<a href="'.$this->href('listpages',$this->GetPageTag(),'tags='.$tag).'" title="Voir toutes les pages contenant ce mot cl&eacute;">'.$tag.'</a>
			</li>'."\n";
		}
		$tagsexistants .= '</ul>'."\n";
		$output .= '<div class="list_tags">'."\n".$tagsexistants.'</div>'."\n";
	}
}

$plugin_output_new = preg_replace ('/\<hr class=\"hr_clear\" \/\>/', '<hr class="hr_clear" />'."\n".$output, $plugin_output_new);

?>
