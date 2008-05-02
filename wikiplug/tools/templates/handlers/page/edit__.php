<?php
/*
*/
if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


if ($this->HasAccess("write") && $this->HasAccess("read")) {
	// Edition
	if ($_POST["submit"] != 'Aper�u' && $_POST["submit"] != 'Sauver') {
		$selecteur = 'Th&egrave;me: <select name="theme" onchange="changeVal(this.value)">'."\n";
	    foreach(array_keys($this->config['templates']) as $key => $value) {
	            if($value !== $this->config['favorite_theme']) {
	                    $selecteur .= '<option value="'.$value.'">'.$value.'</option>'."\n";
	            }
	            else {
	                    $selecteur .= '<option value="'.$value.'" selected="selected">'.$value.'</option>'."\n";
	            }
	    }
	    $selecteur .= '</select>'."\n";
		
		$selecteur .= 'Squelette: <select name="squelette">'."\n";
	    foreach($this->config['templates'][$this->config['favorite_theme']]['squelette'] as $key => $value) {
	            if($value !== $this->config['favorite_squelette']) {
	                    $selecteur .= '<option value="'.$key.'">'.$value.'</option>'."\n";
	            }
	            else {
	                    $selecteur .= '<option value="'.$this->config['favorite_squelette'].'" selected="selected">'.$value.'</option>'."\n";
	            }
	    }
	    $selecteur .= '</select>'."\n";
	
		$selecteur .= 'Style: <select name="style">'."\n";
	    foreach($this->config['templates'][$this->config['favorite_theme']]['style'] as $key => $value) {
	            if($value !== $this->config['favorite_style']) {
	                    $selecteur .= '<option value="'.$key.'">'.$value.'</option>'."\n";
	            }
	            else {	            		
	                    $selecteur .= '<option value="'.$this->config['favorite_style'].'" selected="selected">'.$value.'</option>'."\n";
	            }
	    }
	    $selecteur .= '</select>'."\n".'<br />'."\n";
	    //on enleve l'action template
		$plugin_output_new=preg_replace ("/".'(\\{\\{template)'.'(.*?)'.'(\\}\\})'."/is", '', $plugin_output_new);
		//on ajoute la s�lection des styles
		$plugin_output_new=preg_replace ('/\<input name=\"submit\" type=\"submit\" value=\"Sauver\"/',
		$selecteur.'<input name="submit" type="submit" value="Sauver"', $plugin_output_new);
		
		//AJOUT DU JAVASCRIPT QUI PERMET DE CHANGER DYNAMIQUEMENT DE TEMPLATES			
		$javascript = '<script>
		var tab1 = new Array();
		var tab2 = new Array();'."\n";
		foreach(array_keys($this->config['templates']) as $key => $value) {
	            $javascript .= '		tab1["'.$value.'"] = new Array(';
	            $nbocc=0;	           
	            foreach($this->config['templates'][$value]["squelette"] as $key2 => $value2) {
	            	if ($nbocc==0) $javascript .= '\''.$value2.'\'';
	            	else $javascript .= ',\''.$value2.'\'';
	            	$nbocc++;
	            }
	            $javascript .= ');'."\n";
	            
	            $javascript .= '		tab2["'.$value.'"] = new Array(';
	            $nbocc=0;
	            foreach($this->config['templates'][$value]["style"] as $key3 => $value3) {
	            	if ($nbocc==0) $javascript .= '\''.$value3.'\'';
	            	else $javascript .= ',\''.$value3.'\'';
	            	$nbocc++;
	            }
	            $javascript .= ');'."\n";	      
	    }
				
		$javascript .= '		function changeVal(val){
			
			// pour vider la liste
			document.ACEditor.squelette.options.length=0
			for (var i=0; i<tab1[val].length; i++){
				
				  o=new Option(tab1[val][i],tab1[val][i]);
				 document.ACEditor.squelette.options[document.ACEditor.squelette.options.length]=o;
				
							
			}
			document.ACEditor.style.options.length=0
			for (var i=0; i<tab2[val].length; i++){
				  o=new Option(tab2[val][i],tab2[val][i]);
				 document.ACEditor.style.options[document.ACEditor.style.options.length]=o;
							
			}					
		}
			document.ACEditor.theme.options.selectedIndex = "'.$this->config['favorite_theme'].'";
			document.ACEditor.squelette.options.selectedIndex = "'.$this->config['favorite_squelette'].'";
			document.ACEditor.style.options.selectedIndex = "'.$this->config['favorite_style'].'";
		</script>';
		$plugin_output_new=preg_replace ('/\<div class="page">/',
		$javascript.'<div class="page">',
		$plugin_output_new);
	}
}

?>
