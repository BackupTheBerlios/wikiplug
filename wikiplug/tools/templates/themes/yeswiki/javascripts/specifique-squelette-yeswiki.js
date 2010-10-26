/* Author: Florian Schmitt */ 
(function($){
	/* menu déroulant du haut pour la navigation, inspiré du tutoriel : http://net.tutsplus.com/tutorials/html-css-techniques/how-to-create-a-drop-down-nav-menu-with-html5-css3-and-jquery/ */				
	//cache nav
	var nav = $("#topnav");
	
	//add indicator and hovers to submenu parents
	nav.find("li").each(function() {
		if ($(this).find("ul").length > 0) {
			if ($(this).parents("ul").length <= 1) {
				$("<span>").addClass('arrow-level1').html("&#9660;").appendTo($(this).children(":first"));
			}
			else {
				$("<span>").addClass('arrow-level'+$(this).parents("ul").length).html("	&#9658;").appendTo($(this).children(":first"));
			}
	
			//show subnav on hover
			$(this).mouseenter(function() {
				$(this).find("ul:first").stop(true, true).slideDown();
			});
			
			//hide submenus on exit
			$(this).mouseleave(function() {
				$(this).find("ul").stop(true, true).slideUp();
			});
		}
	});
	nav.find("ul").each(function() { 
		$(this).find("li:last").addClass('last');
	});
	
	/* Ajout de l'overlay pour le partage de page et l'envois par mail */
	$('#container').before('<div id="overlay-link" class="yeswiki-overlay" style="display:none"><div class="contentWrap" style="width:600px"></div></div>');
	$(".link_share, .link_mail").overlay({
		mask: '#999',
		onBeforeLoad: function() {
			// grab wrapper element inside content
			var wrap = this.getOverlay().find(".contentWrap");
	
			// load the page specified in the trigger
			var url = this.getTrigger().attr("href") + ' .page'
			wrap.load(url);
		}
	});
})(jQuery);
