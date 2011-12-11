<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

//feuilles de styles de base yeswiki
$wikini_styles_css = '  <link rel="stylesheet" href="tools/templates/presentation/styles/yeswiki-base.css">';

if (file_exists('themes/'.$this->config['favorite_theme'].'/styles/'.$this->config['favorite_style'])) {
	$css_file = 'themes/'.$this->config['favorite_theme'].'/styles/'.$this->config['favorite_style'];
} else {
	$css_file = 'tools/templates/themes/'.$this->config['favorite_theme'].'/styles/'.$this->config['favorite_style'];
}

if ($this->config['favorite_style']!='none') $wikini_styles_css .= '<link rel="stylesheet" type="text/css" href="'.$css_file.'" media="screen" title="'.$this->config['favorite_style'].'" />';
 	
echo $wikini_styles_css;
?>
