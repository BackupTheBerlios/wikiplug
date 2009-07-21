<?php

if (!defined("WIKINI_VERSION"))
{
            die ("acc&egrave;s direct interdit");
}


	//on recupere tous les tags existants
	$tab_tous_les_tags = $this->GetAllTags();
	$tags = '';
	if (is_array($tab_tous_les_tags))
	{
		foreach ($tab_tous_les_tags as $tab_les_tags)
		{
			$tags .= $tab_les_tags['value'].' ';
		}
		$tags = substr($tags,0,-1);
	}


	$tous_les_tags = split(' ', $tags);
	$remplacement ='
	<link rel="stylesheet" href="tools/tags/presentation/tags.css" type="text/css" media="screen" />
    <script type="text/javascript" src="tools/tags/lib/tag.js" type="text/javascript"></script>
    <script type="text/javascript" src="tools/tags/lib/hovertip.js"></script>
	<script type="text/javascript">		
		$(document).ready(function() {
			// initialize tooltips in a seperate thread
			window.setTimeout(hovertipInit, 1);
			
			//effets d\'animation pour les formulaires de commentaires
			$(".microblogcommentform").hide();
			$(".lien_commenter").css("cursor", "pointer");
			$(".lien_commenter").click(function() {
				$(this).next().toggle().find(".commentaire_microblog").focus();
			});
			
			//apparition des boutons des liens pour les commentaires au survol de la souris
			$("div.comment").bind("mouseenter",function(){
		      $(".commenteditlink",this).show();
		    }).bind("mouseleave",function(){
		      $(".commenteditlink",this).hide();
		    });
		    $("div.include").bind("mouseenter",function(){
		      $(".pageeditlink",this).show();
		    }).bind("mouseleave",function(){
		      $(".pageeditlink",this).hide();
		    });

			//ajax envoi de nouveaux commentaires
			$(".bouton_microblog").live("click", function() {
				var textcommentaire = $(this).parent().find(".commentaire_microblog").val();	
				var urlpost= $(this).parent().attr("action").replace(\'/addcomment\',\'/ajaxaddcomment\'+\'&jsonp_callback=?\'); 		
				$(this).parent().parent().parent().find(".commentaires").attr("id",\'comments\');
		   		
				
				$.ajax({
   					type: "POST",
					url: urlpost,
					data: { body: textcommentaire },
					dataType: "jsonp",					
					success: function(data){
				    	$("#comments").append(data.html);
				    	$(".microblogcommentform").hide();
				    	$("#comments").removeAttr("id");
					}
				 });
				
				
		   		$(this).parent().find(".commentaire_microblog").val("");	   		 		
		  		return false;
			});
			
			//ajax edition commentaire			
			$("a.lien_edit_comment").live("click", function() {
				//on cache les formulaires déja ouverts et on reaffiche le contenu
				$(".comment_a_editer").remove();
				$("#comments").show().removeAttr("id");	
				
				var urlpost= $(this).attr("href").replace(\'edit\',\'ajaxedit\')+\'&commentaire=1&jsonp_callback=?\';
				//var dataString = \'commentaire=1\' ;
				$(this).parents(".comment").attr("id",\'comments\');
				$("#comments").hide();												
				
			   	
			   	$.getJSON(urlpost, function(data) {				 
					//on affiche le contenu ajax
				    var ajoutajax = $("<div>").addClass("comment_a_editer").html(data.html).show();
				    $("#comments").after(ajoutajax);
				});	


				$(this).attr(\'href\',"javascript:void(0);");		      
			});
			
			//annulation edition commentaire
			$("input.bouton_annul").live("click", function() {
				$(".comment_a_editer").remove();
				$("#comments").show().removeAttr("id");		      
			});
			
			//sauvegarde commentaire
			$("input.bouton_submit").live("click", function() {
				var urlpost= $("#ACEditor").attr("action");
				var dataString = \'commentaire=1&submit=Sauver&wiki=\'+$("#ACEditor input[name=\'wiki\']").val()+\'&previous=\'+$("#ACEditor input[name=\'previous\']").val()+\'&body=\'+$("#ACEditor textarea[name=\'body\']").val() ;
				$(this).parents(".comment").attr("id",\'comments\');
				
				$.ajax({
				      type: "POST",
				      url: urlpost,
				      data: dataString,
				      complete: function(data) {
				      	if (data.responseText==\'nochange\') {
				      		$("#comments").show();
				      	} else {      	
					      	//on enleve le formulaire et on affiche le contenu ajax				      	
					      	var ajoutajax = $(data.responseText).show();
					      	$("#comments").after(ajoutajax).remove();
				      	}	        
					  }			      
			   	});
				
				$(".comment_a_editer").remove();	      
			});			
						
			//ajax suppression commentaire			
			$("a.lien_suppr_comment").live("click", function() {
				var urlget = $(this).attr(\'href\');
				$(this).parent().parent().attr("id",\'commentasupp\');
				
				if (confirm(\'Voulez vous vraiment supprimer ce commentaire?\'))
				{
					$.ajax({
						type: "POST",
					    url: urlget,
					    success: function() {
					    	$("#commentasupp").remove();
					    	return false;
						}
					});
					$(this).attr(\'href\',"javascript:void(0);");
				}
				else 
				{
					return false;
				}		      
			});
			//return false;
		});
	</script>

	';

	if ($this->GetMethod() == "edit")
	{
		$remplacement .='
	    <script type="text/javascript">
	    <!--
	    $(function () {
	        $(\'#tags\').tagSuggest({
	            tags: '.json_encode($tous_les_tags).'
	        });
	    });
	    //-->
	    </script>
		 ';
	}

	$plugin_output_new=preg_replace ('/<\/head>/', $remplacement."\n".'</head>', $plugin_output_new);

?>
