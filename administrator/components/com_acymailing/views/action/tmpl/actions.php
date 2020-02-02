<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions" id="executed_actions_block">
	<span class="acyblocktitle"><?php echo acymailing_translation('ACTIONS'); ?></span>
	<?php echo acymailing_translation('ACY_DISTRIBUTION_DELETE_MESSAGE'); ?>
	<div id="actionsarea"></div>
	<button class="acymailing_button" onclick="addAction();return false;"><?php echo acymailing_translation('ADD_ACTION'); ?></button>
	<table cellspacing="1" style="margin-top:30px;">
		<tr>
			<td class="acykey" style="padding-right:20px;">
				<label for="self_signed">
					<?php echo acymailing_tooltip(acymailing_translation('ACTION_SENDER_FROM_DESC').'<br /><strong>'.acymailing_translation('ACTION_SENDER_ADDRESS').'</strong>', '', '', acymailing_translation('ACTION_SENDER_FROM')); ?>
				</label>
			</td>
			<td>
				<?php echo acymailing_boolean("data[action][senderfrom]", '', @$this->action->senderfrom); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey" style="padding-right:20px;">
				<label for="self_signed">
					<?php echo acymailing_tooltip(acymailing_translation('ACTION_SENDER_TO_DESC').'<br /><strong>'.acymailing_translation('ACTION_SENDER_ADDRESS').'</strong>', '', '', acymailing_translation('ACTION_SENDER_TO')); ?>
				</label>
			</td>
			<td>
				<?php echo acymailing_boolean("data[action][senderto]", '', @$this->action->senderto); ?>
			</td>
		</tr>
	</table>
</div>
