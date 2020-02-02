/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

if (typeof(Skyline) == 'undefined') {
	var Skyline	= {};
}

Skyline.AdvPoll = {
	answers:		0,

	refresh: function() {
		// refresh sortable
		jQuery('#answers_container').sortable();
	},

	/**
	 * Add answer.
	 */
	addAnswer: function() {
		var id	= Skyline.AdvPoll.answers++;
		// count all rows
		var len	= jQuery('answers_container').find('tr').length;

		// create new table row
		jQuery('<tr/>', {
			id: 	'answer_row-' + id,
			class:	'row' + (len % 2)
		}).html('<td class="order nowrap center hidden-phone"><span class="sortable-handler" ><i class="icon-menu" style="cusor: pointer;"></i></span><input type="hidden" name="answers[id][]" value="" /></td><td align="center" valign="top"><input type="text" class="inputbox" name="answers[title][]" value="" size="40" /></td><td align="center" valign="top"><input type="text" class="inputbox input-small" name="answers[votes][]" value="" size="10" /></td><td class="center" valign="top"><input type="checkbox" class="inputbox" value="1" onclick="Skyline.AdvPoll.publishAnswer(' + id + ', this.checked ? 1 : 0);" checked="checked" /><input type="hidden" name="answers[state][]" id="answer_state-' + id + '" value="1" /></td><td align="center" valign="top" class="center"><input type="text" class="inputbox input-small" name="answers[type_answer][]" value="default" size="10" readonly="readonly" /></td><td class="center" valign="top"><a class="answer-delete" onclick="Skyline.AdvPoll.removeAnswer(' + id + ');">Remove Answer</a></td>')
		.appendTo(jQuery('#answers_container'));

		Skyline.AdvPoll.refresh();
	},

	/**
	 * Remove answer.
	 */
	removeAnswer: function(id) {
		if (jQuery('#answer_row-' + id)) {
			jQuery('#answer_row-' + id).remove();
		}
	},

	/**
	 * Set answer state value.
	 */
	publishAnswer: function(id, value) {
		if (jQuery('#answer_state-' + id)) {
			jQuery('#answer_state-' + id).val(value);
		}
	},

	displaySchedule: function() {
		var $ = jQuery;
		var $schedule_no = $('#jform_schedule0');
		var $schedule_yes = $('#jform_schedule1');
		var $start_date = $('#jform_publish_up');
		var $end_date = $('#jform_publish_down');

		if($schedule_no.is(':checked')) {
			$start_date.closest('.control-group').css('display', 'none');
			$end_date.closest('.control-group').css('display', 'none');
		} else {
			$start_date.closest('.control-group').css('display', 'block');
			$end_date.closest('.control-group').css('display', 'block');
		}

		$schedule_no.click(function(){
			$start_date.closest('.control-group').css('display', 'none');
			$end_date.closest('.control-group').css('display', 'none');
		});

		$schedule_yes.click(function() {
			$start_date.closest('.control-group').css('display', 'block');
			$end_date.closest('.control-group').css('display', 'block');
		});
	},

	displayOtherAnswer: function() {
		var $ = jQuery;
		var $other_no = $('#jform_params_other_answer0');
		var $other_yes = $('#jform_params_other_answer1');
		var $other_answer_label = $('#jform_params_other_answer_label');
		var $display_other_no = $('#jform_params_display_other_answer1');

		if($other_no.is(':checked')) {
			$other_answer_label.closest('.control-group').css('display', 'none');
			$display_other_no.closest('.control-group').css('display', 'none');
		} else {
			$other_answer_label.closest('.control-group').css('display', 'block');
			$display_other_no.closest('.control-group').css('display', 'block');
		}

		$other_no.click(function(){
			$other_answer_label.closest('.control-group').css('display', 'none');
			$display_other_no.closest('.control-group').css('display', 'none');
		});

		$other_yes.click(function(){
			$other_answer_label.closest('.control-group').css('display', 'block');
			$display_other_no.closest('.control-group').css('display', 'block');
		});
	}
}

