<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
//javascripts
$wikini_javascripts = '';

if (is_dir('themes/'.$this->config['favorite_theme'].'/javascripts')) {
	$repertoire = 'themes/'.$this->config['favorite_theme'].'/javascripts';
} else {
	$repertoire = 'tools/templates/themes/'.$this->config['favorite_theme'].'/javascripts';
}

$dir = opendir($repertoire);
while (false !== ($file = readdir($dir))) {
  if (substr($file, -3, 3)=='.js') $scripts[] = '<script type="text/javascript" src="'.$repertoire.'/'.$file.'"></script>';
}
asort($scripts);
foreach ($scripts as $key => $val) {
    $wikini_javascripts .= "\n$val";
}
closedir($dir);

echo $wikini_javascripts;
?>