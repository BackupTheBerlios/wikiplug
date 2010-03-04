$(document).ready(function () {
	//carto google
	var divcarto = document.getElementById("map" )
	if (divcarto) {	initialize(); }

	//création des div pour jquery tools (tooltips, overlays, etc..)
    $("body").append("<div id=\"dynatooltip\">&nbsp;</div><div class=\"overlay\" id=\"overlay\"><div class=\"contentWrap\"></div></div><div class=\"simple_overlay\" id=\"gallery\"><a class=\"prev\">Pr&eacute;c&eacute;dent</a><a class=\"next\">Suivant</a><div class=\"info\"></div><img class=\"progress\" src=\"tools/bazar/presentation/images/ajax-loader.gif\" /></div>");

    // initialise les tooltips d'aide
    $("img.tooltip_aide[title]").tooltip('#dynatooltip');

    // initialise les iframe overlay
    //cas de la visualisation des fiches reliées
    $("a.voir_fiche[rel]").overlay({
		expose: 'black',
		effect: 'apple',
		onBeforeLoad: function() {
			// grab wrapper element inside content
			var wrap = this.getContent().find(".contentWrap");
			// load the page specified in the trigger
			wrap.load(this.getTrigger().attr("href"));
		}
	});

	//cas du formulaire de saise d'une nouvelle fiche
    $("a.ajout_fiche[rel]").overlay({
		expose: 'black',
		effect: 'apple',
		onBeforeLoad: function() {
			// grab wrapper element inside content
			var wrap = this.getContent().find(".contentWrap");
			// load the page specified in the trigger
			wrap.load(this.getTrigger().attr("href"));
		},
		onLoad: function() {
			$('.contentWrap').before("<div id=\"dynatooltipiframe\">&nbsp;</div>");
			$(".contentWrap img.tooltip_aide[title]").tooltip('#dynatooltipiframe');
			$('.contentWrap #map').each(function() {initialize();});
		}
	});

	//fieldset pour faire des tabulations en édition
	$("#formulaire").before("<ul class='css-tabs'></ul>");
	//fieldset pour faire des tabulations en consultation
	$("h2.BAZ_titre").after("<ul class='css-tabs'></ul>");
	var nbtotal = $("fieldset.tab").size() - 1;
	$("#formulaire fieldset.tab").each(function(i) {
		$(this).addClass("tab" + i);
		if (i==0)
		{
			$(this).append('<button class="btn next" type="button" onClick=\'$("ul.css-tabs:first").tabs('+i+').next();if (divcarto) {	initialize(); };\'>Suivant &raquo;</button>');
		}
		else if (i==nbtotal)
		{
			$(this).append('<button class="btn prev" type="button" onClick=\'$("ul.css-tabs:first").tabs('+i+').prev();if (divcarto) {	initialize(); };\'>&laquo; Pr&eacute;c&eacute;dent</button><input type="submit" value="Valider" name="valider" class="btn bouton_sauver"/>');
		}
		else
		{
			$(this).append('<button class="btn prev" type="button" onClick=\'$("ul.css-tabs:first").tabs('+i+').prev();if (divcarto) {	initialize(); };\'>&laquo; Pr&eacute;c&eacute;dent</button><button class="btn next" type="button" onClick=\'$("ul.css-tabs:first").tabs('+i+').next();if (divcarto) {	initialize(); };\'>Suivant &raquo;</button>');
		}
	});
	$("fieldset.tab").each(function(i) {
		$('ul.css-tabs').append("<li class='liste" + i + "'><a href=\"#\">"+$(this).find('legend:first').hide().html()+"</a></li>");
	});
	if (nbtotal>0) {$(".groupebouton").hide();}

	//initialise tabulations
	//$("ul.css-tabs:first").attr({id:'tab_fiche'}).tabs("fieldset.tab", { onClick: function(){if (divcarto) {	initialize(); }} } );
	$("ul.css-tabs").tabs("fieldset.tab", { onClick: function(){if (divcarto) {	initialize(); }} } );

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
	$(".BAZ_cadre_fiche div[id^='oui'], .BAZ_cadre_fiche div[id^='non']").show();

	//on enleve la fonction doubleclic dans le cas d'une page contenant bazar
	$("div[ondblclick]").removeAttr("ondblclick");

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

	//=====================galerie d'images============================================================================
	var imagespourgalerie = $(".galerie a.triggerimage");
	if (imagespourgalerie.length > 0) {	
		imagespourgalerie.overlay({ target:'#gallery', expose:'#f1f1f1' }).gallery({ speed:500 });
	}
	
	//=====================bidouille pour que les widgets en flash restent en dessous des éléments en survol===========
	$("object").append('<param value="opaque" name="wmode">');$("embed").attr('wmode','opaque');

});
