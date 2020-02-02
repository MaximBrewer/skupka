<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions">
	<span class="acyblocktitle"><?php echo acymailing_translation('FE_EDITION'); ?></span>
	<table class="acymailing_table" cellspacing="1">
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(acymailing_translation('DEFAULT_SENDER_DESC'), acymailing_translation('DEFAULT_SENDER'), '', acymailing_translation('DEFAULT_SENDER')); ?>
			</td>
			<td>
				<?php echo acymailing_boolean("config[frontend_sender]", '', $this->config->get('frontend_sender', 0)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(acymailing_translation('DEFAULT_REPLY_DESC'), acymailing_translation('DEFAULT_REPLY'), '', acymailing_translation('DEFAULT_REPLY')); ?>
			</td>
			<td>
				<?php echo acymailing_boolean("config[frontend_reply]", '', $this->config->get('frontend_reply', 0)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(acymailing_translation('FE_MODIFICATION_DESC'), acymailing_translation('FE_MODIFICATION'), '', acymailing_translation('FE_MODIFICATION')); ?>
			</td>
			<td>
				<?php echo acymailing_boolean("config[frontend_modif]", '', $this->config->get('frontend_modif', 1)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(acymailing_translation('FE_MODIFICATION_SENT_DESC'), acymailing_translation('FE_MODIFICATION_SENT'), '', acymailing_translation('FE_MODIFICATION_SENT')); ?>
			</td>
			<td>
				<?php echo acymailing_boolean("config[frontend_modif_sent]", '', $this->config->get('frontend_modif_sent', 1)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(acymailing_translation('ACY_FE_DELETE_BUTTON_DESC'), acymailing_translation('ACY_FE_DELETE_BUTTON'), '', acymailing_translation('ACY_FE_DELETE_BUTTON')); ?>
			</td>
			<td>
				<?php $deleteButton = array(acymailing_selectOption("delete", acymailing_translation('DELETE_USER')), acymailing_selectOption("unsub", acymailing_translation('UNSUB_USER')));
				echo acymailing_radio($deleteButton, 'config[frontend_delete_button]', '', 'value', 'text', $this->config->get('frontend_delete_button', 'delete')); ?>
			</td>
		</tr>
	</table>
</div>
