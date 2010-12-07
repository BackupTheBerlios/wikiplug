<?php
if (!defined("WIKINI_VERSION")) {
            die ("acc&egrave;s direct interdit");
}


//remplace juste la premiere occurence d'une chaine de caracteres
function str_replace_once($from, $to, $str) {
    if(!$newStr = strstr($str, $from)) {
        return $str;
    }
    $iNewStrLength = strlen($newStr);
    $iFirstPartlength = strlen($str) - $iNewStrLength;
    return substr($str, 0, $iFirstPartlength).$to.substr($newStr, strlen($from), $iNewStrLength);
} 

// str_ireplace est php5 seulement 
if (!function_exists('str_ireplacement')) { 
  function str_ireplacement($search,$replace,$subject){
    $token = chr(1);
    $haystack = strtolower($subject);
    $needle = strtolower($search);
    while (($pos=strpos($haystack,$needle))!==FALSE){
      $subject = substr_replace($subject,$token,$pos,strlen($search));
      $haystack = substr_replace($haystack,$token,$pos,strlen($search));
    }
    $subject = str_replace($token,$replace,$subject);
    return $subject;
  }
}

//fonction recursive pour detecter un nomwiki deja present 
function nomwikidouble($nomwiki, $nomswiki) {
	if (in_array($nomwiki, $nomswiki)) {
		return nomwikidouble($nomwiki.'bis', $nomswiki);
	} else {
		return $nomwiki;
	}
}

//fonction pour remplacer les liens vers les NomWikis n'existant pas
function replace_missingpage_links($output) {	
	$pattern = '/<span class="missingpage">(.*)<\/span><a href="'.str_replace(array('/','?'), array('\/','\?'), 
				$GLOBALS['wiki']->config['base_url']).'(.*)\/edit">\?<\/a>/U';
	preg_match_all($pattern, $output, $matches, PREG_SET_ORDER);

	foreach ($matches as $values) {
		// on passe en parametres GET les valeurs du template de la page de provenance, pour avoir le même graphisme dans la page créée
		$query_string = 'theme='.urlencode($GLOBALS['wiki']->config['favorite_theme']).
						'&amp;squelette='.urlencode($GLOBALS['wiki']->config['favorite_squelette']).
						'&amp;style='.urlencode($GLOBALS['wiki']->config['favorite_style']).
						((!$GLOBALS['wiki']->IsWikiName($values[1])) ? '&amp;body='.urlencode($values[1]) : '');
		$replacement = '<a class="yeswiki-editable" href="'.$GLOBALS['wiki']->href("edit", $values[2], $query_string).'">'.
						$values[1].'&nbsp;<img src="tools/templates/presentation/images/crayon.png" alt="crayon" /></a>';
		$output = str_replace_once( $values[0], $replacement, $output );
	}
	return $output;
}

/**
 * 
 * crée un diaporama à partir d'une PageWiki
 * 
 * @param $pagetag : nom de la PageWiki
 */
function print_diaporama($pagetag) {
	// On teste si l'utilisateur peut lire la page
	if (!$GLOBALS['wiki']->HasAccess("read", $pagetag))
	{
		return '<div class="error_box">Vous n\'avez pas le droit d\'acc&eacute;der &agrave; cette page.</div>'. $GLOBALS['wiki']->Format('{{login template="minimal.tpl.html"}}');
	}
	else
	{
		// On teste si la page existe
		if (!$page = $GLOBALS['wiki']->LoadPage($pagetag))
		{
			return '<div class="error_box">Page '.$pagetag.' non existante.</div>';
		}
		else
		{
			$body_f = $GLOBALS['wiki']->format($page["body"]);
			$body = preg_split('/(.*<h2>.*<\/h2>)/',$body_f,-1,PREG_SPLIT_DELIM_CAPTURE);      
	
			if (!$body)
			{
				return '<div class="=error_box">La page '.$pagetag.' ne peut pas &ecirc;tre d&eacute;coup&eacute;e en diapositives.</div>';
			}
			else
			{			
				// Affiche le corps de la page
				$output = "";
	
				// -- Affichage du contenu -------------------------
				$titre = "";
				$i = 0;
				foreach($body as $slide)
				{
					//pour les titres de niveau 2, on les transforme en titre 1
					if (preg_match('/^<h2>.*<\/h2>/', $slide)) 
					{
						$i++;
						$titre[$i] = str_replace('h2', 'h1', $slide);
					}
					//sinon, on affiche
					else 
					{
						//s'il y a un titre de niveau 1 qui commence la diapositive, on la déplace en titre (sert surtout pour la première page)
						if (preg_match('/^<h1>.*<\/h1>/', $slide)) 
						{
							$split = preg_split('/(.*<h1>.*<\/h1>)/',$slide, -1, PREG_SPLIT_DELIM_CAPTURE);
							$titre[$i] = $split[1];
							$slide = $split[2];
						}
						$output .= "<div class=\"slide\">\n";
						if ($titre[$i] != "") { $output .= "<div class=\"slide-header\">".$titre[$i]."</div>\n"; }
						$output .= $slide."</div>\n";
					}
				}
			}
		}
		
		//le html pour le diaporama
		$output = "<div id=\"slide_show_container_".$pagetag."\" class=\"slide_show_container\">
					<div id=\"slide_show_".$pagetag."\" class=\"slide_show\">
						<div id=\"slides_".$pagetag."\" class=\"slides\">
							$output
						</div>
					</div>\n";
		
		//boutons pour naviguer en bas du slide
		$output .= '<div class="slide-navigation">
						<button class="slide-button custom prev">&laquo; Pr&eacute;c&eacute;dent</button>
						<div id="thumbs_'.$pagetag.'" class="t">
							<div class="navi">'."\n";
		foreach ($titre as $key => $title) {
			if ($key==0) {
				$output .= '<a class="button-begin" title="'.strip_tags($title).'" href="#slide'.$key.'" id="t'.$key.'">D&eacute;but</a>';
			} else {
				$output .= '<a title="'.strip_tags($title).'" href="#slide'.$key.'" id="t'.$key.'"></a>';
			}
		}					
		$output .= '</div>'."\n".'</div>'."\n".
				   '<button class="slide-button custom next">Suivant &raquo;</button>'."\n";
		if ($GLOBALS['wiki']->GetMethod() == "diaporama") {
			$output .= '<div class="buttons-action"><a class="button-edit" href="'.$GLOBALS['wiki']->href('edit',$pagetag).'">&Eacute;diter</a>'."\n";
			$output .= '<a class="button-quit" href="'.$GLOBALS['wiki']->href('',$pagetag).'">Quitter</a></div>'."\n";
		}
		$output .= '</div>'."\n".'</div>'."\n";
			
		//on prépare le javascript du diaporama, qui sera ajoutée par l'action footer de template, à la fin du html
		$GLOBALS['js'] = ((isset($GLOBALS['js'])) ? $GLOBALS['js'] : '').'<script> 
			$("#slide_show_'.$pagetag.'").scrollable({mousewheel:true}).navigator({history: true}).data("scrollable");
			$("#thumbs_'.$pagetag.' .navi a[title]").tooltip({position:	\'bottom center\', opacity:0.9, tipClass:\'tooltip-slideshow\', offset:[5, 0]});
			</script>'."\n";
		return $output;
	}
}

?>