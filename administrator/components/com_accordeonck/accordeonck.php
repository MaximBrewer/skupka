<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_accordeonck'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$lang	= JFactory::getLanguage();
$lang->load('com_modules');
$lang->load('com_accordeonck');

JHtml::_('jquery.framework', true);
$doc = JFactory::getDocument();
$doc->addScript(JUri::root(true) . '/administrator/components/com_accordeonck/assets/ckbox.js');
$doc->addStylesheet(JUri::root(true) . '/administrator/components/com_accordeonck/assets/ckbox.css');

$controller	= JControllerLegacy::getInstance('Accordeonck');
if (!JFactory::getApplication()->input->get('view')) JFactory::getApplication()->input->set('view','modules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
