<?php
if (!defined("WIKINI_VERSION")) {
        die ("acc&egrave;s direct interdit");
}

//On déconnecte si demandé.
if ($_REQUEST["action"] == "deco") {
	$this->LogoutUser();
	$this->Redirect($this->href()); 
}

//echo $this->GetWakkaName()." : ".$this->GetPageTag();
if ($this->GetUser() != null) {
	echo $this->GetUserName();
	echo ' '.'(<a href="' . $this->href('', $this->GetPageTag(), 'action=deco') . "\">D&eacute;connexion</a>)\n";
} 
else {
	echo ' '.'<a href="' . $this->href('', 'ParametresUtilisateur', '') . "\">Connexion</a>\n";
}
?>