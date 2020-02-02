<?php

// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_vm2sms')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');
if(version_compare(JVERSION,'3.0.0','ge')) {
	$controller	= JControllerLegacy::getInstance('Vm2sms');
} else {
	$controller	= JController::getInstance('Vm2sms');
}
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
