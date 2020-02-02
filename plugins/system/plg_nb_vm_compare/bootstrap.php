<?php

/*------------------------------------
* System Compare Products for Virtuemart
* Author    CMSMart Team
* Copyright Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
* Version 1.0.0
-----------------------------------------------------*/
define( 'DS', DIRECTORY_SEPARATOR );
$rootFolder = explode(DS,dirname(__FILE__));
$currentfolderlevel = 3;
array_splice($rootFolder,-$currentfolderlevel);
$base_folder = implode(DS,$rootFolder);
if(!is_dir($base_folder.DS.'libraries'.DS.'joomla')) exit('Error: Could not loaded Joomla.');
define( '_JEXEC', 1 );
define('JPATH_BASE',implode(DS,$rootFolder));
// Include the Joomla framework
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$application  = JFactory::getApplication('site');
$application ->initialise();
$rootUri = explode('/',JURI::root(true));
array_splice($rootUri,-$currentfolderlevel);
$base_uri =   JURI::getInstance()->getScheme().'://'
			. JURI::getInstance()->getHost()
			. implode('/',$rootUri).'/';

