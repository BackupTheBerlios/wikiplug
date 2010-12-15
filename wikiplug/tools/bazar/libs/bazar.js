/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);


/** 
 * 
 * javascript and query tools for Bazar
 * 
 * */
$(document).ready(function () {
	//antispam javascript
	$("input[name=antispam]").val('1');
	
	
	$(".ajout_champs_formulaire").overlay({
		expose:			'black',
		effect:			'apple',
		oneInstance:	true,
		closeOnClick:	false,
		onBeforeLoad: function() {$('#champs_formulaire .titre_overlay').html('Ajouter un nouveau champs au formulaire');}
	});
	
	$('a.bouton_annuler_formulaire').click(function() { 
		$(".ajout_champs_formulaire").overlay().close(); 
		return false;
	});
	
	
	//carto google
	var divcarto = document.getElementById("map" )
	if (divcarto) {	initialize(); }
	// clic sur le lien d'une fiche, l'ouvre sur la carto
	$("#markers a").live("click", function(){
		var i = $(this).attr("rel");
		// this next line closes all open infowindows before opening the selected one
		for(x=0; x < arrInfoWindows.length; x++){ arrInfoWindows[x].close(); }
		arrInfoWindows[i].open(map, arrMarkers[i]);
		$('ul.css-tabs li').remove();
		$("fieldset.tab").each(function(i) {
						$(this).parent('div.BAZ_cadre_fiche').prev('ul.css-tabs').append("<li class='liste" + i + "'><a href=\"#\">"+$(this).find('legend:first').hide().html()+"</a></li>");
		});
		$("ul.css-tabs").tabs("fieldset.tab", { onClick: function(){} } );
	});

	//tabulations (transforme les fieldsets de classe tab en tabulation)
	$(".BAZ_cadre_fiche, #formulaire").each(function() {
		//nb de tabs par fiche
		var nbtotal = $(this).children("fieldset.tab").size() - 1;
		
		//on ajoute le nom des tabs à partir de la legende du fieldset
		$(this).children("fieldset.tab:first").before("<ul class='css-tabs'></ul>");
		$(this).children("fieldset.tab").each(function(i) {
			$(this).addClass("tab" + i)
			if (i==0)
			{
				$(this).append('<a class="btn next-tab">Suivant &raquo;</a>');
			}
			else if (i==nbtotal)
			{
				$(this).append('<a class="btn prev-tab">&laquo; Pr&eacute;c&eacute;dent</a>');
			}
			else
			{
				$(this).append('<a class="btn prev-tab">&laquo; Pr&eacute;c&eacute;dent</a><a class="btn next-tab">Suivant &raquo;</a>');
			}
			$(this).prevAll('ul.css-tabs').append("<li class='liste" + i + "'><a href=\"#\">"+$(this).find('legend:first').hide().html()+"</a></li>");
		});
	});
	//initialise tabulations
	if ($("ul.css-tabs").size() > 1)
	{
		$("ul.css-tabs").tabs("> .tab", { onClick: function(){if (divcarto) {	initialize(); }} } );
	} 
	else if ($("ul.css-tabs").size() == 1)
	{
		$("ul.css-tabs").tabs("fieldset.tab", { onClick: function(){if (divcarto) {	initialize(); }} } );
	}
	var api = $("ul.css-tabs").data("tabs");
	// "next tab" button
	$("a.next-tab").live('click',function() {
		api.next();
		$('ul.css-tabs').scrollTo();
		return false;
	});

	// "previous tab" button
	$("a.prev-tab").live('click',function() {
		api.prev();
		$('ul.css-tabs').scrollTo();
		return false;
	});
	
    // initialise les tooltips d'aide
    $("img.tooltip_aide[title]").each(function() {
    	$(this).tooltip({ 
			effect: 'fade',
			fadeOutSpeed: 100,
			predelay: 0,
			position: "top center",
			opacity: 0.7
    	});
    });
    
  //accordeon pour bazarliste
	$(".accordion h2.titre_accordeon").bind('click',function() {
		$(this).next("div.pane").slideToggle('fast');
		if ($(this).hasClass('current')) {
			$(this).removeClass('current');
		} else { 
			$(this).addClass('current');
		}
	});
	//permet de cliquer sur les liens d'édition sans dérouler l'accordeon 
	$(".accordion .liens_titre_accordeon").bind('click',function(event) {
		event.stopPropagation();
	});

    // initialise les iframe en overlay
    $("a.ouvrir_overlay[rel]").each(function() {
    	$(this).overlay({
			expose:			'black',
			effect:			'apple',
			oneInstance:	true,
			closeOnClick:	false,
			onBeforeLoad: function() {
				//on transforme le lien avecle handler /iframe, pour le charger dans une fenetre overlay
				var overlay_encours = this
				var lien = overlay_encours.getTrigger().attr("href");
				result = lien.match(/\/iframe/i); 
				if (!result) { lien = lien.replace(/wiki=([a-z0-9]+)&/ig, 'wiki=$1/iframe&', 'g'); }
				$("#overlay div.contentWrap").html('<iframe class="wikiframe" width="630" height="480" frameborder="0" src="' + lien + '"></iframe>');
				//dans la frame, on change le fonctionnement des boutons annuler et sauver, pour retourner comme il faut dans la page de modification principale
				var myFrame = $('#overlay .wikiframe');
				myFrame.load(function() { 
					var contenu_iframe = myFrame.contents();
					contenu_iframe.find('.bouton_annuler').click(function(event) {
						event.preventDefault();
						overlay_encours.close(); 
						return false;
					});
					contenu_iframe.find('input.bouton_sauver').click(function(event) {
						//event.preventDefault();
						//return false;
					});
				});
				
			}		
		});
    });

	//liste oui / non conditionnelle
	$("select[id^='liste12'], select[id^='liste1']").change( function() {
		if ($(this).val()==1) {
			$(this).parents(".formulaire_ligne").next("div[id^='oui']").show();
			$(this).parents(".formulaire_ligne").next("div[id^='non']").hide();
		}
		if ($(this).val()==2) {
			$(this).parents(".formulaire_ligne").next("div[id^='non']").show();
			$(this).parents(".formulaire_ligne").next("div[id^='oui']").hide();
		}
	});
	//a l'ouverture du formulaire, on affiche 
	$(".BAZ_cadre_fiche div[id^='oui'], .BAZ_cadre_fiche div[id^='non']").show();
	$("#formulaire select[id^='liste12'], #formulaire select[id^='liste1']").each(function() {
		if ($(this).val()==1) {
			$(this).parents(".formulaire_ligne").next("div[id^='oui']").show();
			$(this).parents(".formulaire_ligne").next("div[id^='non']").hide();
		}
		if ($(this).val()==2) {
			$(this).parents(".formulaire_ligne").next("div[id^='non']").show();
			$(this).parents(".formulaire_ligne").next("div[id^='oui']").hide();
		}
	});

	//on enleve la fonction doubleclic dans le cas d'une page contenant bazar
	$("#formulaire, #map").bind('dblclick', function(e) {return false;});

	//affichage tooltip des evenements dans le calendrier
	$('.date_avec_evenements').each(function () {
		// options
		var distance = 10;
		var time = 250;
		var hideDelay = 100;

		var hideDelayTimer = null;

		// tracker
		var beingShown = false;
		var shown = false;

		var trigger = $(this);
		var popup = $('.evenements ul', this).css('opacity', 0);

		// set the mouseover and mouseout on both element
		$([trigger.get(0), popup.get(0)]).mouseover(function () {
			$(this).addClass('date_hover');
			// stops the hide event if we move from the trigger to the popup element
			if (hideDelayTimer) clearTimeout(hideDelayTimer);

			// don't trigger the animation again if we're being shown, or already visible
			if (beingShown || shown) {
				return;
			} else {
				beingShown = true;

				// reset position of popup box
				popup.css({
					bottom: 20,
					left: -76,
					display: 'block' // brings the popup back in to view
				})

				// (we're using chaining on the popup) now animate it's opacity and position
				.animate({
					bottom: '+=' + distance + 'px',
					opacity: 1
				}, time, 'swing', function() {
					// once the animation is complete, set the tracker variables
					beingShown = false;
					shown = true;
				});
			}
		}).mouseout(function () {
			$(this).removeClass('date_hover');
			// reset the timer if we get fired again - avoids double animations
			if (hideDelayTimer) clearTimeout(hideDelayTimer);

			// store the timer so that it can be cleared in the mouseover if required
			hideDelayTimer = setTimeout(function () {
				hideDelayTimer = null;
				popup.animate({
					bottom: '-=' + distance + 'px',
					opacity: 0
				}, time, 'swing', function () {
					// once the animate is complete, set the tracker variables
					shown = false;
					// hide the popup entirely after the effect (opacity alone doesn't do the job)
					popup.css('display', 'none');
				});
			}, hideDelay);
		});
	});

//=====================galerie d'images===================================================================
/*	var imagespourgalerie = $("div.triggerimage img[rel]");
	if (imagespourgalerie.length > 0) {	
		imagespourgalerie.overlay({ expose:'#f1f1f1', effect: 'apple' });
	}*/
	
	//création des overlay pour les images
    $("body").append("<div class=\"overlay\" id=\"overlay_bazar\"><div class=\"contentWrap_bazar\"></div></div>");

	$('a.triggerimage[rel="#overlay_bazar"]').overlay({
		mask:'#999', effect: 'apple',
		onBeforeLoad: function() {

			// grab wrapper element inside content
			var wrap = this.getOverlay().find('.contentWrap_bazar');

			// load the page specified in the trigger
			wrap.html('<img src='+this.getTrigger().attr("href")+' alt="image" />');
		}

	});

	//permet de gerer des affichages conditionnels, en fonction de balises div
	$("select[id^='liste']").change( function() {
		var id = $(this).attr('id');
		id = id.replace("liste", ""); 
		$("div[id^='"+id+"']").hide();
		$("div[id='"+id+'_'+$(this).val()+"']").show();
	});
	
//============bidouille pour que les widgets en flash restent en dessous des éléments en survol===========
	$("object").append('<param value="opaque" name="wmode">');$("embed").attr('wmode','opaque');
	
	
//============validation formulaire=======================================================================
	$("#formulaire").removeAttr('onSubmit').validator({lang: 'fr', offset: [-10, 10]}).bind("onFail", function(e, errors)  {
		if (e.originalEvent.type == 'submit') {
			
			// loop through Error objects and add the border color
			$.each(errors, function()  {
				var input = this.input;
				input.css({borderColor: 'red'}).focus(function()  {
					input.css({borderColor: '#999'});
				});
			});
			
			//on remonte en haut du formulaire
			$('html, body').animate({scrollTop: $("#formulaire").offset().top - 50}, 800);
		}
	});
	
	$.tools.validator.localize("fr", {
		'*'			: 'Veuillez vérifier ces champs',
		':email'  	: 'Entrez un email valide',
		':number' 	: 'Entrez un chiffre exclusivement',
		':url' 		: 'Entrez une adresse URL valide',
		'[max]'	 	: 'La valeur ne peut pas dépasser $1',
		'[min]'		: 'La valeur doit être plus grande que $1',
		'[required]'	: 'Champs obligatoire'
	});

	
	$.tools.validator.fn(".bazar-select", function(input, value) {
		return value != 0 ? true : {     
			en: "Please make a selection",
			fr: "Il faut choisir une option dans la liste déroulante."
		};
	});
});

//fonction pour faire des polygones
function createPolygon(coords, color) {
		return new google.maps.Polygon({
			paths: coords,
			strokeColor: "black",
			strokeOpacity: 0.8,
			strokeWeight: 1,
			fillColor: color,
			fillOpacity: 0.4
		});
}

jQuery.fn.limitMaxlength = function(options){

	  var settings = jQuery.extend({
	    attribute: "maxlength",
	    onLimit: function(){},
	    onEdit: function(){}
	  }, options);
	  
	  // Event handler to limit the textarea
	  var onEdit = function(){
	    var textarea = jQuery(this);
	    var maxlength = parseInt(textarea.attr(settings.attribute));

	    if(textarea.val().length > maxlength){
	      textarea.val(textarea.val().substr(0, maxlength));
	      
	      // Call the onlimit handler within the scope of the textarea
	      jQuery.proxy(settings.onLimit, this)();
	    }
	    
	    // Call the onEdit handler within the scope of the textarea
	    jQuery.proxy(settings.onEdit, this)(maxlength - textarea.val().length);
	  }

	  this.each(onEdit);

	  return this.keyup(onEdit)
	        .keydown(onEdit)
	        .focus(onEdit);
	}

	$(document).ready(function(){
	  
	  var onEditCallback = function(remaining){
	    $(this).parents(".formulaire_ligne").find('.charsRemaining').text(" (" + remaining + " caractères restants)");
	    
	    if(remaining > 0){
	      $(this).css('background-color', 'white');
	    }
	  }
	  
	  var onLimitCallback = function(){
	    $(this).css('background-color', 'red');
	  }
	  
	  $('textarea[maxlength]').limitMaxlength({
	    onEdit: onEditCallback,
	    onLimit: onLimitCallback,
	  });
	  
	  //pour la gestion des listes, on peut rajouter dynamiquement des champs
	  $('.ajout_label_liste').live('click', function() {addFormField();return false;});
	  $('.suppression_label_liste').live('click', function() {removeFormField('#'+$(this).parent('.liste_ligne').attr('id'));return false;});
	});


/*****************************************************************************************************/	
//déplacement des listes
/*****************************************************************************************************/
	function addFormField() {
		var nb = $("#formulaire .valeur_liste input.input_liste_label[name^='label']").length + 1;
		$("#formulaire .valeur_liste").append('<li class="liste_ligne" id="row'+nb+'">'+
				'<a href="#" title="D&eacute;placer l\'&eacute;l&eacute;me,t" class="handle"></a>'+
				'<input type="text" name="id['+nb+']" class="input_liste_id" />' +
				'<input type="text" name="label['+nb+']" class="input_liste_label" />' +
				'<a href="#" class="BAZ_lien_supprimer suppression_label_liste"></a>'+
				'</li>');
		$("#formulaire input.input_liste_id[name='id["+nb+"]']").focus();
	}

	function removeFormField(id) {
		var nb = $("#formulaire .valeur_liste input.input_liste_label[name^='label']").length;
		if (nb > 1) {
			var nom = 'a_effacer_' + $(id).find("input:hidden").attr('name');
			$(id).find("input:hidden").attr('name', nom).appendTo("#formulaire");
			$(id).remove();
			$("#formulaire .valeur_liste input.input_liste_label[name^='label']").each(function(i) {
				$(this).attr('name', 'label['+(i+1)+']').
				parent('.liste_ligne').attr('id', 'row'+(i+1)).
				find("input:hidden").attr('name', 'ancienlabel['+(i+1)+']');
			});
		} else {
			alert('Le dernier élément ne peut être supprimé.');
		}
	}