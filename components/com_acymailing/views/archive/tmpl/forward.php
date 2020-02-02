<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><h1 class="componentheading"><?php echo acymailing_translation('FORWARD_FRIEND'); ?></h1>
<form action="<?php echo acymailing_route('index.php?option=com_acymailing&ctrl=archive'); ?>" method="post" name="adminForm" id="adminForm" >
	<div class="acymailing_forward">
		<table id="forward_sender_information">
			<tbody id="friend_table">
				<tr>
					<td>
						<label for="sendername"><?php echo acymailing_translation('YOUR_NAME'); ?></label>
					</td>
					<td>
						<input id="sendername" type="text" class="inputbox required" name="sendername" value="<?php echo $this->escape($this->senderName); ?>" style="width:200px"/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="senderemail"><?php echo acymailing_translation('YOUR_EMAIL'); ?></label>
					</td>
					<td>
						<input id="senderemail" type="text" class="inputbox required" name="senderemail" value="<?php echo $this->escape($this->senderMail); ?>" style="width:200px"/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="forwardname"><?php echo acymailing_translation('FRIEND_NAME'); ?></label>
					</td>
					<td>
						<input id="forwardname" type="text" class="inputbox required" name="forwardusers[0][name]" value="" style="width:200px"/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="forwardemail"><?php echo acymailing_translation('FRIEND_EMAIL'); ?></label>
					</td>
					<td>
						<input id="forwardemail" type="text" class="inputbox required" name="forwardusers[0][email]" value="" style="width:200px"/>
					</td>
				</tr>
			</tbody>
		</table>
		<div id="forward_addfriend">
			<a onClick="addLine();return false;" ><?php echo acymailing_translation('ADD_FRIEND') ?></a>
			<span><?php echo acymailing_translation_sprintf('MAX_FORWARD_USER', 5) ?></span>
		</div>
		<div id="forward_sender_message">
			<label for="forwardmsg"><?php echo acymailing_translation('ADD_FORWARD_MESSAGE'); ?></label><br />
			<textarea cols="60" rows="5" name="forwardmsg" id="forwardmsg" ></textarea>
		</div>
		<?php if($this->config->get('forward') == 2 AND acymailing_level(1)){ ?>
			<br /><div id="captcha_forward">
				<?php
					$captchaClass = acymailing_get('class.acycaptcha');
					if($this->config->get('captcha_enabled', 0) == 0) $captchaClass->pluginName = 'acycaptcha';
					$captchaClass->display();
				?>
			</div>
		<?php } ?>
		<input type="submit" class="acymailing_button" onclick="document.adminForm.task.value='doforward';" value="<?php echo acymailing_translation('SEND',true); ?>"/>
	</div>
	<input type="hidden" name="key" value="<?php echo $this->mail->key;?>" />
	<input type="hidden" name="mailid" value="<?php echo $this->mail->mailid; ?>" />
	<?php if(!empty($this->receiver->subid)){ ?>
		<input type="hidden" name="subid" value="<?php echo $this->receiver->subid.'-'.$this->receiver->key ?>" />
	<?php }
	acymailing_formOptions();
	if(acymailing_getVar('cmd', 'tmpl') == 'component'){ ?><input type="hidden" name="tmpl" value="component" /><?php } ?>
</form>

<?php include(dirname(__FILE__).DS.'view.php'); ?>
