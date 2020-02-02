/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

(function($) {
	$.fn.MobileAccordeonMenu = function(options) {
		var defaults = {
			useimages: false,
			container: 'body',
			showdesc: false,
			usemodules: false,
			menuid: '',
			mobilemenutext: 'Menu',
			showmobilemenutext: '',
			titletag: 'h3',
			displaytype: 'flat',
			displayeffect: 'normal',
			mobilebackbuttontext : 'Back'
		};

		var opts = $.extend(defaults, options);
		var menu = this;

		return menu.each(function(options) {
			if ($('#' + opts.menuid + '-mobile').length)
				return;
			if (menu.prev(opts.titletag))
				menu.prev(opts.titletag).addClass('hidemenumobileck');
			updatelevel(menu);
			mobileaccordeonckinit();
			if (opts.displaytype == 'accordion')
				mobileaccordeonckSetAccordeon();
			if (opts.displaytype == 'fade')
				mobileaccordeonckSetFade();
			if (opts.displaytype == 'push')
				mobileaccordeonckSetPush();

			function mobileaccordeonckinit() {
				var activeitem, logoitem;
				var menuitems = $('li.accordeonck', menu);
//				$(document.body).append('<div id="'+opts.menuid+'-mobile" class="mobileaccordeonck"></div>');
				if (opts.container == 'body' 
					|| opts.container == 'topfixed'
					|| opts.displayeffect == 'slideleft'
					|| opts.displayeffect == 'slideright'
					|| opts.displayeffect == 'topfixed'
					) {
					$(document.body).append('<div id="' + opts.menuid + '-mobile" class="mobileaccordeonck"></div>');
				} else {
					menu.after($('<div id="' + opts.menuid + '-mobile" class="mobileaccordeonck"></div>'));
				}
				mobilemenu = $('#'+opts.menuid+'-mobile');
				mobilemenuHTML = '<div class="topbar"><span class="mobileaccordeoncktitle">' + opts.mobilemenutext + '</span><span class="mobileaccordeonckclose"></span></div>';
				menuitems.each(function(i, itemtmp) {
					itemtmp = $(itemtmp);
					if (itemanchor = validateitem(itemtmp)
							) {
						mobilemenuHTML += '<div class="mobileaccordeonckitem">';
						//itemanchor = $('> a', itemtmp).length ? $('> a', itemtmp).clone() : $('> span.separator', itemtmp).clone();
						if (!opts.showdesc) {
							if ($('span.accordeonckdesc', itemanchor).length)
								$('span.accordeonckdesc', itemanchor).remove();
						}
						itemhref = itemanchor.attr('href') ? ' href="' + itemanchor.attr('href') + '"' : '';
//						if (itemtmp.attr('data-mobiletext')) {
//							$('span.titreck', itemanchor).html('<span class="mobiletextck">' + itemtmp.attr('data-mobiletext') + '</span>');
//						}
						var itemmobileicon = '';
						if (itemtmp.attr('data-mobileicon')) {
							itemmobileicon = '<img class="mobileiconck" src="' + itemtmp.attr('data-mobileicon') + '" />';
						}
						var itemanchorClass = '';
						// check for specific class on the anchor to apply to the mobile menu
						if (itemanchor.hasClass('scrollTo')) {
							itemanchorClass += 'scrollTo';
						}
						if (opts.useimages && ($('> a > img', itemtmp).length || $('> span.separator > img', itemtmp).length)) {
							datatocopy = itemanchor.html();
							mobilemenuHTML +='<div class="' + itemtmp.attr('class') + '"><a ' + itemhref + '>' + datatocopy + '</a></div>';
						} else if (opts.usemodules && $('> div.accordeonckmod', itemtmp).length) {
							datatocopy = $('> div.accordeonckmod', itemtmp).html();
							mobilemenuHTML +='<div class="' + itemtmp.attr('class') + '">' + datatocopy + '</div>';
						} else {
							datatocopy = $('> span.image-title', itemanchor).length ? $('> span.image-title', itemanchor).html() : itemanchor.html();
							mobilemenuHTML +='<div class="' + itemtmp.attr('class') + '"><a ' + itemhref + '>' + datatocopy + '</a></div>';
						}

						itemlevel = $(itemtmp).attr('data-level');
						j = i;
						while (menuitems[j+1] && !validateitem(menuitems[j+1]) && j < 1000) {
							j++;
						}
						if (menuitems[j+1] && validateitem(menuitems[j+1])) {
							itemleveldiff = $(menuitems[i]).attr('data-level') - $(menuitems[j+1]).attr('data-level');
							if (itemleveldiff < 0) {
								mobilemenuHTML += '<div class="mobileaccordeoncksubmenu">';
							}
							else if (itemleveldiff > 0) {
								mobilemenuHTML += '</div>';
								mobilemenuHTML += mobileaccordeonck_strrepeat('</div>', itemleveldiff);
							} else {
								mobilemenuHTML += '</div>';
							}
						} else {
							mobilemenuHTML += mobileaccordeonck_strrepeat('</div>', itemlevel);
						}

						if (itemtmp.hasClass('current'))
							activeitem = itemtmp.clone();
						if (!opts.showdesc) {
							if ($('span.accordeonckdesc', $(activeitem)).length)
								$('span.accordeonckdesc', $(activeitem)).remove();
						}
					}
				});
				
				mobilemenu.append(mobilemenuHTML);

				initCss(mobilemenu);

				if (activeitem && opts.showmobilemenutext != 'none' && opts.showmobilemenutext != 'custom') {
					if (opts.useimages) {
						activeitemtext = $('> a', activeitem).html();
					} else {
						activeitemtext = $('> a', activeitem).html();
					}
					if (!activeitemtext || activeitemtext == 'undefined') activeitemtext = opts.mobilemenutext;
				} else {
					activeitemtext = opts.mobilemenutext;
				}
				if (logoitem && opts.showlogo) {
					$('.topbar', mobilemenu).after('<div class="' + $(logoitem).attr('class') + '">'+$(logoitem).html()+'<div style="clear:both;"></div></div>')
				}
				var position = (opts.container == 'body') ? 'absolute' : ( (opts.container == 'topfixed') ? 'fixed' : 'relative' );
				if (opts.container == 'topfixed') opts.container = 'body';
				var mobilebutton = '<div id="'+opts.menuid+'-mobilebarmenuck" class="mobilebaraccordeonmenuck" style="position:' + position + '"><span class="mobilebarmenutitleck">' + activeitemtext + '</span>'
						+ '<div class="mobilebuttonmenuck"></div>'
						+ '</div>';

				if (opts.container == 'body') {
					$(document.body).append(mobilebutton);
				} else {
					menu.after(mobilebutton);
					if (opts.displayeffect == 'normal' || opts.displayeffect == 'open')
						mobilemenu.css('position', 'static');
				}
				$('#' + opts.menuid + '-mobilebarmenuck').click(function() {
//					$('.mobileaccordeonck').fadeOut();
//					$('#'+opts.menuid+'-mobile').fadeIn();
					openMenu(opts.menuid);
				});
				$('.mobileaccordeonckclose', mobilemenu).click(function() {
					closeMenu(opts.menuid);
				});
				// close the menu when scroll is needed
				$('.scrollTo', mobilemenu).click(function() {
					closeMenu(opts.menuid);
				});
			}
			
			function mobileaccordeonckSetAccordeon() {
				mobilemenu = $('#'+opts.menuid+'-mobile');
				$('.mobileaccordeoncksubmenu', mobilemenu).hide();
				$('.accordeonck.parent', mobilemenu).each(function(i, itemparent) {
					if ($('+ .mobileaccordeoncksubmenu > div.mobileaccordeonckitem', itemparent).length)
						$(itemparent).append('<div class="mobileaccordeoncktogglericon" />');
				});
				$('.accordeonck.parent > .mobileaccordeoncktogglericon', mobilemenu).click(function() {
					itemparentsubmenu = $(this).parent().next('.mobileaccordeoncksubmenu');
					if (itemparentsubmenu.css('display') == 'none') {
						itemparentsubmenu.css('display', 'block');
						$(this).parent().addClass('open');
					} else {
						itemparentsubmenu.css('display', 'none');
						$(this).parent().removeClass('open');
					}
				});
			}

			function mobileaccordeonckSetFade() {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.topbar', mobilemenu).prepend('<div class="mobileaccordeoncktitle ckbackbutton">'+opts.mobilebackbuttontext+'</div>');
				$('.ckbackbutton', mobilemenu).hide();
				$('.mobileaccordeoncksubmenu', mobilemenu).hide();
				$('.mobileaccordeoncksubmenu', mobilemenu).each(function(i, submenu) {
					submenu = $(submenu);
					itemparent = submenu.prev('.accordeonck');
					if ($('+ .mobileaccordeoncksubmenu > div.mobileaccordeonckitem', itemparent).length)
						$(itemparent).append('<div class="mobileaccordeoncktogglericon" />');
				});
				$('.mobileaccordeoncktogglericon', mobilemenu).click(function() {
						itemparentsubmenu = $(this).parent().next('.mobileaccordeoncksubmenu');
						parentitem = $(this).parents('.mobileaccordeonckitem')[0];
						$('.ckopen', mobilemenu).removeClass('ckopen');
						$(itemparentsubmenu).addClass('ckopen');
						$('.ckbackbutton', mobilemenu).fadeIn();
						$('.mobileaccordeoncktitle:not(.ckbackbutton)', mobilemenu).hide();
						// hides the current level items and displays the submenus
						$(parentitem).parent().find('> .mobileaccordeonckitem > div.accordeonck').fadeOut(function() {
							$('.ckopen', mobilemenu).fadeIn();
						});
				});
				$('.topbar .ckbackbutton', mobilemenu).click(function() {
					backbutton = this;
					$('.ckopen', mobilemenu).fadeOut(500, function() {
						$('.ckopen', mobilemenu).parent().parent().find('> .mobileaccordeonckitem > div.accordeonck').fadeIn();
						oldopensubmenu = $('.ckopen', mobilemenu);
						if (! $('.ckopen', mobilemenu).parents('.mobileaccordeoncksubmenu').length) {
							$('.ckopen', mobilemenu).removeClass('ckopen');
							$('.mobileaccordeoncktitle', mobilemenu).fadeIn();
							$(backbutton).hide();
						} else {
							$('.ckopen', mobilemenu).removeClass('ckopen');
							$(oldopensubmenu.parents('.mobileaccordeoncksubmenu')[0]).addClass('ckopen');
						}
					});
					
				});
			}

			function mobileaccordeonckSetPush() {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				mobilemenu.css('height', '100%');
				$('.topbar', mobilemenu).prepend('<div class="mobileaccordeoncktitle ckbackbutton">'+opts.mobilebackbuttontext+'</div>');
				$('.ckbackbutton', mobilemenu).hide();
				$('.mobileaccordeoncksubmenu', mobilemenu).hide();
				// $('div.mobileaccordeonckitem', mobilemenu).css('position', 'relative');
				mobilemenu.append('<div id="mobileaccordeonckitemwrap" />');
				$('#mobileaccordeonckitemwrap', mobilemenu).css('position', 'absolute').width('100%');
				$('> div.mobileaccordeonckitem', mobilemenu).each(function(i, item) {
					$('#mobileaccordeonckitemwrap', mobilemenu).append(item);
				});
				zindex = 10;
				$('.mobileaccordeoncksubmenu', mobilemenu).each(function(i, submenu) {
					submenu = $(submenu);
					itemparent = submenu.prev('.accordeonck');
					submenu.css('left', '100%' ).css('width', '100%' ).css('top', '0' ).css('position', 'absolute').css('z-index', zindex);
					if ($('+ .mobileaccordeoncksubmenu > div.mobileaccordeonckitem', itemparent).length)
						$(itemparent).append('<div class="mobileaccordeoncktogglericon" />');
					zindex++;
				});
				$('.mobileaccordeoncktogglericon', mobilemenu).click(function() {
						itemparentsubmenu = $(this).parent().next('.mobileaccordeoncksubmenu');
						parentitem = $(this).parents('.mobileaccordeonckitem')[0];
						$(parentitem).parent().find('.mobileaccordeoncksubmenu').hide();
						$('.ckopen', mobilemenu).removeClass('ckopen');
						$(itemparentsubmenu).addClass('ckopen');
						$('.ckbackbutton', mobilemenu).fadeIn();
						$('.mobileaccordeoncktitle:not(.ckbackbutton)', mobilemenu).hide();
						$('.ckopen', mobilemenu).fadeIn();
						$('#mobileaccordeonckitemwrap', mobilemenu).animate({'left': '-=100%' });
				});
				$('.topbar .ckbackbutton', mobilemenu).click(function() {
					backbutton = this;
					$('#mobileaccordeonckitemwrap', mobilemenu).animate({'left': '+=100%' });
					// $('.ckopen', mobilemenu).fadeOut(500, function() {
						// $('.ckopen', mobilemenu).parent().parent().find('> .mobileaccordeonckitem > div.accordeonck').fadeIn();
						oldopensubmenu = $('.ckopen', mobilemenu);
						if (! $('.ckopen', mobilemenu).parents('.mobileaccordeoncksubmenu').length) {
							$('.ckopen', mobilemenu).removeClass('ckopen');
							$('.mobileaccordeoncktitle', mobilemenu).fadeIn();
							$(backbutton).hide();
						} else {
							$('.ckopen', mobilemenu).removeClass('ckopen');
							$(oldopensubmenu.parents('.mobileaccordeoncksubmenu')[0]).addClass('ckopen');
						}
					// });
					
				});
			}

			function resetFademenu(menu) {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.mobileaccordeoncksubmenu', mobilemenu).hide();
				$('.mobileaccordeonckitem > div.accordeonck').show().removeClass('open');
				$('.topbar .mobileaccordeoncktitle').show();
				$('.topbar .mobileaccordeoncktitle.ckbackbutton').hide();
			}

			function resetPushmenu(menu) {
				mobilemenu = $('#' + opts.menuid + '-mobile');
				$('.mobileaccordeoncksubmenu', mobilemenu).hide();
				$('#mobileaccordeonckitemwrap', mobilemenu).css('left', '0');
				$('.topbar .mobileaccordeoncktitle:not(.ckbackbutton)').show();
				$('.topbar .mobileaccordeoncktitle.ckbackbutton').hide();
			}

			function updatelevel(menu) {
				$('div.accordeonck_mod', menu).each(function(i, module) {
					module = $(module);
					liparentlevel = module.parent('li.accordeonckmodule').attr('data-level');
					$('li.accordeonck', module).each(function(j, li) {
						li = $(li);
						lilevel = parseInt(li.attr('data-level')) + parseInt(liparentlevel) - 1;
						li.attr('data-level', lilevel).addClass('level' + lilevel);
					});
				});
			}

			function validateitem(itemtmp) {
				if (!itemtmp || $(itemtmp).hasClass('nomobileck'))
					return false;
				var outer = $('> .accordeonck_outer', itemtmp).length ? $('> .accordeonck_outer', itemtmp) : itemtmp;
				if (($('> a', outer).length || $('> span.separator', outer).length)
							&& ($('> a', outer).length || $('> span.separator', outer).length || opts.useimages)
							|| ($('> div.accordeonckmod', outer).length && opts.usemodules)
							|| ($('> .accordeonck_outer', outer).length)
							)
					return $('> a', outer).length ? $('> a', outer).clone() : $('> span.separator', outer).clone();
				return false;
			}
			
			function mobileaccordeonck_strrepeat(string, count) {
				if (count < 1) return '';
				while (count > 0) {
					string += string;
					count--;
				}
				return string;
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
						mobilemenu.css({
							'position': 'fixed',
							'overflow-y': 'auto',
							'overflow-x': 'hidden',
							'z-index': '100000',
							'top': '0',
							'left': '0',
							'width': '300px',
							'height': '100%',
							'display': 'none'
						});
						break;
					case 'slideright':
						mobilemenu.css({
							'position': 'fixed',
							'overflow-y': 'auto',
							'overflow-x': 'hidden',
							'z-index': '100000',
							'top': '0',
							'right': '0',
							'left': 'auto',
							'width': '300px',
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

			function openMenu(menuid) {
				mobilemenu = $('#' + menuid + '-mobile');
//				mobilemenu.show();
				switch (opts.displayeffect) {
					case 'normal':
					default:
						mobilemenu.fadeOut();
						$('#' + opts.menuid + '-mobile').fadeIn();
						if (opts.container != 'body')
							$('#' + opts.menuid + '-mobilebarmenuck').css('display', 'none');
						break;
					case 'slideleft':
						mobilemenu.css('display', 'block').css('opacity', '0').css('width', '0').animate({'opacity': '1', 'width': '300px'});
						$('body').css('position', 'relative').animate({'left': '300px'});
						break;
					case 'slideright':
						mobilemenu.css('display', 'block').css('opacity', '0').css('width', '0').animate({'opacity': '1', 'width': '300px'});
						$('body').css('position', 'relative').animate({'right': '300px'});
						break;
					case 'open':
						// mobilemenu..slideDown();
						$('#' + opts.menuid + '-mobile').slideDown('slow');
						if (opts.container != 'body')
							$('#' + opts.menuid + '-mobilebarmenuck').css('display', 'none');
						break;
				}
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
							$('#' + opts.menuid + '-mobilebarmenuck').css('display', '');
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
								$('#' + opts.menuid + '-mobilebarmenuck').css('display', '');
						});
						
						break;
				}
			}
		});
	}
})(jQuery);