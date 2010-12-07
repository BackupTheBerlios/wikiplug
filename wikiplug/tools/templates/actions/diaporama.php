<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}

//parametres wikini
$pagetag = trim($this->GetParameter('page'));
if (empty($pagetag))
{
	return ('<div class="error_box">Action diaporama : param&ecirc;tre "page" obligatoire.</div>');
}
else {
	
	//pour l'action attach, on simule la pr�sence sur la page, afin qu'il r�cup�re les fichiers attach�s au bon endroit
	$oldpage = $this->GetPageTag();
	$this->tag = $pagetag;
	$this->page = $this->LoadPage($this->tag);
	
	//fonction de g�n�ration du diaporama (teste les droits et l'existence de la page)
	include_once('tools/templates/libs/templates.functions.php');
	echo print_diaporama($pagetag);
	
	//on r�tablie le bon nom de page
	$this->tag = $oldpage;
	$this->page = $this->LoadPage($oldpage);
}
?>
