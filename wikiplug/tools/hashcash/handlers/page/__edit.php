<?php
/*
*/
if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	if ($_POST["submit"] == 'Sauver') {
			require_once('tools/hashcash/secret/wp-hashcash.lib');
			if($_POST["hashcash_value"] != hashcash_field_value()) {
				$this->SetMessage("Cette page n\'a pas &eacute;t&eacute; enregistr&eacute;e car ce wiki pense que vous etes un robot !");
				$this->Redirect($this->href());
			}
	}
	
}

?>
