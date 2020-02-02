<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div>
	<?php echo $this->tabs->startPane('followup_tab'); ?>
	<?php echo $this->tabs->startPanel(acymailing_translation('ATTACHMENTS'), 'mail_attachments'); ?>
	<br style="font-size:1px"/>
	<?php if(!empty($this->mail->attach)){ ?>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo acymailing_translation('ATTACHED_FILES'); ?></span>
			<?php
			foreach($this->mail->attach as $idAttach => $oneAttach){
				$idDiv = 'attach_'.$idAttach;
				echo '<div id="'.$idDiv.'">'.$oneAttach->filename.' ('.(round($oneAttach->size / 1000, 1)).' Ko)';
				echo $this->toggleClass->delete($idDiv, $this->mail->mailid.'_'.$idAttach, 'mail');
				echo '</div>';
			}
			?>
		</div>
	<?php } ?>
	<div id="loadfile">
		<?php
		$uploadfileType = acymailing_get('type.uploadfile');
		for($i = 0; $i < 10; $i++){
			echo '<div'.($i == 0 ? '' : ' style="display:none;"').' id="attachmentsdiv'.$i.'">'.$uploadfileType->display(false, 'attachments', $i).'</div>';
		}
		?>
	</div>
	<a href="javascript:void(0);" onclick='addFileLoader()'><?php echo acymailing_translation('ADD_ATTACHMENT'); ?></a>
	<?php echo acymailing_translation_sprintf('MAX_UPLOAD', $this->values->maxupload); ?>
	<?php echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(acymailing_translation('SENDER_INFORMATIONS'), 'mail_sender'); ?>
	<br style="font-size:1px"/>
	<table width="100%" class="acymailing_table">
		<tr>
			<td class="paramlist_key">
				<?php echo acymailing_translation('FROM_NAME'); ?>
			</td>
			<td class="paramlist_value">
				<input placeholder="<?php echo acymailing_translation('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="fromname" type="text" name="data[mail][fromname]" style="width:200px" value="<?php echo $this->escape(@$this->mail->fromname); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<?php echo acymailing_translation('FROM_ADDRESS'); ?>
			</td>
			<td class="paramlist_value">
				<input onchange="validateEmail(this.value, '<?php echo addslashes(acymailing_translation('FROM_ADDRESS')); ?>')" placeholder="<?php echo acymailing_translation('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="fromemail" type="text" name="data[mail][fromemail]" style="width:200px" value="<?php echo $this->escape(@$this->mail->fromemail); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<?php echo acymailing_translation('REPLYTO_NAME'); ?>
			</td>
			<td class="paramlist_value">
				<input placeholder="<?php echo acymailing_translation('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="replyname" type="text" name="data[mail][replyname]" style="width:200px" value="<?php echo $this->escape(@$this->mail->replyname); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<?php echo acymailing_translation('REPLYTO_ADDRESS'); ?>
			</td>
			<td class="paramlist_value">
				<input onchange="validateEmail(this.value, '<?php echo addslashes(acymailing_translation('REPLYTO_ADDRESS')); ?>')" placeholder="<?php echo acymailing_translation('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="replyemail" type="text" name="data[mail][replyemail]" style="width:200px" value="<?php echo $this->escape(@$this->mail->replyemail); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label for="bccaddresses"><?php echo acymailing_translation('ACY_BCC_ADDRESS'); ?></label>
			</td>
			<td class="paramlist_value">
				<input placeholder="address@example.com" class="inputbox" id="bccaddresses" type="text" name="data[mail][bccaddresses]" style="width:200px; max-width:80%;" value="<?php echo $this->escape(@$this->mail->bccaddresses); ?>"/>
			</td>
		</tr>
	</table>

	<?php echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(acymailing_translation('META_DATA'), 'mail_metadata'); ?>
	<br style="font-size:1px"/>
	<table width="100%" class="acymailing_table" id="metadatatable">
		<tr>
			<td class="paramlist_key">
				<label for="metakey"><?php echo acymailing_translation('META_KEYWORDS'); ?></label>
			</td>
			<td class="paramlist_value">
				<textarea id="metakey" name="data[mail][metakey]" rows="5" cols="30"><?php echo @$this->mail->metakey; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label for="metadesc"><?php echo acymailing_translation('META_DESC'); ?></label>
			</td>
			<td class="paramlist_value">
				<textarea id="metadesc" name="data[mail][metadesc]" rows="5" cols="30"><?php echo @$this->mail->metadesc; ?></textarea>
			</td>
		</tr>
	</table>
	<?php
	echo acymailing_getFunctionsEmailCheck();

	echo $this->tabs->endPanel();
	echo $this->tabs->endPane(); ?>
</div>
