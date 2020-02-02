<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><fieldset id="acy_newsletter_listing_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table><tr>
			<td id="acybutton_newsletter_preview"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo acymailing_translation('PLEASE_SELECT',true);?>');}else{  acymailing.submitbutton('preview')} return false;" href="#"><span class="icon-32-acypreview" title="<?php echo acymailing_translation('ACY_PREVIEW').'/'.acymailing_translation('SEND'); ?>"></span><?php echo acymailing_translation('ACY_PREVIEW').'/'.acymailing_translation('SEND'); ?></a></td>
			<td id="acybuttondivider"><span class="divider"></span></td>
			<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_manage','all'))){ ?><td id="acybutton_newsletter_add"><a onclick="acymailing.submitbutton('form'); return false;" href="#" ><span class="icon-32-new" title="<?php echo acymailing_translation('ACY_NEW'); ?>"></span><?php echo acymailing_translation('ACY_NEW'); ?></a></td>
			<td id="acybutton_subscriber_edit"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo acymailing_translation('PLEASE_SELECT',true);?>');}else{  acymailing.submitbutton('edit')} return false;" href="#" ><span class="icon-32-edit" title="<?php echo acymailing_translation('ACY_EDIT'); ?>"></span><?php echo acymailing_translation('ACY_EDIT'); ?></a></td><?php } ?>
			<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_delete','all'))){ ?><td id="acybutton_newsletter_delete"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo acymailing_translation('PLEASE_SELECT',true);?>');}else{if(confirm('<?php echo acymailing_translation('ACY_VALIDDELETEITEMS',true); ?>')){acymailing.submitbutton('remove');}} return false;" href="#" ><span class="icon-32-delete" title="<?php echo acymailing_translation('ACY_DELETE'); ?>"></span><?php echo acymailing_translation('ACY_DELETE'); ?></a></td><?php } ?>
			<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_copy','all'))){ ?><td id="acybutton_newsletter_copy"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo acymailing_translation('PLEASE_SELECT',true);?>');}else{ acymailing.submitbutton('copy')} return false;" href="#"><span class="icon-32-copy" title="<?php echo acymailing_translation('ACY_COPY'); ?>"></span><?php echo acymailing_translation('ACY_COPY'); ?></a></td><?php } ?>
		</tr></table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo acymailing_translation('NEWSLETTER'); ?></h1></div>
</fieldset>
<?php
include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'listing.php');
