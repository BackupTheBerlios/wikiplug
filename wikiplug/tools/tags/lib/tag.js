/*
  @author: remy sharp / http://remysharp.com
  @url: http://remysharp.com/2007/12/28/jquery-tag-suggestion/
  @usage: setGlobalTags(['javascript', 'jquery', 'java', 'json']); // applied tags to be used for all implementations
          $('input.tags').tagSuggest(options);
          
          The selector is the element that the user enters their tag list
  @params:
    matchClass - class applied to the suggestions, defaults to 'tagMatches'
    tagContainer - the type of element uses to contain the suggestions, defaults to 'span'
    tagWrap - the type of element the suggestions a wrapped in, defaults to 'span'
    sort - boolean to force the sorted order of suggestions, defaults to false
    url - optional url to get suggestions if setGlobalTags isn't used.  Must return array of suggested tags
    tags - optional array of tags specific to this instance of element matches
    delay - optional sets the delay between keyup and the request - can help throttle ajax requests, defaults to zero delay
    separator - optional separator string, defaults to ' ' (Brian J. Cardiff)
  @license: Creative Commons License - ShareAlike http://creativecommons.org/licenses/by-sa/3.0/
  @version: 1.4
  @changes: fixed filtering to ajax hits
*/

(function ($) {
    var globalTags = [];

    // creates a public function within our private code.
    // tags can either be an array of strings OR
    // array of objects containing a 'tag' attribute
    window.setGlobalTags = function(tags /* array */) {
        globalTags = getTags(tags);
    };
    
    function getTags(tags) {
        var tag, i, goodTags = [];
        for (i = 0; i < tags.length; i++) {
            tag = tags[i];
            if (typeof tags[i] == 'object') {
                tag = tags[i].tag;
            } 
            goodTags.push(tag.toLowerCase());
        }
        
        return goodTags;
    }
    
    $.fn.tagSuggest = function (options) {
        var defaults = { 
            'matchClass' : 'tagMatches', 
            'tagContainer' : 'span', 
            'tagWrap' : 'span', 
            'sort' : true,
            'tags' : null,
            'url' : null,
            'delay' : 0,
            'separator' : ' '
        };

        var i, tag, userTags = [], settings = $.extend({}, defaults, options);

        if (settings.tags) {
            userTags = getTags(settings.tags);
        } else {
            userTags = globalTags;
        }

        return this.each(function () {
            var tagsElm = $(this);
            var elm = this;
            var matches, fromTab = false;
            var suggestionsShow = false;
            var workingTags = [];
            var currentTag = {"position": 0, tag: ""};
            var tagMatches = document.createElement(settings.tagContainer);
            
            function showSuggestionsDelayed(el, key) {
                if (settings.delay) {
                    if (elm.timer) clearTimeout(elm.timer);
                    elm.timer = setTimeout(function () {
                        showSuggestions(el, key);
                    }, settings.delay);
                } else {
                    showSuggestions(el, key);
                }
            }

            function showSuggestions(el, key) {
                workingTags = el.value.split(settings.separator);
                matches = [];
                var i, html = '', chosenTags = {}, tagSelected = false;

                // we're looking to complete the tag on currentTag.position (to start with)
                currentTag = { position: currentTags.length-1, tag: '' };
                
                for (i = 0; i < currentTags.length && i < workingTags.length; i++) {
                    if (!tagSelected && 
                        currentTags[i].toLowerCase() != workingTags[i].toLowerCase()) {
                        currentTag = { position: i, tag: workingTags[i].toLowerCase() };
                        tagSelected = true;
                    }
                    // lookup for filtering out chosen tags
                    chosenTags[currentTags[i].toLowerCase()] = true;
                }

                if (currentTag.tag) {
                    // collect potential tags
                    if (settings.url) {
                        $.ajax({
                            'url' : settings.url,
                            'dataType' : 'json',
                            'data' : { 'tag' : currentTag.tag },
                            'async' : false, // wait until this is ajax hit is complete before continue
                            'success' : function (m) {
                                matches = m;
                            }
                        });
                    } else {
                        for (i = 0; i < userTags.length; i++) {
                            if (userTags[i].indexOf(currentTag.tag) === 0) {
                                matches.push(userTags[i]);
                            }
                        }                        
                    }
                    
                    matches = $.grep(matches, function (v, i) {
                        return !chosenTags[v.toLowerCase()];
                    });

                    if (settings.sort) {
                        matches = matches.sort();
                    }                    

                    for (i = 0; i < matches.length; i++) {
                        html += '<' + settings.tagWrap + ' class="_tag_suggestion">' + matches[i] + '</' + settings.tagWrap + '>';
                    }

                    tagMatches.html(html);
                    suggestionsShow = !!(matches.length);
                } else {
                    hideSuggestions();
                }
            }

            function hideSuggestions() {
                tagMatches.empty();
                matches = [];
                suggestionsShow = false;
            }

            function setSelection() {
                var v = tagsElm.val();

                // tweak for hintted elements
                // http://remysharp.com/2007/01/25/jquery-tutorial-text-box-hints/
                if (v == tagsElm.attr('title') && tagsElm.is('.hint')) v = '';

                currentTags = v.split(settings.separator);
                hideSuggestions();
            }

            function chooseTag(tag) {
                var i, index;
                for (i = 0; i < currentTags.length; i++) {
                    if (currentTags[i].toLowerCase() != workingTags[i].toLowerCase()) {
                        index = i;
                        break;
                    }
                }

                if (index == workingTags.length - 1) tag = tag + settings.separator;

                workingTags[i] = tag;

                tagsElm.val(workingTags.join(settings.separator));
                tagsElm.blur().focus();
                setSelection();
            }

            function handleKeys(ev) {
                fromTab = false;
                var type = ev.type;
                var resetSelection = false;
                
                switch (ev.keyCode) {
                    case 37: // ignore cases (arrow keys)
                    case 38:
                    case 39:
                    case 40: {
                        hideSuggestions();
                        return true;
                    }
                    case 224:
                    case 17:
                    case 16:
                    case 18: {
                        return true;
                    }

                    case 8: {
                        // delete - hide selections if we're empty
                        if (this.value == '') {
                            hideSuggestions();
                            setSelection();
                            return true;
                        } else {
                            type = 'keyup'; // allow drop through
                            resetSelection = true;
                            showSuggestionsDelayed(this);
                        }
                        break;
                    }

                    case 9: // return and tab
                    case 13: {
                        if (suggestionsShow) {
                            // complete
                            chooseTag(matches[0]);
                            
                            fromTab = true;
                            return false;
                        } else {
                            return true;
                        }
                    }
                    case 27: {
                        hideSuggestions();
                        setSelection();
                        return true;
                    }
                    case 32: {
                        setSelection();
                        return true;
                    }
                }

                if (type == 'keyup') {
                    switch (ev.charCode) {
                        case 9:
                        case 13: {
                            return true;
                        }
                    }

                    if (resetSelection) { 
                        setSelection();
                    }
                    showSuggestionsDelayed(this, ev.charCode);            
                }
            }

            tagsElm.after(tagMatches).keypress(handleKeys).keyup(handleKeys).blur(function () {
                if (fromTab == true || suggestionsShow) { // tweak to support tab selection for Opera & IE
                    fromTab = false;
                    tagsElm.focus();
                }
            });

            // replace with jQuery version
            tagMatches = $(tagMatches).click(function (ev) {
                if (ev.target.nodeName == settings.tagWrap.toUpperCase() && $(ev.target).is('._tag_suggestion')) {
                    chooseTag(ev.target.innerHTML);
                }                
            }).addClass(settings.matchClass);

            // initialise
            setSelection();
        });
    };
})(jQuery);

// use globals to track mouse position
var hovertipMouseX;
var hovertipMouseY;
function hovertipMouseUpdate(e) {
  var mouse = hovertipMouseXY(e);
  hovertipMouseX = mouse[0];
  hovertipMouseY = mouse[1];
}

// http://www.howtocreate.co.uk/tutorials/javascript/eventinfo
function hovertipMouseXY(e) {
  if( !e ) {
    if( window.event ) {
      //Internet Explorer
      e = window.event;
    } else {
      //total failure, we have no way of referencing the event
      return;
    }
  }
  if( typeof( e.pageX ) == 'number' ) {
    //most browsers
    var xcoord = e.pageX;
    var ycoord = e.pageY;
  } else if( typeof( e.clientX ) == 'number' ) {
    //Internet Explorer and older browsers
    //other browsers provide this, but follow the pageX/Y branch
    var xcoord = e.clientX;
    var ycoord = e.clientY;
    var badOldBrowser = ( window.navigator.userAgent.indexOf( 'Opera' ) + 1 ) ||
      ( window.ScriptEngine && ScriptEngine().indexOf( 'InScript' ) + 1 ) ||
      ( navigator.vendor == 'KDE' );
    if( !badOldBrowser ) {
      if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
        //IE 4, 5 & 6 (in non-standards compliant mode)
        xcoord += document.body.scrollLeft;
        ycoord += document.body.scrollTop;
      } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
        //IE 6 (in standards compliant mode)
        xcoord += document.documentElement.scrollLeft;
        ycoord += document.documentElement.scrollTop;
      }
    }
  } else {
    //total failure, we have no way of obtaining the mouse coordinates
    return;
  }
  return [xcoord, ycoord];
}



//// target selectors ////

/**
 * These selectors find the targets for a given tooltip element.  
 * Several methods are supported.  
 * 
 * You may write your own selector functions to customize.
 */

/**
 * For this model:
 * <span hovertip="ht1">target term</span>...
 * <div class="hovertip" id="ht1">tooltip text</div>
 */
targetSelectById = function(el, config) {
  var id;
  var selector;
  if (id = el.getAttribute('id')) {
    selector = '*[@'+config.attribute+'='+id+']';
    return $(selector);
  }
};

/**
 * For this model:
 * <span id="ht1">target term</span>...
 * <div class="hovertip" target="ht1">tooltip text</div>
 */
targetSelectByTargetAttribute = function(el, config) {
  target_list = el.getAttribute('target');
  if (target_list) {
    // use for attribute to specify targets
    target_ids = target_list.split(' ');
    var selector = '#' + target_ids.join(',#');
    return $(selector);
  }
};

/**
 * For this model:
 * <span>target term</span><span class="hovertip">tooltip text</span>
 */
targetSelectByPrevious = function(el, config) {
  return $(el.previousSibling);
}

/**
 * Make all siblings targets.  Experimental.
 */
targetSelectBySiblings = function(el, config) {
  return $(el).siblings();
}

//// prepare tip elements ////

/**
 * The tooltip element needs special preparation.  You may define your own
 * prepare functions to cusomize the behavior.
 */

// adds a close link to clicktips
clicktipPrepareWithCloseLink = function(o, config) {
  return o.append("<a class='clicktip_close'><span>close</span></a>")
  .find('a.clicktip_close').click(function(e) {
      o.hide();
      return false;
    }).end(); 
};

// ensure that hovertips do not disappear when the mouse is over them.
// also position the hovertip as an absolutely positioned child of body.
hovertipPrepare = function(o, config) {
  return o.hover(function() {
      hovertipHideCancel(this);
    }, function() {
      hovertipHideLater(this);
    }).css('position', 'absolute').each(hovertipPosition);
};

// do not modify tooltips when preparing
hovertipPrepareNoOp = function(o, config) {
  return o;
}

//// manipulate tip elements /////
/**
 * A variety of functions to modify tooltip elements
 */

// move tooltips to body, so they are not descended from other absolutely
// positioned elements.
hovertipPosition = function(i) {
  document.body.appendChild(this);
}

hovertipIsVisible = function(el) {
  return (jQuery.css(el, 'display') != 'none');
}

// show the tooltip under the mouse.
// Introduce a delay, so tip appears only if cursor rests on target for more than an instant.
hovertipShowUnderMouse = function(el) {
  hovertipHideCancel(el);
  if (!hovertipIsVisible(el)) {
    el.ht.showing = // keep reference to timer
      window.setTimeout(function() {
          el.ht.tip.css({
              'position':'absolute',
                'top': hovertipMouseY + 'px',
                'left': hovertipMouseX + 'px'})
            .show();
        }, el.ht.config.showDelay);
  }
};

// do not hide
hovertipHideCancel = function(el) {
  if (el.ht.hiding) {
    window.clearTimeout(el.ht.hiding);
    el.ht.hiding = null;
  }  
};

// Hide a tooltip, but only after a delay.
// The delay allow the tip to remain when user moves mouse from target to tooltip
hovertipHideLater = function(el) {
  if (el.ht.showing) {
    window.clearTimeout(el.ht.showing);
    el.ht.showing = null;
  }
  if (el.ht.hiding) {
    window.clearTimeout(el.ht.hiding);
    el.ht.hiding = null;
  }
  el.ht.hiding = 
  window.setTimeout(function() {
      if (el.ht.hiding) {
        // fadeOut, slideUp do not work on Konqueror
        el.ht.tip.hide();
      }
    }, el.ht.config.hideDelay);
};


//// prepare target elements ////
/**
 * As we prepared the tooltip elements, the targets also need preparation.
 * 
 * You may define your own custom behavior.
 */

// when clicked on target, toggle visibilty of tooltip
clicktipTargetPrepare = function(o, el, config) {
  return o.addClass(config.attribute + '_target')
  .click(function() {
      el.ht.tip.toggle();
      return false;
    });
};

// when hover over target, make tooltip appear
hovertipTargetPrepare = function(o, el, config) {
  return o.addClass(config.attribute + '_target')
  .hover(function() {
      // show tip when mouse over target
      hovertipShowUnderMouse(el);
    },
    function() {
      // hide the tip
      // add a delay so user can move mouse from the target to the tip
      hovertipHideLater(el);
    });
};


/**
 * hovertipActivate() is our jQuery plugin function.  It turns on hovertip or
 * clicktip behavior for a set of elements.
 * 
 * @param config 
 * controls aspects of tooltip behavior.  Be sure to define
 * 'attribute', 'showDelay' and 'hideDelay'.
 * 
 * @param targetSelect
 * function finds the targets of a given tooltip element.
 * 
 * @param tipPrepare
 * function alters the tooltip to display and behave properly
 * 
 * @param targetPrepare
 * function alters the target to display and behave properly.
 */
jQuery.fn.hovertipActivate = function(config, targetSelect, tipPrepare, targetPrepare) {
  //alert('activating ' + this.size());
  // unhide so jquery show/hide will work.
  return this.css('display', 'block')
  .hide() // don't show it until click
  .each(function() {
      if (!this.ht)
        this.ht = new Object();
      this.ht.config = config;
      
      // find our targets
      var targets = targetSelect(this, config);
      if (targets && targets.size()) {
        if (!this.ht.targets)
          this.ht.targets = targetPrepare(targets, this, config);
        else
          this.ht.targets.add(targetPrepare(targets, this, config));
        
        // listen to mouse move events so we know exatly where to place hovetips
        targets.mousemove(hovertipMouseUpdate);
        
        // prepare the tooltip element
        // is it bad form to call $(this) here?
        if (!this.ht.tip)
          this.ht.tip = tipPrepare($(this), config);
      }
      
    })
  ;
};

/**
 * Here's an example ready function which shows how to enable tooltips.
 * 
 * You can make this considerably shorter by choosing only the markup style(s)
 * you will use.
 * 
 * You may also remove the code that wraps hovertips to produce drop-shadow FX
 * 
 * Invoke this function or one like it from your $(document).ready(). 
 *  
 *  Here, we break the action up into several timout callbacks, to avoid
 *  locking up browsers.
 */
function hovertipInit() {
  // specify the attribute name we use for our clicktips
  var clicktipConfig = {'attribute':'clicktip'};
  
  /**
   * To enable this style of markup (id on tooltip):
   * <span clicktip="foo">target</span>...
   * <div id="foo" class="clicktip">blah blah</div>
   */
  window.setTimeout(function() {
    $('.clicktip').hovertipActivate(clicktipConfig,
                                    targetSelectById,
                                    clicktipPrepareWithCloseLink,
                                    clicktipTargetPrepare);
  }, 0);
  
  /**
   * To enable this style of markup (id on target):
   * <span id="foo">target</span>...
   * <div target="foo" class="clicktip">blah blah</div>
   */
  window.setTimeout(function() {
    $('.clicktip').hovertipActivate(clicktipConfig,
                                    targetSelectByTargetAttribute,
                                    clicktipPrepareWithCloseLink,
                                    clicktipTargetPrepare);
  }, 0);
  
  // specify our configuration for hovertips, including delay times (millisec)
  var hovertipConfig = {'attribute':'hovertip',
                        'showDelay': 300,
                        'hideDelay': 400};
  
  // use <div class='hovertip'>blah blah</div>
  var hovertipSelect = 'ul.hovertip';
  
  /**
   * To enable this style of markup (id on tooltip):
   * <span hovertip="foo">target</span>...
   * <div id="foo" class="hovertip">blah blah</div>
   */
  /**
   * To enable this style of markup (id on target):
   * <span id="foo">target</span>...
   * <div target="foo" class="hovertip">blah blah</div>
   */
  window.setTimeout(function() {
    $(hovertipSelect).hovertipActivate(hovertipConfig,
                                       targetSelectByTargetAttribute,
                                       hovertipPrepare,
                                       hovertipTargetPrepare);
  }, 0);
  
  /**
   * This next section enables this style of markup:
   * <foo><span>target</span><span class="hovertip">blah blah</span></foo>
   * 
   * With drop shadow effect.
   * 
   */
  var hovertipSpanSelect = 'span.hovertip';
  // activate hovertips with wrappers for FX (drop shadow):
  $(hovertipSpanSelect).css('display', 'block').addClass('hovertip_wrap3').
    wrap("<span class='hovertip_wrap0'><span class='hovertip_wrap1'><span class='hovertip_wrap2'>" + 
         "</span></span></span>").each(function() {
           // fix class and attributes for newly wrapped elements
           var tooltip = this.parentNode.parentNode.parentNode;
           if (this.getAttribute('target'))
             tooltip.setAttribute('target', this.getAttribute('target'));
           if (this.getAttribute('id')) {
             var id = this.getAttribute('id');
             this.removeAttribute('id');
             tooltip.setAttribute('id', id);
           }
         });
  hovertipSpanSelect = 'span.hovertip_wrap0';

  window.setTimeout(function() {
    $(hovertipSpanSelect)
      .hovertipActivate(hovertipConfig,
                        targetSelectByPrevious,
                        hovertipPrepare,
                        hovertipTargetPrepare);
  }, 0);
}


$(document).ready(function() {
	//nettoyage des formulaires
	$.fn.clearForm = function() {
	    return this.each(function() {
	      var type = this.type, tag = this.tagName.toLowerCase();
	      if (tag == 'form')
	        return $(':input',this).clearForm();
	      if (type == 'text' || type == 'password' || tag == 'textarea')
	        this.value = '';
	      else if (type == 'checkbox' || type == 'radio')
	        this.checked = false;
	      else if (tag == 'select')
	        this.selectedIndex = -1;
	    });
	};
	
	// initialize tooltips in a seperate thread
	window.setTimeout(hovertipInit, 1);
	
	//bidouille antispam
	$(".antispam").attr('value', '1');
		
	//accordeon
	$("a.lien_titre_accordeon").live("click", function() {
		$(this).siblings(".accordeon_cache").toggle();
		return false;
	});
	
	//Afficher/cacher les commentaires
	$("strong.lien_commenter").css("cursor", "pointer").click(function() {
		$(this).next(".commentaires_billet_microblog").toggle().find(".commentaire_microblog").focus();
	});

	//ajax envoi de nouveaux commentaires
	$(".bouton_microblog").live("click", function() {
		var textcommentaire = $(this).prevAll(".commentaire_microblog").val();	
		var urlpost= $(this).parent("form").attr("action").replace('/addcomment','/ajaxaddcomment'+'&jsonp_callback=?'); 		
		$(this).parents(".microblogcommentform, .reponsecommentform").attr("id",'comments');	
		
		$.ajax({
			type: "POST",
			url: urlpost,
			data: { body: textcommentaire, antispam : "1" },
			dataType: "jsonp",		
			success: function(data){
				$("#comments").before(data.html).removeAttr("id");
				$(".microblogcommentform form").clearForm();
				$(".reponsecommentform").remove();
			}
		 });

		return false;
	});
	
	//ajax repondre à un commentaire			
	$("a.repondre_commentaire").live("click", function() {
		//on cache les formulaires déja ouverts et on reaffiche le contenu
		$(".comment_a_editer, .reponsecommentform").remove();
		$("#comments").show().removeAttr("id");	
		   
		$(this).parents(".comment").next(".commentreponses").append("<div class=\"reponsecommentform\">" +
			"<form action=\""+$(this).attr("href")+"/addcomment\" method=\"post\">" +
			"<input name=\"wiki\" value=\""+$(this).parents(".comment").prev("a").attr("name")+"/addcomment\" type=\"hidden\">" +
			"<textarea name=\"body\" class=\"commentaire_microblog\" rows=\"3\" cols=\"20\"></textarea><br>" +
			"<input class=\"bouton_microblog\" value=\"R&eacute;pondre\" accesskey=\"s\" type=\"button\">" +
			"<input class=\"bouton_annul\" type=\"button\" value=\"Annulation\" /></form>" +
			"</div>");

		$.scrollTo(".reponsecommentform", 800);
		$(this).parents(".comment").next(".commentreponses").find("textarea.commentaire_microblog").focus();											
		
		return false;		      
	});
	 
	//ajax edition commentaire			
	$("a.editer_commentaire").live("click", function() {
		//on cache les formulaires déja ouverts et on reaffiche le contenu
		$(".comment_a_editer, .reponsecommentform").remove();
		$("#comments").show().removeAttr("id");	
		
		//on attribut un id au div selectionne, afin de le retrouver
		$(this).parents(".comment").attr("id",'comments');		
		
		var urlpost= $(this).attr("href").replace('edit','ajaxedit')+'&jsonp_callback=?';					   
	   	$.getJSON(urlpost, {"commentaire" : "1"}, function(data) {
	   		if (data.nochange=='1') {
		     	$("#comments").show();
		    } else {
				//on affiche le contenu ajax
			    var ajoutajax = $("<div>").addClass("comment_a_editer").html(data.html).show();
			    $("#comments").after(ajoutajax);
			    $("#body").focus();
				$("#comments").hide();
		    }
		});										

		return false;		      
	});
	
	//annulation edition commentaire
	$("input.bouton_annul").live("click", function() {
		$(".comment_a_editer, .reponsecommentform").remove();
		$("#comments").show().removeAttr("id");		      
	});
	
	//sauvegarde commentaire
	$("input.bouton_submit").live("click", function() {
		var urlpost= $("#ACEditor").attr("action") + '&jsonp_callback=?' ;
		$(this).parents(".comment").attr("id",'comments');		
		$.getJSON(urlpost, { 
			"submit" : "Sauver",
			"commentaire" : "1",
			"wiki" : $("#ACEditor input[name='wiki']").val(),
			"previous" : $("#ACEditor input[name='previous']").val(),
			"body" : $("#ACEditor textarea[name='body']").val(),
		}, function(data) {				 
		    if (data.nochange=='1') {
		     	$("#comments").show();
		    } else {      	
		      	//on enleve le formulaire et on affiche le contenu ajax				      			
	      		$("#comments").before(data.html);
		      	$("#comments").remove();
	      	}
		    $(".comment_a_editer").remove();
	   	});
	});			
				
	//ajax suppression commentaire			
	$("a.supprimer_commentaire, a.supprimer_billet").live("click", function() {
		var urlget = $(this).attr('href').replace('deletepage','ajaxdeletepage')+'&jsonp_callback=?';
		$(this).parent().parent().attr("id",'commentasupp');
		
		if (confirm('Voulez vous vraiment supprimer cette entrée et ses commentaires associés?'))
		{
			$.getJSON(urlget, function(data) {				 
			    if (data.reponse=='succes') {
			    	$("#commentasupp").next(".commentreponses").remove();
			    	$("#commentasupp").remove();
			    } else {      	
			      	alert(data.reponse);
		      	}
		   	});
			return false;
		}
		else 
		{
			return false;
		}		      
	});

	//le formulaire du microblog est activé pour écriture directement
	$("textarea.microblog_billet").focus();
		
	//on efface tous les écrits restants dans le formulaire du billet microblog
	$('.btn_annuler').click(function(){
		$(this).parents("form").clearForm();
		var max = parseInt($('.microblog_billet').attr('maxlength'));
		$('.microblog_billet').focus().parent().find('.info_nb_car').html(max);
	});
	
	//on sauve le billet microblog en ajoutant l'antispam
	$('.btn_enregistrer').click(function(){
		$(this).parents("form").append("<input type=\"hidden\" name=\"antispam\" value=\"1\" />");	    
	});
	
	//on empeche d'aller au dela de la limite du nombre de caracteres
	$('.microblog_billet').keypress(function(){
		var max = parseInt($(this).attr('maxlength'));
		if($(this).val().length > max){
			$(this).val($(this).val().substr(0, $(this).attr('maxlength')));
		}

		$(this).parent().find('.info_nb_car').html((max - $(this).val().length));
	});
	
});

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