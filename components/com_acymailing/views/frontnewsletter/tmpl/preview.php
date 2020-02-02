<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<fieldset id="acy_preview_menu">
		<div class="toolbar" id="acytoolbar" style="float: right;">
			<table>
				<tr>
					<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_schedule', 'all'))){
						if($this->mail->published == 2){ ?>
							<td id="acybuttonunschedule"><a onclick="acymailing.submitbutton('unschedule'); return false;" href="#" class="toolbar"><span class="icon-32-unschedule" title="<?php echo acymailing_translation('UNSCHEDULE', true); ?>"></span><?php echo acymailing_translation('UNSCHEDULE'); ?></a></td>
						<?php }else{
							echo '<td id="acybuttonschedule">'.acymailing_popup(acymailing_completeLink("frontnewsletter&task=scheduleconfirm&listid=".acymailing_getVar('int', 'listid')."&mailid=".$this->mail->mailid, true), '<span class="icon-32-schedule" title="'.acymailing_translation('SCHEDULE', true).'"></span>'.acymailing_translation('SCHEDULE'), '', 0).'</td>';
						}
					}
					if(acymailing_isAllowed($this->config->get('acl_newsletters_send', 'all'))){
						echo '<td id="acybuttonsend">'.acymailing_popup(acymailing_completeLink("frontnewsletter&task=sendconfirm&listid=".acymailing_getVar('int', 'listid')."&mailid=".$this->mail->mailid, true), '<span class="icon-32-acysend" title="'.acymailing_translation('SEND', true).'"></span>'.acymailing_translation('SEND'), '').'</td>';
					}
					if(acymailing_isAllowed($this->config->get('acl_newsletters_spam_test', 'all'))){
						echo '<td id="acybuttonspamtest">'.acymailing_popup(acymailing_completeLink("frontnewsletter&task=spamtest&tmpl=component&mailid=".$this->mail->mailid, true), '<span class="icon-32-spamtest" title="'.acymailing_translation('SPAM_TEST').'"></span>'.acymailing_translation('SPAM_TEST'), '', 0).'</td>';
					}
					if(acymailing_isAllowed($this->config->get('acl_newsletters_schedule', 'all')) || acymailing_isAllowed($this->config->get('acl_newsletters_send', 'all')) || acymailing_isAllowed($this->config->get('acl_newsletters_spam_test', 'all'))){ ?>
						<td id="acybuttondivider"><span class="divider"></span></td>
					<?php } ?>
					<td id="acybuttonedit"><a onclick="acymailing.submitbutton('edit'); return false;" href="#" class="toolbar"><span class="icon-32-edit" title="<?php echo acymailing_translation('ACY_EDIT'); ?>"></span><?php echo acymailing_translation('ACY_EDIT'); ?></a></td>
					<td id="acybuttoncancel"><a onclick="acymailing.submitbutton('cancel'); return false;" href="#" class="toolbar"><span class="icon-32-cancel" title="<?php echo acymailing_translation('ACY_CLOSE'); ?>"></span><?php echo acymailing_translation('ACY_CLOSE'); ?></a></td>
				</tr>
			</table>
		</div>
		<div class="acyheader" style="float: left;"><h1><?php echo acymailing_translation('ACY_PREVIEW').' : '.@$this->mail->subject; ?></h1></div>
	</fieldset>
	<?php include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'preview.php'); ?>
</div>
