<?php
/**
 * @package         Advanced Module Manager
 * @version         7.5.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');

if ( ! JFactory::getUser()->authorise('core.manage', 'com_modules'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

jimport('joomla.filesystem.file');

// return if Regular Labs Library plugin is not installed
if (
	! is_file(JPATH_PLUGINS . '/system/regularlabs/regularlabs.xml')
	|| ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php')
)
{
	$msg = JText::_('AMM_REGULAR_LABS_LIBRARY_NOT_INSTALLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');

	return;
}

// give notice if Regular Labs Library plugin is not enabled
if ( ! JPluginHelper::isEnabled('system', 'regularlabs'))
{
	$msg = JText::_('AMM_REGULAR_LABS_LIBRARY_NOT_ENABLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

use RegularLabs\Library\Language as RL_Language;

RL_Language::load('plg_system_regularlabs');
RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('AdvancedModules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
