<?php
 /***************************************************************************
 /* DOCUMENTATION
 ****************************************************************************

  Usage : {{cal src="http://mon.domai.ne/path/fichier.ics"}}

  **************************************************************************/

//on appele le fichier de suite
if (!defined("WIKINI_VERSION"))
{
	$url = urldecode($_GET["url"]);
	$color = urldecode($_GET["color"]);
	include_once '../lib/ical.php';
}

//on est dans wiki
else {
	$url = $this->GetParameter("url");
	$color = $this->GetParameter("color");
	include_once 'tools/wikical/lib/ical.php';
}



//Retourne le timestamp du début du mois du timestamp renseigné
function getMonthStartTS($in_timeStamp) { 
	return mktime( 0, 1, 1, date("m", $in_timeStamp), 1, 
	date("Y", $in_timeStamp)); 					
}

//Retourne le timestamp de la fin du mois du timestamp renseigné
function getMonthEndTS($in_timeStamp) { 
	return mktime( 23,	59,	59, date("m", $in_timeStamp)+1, 1, 
					date("Y", $in_timeStamp)); 
}
 /***************************************************************************
 * Elimine les evenement en dehors de l'intervalle précisé
 * et range ceux restant par ordre chronologique.
 */
function filterEvents($in_startTS, $in_endTS, $in_data) {
	$selectedData = array();
	
	//Filtre les évenements
	foreach($in_data as $event) {
		if (($event["DTSTART"]["unixtime"] >= $in_startTS) 
			&& ($event["DTSTART"]["unixtime"] <= $in_endTS)) {
			array_push($selectedData, $event);
		}
	}/**/
	
	//Range les évenements par ordre chronologique
	$size = count($selectedData);
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
 * Crée le squellete de donnée du calendrier (Vue mensuelle).
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
	//Les jours vide de début de mois
	for($i=0 ; $i<$firstDay ; $i++){
		$day = array("isToday" => false, "isEvent" => false, "startDayTS" => mktime(0,0,0,0,0,0), "endDayTS" => mktime(0,0,0,0,0,0), "events" => array() );
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
			if (($event["DTSTART"]["unixtime"] >= $startDayTS) && ($event["DTSTART"]["unixtime"] <= $endDayTS)) {
				$event["SUMMARY"] = utf8_decode($event["SUMMARY"]);
				array_push($events, $event);
				$isEvent = true;
			}	
		}
		array_push($month, array("isToday" => $isToday, "isEvent" => $isEvent, "startDayTS" => $startDayTS, "endDayTS" => $endDayTS, "events" => $events));
		
	}
	return $month;
}


/****************************************************************************
 * Affichage du calendrier (vue mois)
 */

function printMonthCal($in_data, $in_color="grey", $in_timeStamp, $url) {

	print("<div class='calendar' style='background-color: ".$in_color.";'>\n");
	print("<div class='calendar_content'>\n");

	$monthText = "";
	switch (date("n", $in_timeStamp)) {
		case 1: $monthText = "Janvier"; break;
		case 2: $monthText = "F&eacute;vrier"; break;
		case 3: $monthText = "Mars"; break;
		case 4: $monthText = "Avril"; break;
		case 5: $monthText = "Mai"; break;
		case 6: $monthText = "Juin"; break;
		case 7: $monthText = "Juillet"; break;
		case 8: $monthText = "Aout"; break;
		case 9: $monthText = "Septembre"; break;
		case 10: $monthText = "Octobre"; break;
		case 11: $monthText = "Novembre"; break;
		case 12: $monthText = "D&eacute;cembre"; break;
	}

	$prev_month = mktime( 23, 59, 59, date("m", $in_timeStamp)-1, 1, date("Y", $in_timeStamp));
	$next_month = mktime( 23, 59, 59, date("m", $in_timeStamp)+1, 1, date("Y", $in_timeStamp));
	$url_params = "&amp;url=".urlencode($url)."&amp;color=".urlencode($in_color);
	print("<p class='title'><a href=\"tools/wikical/actions/cal.php?timestamp=".$prev_month.$url_params."\" class=\"cal_prev prev_month\" title=\"Mois pr&eacute;c&eacute;dent\"><<</a>\n"
		.$monthText.date(" Y")."\n
		<a href=\"tools/wikical/actions/cal.php?timestamp=".$next_month.$url_params."\" class=\"cal_next next_month\" title=\"Mois suivant\">>></a></p>\n");
	print("<div class='day day_name'>Lun</div>\n");
	print("<div class='day day_name'>Mar</div>\n");
	print("<div class='day day_name'>Mer</div>\n");
	print("<div class='day day_name'>Jeu</div>\n");
	print("<div class='day day_name'>Ven</div>\n");
	print("<div class='day day_name'>Sam</div>\n");
	print("<div class='day day_name'>Dim</div>\n");

	foreach($in_data as $day) {
		//Creation du DIV
		if ($day["isToday"])
			print("<div class='day today'>");
		else if ($day["isEvent"])
			print("<div class='day evday'>");
		else
			print("<div class='day'>");
		//Contenu du DIV
		print(date("d",$day['startDayTS']));

		//affichage des events
		if ($day["isEvent"]) {
			print ("<div id='events'>");
			foreach($day["events"] as $event) {
				print("<p class='event_title'>".$event["SUMMARY"]."</p>");
				//TODO : Gerer toutes les infos
				//       Ajouter une boucle.
				
				print("<p class='event_info'>De ".date("H:i", $event["DTSTART"]["unixtime"])." &agrave; ".date("H:i", $event["DTEND"]["unixtime"])."</p>\n");
			}
			print ("</div>\n");
		}
		print ("</div>\n");
	}
	print("</div>\n");
	print("</div>\n");
	print("<script>
		$(function() {
			$(\".next_month, .prev_month\").live('click', function() {
				var htmlcal = $(this).attr('href') + ' .calendar_content';
				$(this).parents('.calendar').load(htmlcal);
				return false;
			});
		});
		</script>");
	
}

$cal = new ical();
$cal->parse($url);
$data = $cal->get_event_list();

if (isset($_GET['timestamp'])) {
	$daytime = $_GET['timestamp'];
} 
//si pas de date
else {
	$daytime = time();
}

$data = filterEvents(getMonthStartTS($daytime), getMonthEndTS($daytime), $data);

$data = makeMonth($daytime, $data);

printMonthCal($data, $color, $daytime, $url);



?>
