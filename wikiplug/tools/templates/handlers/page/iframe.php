<?php
/*
*/

// Vérification de sécurité
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >

<head>
        <title> '.$this->Format("{{titrepage}}").'</title>
        '.$this->Format("{{metarobots}}").'
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
        <meta name="keywords" content="'.$this->Format("{{configuration param=\"meta_keywords\"}}").' />
        <meta name="description" content="'.$this->Format("{{configuration param=\"meta_description\"}}").' />
        <link rel="alternate" type="application/rss+xml" title="Flux RSS des derniers changements" href="'.$this->Format("{{configuration param=\"base_url\"}}").'DerniersChangementsRSS/xml" />
        '.$this->Format("{{liensstyle}}").'
        '.$this->Format("{{liensjavascripts}}").'
</head>

<body>';

// Generate page before displaying the header, so that it might interract with the header
ob_start();

echo '<div class="page"';
echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : ' ondblclick="doubleClickEdit(event);"';
echo '>'."\n";
if (!empty($_SESSION['redirects']))
{
	$trace = $_SESSION['redirects'];
	$tag = $trace[count($trace) - 1];
	$prevpage = $this->LoadPage($tag);
	echo '<div class="redirectfrom"><em>(Redirig&eacute; depuis ', $this->Link($prevpage['tag'], 'edit'), ")</em></div>\n";
}

if ($HasAccessRead=$this->HasAccess("read"))
{
	if (!$this->page)
	{
		echo "Cette page n'existe pas encore, voulez vous la <a href=\"".$this->href("edit")."\">cr&eacute;er</a> ?" ;
	}
	else
	{
		// comment header?
		if ($this->page["comment_on"])
		{
			echo "<div class=\"commentinfo\">Ceci est un commentaire sur ",$this->ComposeLinkToPage($this->page["comment_on"], "", "", 0),", post&eacute; par ",$this->Format($this->page["user"])," &agrave; ",$this->page["time"],"</div>";
		}

		if ($this->page["latest"] == "N")
		{
			echo "<div class=\"revisioninfo\">Ceci est une version archiv&eacute;e de <a href=\"",$this->href(),"\">",$this->GetPageTag(),"</a> &agrave; ",$this->page["time"],".</div>";
		}


		// display page
		$this->RegisterInclusion($this->GetPageTag());
		echo $this->Format($this->page["body"], "wakka");
		$this->UnregisterLastInclusion();

		// if this is an old revision, display some buttons
		if (($this->page["latest"] == "N") && $this->HasAccess("write"))
		{
			$latest = $this->LoadPage($this->tag);
			?>
			<br />
			<?php echo  $this->FormOpen("edit") ?>
			<input type="hidden" name="previous" value="<?php echo  $latest["id"] ?>" />
			<input type="hidden" name="body" value="<?php echo  htmlspecialchars($this->page["body"]) ?>" />
			<input type="submit" value="R&eacute;&eacute;diter cette version archiv&eacute;e" />
			<?php echo  $this->FormClose(); ?>
			<?php
		}
	}
}
else
{
	echo "<i>Vous n'&ecirc;tes pas autoris&eacute; &agrave; lire cette page</i>" ;
}
//l'expression reguliere pour que tous les liens s'ouvrent dans une nouvelle fenetre
$pattern = '/<a href="(.*)>(.*)<\/a>/U';
echo preg_replace($pattern,"<a href=\"\\1 onclick=\"window.open(this.href);return false\">\\2</a>", ob_get_clean());
echo '</div>'."\n";
echo '</body>
</html>';
?>