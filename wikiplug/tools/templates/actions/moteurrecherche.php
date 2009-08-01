<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
echo '<span class="titre_rechercher">Rechercher : </span>
<form action="'.$this->href("show","RechercheTexte").'" method="get">
	<input name="wiki" value="RechercheTexte" type="hidden" />
	<input name="phrase" class="input_rech" value="';
echo (isset($_POST['phrase'])) ? $_POST['phrase'] : "Recherche...";
echo '" onfocus="if (this.value==\'Recherche...\') {this.value=\'\';}" size="15" />
	<input type="submit" class="bouton_rech" value="Ok" />
</form>';
?>
