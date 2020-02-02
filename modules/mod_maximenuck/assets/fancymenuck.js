/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

(function($) {

	//define the defaults for the plugin and how to call it	
	$.fn.FancyMaxiMenu = function(options) {
		//set default options  
		var defaults = {
			fancyTransition: 'linear',
			fancyDuree: 500
		};

		var options = $.extend(defaults, options);
		var maximenuObj = this;

		//act upon the element that is passed into the design    
		return maximenuObj.each(function(options) {

			var fancyTransition = defaults.fancyTransition;
			var fancyDuree = defaults.fancyDuree;

			fancymaximenuInit();

			function fancymaximenuInit() {
				if ($('li.active.level1', maximenuObj).length) {
					maximenuObj.currentItem = $('li.active.level1', maximenuObj);
				} else {
					maximenuObj.currentItem = $('li.hoverbgactive.level1', maximenuObj);
				}

				if (!maximenuObj.currentItem.length) {
					$('li.level1', maximenuObj).each(function(i, el) {
						el = $(el);
						el.mouseenter(function() {
							if (!$('li.hoverbgactive', maximenuObj).length) {
								el.addClass('hoverbgactive');
								maximenuObj.FancyMaxiMenu({fancyTransition: fancyTransition, fancyDuree: fancyDuree});
							}

							//currentItem = this;

						});
					});
				}

				// if no active element in the menu, get out
				if (!$('.active', maximenuObj).length && !$('.hoverbgactive', maximenuObj).length)
					return false;


				$('ul.maximenuck', maximenuObj).append('<li class="maxiFancybackground"><div class="maxiFancycenter"><div class="maxiFancyleft"><div class="maxiFancyright"></div></div></div></li>');
				fancyItem = $('.maxiFancybackground', maximenuObj);

				if (maximenuObj.currentItem)
					setCurrent(maximenuObj.currentItem);

				$('li.level1', maximenuObj).each(function(i, el) {
					el = $(el);
					el.mouseenter(function() {
						moveFancyck(el);
					});
					el.mouseleave(function() {
						if (!$('li.active', maximenuObj).length) {
							$('.maxiFancybackground', maximenuObj).stop(false, false).animate({left: 0, width: 0}, {duration: fancyDuree, easing: fancyTransition});
						} else {
							moveFancyck($(maximenuObj.currentItem));
						}
					});
				});
			}

			function moveFancyck(toEl) {
				var toEl_left = toEl.position().left + parseInt(toEl.css('marginLeft'));
				var toEl_width = toEl.outerWidth();
				$('.maxiFancybackground', maximenuObj).stop(false, false).animate({left: toEl_left, width: toEl_width}, {duration: fancyDuree, easing: fancyTransition});
			}

			function setCurrent(el) {
				el = $(el);
				//Retrieve the selected item position and width
				var default_left = Math.round(el.position().left) + parseInt(el.css('marginLeft'));
				var default_width = el.outerWidth();

				//Set the floating bar position and width
				$('.maxiFancybackground', maximenuObj).stop(false, false).animate({left: default_left, width: default_width}, {duration: fancyDuree, easing: fancyTransition});
			}
		});
	};
})(jQuery);