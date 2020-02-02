<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><table width="100%">
	<tr>
		<td class="acykey" id="subjectkey">
			<label for="subject">
				<?php echo acymailing_translation('JOOMEXT_SUBJECT'); ?>
			</label>
		</td>
		<td>
			<div>
				<input type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>" onClick="zoneToTag='subject';"/>
			</div>
		</td>
		<td class="acykey" id="publishedkey">
			<label for="published">
				<?php echo acymailing_translation('ACY_PUBLISHED'); ?>
			</label>
		</td>
		<td>
			<?php echo acymailing_boolean("data[mail][published]", '', $this->mail->published); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="aliaskey">
			<label for="alias">
				<?php echo acymailing_translation('JOOMEXT_ALIAS'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][alias]" id="alias" style="width:60%" value="<?php echo $this->escape(@$this->mail->alias); ?>"/>
		</td>
		<td class="acykey" id="htmlkey">
			<label for="html">
				<?php echo acymailing_translation('SEND_HTML'); ?>
			</label>
		</td>
		<td>
			<?php echo acymailing_boolean("data[mail][html]", 'onchange="updateAcyEditor(this.value)"', $this->mail->html); ?>
		</td>
	</tr>
	<?php
	$jflanguages = acymailing_get('type.jflanguages');

	if($jflanguages->multilingue || acymailing_level(3)){
		echo '<tr>';
		if($jflanguages->multilingue){ ?>
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
		<?php
			if(!acymailing_level(3)){
				echo '<td colspan="2"/>';
			}
		}
		if(acymailing_level(3)){
			?>
				<td class="acykey" id="createdkey" valign="top">
					<label for="createdinput">
						<?php echo acymailing_translation('TAGS'); ?>
					</label>
				</td>
				<td id="taginput" valign="top">
					<?php
					$tagfieldtype = acymailing_get('type.tagfield');
					echo $tagfieldtype->display('tags', $this->mail);
					?>
				</td>
			<?php
			if(!$jflanguages->multilingue){
				echo '<td colspan="2"/>';
			}
		}
		echo '</tr>';
	}
		?>
	<tr>
		<td class="acykey" id="senddatekey" valign="top">
			<label for="senddate">
				<?php echo acymailing_translation('NEXT_GENERATE'); ?>
			</label>
		</td>
		<td valign="top">
			<?php
			if(empty($this->mail->senddate)) $this->mail->senddate = time();

			if('joomla' == 'wordpress'){
				echo acymailing_calendar(acymailing_getDate($this->mail->senddate, '%Y-%m-%d %H:%M'), 'data[mail][senddate]', 'senddate', '%Y-%m-%d', array('style' => 'width:90px'));
				echo '<input style="width: 45px;" min="0" max="23" type="number" name="senddate_hour" value="'.acymailing_getDate($this->mail->senddate, '%H').'" /> : ';
				echo '<input style="width: 45px;" min="0" max="59" type="number" name="senddate_minute" value="'.acymailing_getDate($this->mail->senddate, '%M').'" />';
			}else{
				echo acymailing_calendar(acymailing_getDate($this->mail->senddate, '%Y-%m-%d %H:%M'), 'data[mail][senddate]', 'senddate', '%Y-%m-%d %H:%M', array('style' => 'width:160px'));
			}
			?>
		</td>
		<td class="acykey" id="frequencykey" valign="top">
			<label for="frequencyType">
				<?php echo acymailing_translation('GENERATE_FREQUENCY'); ?>
			</label>
		</td>
		<td valign="top">
			<?php echo $this->frequencyType->displayFrequency('data[mail][frequency]', @$this->mail->frequency, 3); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="issuenbkey">
			<label for="issuenb">
				<?php echo acymailing_translation('ISSUE_NB'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" id="issuenb" name="data[mail][params][issuenb]" style="width:50px" value="<?php echo empty($this->mail->params['issuenb']) ? 1 : $this->mail->params['issuenb']; ?>"/>
		</td>
		<td class="acykey" id="lastgenerateddatekey">
			<label for="lastgenerateddate">
				<?php echo acymailing_translation('LAST_RUN'); ?>
			</label>
		</td>
		<td>
			<input type="text" class="inputbox" id="lastgenerateddate" style="width:200px" value="<?php echo $this->escape(acymailing_getDate(@$this->mail->params['lastgenerateddate'], '%Y-%m-%d %H:%M')); ?>" name="data[mail][params][lastgenerateddate]"/>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="generatekey">
			<label for="datamailparamsgenerate">
				<?php echo acymailing_translation('GENERATE_MODE'); ?>
			</label>
		</td>
		<td>
			<?php echo $this->generatingMode->display('data[mail][params][generate]', @$this->mail->params['generate']); ?>
		</td>
		<td class="acykey" id="generatetokey">
			<label for="generateto">
				<?php echo acymailing_translation('NOTIFICATION_TO'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][params][generateto]" id="generateto" style="width:200px" value="<?php echo $this->escape(@$this->mail->params['generateto']); ?>" placeholder="address@example.com"/>
		</td>
	</tr>
</table>
