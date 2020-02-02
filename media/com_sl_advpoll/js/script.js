/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

if (typeof (Skyline) == 'undefined') {
	var Skyline	= {};
}

Skyline.AdvPoll = {
	live_site:	null,
	back_to_vote: null,

	init: function() {
		jQuery(function() {
			jQuery('.sl_advpoll').each(function() {
				var form		= jQuery(this).children('form.sl_advpoll_form');
				var maxChoices	= parseInt(jQuery(this).find("form.sl_advpoll_form input[name=maxChoices]").val());
				if (maxChoices >= 1) {
					Skyline.AdvPoll.initChoice(form, maxChoices);
				}

				Skyline.AdvPoll.initAjax(jQuery(this));

				var advpoll_ajax_result = jQuery(this).find('.sl_advpoll_ajax_result');
				advpoll_ajax_result.on('click', '.sl_advpoll_back_poll', function() {
					advpoll_ajax_result.html(null);
					advpoll_ajax_result.closest(jQuery('.sl_advpoll')).find('.sl_advpoll_form').show('900');
				});

			});
		});
	},

	initChoice: function(el, max) {
		var checkBoxs	= el.find('input[type="checkbox"]');
		var otherCheck = jQuery(el).find('.other-answer-checkbox');
		var otherInput = jQuery(el).find('.other-answer-input');

		checkBoxs.click(function(event) {
			if (jQuery(this).attr('checked')) {
				otherInput.val('');
				otherInput.css('display', 'none');
				if (max == 1) {
					checkBoxs.attr('checked', false);
					jQuery(this).attr('checked', true);
				} else {
					if (jQuery(el).find('input[type=checkbox]:checked').length > max) {
						jQuery(this).attr('checked', false);
					}
				}
			}
		});

		otherCheck.click(function(event) {
			if(jQuery(this).is(':checked')) {
				otherInput.show(200);
				otherInput.css('max-width', '92%');
			} else {
				otherInput.val('');
				otherInput.hide(200);
			}
		});
	},

	initAjax: function(el) {
		var displayType = parseInt(jQuery(el).find('form.sl_advpoll_form input[name=displayType]').val());
		var graphType = parseInt(jQuery(el).find('form.sl_advpoll_form input[name=graphType]').val());

		jQuery(el).find('.sl_advpoll_showresult').click(function(event) {
			event.preventDefault();
			var container	= jQuery(el).children('.sl_advpoll_result_container');
			var id			= parseInt(jQuery(el).find("form.sl_advpoll_form input[name=id]").val());

			jQuery.ajax({
				url:		Skyline.AdvPoll.live_site,
				method:		'post',
				data:		'option=com_sl_advpoll&view=poll&layout=result&format=raw&id=' + id
			}).done(function(msg) {
				if(displayType == 0) {
					el.find('.sl_advpoll_form').hide(500);
					Skyline.AdvPoll.setNormalContent(el, msg, graphType);
				} else if(displayType == 1) {
					Skyline.AdvPoll.setModalContent(msg);
				}
			});
		});

		// Vote button
		jQuery(el).find('.sl_advpoll_vote').click(function(event) {
			event.preventDefault();
			var container	= jQuery(el).children('.sl_advpoll_result_container');

			var otherCheck = jQuery(el).find('.other-answer-checkbox');
			var otherInput = jQuery(el).find('.other-answer-input');
			var advpoll_form = jQuery(el).find('form.sl_advpoll_form');

			if(otherCheck.is(':checked')) {
				if(otherInput.val() == '') {
					alert('Please enter your answer!');
					otherInput.focus();
					return;
				}
			}

			//if (jQuery("form.sl_advpoll_form input[type=checkbox]:checked").length == 0) {
			if(advpoll_form.find('input[type=checkbox]:checked').length == 0) {
				jQuery.fancybox.open(jQuery(el).children('.sl_advpoll_msg_container'));
			} else {
				jQuery.ajax({
					url:		Skyline.AdvPoll.live_site,
					method:		'post',
					data:		jQuery.param(jQuery(this.form).find('input[type=hidden]')) + '&' + jQuery.param(jQuery(this.form).find('input[type=checkbox]:checked')) + '&' + jQuery.param(jQuery(this.form).find('.other-answer-input')) + '&view=poll&layout=result&format=raw'
				}).done(function(msg){
					if(displayType == 0) {
						el.find('.sl_advpoll_form').hide(500);
						Skyline.AdvPoll.setNormalContent(el, msg, graphType);
					} else if(displayType == 1) {
						Skyline.AdvPoll.setModalContent(msg);
					}
				});
			}
		});
	},

	setModalContent: function(content) {
		jQuery.fancybox.open(content, {
			beforeShow: function() {
				jQuery(this.inner).find('.sl_advpoll_line').css('width', 0);
				jQuery(this.inner).find('.sl_advpoll_percent').css('width', 0);
			},

			afterShow: function() {
				jQuery(this.inner).find('.sl_advpoll_answer_graph').each(function() {
					var el = jQuery(this);
					el.find('.sl_advpoll_line').animate({'width': el.data('percent')});
					el.find('.sl_advpoll_percent').animate({'width': el.data('percent')});
				});
			}
		});
	},

	setNormalContent: function(el, content, graphType) {
		var ajax_result = el.find('.sl_advpoll_ajax_result');
		ajax_result.html(content);

		var advpoll_total = ajax_result.find('.sl_advppoll_total');
		var back_to_vote = '<a class="sl_advpoll_back_poll" style="float: left;" href="javascript:void(0);">' + Skyline.AdvPoll.back_to_vote + '</a>';

		if(advpoll_total.length > 0) {
			advpoll_total.append(back_to_vote);
		} else {
			el.find('.sl_advpoll_result').append('<div class="sl_advppoll_total">' + back_to_vote + '</div>');
		}

		ajax_result.show(900);
		if(graphType != 2 && graphType != 3) {
			ajax_result.find('.sl_advpoll_answer_graph').each(function() {
				jQuery(this).find('.sl_advpoll_line').animate({'width': jQuery(this).data('percent')}, 900);
				jQuery(this).find('.sl_advpoll_percent').animate({'width': jQuery(this).data('percent')}, 900);
			});
		}
	}
}

jQuery(document).ready(function() {
	Skyline.AdvPoll.init();
});


