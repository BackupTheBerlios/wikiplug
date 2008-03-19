<?php

// Partie publique

if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
define ('CSS_PAR_DEFAUT', 'mandarine.css');
define ('SQUELETTE_PAR_DEFAUT', 'mandarine');
define ('THEME_PAR_DEFAUT', 'mandarine');

//=======Changer de theme=================================================================================================
    //on cherche tous les dossiers du repertoire themes et on les range dans le tableau $wakkaConfig['themes']
    $repertoire = 'tools/templates/themes';
    $dir = opendir($repertoire);
    while (false !== ($file = readdir($dir))) {
    	if  ($file!='.' && $file!='..') $wakkaConfig['themes'][$file]=$file;
    }
    closedir($dir);

    if (isset($_POST['theme']) && array_key_exists($_POST['theme'], $wakkaConfig['themes'])) {
            $wakkaConfig['favorite_theme'] = $_POST['theme'];
    }
    elseif (isset($_COOKIE['favorite_theme'])) {
            $wakkaConfig['favorite_theme'] = $_COOKIE['favorite_theme'];
    }
    else {
            $wakkaConfig['favorite_theme'] = THEME_PAR_DEFAUT;

    }
    setcookie('favorite_theme', $wakkaConfig['favorite_theme'], time() + 63115200);

//=======Changer de style=====================================================================================================
    //on cherche tous les fichiers avec l'extension .css du répertoire des styles et on les range dans le tableau $wakkaConfig['styles']
    $repertoire = 'tools/templates/themes/'.$wakkaConfig['favorite_theme'].'/styles';
    $dir = opendir($repertoire);
    $styles['none']='pas de style';
    while (false !== ($file = readdir($dir))) {
      if (substr($file, -4, 4)=='.css') $wakkaConfig['styles'][$file]=$file;
    }
    closedir($dir);

    if (isset($_POST['style']) && array_key_exists($_POST['style'], $wakkaConfig['styles'])) {
            $wakkaConfig['favorite_style'] = $_POST['style'];
    }
    elseif (isset($_COOKIE['favorite_style'])) {
            $wakkaConfig['favorite_style'] = $_COOKIE['favorite_style'];
    }
    else {
            $wakkaConfig['favorite_style'] = CSS_PAR_DEFAUT;
    }
    setcookie('favorite_style', $wakkaConfig['favorite_style'], time() + 63115200);

//=======Changer de squelette=================================================================================================
    //on cherche tous les fichiers avec l'extension .html du répertoire des squelettes et on les range dans $wakkaConfig['squelettes']
    $repertoire = 'tools/templates/themes/'.$wakkaConfig['favorite_theme'].'/squelettes';
    $dir = opendir($repertoire);
    while (false !== ($file = readdir($dir))) {
    $nom_extension_squelette=substr($file, -9, 9);
    $nom_squelette=str_replace($nom_extension_squelette, '', $file);
      if ($nom_extension_squelette=='.tpl.html') $wakkaConfig['squelettes'][$nom_squelette]=$file;
    }
    closedir($dir);

    if(isset($_POST['squelette']) && array_key_exists($_POST['squelette'], $wakkaConfig['squelettes'])) {
            $wakkaConfig['favorite_squelette'] = $_POST['squelette'];
    }
    elseif(isset($_COOKIE['favorite_squelette'])) {
            $wakkaConfig['favorite_squelette'] = $_COOKIE['favorite_squelette'];
    }
    else {
            $wakkaConfig['favorite_squelette'] = SQUELETTE_PAR_DEFAUT;
    }
    setcookie('favorite_squelette', $wakkaConfig['favorite_squelette'], time() + 63115200);
?>
