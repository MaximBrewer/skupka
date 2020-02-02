<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
	<div id="rsfpsmsnotificationdiv">
		<legend><?php echo JText::_('RSFP_SMSNOTIFICATION_USER_SMS_HEADER') ?></legend>

		<table class="admintable table table-bordered">
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<label for="user_sms"><?php echo JText::_('RSFP_SMSNOTIFICATION_ENABLE_USER_SMS'); ?></label>
				</td>
				<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'user_sms', '', $row->user_sms); ?></td>
			</tr>

			<?php
			if (RSFormProHelper::getConfig('smsnotification.smsservice') !== 'twilio')
			{
				?>

				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key">
						<label for="user_from"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_USER_FROM_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_USER_FROM'); ?>
						</label>
					</td>
					<td>
						<input data-delimiter=" " data-filter-type="include" data-filter="value,global" data-placeholders="display" id="user_from" size="100" maxlength="64" name="user_from" value="<?php echo RSFormProHelper::htmlEscape($row->user_from); ?>" type="text" />
					</td>
				</tr>
				<?php
			} else {
				try
				{
					require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/Twilio/Twilio.php';
					$credentials = RSFPSmsNotification::verifyKey('twilio');

					if (!$credentials['status'])
					{
						throw new Exception(JText::_('RSFP_SMSNOTIFICATION_ERROR_NO_API'));
					}

					$client = new Services_Twilio($credentials['sid'], $credentials['token']);

					foreach ($client->account->incoming_phone_numbers as $number)
					{
						$numbers[] = JHtml::_('select.option', $number->phone_number, $number->friendly_name);
					}

				} catch (Exception $e)
				{
					$application = JFactory::getApplication();
					$application->enqueueMessage($e->getMessage(), 'Warning');
				}

				?>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key">
						<label for="user_from"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_USER_FROM_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_USER_FROM'); ?>
						</label>
					</td>
					<td>
						<select name="user_from" id="user_from">
							<?php echo JHtml::_('select.options',
								$numbers,
								'value', 'text', RSFormProHelper::htmlEscape($row->user_from));
							?>
						</select>

					</td>
				</tr>

			<?php } ?>

			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<label for="user_to"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_USER_TO_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_USER_TO'); ?>
					</label>
				</td>
				<td>
					<input data-delimiter=" " data-filter-type="include" data-filter="value,global" data-placeholders="display" id="user_to" size="100" maxlength="64" name="user_to" value="<?php echo RSFormProHelper::htmlEscape($row->user_to); ?>" type="text" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<label for="user_text"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_USER_TEXT_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_USER_TEXT'); ?>
					</label>
				</td>
				<td>
					<textarea id="user_text" maxlength="459" class="input-xxlarge" rows="5" name="user_text"><?php echo RSFormProHelper::htmlEscape($row->user_text); ?></textarea>
					<span class="count">459</span>
				</td>
			</tr>
		</table>

		<legend><?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_SMS_HEADER') ?></legend>
		<table class="admintable table table-bordered">

			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<label for="admin_sms"><?php echo JText::_('RSFP_SMSNOTIFICATION_ENABLE_ADMIN_SMS'); ?></label>
				</td>
				<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'admin_sms', '', $row->admin_sms); ?></td>
			</tr>

			<?php
			if (RSFormProHelper::getConfig('smsnotification.smsservice') == 'twilio')
			{
				?>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key">
						<label for="admin_from"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_FROM_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_FROM'); ?>
						</label>
					</td>
					<td>
						<select name="admin_from" id="admin_from">
							<?php echo JHtml::_('select.options',
								$numbers,
								'value', 'text', RSFormProHelper::htmlEscape($row->admin_from));
							?>
						</select>

					</td>
				</tr>
			<?php }	else { ?>
				<tr>
					<td width="80" align="right" nowrap="nowrap" class="key">
						<label for="admin_from"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_FROM_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_FROM'); ?>
						</label>
					</td>
					<td>
						<input data-delimiter=" " data-filter-type="include" data-filter="value,global" data-placeholders="display" id="admin_from" size="100" maxlength="64" name="admin_from" value="<?php echo RSFormProHelper::htmlEscape($row->admin_from); ?>" type="text" />
					</td>
				</tr>

			<?php } ?>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<label for="admin_to"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_TO_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_TO'); ?>
					</label>
				</td>
				<td>
					<input data-delimiter=" " data-filter-type="include" data-filter="value,global" data-placeholders="display" id="admin_to" size="100" maxlength="64" name="admin_to" value="<?php echo RSFormProHelper::htmlEscape($row->admin_to); ?>" type="text" />
				</td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key">
					<label for="admin_text"><a class="hasTooltip" href="#" data-toggle="tooltip" title="<?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_TEXT_DESC'); ?>"><span class="rsficon rsficon-info-circle"></span></a> <?php echo JText::_('RSFP_SMSNOTIFICATION_ADMIN_TEXT'); ?>
					</label>
				</td>
				<td>
					<textarea id="admin_text" maxlength="459" class="input-xxlarge" rows="5" name="admin_text"><?php echo RSFormProHelper::htmlEscape($row->admin_text); ?></textarea>
					<span class="count">459</span>
				</td>
			</tr>
		</table>
	</div>

<?php

RSFormProAssets::addScriptDeclaration('
	jQuery(document).ready(function ($) {
		var maxLength = 459;
		$(\'textarea\').each(function () {
			var length = $(this).val().length;
			var length = maxLength - length;
			$(this).next(\'span\').text(length);

			$(this).keyup(function () {
				length = $(this).val().length;
				length = maxLength - length;
				$(this).next(\'span\').text(length);
			});
		})
	});
');

RSFormProAssets::addStyleDeclaration('
	.count {
		display : block;
		color   : #38353A;
		style   : italic;
	}
	#rsfpsmsnotificationdiv .rsfp-dropdown-list-container{
		top: -5px;
	}
');
