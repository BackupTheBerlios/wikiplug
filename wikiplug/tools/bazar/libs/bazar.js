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


//JQuery Unserialize v1.0 by James Campbell
(function($){
$.unserialize = function(Data){
        var Data = Data.split("&");
        var Serialised = new Array();
        $.each(Data, function(){
            var Properties = this.split("=");
            Serialised[Properties[0]] = Properties[1];
        });
        return Serialised;
    };
})(jQuery);


/**
 *
 * javascript and jquery tools for Bazar
 *
 * */
$(document).ready(function () {

	//creation des overlay pour bazar
	$('#container').before('<div id="overlay-bazar" class="yeswiki-overlay" style="display:none"></div>');

	//antispam javascript
	$("input[name=antispam]").val('1');

	//carto google
	var divcarto = document.getElementById("map" );
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

		//on ajoute le nom des tabs a partir de la legende du fieldset
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
			delay: 0,
			predelay: 0,
			position: "top center",
			opacity: 0.9
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

	//permet de cliquer sur les liens d'edition sans derouler l'accordeon
	$(".accordion .liens_titre_accordeon").bind('click',function(event) {
		event.stopPropagation();
	});

	// initialise les iframe en overlay
	$("a.ouvrir_overlay[rel]").each(function() {
		$(this).overlay({
			expose : 'black',
			effect : 'apple',
			oneInstance : true,
			closeOnClick : false,
			onBeforeLoad : function() {
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

	//on enleve la fonction doubleclic dans le cas d'une page contenant bazar
	$("#formulaire, #map, #calendar, .accordion").bind('dblclick', function(e) {
		return false;
	});

	//permet de gerer des affichages conditionnels quand on choisit une valeur dans une liste deroulante, en fonction de balises div
	$("#formulaire select, #champs_formulaire select").live('change', function() {
		var id = $(this).attr('id');
		$("div[id^='"+id+"']").hide();
		$("div[id='"+id+'_'+$(this).val()+"']").show();
	});

	//============bidouille pour que les widgets en flash restent en dessous des elements en survol===========
	$("object").append('<param value="opaque" name="wmode">');
	$("embed").attr('wmode','opaque');

	//============validation formulaire=======================================================================
	$("#formulaire").removeAttr('onSubmit').validator({
		lang: 'fr',
		offset: [-20, -20],
		position: 'top right',
		message: '<div><em/></div>',
		speed: 0
	}).bind("onFail", function(e, errors)  {
		if (e.originalEvent.type == 'submit') {
			// loop through Error objects and add the border color
			$.each(errors, function()  {
				var input = this.input;
				input.css({borderColor: 'red'}).focus(function()  {
					input.css({borderColor: '#999'});
				});
			});

			//on remonte en haut du formulaire
			$('html, body').animate({scrollTop: $(this).offset().top - 50}, 800);
		}
	});

	$.tools.validator.localize("fr", {
		'*' : 'Veuillez v&eacute;rifier ces champs',
		':email' : 'Entrez un email valide',
		':number' : 'Entrez un chiffre exclusivement',
		':url' : 'Entrez une adresse URL valide',
		'[max]' : 'La valeur ne peut pas d&eacute;passer $1',
		'[min]' : 'La valeur doit &ecirc;tre plus grande que $1',
		'[required]' : 'Champs obligatoire'
	});


	$.tools.validator.fn(".bazar-select", function(input, value) {
		return value != 0 ? true : {
			en: "Please make a selection",
			fr: "Il faut choisir une option dans la liste d&eacute;roulante."
		};
	});


	//============formulaire de creation de formulaire====================================================
	var listes_et_fiches;

	//quand on choisit un type de champs, le formulaire adequat apparait
	$('#type_champs_formulaire').live('change', function() {
		var val_type_champs = $(this).val();

		// on charge le bon formulaire
		if (val_type_champs != '') {
			$('#form_type_champs').load('tools/bazar/presentation/squelettes/champs_'+val_type_champs+'.tpl.html', function() {
				// on recupere par ajax les types de liste et de fiches a proposer comme source de donnees
				if (val_type_champs == 'liste') {
					// pour les listes et checkbox, il faut recuperer en json toutes les listes et formulaires
					$.getJSON("wakka.php?wiki=BazaR/json&demand=listes_et_fiches", function(data) {
						listes_et_fiches = data;

						// on recupere les listes
						$.each(listes_et_fiches.listes, function(PageWikiListe, value) {
							$('#list_source').append('<option value="' + PageWikiListe + '">' + value.titre_liste + '</option>');
						});

						//on recupere les types de fiches
						$.each(listes_et_fiches.fiches, function(Categorie, value) {
							$.each(value, function(PageWikiTypeFiche, DataTypeFiche) {
								$('#type_sheet_source').append('<option value="' + PageWikiTypeFiche + '">' + DataTypeFiche.bn_label_nature + '</option>');
							});
						});
					});
				}

				// quand on change la valeur dans la liste des sources, on vide la liste format et les valeurs par defaut
				$('#source').live('change', function() {
					$('#type_champs').children().removeAttr('selected');
					$('#type_champs:first-child').attr('selected','selected');
					$('#select_default').html('<option value="" selected="selected">Choisir...</option>');
					$('#checkbox_default').empty();
					$('#type_champs_checkbox, #type_champs_select').hide();
				});

				// quand on choisit un format de type de champs on peut rentrer les valeurs par defaut et autre
				$('#type_champs').live('change', function() {
					var format_type_champs = $(this).val();
					var format_affichage = $('#source option:selected').parent().attr('id');
					var PageWiki = $('#source').val();
					if (format_affichage == 'type_sheet_source') {
						alert('Les fiches ne marchent pas encore, stay tuned!');
					}
					else if (format_affichage == 'list_source') {
						if (format_type_champs == 'select') {
							// on formatte la liste des valeurs par defaut avec les champs correspondants a la liste choisie
							$('#select_default').html('<option value="" selected="selected">Choisir...</option>');
							$.each(listes_et_fiches.listes[PageWiki].label, function(id, value) {
								$('#select_default').append('<option value="' + id + '">' + value + '</option>');
							});

							// on compte le nb d'entree et on ajuste les curseurs range
							var nb = $('#select_default option').length - 1;
							$('#select_nb_choices_min, #select_nb_choices_max, #select_size').attr('max', nb);
							$('#select_nb_choices_min, #select_nb_choices_max, #select_size').rangeinput({
								css: { input:  'range', slider: 'range_slider', progress: 'range_progress', handle: 'range_handle'}
							});
						}
						else if (format_type_champs == 'checkbox') {
							//on vide le contenu existant
							$('#checkbox_default').empty();

							// on formatte les cases a cocher avec les champs correspondants a la liste choisie
							$.each(listes_et_fiches.listes[PageWiki].label, function(id, value) {
								$('#checkbox_default').append('<input type="checkbox" id="checkbox_default' + id + '" value="1" name="checkbox_default[' + id + ']" class="element_checkbox" /><label for="checkbox_default' + id + '">' + value + '</label>');
							});

							// on compte le nb d'entree et on ajuste les curseurs range
							var nb = $('#checkbox_default input:checkbox').length;
							$('#checkbox_nb_choices_min, #checkbox_nb_choices_max').attr('max', nb);

							$('#checkbox_nb_choices_min, #checkbox_nb_choices_max').rangeinput({
								css: { input:  'range', slider: 'range_slider', progress: 'range_progress', handle: 'range_handle'}
							});
						}
					}
				});

				/* ON UTILISE PAS : TROP COMPLIQUE
				// gestion de la taille des selects
				$('#select_size').live('change', function() {
					var size = $(this).val();
					if (size == 1) {
						$('#select_default').removeAttr('multiple');
					} else {
						$('#select_default').attr('multiple', 'multiple');
					}
					$('#select_default').attr('size', size);
				}); */

				// initialise les curseurs range pour les champs texte et textelong
				$("#nb_char_min, #nb_char_max, #image_max, #thumbnail_max").rangeinput({
					css: { input:  'range', slider: 'range_slider', progress: 'range_progress', handle: 'range_handle'}
				});

				// initialise les tooltips d'aide
				$("#form_type_champs img.tooltip_aide[title]").each(function() {
					$(this).tooltip({
						effect: 'fade',
						delay: 0,
						predelay: 0,
						position: "top center",
						opacity: 0.9
					});
				});

				// initialise le validateur
				var validateur = $("#champs_formulaire").validator({
					lang: 'fr',
					offset: [-20, -20],
					position: 'top right',
					message: '<div><em/></div>',
					speed: 0
				});

				$(this).append('<div class="clear"></div>');

				// l'overlay : on le charge s'il a deja ete initialise
				if ($("#champs_formulaire").hasClass("init")) {
					$("#champs_formulaire").overlay().load();
				}

				// on l'initialise sinon
				else {
					$("#champs_formulaire").addClass('init').overlay({
						load : true,
						oneInstance : true,
						closeOnClick : false,
						mask : {color: '#000', loadSpeed:0, opacity: 0.7},
						onBeforeClose : function(e) {
							//on vide le contenu de l'overlay type de champs, on le reinitialise puis on le deplace
							$('#form_type_champs').empty();
							$('#type_champs_formulaire').val('0');
							$(".error").hide();
						}
					});

					// on place le curseur sur le premier champs a saisir
					$("#champs_formulaire input:first").focus();

					//quand on annule, l'overlay disparait
					$('a.bouton_annuler_formulaire').live('click', function() {
						$("#champs_formulaire").overlay().close();
						return false;
					});
				}
			});
		}
	});

	// Quand on ajoute un champs et qu'il est valide, on renvoie le champ a l'autre formulaire en le previsualisant
	$('.bouton_ajouter_formulaire').bind("click", function(e){

		// on enleve les champs caches pour la validation
		var entrees_cachee_html = $("#type_html_HTML:hidden").detach();
		var entrees_cachee_title = $("#type_html_title:hidden").detach();

		var validateur = $("#champs_formulaire").validator({
			lang: 'fr',
			offset: [-20, -20],
			position: 'top right',
			message: '<div><em/></div>',
			speed: 0
		});

		// si le formulaire  est valide, on envoie les donnees au formulaire principal, en formatant les donnees en liste deplacable
		if (validateur.data("validator").checkValidity()) {

			// on passe les valeurs dans un string pour les exploiter plus tard
			var values = $("#champs_formulaire").serialize();

			// on remet les champs caches
			entrees_cachee_html.after('#champs_formulaire div.formulaire_ligne:first');
			entrees_cachee_title.after('#champs_formulaire div.formulaire_ligne:first');

			// on incremente le nombre d elements presents dans le formulaire pour generer les id
			var nb = $("#formulaire ul.valeur_formulaire li").length + 1;
			var choix = ''; var cases_cochees = new Array();

			// cas de l'insertion d'une checkbox : on n'a pas le label a mettre devant mais on prepare la legende
			if ($("#type_champs_formulaire").val() == 'liste' && $("#type_champs").val() == 'checkbox') {
				$('#checkbox_default input:checked').each(function(index) {
					cases_cochees[index] = $(this).attr('id');
				});
				var champ = '<fieldset id="checkbox' + nb + '" class="bazar_fieldset"><legend>';
				if ($("#checkbox_nb_choices_min").val() > 0) {
					champ += '<span class="symbole_obligatoire">*&nbsp;</span>';
				}
				champ += $('#label_liste').val();
				if ($("#champs_formulaire input[name=help_tooltip]").val() != '') {
					champ += '&nbsp;<img class="tooltip_aide" title="' + $("#champs_formulaire input[name=help_tooltip]").val() + '" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
				}
				champ += '</legend>' + $('#checkbox_default').html() + '</fieldset>';
			}

			// cas de l'insertion d'un fichier
			else if ($("#type_champs_formulaire").val() == 'fichier') {
				var champ = '<span class="link-title">' + $('#link-title').val() + '</span>';
				if ($('#link-description').val() != '') {
					champ += '<span class="link-description"> : ' + $('#link-description').val()+'</span>';
				}
				champ += '<div class="champ_formulaire_ligne"><label>';
				if ($("#required:checked").val() == 'on') {
					champ += '<span class="symbole_obligatoire">*&nbsp;</span>';
				}
				champ += 'T&eacute;l&eacute;verser le fichier</label><input class="attach_file" type="file" disabled="disabled" /></div>';
			}

			// cas de l'insertion d'une image
			else if ($("#type_champs_formulaire").val() == 'image') {
				var champ = '<span class="image_description">' + $('#image_description').val() + '</span>';
				champ += '<div class="champ_formulaire_ligne"><label class="attach_file ' + $("#type_champs_formulaire").val() + '">';
				if ($("#required:checked").val() == 'on') {
					champ += '<span class="symbole_obligatoire">*&nbsp;</span>';
				}
				champ += 'T&eacute;l&eacute;verser l\'image</label><input class="attach_file" type="file" disabled="disabled" /></div>';
			}

			// cas de l'insertion d'un titre de section
			else if ($("#type_champs_formulaire").val() == 'titre') {
				var champ = '';
				if ($('#type_html').val() == 'title') {
					champ += '<h3 class="section_title">' + $("#title_text").val() + '</h3>';
				}
				else if ($('#type_html').val() == 'HTML') {
					champ += '<div class="html_text">' + $("#html_text").val() + '</div>';
				}
				champ += '<em class="appears_for">Apparait pour : ';

				$('#form_type_champs input:checked + label').each(function(i) {
					if (i>0) {
						champ +=  ', ' + $(this).text();
					}
					else {
						champ +=  $(this).text();
					}
				});
				champ += '</em>';
			}

			// on prepare le label du formulaire des champs texte, textelong, liste deroulante, fichier, image
			else {
				var champ = '<div class="champ_formulaire_ligne"><label>';

				// on ajoute l'asterisque pour les champs obligatoires
				if ($("#required:checked").val() == 'on') {
					champ += '<span class="symbole_obligatoire">*&nbsp;</span>';
				}

				/* N'EST PLUS UTILE, TROP COMPLIQUE
				// cas du champ obligatoire de la liste deroulante
				else if ($("#select_nb_choices_min").val() > 0) {
					champ += '<span class="symbole_obligatoire">*&nbsp;</span>';
				}
				*/

				champ += $("#champs_formulaire input[name=label]").val();

				// on ajoute l'info bulle si elle existe
				if ($("#champs_formulaire input[name=help_tooltip]").val() != '') {
					champ += '&nbsp;<img class="tooltip_aide" title="' + $("#champs_formulaire input[name=help_tooltip]").val() + '" src="tools/bazar/presentation/images/aide.png" width="16" height="16" alt="image aide" />';
				}

				champ += '</label>';
			}

			//insertion d'un champs texte
			if ($("#type_champs_formulaire").val() == 'texte') {
				champ += '<input class="input_texte ' + $('#type_champs').val() + '" name="bn_label_nature" type="text" value="' +
					$("#champs_formulaire input[name=default]").val() +
					'" disabled="disabled" /></div>';
				
				//pour toutes les listes deroulantes permettant un choix de valeur texte, on ajoute une option 
				var optionselect = $('<option>').attr('value', $('#champs_formulaire input[name=id]').val()).text($('#champs_formulaire input[name=label]').val());
				optionselect.appendTo('.updatable-input-text');
			}

			//insertion d'un champs textelong
			else if ($("#type_champs_formulaire").val() == 'textelong') {
				champ += '<textarea class="input_textarea" cols="20" rows="3" disabled="disabled"">' +
					$("#champs_formulaire input[name=default]").val() +
					'</textarea></div>';
			}

			//insertion d'une liste deroulante
			else if ($("#type_champs_formulaire").val() == 'liste' && $("#type_champs").val() == 'select') {
				choix = $('#select_default:visible').val();
				champ += '<select id="select' + nb + '">' + $('#select_default').html() + '</select></div>';
			}

			$('#formulaire ul.valeur_formulaire').append('<li id="row' + nb + '" class="formulaire_ligne">' +
					'<a href="#" title="D&eacute;placer l\'&eacute;l&eacute;ment" class="handle"></a>' +
					'<a href="#" class="BAZ_lien_supprimer supprimer_champs_formulaire"></a>' +
					'<a href="#" class="BAZ_lien_modifier modifier_champs_formulaire" rel="#overlay-bazar" ></a>' +
					champ + '<input type="hidden" id="champ' + nb + '" name="champ[' + nb + ']" value="' + values + '" />'+
					'<div class="clear"></div></li>');

			$("#champs_formulaire").overlay().close();

			// on ajoute les valeurs par defaut et on desactive la saisie pour les listes deroulantes
			$('#select' + nb).val(choix).attr('disabled', true);

			// on ajoute les valeurs par defaut et on desactive la saisie pour les cases a cocher
			for ( var i=0; i<cases_cochees.length; i++ ) {
				$('#'+ cases_cochees[i]).attr('checked', true);
			}
			$('#checkbox' + nb + ' input.element_checkbox').attr('disabled', true);

			return false;
		};
		return false;
	});

	//on supprime la ligne du champs formulaire
	$('a.supprimer_champs_formulaire').live('click', function() {
		$(this).parents('li.formulaire_ligne').remove();
		$("#formulaire .valeur_formulaire li").each(function(i) {
			$(this).attr('id', 'row'+(i+1));
		});
		return false;
	});

	//============gestion des dates=======================================================================
	//traduction francaise
	$.tools.dateinput.localize("fr",  {
	   months:        'janvier,f&eacute;vrier,mars,avril,mai,juin,juillet,ao&ucirc;t,' +
	                   	'septembre,octobre,novembre,d&eacute;cembre',
	   shortMonths:   'jan,f&eacute;v,mar,avr,mai,jun,jul,ao&ucirc;,sep,oct,nov,d&eacute;c',
	   days:          'dimanche,lundi,mardi,mercredi,jeudi,vendredi,samedi',
	   shortDays:     'dim,lun,mar,mer,jeu,ven,sam'
	});


	// dateinput initialization. the language is specified with lang- option
	$("#formulaire :date").dateinput({
		lang: 'fr',
		format: 'yyyy-mm-dd',
		offset: [0, 0],
		selectors: true,
		speed: 'fast',
		firstDay: 1
	});

	// initialise les barres slide (input range)
	$("#formulaire :range").rangeinput({
		css: { input:  'range', slider: 'range_slider', progress: 'range_progress', handle: 'range_handle'}
	});
});

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

	return this.keyup(onEdit).keydown(onEdit).focus(onEdit);
}

$(document).ready(function(){
	var onEditCallback = function(remaining){
		$(this).parents(".formulaire_ligne").find('.charsRemaining').text(" (" + remaining + " caracteres restants)");
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
//deplacement des listes
/*****************************************************************************************************/
function addFormField() {
	var nb = $("#formulaire .valeur_liste input.input_liste_label[name^='label']").length + 1;
	$("#formulaire .valeur_liste").append(
		'<li class="liste_ligne" id="row' + nb + '">' +
			'<a href="#" title="D&eacute;placer l\'&eacute;l&eacute;me,t" class="handle"></a>' +
			'<input type="text" name="id[' + nb + ']" class="input_liste_id" />' +
			'<input type="text" name="label[' + nb + ']" class="input_liste_label" />' +
			'<a href="#" class="BAZ_lien_supprimer suppression_label_liste"></a>' +
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
		alert('Le dernier element ne peut etre supprime.');
	}
}
