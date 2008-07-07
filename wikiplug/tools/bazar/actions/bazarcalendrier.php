<?php
/**
* bazarcalendrier : programme affichant les evenements du bazar sous forme de Calendrier dans wikini
*
*
*@package Bazar
//Auteur original :
*@author        David DELON <david.delon@clapas.net>
*@author        Florian SCHMITT <florian.schmitt@laposte.net>
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
// +------------------------------------------------------------------------------------------------------+
*/
define ('GEN_CHEMIN_API', str_replace('wakka.php', '', $_SERVER["SCRIPT_FILENAME"]).'tools'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR);
define ('PAP_CHEMIN_API_PEAR', GEN_CHEMIN_API);
define ('PAP_CHEMIN_RACINE', '');
define ('GEN_SEP', DIRECTORY_SEPARATOR);
define ('PAP_CHEMIN_API_PEARDB', PAP_CHEMIN_API_PEAR);
set_include_path(PAP_CHEMIN_API_PEAR.PATH_SEPARATOR.get_include_path());
require_once 'DB.php' ;
include_once 'Net'.DIRECTORY_SEPARATOR.'URL.php' ;
include_once 'bazar/configuration/baz_config.inc.php'; //fichier de configuration de Bazar
include_once 'bazar/bibliotheque/bazar.fonct.php'; //fichier des fonctions de Bazar
include_once 'bazar/bibliotheque/bazar.fonct.cal.php'; //fichier des fonctions de Bazar

	
echo GestionAffichageCalendrier('calendrier');	
?>

