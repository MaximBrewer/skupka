<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

class Sh404sefHelperMsg
{
	private static $instance;

	public static function updateSystemMessages()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new Sh404sefHelperMsg();
		}

		// run various checks on the system, and possibly
		// store messages, to be displayed later on in
		// the message center
		self::$instance->welcome()
			->languageWarning()
			->joomlaSettings()
			->checkAnalytics()
			->checkSourceRecording()
			->checkFileAccess()
			->checkNewVersion();
	}

	/*
	 * Provide an empty base message
	 *
	 */
	private function buildMessage($msg)
	{
		$baseMsg = array(
			'scope'        => 'com_sh404sef',
			'type'         => '',
			'sub_type'     => '',
			'display_type' => ShlMsg_Manager::DISPLAY_TYPE_INFO,
			'title'        => '',
			'body'         => '',
			'action'       => ShlMsg_Manager::ACTION_CAN_CLOSE
		);

		return array_merge($baseMsg, $msg);
	}

	private function welcome()
	{
		$msg = $this->buildMessage(
			array(
				'type'     => 'sh404sef.general',
				'sub_type' => 'welcome',
				'title'    => JText::_('COM_SH404SEF_WELCOME'),
				'body'     => JText::_('COM_SH404SEF_QCONTROL')
			)
		);
		ShlMsg_Manager::getInstance()->addOnce($msg);
		return $this;
	}

	/**
	 * Display an optional message about the language filter
	 * being enabled or not
	 *
	 * To be removed approx. beginning 2016?
	 */
	private function languageWarning()
	{
		$shouldShow = Sh404sefHelperLanguage::shouldShowLanguageFilterWarning();
		$msg = $this->buildMessage(
			array(
				'type'         => 'joomla.config',
				'sub_type'     => 'language_filter_warning',
				'title'        => JText::_('COM_SH404SEF_WARNING_LANGUAGE_FILTER_TITLE'),
				'body'         => JText::_('COM_SH404SEF_LANGUAGEFILTER_PLUGIN_WARNING'),
				'action'       => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_24H,
				'display_type' => ShlMsg_Manager::DISPLAY_TYPE_ERROR
			)
		);
		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	private function joomlaSettings()
	{
		$model = new Sh404sefModelConfiguration();
		$shouldShow = !$model->checkJoomlaConfig();
		$msg = $this->buildMessage(
			array(
				'type'         => 'joomla.config',
				'sub_type'     => 'sef_settings_dont_match',
				'title'        => JText::_('COM_SH404SEF_WARNING_JOOMLA_SETTINGS'),
				'body'         => JText::_('COM_SH404SEF_WARNING_JOOMLA_SETTINGS_SEF'),
				'action'       => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_24H,
				'display_type' => ShlMsg_Manager::DISPLAY_TYPE_ERROR
			)
		);
		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	private function checkAnalytics()
	{
		$model = new Sh404sefModelConfiguration();
		$shouldShow = !$model->checkAnalytics();
		$msg = $this->buildMessage(
			array('type'         => 'sh404sef.config',
			      'sub_type'     => 'analytics_configuration_issue',
			      'title'        => JText::_('COM_SH404SEF_WARNING_ANALYTICS_TITLE'),
			      'body'         => JText::_('COM_SH404SEF_WARNING_ANALYTICS'),
			      'action'       => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_24H,
			      'display_type' => ShlMsg_Manager::DISPLAY_TYPE_ERROR
			)
		);
		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	private function checkSourceRecording()
	{
		$shouldShow = Sh404sefFactory::getConfig()->logUrlsSource;
		$msg = $this->buildMessage(
			array('type'         => 'sh404sef.general',
			      'sub_type'     => 'url_source_recording_enabled',
			      'title'        => JText::_('COM_SH404SEF_WARNING_SRC_RECORDING_TITLE'),
			      'body'         => '',
			      'action'       => ShlMsg_Manager::ACTION_CANNOT_CLOSE,
			      'display_type' => ShlMsg_Manager::DISPLAY_TYPE_ERROR
			)
		);
		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	private function checkFileAccess()
	{
		$config = Sh404sefFactory::getConfig();
		$status = $config->shCheckFilesAccess();
		$canAccess = array_filter($status['canAccess']);
		$shouldShow = !empty($canAccess);
		if ($shouldShow)
		{
			$body = '';
			foreach ($status['html'] as $key => $value)
			{
				$body .= '<li>' . $key . $value;
			}
			$body = '<ul>' . $body . '</ul>';
		}
		else
		{
			$body = '';
		}
		$msg = $this->buildMessage(
			array('type'         => 'sh404sef.config',
			      'sub_type'     => 'config.cannot_access_some_paths',
			      'title'        => JText::_('COM_SH404SEF_WARNING_PATH_ACCESS'),
			      'body'         => $body,
			      'action'       => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_7D,
			      'display_type' => ShlMsg_Manager::DISPLAY_TYPE_ERROR
			)
		);

		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	private function checkNewVersion()
	{
		$updates = Sh404sefHelperUpdates::getUpdatesInfos();

		// delete all previous notifications
		$manager = ShlMsg_Manager::getInstance();
		if ($updates->shouldUpdate)
		{
			$msg = $this->buildMessage(
				array('type'         => 'sh404sef.updates',
				      'sub_type'     => $updates->current,
				      'title'        => JText::sprintf('COM_SH404SEF_WARNING_UPDATE_AVAILABLE', $updates->current),
				      'body'         => '',
				      'display_type' => ShlMsg_Manager::DISPLAY_TYPE_INFO,
				      'action'       => ShlMsg_Manager::ACTION_CANNOT_CLOSE
				)
			);

			// delete previous update notif
			$manager->delete(
				array(
					'scope'    => 'com_sh404sef',
					'type'     => 'sh404sef.updates',
					'acked_on' => '0000-00-00 00:00:00'
				)
			);
			// add the latest
			$manager->add($msg,
				array(
					'scope'    => 'com_sh404sef',
					'sub_type' => $updates->current,
					'type'     => 'sh404sef.updates',
					'acked_on' => '0000-00-00 00:00:00'
				)
			);
		}
		else
		{
			$manager->acknowledge(
				array(
					'scope' => 'com_sh404sef',
					'type'  => 'sh404sef.updates',
				),
				$force = true
			);
		}

		return $this;
	}

	private function addUnlessNotAcknowledged($shouldShow, $msg)
	{
		if ($shouldShow)
		{
			ShlMsg_Manager::getInstance()->addUnlessNotAcknowledged($msg);
		}
		else
		{
			// clear any non-acknowledged instance
			ShlMsg_Manager::getInstance()->acknowledge(
				array(
					'scope'    => $msg['scope'],
					'type'     => $msg['type'],
					'sub_type' => $msg['sub_type']
				),
				$force = true
			);
		}

		return $this;
	}
}
