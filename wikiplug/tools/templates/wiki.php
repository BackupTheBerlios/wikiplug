<?php

// Partie publique

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}



// Surcharge  fonction  LoadRecentlyChanged : suppression remplissage cache car affecte le rendu du template.
$wikiClasses [] = 'Template';
$wikiClassesContent [] = ' 

	function LoadRecentlyChanged($limit=50)
        {
                $limit= (int) $limit;
                if ($pages = $this->LoadAll("select id, tag, time, user, owner from ".$this->config["table_prefix"]."pages where latest = \'Y\' and comment_on =  \'\' order by time desc limit $limit"))
                {
                        return $pages;
                }
        }


	
';	


//on cherche l'action template dans la page, qui definit le graphisme a utiliser
if ($_POST["submit"] == html_entity_decode('Aper&ccedil;u')) {
	//$contenu["body"] = $_POST["body"].'{{template theme="'.$_POST["theme"].'" squelette="'.$_POST["squelette"].'" style="'.$_POST["style"].'"}}';
	$contenu["body"] = $_POST["body"].'{{template theme="'.$_POST["theme"].'" squelette="'.$_POST["squelette"].'" style="'.$_POST["style"].'"}}';
	
	$_POST["body"] = $_POST["body"].'{{template theme="'.$_POST["theme"].'" squelette="'.$_POST["squelette"].'" style="'.$_POST["style"].'"}}';
} 

else {
 $contenu=$wiki->LoadPage($page);
}



if ($act=preg_match_all ("/".'(\\{\\{template)'.'(.*?)'.'(\\}\\})'."/is", $contenu["body"], $matches)) {
     $i = 0; $j = 0;
     foreach($matches as $valeur) {
       foreach($valeur as $val) {
         if ($matches[2][$j]!='') {
           $action= $matches[2][$j];
           if (preg_match_all("/([a-zA-Z0-9]*)=\"(.*)\"/U", $action, $params))
			{
				for ($a = 0; $a < count($params[1]); $a++)
				{
					$vars[$params[1][$a]] = $params[2][$a];
				}
			}
         }
         $j++;
       }
       $i++;
     }
   }
   
// Dans Wakka.config.php preciser :
// favorite_theme
// favorite_style
// favorite_squelette

// Sinon ;
// Theme par defaut : default
// Css par defaut : default.css
// squelette par defaut : default.tpl.html
   

 
  

if (isset($vars["theme"]) && $vars["theme"]!="") {
	 define ('THEME_PAR_DEFAUT', $vars["theme"]); 
}
else {
	if (isset($wakkaConfig['favorite_theme'])) {
		define ('THEME_PAR_DEFAUT',$wakkaConfig['favorite_theme']);
	}
	else {
		define ('THEME_PAR_DEFAUT', 'default');
	}
}

if (isset($vars["style"]) && $vars["style"]!="") {
 	define ('CSS_PAR_DEFAUT', $vars["style"]);
}
else {
	if (isset($wakkaConfig['favorite_style'])) {
		define ('CSS_PAR_DEFAUT',$wakkaConfig['favorite_style']);
	}
	else {
		define ('CSS_PAR_DEFAUT', 'default.css');
	}
}


if  (isset($vars["squelette"]) && $vars["squelette"]!="") {
	define ('SQUELETTE_PAR_DEFAUT', $vars["squelette"]);
}
else {	
	if (isset($wakkaConfig['favorite_squelette'])) {
		define ('SQUELETTE_PAR_DEFAUT',$wakkaConfig['favorite_squelette']);
	} 
	else {
		define ('SQUELETTE_PAR_DEFAUT', 'default.tpl.html');
 		
	}
}

    
    

//on cherche tous les dossiers du repertoire themes et des sous dossier styles et squelettes, et on les range dans le tableau $wakkaConfig['templates']
    $repertoire = 'tools'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'themes';
    $dir = opendir($repertoire);
    while (false !== ($file = readdir($dir))) {    	
    	if  ($file!='.' && $file!='..' && $file!='CVS' && is_dir($repertoire.DIRECTORY_SEPARATOR.$file)) {
	    	$dir2 = opendir($repertoire.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'styles');
	    	while (false !== ($file2 = readdir($dir2))) {
	    		if (substr($file2, -4, 4)=='.css') $wakkaConfig['templates'][$file]["style"][$file2]=$file2;
	    	}
	    	closedir($dir2);
	    	ksort($wakkaConfig['templates'][$file]["style"]);
	    	$dir3 = opendir($repertoire.DIRECTORY_SEPARATOR.$file.DIRECTORY_SEPARATOR.'squelettes');
	    	while (false !== ($file3 = readdir($dir3))) {
	    		if (substr($file3, -9, 9)=='.tpl.html') $wakkaConfig['templates'][$file]["squelette"][$file3]=$file3;	    
	    	}	    	
	    	closedir($dir3);
	    	ksort($wakkaConfig['templates'][$file]["squelette"]);
    	}
    }
    closedir($dir);
    if (is_array($wakkaConfig)) ksort($wakkaConfig['templates']);

       
//=======Changer de theme=================================================================================================
    if (isset($_POST['theme'])  && array_key_exists($_POST['theme'], array_keys($wakkaConfig['templates']))) {
            $wakkaConfig['favorite_theme'] = $_POST['theme'];
    }
    else {
            $wakkaConfig['favorite_theme'] = THEME_PAR_DEFAUT;

    }

         
    

//=======Changer de style=====================================================================================================
    $styles['none']='pas de style';

    if (isset($_POST['style']) && array_key_exists($_POST['style'], $wakkaConfig['templates'][$wakkaConfig['favorite_theme']]['style'])) {
            $wakkaConfig['favorite_style'] = $_POST['style'];
    }
    else {
            $wakkaConfig['favorite_style'] = CSS_PAR_DEFAUT;
    }

//=======Changer de squelette=================================================================================================    
    if(isset($_POST['squelette']) && array_key_exists($_POST['squelette'], $wakkaConfig['templates'][$wakkaConfig['favorite_theme']]['squelette'])) {
            $wakkaConfig['favorite_squelette'] = $_POST['squelette'];
    }
    else {
            $wakkaConfig['favorite_squelette'] = SQUELETTE_PAR_DEFAUT;
    }

?>
