<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
$param = $this->GetParameter('param');
//TODO: verifier que param ne veut pas prendre le mot de passe mysql
if (!empty($param)) {
	echo $this->config[$param];
}
?>