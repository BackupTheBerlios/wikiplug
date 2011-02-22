/* Specifique eco2 jquery */

(function($){

      // Ajout de la classe actif sur les a et li de toute la hierarchie.
      $("#nav a.actif").parents("li").addClass("actif").children("a").addClass("actif");
      $("#nav a.actif").siblings().children("li").children("a").addClass("actif");
      $("#nav a:first.actif").addClass("first"); 
      $("#nav a:first.actif").addClass("first"); 


     /* Superfish */
     $("#nav ul:first").addClass('sf-menu sf-navbar'); 

     $("ul.sf-menu").superfish({ 
        pathClass:  'actif',
        pathLevels:    1 
     }); 

     $(".edit").css({'border':'0px'});


// En mode edition : pas de padding 
     if (($("#body").hasClass("edit"))) {
        $(".page").css({'padding':'0px 0px 0px 0px'});
     }



})(jQuery);
