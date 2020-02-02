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
	<?php

	if(!empty($this->values->nbqueue)){
		echo acymailing_display(acymailing_translation_sprintf('ALREADY_QUEUED', $this->values->nbqueue));
	}elseif(empty($this->lists)){
		echo acymailing_display(acymailing_translation('EMAIL_AFFECT'), 'warning');
	}else{ ?>
		<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">
			<div>
				<div class="onelineblockoptions">
					<span class="acyblocktitle"><?php echo acymailing_translation('NEWSLETTER_SENT_TO'); ?></span>
					<table class="acymailing_table" cellspacing="1" align="center">
						<tbody>
						<?php
						$k = 0;
						$listids = array();
						foreach($this->lists as $row){
							$listids[] = $row->listid;
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td>
									<?php
									echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name);
									echo ' ( '.acymailing_translation_sprintf('ACY_SELECTED_USERS', $row->nbsub).' )';
									?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						} ?>
						</tbody>
					</table>
					<?php
					$filterClass = acymailing_get('class.filter');
					if(!empty($this->mail->filter)){
						$resultFilters = $filterClass->displayFilters($this->mail->filter);
						if(!empty($resultFilters)){
							echo '<br />'.acymailing_translation('RECEIVER_LISTS').'<br />'.acymailing_translation('FILTER_ONLY_IF');
							echo '<ul><li>'.implode('</li><li>', $resultFilters).'</li></ul>';
						}
					} ?>
				</div>
				<div style="text-align:center;font-size:14px;padding:20px;">
					<?php
					$nbTotalReceivers = $nbTotalReceiversAll = $filterClass->countReceivers($listids, $this->mail->filter);
					if(!empty($this->values->alreadySent)){
						$filterClass->onlynew = true;
						$nbTotalReceivers = $nbTotalReceiversAlready = $filterClass->countReceivers($listids, $this->mail->filter, $this->mail->mailid);

						acymailing_display(acymailing_translation_sprintf('ALREADY_SENT', $this->values->alreadySent).'<br />'.acymailing_translation('REMOVE_ALREADY_SENT').'<br />'.acymailing_boolean("onlynew", 'onclick="if(this.value == 1){document.getElementById(\'nbreceivers\').innerHTML = \''.$nbTotalReceiversAlready.'\';}else{document.getElementById(\'nbreceivers\').innerHTML = \''.$nbTotalReceiversAll.'\'}"', 1, acymailing_translation('JOOMEXT_YES'), acymailing_translation('SEND_TO_ALL')), 'warning');
					}
					if(empty($this->values->nbqueue)) echo acymailing_translation_sprintf('SENT_TO_NUMBER', '<span style="font-weight:bold;" id="nbreceivers" >'.$nbTotalReceivers.'</span>').'<br />';
					?>

					<button type="submit" class="acymailing_button" onclick="document.adminForm.task.value='send';"><?php echo acymailing_translation('SEND'); ?></button>
				</div>
			</div>
			<div class="clr"></div>
			<input type="hidden" name="cid[]" value="<?php echo $this->mail->mailid; ?>"/>
			<input type="hidden" name="listid" value="<?php echo acymailing_getVar('int', 'listid'); ?>"/>
			<input type="hidden" name="tmpl" value="component"/>
			<?php acymailing_formOptions(); ?>
		</form>

	<?php } ?>
</div>
