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

/**
 *  Some portions of this file are also credited to: 
 *  @package AkeebaSubs
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */


if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 


if (!defined('VAT_STATUS_VALID'))
{
	define('VAT_STATUS_VALID', 1);
	define('VAT_STATUS_NOT_VALID', 0);
	define('VAT_STATUS_SOAP_ERROR', -1);
	define('VAT_STATUS_COUNTRY_ERROR', -3);
	define('VAT_STATUS_COUNTRY_NOT_IN_EU', -20);
	define('VAT_STATUS_COUNTRY_NOT_IN_EU2', -21);
	define('VAT_STATUS_COUNTRY_HOME', 2);
}

class OPCvat
{
  public static $european_states = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'HR', 'UK', 'EL', 'EU');
  
  
  public static function returnVatResp($msg, $valid, $orig_vat_id, $country, $full_resp, $newvatid)
  {
	  
	  if ($full_resp === true)
	  {
		  return OPCLang::_($msg); 
	  }
	  static $c; 
	  
	  
	  
	  if (empty($orig_vat_id)) return ''; 
	  
	  $zz = json_encode($full_resp); 
	  $cachekey = $msg.'_'.$valid.'_'.$newvatid.'_'.$country.'_'.$zz.'_'.$newvatid.'_'.$orig_vat_id; 
	  
	  // we do not need to save the values twice: 
	  if (isset($c[$zz]))
		  return $c[$zz];  
	  
	  OPCVatWorker::save($msg, $valid, $newvatid, $country, '', $full_resp); 

	  $ret = OPCLang::_($msg); 
	  if (is_string($full_resp))
		  $ret .= ' '.$full_resp; 
	  
	  $c[$zz] = $ret; 
	  
	  return $ret; 
	  
  }
  public static function setVatCache($fieldname, $country_id, $vat_ids, $result=VAT_STATUS_VALID)
  {
	  		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  
/*	  
if ($result == VAT_STATUS_VALID) { 
 $x = debug_backtrace(); 
 foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />"; 
 die(); 
}
*/
	  // will be used for shopper group handling
			$session = JFactory::getSession(); 
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
			$vatids = $session->get($opc_vat_key, array());
			if (!is_array($vatids))
			$vatids = json_decode($vatids, true); 
			if (!isset($vatids['field']))
			{
			  $vatids['field'] = $opc_vat_key;
			}
				else
				{
					$vatids['field'] = $fieldname; 
				}
				
				foreach ($vat_ids as $k=>$v)
				{
					$vatids[$country_id.'_'.$v] = $result; 
					$vatids[$v] = $result; 
					if (isset(OPCvat::$requestIdentifier[$v]))
					{
					 $vatids[$v.'_token'] = OPCvat::$requestIdentifier[$v]; 
					}
				}
				
		$s = json_encode($vatids); 
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		$vatids = $session->set($opc_vat_key, $s);
  }
  
  public static function alterRates(&$calculationHelper, &$rules)
  {
	   if (empty($rules)) return;
	   if (!class_exists('VirtuemartCart'))
			   {
				   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
			   }
	   
	    if (isset($calculationHelper->_cart))
				 {
					$cart =& $calculationHelper->_cart; 
				  }
				  else
				  {
			  
					$cart = VirtueMartCart::getCart();
				   }
		  
		  if (method_exists($cart, 'getST'))
		  {
			  $address = $cart->getST(); 
		  }
		  else
		  {
		    $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		  }
		  
		   $home_vat_countries = OPCconfig::get('home_vat_countries', ''); 
	   
	      $home = explode(',', $home_vat_countries); 
		   $list = array(); 
		   if (is_array($home))
		   {
		     foreach ($home as $k=>$v)
			  {
			    $list[] = strtoupper(trim($v)); 
			  }
			 
		   }
		  
		  
		  if (empty($address) && (!empty($list))) return; 
		  $country = $address['virtuemart_country_id']; 
		  
		  
		   
		   if (!empty($country))
		   {
			 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			 
		    $country_2_code = OPCmini::getCountryByID($country, 'country_2_code'); 
		   }
	   
	   
	  
		  
		   
		   if ((empty($list) && (empty($country))) || (!in_array($country_2_code, $list)) || (empty($list))) {
	   
	   
	   
	   $is_valid = OPCvat::detectVatCache(); 
	  
	   if ($is_valid) {
	  
	   foreach($rules as $k=>$v)
		{
			
			
			
			
				
				$types = array('DBTaxRulesBill'=>'DBTaxBill', 'taxRulesBill'=>'TaxBill', 'DATaxRulesBill'=>'DATaxBill', 'salesPriceDBT'=>'salesPriceDBT', 'VatTax'=>'VatTax', 'Marge'=>'Marge', 'Tax'=>'Tax', 'DBTax'=>'DBTax', 'DATax'=>'DATax'); 
				
		 
		  
		  $id = $v['virtuemart_calc_id']; 
		 
		      $kind = $rules[$k]['calc_kind'];
		  if (in_array($kind, $types)) { 
		  
			  $rules[$k]['calc_value'] = 0; 
			  $rules[$k]['calc_value_mathop'] = '+%'; 
			  
			  foreach ($types as $type=>$vz) {
			  
			  if (isset($cart->cartData[$type]))
			  {
				  foreach ($cart->cartData[$type] as $k=>$v)
				  {
					  if (is_array($cart->cartData[$type][$k]))
					  {
					   $cart->cartData[$type][$k]['calc_value'] = 0; 
					  }
					  if (is_object($cart->cartData[$type][$k]))
					  {
					   $cart->cartData[$type][$k]->calc_value = 0; 
					  }
				  }
			  }
			  }
			  //$cart->cartData['taxRulesBill']
			  
		  }
		  
		  
		  
				
				
			}
	   }
		   }
		   
		   
		   
		
  }
  
  
  public static function detectVatCache($country_id='', $euvat_input='', $legacy=true)
  {
	  		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  
	     $opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
	    $session = JFactory::getSession(); 
		$vatids = $session->get($opc_vat_key, array());
		
		
		
		if (!empty($vatids))
		{
		 if (!is_array($vatids))
		 $vatids = @json_decode($vatids, true); 
	    }
		
		if (empty($vatids)) return false; 
		
		if (empty($vatids['field']))
		{
			$vatids['field'] = $opc_vat_key; 
		}
		
		
		
		  if (!isset($cart))
		   {
			   if (!class_exists('VirtuemartCart'))
			   {
				   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
			   }
			   $cart = VirtuemartCart::getCart(); 
			   
		   }
		   
		   if (empty($euvat_input))
		   $euvat = JRequest::getVar($vatids['field'], ''); 
		   else $euvat = $euvat_input; 
		   
		   
		   if (empty($euvat))
		   if ((!empty($cart->BT)) && (!empty($cart->BT[$vatids['field']])))
		   {
			   $euvat_cart = $cart->BT[$vatids['field']]; 
			   $euvat = $euvat_cart; 
		   }
		   
		   if (!empty($euvat))
		   {
		   $euvat = preg_replace("/[^a-zA-Z0-9]/", "", $euvat);
		   $euvat = strtoupper($euvat); 
		   
		   if (empty($country_id))
		   {
			   $country = JRequest::getVar('virtuemart_country_id'); 
		   if (empty($country))
		   if (!empty($cart))
		   {
		   $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		   if (!empty($address))
		   $country = $address['virtuemart_country_id']; 
		   }
		  
		   }
		   else
		   {
			   $country = $country_id; 
		   }
		   
		   
		   $vathash = $country.'_'.$euvat; 
		   
		   foreach ($vatids as $k=>$v)
		   {
			   if (stripos($k, '_token')!==false) {
			      $vx = (array)$v; 
				  $kx = str_replace('_token', '', $k); 
				  OPCvat::$requestIdentifier[$kx] = $vx;
			   }
		   }
		   
		   
		   if (!empty($legacy)) { 
		    if (!empty($vatids[$vathash]) && (($vatids[$vathash]===true) || ($vatids[$vathash]===1)))
			{
				
				return true;
			}
		   }
		   else
		   {
			
		   if (isset($vatids[$vathash]))
			{
				$result = (int)$vatids[$vathash]; 
				/*
				
				if ($result == VAT_STATUS_VALID) { 
				
 $x = debug_backtrace(); 
 foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />"; 
 die(); 
}
*/
				return $result; 
			}
		   }
		   }
		  
		   return false; 
		   
  }
  
  public static function addOrderId($vat_id, $orderId, $paymentData='')
  {
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  
	   if (!OPCmini::tableExists('onepage_moss'))
	   {
		   OPCVatWorker::createTable(); 
	   }
	  
	  $db = JFactory::getDBO(); 
	  
	  $user_id = JFactory::getUser()->get('id'); 
	  // last 24 hours: 
	  $time = time() - (24 * 60 * 60); 
	  
	  $h = OPCvat::checkHistory($vat_id, true, $time); 
	  
	  if ((!empty($vat_id)) && (!empty($h)) && (empty($h['order_id']))) { 
	   $q = 'update `#__onepage_moss` set `order_id` = '.(int)$orderId.', `user_id` = '.(int)$user_id; 
	   
	   if (!empty($paymentData))
	   $q .= ", `payment_data` = '".$db->escape($paymentData)."' "; 
	   
	   $q .= " where `eu_vat_id` = '".$db->escape($vat_id)."' and `timestamp` >= ".(int)$time.' and `order_id` = 0'; 
	   $db->setQuery($q); 
	   $db->query(); 
	   
	   
	  
	  }
	  else
	  {
		  
		   $geoip = JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php'; 
	  
      
	  if (file_exists($geoip))
	  {
	  include_once($geoip);
	  $ip = ''; 
	  OPCVatWorker::getIP($ip); 
	  $ip2c = geoHelper::getCountry2Code(); 
	  
	  		$arr = array('A1', 'A2', 'O1'); 
			if (in_array($ip2c, $arr)) $ip2c = '';  

	  
	  }
	   
	  if (empty($ip2c)) 
	  {
		   $ip = $ip2c = ''; 
		   OPCVatWorker::getIP($ip); 
	  }
		  
		  $q = "insert into `#__onepage_moss` (`id`, `order_id`, `payment_data`, `ip`, `ipc2`, `vm_country`, `payment_country`, `eu_vat_id`) values (NULL, ".(int)$orderId.", '".$db->escape($paymentData)."', '".$db->escape($ip)."', '".$db->escape($ip2c)."', '', '', '".$db->escape($vat_id)."')"; 
		  $db->setQuery($q); 
		  $db->query(); 
		  
		  
	  }
	  
	  
	  
  }
  
  public static function getHistory($vat_id, $orderId)
  {
	  
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  
	   if (!OPCmini::tableExists('onepage_moss'))
	   {
		   OPCVatWorker::createTable(); 
	   }
	  
	   $db = JFactory::getDBO(); 
	   $q = 'select * from `#__onepage_moss` where `order_id` = '.(int)$orderId.' order by `timestamp` asc'; 
	   $db->setQuery($q); 
	   $res = $db->loadAssocList();
	   
	   
	    $e = $db->getErrorMsg(); if (!empty($e)) { 
		  //JFactory::getApplication()->enqueueMessage($e, 'error'); 
		  return array();  
		  }
	   if (empty($res))
	   {
		   $ret = OPCvat::checkHistory($vat_id, true); 
		   
		   return array( $ret ); 
	   }
	   return $res; 
  }
  
  public static function checkHistory($vat_id, $returnfull=false, $time=0, $noorder=false)
  {
	  	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  
	   if (!OPCmini::tableExists('onepage_moss'))
	   {
		   OPCVatWorker::createTable(); 
	   }
	    $tq = ''; 
	    if (!empty($time)) {
		  $tq .= " and `timestamp` >= ".(int)$time." "; 
		}
	  
	  if (!empty($noorder)) {
	    $tq .= ' and order_id = 0 '; 
	  }
	  
	   {
	  
	   $db = JFactory::getDBO(); 
	   if (!$returnfull)
	   {
	    $q = "select `vat_response_id`, `vat_error`, `vat_data` from `#__onepage_moss` where `eu_vat_id` = '".$db->escape($vat_id)."' and (`vat_response_id` NOT LIKE '".VAT_STATUS_SOAP_ERROR."') ".$tq." order by `timestamp` desc limit 1"; ; 
	   }
	   else
	   {
		   $q = "select * from `#__onepage_moss` where (`eu_vat_id` = '".$db->escape($vat_id)."' or `eu_vat_id` like '".$db->escape($vat_id)."') and (`vat_response_id` NOT LIKE '".VAT_STATUS_SOAP_ERROR."') ".$tq." order by `timestamp` desc limit 1"; ; 
	   }
	   
	   
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   
	   if (empty($res)) {
	      if (!$returnfull)
	   {
	    $q = "select `vat_response_id`, `vat_error`, `vat_data` from `#__onepage_moss` where `eu_vat_id` = '".$db->escape($vat_id)."' ".$tq." order by `timestamp` desc limit 1"; ; 
	   }
	   else
	   {
		   $q = "select * from `#__onepage_moss` where (`eu_vat_id` = '".$db->escape($vat_id)."' or `eu_vat_id` like '".$db->escape($vat_id)."') ".$tq." order by `timestamp` desc limit 1"; ; 
	   }
	   
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   }
	   
	    
	   
	   $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
	   
	   if ($returnfull) return $res; 
	   
	   if (!empty($res))
	   {
		    $len = strlen($res['vat_response_id']); 
		    if (($res['vat_error'] == 'COM_ONEPAGE_VALIDATE_VAT_VALID') && ($len > 14))
			{
			
			$vat_data = @json_decode($res['vat_data'], true); 
			
			if (!empty($vat_data))
			{
			OPCvat::$requestIdentifier[$vat_id] = $vat_data; 
			
			
			return VAT_STATUS_VALID; 
			}
			}
			
			
			return (int)$res['vat_response_id']; 
	   }
	   }
	   return VAT_STATUS_NOT_VALID; 
	   
  }
  
  public static function formatVat(&$country, &$vat, $country_id=0)
  {
	  
  }	  
  /**
	 * We cache the results of all time-consuming operations, e.g. vat validation, subscription membership calculation,
	 * tax calculations, etc into this array, saved in the user's session.
	 * @var array
	 */
	public static $_cache; 
	public static $requestIdentifier; 
	
	public static function getCountry($country=0)
	{
		if (empty($country))
		$country = JRequest::getVar('virtuemart_country_id', 0); 
	
		  $country = (int)$country; 
		  $db = JFactory::getDBO();
			
			$q = "SELECT country_2_code FROM #__virtuemart_countries WHERE virtuemart_country_id =". (int)$country;
			$db->setQuery($q);
			$db->query();
			$country_2_code = $db->loadResult();
			if (!empty($country_2_code))
			{
				  $country_2_code = strtoupper($country_2_code); 
				  
				  if ($country_2_code === 'GR') $country_2_code = 'EL'; 
				  if ($country_2_code === 'UK') $country_2_code = 'GB'; 
				  /*
				  $country_2_code = $country_2_code == 'EL' ? 'GR' : $country_2_code;
				  $country_2_code = $country_2_code == 'UK' ? 'GB' : $country_2_code;
				  */
				  return strtoupper($country_2_code); 
			}
			
			return ''; 
			
	}
	
  	public static function isVIESValidVAT(&$country, $vat, $company='', &$err, $requester='')
	{
	   $key = $mk = $country.$vat;
	
	   if (!isset(OPCvat::$_cache)) OPCvat::$_cache = array(); 
	   if (!isset(OPCvat::$requestIdentifier)) OPCvat::$requestIdentifier = array(); 
	
		// Validate VAT number
		$vat = trim(strtoupper($vat));
		$country = $country == 'GR' ? 'EL' : $country;
		$country = $country == 'UK' ? 'GB' : $country;
		
		// (remove the country prefix if present)
		
		$starts = substr($vat, 0,2); 
		
		if ($starts == 'GR') $vat = trim(substr($vat,2));
		else
		if ($starts == 'UK') $vat = trim(substr($vat,2));
		else
		if(substr($vat,0,2) == $country) $vat = trim(substr($vat,2));
		else
		{
			
			if (in_array($starts, OPCVat::$european_states))
			{
				$vat = trim(substr($vat,2));
			}
		}
		$vat = preg_replace("/[^A-Z0-9]/", "", $vat);
		
		if (!empty($requester))
		{
		 $requester = preg_replace("/[^A-Z0-9]/", "", $requester);
		 $rc = substr($requester, 0, 2); 
		 $rc = strtoupper($rc); 
		 
		 if (in_array($rc, OPCvat::$european_states)) 
		  {
		    $rn = substr($requester, 2); 
			 if ($requester) {
			    $extra = array(); 
				$extra['requesterCountryCode'] = $rc;
				$extra['requesterVatNumber'] = $rn;
			 }
		  }
		}
		
		$vat = preg_replace('/[^A-Z0-9]/', '', $vat); // Remove spaces, dots and stuff
		// Is the validation already cached?
		
		$ret = null;
		
		if (is_null(OPCvat::$_cache)) OPCvat::$_cache = array(); 
		if(array_key_exists('vat', OPCvat::$_cache)) {
			if(array_key_exists($key, OPCvat::$_cache['vat'])) {
				$ret = OPCvat::$_cache['vat'][$key];
			}
		}
		$country = strtoupper($country); 
		if (!in_array($country, OPCvat::$european_states)) 
		{
		 $ret = VAT_STATUS_COUNTRY_NOT_IN_EU; 
		 //$vat = ''; 
		}
		if(!is_null($ret)) return $ret;

		
		
		
		
		if(empty($vat)) {
			$ret = VAT_STATUS_NOT_VALID;
		} else {
			if(!class_exists('SoapClient')) {
				$ret = VAT_STATUS_SOAP_ERROR;
			} else {
				// Using the SOAP API
				// Code credits: Angel Melguiz / KMELWEBDESIGN SLNE (www.kmelwebdesign.com)
				try {
					if (function_exists('ini_set')) {
					  if (is_writable(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'))
					  $scache = @ini_set('soap.wsdl_cache_dir', JPATH_ROOT.DIRECTORY_SEPARATOR.'cache');
					}
					$sOptions = array(
						'user_agent'		=> 'PHP'
					);
					
					if (!empty($scache)) {
						$sOptions['cache_wsdl'] = WSDL_CACHE_BOTH; 
						
					}
					$sOptions['connection_timeout'] = 30; 
					$sClient = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', $sOptions);
					$params = array('countryCode'=>$country,'vatNumber'=>$vat);
				    
					if (isset($extra)) 
					{
					$params = array_merge($params, $extra); 
					
					//stAn: original - 
					//$response = $sClient->checkVat($params);
					
					$response = $sClient->checkVatApprox($params);
					}
					else
					{
					  $response = $sClient->checkVat($params);
					}
					
					$ret = false;
						
					
					if (is_object($response) && ($response->valid)) {
						$ret = true;
						
						OPCvat::$requestIdentifier[$mk] = array(); 
						
						if (!empty($response->requestIdentifier))
						OPCvat::$requestIdentifier[$mk]['requestIdentifier'] = $response->requestIdentifier; 
						if (isset($response->traderName))
						OPCvat::$requestIdentifier[$mk]['company'] = $response->traderName; 
						if (isset($response->traderAddress))
						OPCvat::$requestIdentifier[$mk]['address_1'] = $response->traderAddress; 
						if (isset($response->traderPostcode))
						OPCvat::$requestIdentifier[$mk]['zip'] = $response->traderPostcode; 
						if (isset($response->traderCity))
						OPCvat::$requestIdentifier[$mk]['city'] = $response->traderCity; 
						if (isset($response->countryCode))
						OPCvat::$requestIdentifier[$mk]['country_code'] = $response->countryCode; 
					
						OPCvat::$requestIdentifier[$mk]['time'] = time(); 
						
						
					} else {
						
						$ret = VAT_STATUS_NOT_VALID;
					}
					
				} catch(SoapFault $ex) {
					if (method_exists($ex, 'getMessage')) { 
					 $err = $ex->getMessage(); 
					}
					else
					{
				     $err = $ex->faultcode.' '.$ex->faultstring.' '.$ex->faultactor.' '.$ex->detail.' '.$ex->_name.' '.$ex->headerfault; 
					}
					
					$ret = VAT_STATUS_NOT_VALID;
					
					if (OPCvat::checkHistory($mk) === VAT_STATUS_VALID)
					{
						if (!empty(OPCvat::$requestIdentifier[$mk]))
							return OPCvat::$requestIdentifier[$mk]; 
					}
					
					return VAT_STATUS_SOAP_ERROR; 
				}
			}
		}

		// Cache the result
		if (is_null(OPCvat::$_cache)) OPCvat::$_cache = array(); 
		
		if(!array_key_exists('vat', OPCvat::$_cache)) {
			OPCvat::$_cache['vat'] = array();
		}
		OPCvat::$_cache['vat'][$key] = $ret;
		$encodedCacheData = json_encode(OPCvat::$_cache);

		//$session = JFactory::getSession();
		//$session->set('validation_cache_data', $encodedCacheData, 'com_onepage');

		// Return the result
		if ($ret == VAT_STATUS_VALID)
		  {
		    return OPCvat::$requestIdentifier[$mk]; 
		  }
		return $ret;
	}
	/**
	 * Sanitizes the VAT number and checks if it's valid for a specific country.
	 * Ref: http://ec.europa.eu/taxation_customs/vies/faq.html#item_8
	 *
	 * @param string $country Country code
	 * @param string $vatnumber VAT number to check
	 *
	 * @return array The VAT number and the validity check
	 */
	private function _checkVATFormat($country, $vatnumber)
	{
		$ret = (object)array(
			'prefix'		=> $country,
			'vatnumber'		=> $vatnumber,
			'valid'			=> true
		);

		$vatnumber = strtoupper($vatnumber); // All uppercase
		$vatnumber = preg_replace('/[^A-Z0-9]/', '', $vatnumber); // Remove spaces, dots and stuff
		$vat_country_prefix = $country; // Remove the country prefix, if it exists
		if($vat_country_prefix == 'GR') $vat_country_prefix = 'EL';
		if(substr($vatnumber, 0, strlen($vat_country_prefix)) == $vat_country_prefix) {
			$vatnumber = substr($vatnumber, 2);
		}
		$ret->prefix = $vat_country_prefix;
		$ret->vatnumber = $vatnumber;

		switch ($ret->prefix) {
			case 'AT':
				// AUSTRIA
				// VAT number is called: MWST.
				// Format: U + 8 numbers

				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					if(substr($vatnumber,0,1) != 'U') $ret->valid = false;
				}
				if($ret->valid) {
					$rest = substr($vatnumber, 1);
					if(preg_replace('/[0-9]/', '', $rest) != '') $ret->valid = false;
				}
				break;

			case 'BG':
				// BULGARIA
				// Format: 9 or 10 digits
				if((strlen($vatnumber) != 10) && (strlen($vatnumber) != 9)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'CY':
				// CYPRUS
				// Format: 8 digits and a trailing letter
				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					$check = substr($vatnumber, -1);
					if(preg_replace('/[0-9]/', '', $check) == '') $ret->valid = false;
				}
				if($ret->valid) {
					$check = substr($vatnumber, 0, -1);
					if(preg_replace('/[0-9]/', '', $check) != '') $ret->valid = false;
				}
				break;

			case 'CZ':
				// CZECH REPUBLIC
				// Format: 8, 9 or 10 digits
				$len = strlen($vatnumber);
				if(!in_array($len, array(8,9,10))) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'BE':
				// BELGIUM
				// VAT number is called: BYW.
				// Format: 9 digits
				if((strlen($vatnumber) == 10) && (substr($vatnumber,0,1) == '0')) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
					break;
				}
			case 'DE':
				// GERMANY
				// VAT number is called: MWST.
				// Format: 9 digits
			case 'GR':
			case 'EL':
				// GREECE
				// VAT number is called: ???.
				// Format: 9 digits
			case 'PT':
				// PORTUGAL
				// VAT number is called: IVA.
				// Format: 9 digits
			case 'EE':
				// ESTONIA
				// Format: 9 digits
				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'DK':
				// DENMARK
				// VAT number is called: MOMS.
				// Format: 8 digits
			case 'FI':
				// FINLAND
				// VAT number is called: ALV.
				// Format: 8 digits
			case 'LU':
				// LUXEMBURG
				// VAT number is called: TVA.
				// Format: 8 digits
			case 'HU':
				// HUNGARY
				// Format: 8 digits
			case 'MT':
				// MALTA
				// Format: 8 digits
			case 'SI':
				// SLOVENIA
				// Format: 8 digits
				if(strlen($vatnumber) != 8) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'FR':
				// FRANCE
				// VAT number is called: TVA.
				// Format: 11 digits; or 10 digits and a letter; or 9 digits and two letters
				// Eg: 12345678901 or X2345678901 or 1X345678901 or XX345678901
				if(strlen($vatnumber) != 11) $ret->valid = false;
				if($ret->valid) {
					// Letters O and I are forbidden
					if(strstr($vatnumber, 'O')) $ret->valid = false;
					if(strstr($vatnumber, 'I')) $ret->valid = false;
				}
				if($ret->valid) {
					$valid = false;
					// Case I: no letters
					if(preg_replace('/[0-9]/', '', $vatnumber) == '') $valid = true;

					// Case II: first character is letter, rest is numbers
					if(!$valid) {
						if(preg_replace('/[0-9]/', '', substr($vatnumber,1)) == '') $valid = true;
					}

					// Case III: second character is letter, rest is numbers
					if(!$valid) {
						$check = substr($vatnumber,0,1) . substr($vatnumber,2);
						if(preg_replace('/[0-9]/', '', $check) == '') $valid = true;
					}

					// Case IV: first two characters are letters, rest is numbers
					if(!$valid) {
						$check = substr($vatnumber,2);
						if(preg_replace('/[0-9]/', '', $check) == '') $valid = true;
					}

					$ret->valid = $valid;
				}
				break;

			case 'IE':
				// IRELAND
				// VAT number is called: VAT.
				// Format: seven digits and a letter; or six digits and two letters
				// Eg: 1234567X or 1X34567X
				if(strlen($vatnumber) != 8) $ret->valid = false;
				if($ret->valid) {
					// The last position must be a letter
					$check = substr($vatnumber,-1);
					if(preg_replace('/[0-9]/', '', $check) == '') $ret->valid = false;
				}
				if($ret->valid) {
					// Skip the second position (it's a number or letter, who cares), check the rest
					$check = substr($vatnumber,0,1) . substr($vatnumber,2,-1);
					if(preg_replace('/[0-9]/', '', $check) != '') $ret->valid = false;
				}
				break;

			case 'IT':
				// ITALY
				// VAT number is called: IVA.
				// Format: 11 digits
				if(strlen($vatnumber) != 11) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'LT':
				// LITUANIA
				// Format: 9 or 12 digits
				if((strlen($vatnumber) != 9) && (strlen($vatnumber) != 12)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'LV':
				// LATVIA
				// Format: 11 digits
				if((strlen($vatnumber) != 11)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'PL':
				// POLAND
				// Format: 10 digits
			case 'SK':
				// SLOVAKIA
				// Format: 10 digits
				if((strlen($vatnumber) != 10)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'RO':
				// ROMANIA
				// Format: 2 to 10 digits
				$len = strlen($vatnumber);
				if(($len < 2) || ($len > 10)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'NL':
				// NETHERLANDS
				// VAT number is called: BTW.
				// Format: 12 characters long, first 9 characters are numbers, last three characters are B01 to B99
				if(strlen($vatnumber) != 12) $ret->valid = false;
				if($ret->valid) {
					if((substr($vatnumber,9,1) != 'B')) {
						$ret->valid = false;
					}
				}
				if($ret->valid) {
					$check = substr($vatnumber,0,9) . substr($vatnumber,11);
					if(preg_replace('/[0-9]/', '', $check) == '') $valid = true;
				}
				break;

			case 'ES':
				// SPAIN
				// VAT number is called: IVA.
				// Format: Eight digits and one letter; or seven digits and two letters
				// E.g.: X12345678 or 12345678X or X1234567X
				if(strlen($vatnumber) != 9) $ret->valid = false;
				if($ret->valid) {
					// If first is number last must be letter
					$check = substr($vatnumber,0,1);
					if(preg_replace('/[0-9]/', '', $check) == '') {
						$check = substr($vatnumber,0);
						if(preg_replace('/[0-9]/', '', $check) == '') $ret->valid = false;
					}
				}
				if($ret->valid) {
					// If first is not a number, the  last can be anything; just check the middle
					$check = substr($vatnumber,1,-1);
					if(preg_replace('/[0-9]/', '', $check) != '') $ret->valid = false;
				}
				break;

			case 'SE':
				// SWEDEN
				// VAT number is called: MOMS.
				// Format: Twelve digits, last two must be 01
				if(strlen($vatnumber) != 12) $ret->valid = false;
				if($ret->valid) {
					if(substr($vatnumber,-2) != '01') $ret->valid = false;
				}
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			case 'GB':
				// UNITED KINGDOM
				// VAT number is called: VAT.
				// Format: Nine or twelve digits; or 5 characters (alphanumeric)
				if(strlen($vatnumber) == 5) {
					break;
				}
				if((strlen($vatnumber) != 9) && (strlen($vatnumber) != 12)) $ret->valid = false;
				if($ret->valid) {
					if(preg_replace('/[0-9]/', '', $vatnumber) != '') $ret->valid = false;
				}
				break;

			default:
				$ret->valid = false;
				break;
		}

		return $ret;
	}

  	public static function plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id, &$order)
	{
		
		$arr = array('com_virtuemart', 'com_onepage'); 
		$option = JRequest::getVar('option', ''); 
		if (!in_array($option, $arr)) return; 
		
		 $msg = ''; 
		  $opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		  if (!empty($opc_vat_key)) {
		    $res = OPCVat::getHistory($order['details']['BT']->$opc_vat_key, $order['details']['BT']->virtuemart_order_id); 
		  }
		 
		 
		 
				  if (!empty($res))
				  {
					  $vat_data = array(); 
					  
					  
					  
					  foreach ($res as $row)
					  {
				  
				  
					   $ident = $row['vat_response_id'];
					   
					   
					   $msg = str_replace('COM_ONEPAGE_VALIDATE_VAT_VALID', JText::_('COM_ONEPAGE_VALIDATE_VAT_VALID'), $row['vat_error']); 
					   
					   $msg = str_replace('COM_ONEPAGE_VALIDATE_VAT_VALID', JText::_('COM_ONEPAGE_VALIDATE_VAT_VALID'), $row['vat_error']); 
					   
					   $msg = str_replace('COM_ONEPAGE_VAT_CHECKER_DOWN', JText::_('COM_ONEPAGE_VAT_CHECKER_DOWN'), $row['vat_error']); 
					    
						$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat');
						
						if (!empty($opc_vat_key))
						{
					     $vat_num = $order['details']['BT']->$opc_vat_key; 
						}
					   $msg = JText::_($msg); 
					   
					   $obj = new stdClass(); 
					   
					   $obj->vat_id = $row['eu_vat_id']; 
					   $obj->ip = $row['ip']; 
					   
					   if (empty($last_ip))
					   $last_ip = $obj->ip; 
					   
					   $obj->vat_response_id = $ident; 
					   $obj->msg = $msg;
					   $obj->address = ''; 					   
					   $obj->timestamp = $row['timestamp']; 
					   
					   $resp_data = @json_decode($row['vat_data'], true); 
					   if (!empty($resp_data))
					   {
					   if (!empty($resp_data['company']))
					   $obj->address = $resp_data['company']; 
					   
					   if (!empty($resp_data['address_1']))
					   {
						   if (!empty($obj->address)) $obj->address .= ', '; 
					   $obj->address .= $resp_data['address_1']; 
					   }
					   if (!empty($resp_data['country_code']))
					   {
						   if (!empty($obj->address)) $obj->address .= ', '; 
					   $obj->address .= $resp_data['country_code']; 
					   }
					   }
					   
					   /*
					   $resp_data = @json_decode($row['payment_data'], true); 
					    if (!empty($resp_data))
					   {
					   if (!empty($resp_data['company']))
					   $obj->payment_address = $resp_data['company']; 
					   
					   if (!empty($resp_data['address_1']))
						   if (!empty($obj->payment_address)) $obj->payment_address .= ', '; 
					   $obj->payment_address .= $resp_data['address_1']; 
					   
					   if (!empty($resp_data['country_code']))
						   if (!empty($obj->payment_address)) $obj->payment_address .= ', '; 
					   $obj->payment_address .= $resp_data['country_code']; 
					   }
					   */
					   
					   
					   $vat_data[] = $obj; 
				 
					   
				  }
				  }
			
		
		
		$ip_name = ''; 
		
		if (!empty($order['details']['BT']->ip_address))
		{
			 $ip = $order['details']['BT']->ip_address; 
			 
			 if (!empty($last_ip))
			 if (stripos($ip, 'xx')!==false)
		     {
					 $ip = $last_ip; 
			 }				 
			 
			 
			 if (!empty($ip))
			 {
				  $geo = JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php'; 
		   if (file_exists($geo))
		   {
		     include_once($geo);
		     if (class_exists('geoHelper')) 
		     $ip_name = geoHelper::getCountry($ip);
			
			//$arr = array('A1', 'A2', 'O1'); 
			//if (in_array($ip_name, $arr)) $ip_name = '';  
	   
	   
		   }
			 }
			 
			 
			 
			 
		}
		
		
		
		if (empty($ip_name) && (empty($vat_data))) return ''; 
		
		
		ob_start(); 
		?>
		<fieldset><legend><?php echo JText::_('COM_ONEPAGE_ORDERDETAILS_LEGEND'); ?></legend>
		 <table class="table adminlist">
		   <?php if (!empty($ip_name)) { ?>
		   <tr><td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_IPADDRESS'); ?></td>
		   <td><?php echo $ip.' ('.$ip_name.')'; 
		     ?>
			 </td>
			</tr>
		   <?php } 
		   $cd = 0; 
		   ?>
			<?php 
			if (!empty($vat_data))
			foreach ($vat_data as $obj) { 
			if ((empty($obj->vat_response_id)) && (empty($obj->msg))) {
			  continue; 
			}
			$cd++; 
			?>
			<tr><td class="key"><?php echo JText::_('COM_ONEPAGE_EUVAT_FIELD'); ?></td>
			<td><?php echo $vat_num; ?></td>
			</tr>
			
			<tr>
			<td class="key"><?php echo JText::_('COM_ONEPAGE_ORDERDETAILS_VAT_STATUS'); ?></td>
			<td><?php echo $obj->msg; ?></td>
			</tr>
			
			<tr>
			<td class="key"><?php echo JText::_('COM_ONEPAGE_ORDERDETAILS_VAT_TOKEN'); ?></td>
			<td><?php echo $obj->vat_response_id; ?></td>
			</tr>

			<?php if (!empty($obj->address)) { ?>
			<tr>
			<td class="key"><?php echo JText::_('COM_ONEPAGE_ORDERDETAILS_VAT_ADDRESS'); ?></td>
			<td><?php echo $obj->address; ?></td>
			</tr>
            <?php } ?>
			
		   
			<tr>
			<td class="key"><?php echo JText::_('COM_ONEPAGE_ORDERDETAILS_VAT_LASTVALIDATIONTIME'); ?></td>
			<td><?php 
			if (class_exists('JDate'))
			{
				
			$date = new JDate($obj->timestamp); 
			if (method_exists($date, 'toRFC822'))
			echo $date->toRFC822(); 
			}
			else echo date('c', $obj->timestamp); 
			
			?></td>
			</tr>
           
			
			
			
			<?php } ?>
		 </table>
		</fieldset>
		<?php 
		$details = ob_get_clean(); 
		if (empty($cd)) return ''; 
		return $details; 
	}

}



class OPCVatWorker
{
	
   public static function createTable()
   {
	    $db = JFactory::getDBO(); 
		$q = "

CREATE TABLE IF NOT EXISTS `#__onepage_moss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eu_vat_id` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `ipc2` varchar(2) COLLATE utf8_general_ci NOT NULL,
  `vm_country` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `payment_country` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `vat_response_id` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `vat_error` varchar(500) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `user_id`  int(11) NOT NULL DEFAULT '0',
  `vat_data` text COLLATE utf8_general_ci NOT NULL,
  `payment_data` text COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `eu_vat_id` (`eu_vat_id`),
  KEY `eu_vat_id_2` (`eu_vat_id`,`timestamp`),
  KEY `order_id` (`order_id`),
  KEY `timestamp` (`timestamp`),
  KEY `vat_response_id` (`vat_response_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;"; 
	$db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); 
	if (!empty($e)) die($e); 
	
   }
   
   public static function save($msg, $valid, $newvatid, $vm_country, $payment_country,  $full_resp)
   {
	   
      if (empty($newvatid)) return; 


	  
	 if (is_array($full_resp))
	  {
		   if (isset($full_resp['requestIdentifier'])) {
		    $vat_response_id = $full_resp['requestIdentifier']; 
		   }
		   else
		   if (isset($full_resp['time'])) {
			   $vat_response_id = $full_resp['time']; 
		   }
		   else {
			   $vat_response_id = $valid;
		   }
	  }
	  else $vat_response_id = $valid;

	 
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  if (!OPCmini::tableExists('onepage_moss'))
	  {
		   OPCVatWorker::createTable(); 
	  }
	  
	  $geoip = JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php'; 
	  
      
	  if (file_exists($geoip))
	  {
	  include_once($geoip);
	  $ip = ''; 
	  OPCVatWorker::getIP($ip); 
	  $ip2c = geoHelper::getCountry2Code(); 
	  
	  		$arr = array('A1', 'A2', 'O1'); 
			if (in_array($ip2c, $arr)) $ip2c = '';  

	  
	  }
	   
	  if (empty($ip2c)) 
	  {
		   $ip = $ip2c = ''; 
		   OPCVatWorker::getIP($ip); 
	  }
	  
	 
	  
	  $fullr = json_encode($full_resp); 
	  
	  
	  $db = JFactory::getDBO(); 
	  
	  $hist = OPCVat::checkHistory($newvatid, true, 0, true); 
	  if (!empty($hist)) { 
	  if (empty($hist['order_id'])) { 
	  if (empty($full_resp)) {
		  
		  return; 
	  }
	  if ($fullr === $hist['vat_data']) {
		  
		  return; 
	  }
	  if ($vat_response_id === $hist['vat_response_id']) {
		  
		  return; 
	  }
	  }
	  
	  
	 
	  
	   $id = $hist['id']; 
	  
	   
	   $vti = (int)$vat_response_id; 
	   if ((strlen($hist['vat_response_id'])>3) && ($vat_response_id===1)) 
		   $vat_response_id = $hist['vat_response_id']; 
	   
	   //let's recycle the data: 
	   $q = 'update `#__onepage_moss` '; 
	   $q .= ' set `eu_vat_id` = "'.$db->escape($newvatid).'", `timestamp` = '.time().', `ip` = "'.$ip.'", `ipc2` =  "'.$db->escape($ip2c).'", `vat_response_id` = "'.$db->escape($vat_response_id).'", `vm_country` = "'.$db->escape($vm_country).'", `payment_country` = "'.$db->escape($payment_country).'",	`vat_error` = "'.$db->escape($msg).'", 	`vat_data` = "'.$db->escape($fullr).'" where id = '.$id; 
	    $db->setQuery($q); 
		$db->query(); 
		
		
	
		
		return; 
	  }
	  
	  
      $q = 'insert into `#__onepage_moss` '; 
	  $q .= ' (`id`, `eu_vat_id`, `timestamp`, `ip`, `ipc2`, `vat_response_id`, `vm_country`, `payment_country`,	`vat_error`, 	`order_id`, `vat_data`) values '; 
	  $q .= ' (NULL, "'.$db->escape($newvatid).'", '.time().', "'.$ip.'", "'.$db->escape($ip2c).'", "'.$db->escape($vat_response_id).'", "'.$db->escape($vm_country).'", "'.$db->escape($payment_country).'", "'.$db->escape($msg).'", 0, "'.$db->escape($fullr).'" ) ';  
	
	  try
	  {
	  $db->setQuery($q); 
	  $db->query();
	  }
	  catch(Exception $e)
      {
		   $e = (string)$e; 
		   if (class_exists('OPCloader'))
		   {
	         OPCloader::opcDebug($e, 'vat_validation'); 
		   }
	  }
	  
	  $time = time() - 60 * 24 * 24 * 30;
	  // order_id 1 is here to fix an old bug: 
	  $q = 'delete from `#__onepage_moss` where `timestamp` < '.$time.' and (`order_id` = 0 or `order_id` = 1) limit 9999999'; 
	  $db->setQuery($q); 
	  $db->query(); 
	  

	return; 
	  
   }

     public static function getIP(&$ip)
  {
     if (!empty($ip)) return $ip; 
	 
	if (empty($ip))
    {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	
	
	if (stripos($ip, ',')!==false)
	 {
	    $a = explode(',', $ip); 
		$ip = trim($a[0]); 
	 }
	
	}
    else
	{
    if (empty($ip) && (!empty($_SERVER['REMOTE_ADDR']))) 
	{
     $ip = $_SERVER['REMOTE_ADDR'];
	}
	}
    }
	
	
	 
  }
   
   public static function ret($msg, $valid, $newvatid='', $id='', $full_resp=array())
   {
       @header('Content-Type: text/html; charset=utf-8');
	   @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	   @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
      OPCVatWorker::save($msg, $valid, $newvatid, $id, $full_resp); 
      
      $lang = JFactory::getLanguage();
	  $extension = 'com_onepage';
	  $base_dir = JPATH_SITE;
	  $language_tag = 'en-GB';
	  $lang->load($extension, $base_dir, $language_tag, $reload);
	  $lang->load($extension, $base_dir);
	  
	  
	   
      $ret = array(); 
	  $ret['msg'] = JText::_($msg); 
	  $ret['valid'] = $valid; 
	  $ret['newvatid'] = $newvatid; 
	  $ret['id'] = $id; 
	  echo json_encode($ret); 
	  JFactory::getApplication()->close();
	  die(); 
   }
   
   // UNUSED ON VM2/3 !!!
   public function checkOPCVat()
	{
	    
		static $last_vat_address; 
		if (empty($last_vat_address))
		$last_vat_address = array(); 
		
		//COM_ONEPAGE_VAT_CHECKER_DOWN="EU validation service is currently not available for your country. Please try again later."
		//COM_ONEPAGE_VAT_CHECKER_INVALID="Invalid VAT number"
		//COM_ONEPAGE_VAT_CHECKER_INVALID_COUNTRY="The VAT ID you've entered doesn't match your country."
		$error0 = 'COM_ONEPAGE_VAT_CHECKER_INVALID'; 
		$error2 = 'COM_ONEPAGE_VAT_CHECKER_INVALID_COUNTRY'; 
		$error1 = 'COM_ONEPAGE_VAT_CHECKER_DOWN'; 
		$vatid = $vat_id = JRequest::getVar('vm_eu_vat'); 
		   
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'vm_files'.DIRECTORY_SEPARATOR.'ps_checkout_ext.php'); 
		   
		   $d = array(); 
		   OPCaddressHelper::getAddress($d); 
		   
		  
		   
		   if (!$d['ineu'])
		   return OPCVatWorker::ret('', true, ''); 
	   
		   if (empty($vatid)) 
		   {
		   if (!$d['ineu'])
		   return OPCVatWorker::ret('', true, ''); 
		   else
		   return OPCVatWorker::ret('COM_ONEPAGE_VAT_CHECKER_INVALID', false, ''); 
		   }
		   
		   $orig_vatid = $vatid; 
		   
		   $c = substr($vat_id, 0, 2); 
		   $c = strtoupper($c); 
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'ajax'.DIRECTORY_SEPARATOR.'vat_helper.php'); 
		   
		 
		   
		   
		   $opc_euvat_contrymatch = true; 
		   if ((!in_array($c, OPCVat::$european_states)) || ((!empty($opc_euvat_contrymatch))))
		   {
		    $country = JRequest::getVar('country', ''); 
		    $db = JFactory::getDBO();
			
			$q = "select country_2_code from `#__vm_country` where country_3_code = '".$db->escape($country)."' limit 1 "; 
			
			$db->setQuery($q);
			$db->query();
			$country_2_code = $db->loadResult();
			
			
			
			$c = $c == 'EL' ? 'GR' : $c;
			$c = $c == 'UK' ? 'GB' : $c;
			
			
			
			if (!is_numeric($c))
			if ($c != $country_2_code) return OPCVatWorker::ret( $error2, false, $orig_vatid ); 
			
			}
			else $country_2_code = $c; 
			
			
			$ret = $country_2_code.'_'.$vat_id; 
			
			$company = $e = ''; 
			
			if (!empty($home_vat_num))
			$requester = $home_vat_num; 
			else $requester = 'SK2022104216'; 
			
			$result = OPCVat::isVIESValidVAT($country_2_code, $vat_id, $company, $e, $requester); 
			
			$mk = $country_2_code.$vat_id; 
			if (empty($mk)) $mk = $orig_vatid; 
			
			if ($result === false) 
			{
			
			return OPCVatWorker::ret($error0, false, $mk ); 
			}
		    
			//commnet for testing...
			if ($result === -1) 
			{
			if (stripos($e, 'INVALID_INPUT')!==false) return  OPCVatWorker::ret($error0, false, $mk); 
			
			
			$ret_data = array(); 
			$ret_data['error_msg'] = $e; 
			$ret_data['status'] = false; 
			
			// -1 one means that WE DO NOT KNOW IF THE VALIDATION IS OKAY !
			return OPCVatWorker::ret( $error1, false, $mk, -1, $ret_data ); 
			}
			
			// will be used for shopper group handling
			
			
			if (!empty(OPCvat::$requestIdentifier[$mk]))
			{
			if (!empty(OPCvat::$requestIdentifier[$mk]['requestIdentifier']))
			$suffix = ' (ID:'.OPCvat::$requestIdentifier[$mk]['requestIdentifier'].')'; 
			
			
			foreach (OPCvat::$requestIdentifier[$mk] as $a => $k)
			 {
			    $last_vat_address[$a] = $k;
			 }
			
			}
			else 
			$suffix = ''; 
			
			
			if (is_array($result))
			{
			
			OPCVatWorker::ret('COM_ONEPAGE_VALIDATE_VAT_VALID', true, $mk, $result['requestIdentifier'], $result); 
			
			}
			
			
	}
	
	
}