<?php 
/*
 * This file is here for broader compatibility with Joomla system
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 *
*/
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );



$view = JRequest::getVar('view', '');  
if ($view !== 'test') {
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 


// disable cache for all one page pages
if (class_exists('JCache'))
{
 $options = array(
			'defaultgroup'	=> 'page',
			'browsercache'	=> false,
			'caching'		=> false,
		);
 $caching = JCache::getInstance('page', $options);
 $caching->setCaching(false);
}

$task = JRequest::getVar('task', ''); 
$task = strtolower($task); 




if ($task === 'hikaopc') {
	
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'hikaopc.php'); 
  $opc = new OPCControllerHikaopc(); 
  $opc->opc(); 
  $app  = JFactory::getApplication(); 
  $app->close(); 
}
else 
if ($task === 'opcregister')
{
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
  $opc = new VirtueMartControllerOpc(); 
  $opc->opcregister(); 
  $app  = JFactory::getApplication(); 
  $app->close(); 
}
else
if ($task === 'opcthird')
{
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
  $opc = new VirtueMartControllerOpc(); 
  $opc->opcthird(); 
  $app  = JFactory::getApplication(); 
  $app->close(); 
}
else
if ($task === 'loadjs')
{
  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
  $file = JRequest::getVar('file', ''); 
  if (!empty($file))
  {
   OPCmini::loadJSfile($file); 
   $app  = JFactory::getApplication(); 
   $app->close(); 
   die(); 
  }
}
else 
if ($task === 'ping')
{
  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
  OPCtrackingHelper::ping(); 
   $app  = JFactory::getApplication(); 
   $app->close(); 
   die(); 
}
else
if ($task === 'pingstatus')
{
   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
   OPCtrackingHelper::pingstatus(); 
   $app  = JFactory::getApplication(); 
   $app->close(); 
   die(); 
}
else
if ($task === 'clearcart')	
{
	
	
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
  $opc = new VirtueMartControllerOpc(); 
  $opc->clearcart(); 
  $app  = JFactory::getApplication(); 
  $app->close(); 
	
}
//index.php?option=com_onepage&task=loadjs&file=onepage.js
$memstart = memory_get_usage(true); 
define('OPCMEMSTART', $memstart); 

include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 


{
if (!isset($opc_memory)) $opc_memory = '128M'; 
if (!empty($opc_memory))
{
 ini_set('memory_limit',$opc_memory);
}
ini_set('error_reporting', 0);
// disable error reporting for ajax:
error_reporting(0); 
}



if (!empty($opc_calc_cache))
		   {  
			 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
		     OPCcache::installCache(); 
		   }


// since 2.0.109 we need to load com_onepage instead of com_virtuemart becuase of captcha support 
JRequest::setVar('option', 'com_virtuemart'); 
if (!class_exists( 'VmConfig' )) 
{
	require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	VmConfig::loadConfig(); 
}
$task = JRequest::getVar('task', ''); 



if ($view == 'xmlexport')
{
	
	
	
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'xmlexport.php'); 



$VirtueMartControllerXmlexport = new VirtueMartControllerXmlexport(); 

if ($task === 'getproduct') {
	$VirtueMartControllerXmlexport->getproduct(); 
}
elseif ($VirtueMartControllerXmlexport->enabled) {
if ($task === 'getlist') 
{

	$VirtueMartControllerXmlexport->getlist(); 
} else {
	$VirtueMartControllerXmlexport->createXml(); 
}

}
}
else
if ($view=='orderexport')
{
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'orderexport.php'); 
$VirtueMartControllerOrderexport = new VirtueMartControllerOrderexport();
$VirtueMartControllerOrderexport->process(); 
$app = JFactory::getApplication()->close(); 
}
else
if ($view=='add_shopper')
{
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'add_shopper.php'); 
$VirtueMartControllerAdd_shopper = new VirtueMartControllerAdd_shopper();
$VirtueMartControllerAdd_shopper->execute($task); 

}
else
if ($view=='third_address')
{

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'third_address.php'); 
$VirtueMartControllerThird_address = new VirtueMartControllerThird_address();
$VirtueMartControllerThird_address->execute($task); 

}
else
if ($view=='registration')
{

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'registration.php'); 
$VirtueMartControllerRegistration = new VirtueMartControllerRegistration();
$VirtueMartControllerRegistration->execute($task); 

}
else
if ($view=='registrationst')
{

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'registrationst.php'); 
$VirtueMartControllerRegistrationst = new VirtueMartControllerRegistrationst();
$VirtueMartControllerRegistrationst->execute($task); 

}	
else
if ($view=='registration_complete')
{
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'registration_complete.php'); 
$VirtueMartControllerRegistration_complete = new VirtueMartControllerRegistration_complete();
$VirtueMartControllerRegistration_complete->execute('display'); 

}
else
if ($view == 'test') {

}
else
{
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'virtuemart.cart.view.html.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.php'); 
JRequest::setVar('option', 'com_onepage'); 
$task = JRequest::getVar('task', ''); 
}

}
else {
   	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'test.php'); 
	$VirtueMartControllerTest = new VirtueMartControllerTest();
	$VirtueMartControllerTest->execute('display'); 
	
}