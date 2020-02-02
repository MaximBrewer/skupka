<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/ 
// no direct access
defined('_JEXEC') or die('Restricted access');

// general joomla files: 

if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');


if(version_compare(JVERSION,'3.0.0','ge')) 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj3.php'); 
else
if(version_compare(JVERSION,'1.7.0','ge')) 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj2.php'); 
else
if(version_compare(JVERSION,'1.5.0','ge')) 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj1.php'); 