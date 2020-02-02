<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_sl_advpoll')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include CSS and JS
JHtml::_('behavior.framework', true);
JHtml::_('jquery.framework');

JHtml::_('script', 'com_sl_advpoll/admin.script.js', array(), true);
JHtml::_('stylesheet', 'com_sl_advpoll/admin.style.css', array(), true);


// Include dependancies
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once(JPATH_COMPONENT . '/helpers/factory.php');
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('SL_AdvPoll');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();