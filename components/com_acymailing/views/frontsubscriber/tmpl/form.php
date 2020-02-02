<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><fieldset id="acy_subscriber_form_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table>
			<tr>
				<td id="acybutton_subscriber_download_data"><a onclick="acymailing.submitbutton('exportdata'); return false;" href="#" ><span class="icon-32-acyexport" title="<?php echo acymailing_translation('ACY_EXPORT_ALL_DATA'); ?>"></span><?php echo acymailing_translation('ACY_EXPORT_ALL_DATA'); ?></a></td>
				<?php $config = acymailing_config();
				if(acymailing_isAllowed($config->get('acl_newsletters_schedule','all')) && !empty($this->subscriber->subid)){
					echo '<td id="acybutton_subscriber_send">'.acymailing_popup(acymailing_completeLink('index.php?option=com_acymailing&ctrl=frontsubscriber&task=addqueue&tmpl=component&subid='.$this->subscriber->subid), '<span class="icon-32-acysend" title="'.acymailing_translation('SEND').'"></span>'.acymailing_translation('SEND'), '', 640, 480).'</td>';
				} ?>
				<td id="acybutton_subscriber_save"><a onclick="acymailing.submitbutton('save'); return false;" href="#" ><span class="icon-32-save" title="<?php echo acymailing_translation('ACY_SAVE'); ?>"></span><?php echo acymailing_translation('ACY_SAVE'); ?></a></td>
				<td id="acybutton_subscriber_apply"><a onclick="acymailing.submitbutton('apply'); return false;" href="#" ><span class="icon-32-apply" title="<?php echo acymailing_translation('ACY_APPLY'); ?>"></span><?php echo acymailing_translation('ACY_APPLY'); ?></a></td>
				<td id="acybutton_subscriber_cancel"><a onclick="acymailing.submitbutton('cancel'); return false;" href="#" ><span class="icon-32-cancel" title="<?php echo acymailing_translation('ACY_CANCEL'); ?>"></span><?php echo acymailing_translation('ACY_CANCEL'); ?></a></td>
			</tr>
		</table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo acymailing_translation('ACY_USER'); ?></h1></div>
</fieldset>
<?php
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'tmpl'.DS.'form.php');
