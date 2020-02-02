<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

$document			= JFactory::getDocument();

global $advPollMedia, $advPollGoogle;

if (!$advPollMedia) {
	$advPollMedia	= true;

	JHtml::_('jquery.framework');

	JHtml::_('script', 'com_sl_advpoll/script.js', array(), true);
	JHtml::_('stylesheet', 'com_sl_advpoll/style.css', array(), true);
	JHtml::_('script', 'com_sl_advpoll/jquery.fancybox.js', array(), true);
	JHtml::_('stylesheet', 'com_sl_advpoll/jquery.fancybox.css', array(), true);

	$document->addScriptDeclaration("Skyline.AdvPoll.live_site = '" . JURI::root() . "';");
	$document->addScriptDeclaration("Skyline.AdvPoll.back_to_vote = '" . JText::_('PLG_SL_ADVPOLL_BACK_POLL') . "';");
}

//if (($item->params->get('graph_type') == 2 || $item->params->get('graph_type') == 3) && !$advPollGoogle) {
	$advPollGoogle	= true;
	$document->addScript('https://www.google.com/jsapi');
	$document->addScriptDeclaration('google.load("visualization", "1", {packages:["corechart"]});');
//}

JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_sl_advpoll/models');
$model	= JModelLegacy::getInstance('Poll', 'SL_AdvPollModel', array('ignore_request' => true));
$custom_style = $model->customStyle($item, 'advpoll-' . $item->id);
$document->addStyleDeclaration($custom_style);

?>

<div class="sl_advpoll" style="width:<?php echo $width; ?>px;<?php echo $center; ?>" id="advpoll-<?php echo $item->id; ?>">
	<form class="sl_advpoll_form" method="post" action="<?php echo ''; ?>">
		<?php if ($title) : ?>
		<div class="wrap_sl_advpoll_title">
			<div class="sl_advpoll_title"><?php echo $title; ?></div>
		</div>
		<?php endif; ?>
		<div class="sl_advpoll_body">
			<div class="sl_advpoll_question">
				<?php echo $item->title; ?>
			</div>
			<div class="sl_advpoll_answers">
				<ul>
				<?php foreach ($item->answers as $answer) : ?>
					<li>
						<label><input type="checkbox" name="answers[]" value="<?php echo $answer->id; ?>" /><?php echo $answer->title; ?></label>
					</li>
				<?php endforeach; ?>
				<?php if($item->params->get('other_answer', 0) == 1) : ?>
					<li>
						<label>
							<input type="checkbox" name="other_answers[]" value="" class="other-answer-checkbox" />
							<?php echo $item->params->get('other_answer_label', 'Other'); ?>
						</label>
						<input type="text" name="other_answer_value" id="other_answer_value" value="" class="other-answer-input" style="display: none;">
					</li>
				<?php endif; ?>
				</ul>
			</div>
		</div>
		<div class="sl_advpoll_buttons">
			<?php if(isset($item->expired) && $item->expired) : ?>
				<label><?php echo JText::_('COM_SL_ADVPOLL_EXPIRED_POLL'); ?></label>
			<?php else: ?>
				<input class="sl_advpoll_button sl_advpoll_vote" type="submit" value="<?php echo JText::_('COM_SL_ADVPOLL_VOTE'); ?>" />
			<?php endif; ?>
			<?php if ($item->params->get('show_result', 1)) : ?>
				<a href="javascript:void(0)" class="sl_advpoll_showresult"><?php echo JText::_('COM_SL_ADVPOLL_RESULT'); ?></a>
			<?php endif; ?>
		</div>

		<input type="hidden" name="option" value="com_sl_advpoll" />
		<input type="hidden" name="task" value="poll.vote" />
		<input type="hidden" name="id" value="<?php echo $item->id; ?>" />
		<input type="hidden" name="maxChoices" value="<?php echo $item->params->get('maxChoices'); ?>" />
		<input type="hidden" name="displayType" value="<?php echo $item->params->get('result_display_type'); ?>"/>
		<input type="hidden" name="graphType" value="<?php echo $item->params->get('graph_type'); ?>"/>
	</form>
	<div class="sl_advpoll_result_container"></div>
	<div class="sl_advpoll_msg_container">
		<div class="sl_advpoll_msg">
			<div class="sl_advpoll_title">
				<?php echo $item->title; ?>
			</div>
			<div class="sl_advpoll_message">
				<?php echo JText::_('COM_SL_ADVPOLL_SELECT_ITEM'); ?>
			</div>
			<div class="sl_advpoll_buttons">
				<button class="sl_advpoll_button" onclick="jQuery.fancybox.close();">
					<?php echo JText::_('COM_SL_ADVPOLL_CLOSE'); ?>
				</button>
			</div>
		</div>
	</div>

	<div class="sl_advpoll_ajax_result"></div>

</div>

