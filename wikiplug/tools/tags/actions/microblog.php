<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}

//mot clés utilisés d'office
$tags = $this->GetParameter('tags');

//peut on éditer les pages?
$lienedit = $this->GetParameter('edit');

$titrerss = $this->GetParameter('titrerss');

//classe CSS associée
$class = $this->GetParameter('class');
if (empty($class)) $class = 'microblog';

//formulaire de microblog au dessus ou en dessous
$enhaut = $this->GetParameter('enhaut');
if (empty($enhaut)) $enhaut="oui";

//tri alphabetique ou par date
$tri = $this->GetParameter('tri');
if (empty($tri)) $tri = 'date';

//nom de la page wiki faisant formulaire
$pageformulaire = $this->GetParameter('pageformulaire');

//nombre de pages wiki affichées par page
$nb = $this->GetParameter('nb');

//nombre de caracteres maximum pour un microbillet
$nbcar = $this->GetParameter('nbcar');
if (empty($nbcar)) $nbcar=300;

if (isset($_POST['FormMicroblog'])) {
	$date = date("Ymdhis");
  	$this->SavePage($this->getPageTag().$date, $_POST['microblog_billet']);
	$this->SaveTags($this->getPageTag().$date, $_POST['microblog_tags_caches'].' '.$_POST['microblog_tags']);
	$this->Redirect($this->Href());
	exit;
}

else {
	if ($this->GetMethod() != 'xml')
	{
		//on affiche le lien vers le flux RSS
		$html_rss = '<div class="liens_rss">';
		if (empty($titrerss)) $html_rss .= $this->Format('{{rss tags="'.$tags.' microblog"}}');
		else $html_rss .= $this->Format('{{rss tags="'.$tags.' microblog" titrerss="'.$titrerss.'"}}');
		$html_rss .= '</div>';
		
		// affichage du formulaire
		$html_formulaire = '';
		$html_formulaire .= $this->FormOpen();
		$html_formulaire .= '<input type="hidden" name="FormMicroblog" value="true" />'."\n";
		$html_formulaire .= '<input type="hidden" name="microblog_tags_caches" value="'.$tags.' microblog" />'."\n";
			
		if (empty($pageformulaire)) {
			if (!file_exists('tools/tags/presentation/formulaire_microblog.tpl.html')) {
					exit('Le fichier template du formulaire de microblog n\'existe pas. Il doit exister...');
			}
			include_once('tools/tags/lib/squelettephp.class.php');
			$squel = new SquelettePhp('tools/tags/presentation/formulaire_microblog.tpl.html');
			$squel->set(array("nb"=>$nbcar));
			$html_formulaire .= $squel->analyser();
			 
		} else {
			$page = $this->LoadPage($pageformulaire);
			$html_formulaire .= $this->Format($page["body"], "wakka");
		}		
	
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
	
			
		//on formatte l'action includetag qui va tout nous afficher à l'écran la liste des bulles du microblog
		$texte = '{{listepages tags="microblog '.$tags.'" class="'.$class.'" tri="'.$tri.'"';
		if (!empty($lienedit)) $texte .= ' edit="'.$lienedit.'"';
		if (!empty($nb)) $texte .= ' nb="'.$nb.'"';
		$texte .= '}}';
	
		//le formulaire de saisie doit il etre en haut
		if ($enhaut=='oui')
		{
			echo $html_rss.$html_formulaire.$this->Format($texte);
		}
		else
		{
			echo $html_rss.$this->Format($texte).$html_formulaire;
		}
	}
	else {
		if (empty($titrerss)) echo $this->Format('{{rss tags="'.$tags.' microblog"}}');
		else echo $this->Format('{{rss tags="'.$tags.' microblog" titrerss="'.$titrerss.'"}}');
	}
}
?>