<?php
/**
 * @package         Sliders
 * @version         6.0.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Button Plugin that places Editor Buttons
 */
class PlgButtonSliders extends JPlugin
{
	private $_alias = 'sliders';

	private $_init   = false;
	private $_helper = null;

	/**
	 * Display the button
	 *
	 * @return array  A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		if (!$this->getHelper())
		{
			return;
		}

		return $this->_helper->render($name);
	}

	/*
	 * Below methods are general functions used in most of the Regular Labs extensions
	 * The reason these are not placed in the Regular Labs Library files is that they also
	 * need to be used when the Regular Labs Library is not installed
	 */

	/**
	 * Create the helper object
	 *
	 * @return object The plugins helper object
	 */
	private function getHelper()
	{
		// Already initialized, so return
		if ($this->_init)
		{
			return $this->_helper;
		}

		$this->_init = true;

		if (!$this->isFrameworkEnabled())
		{
			return false;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/protect.php';

		if (!RLProtect::isSystemPluginInstalled($this->_alias))
		{
			return false;
		}

		// Load plugin parameters
		require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
		$parameters = RLParameters::getInstance();
		$params     = $parameters->getPluginParams($this->_name);

		if (RLProtect::isRestrictedComponent(isset($params->disabled_components) ? $params->disabled_components : array()))
		{
			return false;
		}

		// allow in frontend?
		if (!$params->enable_frontend && JFactory::getApplication()->isSite())
		{
			return false;
		}

		require_once JPATH_LIBRARIES . '/regularlabs/helpers/helper.php';
		$this->_helper = RLHelper::getPluginHelper($this, $params);

		return $this->_helper;
	}

	/**
	 * Check if the Regular Labs Library is enabled
	 *
	 * @return bool
	 */
	private function isFrameworkEnabled()
	{
		// Return false if Regular Labs Library is not installed
		if (!$this->isFrameworkInstalled())
		{
			return false;
		}

		$regularlabs = JPluginHelper::getPlugin('system', 'regularlabs');

		return isset($regularlabs->name);
	}

	/**
	 * Check if the Regular Labs Library is installed
	 *
	 * @return bool
	 */
	private function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		return JFile::exists(JPATH_PLUGINS . '/system/regularlabs/regularlabs.php');
	}
}
