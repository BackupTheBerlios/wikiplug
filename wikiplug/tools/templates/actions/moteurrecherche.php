<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
echo '<strong>Rechercher : </strong><br />
<form action="'.$this->href("show","RechercheTexte").'" method="get">
	<input name="wiki" value="RechercheTexte" type="hidden">
	<input name="phrase" size="15" class="input_rech" /><input type="submit" class="bouton_rech" value="Ok" />
</form>';
?>
