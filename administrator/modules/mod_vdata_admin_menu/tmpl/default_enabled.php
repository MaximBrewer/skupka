<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined ('_JEXEC') or die ('resticted aceess');

$user = JFactory::getUser();
$lang = JFactory::getLanguage();

if ($user->authorise('core.manage', 'com_vdata'))
{
	$menu->addChild(JText::_('MOD_MENU_COM_VDATA'), true);
	
	/* $menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_DASHBOARD'), 'index.php?option=com_vdata&view=vdata', 'class:dashboard'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_CONFIG'), 'index.php?option=com_vdata&view=config', 'class:config'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_PROFILES'), 'index.php?option=com_vdata&view=profiles', 'class:profiles'));
	
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_IMPORT'), 'index.php?option=com_vdata&view=import', 'class:import'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_EXPORT'), 'index.php?option=com_vdata&view=export', 'class:export'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_CRON'), 'index.php?option=com_vdata&view=schedules', 'class:schedules'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_QUICK'), 'index.php?option=com_vdata&view=quick', 'class:quick'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_NOTIFICATIONS'), 'index.php?option=com_vdata&view=notifications', 'class:notifications'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_DISPLAY'), 'index.php?option=com_vdata&view=display', 'class:display'));
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_VDATA_LOGS'), 'index.php?option=com_vdata&view=logs', 'class:logs'));
	$menu->getParent(); */
}