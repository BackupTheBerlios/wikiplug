<?php
/*vim: set expandtab tabstop=4 shiftwidth=4: */
// +------------------------------------------------------------------------------------------------------+
// | PHP version 4.1                                                                                      |
// +------------------------------------------------------------------------------------------------------+
// | Copyright (C) 2004 Tela Botanica (accueil@tela-botanica.org)                                         |
// +------------------------------------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or                                        |
// | modify it under the terms of the GNU Lesser General Public                                           |
// | License as published by the Free Software Foundation; either                                         |
// | version 2.1 of the License, or (at your option) any later version.                                   |
// |                                                                                                      |
// | This library is distributed in the hope that it will be useful,                                      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of                                       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU                                    |
// | Lesser General Public License for more details.                                                      |
// |                                                                                                      |
// | You should have received a copy of the GNU Lesser General Public                                     |
// | License along with this library; if not, write to the Free Software                                  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                            |
// +------------------------------------------------------------------------------------------------------+
// CVS : $Id: bazar.fonct.cal.php,v 1.1 2008/07/07 18:00:39 mrflos Exp $
/**
*
* Fonctions calendrier du module bazar
*
*@package bazar
//Auteur original :
*@author        David Delon <david.delon@clapas.net>
//Autres auteurs :
*@copyright     Tela-Botanica 2000-2004
*@version       $Revision: 1.1 $ $Date: 2008/07/07 18:00:39 $
// +------------------------------------------------------------------------------------------------------+
*/

// +------------------------------------------------------------------------------------------------------+
// |                                            ENTETE du PROGRAMME                                       |
// +------------------------------------------------------------------------------------------------------+

require_once PAP_CHEMIN_RACINE.'Calendar/Month/Weekdays.php';
require_once PAP_CHEMIN_RACINE.'Calendar/Day.php';
require_once PAP_CHEMIN_RACINE.'Calendar/Decorator.php'; 

// +------------------------------------------------------------------------------------------------------+
// |                                           LISTE de FONCTIONS                                         |
// +------------------------------------------------------------------------------------------------------+

// Classe Utilitaire pour Calendrier
class DiaryEvent extends Calendar_Decorator {
	var $entry = array();
	function DiaryEvent($calendar)
	{
		Calendar_Decorator::Calendar_Decorator($calendar);
	}
	function setEntry($entry)
	{
		$this->entry[] = $entry;
		
	}
	function getEntry()
	{
		return $this->entry;
	}
} 


// $type : calendrier
// $type : calendrier_appplette
function GestionAffichageCalendrier($arguments = array(), $type = 'calendrier') {
	$script = '';
	// recuperation des arguments de l applette
	$balise = isset ($arguments[0]) ? $arguments[0] : '';
    $tab_arguments = $arguments;
	if (is_string ($tab_arguments)) {
		$tab_arguments = substr($tab_arguments, 1);
	} else {
		unset($tab_arguments[0]);	
	}
	if (is_array($tab_arguments)) {
	    foreach($tab_arguments as $argument) {
	    	if ($argument != '') {
		    	$tab_parametres = explode('=', $argument, 2);
		    	if (is_array($tab_parametres)) {
		    		$options[$tab_parametres[0]] = 
		    		(isset($tab_parametres[1])? trim($tab_parametres[1], '"') : '') ;		    		
		    	}
	    	}
	    }
	}
    
    if (!isset($options['template'])) {
		$options['template'] = BAZ_CHEMIN_SQUELETTE.BAZ_SQUELETTE_DEFAUT;
	} else {
		if (file_exists(BAZ_CHEMIN_SQUELETTE.$options['template'])) {
			$options['template'] = BAZ_CHEMIN_SQUELETTE.$options['template'];
		}
	}
    
	$retour = '';

	$url = $GLOBALS['_BAZAR_']['url'] ;
	$db =& $GLOBALS['_BAZAR_']['db'] ;
//	$auth =& $GLOBALS['_GEN_commun']['pear_auth'] ;
	
	// Nettoyage de l'url de la query string
	$chaine_url = $url->getQueryString();
	$tab_params = explode('&amp;', $chaine_url);
	if (count($tab_params) == 0) {
		$tab_params = explode('&', $chaine_url);
	}
	foreach ($tab_params as $param) {
		$tab_parametre = explode('=', $param);
		if ($tab_parametre[0]!='wiki') $url->removeQueryString($tab_parametre[0]);
	}
	
	if (!isset($_GET['y'])) { 
		$_GET['y'] = date('Y');
	}
	
	if (!isset($_GET['m'])) { 
		$_GET['m'] = date('m');
	}
	
	// 	Construction Mois en Cours 
	$month = new Calendar_Month_Weekdays($_GET['y'],$_GET['m']);

	$curStamp = $month->getTimeStamp();
	$url->addQueryString('y', date('Y',$curStamp));
	$url->addQueryString('m', date('n',$curStamp));
	$url->addQueryString('d', date('j',$curStamp));
	$cur = $url->getUrl();

	// Gestion de l'affichage des titres des évènements
	if (isset($_GET['ctt']) && $_GET['ctt'] == '1') {
		$url->addQueryString('tt', '0');
		if (isset($_GET['tt']) && $_GET['tt'] == '0') {
			$url->addQueryString('tt', '1');
		}
		$tc_lien = $url->getUrl();
	} else {
		$url->addQueryString('tt', '0');
		if (isset($_GET['tt']) && $_GET['tt'] == '0') {
			$url->addQueryString('tt', '1');
		}
		$url->addQueryString('ctt', '1');
		$tc_lien = $url->getUrl();
	}
	$url->removeQueryString('ctt');
	$url->removeQueryString('tt');
	$tc_txt = 'Afficher les titres complets des évènements';
	if (isset($_GET['tt']) && $_GET['tt'] == '0') {
		$tc_txt = 'Tronquer les titres des évènements';
		$url->addQueryString('tt', $_GET['tt']);
	}
	
	// Navigation 
	$prevStamp = $month->prevMonth(true);
	$url->addQueryString('y', date('Y',$prevStamp));
	$url->addQueryString('m', date('n',$prevStamp));
	$url->addQueryString('d', date('j',$prevStamp));
	$prev = $url->getUrl();
	
	$nextStamp = $month->nextMonth(true);
	$url->addQueryString('y', date('Y',$nextStamp));
	$url->addQueryString('m', date('n',$nextStamp));
	$url->addQueryString('d', date('j',$nextStamp));
	$next = $url->getUrl();
	
	// Suppression du paramêtre de troncage des titres
	$url->removeQueryString('tt');
	
	$fr_month = array(	"1"=>BAZ_JANVIER,"2"=>BAZ_FEVRIER,"3"=>BAZ_MARS,"4"=>BAZ_AVRIL,"5"=>BAZ_MAI,"6"=>BAZ_JUIN,
						"7"=>BAZ_JUILLET,"8"=>BAZ_AOUT,"9"=>BAZ_SEPTEMBRE,"10"=>BAZ_OCTOBRE,"11"=>BAZ_NOVEMBRE,"12"=>BAZ_DECEMBRE);

	// Titre
	if ($type == 'calendrier') {
		// Ajout des styles du bazar
		$retour .= '<div id="cal_entete">'."\n";
		$retour .= '<h1 id="cal_titre">'.BAZ_CALENDRIER.'</h1>'."\n";
		$retour .= '<span class="cal_navigation">'."\n";	
		$retour .= '<a id="cal_precedent_lien" href="'.$prev.'" title="Allez au mois précédent"><img id="cal_precedent_img" src="tools/bazar/actions/bazar/images/cal_precedent.png" alt="&lt;&lt;"/></a>'."\n"; 
		$retour .= '<a id="cal_mois_courrant" href="'.$cur.'">'."\n";
		$retour .= $fr_month[(date('n',$curStamp))]; 
		$retour .= '&nbsp;';
		$retour .= (date('Y',$curStamp));
		$retour .= '</a>'."\n";
		$retour .= '<a id="cal_suivant_lien" href="'.$next.'" title="Allez au mois suivant"><img id="cal_suivant_img" src="tools/bazar/actions/bazar/images/cal_suivant.png" alt="&gt;&gt;"/></a>'."\n";
		$retour .= '</span>'."\n";
		$retour .= '<span class="tc_lien">'.'<a href="'.$tc_lien.'">'.$tc_txt.'</a>'.'</span>'."\n";
		$retour .= '</div>'."\n";
		

	} else {
		// Appel du template
		ob_start();
		include $options['template'];
		$retour .= ob_get_contents();
		ob_end_clean();
	}
	// Vue Mois calendrier ou vue applette
		
	if ((!isset($_GET['id_fiche']) && $type == 'calendrier') || ($type == 'calendrier_applette')){
		// Recherche evenement de la periode selectionnée 
		$ts_jour_fin_mois = $month->nextMonth('timestamp');
		$ts_jour_debut_mois = $month->thisMonth('timestamp');; 
	    $requete_evenements = 	"SELECT DISTINCT bf_id_fiche, bf_titre, bf_lieu_evenement, DAY(bf_date_debut_evenement) AS bf_jour_debut_evenement, bf_date_debut_evenement, bf_date_fin_evenement, bf_description ".
								"FROM bazar_fiche, bazar_nature ".
								"WHERE bf_date_debut_evenement < '".date('Y-m-d', $ts_jour_fin_mois)."' ".
								"AND bf_date_fin_evenement >= '".date('Y-m-d', $ts_jour_debut_mois)."' ".
								"AND bf_ce_nature = bn_id_nature ".
						//		"AND bn_id_nature IN (".BAZ_NUM_ANNONCE_CALENDRIER.") ".
								"AND bf_statut_fiche = 1 ".
								"ORDER BY bf_jour_debut_evenement";
		
	   	$resultat_evenement = $db->query($requete_evenements);
	   	
	    (DB::isError($resultat_evenement)) ? trigger_error(BOG_afficherErreurSql(__FILE__, __LINE__, $resultat_evenement->getMessage(), $requete_evenements), E_USER_WARNING) : '';

		$selection = array();
		$evenements = array();
		$annee = date('Y', $curStamp);
		$mois = date('m', $curStamp);
		$tablo_jours = array();
	    while ($ligne_evenements = $resultat_evenement->fetchRow(DB_FETCHMODE_OBJECT)) {
			list($annee_debut, $mois_debut, $jour_debut) = explode('-', $ligne_evenements->bf_date_debut_evenement);
			list($annee_fin, $mois_fin, $jour_fin) = explode('-', $ligne_evenements->bf_date_fin_evenement);
			
			$Calendrier = new Calendar($annee_debut, $mois_debut, $jour_debut);
			$ts_jour_suivant = $Calendrier->thisDay('timestamp');
			$ts_jour_fin = mktime(0,0,0,$mois_fin, $jour_fin, $annee_fin);
			
	    	if ($ts_jour_suivant < $ts_jour_fin) {
				//echo "$ts_jour_suivant-";
				$naviguer = true;
				while ($naviguer) {
					// Si le jours suivant est inférieur à la date de fin, on continue...
					if ($ts_jour_suivant <= $ts_jour_fin) {
						// Si le jours suivant est inférieur à la date de fin du mois courrant, on continue...
						if ($ts_jour_suivant < $ts_jour_fin_mois) {
							$cle_j = date('Y-m-d', $ts_jour_suivant);
							if (!isset($tablo_jours[$cle_j])) {
								$tablo_jours[$cle_j]['Calendar_Day'] = new Calendar_Day(date('Y', $ts_jour_suivant),date('m', $ts_jour_suivant), date('d', $ts_jour_suivant));
								$tablo_jours[$cle_j]['Diary_Event'] = new DiaryEvent($tablo_jours[$cle_j]['Calendar_Day']);
							}
							$tablo_jours[$cle_j]['Diary_Event']->setEntry($ligne_evenements);
							
							$ts_jour_suivant = $Calendrier->nextDay('timestamp');
							//echo "ici$ts_jour_suivant-";
							$Calendrier->setTimestamp($ts_jour_suivant);
							//echo "la".$Calendrier->thisDay('timestamp')."-";
						} else {
							$naviguer = false;
						}
					} else {
						$naviguer = false;
					}
				}
	    	} else { 
				$curday_ymd = $annee.$mois.$ligne_evenements->bf_jour_debut_evenement;
				$cle_j = $annee.'-'.$mois.'-'.$ligne_evenements->bf_jour_debut_evenement;
				if (!isset($tablo_jours[$cle_j])) {
					$tablo_jours[$cle_j]['Calendar_Day'] = new Calendar_Day($annee, $mois, $ligne_evenements->bf_jour_debut_evenement);
					$tablo_jours[$cle_j]['Diary_Event'] = new DiaryEvent($tablo_jours[$cle_j]['Calendar_Day']);
				}
				$tablo_jours[$cle_j]['Diary_Event']->setEntry($ligne_evenements);
	    	}
		}
		// Add the decorator to the selection
		foreach ($tablo_jours as $jour) {
			$selection[] = $jour['Diary_Event'];				
		}
	
		// Affichage Calendrier
		$month->build($selection);
		if ($type == 'calendrier') {
			$retour.= '<table class="calendrier">'.
				'<colgroup>'.
					'<col class="cal_lundi"/>'.
					'<col class="cal_mardi"/>'.
					'<col class="cal_mercredi"/>'.
					'<col class="cal_jeudi"/>'.
					'<col class="cal_vendredi"/>'.
					'<col class="cal_samedi"/>'.
					'<col class="cal_dimanche"/>'.
				'</colgroup>'.
				'<thead>'.
			 	"<tr>
			
			  <th> ". BAZ_LUNDI ."</th>
			  <th> ". BAZ_MARDI ."</th>
			  <th> ". BAZ_MERCREDI ."</th>
			  <th> ". BAZ_JEUDI ."</th>
			  <th> ". BAZ_VENDREDI ."</th>
			  <th> ". BAZ_SAMEDI ."</th>
			  <th> ". BAZ_DIMANCHE ."</th>
			 </tr>
			 ".'</thead>'.'<tbody>';
		} else {
			$retour.= '<table class="calendrier_applette">'.
				'<colgroup>'.
					'<col class="cal_lundi"/>'.
					'<col class="cal_mardi"/>'.
					'<col class="cal_mercredi"/>'.
					'<col class="cal_jeudi"/>'.
					'<col class="cal_vendredi"/>'.
					'<col class="cal_samedi"/>'.
					'<col class="cal_dimanche"/>'.
				'</colgroup>'.
				'<thead>'.
			 "<tr>
			
			  <th> ". BAZ_LUNDI_COURT ."</th>
			  <th> ". BAZ_MARDI_COURT ."</th>
			  <th> ". BAZ_MERCREDI_COURT ."</th>
			  <th> ". BAZ_JEUDI_COURT ."</th>
			  <th> ". BAZ_VENDREDI_COURT ."</th>
			  <th> ". BAZ_SAMEDI_COURT ."</th>
			  <th> ". BAZ_DIMANCHE_COURT ."</th>
			 </tr>
			 ".'</thead>'.'<tbody>';
		}
		
		$todayStamp=time();
		$today_ymd=date('Ymd',$todayStamp);

		// Other month : mois 
		while ($day = $month->fetch() ) {
			$dayStamp = $day->thisDay(true);
			$day_ymd = date('Ymd',$dayStamp);
			if ( $day->isEmpty() ) {
				$class = "cal_ma other_month";
			} else {
				if (($day_ymd < $today_ymd)) {
					$class= "cal_mp previous_month";
				} else {
					 if ($day_ymd == $today_ymd) {
					 	$class= "cal_jc current_day";
					 } else {
						$class="cal_mc current_month";
					 }
				}
			}
			
			$url->addQueryString ('y', date('Y',$dayStamp));
			$url->addQueryString ('m', date('n',$dayStamp));
			$url->addQueryString ('d', date('j',$dayStamp));
			$link = $url->getUrl();
		
			// isFirst() to find start of week
			if ($day->isFirst()) {
				$retour.= ( "<tr>\n" );
			}
			if ($type == 'calendrier') {
				$retour.= "<td class=\"".$class."\">".'<span class="cal_j">'.$day->thisDay().'</span>'."\n";
				if ($day->isSelected() ) {
					$evenements = $day->getEntry();
					$evenements_nbre = count($evenements);
					$evenemt_xhtml = '';
					while ($ligne_evenement = array_pop($evenements)) {
						$id_fiches = array();
						$id_fiches[] = $ligne_evenement->bf_id_fiche;
						$url->addQueryString ('id_fiches',$id_fiches);
						$link = $url->getUrl();
						
						if (!isset($_GET['tt']) || (isset($_GET['tt']) && $_GET['tt'] == '1')) {
							$titre_taille = strlen($ligne_evenement->bf_titre);
							$titre = ($titre_taille > 20)?substr($ligne_evenement->bf_titre, 0, 20).'...':$ligne_evenement->bf_titre;
						} else {
							$titre = $ligne_evenement->bf_titre;
						}
						$evenemt_xhtml .= '<li class="tooltip" title="'.$ligne_evenement->bf_titre.'"><a class="cal_evenemt" href="'.$link.'">'.$titre.'</a></li>'."\n";
						$url->removeQueryString ('id_fiches');
					}
					if ($evenements_nbre > 0) {
						$retour .= '<ul class="cal_evenemt_liste">';
						$retour .= $evenemt_xhtml;
						$retour .= '</ul>';
					}
				}
			} else {
				$lien_date= "<td class=\"".$class."\">".$day->thisDay();
				if ($day->isSelected() ) {
					$evenements=$day->getEntry();
					$id_fiches=array();
					while ($ligne_evenement=array_pop($evenements)) {
						$id_fiches[]=$ligne_evenement->bf_id_fiche;
					}
					$url->addQueryString ('id_fiches',$id_fiches);
					$link = $url->getUrl();
					$lien_date= "<td class=\"".$class."\"><a href=\"".$link."\">".$day->thisDay()."</a>\n";
					$url->removeQueryString ('id_fiches');
				}
				$retour.=$lien_date;
			}
			$retour.= ( "</td>\n" );
			
			// isLast() to find end of week
			if ( $day->isLast() ) {
				$retour.= ( "</tr>\n" );
			}
		}
			$retour.= "</tbody></table>";
	}
	$retour.= '<script type="text/javascript">//<![CDATA['."\n".$script.'//]]></script>'."\n";
	// Vue detail
	
	if ((isset($_GET['id_fiches']))) {
		// Ajout d'un titre pour la page avec la date
		$jours = array ('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche') ;
		$mois = array ('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre',
						'octobre', 'novembre', 'décembre') ;
		$timestamp = strtotime ($_GET['y'].'/'.$_GET['m'].'/'.$_GET['d']) ;
		
		$retour = '<h1>'.$jours[date('w', $timestamp)].
						' '.$_GET['d'].' '.$mois[$_GET['m']-1].' '.$_GET['y'].'</h1>' ;
		$retour .= baz_voir_fiches(0,$_GET['id_fiches'] );
		
		// Un lien pour retourner au calendrier
		$url->removeQueryString('id_fiches');
		$url->removeQueryString('y');
		$url->removeQueryString('m');
		$url->removeQueryString('d');
		$retour .= '<div class="retour"><a href="'.$url->getURL().'">Retour au calendrier</a></div>';
	}

	// Nettoyage de l'url
	$url->removeQueryString('id_fiches');
	$url->removeQueryString('y');
	$url->removeQueryString('m');
	$url->removeQueryString('d');		
	return $retour;
}
?>
