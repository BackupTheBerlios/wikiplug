<?php
if (!defined("WIKINI_VERSION"))
{
        die ("acc&egrave;s direct interdit");
}
//attributs du body
$wikini_body = $message ? "onLoad=\"alert('".$message."');\" " : "";
echo $wikini_body;
?>