<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

//Sauvegarde
if ( $_POST["submit"] == 'Sauver' && isset($_POST["tags"]) )
{
	$this->SaveTags($this->GetPageTag(), $_POST["tags"]);
}
?>
