<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


$tags = $this->GetParameter('with');
$lienedit = $this->GetParameter('edit');
$class = $this->GetParameter('class');
$enhaut = $this->GetParameter('enhaut');
if (empty($class)) $class = 'microblog';
$tri = $this->GetParameter('tri');
if (empty($tri)) $tri = 'date';
$template_formulaire = 'FormulaireMicroblog';


if (isset($_POST['FormMicroblog'])) {
	$date = date("Ymdhis");
  	$this->SavePage($this->getPageTag().$date, $_POST['microblog_billet']);
	$this->SaveTags($this->getPageTag().$date, $_POST['microblog_tags_caches'].' '.$_POST['microblog_tags']);
	$this->Redirect($this->Href());
	exit;
}

else {
	// affichage du formulaire
	$html_formulaire = '';
	$page = $this->LoadPage($template_formulaire);
	if ($page!='')
	{
		$html_formulaire .= $this->FormOpen();
		$html_formulaire .= '<input type="hidden" name="FormMicroblog" value="true" />'."\n";
		$html_formulaire .= '<input type="hidden" name="microblog_tags_caches" value="'.$tags.' microblog" />'."\n";
		$html_formulaire .= $this->Format($page["body"], "wakka");

		//on récupère tous les tags existants
		$tab_tous_les_tags = $this->GetAllTags();
		$toustags = '';
		if (is_array($tab_tous_les_tags))
		{
			foreach ($tab_tous_les_tags as $tab_les_tags)
			{
				$toustags .= $tab_les_tags['value'].' ';
			}
			$toustags = substr($toustags,0,-1);
		}
		$tous_les_tags = split(' ', $toustags);
		$html_formulaire .= '
	    <script type="text/javascript">
	    <!--
	    $(function () {
	        $(\'#microblog_toustags\').tagSuggest({
	            tags: '.json_encode($tous_les_tags).'
	        });
	    });
	    //-->
	    </script>'."\n";
		$html_formulaire .= $this->FormClose();
		$html_formulaire .= '<br class="alaligne">'."\n";
	}
	else
	{
		$html_formulaire .= $this->Format('//La page wikini du formulaire '.$template_formulaire.' est introuvable.');
	}

	//on formatte l'action includetag qui va tout nous afficher à l'écran la liste des bulles du microblog
	$texte = '{{includetag with="microblog '.$tags.'" class="'.$class.'" tri="'.$tri.'"';
	if (!empty($lienedit)) $texte .= ' edit="'.$lienedit.'"';
	$texte .= '}}';

	//le formulaire de saisie doit il etre en haut
	if ($enhaut=='oui')
	{
		echo $html_formulaire.$this->Format($texte);
	}
	else
	{
		echo $this->Format($texte).$html_formulaire;
	}

}

?>
