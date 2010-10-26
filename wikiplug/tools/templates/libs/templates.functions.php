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

?>