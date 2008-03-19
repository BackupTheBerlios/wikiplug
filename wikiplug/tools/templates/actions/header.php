<?php
//=======Restes de wikini=====================================================================================================
    $message = $this->GetMessage();
    $user = $this->GetUser();

//On r�cupere le template et on execute les actions wikini
$template = file_get_contents('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes/'.$this->config['favorite_squelette'].'.tpl.html');
$template_decoupe = explode("{WIKINI_PAGE}", $template);
   if ($act=preg_match_all ("/".'(\\{\\{)'.'(.*?)'.'(\\}\\})'."/is", $template_decoupe[0], $matches)) {
     $i = 0; $j = 0;
     foreach($matches as $valeur) {
       foreach($valeur as $val) {
         if ($matches[2][$j]!='') {
           $action= $matches[2][$j];
           $template=str_replace('{{'.$action.'}}', $this->Format('{{'.$action.'}}'), $template_decoupe[0]);
         }
         $j++;
       }
       $i++;
     }
   }


//on utilise la bibliotheque pear template it pour gerer les variables dans la template
require_once 'tools/templates/api/IT.php';
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

//feuilles de styles
$wikini_styles_css = '';
if ($this->config['favorite_style']!='none') $wikini_styles_css .= '<link rel="stylesheet" type="text/css" href="tools/templates/themes/'.$this->config['favorite_theme'].'/styles/'.$this->config['favorite_style'].'" media="screen" title="'.$this->config['favorite_style'].'" />'."\n";
foreach($this->config['styles'] as $key => $value) {
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
  if (substr($file, -3, 3)=='.js') $wikini_javascripts .= '<script type="text/javascript" src="'.$repertoire.'/'.$file.'"></script>'."\n";
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
$wikini_double_clic = "ondblclick=\"document.location='".$this->href("edit")."';\" ";
$tpl->setVariable('WIKINI_DOUBLE_CLIC', $wikini_double_clic);

$plugin_output_new= $tpl->show();