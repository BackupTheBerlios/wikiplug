<?php
//=======Restes de wikini=====================================================================================================
    $message = $this->GetMessage();
    $user = $this->GetUser();

//On recupere le template et on execute les actions wikini
$template_decoupe = explode("{WIKINI_PAGE}", file_get_contents('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes/'.$this->config['favorite_squelette']));
$template = $template_decoupe[0];
   if ($act=preg_match_all ("/".'(\\{\\{)'.'(.*?)'.'(\\}\\})'."/is", $template, $matches)) {
     $i = 0; $j = 0;
     foreach($matches as $valeur) {
       foreach($valeur as $val) {
         if ($matches[2][$j]!='') {
           $action= $matches[2][$j];
           $template=str_replace('{{'.$action.'}}', $this->Format('{{'.$action.'}}'), $template);
         }
         $j++;
       }
       $i++;
     }
   }


//on utilise la bibliotheque pear template it pour gerer les variables dans la template
require_once 'tools/templates/libs/IT.php';
$tpl = new HTML_Template_IT('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes');
$tpl->setTemplate($template, true, true);

//on assigne les valeurs aux variables du template

//titre de la page
$wikini_titre=$this->GetWakkaName().":".$this->GetPageTag();
$tpl->setVariable('WIKINI_TITRE', $wikini_titre);

// meta robots
if ($this->GetMethod() != 'show')  $wikini_meta_robots="<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";
if (isset($wikini_meta_robots)) $tpl->setVariable('WIKINI_META_ROBOTS', $wikini_meta_robots);

//mots cles
$wikini_mots_cles = $this->config['meta_keywords'];
$tpl->setVariable('WIKINI_MOTS_CLES', $wikini_mots_cles);

//description
$wikini_description = $this->config['meta_description'];
$tpl->setVariable('WIKINI_DESCRIPTION', $wikini_description);

//flux rss
$wikini_flux_rss = '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="'.$this->config['base_url'].'DerniersChangementsRSS/xml" />'."\n";
$tpl->setVariable('WIKINI_FLUX_RSS', $wikini_flux_rss);


//feuilles de styles
$wikini_styles_css = '';
if ($this->config['favorite_style']!='none') $wikini_styles_css .= '<link rel="stylesheet" type="text/css" href="tools/templates/themes/'.$this->config['favorite_theme'].'/styles/'.$this->config['favorite_style'].'" media="screen" title="'.$this->config['favorite_style'].'" />'."\n";
foreach($this->config['templates'][$this->config['favorite_theme']]['style'] as $key => $value) {
  if($key !== $this->config['favorite_style'] && $key !== 'none') {
    $wikini_styles_css .= '<link rel="alternate stylesheet" type="text/css" href="tools/templates/themes/'.$this->config['favorite_theme'].'/styles/'.$key.'" media="screen" title="'.$value.'" />'."\n";
  }
}
$tpl->setVariable('WIKINI_STYLES_CSS', $wikini_styles_css);

//javascripts
$wikini_javascripts = '';
$repertoire = 'tools/templates/themes/'.$this->config['favorite_theme'].'/javascripts';
$dir = opendir($repertoire);
while (false !== ($file = readdir($dir))) {
  if (substr($file, -3, 3)=='.js') $scripts[] = '<script type="text/javascript" src="'.$repertoire.'/'.$file.'"></script>'."\n";
}
asort($scripts);
foreach ($scripts as $key => $val) {
    $wikini_javascripts .= "$val\n";
}
closedir($dir);
if ($wikini_javascripts!='') $tpl->setVariable('WIKINI_JAVASCRIPTS', $wikini_javascripts);


//attributs du body
$wikini_body = $message ? "onLoad=\"alert('".$message."');\" " : "";
$tpl->setVariable('WIKINI_BODY', $wikini_body);

//nom du site
$wikini_titre_site = $this->config["wakka_name"] ;
$tpl->setVariable('WIKINI_TITRE_SITE', $wikini_titre_site);

//remise a zero des styles css
$wikini_resetstyle .= $this->href().'/resetstyle';
$tpl->setVariable('WIKINI_RESETSTYLE', $wikini_resetstyle);

//javascript du double clic
//if ($this->GetMethod() != 'edit') {
//	$wikini_double_clic = "ondblclick=\"document.location='".$this->href("edit")."';\" ";
//	$tpl->setVariable('WIKINI_DOUBLE_CLIC', $wikini_double_clic);
//}
//else $tpl->setVariable('WIKINI_DOUBLE_CLIC', '');

$plugin_output_new = $tpl->show();