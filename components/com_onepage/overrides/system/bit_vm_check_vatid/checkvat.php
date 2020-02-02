<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date 31.March 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*	  BIT VAT COMPATIBILITY FILE
*/
 
$task = JRequest::getVar('cmd', ''); 
// support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
$vatid = JRequest::getVar('vatid', JRequest::getVar('EUVatID', ''));
$country = (int)JRequest::getVar('country_id', JRequest::getVar('virtuemart_country_id', '')); 
$session = JFactory::getSession(); 
$forcevat = false; 
 
 
$return = ''; 
 
if (!empty($vatid))
{
		$db = JFactory::getDBO();
		$q = "SELECT `country_2_code` FROM #__virtuemart_countries WHERE `virtuemart_country_id` =". (int)$country.' limit 0,1';
		$db->setQuery($q);
		$country_2_code = $db->loadResult();


		
		
		
  $lastcheck = $session->get('vatlastcheck', ''); 
  if ($lastcheck != $country.$vatid)
   {
     $forcevat = true; 
   }
  $session->set('vatlastcheck', $country.$vatid); 
}
if (($task == 'checkbitvat') || ($forcevat))
{

if ($forcevat)
{
  
}
 
  @header('Content-Type: text/html; charset=utf-8');
  @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
 
if (!empty($vatid))
{
   
	$app = JFactory::getApplication('site');
	$plugin = JPluginHelper::getPlugin('system', 'bit_vm_check_vatid');
	$pluginParams = new JRegistry();
	$pluginParams->loadString($plugin->params);
	
	$vies_url = $pluginParams->get('vies_url','http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
	$vat_to_check = $vatid;
	$error_msg = $pluginParams->get('error_msg_invalid_id','Invalid VAT number');
	$vies_down = $pluginParams->get('vies_down',1);
	$vies_down_error_msg = $pluginParams->get('error_msg_vies_down','EU validation service is currently not available for your country. Please try again later.');
	$fname = $pluginParams->get('euvatid_field_name', 'EUVATnumber');
	if(version_compare(JVERSION,'2.5.0','ge')) {
// Joomla! 2.5 code here
		$plugin_short_path = 'plugins/system/bit_vm_check_vatid/bitvatidchecker/';
	} elseif(version_compare(JVERSION,'1.7.0','ge')) {
	// Joomla! 1.7 code here
		$plugin_short_path = 'plugins/system/bit_vm_check_vatid/bitvatidchecker/';
	}
	elseif(version_compare(JVERSION,'1.6.0','ge')) {
	// Joomla! 1.6 code here
		$plugin_short_path = 'plugins/system/bit_vm_check_vatid/bitvatidchecker/';
	} else {
	// Joomla! 1.5 code here
					$plugin_short_path = 'plugins/system/bitvatidchecker/';
	}
	
			
	require_once ( JPATH_SITE.DIRECTORY_SEPARATOR.$plugin_short_path.'classes/euvatcheck.class.php');
	
	
	$error_msg =  OPCLang::setGet('COM_ONEPAGE_VAT_CHECKER_INVALID', $error_msg ); 
	$vies_down_error_msg = OPCLang::setGet('COM_ONEPAGE_VAT_CHECKER_DOWN', $vies_down_error_msg); 
	
	
    $session = JFactory::getSession(); 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
	$vatids = $session->get($opc_vat_key, array());
	
	if (!is_array($vatids))
	$vatids = json_decode($vatids, true); 

	$vatids['field'] = $fname; 
	
	$vatid = strtoupper($vatid); 
	
	$vatid = preg_replace("/[^a-zA-Z0-9]/", "", $vatid);
	$country = JRequest::getVar('virtuemart_country_id'); 
	$vathash = $country.'_'.$vatid; 
	
	// vatid must start with country: 
	$invalid = false; 
	$str = substr($vatid, 0, 2); 
	$test = preg_replace("/[a-zA-Z]/", "", $str);
	if (!empty($test))
	{
	  $invalid = true; 
	}
	
	if (!$invalid)
	{
	if (!isset($vatids[$vathash]) || ($vatids[$vathash]==true))
	$vatcheck = new VmEUVatCheck($vat_to_check, $vies_url, $error_msg, $vies_down, $vies_down_error_msg);
	else
	{
	  $vatcheck = new stdClass(); 
	  $vatcheck->validvatid = '1'; 
	}
	}
	else
	{
	  $vatcheck = new stdClass(); 
	  $vatcheck->validvatid = $error_msg;
	}
	
	echo $vatcheck->validvatid;
	
	
	$isvalid = (string)$vatcheck->validvatid;
	if ($isvalid === '1') $isvalid = true; 
	else $isvalid = false; 
	
	
	// build hash for shopper groups: 
	
	
	if (!empty($isvalid))
	$vatids[$vathash] = (bool)$isvalid; 
	$s = json_encode($vatids); 
	
	$vatids = $session->set($opc_vat_key, $s);
	
	if ($task == 'checkbitvat')
	{
			$app->close(); 
			die(); 
	}
	
	if (!$isvalid)
	$return = $vatcheck->validvatid;
	
	}
else
{
if ($task == 'checkbitvat')
if (!empty($country))
{
    
	
	  
	
			$db = JFactory::getDBO();
			$q = "SELECT country_2_code FROM #__virtuemart_countries WHERE virtuemart_country_id =". (int)$country;
			$db->setQuery($q);
			$db->query();
			$country_2_code = $db->loadResult();
			
			{
			echo $country_2_code;
			$app = JFactory::getApplication('site');
			$app->close(); 
			die(); 
			}
						
}

}
}