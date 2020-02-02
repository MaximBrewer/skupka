<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_maximenuck'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$lang	= JFactory::getLanguage();
$lang->load('com_modules');
$lang->load('com_maximenuck');

JHtml::_('jquery.framework', true);
$doc = JFactory::getDocument();
$doc->addScript(JUri::root(true) . '/administrator/components/com_maximenuck/assets/ckbox.js');
$doc->addStylesheet(JUri::root(true) . '/administrator/components/com_maximenuck/assets/ckbox.css');

$controller	= JControllerLegacy::getInstance('Maximenuck');
if (!JFactory::getApplication()->input->get('view')) JFactory::getApplication()->input->set('view','modules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
