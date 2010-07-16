<?php 
//Retourne le timestamp du d�but du mois du timestamp renseign�
function getMonthStartTS($in_timeStamp) { 
	return mktime( 0, 1, 1, date("m", $in_timeStamp), 1, 
		date("Y", $in_timeStamp)); 					
}

//Retourne le timestamp de la fin du mois du timestamp renseign�
function getMonthEndTS($in_timeStamp) { 
	return mktime( 23,	59,	59, date("m", $in_timeStamp)+1, 1,
		date("Y", $in_timeStamp)); 
}

//Retourne le timestamp du d�but de la semaine du timestamp renseign�
function getWeekStartTS($in_timeStamp) { 
	
}

//Retourne le timestamp de la fin de la semaine mois du timestamp renseign�
function getWeekEndTS($in_timeStamp) { 
	
}

//Retourne le timestamp du d�but du jour du timestamp renseign�
function getDayStartTS($in_timeStamp) { 

}

//Retourne le timestamp de la fin du jour du timestamp renseign�
function getDayEndTS($in_timeStamp) { 

}
 /***************************************************************************
 * Elimine les evenement en dehors de l'intervalle pr�cis�
 * et range ceux restant par ordre chronologique.
 */
function filterEvents($in_startTS, $in_endTS, $in_data) {
	$selectedData = array();
	//Filtre les �venements
	foreach($in_data as $event) {
		//TODO : prendre en compte les �venement sur plusieurs jours/mois/ann�es/mill�naires
		//       decouper les �l�ments au jour le jour.
		if (($event["DTSTART"]["unixtime"] <= $in_endTS) 
			&& ($event["DTEND"]["unixtime"] >= $in_startTS)) {
				array_push($selectedData, $event);		
		/*if (($event["DTSTART"]["unixtime"] >= $in_startTS) 
			&& ($event["DTSTART"]["unixtime"] <= $in_endTS)) {
			array_push($selectedData, $event);*/
		}
	}/**/
	
	//Range les �venements par ordre chronologique
	/*$size = count($selectedData);
	do {
		$changement = false;
		for($i=1;$i<$size;$i++) { 
			if ($selectedData[$i]["DTSTART"]["unixtime"] 
				< $selectedData[$i-1]["DTSTART"]["unixtime"]) {
				$tampon =  $selectedData[$i-1];
				$selectedData[$i-1] = $selectedData[$i];
				$selectedData[$i] = $tampon;
				$changement = true;
			}		
		}
	} while($changement); /*On continue tant qu'il y a des changements.*/
	
	return $selectedData;	
}

/****************************************************************************
 * Cr�e le squelette de donn�e du calendrier (Vue mensuelle).
 */
function makeMonth($in_timestamp, $in_data)
{
	$startMonthTS = mktime( 0, 0, 0, date("m", $in_timestamp), 1, date("Y", $in_timestamp));
	$firstDay = date("w",$startMonthTS); //0 --> Dimanche...6--> Samedi
	if ($firstDay == 0) 
		$firstDay = 7;
	$firstDay--; //<-- premier jour de la semaine = Lundi

	$nb_jours = date("t", mktime( 0, 1, 1, date("m", $in_timestamp)+1, 0, date("Y", $in_timestamp)));
	
	
	
	$month = array();
	//Les jours vide de d�but de mois
	for($i=0 ; $i<$firstDay ; $i++){
		$day = array("isBlank" => true, "isToday" => false, "isEvent" => false, "startDayTS" => mktime(0,0,0,0,0,0), "endDayTS" => mktime(0,0,0,0,0,0), "events" => array() );
		array_push($month, $day);
	}
	//ajouter les jours
	for($i=0 ; $i<$nb_jours ; $i++){
		$isToday = false;
		$isEvent = false;
		$startDayTS = mktime(0, 0, 0, date("m", $startMonthTS)  , date("d", $startMonthTS)+$i, date("Y", $startMonthTS));
		$endDayTS = mktime(23, 59, 59, date("m", $startMonthTS)  , date("d", $startMonthTS)+$i, date("Y", $startMonthTS));
		
		if ((time() >= $startDayTS ) && (time() <= $endDayTS ))
			$isToday = true;
		
		$events = array();
		foreach ($in_data as $event){
			if (($event["DTSTART"]["unixtime"] <= $endDayTS) && ($event["DTEND"]["unixtime"] >= $startDayTS)) {
				$event["SUMMARY"] = htmlentities(utf8_decode($event["SUMMARY"]));
				if ($event["DTEND"]["unixtime"] != $startDayTS){
					array_push($events, $event);
					$isEvent = true;
				}
			}
				
		}
		array_push($month, array("isBlank" => false, "isToday" => $isToday, "isEvent" => $isEvent, "startDayTS" => $startDayTS, "endDayTS" => $endDayTS, "events" => $events));
		
	}
	//Les jours de fin de mois.
	if (($nb_jours+$firstDay) % 7 != 0) {
		for($i=0 ; $i < 7 - (($nb_jours+$firstDay) % 7) ; $i++){
			$day = array("isBlank" => true, "isToday" => false, "isEvent" => false, "startDayTS" => mktime(0,0,0,0,0,0), "endDayTS" => mktime(0,0,0,0,0,0), "events" => array() );
			array_push($month, $day);
		}
	}
	
	return $month;
}


/****************************************************************************
 * Affichage du calendrier (vue mois)
 */

function printMonthCal($in_timeStamp, $in_data, $params) {

	if (isset($params['color'])) $in_color = $params['color']; else $in_color = 'grey';
	if (isset($params['url'])) $url = $params['url']; else die('ERREUR action cal : param&ecirc;tre "url" obligatoire!');
	
	print("<div class='calendar' style='background-color: ".$in_color.";'>\n");
	print("<div class='calendar_content'>\n");

	$jourencours = date("j", $in_timeStamp);
	$moisencours = date("n", $in_timeStamp);
	$anneeencours = date("Y", $in_timeStamp);
	$next_month = strtotime('+1 month',$in_timeStamp);
	$prev_month = strtotime('-1 month',$in_timeStamp);
	$url_params = "&amp;url=".urlencode($url)."&amp;color=".urlencode($in_color);
	
	$monthText = "<select name=\"mois\" class=\"select_mois\">\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 1, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 1 ? ' selected="selected"':'').'>Janvier</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 2, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 2 ? ' selected="selected"':'').'>F&eacute;vrier</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 3, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 3 ? ' selected="selected"':'').'>Mars</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 4, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 4 ? ' selected="selected"':'').'>Avril</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 5, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 5 ? ' selected="selected"':'').'>Mai</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 6, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 6 ? ' selected="selected"':'').'>Juin</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 7, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 7 ? ' selected="selected"':'').'>Juillet</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 8, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 8 ? ' selected="selected"':'').'>Aout</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 9, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 9 ? ' selected="selected"':'').'>Septembre</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 10, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 10 ? ' selected="selected"':'').'>Octobre</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 11, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 11 ? ' selected="selected"':'').'>Novembre</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, 12, $jourencours, $anneeencours).$url_params.'"'.($moisencours == 12 ? ' selected="selected"':'').'>D&eacute;cembre</option>'."\n";
	$monthText .= "</select>\n";
	
	$monthText .= "<select name=\"annee\" class=\"select_annee\">\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours-4).$url_params.'">'.($anneeencours-4).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours-3).$url_params.'">'.($anneeencours-3).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours-2).$url_params.'">'.($anneeencours-2).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours-1).$url_params.'">'.($anneeencours-1).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours).$url_params.'" selected="selected">'.$anneeencours.'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours+1).$url_params.'">'.($anneeencours+1).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours+2).$url_params.'">'.($anneeencours+2).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours+3).$url_params.'">'.($anneeencours+3).'</option>'."\n";
	$monthText .= '<option value="tools/wikical/actions/cal.php?timestamp='.mktime(0, 0, 0, $moisencours, $jourencours, $anneeencours+4).$url_params.'">'.($anneeencours+4).'</option>'."\n";
	$monthText .= "</select>\n";
	
	print("<p class='title'>\n"
		.$monthText."\n
		<a href=\"tools/wikical/actions/cal.php?timestamp=".$prev_month.$url_params."\" class=\"cal_prev prev_month\" title=\"Mois pr&eacute;c&eacute;dent\"><</a>
		<a href=\"tools/wikical/actions/cal.php?timestamp=".time().$url_params."\" class=\"cal_now today\" title=\"Aujourd'hui\">o</a>
		<a href=\"tools/wikical/actions/cal.php?timestamp=".$next_month.$url_params."\" class=\"cal_next next_month\" title=\"Mois suivant\">></a></p>\n");
	print("<div class='day_name'>Lun</div>\n");
	print("<div class='day_name'>Mar</div>\n");
	print("<div class='day_name'>Mer</div>\n");
	print("<div class='day_name'>Jeu</div>\n");
	print("<div class='day_name'>Ven</div>\n");
	print("<div class='day_name'>Sam</div>\n");
	print("<div class='day_name'>Dim</div>\n");

	foreach($in_data as $day) {
		//Creation du DIV
		if ($day["isToday"])
			print("<div class='day today'>");
		else if ($day["isEvent"])
			print("<div class='day evday'>");
		else
			print("<div class='day'>");
		//Contenu du DIV
		if(!$day["isBlank"])
			print(date("d",$day['startDayTS']));
		//affichage des events
		if ($day["isEvent"]) {
			print ("<div id='events'>");
			foreach($day["events"] as $event) {
				print("<p class='event_title'>".$event["SUMMARY"]."</p>");
				//TODO : Gerer toutes les infos
				//       Ajouter une boucle. 
				
				if ( ($event["DTEND"]["unixtime"] - $event["DTSTART"]["unixtime"]) > 86400) {
					print("<p class='event_info'>Du "
						.date("d/m/Y", $event["DTSTART"]["unixtime"])
						." &agrave; ".date("G:i", $event["DTSTART"]["unixtime"])
						." au ".date("d/m/Y", $event["DTEND"]["unixtime"] )
						." &agrave; ".date("G:i", $event["DTEND"]["unixtime"])."</p>\n");
				} else print("<p class='event_info'>De ".date("H:i", $event["DTSTART"]["unixtime"])." &agrave; ".date("H:i", $event["DTEND"]["unixtime"])."</p>\n");
			}
			print ("</div>\n");
		}
		print ("</div>\n");
	}
	print("</div>\n");
	print("</div>\n");
	print("<script>
		$(function() {
			//liens pour se d�placer dans le calendrier
			$(\".next_month, .prev_month, .today\").live('click', function() {
				var htmlcal = $(this).attr('href') + ' .calendar_content';
				var calheight = $(this).parents('.calendar').height();
				$(this).parents('.calendar').html('<div style=\"height:'+calheight+'px;background:transparent url(tools/wikical/presentation/images/loading.gif) no-repeat center center;\"></div>').load(htmlcal);
				return false;
			});
			
			//listes d�roulantes de s�lection de date
			$(\".select_annee, .select_mois\").live('change', function() {
				var htmlcal = $(this).find(\"option:selected\").val() + ' .calendar_content';
				var calheight = $(this).parents('.calendar').height();
				$(this).parents('.calendar').html('<div style=\"height:'+calheight+'px;background:transparent url(tools/wikical/presentation/images/loading.gif) no-repeat center center;\"></div>').load(htmlcal);
				return false;
			});
		});
		</script>");
	
}
?>