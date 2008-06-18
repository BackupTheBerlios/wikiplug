<?php

// Partie publique

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}


//on cherche l'action template dans la page, qui definit le graphisme a utiliser
$nomwiki = $_REQUEST['wiki'];

// remove leading slash
$nomwiki = preg_replace("/^\//", "", $nomwiki);

// split into page/method, checking wiki name & method name (XSS proof)
if (!defined("WN_CHAR")) define("WN_CHAR", "[A-Za-z0-9]"); // \xC0-\xD6\xD8-\xF6\xF8-\xFF]");
if (!defined("WN_CHAR2")) define("WN_CHAR2", "[A-Za-z0-9_-]"); 
if (!defined("WN_PAGE_TAG")) define('WN_PAGE_TAG', WN_CHAR . '+');
if (!defined("WN_TAG_HANDLER_CAPTURE")) define('WN_TAG_HANDLER_CAPTURE', '(' . WN_PAGE_TAG . ')/(' . WN_CHAR2 . '*)');
if (preg_match('`^' . WN_TAG_HANDLER_CAPTURE . '$`', $nomwiki, $matches))
{
	list(, $page, $method) = $matches;
}
elseif (preg_match('`^' . WN_PAGE_TAG . '$`', $nomwiki))
{
	$page = $nomwiki;
}
else
{
	echo "<p>Le nom de la page est incorrect.</p>";
	exit;
}

$contenu=$wiki->LoadPage($page);
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
isset($vars["theme"]) ? define ('THEME_PAR_DEFAUT', $vars["theme"]) : define ('THEME_PAR_DEFAUT', 'default');
isset($vars["style"]) ? define ('CSS_PAR_DEFAUT', $vars["style"]) : define ('CSS_PAR_DEFAUT', 'default.css');
isset($vars["squelette"]) ? define ('SQUELETTE_PAR_DEFAUT', $vars["squelette"]) : define ('SQUELETTE_PAR_DEFAUT', 'default.tpl.html');

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
    if (isset($_POST['theme']) && array_key_exists($_POST['theme'], array_keys($wakkaConfig['templates']))) {
            $wakkaConfig['favorite_theme'] = $_POST['theme'];
    }
    else {
            $wakkaConfig['favorite_theme'] = THEME_PAR_DEFAUT;

    }

//=======Changer de style=====================================================================================================
    $styles['none']='pas de style';

    if (isset($_POST['style']) && array_key_exists($_POST['style'], $wakkaConfig['templates'][$wakkaConfig['favorite_theme']]['styles'])) {
            $wakkaConfig['favorite_style'] = $_POST['style'];
    }
    else {
            $wakkaConfig['favorite_style'] = CSS_PAR_DEFAUT;
    }

//=======Changer de squelette=================================================================================================    
    if(isset($_POST['squelette']) && array_key_exists($_POST['squelette'], $wakkaConfig['templates'][$wakkaConfig['favorite_theme']]['squelettes'])) {
            $wakkaConfig['favorite_squelette'] = $_POST['squelette'];
    }
    else {
            $wakkaConfig['favorite_squelette'] = SQUELETTE_PAR_DEFAUT;
    }
?>
