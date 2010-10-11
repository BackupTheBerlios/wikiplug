<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

$javascript = '<script type="text/javascript">
	$(document).ready(function() {
		$(".link_login").overlay({mask: \'#999\', effect: \'apple\'});
	});
</script>'."\n";

$plugin_output_new = preg_replace ('/<\/body>/', $javascript."\n".'</body>', $plugin_output_new);

?>
