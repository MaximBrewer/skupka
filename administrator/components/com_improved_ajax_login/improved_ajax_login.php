<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
$revision = '2.4.166';
$revision = '2.4.166';
?><?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');
if (!class_exists('JControllerLegacy')) {
	class JControllerLegacy extends JController {}
	class JViewLegacy extends JView {}
	class JModelLegacy extends JModel {}
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_improved_ajax_login'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}
$GLOBALS['j25'] = version_compare(JVERSION, '3.0.0', 'l');

// Include dependancies
$controller	= JControllerLegacy::getInstance('Improved_ajax_login');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
