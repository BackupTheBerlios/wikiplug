<?php
/*
listetag.php

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

// recuperation de tous les tags
$tags = $this->GetParameter('tags');
if (empty($tags))
{
	die ('<span class="error">Action listepages : param&egrave;tre tags obligatoire.</span>');
}

$notags = $this->GetParameter('notags');
$lienedit = $this->GetParameter('edit');
$class = $this->GetParameter('class');
if (empty($class)) $class = 'liste';
$tri = $this->GetParameter('tri');
$nb = $this->GetParameter('nb');
$accordeon = $this->GetParameter('accordeon');

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
require_once 'tools/tags/lib/MDB2.php';
$dsn = array(
    'phptype'  => 'mysql',
    'username' => $this->config["mysql_user"],
    'password' => $this->config["mysql_password"],
    'hostspec' => $this->config["mysql_host"],
    'database' => $this->config["mysql_database"],
);

// create MDB2 instance
$db =& MDB2::connect($dsn);

if (!empty($nb))
{
	require_once 'tools/tags/lib/Pager/Pager_Wrapper.php'; //this file
	$pagerOptions = array(
    	'mode'    => 'Sliding',
   	 	'delta'   => 2,
    	'perPage' => $nb,
 	);
	$paged_data = Pager_Wrapper_MDB2($db, $requete, $pagerOptions);
	//$paged_data['page_numbers']; //array('current', 'total');
} else
{
	$paged_data['data'] = $db->queryAll($requete, null, MDB2_FETCHMODE_ASSOC);
}

foreach ($paged_data['data'] as $microblogpost) {
    if ( $this->tag!=$microblogpost['tag'] )
		{
			$text = '';
			if (!empty($accordeon)) $text .= '""<a class="lien_accordeon" href="#">'.$microblogpost['tag'].'</a>""'."\n";
			$text .= '{{include page="'.$microblogpost['tag'].'"';
			if (!empty($lienedit))
			{
				$text .= ' edit="'.$lienedit.'"';
			}
			$text .= ' class="'.$class.'"}}';
			$textformatted = $this->Format($text);
			
			// affichage des commentaires
					
			// load comments for this page
	        $comments = $this->LoadComments($microblogpost['tag']);
			$commentaire = "<div class=\"".$class."info\">\n le ".$microblogpost["time"]." par ".$this->Format($microblogpost["user"])."
							<div class=\"lienpage\">(page <a href=\"".$this->href('',$microblogpost['tag'])."\">".$microblogpost['tag']."</a>)</div>\n</div>\n" ;
			$commentaire .= "<div class=\"commentaires\">\n";
			
			// display comments themselves
			if ($comments)
			{
				foreach ($comments as $comment)
				{					
					$commentaire .= "<a name=\"".$comment["tag"]."\"></a>\n" ;
					$commentaire .= "<div class=\"comment\">\n" ;
					if ($this->HasAccess('write', $comment['tag'])
					 || $this->UserIsOwner($comment['tag'])
					 || $this->UserIsAdmin($comment['tag']))
					{
						$commentaire .= '<div class="commenteditlink">';
						if ($this->HasAccess('write', $comment['tag']))
						{
							$commentaire .= '<a class="lien_edit_comment" href="'.$this->href('edit',$comment['tag']).'">&Eacute;diter</a>';
						}
						if ($this->UserIsOwner($comment['tag'])
						 || $this->UserIsAdmin())
						{
							$commentaire .= '<br />'.'<a class="lien_suppr_comment" href="'.$this->href('ajaxdeletepage',$comment['tag']).'">Supprimer</a>';
						}
						$commentaire .= "</div>\n";
					}

					$commentaire .= "<div class=\"commenthtml\">\n".$this->Format($comment["body"])."\n"."</div>"."\n" ;
					$commentaire .= "<div class=\"commentinfo\">\nle ".$comment["time"]." par ".$this->Format($comment["user"])." \n</div>\n" ;
					$commentaire .=  "</div>\n" ;					
				}
			}
			
			$commentaire .= "</div>\n";

			
			// display comments header
			$commentaire .= '<a href="javascript:void(0);" class="lien_commenter">Commenter</a>'."\n";
			
			// display comment form
			$commentaire .= "<div class=\"microblogcommentform\">\n" ;
			if ($this->HasAccess("comment"))
			{
				$commentaire .= $this->FormOpen("addcomment", $microblogpost['tag']).'
					<textarea name="body" class="commentaire_microblog"></textarea><br />
					<input type="button" class="bouton_microblog" value="Ajouter Commentaire" accesskey="s" />'.$this->FormClose();
			}
			$commentaire .= "</div>\n</div>\n" ;
			echo substr_replace($textformatted, $commentaire, -7, -1);
		}
}

//javascript accordeon
if (!empty($accordeon)) echo '
<script type="text/javascript">
    <!--
    $(document).ready( function () {
        // On cache les pages inclues
        $("div.include").hide();          
        // On modifie l\'evenement "click" sur les liens vers la page
        $("a.lien_accordeon").click( function () {
            // Si le div etait deja ouvert, on le referme :
            $("div.include:visible").slideUp("fast");
            
            // Si le div est cache, on ferme les autres et on l\'affiche :            
            $(this).next().next("div.include").slideDown("fast");
            
            // On empêche le navigateur de suivre le lien :
            return false;
        });
    
    } ) ;
    // -->
    </script>

';

//show the links
if (!empty($nb)) echo "\n".'<div class="liste_pager">'."\n".$paged_data['links']."\n".'</div>'."\n";


?>