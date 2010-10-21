<?php
if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

if (!$class) $plugin_output_new = '<div class="include">'."\n".$plugin_output_new."\n".'</div>' ;

if (!empty($dblclic) && $dblclic=="1" && $this->HasAccess("write", $incPageName)) {
	$actiondblclic = ' ondblclick="document.location=\''.$this->Href("edit", $incPageName).'\';"';
}
else $actiondblclic = '';

//remplace juste la premiere occurence d'une chaine de caracteres
if (!function_exists('str_replace_once')) 
{
	function str_replace_once($from, $to, $str) {
	    if(!$newStr = strstr($str, $from)) {
	        return $str;
	    }
	    $iNewStrLength = strlen($newStr);
	    $iFirstPartlength = strlen($str) - $iNewStrLength;
	    return substr($str, 0, $iFirstPartlength).$to.substr($newStr, strlen($from), $iNewStrLength);
	}
} 

if (!function_exists('str_ireplacement')) { // str_ireplace est php5 seulement 
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
if (!function_exists('nomwikidouble')) 
{
	function nomwikidouble($nomwiki, $nomswiki) 
	{
		if (in_array($nomwiki, $nomswiki)) 
		{
			return nomwikidouble($nomwiki.'bis', $nomswiki);
		} else
		{
			return $nomwiki;
		}
	}
}

if (isset($this->config['hide_action_template']) && !$this->config['hide_action_template']) 
{ 		
	$pattern = '/<span class="missingpage">(.*)<\/span><a href="'.str_replace(array('/','?'), array('\/','\?'),$this->config['base_url']).'(.*)\/edit">\?<\/a>/U';
	preg_match_all($pattern, $plugin_output_new, $matches, PREG_SET_ORDER);
	$nomswiki = array();

	foreach ($matches as $values) 
	{
		$valuedep=$values[2];
		$values[2] = nomwikidouble($values[2], $nomswiki); 
		$nomswiki[] = $values[2];		
		$replacement = '<div class="missingpage">'.$values[1].'<form class="form_include" name="'.$pageincluded.'editform'.$values[2].'" action="'.$this->href("edit",$valuedep).'" method="post" style="display:inline;margin-left:-5px;">
		<a href="javascript:document.'.$pageincluded.'editform'.$values[2].'.submit();" title="Editer cette nouvelle page Wikini">?</a>';
		
		//si le lien de provenance n'est pas un NomWiki, on l'utilise comme titre de la nouvelle page
		if (!$this->IsWikiName($values[1])) {
			$replacement .= '<input type="hidden" name="body" value="======'.$values[1].'======" />';
		}
		
		//on cache les valeurs du template de provenance, pour avoir le meme graphisme dans la page creee
		$replacement .= '<input type="hidden" name="theme" value="'.$this->config['favorite_theme'].'" />		
		<input type="hidden" name="squelette" value="'.$this->config['favorite_squelette'].'" />
		<input type="hidden" name="style" value="'.$this->config['favorite_style'].'" />
		</form></div>'."\n";
		$plugin_output_new = str_replace_once( $values[0], $replacement, $plugin_output_new );
	}
	
}

if (!empty($clear) && $clear=='non') $texteclear='';
else $texteclear = '<div style="clear:both;display:block;"></div>'."\n";

if (!$incPage = $this->LoadPage($incPageName))
{
	$plugin_output_new = '<a style="background:transparent url(tools/templates/presentation/images/crayon.png) no-repeat left center;padding-left:12px;" href="'.$this->href('edit', $incPageName).'">Editer '.$incPageName.'</a>';
} 

//si le lien correspond � l'url, on rajoute une classe "actif"
if (!empty($actif)&&$actif=="1") {
    $plugin_output_new=str_ireplacement('<a href="'.$this->config["base_url"].$this->tag.'"','<a class="actif" href="'.$this->config["base_url"].$this->tag.'"', $plugin_output_new);
}

//rajoute le javascript pour le double clic
$plugin_output_new = str_replace('<div class="include', '<div'.$actiondblclic.' class="include div_include', $plugin_output_new);
$plugin_output_new = str_replace('include_', '', $plugin_output_new);
$plugin_output_new =  (!empty($clear) && $clear=='yes') ? $plugin_output_new.'<div style="clear:both;display:block;"></div>'."\n" : $plugin_output_new;


?>
