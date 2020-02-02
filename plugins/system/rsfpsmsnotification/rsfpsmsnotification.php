<?php
/**
 * @package        RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/smsnotification.php';

/**
 * Class plgSystemRSFPSmsNotification
 */
class plgSystemRSFPSmsNotification extends JPlugin
{
	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * plgSystemRSFPSmsNotification constructor.
	 *
	 * @param       $subject
	 * @param array $config
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$jversion = new JVersion();
		if ($jversion->isCompatible('2.5') && !$jversion->isCompatible('3.0'))
		{
			$this->loadLanguage();
		}
	}

	/**
	 * Save the form properties
	 *
	 * @param $form
	 *
	 * @return bool|void
	 */
	public function rsfp_onFormSave($form)
	{
		$application          = JFactory::getApplication();
		$jinput               = $application->input;
		$postArray            = $jinput->getArray($_POST);
		$postArray['form_id'] = $postArray['formId'];

		try
		{
			$row = JTable::getInstance('RSForm_SmsNotification', 'Table');

			if (!$row)
			{
				throw new Exception ('Could not get an instance of the SMS Notification Table');
			}
			if (!$row->bind($postArray))
			{
				throw new Exception ('Could not bind data from the SMS Notification Table');
			}

			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->qn('form_id'))
				->from($db->qn('#__rsform_smsnotification'))
				->where($db->qn('form_id') . '=' . $db->q((int) $postArray['form_id']));
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$query->clear();
				$query->insert('#__rsform_smsnotification')
					->set($db->qn('form_id') . '=' . $db->q((int) $postArray['form_id']));
				$db->setQuery($query);
				$db->execute();
			}

			if (!$row->store())
			{
				throw new Exception ('Could not save information to database');
			}

		}
		catch (Exception $e)
		{
			$application->enqueueMessage($e->getMessage(), 'error');
		}
	}

	public function rsfp_bk_onFormCopy($args)
	{
		$formId    = $args['formId'];
		$newFormId = $args['newFormId'];

		if ($row = JTable::getInstance('RSForm_SmsNotification', 'Table'))
		{
			if ($row->load($formId))
			{

				if (!$row->bind(array('form_id' => $newFormId)))
				{
					JError::raiseWarning(500, $row->getError());

					return false;
				}

				$db    = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select($db->qn('form_id'))
					->from($db->qn('#__rsform_smsnotification'))
					->where($db->qn('form_id') . '=' . $db->q($newFormId));
				if (!$db->setQuery($query)->loadResult())
				{
					$query = $db->getQuery(true)
						->insert($db->qn('#__rsform_smsnotification'))
						->set($db->qn('form_id') . '=' . $db->q($newFormId));
					$db->setQuery($query)->execute();
				}

				if ($row->store())
				{
					return true;
				}
				else
				{
					JError::raiseWarning(500, $row->getError());

					return false;
				}
			}
		}
	}

	/**
	 * Show the configuration tab (RSForm!Pro - Configuration)
	 *
	 * @param $tabs
	 */
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs)
	{
		$tabs->addTitle(JText::_('RSFP_RSFPSMSNOTIFICATION_TAB_LABEL'), 'rsfp-smsnotification');
		$tabs->addContent($this->showConfigurationScreen());
	}

	/**
	 * The actual content of the Configuration tab
	 *
	 * @return string
	 */
	protected function showConfigurationScreen()
	{
		ob_start();

		$jversion = new JVersion();
		if ($jversion->isCompatible('3.0'))
		{
			JHtml::_('jquery.framework');
		}
		else
		{
			RSFormProAssets::addScript(JURI::root(true) . '/administrator/components/com_rsform/assets/js/jquery.js');
		}

		?>
		<div id="page-rsfpsmsnotification">
			<table class="admintable">
				<tr class="mainsms">
					<td width="200" style="width: 200px;" class="key">
						<label for="smsservice"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_SERVICE'); ?></label></td>
					<td>
						<select name="rsformConfig[smsnotification.smsservice]" id="smsservice">
							<?php echo JHtml::_('select.options',
								array(
									JHtml::_('select.option', 'mainsmsservis', 'Mainsmsservis'),
									JHtml::_('select.option', 'clockwork', 'Clockwork'),
									JHtml::_('select.option', 'twilio', 'Twilio'),
									JHtml::_('select.option', 'smsglobal', 'SMS Global'),
									JHtml::_('select.option', 'clickatell', 'Clickatell'),
									JHtml::_('select.option', '
									', 'Nexmo'),
								),
								'value', 'text', RSFormProHelper::getConfig('smsnotification.smsservice'));
							?>
						</select>
					</td>
				</tr>
				<tr class="usessl">
					<td width="200" style="width: 200px;" class="key">
						<label for="smsnotification-usessl"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_USESSL'); ?></label>
					</td>
					<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[smsnotification.usessl]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.usessl')), JText::_('JYES'), JText::_('JNO')); ?>
					</td>
				</tr>

				<!-- добавляем в админку  mainsms -->
				<tr class="mainsmsservis">
					<td width="200" style="width: 200px;" class="key">
						<label for="mainsmsservisproject">Проэкт</label></td>
					<td>
						<input id="mainsmsservisproject" type="text" name="rsformConfig[smsnotification.mainsmsservisproject]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.mainsmsservisproject')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="mainsmsservis">
					<td width="200" style="width: 200px;" class="key">
						<label for="mainsmsserviskey">Ключ</label></td>
					<td>
						<input id="mainsmsserviskey" type="text" name="rsformConfig[smsnotification.mainsmsserviskey]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.mainsmsserviskey')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<!--добавляем в админку  mainsms -->

				<tr class="clockwork">
					<td width="200" style="width: 200px;" class="key">
						<label for="clockworkkey"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_KEY'); ?></label></td>
					<td>
						<input id="clockworkkey" type="text" name="rsformConfig[smsnotification.clockworkkey]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.clockworkkey')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="twilio">
					<td width="200" style="width: 200px;" class="key">
						<label for="twiliosid"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_TWILIO_SID'); ?></label>
					</td>
					<td>
						<input id="twiliosid" type="text" name="rsformConfig[smsnotification.twiliosid]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.twiliosid')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="twilio">
					<td width="200" style="width: 200px;" class="key">
						<label for="twiliotoken"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_TWILIO_TOKEN'); ?></label>
					</td>
					<td>
						<input id="twiliotoken" type="text" name="rsformConfig[smsnotification.twiliotoken]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.twiliotoken')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="smsglobal">
					<td width="200" style="width: 200px;" class="key">
						<label for="smsglobaluser"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_SMSGLOBAL_USER'); ?></label>
					</td>
					<td>
						<input id="smsglobaluser" type="text" name="rsformConfig[smsnotification.smsglobaluser]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.smsglobaluser')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="smsglobal">
					<td width="200" style="width: 200px;" class="key">
						<label for="smsglobalpassword"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_SMSGLOBAL_PASSWORD'); ?></label>
					</td>
					<td>
						<input id="smsglobalpassword" type="password" name="rsformConfig[smsnotification.smsglobalpassword]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.smsglobalpassword')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="clickatell">
					<td width="200" style="width: 200px;" class="key">
						<label for="clickatellusername"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_CLICKATELL_USERNAME'); ?></label>
					</td>
					<td>
						<input id="clickatellusername" type="text" name="rsformConfig[smsnotification.clickatellusername]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.clickatellusername')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="clickatell">
					<td width="200" style="width: 200px;" class="key">
						<label for="clickatellpassword"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_CLICKATELL_PASSWORD'); ?></label>
					</td>
					<td>
						<input id="clickatellpassword" type="password" name="rsformConfig[smsnotification.clickatellpassword]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.clickatellpassword')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="clickatell">
					<td width="200" style="width: 200px;" class="key">
						<label for="clickatellapiid"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_CLICKATELL_APIID'); ?></label>
					</td>
					<td>
						<input id="clickatellapiid" type="text" name="rsformConfig[smsnotification.clickatellapiid]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.clickatellapiid')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="clickatell">
					<td width="200" style="width: 200px;" class="key">
						<label for="clickatellmo" class="hasPopover" data-original-title="<?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_CLICKATEL_MO_PARAM'); ?>" data-content="<?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_CLICKATEL_MO_PARAM_DESC') ?>"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_CLICKATEL_MO_PARAM'); ?></label>
					</td>
					<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'rsformConfig[smsnotification.clickatellmo]', '', RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.clickatellmo')), JText::_('JYES'), JText::_('JNO')); ?>
					</td>
				</tr>
				<tr class="nexmo">
					<td width="200" style="width: 200px;" class="key">
						<label for="nexmokey"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_NEXMO_KEY'); ?></label>
					</td>
					<td>
						<input id="nexmokey" type="text" name="rsformConfig[smsnotification.nexmokey]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.nexmokey')); ?>" size="100" maxlength="64">
					</td>
				</tr>
				<tr class="nexmo">
					<td width="200" style="width: 200px;" class="key">
						<label for="nexmosecret"><?php echo JText::_('RSFP_RSFPSMSNOTIFICATION_NEXMO_SECRET'); ?></label>
					</td>
					<td>
						<input id="nexmosecret" type="text" name="rsformConfig[smsnotification.nexmosecret]" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('smsnotification.nexmosecret')); ?>" size="100" maxlength="64">
					</td>
				</tr>
			</table>
		</div>

		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		RSFormProAssets::addScriptDeclaration('
			jQuery(document).ready(function ($) {
				var $smsselect = $(\'#smsservice\');
				var $defaultsms = $smsselect.val();

				$(\'.mainsms\').siblings(\'tr\').not(\'.usessl, .\' + $defaultsms).hide();
				$(\'.mainsms\').siblings(\'tr.\' + $defaultsms).show();

				$smsselect.change(function () {
					var $val = $(this).val();
					$(\'.mainsms\').siblings(\'tr\').not(\'.usessl, .\' + $val).hide();
					$(\'.mainsms\').siblings(\'tr.\' + $val).show();
				});
			});
		');

		return $contents;
	}

	/**
	 *
	 * Add the button for the SMS Notification in the Form Properties
	 *
	 */
	public function rsfp_bk_onAfterShowFormEditTabsTab()
	{
		echo '<li><a href="javascript: void(0);" id="rsfpsmsnotification"><span class="rsficon rsficon-envelope-square"></span><span class="inner-text">' . JText::_('RSFP_RSFPSMSNOTIFICATION_LABEL') . '</span></a></li>';
	}

	/**
	 *
	 * Create the backend form for the properties
	 *
	 */
	public function rsfp_bk_onAfterShowFormEditTabs()
	{
		$formId = JFactory::getApplication()->input->getInt('formId', 0);
		$row    = JTable::getInstance('RSForm_SmsNotification', 'Table');

		if (!$row)
		{
			return;
		}

		$row->load($formId);

		// Load the view
		include_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification.php';

	}

	/**
	 * After placeholders are created, we create and send the SMS message
	 *
	 * @param $args
	 *
	 * @return bool
	 */
	public function rsfp_f_onAfterFormProcess($args)
	{
		$application  = JFactory::getApplication();
		$type         = RSFormProHelper::getConfig('smsnotification.smsservice');



		$SubmissionId = (int) $args['SubmissionId'];
		$credentials  = RSFPSmsNotification::verifyKey($type);
		$formId       = $args['formId'];
		$row          = JTable::getInstance('RSForm_SmsNotification', 'Table');



		if (!$credentials['status'])
		{
			return false;
		}

		if (!$row)
		{
			return false;
		}

		$row->load($formId);

		list($placeholders, $values) = RSFormProHelper::getReplacements($SubmissionId);

		$args = array(
			'placeholders' => $placeholders,
			'values'       => $values,
		);

		$Notifier = new RSFPSmsNotification($type, $credentials, $row, $args);
		$log      = $Notifier->sendMessages();



		//**********************
		// echo '<pre>';
//		include_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/mainsms.class.php';
//		$api = new MainSMS('your_project_name', 'your_api_key', false, false);
		// var_dump($log);
		// var_dump($Notifier);
		// die();
		// echo '</pre>';
		//**********************

		if (!empty($log))
		{
			foreach ($log as $result)
			{
				if (!(bool) $result['status'])
				{
					$application->enqueueMessage(JText::sprintf('RSFP_SMSNOTIFICATION_ERROR', $result['type'], RSFormProHelper::htmlEscape($result['error_message'])), 'error');
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * @param $formId
	 */
	public function rsfp_onFormDelete($formId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__rsform_smsnotification')
			->where($db->qn('form_id') . '=' . $db->q($formId));
		$db->setQuery($query)->execute();
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormBackup($form, $xml, $fields)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->qn('#__rsform_smsnotification'))
			->where($db->qn('form_id') . '=' . $db->q($form->FormId));
		$db->setQuery($query);
		if ($result = $db->loadObject())
		{
			// No need for a form_id
			unset($result->form_id);

			$xml->add('smsnotification');
			foreach ($result as $property => $value)
			{
				$xml->add($property, $value);
			}
			$xml->add('/smsnotification');
		}
	}

	/**
	 * @param $form
	 * @param $xml
	 * @param $fields
	 */
	public function rsfp_onFormRestore($form, $xml, $fields)
	{
		if (isset($xml->smsnotification))
		{
			$data = array(
				'form_id' => $form->FormId
			);
			foreach ($xml->smsnotification->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}
			$row = JTable::getInstance('RSForm_SMSNotification', 'Table');

			if (!$row->load($form->FormId))
			{
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->insert('#__rsform_smsnotification')
					->set(array(
						$db->qn('form_id') . '=' . $db->q($form->FormId),
					));
				$db->setQuery($query)->execute();
			}

			$row->save($data);
		}
	}

	/**
	 *
	 */
	public function rsfp_bk_onFormRestoreTruncate()
	{
		JFactory::getDbo()->truncateTable('#__rsform_smsnotification');
	}
}