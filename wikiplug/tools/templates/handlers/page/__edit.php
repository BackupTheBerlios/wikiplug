<?php
/*
*/
if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}
//Sauvegarde
if ($_POST["submit"] == 'Sauver') {
	$_POST["body"] = $_POST["body"].'{{template theme="'.$_POST["theme"].'" squelette="'.$_POST["squelette"].'" style="'.$_POST["style"].'"}}';
}

?>