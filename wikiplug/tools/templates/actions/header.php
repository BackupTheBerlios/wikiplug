<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
//=======Restes de wikini=================================================================================

 $user = $this->GetUser();

//On recupere la partie haut du template et on execute les actions wikini

$template_decoupe = explode("{WIKINI_PAGE}", file_get_contents('tools/templates/themes/'.$this->config['favorite_theme'].'/squelettes/'.$this->config['favorite_squelette']));
$template_header = $template_decoupe[0];
if ($act=preg_match_all ("/".'(\\{\\{)'.'(.*?)'.'(\\}\\})'."/is", $template_header, $matches)) {
	$i = 0; $j = 0;
	foreach($matches as $valeur) {
		foreach($valeur as $val) {
			if (isset($matches[2][$j]) && $matches[2][$j]!='') {
				$action= $matches[2][$j];
				
				// Si  inclusion de page dans le template : creation automatique				
				if (preg_match ('/^include.*/',$action)) {
					if (preg_match_all("/([a-zA-Z0-9]*)=\"(.*)\"/U", $action, $action_param)) {
						   for ($a = 0; $a < count($action_param[1]); $a++)  {
	                        		if ($action_param[1][$a]=="page") {
	                                	$action_param_page = $action_param[2][$a];
	                                	if (!$tempPage = $this->LoadPage($action_param_page)) {
	                                		$this->SavePage($action_param_page,"                    ");
	                        			}
	                        		}
	                        }
	                }
				}
				// Fin creation automatique
				
				$template_header=str_replace('{{'.$action.'}}', $this->Format('{{'.$action.'}}'), $template_header);
			}
			$j++;
		}
		$i++;
	}
}

echo $template_header;
?>