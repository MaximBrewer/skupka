<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

if(!class_exists('VmView'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
else
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmview.php');
}
 
class virtuemartViewtest extends OPCView
{
	
	 function display($tpl = null) {
		 require(__DIR__.DIRECTORY_SEPARATOR.'test.config.php'); 
		 
		  $dev = JRequest::getVar('dev', false); 
		 if (!$dev) {
		 if (empty($ip)) return; 
		 if (!in_array($_SERVER['REMOTE_ADDR'], $ip)) return; 
		 }
		 
		 
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'test.calc.php');	 
		$OPCTest = new OPCTest; 
		$OPCTest->runTest(); 
	
	 }
	 
	
}