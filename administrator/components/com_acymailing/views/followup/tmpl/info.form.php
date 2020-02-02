<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><table class="acymailing_table" width="100%">
	<tr>
		<td class="acykey">
			<label for="subject">
				<?php echo acymailing_translation('JOOMEXT_SUBJECT'); ?>
			</label>
		</td>
		<td>
			<div>
				<input onClick="zoneToTag='subject';" type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>"/>
			</div>
		</td>
		<td class="acykey">
			<label for="published">
				<?php echo acymailing_translation('ACY_PUBLISHED'); ?>
			</label>
		</td>
		<td>
			<?php echo ($this->mail->published == 2) ? acymailing_translation('SCHED_NEWS') : acymailing_boolean("data[mail][published]", '', $this->mail->published); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<label for="alias">
				<?php echo acymailing_translation('JOOMEXT_ALIAS'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][alias]" id="alias" style="width:200px" value="<?php echo @$this->mail->alias; ?>"/>
		</td>
		<td class="acykey">
			<label for="html">
				<?php echo acymailing_translation('SEND_HTML'); ?>
			</label>
		</td>
		<td>
			<?php echo acymailing_boolean("data[mail][html]", 'onclick="updateAcyEditor(this.value)"', $this->mail->html); ?>
		</td>
	</tr>
	<?php
	$jflanguages = acymailing_get('type.jflanguages');
	if($jflanguages->multilingue){ ?>
		<tr>
			<td class="acykey" id="languagekey">
				<label for="language">
					<?php echo acymailing_translation('ACY_LANGUAGE'); ?>
				</label>
			</td>
			<td>
				<?php
				$jflanguages->sef = true;
				echo $jflanguages->displayJLanguages('data[mail][language]', empty($this->mail->language) ? '' : $this->mail->language);
				?>
			</td>
			<td colspan="2"/>
		</tr>
	<?php } ?>
	<tr>
		<td class="acykey" id="delaykey">
			<label for="delayvalue1">
				<?php echo acymailing_translation('DELAY'); ?>
			</label>
		</td>
		<td>
			<?php echo $this->values->delay->display('data[mail][senddate]', (int)@$this->mail->senddate); ?>
		</td>
		<td class="acykey" id="createdkey">
			<label for="created">
				<?php echo acymailing_translation('CREATED_DATE'); ?>
			</label>
		</td>
		<td>
			<?php echo acymailing_getDate(@$this->mail->created); ?>
		</td>
	</tr>
</table>
