<?php
/*
*/

// V�rification de s�curit�
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}

$html = "<div class=\"page\">"."\n";
$html .= "<h2>Widget : int&eacute;grer le contenu de cette page ailleurs</h2>"."\n";
$html .= '<div class="BAZ_info">Copier-collez le code ci-dessous dans n\'importe quelle page HTML pour int&eacute;grer le contenu de cette page.</div>'."\n";
$html .= "<div style=\"font-family: 'Courier New'; border:1px solid #ddd; text-align:left; background:#fff; padding:5px; margin:10px 0; display:block; \">\n";
$html .= htmlentities('<iframe class="yeswiki_frame" width="500" height="300" frameborder="0" src="'.$this->Href('iframe').'"></iframe>')."\n";
$html .= '</div>'."\n";
$html .= "<br /><h2>Partager sur les r&eacute;seaux sociaux</h2>"."\n";
$html .= '<a href="http://www.facebook.com/sharer.php?u='.urlencode($this->Href()).'&amp;t='.urlencode($this->GetPageTag()).'" title="Partager sur Facebook" class="bouton_share"><img src="tools/templates/presentation/images/facebook.png" width="64" height="64" alt="Facebook" /></a>'."\n";
$html .= '<a href="http://twitter.com/home?status='.urlencode('A lire : '.$this->Href()).'" title="Partager sur Twitter" class="bouton_share"><img src="tools/templates/presentation/images/twitter.png" width="64" height="64" alt="Twitter" /></a>'."\n";
$html .= '<a href="http://www.netvibes.com/share?title='.urlencode($this->GetPageTag()).'&amp;url='.urlencode($this->Href()).'" title="Partager sur Netvibes" class="bouton_share"><img src="tools/templates/presentation/images/netvibes.png" width="64" height="64" alt="Netvibes" /></a>'."\n";
$html .= '<a href="http://del.icio.us/post?url='.urlencode($this->Href()).'&amp;title='.urlencode($this->GetPageTag()).'" title="Partager sur Delicious" class="bouton_share"><img src="tools/templates/presentation/images/delicious.png" width="64" height="64" alt="Delicious" /></a>'."\n";
$html .= '<a href="http://www.google.com/reader/link?title='.urlencode($this->GetPageTag()).'&amp;url='.urlencode($this->Href()).'" title="Partager sur Google" class="bouton_share"><img src="tools/templates/presentation/images/google.png" width="64" height="64" alt="Google" /></a>'."\n";
$html .= "<br /><br />"."\n";
$html .= "</div>"."\n";

echo utf8_encode($html);
?>