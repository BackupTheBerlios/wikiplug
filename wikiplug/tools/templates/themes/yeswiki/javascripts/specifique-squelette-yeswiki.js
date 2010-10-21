/* Author: Florian Schmitt */ 
(function($){
	/* menu déroulant du haut pour la navigation, inspiré du tutoriel : http://net.tutsplus.com/tutorials/html-css-techniques/how-to-create-a-drop-down-nav-menu-with-html5-css3-and-jquery/ */				
	//cache nav
	var nav = $("#topnav");
	
	//add indicator and hovers to submenu parents
	nav.find("li").each(function() {
		if ($(this).find("ul").length > 0) {
			$("<span>").text("^").appendTo($(this).children(":first"));
	
			//show subnav on hover
			$(this).mouseenter(function() {
				$(this).find("ul").stop(true, true).slideDown();
			});
			
			//hide submenus on exit
			$(this).mouseleave(function() {
				$(this).find("ul").stop(true, true).slideUp();
			});
		}
	});
	nav.find("li:last").addClass('last');
})(jQuery);
