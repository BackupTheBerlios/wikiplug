<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

if (!$this->LoadPage('TotO')) {

	echo '<div class="info_box">'."\n".'<h2>Postinstallation de pages</h2>'."\n";
	
	//insertion des pages de YesWiki
	$d = dir("tools/postinstall/setup/yeswiki/");
	
	while ($doc = $d->read()){
		if (is_dir($doc) || substr($doc, -4) != '.txt')
			continue;
		$pagecontent = implode ('', file("tools/postinstall/setup/yeswiki/$doc"));
		$pagename = substr($doc,0,strpos($doc,'.txt'));
		
		// On prend le premier admin venu pour le mettre comme propriétaire des pages
		$result = explode("\n",$this->GetGroupACL('admins'));
		$admin_name = $result[0];
		
		$pages_ajoutees = ''; $pages_deja_existantes = '';
		
		// On ajoute toutes les pages du dossier, sauf celles deja existantes
		if  (!$this->LoadPage($pagename)) {		
			$sql = "Insert into ".$this->config["table_prefix"]."pages ".
				"set tag = '$pagename', ".
				"body = '".mysql_escape_string($pagecontent)."', ".
				"user = '" . mysql_escape_string($admin_name) . "', ".
				"owner = '" . mysql_escape_string($admin_name) . "', " .
				"time = now(), ".
				"latest = 'Y'";

			$this->Query($sql);

			// update table_links 
			$this->SetPage($this->LoadPage($pagename,"",0));
			$this->ClearLinkTable();
			$this->StartLinkTracking();
			$this->TrackLinkTo($pagename);
			$dummy = $this->Header();
			$dummy = $this->Format($pagecontent);
			$dummy .= $this->Footer();
			$this->StopLinkTracking();
			$this->WriteLinkTable();
			$this->ClearLinkTable();
			
			$pages_ajoutees = $pages_ajoutees.(($pages_ajoutees == '') ? $pagename : ', '.$pagename);
		}
		
		else {
			$pages_deja_existantes = $pages_deja_existantes.(($pages_deja_existantes == '') ? $pagename : ', '.$pagename);
		}	

	}
	
	if ($pages_ajoutees != '') {
			echo $this->Format('===Pages ajoutées===
						//Les pages suivantes ont été créées : //
						'.$pages_ajoutees.'
						
						');
	}
						
	if ($pages_deja_existantes != '') {
			echo $this->Format('===Pages déja présentes===
						//les pages suivantes n\'ont pas été créées car elles existent déjà : //
						'.$pages_deja_existantes);
	}
	
	echo '</div>'."\n";
}
?>
