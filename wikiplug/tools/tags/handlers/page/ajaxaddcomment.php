<?php
/*
$Id: ajaxaddcomment.php,v 1.1 2009/07/21 12:32:04 mrflos Exp $
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2003  Eric FELDSTEIN
Copyright 2004  Jean Christophe ANDRÃ‰
Copyright 2005  Didier LOISEAU
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

// Vérification de sécurité
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}

// on initialise la sortie:
header('Content-type:application/json');

if ($this->HasAccess("comment") && $this->page && !$this->page['comment_on'])
{
	// find number
	$sql = 'SELECT MAX(SUBSTRING(tag, 8) + 0) AS comment_id'
		. ' FROM ' . $this->GetConfigValue('table_prefix') . 'pages'
		. ' WHERE comment_on != ""';
	if ($lastComment = $this->LoadSingle($sql))
	{
		$num = $lastComment['comment_id'] + 1;
	}
	else
	{
		$num = "1";
	}

	$body = trim(utf8_decode($_POST["body"]));
	if (!$body)
	{
		$this->SetMessage("Commentaire vide  -- pas de sauvegarde !");
	}
	else
	{
		// store new comment
		$wakkaname = "Comment".$num;
		$this->SavePage($wakkaname, $body, $this->tag);
		
		$commentpage = $this->LoadPage($wakkaname);
		$commentaire = "<a name=\"".$wakkaname."\"></a>\n" ;
		$commentaire .= "<div class=\"comment\">\n" ;
		if ($this->HasAccess('write', $wakkaname)
		 || $this->UserIsOwner($wakkaname)
		 || $this->UserIsAdmin($wakkaname))
		{
			$commentaire .= '<div class="commenteditlink">';
			if ($this->HasAccess('write', $wakkaname))
			{
				$commentaire .= '<a class="lien_edit_comment" href="'.$this->href('edit',$wakkaname).'">&Eacute;diter</a>';
			}
			if ($this->UserIsOwner($wakkaname)
			 || $this->UserIsAdmin())
			{
				$commentaire .= '<br />'.'<a class="lien_suppr_comment" href="'.$this->href('ajaxdeletepage',$wakkaname).'">Supprimer</a>';
			}
			$commentaire .= "</div>\n";
		}
	
		$commentaire .= "<div class=\"commenthtml\">\n".$this->Format($body)."\n"."</div>"."\n" ;
		$commentaire .= "<div class=\"commentinfo\">\nle ".$commentpage["time"]." par ".$this->Format($commentpage["user"])." \n</div>\n" ;
		$commentaire .=  "</div>\n" ;		
		$response = json_encode(array("html"=>$commentaire));
		if (isset($_GET['jsonp_callback']))  $response = $_GET['jsonp_callback']."(".$response.")";
		echo $response;
	}
}

?>
