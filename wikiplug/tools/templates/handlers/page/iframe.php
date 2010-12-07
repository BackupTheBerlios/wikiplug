<?php
/*
*/

// Vérification de sécurité
if (!defined("WIKINI_VERSION"))
{
	die ("acc&egrave;s direct interdit");
}


if ($this->HasAccess("read"))
{
	if (!$this->page)
	{
		return;
	}
	else
	{
				
		//javascript pour gerer les liens (ouvrir vers l'extérieur) dans les iframes
		$scripts_iframe = '<script type="text/javascript">
		$(document).ready(function () {
			$("body").css(\'background-color\', \'transparent\').css(\'background-image\', \'none\').css(\'text-align\',\'left\');
			$("a[href^=\'http://\']:not(a[href$=\'/slide_show\'])").click(function() {
				if (window.location != window.parent.location)
				{
					//alert(\'iframe\');
					if (!($(this).hasClass("bouton_annuler")))
					{
						window.open($(this).attr("href"));
						return false;
					}
				}
				else 
				{
					//alert(\'pas iframe\');					
				}
			});			
		});
		</script>';		
			
		// on récupère les entêtes html mais pas ce qu'il y a dans le body
		$header =  explode('<body',$this->Header());
		echo $header[0]."<body>\n<div class=\"page widget\">\n";
		
		//affichage de la page formatée
		echo $this->Format($this->page["body"]);
		echo "</div><!-- end div.page -->";
			
		//on récupère juste les javascripts et la fin des balises body et html
		$footer =  preg_replace('/^.+<script/Us', '<script', $this->Footer());
		
		echo $footer;
		
	}
}
else
{
	return;
}
?>
