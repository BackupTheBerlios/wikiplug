<?php
/**
* bazarcalendrier : programme affichant les evenements du bazar sous forme de Calendrier dans wikini
*
*
*@package Bazar
//Auteur original :
*@author        David DELON <david.delon@clapas.net>
*@author        Florian SCHMITT <florian.schmitt@laposte.net>
*@version       $Revision: 1.2 $ $Date: 2008/08/27 13:18:57 $
// +------------------------------------------------------------------------------------------------------+
*/
//récupération des paramêtres wikini
$categorie_nature = $this->GetParameter("categorienature");
if (!empty($categorie_nature)) {
	$GLOBALS['_BAZAR_']['categorie_nature']=$categorie_nature;
}
//si rien n'est donne, on affiche toutes les categories
else {
	$GLOBALS['_BAZAR_']['categorie_nature']='toutes';
}
$id_typeannonce = $this->GetParameter("idtypeannonce");
if (!empty($id_typeannonce)) {
	$GLOBALS['_BAZAR_']['id_typeannonce']=$id_typeannonce;
}
//si rien n'est donne, on affiche toutes les annonces
else {
	$GLOBALS['_BAZAR_']['id_typeannonce']='toutes';
}

//fichier des fonctions calendrier de Bazar
include_once BAZ_CHEMIN.'actions'.DIRECTORY_SEPARATOR.'bazar'.DIRECTORY_SEPARATOR.'bibliotheque'.DIRECTORY_SEPARATOR.'bazar.fonct.cal.php'; 

echo GestionAffichageCalendrier('calendrier');	
?>

