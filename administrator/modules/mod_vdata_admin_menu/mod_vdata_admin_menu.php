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

$user		= JFactory::getUser();
if ($user->guest) return;

// Include the module helper classes.
if (!class_exists('ModVdataMenuHelper')) {
	require dirname(__FILE__).'/helper.php';
}

$lang 	= JFactory::getLanguage();

$input 	= JFactory::getApplication()->input;
$vdataComponentItems = ModVdataMenuHelper::getVdataComponent(true); 
$hideMainmenu = $input->getBool('hidemainmenu') ? false : true;
require JModuleHelper::getLayoutPath('mod_vdata_admin_menu', $params->get('layout', 'default'));
