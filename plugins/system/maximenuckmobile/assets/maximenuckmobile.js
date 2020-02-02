/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

(function($) {
	$.fn.MobileMaxiMenu = function(options) {
		var defaults = {
			useimages: false,
			container: 'body',
			showdesc: false,
			showlogo: true,
			usemodules: false,
			menuid: '',
			mobilemenutext: 'Menu',
			showmobilemenutext: '',
			titletag: 'h3',
			displaytype: 'flat',
			displayeffect: 'normal',
			menubarbuttoncontent : '',
			topbarbuttoncontent : '',
			uriroot : '',
			mobilebackbuttontext : 'Back',
			menuwidth : '300',
			openedonactiveitem : '1',
			loadanchorclass : '1',
			langdirection : 'ltr'
		};

		var opts = $.extend(defaults, options);
		var menu = this;
		var mobilemenu = $('#' + opts.menuid + '-mobile');

		// return menu.each(function(options) {
			if ($('#' + opts.menuid + '-mobile').length)
				return;
			if (menu.prev(opts.titletag))
				menu.prev(opts.titletag).addClass('hidemenumobileck');
			updatelevel(menu);
			mobilemaximenuinit();
			if (opts.displaytype == 'accordion')
				mobilemaximenuSetAccordeon();
			if (opts.displaytype == 'fade')
				mobilemaximenuSetFade();
			if (opts.displaytype == 'push')
				mobilemaximenuSetPush();

			function mobilemaximenuinit() {
				var activeitem, logoitem;
				if ($('.maxipushdownck', menu).length) {
					var menuitems = $(sortmenu(menu));
				} else {
					var menuitems = $('ul.maximenuck li', menu);
				}
				//$(document.body).append('<div id="'+opts.menuid+'-mobile" class="mobilemaximenuck"></div>');
				if (opts.container == 'body' 
					|| opts.container == 'topfixed'
					|| opts.displayeffect == 'slideleft'
					|| opts.displayeffect == 'slideright'
					|| opts.displayeffect == 'topfixed'
					) {
					$(document.body).append('<div id="' + opts.menuid + '-mobile" class="mobilemaximenuck ' + opts.langdirection + '"></div>');
				} else {
					menu.after($('<div id="' + opts.menuid + '-mobile" class="mobilemaximenuck"></div>'));
				}
				mobilemenu = $('#' + opts.menuid + '-mobile');
				mobilemenuHTML = '<div class="mobilemaximenucktopbar"><span class="mobilemaximenucktitle">' + opts.mobilemenutext + '</span><span class="mobilemaximenuckclose">' + opts.topbarbuttoncontent + '</span></div>';
				menuitems.each(function(i, itemtmp) {
					itemtmp = $(itemtmp);
					if (itemanchor = validateitem(itemtmp)
							) {
						mobilemenuHTML += '<div class="mobilemaximenuckitem">';
						// itemanchor = $('> a.maximenuck', itemtmp).length ? $('> a.maximenuck', itemtmp).clone() : $('> span.separator', itemtmp).clone();
						if (!opts.showdesc) {
							if ($('span.descck', itemanchor).length)
								$('span.descck', itemanchor).remove();
						}
						itemhref = itemanchor.attr('href') ? ' href="' + itemanchor.attr('href') + '"' : '';
						if (itemanchor.attr('target')) itemhref += ' target="' + itemanchor.attr('target') + '"';

						if (itemtmp.attr('data-mobiletext')) {
							$('span.titreck', itemanchor).html(itemtmp.attr('data-mobiletext'));
						}
						var itemmobileicon = '';
						if (itemtmp.attr('data-mobileicon')) {
							itemmobileicon = '<img class="mobileiconck" src="' + opts.uriroot + '/' + itemtmp.attr('data-mobileicon') + '" />';
						}
						// var itemanchorClass = '';
						var itemanchorClass = (opts.loadanchorclass == '1' && itemanchor.attr('class')) ? itemanchor.attr('class') : '';
						// check for specific class on the anchor to apply to the mobile menu
						if (itemanchor.hasClass('scrollTo') && opts.loadanchorclass != '1') {
							itemanchorClass += 'scrollTo';
						}
						itemanchorClass = (itemanchorClass) ? ' class="' + itemanchorClass + '"' : '';
						if (opts.useimages && ($('> * > img', itemtmp).length || $('> * > * > img', itemtmp).length)) {
							datatocopy = itemanchor.html();
							mobilemenuHTML += '<div class="' + itemtmp.attr('class') + '"><a ' + itemhref + itemanchorClass + '>' + itemmobileicon + '<span class="mobiletextck">' + datatocopy + '</span></a></div>';
						} else if (opts.usemodules && $('> div.maximenuck_mod', itemtmp).length) {
							datatocopy = $('> div.maximenuck_mod', itemtmp).html();
							mobilemenuHTML += '<div class="' + itemtmp.attr('class') + '">' + itemmobileicon + datatocopy + '</div>';
						} else {
							if (itemtmp.attr('data-mobiletext')) {
								datatocopy = itemtmp.attr('data-mobiletext');
							} else {
								datatocopy = $('> span.titreck', itemanchor).html();
							}
							mobilemenuHTML += '<div class="' + itemtmp.attr('class') + '"><a ' + itemhref + itemanchorClass + '>' + itemmobileicon + '<span class="mobiletextck">' + datatocopy + '</span></a></div>';
						}

						itemlevel = $(itemtmp).attr('data-level');
						j = i;
						while (menuitems[j + 1] && !validateitem(menuitems[j + 1]) && j < 1000) {
							j++;
						}
						if (menuitems[j + 1] && validateitem(menuitems[j + 1])) {
							itemleveldiff = $(menuitems[i]).attr('data-level') - $(menuitems[j + 1]).attr('data-level');
							if (itemleveldiff < 0) {
								mobilemenuHTML += '<div class="mobilemaximenucksubmenu">';
							}
							else if (itemleveldiff > 0) {
								mobilemenuHTML += '</div>';
								mobilemenuHTML += mobilemaximenu_strrepeat('</div></div>', itemleveldiff);
							} else {
								mobilemenuHTML += '</div>';
							}
						} else {
							mobilemenuHTML += mobilemaximenu_strrepeat('</div></div>', itemlevel);
						}

						if (itemtmp.hasClass('current'))
							activeitem = itemtmp.clone();
						if (!opts.showdesc) {
							if ($('span.descck', $(activeitem)).length)
								$('span.descck', $(activeitem)).remove();
						}
					} //else if ($(itemtmp).hasClass('maximenucklogo')) {
					//logoitem = $(itemtmp).clone();
					//}
				});

				mobilemenu.append(mobilemenuHTML);

				initCss(mobilemenu);

				if (activeitem && opts.showmobilemenutext != 'none' && opts.showmobilemenutext != 'custom') {
					if (opts.useimages) {
						activeitemtext = activeitem.find('a.maximenuck').html();
					} else {
						activeitemtext = activeitem.find('span.titreck').html();
					}
					if (!activeitemtext || activeitemtext == 'undefined')
						activeitemtext = opts.mobilemenutext;
				} else {
					activeitemtext = opts.mobilemenutext;
				}
				
				var position = (opts.container == 'body') ? 'absolute' : ( (opts.container == 'topfixed') ? 'fixed' : 'relative' );
				if (opts.container == 'topfixed') opts.container = 'body';
				var mobilebutton = '<div id="' + opts.menuid + '-mobilebarmaximenuck" class="mobilebarmaximenuck ' + opts.langdirection + '" style="position:' + position + '"><span class="mobilebarmenutitleck">' + activeitemtext + '</span>'
						+ '<div class="mobilebuttonmaximenuck">' + opts.menubarbuttoncontent + '</div>'
						+ '</div>';

				if (opts.container == 'body') {
					$(document.body).append(mobilebutton);
				} else {
					menu.after(mobilebutton);
					if (opts.displayeffect == 'normal' || opts.displayeffect == 'open')
						mobilemenu.css('position', 'relative');
					mobilemenu.parents('.nav-collapse').css('height', 'auto');
					mobilemenu.parents('.navigation').find('.navbar').css('display', 'none');
					$('#' + opts.menuid + '-mobilebarmaximenuck').parents('.nav-collapse').css('height', 'auto');
					$('#' + opts.menuid + '-mobilebarmaximenuck').parents('.navigation').find('.navbar').css('display', 'none');
				}
				
				if ($('.maximenucklogo', menu).length && opts.showlogo) {
					logoitem = $('.maximenucklogo', menu).clone();
					if (opts.showlogo == '2') {
						
						$('#' + opts.menuid + '-mobilebarmaximenuck').prepend('<div style="float:left;" class="' + $(logoitem).attr('class') + '">' + $(logoitem).html() + '</div>');
					} else if (opts.showlogo == '3') {
						$('.mobilemaximenucktopbar', mobilemenu).prepend('<div style="float:left;" class="' + $(logoitem).attr('class') + '">' + $(logoitem).html() + '</div>');
					} else {
						$('.mobilemaximenucktopbar', mobilemenu).after('<div class="' + $(logoitem).attr('class') + '">' + $(logoitem).html() + '<div style="clear:both;"></div></div>');
					}
				}
				
				$('#' + opts.menuid + '-mobilebarmaximenuck').click(function() {
					toggleMenu(opts.menuid);
				});
				$('.mobilemaximenuckclose', mobilemenu).click(function() {
					closeMenu(opts.menuid);
				});
				// close the menu when scroll is needed
				$('.scrollTo', mobilemenu).click(function() {
					closeMenu(opts.menuid);
				});

				$(window).on("click touchstart", function(event){
					var shallclose = true;
					$('.mobilemaximenuck').each(function() {
						$this = $(this);
						if ( 
							$this.has(event.target).length == 0 //checks if descendants of submenu was clicked
							&&
							!$this.is(event.target) //checks if the submenu itself was clicked
							&&
							$('#' + opts.menuid + '-mobilebarmaximenuck').has(event.target).length == 0
							&&
							!$('#' + opts.menuid + '-mobilebarmaximenuck').is(event.target)
							){
							// is outside
							// closeMenu(opts.menuid);
							// shallclose = true;
						} else {
							// is inside one of the mobile menus, do nothing
							shallclose = false;
						}
					});
					if (shallclose) closeMenu(opts.menuid);
				});
			}

			function mobilemaximenuSetAccordeon() {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.mobilemaximenucksubmenu', mobilemenu).hide();
				$('.mobilemaximenucksubmenu', mobilemenu).each(function(i, submenu) {
					submenu = $(submenu);
					itemparent = submenu.prev('.maximenuck');
					if ($('+ .mobilemaximenucksubmenu > div.mobilemaximenuckitem', itemparent).length)
						$(itemparent).append('<div class="mobilemaximenucktogglericon" />');
				});
				$('.mobilemaximenucktogglericon', mobilemenu).click(function() {
					itemparentsubmenu = $(this).parent().next('.mobilemaximenucksubmenu');
					if (itemparentsubmenu.css('display') == 'none') {
						itemparentsubmenu.css('display', 'block');
						$(this).parent().addClass('open');
					} else {
						itemparentsubmenu.css('display', 'none');
						$(this).parent().removeClass('open');
					}
				});
				// open the submenu on the active item
				if (opts.openedonactiveitem == '1') {
					$('.maximenuck.active:not(.current) > .mobilemaximenucktogglericon', mobilemenu).trigger('click');
				}
			}

			function mobilemaximenuSetFade() {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.mobilemaximenucktopbar', mobilemenu).prepend('<div class="mobilemaximenucktitle mobilemaximenuckbackbutton">'+opts.mobilebackbuttontext+'</div>');
				$('.mobilemaximenuckbackbutton', mobilemenu).hide();
				$('.mobilemaximenucksubmenu', mobilemenu).hide();
				$('.mobilemaximenucksubmenu', mobilemenu).each(function(i, submenu) {
					submenu = $(submenu);
					itemparent = submenu.prev('.maximenuck');
					if ($('+ .mobilemaximenucksubmenu > div.mobilemaximenuckitem', itemparent).length)
						$(itemparent).append('<div class="mobilemaximenucktogglericon" />');
				});
				$('.mobilemaximenucktogglericon', mobilemenu).click(function() {
						itemparentsubmenu = $(this).parent().next('.mobilemaximenucksubmenu');
						parentitem = $(this).parents('.mobilemaximenuckitem')[0];
						$('.ckopen', mobilemenu).removeClass('ckopen');
						$(itemparentsubmenu).addClass('ckopen');
						$('.mobilemaximenuckbackbutton', mobilemenu).fadeIn();
						$('.mobilemaximenucktitle:not(.mobilemaximenuckbackbutton)', mobilemenu).hide();
						// hides the current level items and displays the submenus
						$(parentitem).parent().find('> .mobilemaximenuckitem > div.maximenuck').fadeOut(function() {
							$('.ckopen', mobilemenu).fadeIn();
						});
				});
				$('.mobilemaximenucktopbar .mobilemaximenuckbackbutton', mobilemenu).click(function() {
					backbutton = this;
					$('.ckopen', mobilemenu).fadeOut(500, function() {
						$('.ckopen', mobilemenu).parent().parent().find('> .mobilemaximenuckitem > div.maximenuck').fadeIn();
						oldopensubmenu = $('.ckopen', mobilemenu);
						if (! $('.ckopen', mobilemenu).parents('.mobilemaximenucksubmenu').length) {
							$('.ckopen', mobilemenu).removeClass('ckopen');
							$('.mobilemaximenucktitle', mobilemenu).fadeIn();
							$(backbutton).hide();
						} else {
							$('.ckopen', mobilemenu).removeClass('ckopen');
							$(oldopensubmenu.parents('.mobilemaximenucksubmenu')[0]).addClass('ckopen');
						}
					});
					
				});
			}

			function mobilemaximenuSetPush() {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				mobilemenu.css('height', '100%');
				$('.mobilemaximenucktopbar', mobilemenu).prepend('<div class="mobilemaximenucktitle mobilemaximenuckbackbutton">'+opts.mobilebackbuttontext+'</div>');
				$('.mobilemaximenuckbackbutton', mobilemenu).hide();
				$('.mobilemaximenucksubmenu', mobilemenu).hide();
				// $('div.mobilemaximenuckitem', mobilemenu).css('position', 'relative');
				mobilemenu.append('<div class="mobilemaximenuckitemwrap" />');
				$('.mobilemaximenuckitemwrap', mobilemenu).css('position', 'absolute').width('100%');
				$('> div.mobilemaximenuckitem', mobilemenu).each(function(i, item) {
					$('.mobilemaximenuckitemwrap', mobilemenu).append(item);
				});
				zindex = 10;
				$('.mobilemaximenucksubmenu', mobilemenu).each(function(i, submenu) {
					submenu = $(submenu);
					itemparent = submenu.prev('.maximenuck');
					submenu.css('left', '100%' ).css('width', '100%' ).css('top', '0' ).css('position', 'absolute').css('z-index', zindex);
					if ($('+ .mobilemaximenucksubmenu > div.mobilemaximenuckitem', itemparent).length)
						$(itemparent).append('<div class="mobilemaximenucktogglericon" />');
					zindex++;
				});
				$('.mobilemaximenucktogglericon', mobilemenu).click(function() {
						itemparentsubmenu = $(this).parent().next('.mobilemaximenucksubmenu');
						parentitem = $(this).parents('.mobilemaximenuckitem')[0];
						$(parentitem).parent().find('.mobilemaximenucksubmenu').hide();
						$('.ckopen', mobilemenu).removeClass('ckopen');
						$(itemparentsubmenu).addClass('ckopen');
						$('.mobilemaximenuckbackbutton', mobilemenu).fadeIn();
						$('.mobilemaximenucktitle:not(.mobilemaximenuckbackbutton)', mobilemenu).hide();
						$('.ckopen', mobilemenu).fadeIn();
						$('.mobilemaximenuckitemwrap', mobilemenu).animate({'left': '-=100%' });
				});
				$('.mobilemaximenucktopbar .mobilemaximenuckbackbutton', mobilemenu).click(function() {
					backbutton = this;
					$('.mobilemaximenuckitemwrap', mobilemenu).animate({'left': '+=100%' });
					// $('.ckopen', mobilemenu).fadeOut(500, function() {
						// $('.ckopen', mobilemenu).parent().parent().find('> .mobilemaximenuckitem > div.maximenuck').fadeIn();
						oldopensubmenu = $('.ckopen', mobilemenu);
						if (! $('.ckopen', mobilemenu).parents('.mobilemaximenucksubmenu').length) {
							$('.ckopen', mobilemenu).removeClass('ckopen').hide();
							$('.mobilemaximenucktitle', mobilemenu).fadeIn();
							$(backbutton).hide();
						} else {
							$('.ckopen', mobilemenu).removeClass('ckopen').hide();
							$(oldopensubmenu.parents('.mobilemaximenucksubmenu')[0]).addClass('ckopen');
						}
					// });
					
				});
			}

			function resetFademenu(menu) {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.mobilemaximenucksubmenu', mobilemenu).hide();
				$('.mobilemaximenuckitem > div.maximenuck').show().removeClass('open');
				$('.mobilemaximenucktopbar .mobilemaximenucktitle').show();
				$('.mobilemaximenucktopbar .mobilemaximenucktitle.mobilemaximenuckbackbutton').hide();
			}

			function resetPushmenu(menu) {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.mobilemaximenucksubmenu', mobilemenu).hide();
				$('.mobilemaximenuckitemwrap', mobilemenu).css('left', '0');
				$('.mobilemaximenucktopbar .mobilemaximenucktitle:not(.mobilemaximenuckbackbutton)').show();
				$('.mobilemaximenucktopbar .mobilemaximenucktitle.mobilemaximenuckbackbutton').hide();
			}

			function updatelevel(menu) {
				$('div.maximenuck_mod', menu).each(function(i, module) {
					module = $(module);
					liparentlevel = module.parent('li.maximenuckmodule').attr('data-level');
					$('li.maximenuck', module).each(function(j, li) {
						li = $(li);
						lilevel = parseInt(li.attr('data-level')) + parseInt(liparentlevel) - 1;
						li.attr('data-level', lilevel).addClass('level' + lilevel);
					});
				});
			}

			function validateitem(itemtmp) {
				if (!itemtmp || $(itemtmp).hasClass('nomobileck'))
					return false;
//				if (($('> a.maximenuck', itemtmp).length || $('> span.separator', itemtmp).length || $('> * > a.maximenuck', itemtmp).length || $('> * > span.separator', itemtmp).length)
//							&& ($('> a.maximenuck > span.titreck', itemtmp).length || $('> span.separator > span.titreck', itemtmp).length || opts.useimages)
//							|| ($('> div.maximenuck_mod', itemtmp).length && opts.usemodules)
//							&& !itemtmp.hasClass('nomobileck')
//							)
//					if ($('> * > img', itemtmp).length && opts.useimages) {
//						return $('> *', itemtmp).clone();
				if ($('> * > img', itemtmp).length && !opts.useimages && !$('> * > span.titreck', itemtmp).length) {
					return false
				}
				if ($('> a.maximenuck', itemtmp).length)
					return $('> a.maximenuck', itemtmp).clone();
				if ($('> span.separator,> span.nav-header', itemtmp).length)
					return $('> span.separator,> span.nav-header', itemtmp).clone();
				if ($('> * > a.maximenuck', itemtmp).length)
					return $('> * > a.maximenuck', itemtmp).clone();
				if ($('> * > span.separator,> * > span.nav-header', itemtmp).length)
					return $('> * > span.separator,> * >  span.nav-header', itemtmp).clone();
				if ($('> div.maximenuck_mod', itemtmp).length && opts.usemodules)
					return $('> div.maximenuck_mod', itemtmp).clone();

//					if ($('> * > * > img', itemtmp).length && opts.useimages) return $('> * > *', itemtmp).clone();
//					return $('> a.maximenuck', itemtmp).length ? $('> a.maximenuck', itemtmp).clone() : $('> span.separator', itemtmp).clone();
				return false;
			}

			function mobilemaximenu_strrepeat(string, count) {
			var s = '';
				if (count < 1)
					return '';
				while (count > 0) {
					s += string;
					count--;
				}
				return s;
			}

			function sortmenu(menu) {
				var items = new Array();
				mainitems = $('ul.maximenuck > li.maximenuck.level1', menu);
				j = 0;
				mainitems.each(function(ii, mainitem) {
					items.push(mainitem);
					if ($(mainitem).hasClass('parent')) {
						subitemcontainer = $('.maxipushdownck > .floatck', menu).eq(j);
						subitems = $('li.maximenuck', subitemcontainer);
						subitems.each(function(k, subitem) {
							items.push(subitem);
						});
						j++;
					}
				});
				return items;
			}

			function initCss(mobilemenu) {
				switch (opts.displayeffect) {
					case 'normal':
					default:
						mobilemenu.css({
							'position': 'absolute',
							'z-index': '100000',
							'top': '0',
							'left': '0',
							'display': 'none'
						});
						break;
					case 'slideleft':
					case 'slideleftover':
						mobilemenu.css({
							'position': 'fixed',
							'overflow-y': 'auto',
							'overflow-x': 'hidden',
							'z-index': '100000',
							'top': '0',
							'left': '0',
							'width': opts.menuwidth,
							'height': '100%',
							'display': 'none'
						});
						break;
					case 'slideright':
					case 'sliderightover':
						mobilemenu.css({
							'position': 'fixed',
							'overflow-y': 'auto',
							'overflow-x': 'hidden',
							'z-index': '100000',
							'top': '0',
							'right': '0',
							'left': 'auto',
							'width': opts.menuwidth,
							'height': '100%',
							'display': 'none'
						});
						break;
					case 'topfixed':
						mobilemenu.css({
							'position': 'fixed',
							'overflow-y': 'scroll',
							'z-index': '100000',
							'top': '0',
							'right': '0',
							'left': '0',
							'max-height': '100%',
							'display': 'none'
						});
						break;
				}
			}

			function toggleMenu(menuid) {
				if ($('#' + menuid + '-mobile').css('display') == 'block') {
					closeMenu(menuid);
				} else {
					openMenu(menuid);
				}
			}

			function openMenu(menuid) {
				mobilemenu = $('#' + menuid + '-mobile');
//				mobilemenu.show();
				switch (opts.displayeffect) {
					case 'normal':
					default:
						mobilemenu.fadeOut();
						$('#' + opts.menuid + '-mobile').fadeIn();
						if (opts.container != 'body')
							$('#' + opts.menuid + '-mobilebarmaximenuck').css('display', 'none');
						break;
					case 'slideleft':
					case 'slideleftover':
						mobilemenu.css('display', 'block').css('opacity', '0').css('width', '0').animate({'opacity': '1', 'width': opts.menuwidth});
						if (opts.displayeffect =='slideleft')$('body').css('position', 'relative').animate({'left': opts.menuwidth});
						break;
					case 'slideright':
					case 'sliderightover':
						mobilemenu.css('display', 'block').css('opacity', '0').css('width', '0').animate({'opacity': '1', 'width': opts.menuwidth});
						if (opts.displayeffect =='slideright') $('body').css('position', 'relative').animate({'right': opts.menuwidth});
						break;
					case 'open':
						// mobilemenu..slideDown();
						$('#' + opts.menuid + '-mobile').slideDown('slow');
						if (opts.container != 'body')
							$('#' + opts.menuid + '-mobilebarmaximenuck').css('display', 'none');
						break;
				}
				$(document).trigger('maximenuckmobile_open');
			}

			function closeMenu(menuid) {
				if (opts.displaytype == 'fade') {
					resetFademenu();
				}
				if (opts.displaytype == 'push') {
					resetPushmenu();
				}
				mobilemenu = $('#' + menuid + '-mobile');
				switch (opts.displayeffect) {
					case 'normal':
					default:
						$('#' + opts.menuid + '-mobile').fadeOut();
						if (opts.container != 'body')
							$('#' + opts.menuid + '-mobilebarmaximenuck').css('display', '');
						break;
					case 'slideleft':
						mobilemenu.animate({'opacity': '0', 'width': '0'}, function() {
							mobilemenu.css('display', 'none');
						});
						$('body').animate({'left': '0'}, function() {
							$('body').css('position', '')
						});
						break;
					case 'slideright':
						mobilemenu.animate({'opacity': '0', 'width': '0'}, function() {
							mobilemenu.css('display', 'none');
						});
						$('body').animate({'right': '0'}, function() {
							$('body').css('position', '')
						});
						break;
					case 'open':
						$('#' + opts.menuid + '-mobile').slideUp('slow', function() {
							if (opts.container != 'body')
								$('#' + opts.menuid + '-mobilebarmaximenuck').css('display', '');
						});
						
						break;
				}
				$(document).trigger('maximenuckmobile_close');
			}
		// });
	}
})(jQuery);