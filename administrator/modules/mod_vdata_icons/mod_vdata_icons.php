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

$mod_name = 'mod_vdata_icons';

$document 	= JFactory::getDocument();
$input 		= JFactory::getApplication()->input;

$document->addStyleSheet(JURI::base(true).'/modules/'.$mod_name.'/tmpl/css/vdata-style.css');

require JModuleHelper::getLayoutPath($mod_name,$params->get('layout','default'));