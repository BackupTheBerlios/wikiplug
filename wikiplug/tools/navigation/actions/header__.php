<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


if (version_compare(phpversion(), '5.0') < 0) {
    eval('
    if (!function_exists("clone")) {
        function clone($object) {
                return $object;
        }
    }
    ');
}


$menu_page=$this->config["menu_page"];
$banniere_page=$this->config["banniere_page"];
if (isset($menu_page) and ($menu_page!=""))
    {
    // Ajout Menu de Navigation
    echo '<table class="page_table">';
    echo '<tr><td class="menu_column">';
    $wikiMenu = clone($this);
    $wikiMenu->tag=$menu_page;


    $wikiMenu->SetPage($wikiMenu->LoadPage($wikiMenu->tag));
    echo $wikiMenu->Format($wikiMenu->page["body"], "wakka");
    echo '</td>';
    echo '<td class="body_column">';
 }
 
if (isset($banniere_page) and ($banniere_page!=""))
    {
    // Ajout Banniere de Navigation
    echo '<div id="banniere">';
    $wikiBanniere = clone($this);
    $wikiBanniere->tag=$banniere_page;


    $wikiBanniere->SetPage($wikiBanniere->LoadPage($wikiBanniere->tag));
    echo $wikiBanniere->Format($wikiBanniere->page["body"], "wakka");
    echo '</div>';
}


 
   	$plugin_output_new=preg_replace ('/<head>/',
	'
	<head>
	<style type="text/css">
	.page_table {margin: 0px; padding: 0px ; border: none; height: 100%;width: 100%;} 
	.menu_column {background-color: #FFFFCC; vertical-align: top; width: 150px; border: 1px solid #000000;padding:5px;}
	.body_column {vertical-align: top; border: none;padding:5px;}
	 #banniere {background-color: #FFFFCC; height: 104px;}
	</style>
	',
	$plugin_output_new);
?>
