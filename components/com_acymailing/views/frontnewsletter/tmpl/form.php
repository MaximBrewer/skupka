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
	<fieldset id="acy_newsletter_form_menu">
		<div class="toolbar" id="acytoolbar" style="float: right;">
			<table>
				<tr>

					<?php if(acymailing_isAllowed($this->config->get('acl_templates_view', 'all'))){
						echo '<td id="acybuttontemplate">'.acymailing_popup(acymailing_completeLink("fronttemplate&task=theme", true), '<span class="icon-32-acytemplate" title="'.acymailing_translation('ACY_TEMPLATE').'"></span>'.acymailing_translation('ACY_TEMPLATE'), '').'</td>';
					} ?>

					<?php if(acymailing_isAllowed($this->config->get('acl_tags_view', 'all'))){
						echo '<td id="acybuttontag">'.acymailing_popup(acymailing_completeLink("fronttag&task=tag&type=".$this->type, true), '<span class="icon-32-acytags" title="'.acymailing_translation('TAGS').'"></span>'.acymailing_translation('TAGS'), '').'</td>';
						?>
						<td id="acybuttonreplace"><a onclick="acymailing.submitbutton('replacetags'); return false;" href="#"><span class="icon-32-replacetag" title="<?php echo acymailing_translation('REPLACE_TAGS'); ?>"></span><?php echo acymailing_translation('REPLACE_TAGS'); ?></a></td>
					<?php } ?>
					<td id="acybuttondivider"><span class="divider"></span></td>
					<td id="acybuttonpreview"><a onclick="acymailing.submitbutton('savepreview'); return false;" href="#"><span class="icon-32-acypreview" title="<?php echo acymailing_translation('ACY_PREVIEW').' / '.acymailing_translation('SEND'); ?>"></span><?php echo acymailing_translation('ACY_PREVIEW').' / '.acymailing_translation('SEND'); ?></a></td>
					<td id="acybuttonsave"><a onclick="acymailing.submitbutton('save'); return false;" href="#"><span class="icon-32-save" title="<?php echo acymailing_translation('ACY_SAVE'); ?>"></span><?php echo acymailing_translation('ACY_SAVE'); ?></a></td>
					<td id="acybuttonapply"><a onclick="acymailing.submitbutton('apply'); return false;" href="#"><span class="icon-32-apply" title="<?php echo acymailing_translation('ACY_APPLY'); ?>"></span><?php echo acymailing_translation('ACY_APPLY'); ?></a></td>
					<td id="acybuttoncancel"><a onclick="acymailing.submitbutton('cancel'); return false;" href="#"><span class="icon-32-cancel" title="<?php echo acymailing_translation('ACY_CANCEL'); ?>"></span><?php echo acymailing_translation('ACY_CANCEL'); ?></a></td>

				</tr>
			</table>
		</div>
		<div class="acyheader" style="float: left;"><h1><?php echo acymailing_translation('NEWSLETTER').' : '.@$this->mail->subject; ?></h1></div>
	</fieldset>
	<div id="acymailing_edit">
		<div class="confirmBoxMM" id="confirmBoxMM" style="display: none;">
			<div id="acy_popup_content">
				<span class="confirmTxtMM" id="confirmTxtMM"></span><br/>
				<button class="acymailing_button" id="confirmCancelMM" onclick="document.getElementById('confirmBoxMM').style.display='none';document.getElementById('modal-background').style.display='none';return false;" style="padding: 6px 15px 6px 10px;">
					<i class="acyicon-cancel" id="cancelSave" style="margin-right: 5px; font-size: 16px;top: 2px; position: relative;"></i><?php echo acymailing_translation('ACY_CANCEL'); ?>
				</button>
				<button class="acymailing_button acymailing_button_delete" id="confirmOkMM" style="padding: 8px 15px 6px 10px; background-color: #d75c55" onclick="acymailing.submitform(pressbutton,document.adminForm)">
					<i class="acyicon-save" id="iconAction" style="margin-right: 5px; font-size: 12px;"></i><span id="textBtnAction"><?php echo acymailing_translation('ACY_SAVE'); ?></span>
				</button>
			</div>
		</div>
		<div id="modal-background" style="display: none;"></div>
		<form action="<?php echo acymailing_route('index.php?option=com_acymailing&ctrl=frontnewsletter'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data">

			<div class="onelineblockoptions acyblock_newsletter">
				<span class="acyblocktitle"><?php echo acymailing_translation('ACY_NEWSLETTER_INFORMATION'); ?></span>
				<?php include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'info.form.php'); ?>
			</div>
			<div class="onelineblockoptions">
				<?php include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'param.form.php'); ?>
			</div>
			<div class="onelineblockoptions acyblock_newsletter" id="htmlfieldset">
				<span class="acyblocktitle"><?php echo acymailing_translation('HTML_VERSION'); ?></span>

				<div style="clear:both"><?php echo $this->editor->display(); ?></div>
			</div>
			<div class="onelineblockoptions acyblock_newsletter">
				<span class="acyblocktitle"><?php echo acymailing_translation('TEXT_VERSION'); ?></span>
				<textarea style="width:98%" rows="20" name="data[mail][altbody]" id="altbody" placeholder="<?php echo acymailing_translation('AUTO_GENERATED_HTML'); ?>" onClick="zoneToTag='altbody';"><?php echo @$this->mail->altbody; ?></textarea>
			</div>


			<div class="clr"></div>
			<input type="hidden" name="cid[]" value="<?php echo @$this->mail->mailid; ?>"/>
			<input type="hidden" id="tempid" name="data[mail][tempid]" value="<?php echo @$this->mail->tempid; ?>"/>
			<input type="hidden" name="data[mail][type]" value="news"/>
			<input type="hidden" name="listid" value="<?php echo acymailing_getVar('int', 'listid'); ?>"/>
			<?php if(!empty($this->Itemid)) echo '<input type="hidden" name="Itemid" value="'.$this->Itemid.'" />';
			acymailing_formOptions(); ?>
		</form>
	</div>
</div>
