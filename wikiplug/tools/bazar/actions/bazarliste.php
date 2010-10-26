<?php
/**
* bazarliste : programme affichant les fiches du bazar sous forme de liste accordeon
*
*
*@package Bazar
//Auteur original :
*@author        Florian SCHMITT <florian@outils-reseaux.org>
*@version       $Revision: 1.5 $ $Date: 2010/03/04 14:19:03 $
// +------------------------------------------------------------------------------------------------------+
*/


// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+


if (!defined("WIKINI_VERSION")) {
        die ("acc&egrave;s direct interdit");
}

//récupération des paramètres wikini

$categorie_nature = $this->GetParameter("categorienature");
if (empty($categorie_nature)) {	
	$categorie_nature = 'toutes';
}

$id_typeannonce = $this->GetParameter("idtypeannonce");
if (empty($id_typeannonce)) {
	$id_typeannonce = 'toutes';
}

$ordre = $this->GetParameter("ordre");
if (empty($ordre)) {
	$ordre = 'alphabetique';
}

$template = $this->GetParameter("template");
if (empty($template)) {
	$template = 'liste_accordeon.tpl.html';
}

//on récupère les paramètres pour une requête spécifique
$query = $this->GetParameter("query");
if (!empty($query)) {
	$tabquery = array();
	$tableau = array();
	$tab = explode('|', $query);
	foreach ($tab as $req)
	{
		$tabdecoup = explode('=', $req, 2);
		$tableau[$tabdecoup[0]] = trim($tabdecoup[1]);
	}
	$tabquery = array_merge($tabquery, $tableau);
}
else
{
	$tabquery = '';
}
$tableau_resultat = baz_requete_recherche_fiches($tabquery, $ordre, $id_typeannonce, $categorie_nature);

$fiches['fiches'] = array(); $i=0; 
foreach ($tableau_resultat as $fiche) {
	$tmp = array();
	$i++;
	$tmp['titre'] = stripslashes($fiche[3]);
	$tmp['valeurs_fiche'] = baz_valeurs_fiche($fiche[0]);
	$tmp['contenu'] = baz_voir_fiche(0, $tmp['valeurs_fiche']);

	$GLOBALS['_BAZAR_']['url']->addQueryString('id_fiche', $fiche[0]);
	if (baz_a_le_droit('saisir_fiche', $fiche[2])) {
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_SUPPRESSION);
		$tmp['lien_suppression'] = '<a class="BAZ_lien_supprimer" href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"  onclick="javascript:return confirm(\''.BAZ_CONFIRM_SUPPRIMER_FICHE.' ?\');"></a>'."\n";
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_SAISIR);
		$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_ACTION_MODIFIER);
		$tmp['lien_edition'] = '<a class="BAZ_lien_modifier" href="'.str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()).'"></a>'."\n";
	}
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_VOIR, BAZ_VOIR_CONSULTER);
	$GLOBALS['_BAZAR_']['url']->addQueryString(BAZ_VARIABLE_ACTION, BAZ_VOIR_FICHE);
	$tmp['lien_voir_titre'] = '<a class="BAZ_lien_voir" href="'. str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()) .'" title="Voir la fiche">'. stripslashes($fiche[3]).'</a>'."\n";
	$tmp['lien_voir'] = '<a class="BAZ_lien_voir" href="'. str_replace('&','&amp;',$GLOBALS['_BAZAR_']['url']->getURL()) .'" title="Voir la fiche"></a>'."\n";
	$fiches['fiches'][] = $tmp;

	//r�initialisation de l'url
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_VOIR);
	$GLOBALS['_BAZAR_']['url']->removeQueryString(BAZ_VARIABLE_ACTION);
}
include_once('tools/bazar/libs/squelettephp.class.php');
$squelcomment = new SquelettePhp('tools/bazar/presentation/squelettes/'.$template);
$squelcomment->set($fiches);
echo $squelcomment->analyser();



//on ajoute le javascript de l'accordeon, s'il y a des résultats
//if ($res!='') {
//	echo '<div class="accordion">'.$res.'</div>';
//}

?>
