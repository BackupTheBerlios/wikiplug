<?
//on inclue Magpie le parser RSS
define('MAGPIE_DIR', 'tools/syndication/libs/');
require_once(MAGPIE_DIR.'rss_fetch.inc');

//pour cacher les erreurs Warning de Magpie
error_reporting(E_ERROR);

//on vérifie si il existe un dossier pour le cache et si on a les droits d'écriture dessus
if (file_exists('cache')) {
	if (!is_writable('cache')) {
		echo '<p class="erreur">Le r&eacute;pertoire "cache" n\'a pas les droits d\'acc&egrave;s en &eacute;criture.</p>'."\n";
	}
} else {
	echo '<p class="erreur">Il faut cr&eacute;er un r&eacute;pertoire "cache" dans le r&eacute;pertoire principal du wikini.</p>'."\n";
}

//récuperation des parametres
$titre = $this->GetParameter("titre");

$nb = $this->GetParameter("nb");
if (empty($nb)) {
	$nb=0;
}

$nouvellefenetre = $this->GetParameter("nouvellefenetre");

$formatdate = $this->GetParameter("formatdate");

$template = $this->GetParameter("template");
if (empty($template)) {
	$template = 'tools/syndication/templates/liste.tpl.html';
} else {
	$template = 'tools/syndication/templates/'.$this->GetParameter("template");
	if (!file_exists($template)) {
			echo 'Le fichier template: "'.$template.'" n\'existe pas, on utilise le template par d&eacute;faut.';
			$template = 'tools/syndication/templates/liste.tpl.html';
	}
}

//recuperation du parametre obligatoire des urls
$urls = $this->GetParameter("url");
if (!empty($urls)) {
	$tab_url = array_map('trim', explode(',', $urls));
    foreach ($tab_url as $cle => $url) {    		
			if ($url != '') {
				$aso_site = array();
				// Liste des encodages acceptes pour les flux
				$encodages = 'UTF-8, ISO-8859-1, ISO-8859-15';
				
				$feed = fetch_rss( $url );
				if ($feed) {
					// Gestion du titre
					if ( $titre == '' ) {
						$aso_site['titre'] = mb_convert_encoding($feed->channel['title'], 'HTML-ENTITIES', $encodages);
					} else {
						$aso_site['titre'] = $titre;
					}
					// Gestion de l'url du site
					$aso_site['url'] = htmlentities($feed->channel['link']);
	
					// Ouverture du lien dans une nouvelle fenetre
					$aso_site['ext'] = $nouvellefenetre;
					
					// Gestion des pages syndiquees
					$i = 0;
				    $nb_item = count($feed->items);
					foreach ($feed->items as $item) {					
						if ($nb != 0 && $nb_item >= $nb && $i >= $nb) {
							break;
						}
						$i++;
						$aso_page = array();
						$aso_page['site'] = $aso_site;	
						$aso_page['url'] = htmlentities($item['link']);
						$aso_page['titre'] = mb_convert_encoding($item['title'], 'HTML-ENTITIES', $encodages);
						$aso_page['description'] = mb_convert_encoding($item['description'], 'HTML-ENTITIES', $encodages);
						if (is_string($item['pubdate'])) $aso_page['date']=strtotime($item['pubdate']);
						else $aso_page['date'] = $item['pubdate'];
						if ($formatdate!='') {
							switch ($formatdate) {							
								case 'jm' :
									$aso_page['date'] = strftime('%d.%m', $aso_page['date']);
									break;
								case 'jma' :
									$aso_page['date'] = strftime('%d.%m.%Y', $aso_page['date']);
									break;
								case 'jmh' :
									$aso_page['date'] = strftime('%d.%m %H:%M', $aso_page['date']);
									break;
								case 'jmah' :
									$aso_page['date'] = strftime('%d.%m.%Y %H:%M', $aso_page['date']);
									break;
								default :
									$aso_page['date'] = '';
							}
						}
						$aso_site['pages'][] = $aso_page;
						$syndication['pages'][strtotime($aso_page['date'])] = $aso_page;
					}
					$syndication['sites'][] = $aso_site;
				} else {
					echo '<p class="erreur">Erreur '.magpie_error().'</p>'."\n";        			    
				}			
			}
        }    
	// Trie des pages par date
	krsort($syndication['pages']);
	
	//+----------------------------------------------------------------------------------------------------------------+
    // Extrait les variables et les ajoutes a l'espace de noms local
	// Gestion des squelettes
	extract($syndication);
	include($template);
} else {
	echo 'Il faut entrer obligatoirement le param&ecirc;tre de l\'url pour syndiquer un flux RSS.';
}
?>