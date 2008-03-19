<?php
// Partie publique 

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

// Surcharge methode GetUserName de la class Wiki

$wikiClasses [] = 'Player';
$wikiClassesContent [] = ' 

	function Link($tag, $method = "", $text = "", $track = 1) {
		
			
			if ($text!=$tag and preg_match("/.mp3$/i",$tag))
			{
				$mp3ret = "<object type=\"application/x-shockwave-flash\" data=\"tools/player/dewplayer.swf?son=$tag&amp;bgcolor=FFFFFF\" width=\"200\"
 height=\"20\"><param name=\"movie\" value=\"tools/player/dewplayer.swf?son=$tag&amp;bgcolor=FFFFFF\"/></object>";
 				$mp3ret .= "<br></br>";
 				$mp3ret .= "[<a href=\"$tag\">mp3</a>]";
				return $mp3ret;
				
			}
			else
			{
				return Wiki::Link($tag, $method, $text, $track);
			}
	}
';
?>