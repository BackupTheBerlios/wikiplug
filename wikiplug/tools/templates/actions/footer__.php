<?php
//On recupere le template et on execute les actions wikini
$template = file_get_contents('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes/'.$this->config['favorite_squelette'].'.tpl.html');
$template_decoupe = explode("{WIKINI_PAGE}", $template);
   if ($act=preg_match_all ("/".'(\\{\\{)'.'(.*?)'.'(\\}\\})'."/is", $template_decoupe[1], $matches)) {
     $i = 0; $j = 0;
     foreach($matches as $valeur) {
       foreach($valeur as $val) {
         if ($matches[2][$j]!='') {
           $action= $matches[2][$j];
           $template=str_replace('{{'.$action.'}}', $this->Format('{{'.$action.'}}'), $template_decoupe[1]);
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


$plugin_output_after= $tpl->show();
?>