function slideSwitch() {
	var $active = $('#banniere img.active');
	if ( $active.length == 0 ) $active = $('#banniere img:last');

	// use this to pull the images in the order they appear in the markup
	var $next =  $active.next().length ? $active.next()	: $('#banniere img:first');
	
	// uncomment the 3 lines below to pull the images in random order	
	// var $sibs  = $active.siblings();
	// var rndNum = Math.floor(Math.random() * $sibs.length );
	// var $next  = $( $sibs[ rndNum ] );
		
	$active.addClass('last-active');
	
	$next.css({opacity: 0.0}).addClass('active').animate({opacity: 1.0}, 1000, function() {
		$active.removeClass('active last-active');
	});
}

jQuery(function(){
	//intervalle de changement d'image pour le diaporama
	setInterval( "slideSwitch()", 4000 );
	//menu deroulant du haut
	$('#menu_haut ul').addClass('sf-menu').superfish();
	//menu de gauche avec accordeon
	$("#menu_gauche > div.div_include > ul > li > ul, #menu_droite > div.div_include > ul > li > ul").hide();
	$("#menu_gauche > div.div_include > ul > li, #menu_droite > div.div_include > ul > li").children("ul").each(function(i) {
		$(this).prev("a").addClass("depliable").bind("click", function() {
			if ( $(this).hasClass("depliable") ) { $(this).removeClass("depliable").addClass("deplie"); }
			else {$(this).removeClass("deplie").addClass("depliable"); }
			$(this).next("ul").slideToggle('fast');
			return false;
		})
	});
});
