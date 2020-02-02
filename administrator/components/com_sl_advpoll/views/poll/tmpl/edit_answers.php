<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

$count	= count($this->item->answers);

$js	= <<<JSHERE
jQuery(document).ready(function() {
	Skyline.AdvPoll.answers = $count;

	if (!Skyline.AdvPoll.answers) {
		Skyline.AdvPoll.addAnswer();
	} else {
		Skyline.AdvPoll.refresh();
	}

	Skyline.AdvPoll.displaySchedule();
	Skyline.AdvPoll.displayOtherAnswer();
});
JSHERE;
// Depends on jQuery UI
JHtml::_('jquery.ui', array('core', 'sortable'));

$this->document->addScriptDeclaration($js);
?>

<table class="table table-striped table-bordered" style="width: auto;">
	<thead>
		<tr>
			<th class="nowrap center hidden-phone">
				<i class="icon-menu-2"></i>
			</th>
			</th>
			<th class="nowrap">
				<?php echo JText::_('COM_SL_ADVPOLL_ANSWER_TITLE'); ?>
			</th>
			<th class="nowrap">
				<?php echo JText::_('COM_SL_ADVPOLL_ANSWER_VOTES'); ?>
			</th>
			<th class="nowrap">
				<?php echo JText::_('COM_SL_ADVPOLL_ANSWER_PUBLISHED'); ?>
			</th>
			<th class="nowrap">
				<?php echo JText::_('COM_SL_ADVPOLL_ANSWER_TYPE'); ?>
			</th>
			<th class="nowrap">
				<?php echo JText::_('COM_SL_ADVPOLL_ANSWER_REMOVE'); ?>
			</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="5" class="left">
				<a class="add-button" onclick="Skyline.AdvPoll.addAnswer();" href="javascript:void(0);">
					<?php echo JText::_('COM_SL_ADVPOLL_ADD_ANSWERS'); ?>
				</a>
			</td>
		</tr>
	</tfoot>

	<tbody id="answers_container">
		<?php if (isset($this->item->answers)) : ?>
		<?php foreach ($this->item->answers as $i => $answer) : ?>
			<tr class="row<?php echo $i % 2; ?>" id="answer_row-<?php echo $i; ?>" sortable-group-id="<?php echo $answer->title ?>">
				<td class="order nowrap center hidden-phone">
					<span class="sortable-handler">
						<i class="icon-menu" style="cursor: move;"></i>
					</span>
					<input type="hidden" name="answers[id][]" value="<?php echo $answer->id; ?>" />
				</td>
				<td align="center" valign="top" class="center">
					<input type="text" class="inputbox" name="answers[title][]" value="<?php echo $answer->title; ?>" size="40" />
				</td>
				<td align="center" valign="top" class="center">
					<input type="text" class="inputbox input-small" name="answers[votes][]" value="<?php echo $answer->votes; ?>" size="10" />
				</td>
				<td class="center" valign="top" class="center">
					<input type="checkbox" class="inputbox" value="1" onclick="Skyline.AdvPoll.publishAnswer(<?php echo $i; ?>, this.checked ? 1 : 0);"<?php echo $answer->state != 0 ? ' checked="checked"' : ''; ?> />
					<input type="hidden" name="answers[state][]" id="answer_state-<?php echo $i; ?>" value="<?php echo $answer->state; ?>" />
				</td>
				<td align="center" valign="top" class="center">
					<input type="text" class="inputbox input-small" name="answers[type_answer][]" value="<?php echo empty($answer->type_answer) ? 'default' : $answer->type_answer; ?>" size="10" readonly="readonly" />
				</td>
				<td class="center" valign="top" class="center">
					<a class="answer-delete"  onclick="Skyline.AdvPoll.removeAnswer(<?php echo $i; ?>);">
						<?php echo JText::_('COM_SL_ADVPOLL_REMOVE_ANSWER'); ?>
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>