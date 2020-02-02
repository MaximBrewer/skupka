<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controller');

Class Sh404sefControllerConfiguration extends ShlMvcController_Base
{

	protected $_context = 'com_sh404sef.configuration';

	/**
	 * Method to save the new configuration
	 * using a model from com_config
	 */
	public function saveconfiguration()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// collect data
		$data = JRequest::getVar('jform', array(), 'post', 'array');

		$failure = array('url' => 'index.php?option=com_sh404sef&c=configuration&view=configuration&tmpl=component',
			'message' => JText::sprintf('JERROR_SAVE_FAILED', $this->getError()));
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$success = array('url' => 'index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&layout=refresh',
							'message' => JText::_('COM_SH404SEF_ELEMENT_SAVED'));
		} else {
			$success = array('url' => 'index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&layout=close',
							'message' => JText::_('COM_SH404SEF_ELEMENT_SAVED'));
		}

		$status = $this->_doSave($data, $success, $failure);

		return $status;
	}

	/**
	 * Saves data from the quickStart pane on main dashboard
	 */
	public function saveqcontrol()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		try
		{
			// get current settings for sh404SEF
			$params = Sh404sefHelperGeneral::getComponentParams($forceRead = true);

			// set params from the form
			$params->set('Enabled', JRequest::getInt('Enabled', 0));
			$params->set('canReadRemoteConfig', JRequest::getInt('canReadRemoteConfig', 0));
			$params->set('shRewriteMode', JRequest::getInt('shRewriteMode', 1));
			$params->set('shSecEnableSecurity', JRequest::getInt('shSecEnableSecurity', 1));

			// convert to json and store into db
			$textParams = $params->toString();

			ShlDbHelper::update('#__extensions', array('params' => $textParams), array('element' => 'com_sh404sef', 'type' => 'component'));
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SH404SEF_ELEMENT_SAVED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SH404SEF_ELEMENT_NOT_SAVED'), 'error');
		}

		parent::display();
	}

	/**
	 * Saves configuration data coming from a form filled up by user
	 *
	 * @param array $data data to be saved, according to config.xml description
	 * @param array $success url and message to display and redirect to on success
	 * @param array $failure url and message to display and redirect to on failure
	 * @return boolean true on success
	 */
	private function _doSave($data, $success, $failure)
	{
		$component = JComponentHelper::getComponent('com_sh404sef');

		// instantiate model, and pass data to be saved
		$model = new Sh404sefModelConfiguration();
		$status = $model->save($data, $component->id);

		// Check the return value.
		if (!$status)
		{
			// Save failed, go back to the screen and display a notice.
			$this->setRedirect(JRoute::_($failure['url'], false), $failure['message'] . $model->getError(), 'error');
			return false;
		}

		$this->setRedirect(JRoute::_($success['url'], false), $success['message'], 'message');
		return true;
	}
}
