<?php
//v�rification de s�curit�
if (!eregi("wakka.php", $_SERVER['PHP_SELF'])) {
    die ("acc&egrave;s direct interdit");
}

?>
<div class="page">
<?php
if (isset($_POST['Expediteur'])) {
	include_once 'tools/preinscription/libs/Mail.php' ;
    $result = $this->LoadSingle("SELECT COUNT(*) as count FROM ".$this->config["table_prefix"]."triples WHERE ".
                "resource = '".mysql_escape_string($this->tag)."' AND ".
                "property  = '".mysql_escape_string('http://outils-reseaux.org/_vocabulary/preinscription')."' AND ".
                "value     LIKE '".$_POST['Expediteur']."%'");
                
    if ($result['count'] >= 1) {
                $msg = "Vous �tes d�ja inscrit � cette formation.";
    }
    else {
	        $this->Query("insert into ".$this->config["table_prefix"]."triples set ".
            "resource = '".mysql_escape_string($this->tag)."', ".
            "property  = '".mysql_escape_string('http://outils-reseaux.org/_vocabulary/preinscription')."', ".
            "value     = '".mysql_escape_string($_POST['Expediteur'].'|'.$_POST['Prenom'].'|'.$_POST['Nom'].'|'.$_POST['Tarif'])."' ");
            
		    $mail = & Mail::factory ('mail') ;
			$email = $_POST['Expediteur'] ;
			$headers ['Return-Path'] = $email ;
			$headers ['From'] = "<".$email.">" ;
			$sujet = '[Outils-R�seaux : pr�-inscription] Formation � la carte : '.$this->tag; 
			$headers ['Subject'] = $sujet;
			$headers ['Reply-To'] = $email ;
			$texte_mail = 'Bonjour, nous confirmons votre pr�-inscription � la formation d�crite sur cette page '.$this->href().'.'."\n\n";
			$texte_mail .= 'Une jauge pr�sente sur cette page vous indique la progression des inscriptions au fur et � mesure.'."\n";
			$texte_mail .= 'Une fois le quota de pr�-inscrits acquis, une date sera propos�e aux stagiaires.'."\n\n";
			$texte_mail .= 'N.b. Nous nous r�servons le droit d\'annuler la formation si l\'effectif minimal, n\'est pas atteint.'."\n\n";		
			$texte_mail .= "Prenom : ".$_POST['Prenom']."\n";					
			$texte_mail .= "Nom : ".$_POST['Nom']."\n";
			$texte_mail .= "Adresse mail : ".$_POST['Expediteur']."\n";	
			$texte_mail .= "Tarif : ".$_POST['Tarif']." euros\n\n";			
			$texte_mail .= 'Vous pouvez consulter les formations auxquelles vous vous �tes inscrit et vous d�sinscrire � la page http://outils-reseaux.org/wakka.php?wiki=InscriptionFormation '."\n\n";
			$texte_mail .= "\n\n".'Coop�rativement votre !'."\n";
			$texte_mail .= 'L\'�quipe Outils-R�seaux'."\n";
			$texte_mail .= 'http://outils-reseaux.org'."\n";
			$mail -> send ($_POST['mailadmin'].','.$email, $headers, $texte_mail) ;
			if (PEAR::isError ($mail)) {
		    		$msg = 'Le mail n\'est pas parti...' ;
			} else {
				$msg = 'Votre pr&eacute;-inscription nous a bien &eacute;t&eacute; envoy&eacute;.
				Vous recevrez vous aussi sur votre boite mail, un message de confirmation de pr&eacute;-inscription.';
			}
            
    }	
	$this->SetMessage($msg);
	
	$this->Redirect($this->config['base_url'].$_POST['page']);

}
?>
</div>
