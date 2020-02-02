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
	<span class="acyblocktitle"><?php echo acymailing_translation('ACY_CONDITIONS'); ?></span>
	<table cellspacing="1" width="100%">
		<tr>
			<td valign="top" style="width:200px;">
				<?php echo acymailing_translation('ACY_ALLOWED_SENDER'); ?>
			</td>
			<td>
				<?php
				$sender = array();
				$sender[] = acymailing_selectOption('all', acymailing_translation('ACY_ALL'));
				$sender[] = acymailing_selectOption('specific', acymailing_translation('ACY_SPECIFIC'));
				$sender[] = acymailing_selectOption('group', acymailing_translation('ACY_GROUP'));
				$sender[] = acymailing_selectOption('list', acymailing_translation('LIST'));

				echo acymailing_select($sender, "data[conditions][sender]", 'size="1" style="width:125px;" onchange="displayAllowedOptions(this.value);"', 'value', 'text', $this->escape(@$this->action->conditions['sender'])).' ';

				echo $this->allowedoptions->specific;
				echo $this->allowedoptions->group;
				echo $this->allowedoptions->list;
				?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo acymailing_translation('ACY_ALLOWED_SUBJECT'); ?>
			</td>
			<td style="width: 36%">
				<?php
				$subjectchoices = array();
				$subjectchoices[] = (object)array('value' => 'all', 'text' => acymailing_translation('ACY_ALL'));
				$subjectchoices[] = (object)array('value' => 'begins', 'text' => acymailing_translation('ACY_BEGINS_WITH'));
				$subjectchoices[] = (object)array('value' => 'ends', 'text' => acymailing_translation('ACY_ENDS_WITH'));
				$subjectchoices[] = (object)array('value' => 'contains', 'text' => acymailing_translation('ACY_CONTAINS'));
				echo acymailing_select($subjectchoices, "data[conditions][subject]", 'size="1" style="width:125px;" onchange="if(this.value == \'all\'){document.getElementById(\'dataconditionssubjectvalue\').style.display = \'none\';}else{document.getElementById(\'dataconditionssubjectvalue\').style.display = \'\';}"', 'value', 'text', $this->escape(@$this->action->conditions['subject']));
				$hideSubject = empty($this->action->conditions['subject']) || $this->action->conditions['subject'] == 'all' ? 'display:none;' : '';
				?>
				<input type="text" name="data[conditions][subjectvalue]" id="dataconditionssubjectvalue" value="<?php echo @$this->action->conditions['subjectvalue']; ?>" style="<?php echo $hideSubject; ?>width:200px;" />
			</td>
			<td>
				<?php if(!empty($this->removeSub)){
					echo '<input type="checkbox" value="1" name="data[conditions][removeSubject]" id="removeFromSubject" checked>';
				}else{
					echo '<input type="checkbox" value="1" name="data[conditions][removeSubject]" id="removeFromSubject">';
				} ?>
				<label for="removeFromSubject"><?php echo acymailing_translation('ACY_REMOVE_FROM_SUBJECT') ?></label>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo acymailing_translation('ACY_DELETE_WRONG_EMAILS'); ?>
			</td>
			<td>
				<?php echo acymailing_boolean("data[action][delete_wrong_emails]", '', @$this->action->delete_wrong_emails); ?>
			</td>
			<td></td>
		</tr>
	</table>
</div>
