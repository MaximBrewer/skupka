<?php

/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class OPCmini
{
	
 private static $req_state; 
 
 
 public static function getMemLimit() {
		 $memory_limit = ini_get('memory_limit');
		 if (empty($memory_limit)) return 0; 
			$val = trim($memory_limit);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
	return $val; 
	}
 
 
 public static function &getCart($debug=false) {
	 
	 		if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		  	if (!class_exists('calculationHelper'))
			require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');

		  $cart = VirtuemartCart::getCart(); 
	 
	 if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			unset($cart->products); 
			$cart->products = array(); 
			
			if (isset($cart->_productAdded))
			{
				$cart->_productAdded = true; 
				
			}
			if (isset($cart->_calculated)) {
				$cart->_calculated = false;
			}
			
			
			
			
			
			$ptest = JRequest::getVar('virtuemart_product_id', null); 
			if (!is_null($ptest)) {
				unset($_GET['virtuemart_product_id']); 
				unset($_POST['virtuemart_product_id']); 
				unset($_REQUEST['virtuemart_product_id']); 
				JRequest::setVar('virtuemart_product_id', null); 
			}
			
			
			ob_start(); 
			$stored_coupon = $cart->couponCode; 
			$cart->prepareCartData(); 
			$cart->couponCode = $stored_coupon; 
			$zz = ob_get_clean(); 
			
			
			
			
			JPluginHelper::importPlugin('vmpayment');
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmgetPaymentCurrency', array( $cart->virtuemart_paymentmethod_id, &$cart->paymentCurrency));
			
			if (empty($cart->couponCode)) {
				if (class_exists('AwoCouponVirtuemartCouponHandler')) {
				if (!empty($debug)) {
			      AwoCouponVirtuemartCouponHandler::process_autocoupon($debug);
				}
				else {
					AwoCouponVirtuemartCouponHandler::process_autocoupon();
				}
					
				
				
				}
			
			}
			
			$cart->order_language = JRequest::getVar('order_language', $cart->order_language);
			
			
		}
		
		return $cart; 
 }
 
 public static function storeReqState($arr) {
	 
	 self::$req_state = array(); 
	 
	 foreach($arr as $k=>$v) {
		 $obj = array(); 
		 if (isset($_GET[$k])) $obj['_GET'] = $v; 
		 else $obj['_GET'] = null; 
		 
		 if (isset($_POST[$k])) $obj['_POST'] = $v; 
		 else $obj['_POST'] = null; 
		 
		 if (isset($_REQUEST[$k])) $obj['_REQUEST'] = $v; 
		 else $obj['_REQUEST'] = null; 
		 
		 self::$req_state[$k] = $obj; 
		 
	 }
	 
	 
 }
 
 public static function loadReqState($arr) {
	 foreach ($arr as $k => $v) {
		 
		 if (isset(self::$req_state[$k])) {
			 
			 if (!is_null(self::$req_state[$k]['_GET'])) $_GET[$k] = self::$req_state[$k]['_GET']; 
			else unset($_GET[$k]);
		 
			if (!is_null(self::$req_state[$k]['_POST'])) $_POST[$k] = self::$req_state[$k]['_POST']; 
			else unset($_POST[$k]);
			
			 if (!is_null(self::$req_state[$k]['_REQUEST'])) $_REQUEST[$k] = self::$req_state[$k]['_REQUEST']; 
			else unset($_REQUEST[$k]);
		 
			 
		 }
		 
	 }
 }
 
	
 function loadJSfile($file)
 {
   jimport('joomla.filesystem.file');
   $file = JFile::makeSafe($file); 
   $pa = pathinfo($file); 
   $fullpath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.$file; 
   if (!empty($pa['extension']))
   if ($pa['extension']=='js')
    {
	 //http://php.net/manual/en/function.header.php 
	if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
		@header("Content-type: text/javascript");
		@header("Content-Disposition: inline; filename=\"".$file."\"");
		//@header("Content-Length: ".filesize($fullpath));
	} else {
		@header("Content-type: application/force-download");
		@header("Content-Disposition: attachment; filename=\"".$file."\"");
		//@header("Content-Length: ".filesize($fullpath));
	}
	@header("Expires: Fri, 01 Jan 2010 05:00:00 GMT");
	if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")==false) {
	@header("Cache-Control: no-cache");
	@header("Pragma: no-cache");
    }
	//include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.$file);
	echo file_get_contents($fullpath); 
	$doc = JFactory::getApplication(); 
	$doc->close(); 
   

	}
	
	
 }
 public static function extExists($ext) {
	 static $c; 
	 if (isset($c[$ext])) return $c[$ext]; 
   $db = JFactory::getDBO(); 
   $q = "select * from #__extensions where element = '".$db->escape($ext)."' limit 0,1"; 
   $db->setQuery($q); 
   $r = $db->loadAssoc(); 
   if (empty($r)) return false; 
   $c[$ext] = (array)$r; 
   return $c[$ext]; 
 }
 
  public static function getCurrentCurrency() {
	 $mainframe = JFactory::getApplication();
	 $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 
	 
	 if (!class_exists('VirtuemartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
		$cart = VirtuemartCart::getCart(); 
	 
	 if (empty($virtuemart_currency_id)) {
		

		if (!empty($cart->pricesCurrency)) {
			$virtuemart_currency_id = $cart->pricesCurrency; 
		}
		else {
			if (!empty($cart->paymentCurrency))
	 	    {
				$virtuemart_currency_id = $cart->paymentCurrency;
			}
		}
	 }
	 $db = JFactory::getDBO(); 
	 if (empty($virtuemart_currency_id)) {
		 // secnd take the vendors currency: 
		if (!empty($cart->vendorId))
		$vendorId = $cart->vendorId; 
		else $vendorId = 1; 	
		
		if (empty($vendorId)) $vendorId = 1; 
			
		$q  = 'SELECT  `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id` = '.(int)$vendorId.' limit 0,1';
		$db->setQuery($q);
		$virtuemart_currency_id = $db->loadResult();
		$virtuemart_currency_id = (int)$virtuemart_currency_id; 
		
		
		  
	 }
	 
	 return (int)$virtuemart_currency_id; 
 }
 
 public static function displayPrice($price) {
	 if ($cur === 0) $cur = self::getCurrentCurrency(); 
	 return self::displayCustomCurrency($price, $cur, '', false); 
 }
 public static function displayCustomCurrency($price, $cur=0, $convertFrom='', $hideIfCurrent=true) {
    if ($cur === 0) $cur = self::getCurrentCurrency(); 
	if (!empty($convertFrom)) {
		$price = OPCmini::convertPrice($price, $convertFrom, $cur); 
	}
	
	
	
	if (!class_exists('CurrencyDisplay'))
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
	
	$price = (float)$price; 
	
	static $curinfo; 
	
	
	
	if (!isset($curinfo[$cur])) {
   if ((!is_numeric($cur)) && (strlen($cur)==3)) {
	   $db = JFactory::getDBO(); 
	   $q = 'select `virtuemart_currency_id` from `#__virtuemart_currencies` where `currency_code_3` = '."'".$db->escape($cur)."'".' limit 0,1'; 
	   $db->setQuery($q); 
	   $cidI = $db->loadResult(); 
	   $cidI = (int)$cidI; 
	   if (empty($cidI)) return ''; 
   }
   else {
	   $cidI = (int)$cur; 
   }
      
      $curinfo[$cur] = self::getCurInfo($cidI); 
	}
   
   
   
   
   if ($hideIfCurrent) {
    $current = self::getCurrentCurrency(); 
	$cid = (int)$curinfo[$cur]['virtuemart_currency_id']; 
	
    if ($current === $cid) return ''; 
   }
   
   //format per curency config: 
   $nbDecimal = (int)$curinfo[$cur]['currency_decimal_place']; 
   $decimalP = $curinfo[$cur]['currency_decimal_symbol']; 
   $positivePos =  $curinfo[$cur]['currency_positive_style']; 
   $negativePos =  $curinfo[$cur]['currency_negative_style']; 
   $thousands = $curinfo[$cur]['currency_thousands']; 
   $symbol = $curinfo[$cur]['currency_symbol']; 
   
   if($price>=0){
			$format = $positivePos;
			$sign = '+';
		} else {
			$format = $negativePos;
			$sign = '-';
			$price = abs($price);
		}

		
		$res = number_format($price, $nbDecimal, $decimalP, $thousands);
		$search = array('{sign}', '{number}', '{symbol}');
		$replace = array($sign, $res, $symbol);
		$resultPrice = str_replace ($search,$replace,$format);
   
   
   
   return  $resultPrice; 
   
   
 }
 
 public static function getVendorCurrency() {
	 static $virtuemart_currency_id; 
	 if (!empty($virtuemart_currency_id)) return $virtuemart_currency_id; 
	 
	  if (!class_exists('VirtuemartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
		$cart = VirtuemartCart::getCart(); 
	 
	 if (!empty($cart->vendorId))
		$vendorId = $cart->vendorId; 
		else $vendorId = 1; 	
	 
	  $db = JFactory::getDBO(); 
	 $q  = 'SELECT  `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id` = '.(int)$vendorId.' limit 0,1';
		$db->setQuery($q);
		$virtuemart_currency_id = $db->loadResult();
		$virtuemart_currency_id = (int)$virtuemart_currency_id; 
		
		return $virtuemart_currency_id; 
 }
 
 // from cur1 to cur2 
 public static function convertPrice($price, $cur1, $cur2) {
		
		if (($cur1 === $cur2) || (empty($cur1)) || (empty($cur2))) return $price; 
	$db = JFactory::getDBO(); 
	if ((!is_numeric($cur1)) && (strlen($cur1)==3)) {
	   
	   $q = 'select `virtuemart_currency_id` from `#__virtuemart_currencies` where `currency_code_3` = '."'".$db->escape($cur1)."'".' limit 0,1'; 
	   $db->setQuery($q); 
	   $cidI = $db->loadResult(); 
	   $cur1 = (int)$cidI; 
	   if (empty($cidI)) return $price; 
   }
   if ((!is_numeric($cur2)) && (strlen($cur2)==3)) {
	  
	   $q = 'select `virtuemart_currency_id` from `#__virtuemart_currencies` where `currency_code_3` = '."'".$db->escape($cur2)."'".' limit 0,1'; 
	   $db->setQuery($q); 
	   $cidI = $db->loadResult(); 
	   $cur2 = (int)$cidI; 
	   if (empty($cidI)) return $price; 
   }
		
		
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
		
			
		$c1o = self::getCurInfo($cur1); 
		$c2o = self::getCurInfo($cur2); 
		
		$v = self::getVendorCurrency(); 
		
		
		
		if (!empty($c2o['currency_exchange_rate']))
		if ($cur1 === $v) {
			
			$price = (float)$price; 
			$c2o['currency_exchange_rate'] = (float)$c2o['currency_exchange_rate'];
			$price = $c2o['currency_exchange_rate'] * $price; 
			return $price; 
		}
		
		
		if (!empty($c1o['currency_exchange_rate']))
		if ($cur2 === $v) {
			$price = (float)$price; 
			$c1o['currency_exchange_rate'] = (float)$c1o['currency_exchange_rate'];
			if (empty($c1o['currency_exchange_rate'])) $c1o['currency_exchange_rate'] = 1; 
			$price = $price / $c1o['currency_exchange_rate']; 
			return $price; 
		}
		
		
		static $cC; 
		static $rate; 
		
		
		if (empty($cC)) {
		$converterFile  = VmConfig::get('currency_converter_module','convertECB.php');

		if (file_exists( JPATH_ADMINISTRATOR.DS.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DS.'currency_converter'.DIRECTORY_SEPARATOR.$converterFile ) and !is_dir(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'currency_converter'.DIRECTORY_SEPARATOR.$converterFile)) {
			$module_filename=substr($converterFile, 0, -4);
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DS.'plugins'.DS.'currency_converter'.DS.$converterFile);
			if( class_exists( $module_filename )) {
				$cC = new $module_filename();
			}
		} else {

			if(!class_exists('convertECB')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'currency_converter'.DIRECTORY_SEPARATOR.'convertECB.php');
			$cC = new convertECB();

		}
		}
		
		
		
		$priceC = (float)$price; 
		if (empty($priceC)) return $price; 
		
		if (!isset($rate[$cur1.'_'.$cur2]))
		if ((method_exists($cC, 'convert'))) {
		  
		  $multi = PHP_INT_MAX;
		  try {
		   $rateZ = $cC->convert( PHP_INT_MAX, $c1o['currency_code_3'], $c2o['currency_code_3']);
		  }
		  catch (Exception $e) {
		    $rateZ = 1; 
		  }
		  $rate[$cur1.'_'.$cur2] = $rateZ / PHP_INT_MAX; 
		}
		else
		{
			$rate[$cur1.'_'.$cur2] = 1; 
		}
		
		$priceC = $price * $rate[$cur1.'_'.$cur2]; 
		return $priceC; 
		
		
 }
 
 
 public static function getCurInfo($currency)
   {
	   static $c; 
	   static $c2; // always vendor currency
	   $currency = (int)$currency; 
	   $db = JFactory::getDBO();
	   if (empty($currency)) {
		if (empty($c2)) {
	    
		if (!class_exists('VmConfig'))	  
		{
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 VmConfig::loadConfig(); 
		}
		
		
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			$cart = VirtuemartCart::getCart(); 
		
			   if (defined('VM_VERSION') && (VM_VERSION >= 3))
			   {
				    if (method_exists($cart, 'prepareCartData')) {
						ob_start(); 
				     $cart->prepareCartData(); 
					 $zz = ob_get_clean(); 
					}
			   }
		
		// first take the cart currency: 
		if (!empty($cart->pricesCurrency)) {
			$currency  = $c2 = $cart->pricesCurrency; 
		}
		else {
			
		// secnd take the vendors currency: 
		if (!empty($cart->vendorId))
		$vendorId = $cart->vendorId; 
		else $vendorId = 1; 	
		
		if (empty($vendorId)) $vendorId = 1; 
			
		$q  = 'SELECT  `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`='.$vendorId;
		$db->setQuery($q);
		$vendor_currency = $db->loadResult();
		$c2 = $vendor_currency; 
		
		
		  
		}
		}
		else
		{
			$currency = $c2; 
		}
	   }
	   
	   
	   if (isset($c[$currency])) return $c[$currency]; 
	    
	   $q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$currency.' limit 0,1'; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   if (empty($res)) {
	 
	
	   $res = array(); 
	   $res['currency_symbol'] = '$'; 
	   $res['currency_decimal_place'] = 2; 
	   $res['currency_decimal_symbol'] = '.'; 
	   $res['currency_thousands'] = ' '; 
	   $res['currency_positive_style'] = '{number} {symbol}';
	   $res['currency_negative_style'] = '{sign}{number} {symbol}'; 
	   
	   
	   

	   
	   
	   }
	   $res = (array)$res; 
	   
	   $c[$currency] = $res; 
	   return $res; 
   }
 
 
 public static function setVMLANG() {
	 
	  if (!class_exists('VmConfig'))
		    {
			     if (!class_exists('VmConfig'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig ();

			}
			if(!defined('VMLANG'))		
			if (method_exists('VmConfig', 'setdbLanguageTag')) {
			   VmConfig::setdbLanguageTag();
			}
	 
    
	if ((!defined('VMLANG')) && (!empty(VmConfig::$vmlang))) {
	  define('VMLANG', VmConfig::$vmlang); 
	}
		
 }
 // gets rid of any DB references or DB objects, all objects are converted to stdClass
 public static function toObject(&$product, $recursion=0) {
    
	
	
	if (is_object($product)) {
	 $copy = new stdClass(); 
	 $attribs = get_object_vars($product); 
	 $isO = true; 
	}
	elseif (is_array($product)) {
		  $copy = array(); 
		  $isO = false; 
		  $attribs = array_keys($product); 
		  $copy2 = array(); 
		  foreach ($attribs as $zza=>$kka) {
		       if (strpos($kka, "\0")===0) continue;
			   $copy2[$kka] = $product[$kka]; 
		  }
		  $attribs = $copy2; 
		}
		
	if (!empty($attribs))
    foreach ($attribs as $k=> $v) {
		
		if (strpos($k, "\0")===0) continue;
		if ($isO) {
	      $copy->{$k} = $v; 	
		}
		else
		{
			$copy[$k] = $v; 
		}
		if (empty($v)) continue; 
		//if ($recursion < 5)
		if ((is_object($v)) && (!($v instanceof stdClass))) {
		   $recursion++; 
		   if ($isO) {
		     OPCmini::toObject($copy->{$k}, $recursion); 
		   }
		   else
		   {
			   OPCmini::toObject($copy[$k], $recursion); 
		   }
		}
		else
		{
			
			if (is_array($v)) {
			   $recursion++; 
			   if ($isO) {
		        OPCmini::toObject($copy->{$k}, $recursion); 
			   }
			   else
			   {
				   OPCmini::toObject($copy[$k], $recursion); 
			   }
			}
		}
		/*
		if (is_array($v)) {
		
		  $keys = array_keys($v); 
	  
		  foreach ($keys as $kk2=>$z2) {
		     if (strpos($z2, "\0")===0) continue;
			 $copy->{$k}[$z2] = $v[$z2]; 
			 if ((is_object($v[$z2])) && (!($v[$z2] instanceof stdClass))) {
				$recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 else
			 if (is_array($v[$z2])) {
			    $recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 
		  }
		}
		*/
		
		
	}
	$recursion--;
	if (empty($copy)) return; 
	$product = $copy; 
 }
 public static function isMysql($ver, $operator='>=') {
   $db = JFactory::getDBO(); 
   $q = 'SELECT @@version as version'; 
   $db->setQuery($q); 
   $version = $db->loadResult(); 
   
   if (stripos($version, '-')) {
     $versionA = explode('-', $version); 
	 if (count($versionA)>1) $version = $versionA[0]; 
   }
   return version_compare($version, $ver, $operator); 
   
 }
 public static function parseCommas($str, $toint=true)
 {
	  if (empty($str)) return array(); 
	  $e = explode(',', $str); 
	  
	  $ea = array(); 
	  if (count($e)>0) {
	    foreach ($e as $c) {
		  $c = trim($c); 
		  if ($c === '0') {
		   $ea[0] = 0; 
		   continue; 
		  }
		  if ($toint) {
		    $c = (int)$c; 
		  }
		  if (empty($c)) continue; 
		  $ea[$c] = $c; 
		}
	  }
	  else
	  {
		  $c = trim( $str ); 
		  if ($c === '0') {
		   $ea[0] = 0; 
		  }
		  if ($toint) {
			$c = (int)$c;
		  }		  
		  if (!empty($c)) $ea[$c] = $c; 
	  }
	  return $ea; 
 }
 
 public static function getPrimary($table) {
	  if (!static::tableExists($table)) return array(); 
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
	 
   if (isset(static::$cache['primary_'.$table])) return static::$cache['primary_'.$table]; 
   // here we load a first row of a table to get columns
   
   $q = 'SHOW COLUMNS FROM '.$table; 
   $db->setQuery($q); 
   $res = $db->loadAssocList(); 
  
   $new = '';
   if (!empty($res)) {
    foreach ($res as $k=>$v)
	{
		$field = (string)$v['Field']; 
		$auto = (string)$v['Extra']; 
		$key = (string)$v['Key']; 
		if (($key === 'PRI') || (stripos($auto, 'auto_increment')!==false)) {
			$new = $field; 
			break; 
		}
		
	}
	static::$cache['primary_'.$table] = $new; 
	return $new; 
   }
   static::$cache['primary_'.$table] = '';
   return array(); 
 }
 
 public static function getUnique($table) {
	  if (!static::tableExists($table)) return array(); 
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
	 
   if (isset(static::$cache['unique_'.$table])) return static::$cache['unique_'.$table]; 
   // here we load a first row of a table to get columns
   
   $q = 'SHOW COLUMNS FROM '.$table; 
   $db->setQuery($q); 
   $res = $db->loadAssocList(); 
  
   $new = '';
   if (!empty($res)) {
    foreach ($res as $k=>$v)
	{
		$field = (string)$v['Field']; 
		$auto = (string)$v['Extra']; 
		$key = (string)$v['Key']; 
		if (($key === 'PRI') && (stripos($auto, 'auto_increment')!==false)) {
			if (empty($new))
			$new = $field; 
		
		}
		if ($key === 'UNI') {
			
			$new = $field; 
		}
		
	}
	static::$cache['unique_'.$table] = $new; 
	return $new; 
   }
   static::$cache['unique_'.$table] = '';
   return array(); 
 }
 
 
 public static function getUniques($table) {
	  if (!static::tableExists($table)) return array(); 
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
	 
   if (isset(static::$cache['uniques'.$table])) return static::$cache['uniques'.$table]; 
   // here we load a first row of a table to get columns
   
   $q = 'SHOW COLUMNS FROM '.$table; 
   $db->setQuery($q); 
   $res = $db->loadAssocList(); 
  
  
   $allkeys = array(); 
   
   $new = '';
   if (!empty($res)) {
    foreach ($res as $k=>$v)
	{
		$field = (string)$v['Field']; 
		$auto = (string)$v['Extra']; 
		$key = (string)$v['Key']; 
		
		
		if ($key === 'UNI') {
			
			$new = $field; 
			$allkeys[] = $new; 
		}
		
	}
	static::$cache['uniques_'.$table] = $allkeys; 
	return $allkeys; 
   }
   
   return array(); 
 }
 
  public static function insertArray($table, &$fields, $def=array())
 {
	 
	 $primary = $primary_col = static::getPrimary($table); 
	 $primary_id = 'NULL'; 
	 if ((!empty($primary)) && (isset($fields[$primary]) && ($fields[$primary] !== 'NULL'))) {
		 
		 $primary_id = (int)$fields[$primary]; 
     }
	 
	 
	
	 
	 
	 $unique_val = ''; 
	 $unique_col = static::getUnique($table); 
	 if ((!empty($unique_col)) && (isset($fields[$unique_col]) && ($fields[$unique_col] !== 'NULL'))) {
	  $unique_val = $fields[$unique_col]; 
	 }
	 $dbvv = JFactory::getDBO(); 
	 // check for other unique keys before insert
	 
	 if ((!empty($primary_col)) && (!empty($unique_col)))
	 if ($unique_col !== $primary_col) {
		 
		 
		 $q = 'select * from '.$table.' where '.$unique_col." = '".$dbvv->escape($unique_val)."'"; 
		 $dbvv->setQuery($q); 
		 $res = $dbvv->loadAssoc(); 
		 
		 
		 
	 }
	 
	 
	 
	 if (empty($def)) {
		 $def = static::getColumns($table); 
	 }
	 foreach ($fields as $k=>$v)
	 {
		 if (!isset($def[$k])) {
			 //n_log::notice('Found extra column for '.$table.'.'.$k); 
			 unset($fields[$k]); 
		 }
		 else
		 if (is_null($fields[$k])) {
			 //if we are just updating, don't overwrite with null values !
			 //n_log::notice('DB unsetting null values '.$table.'.'.$k); 
			 unset($fields[$k]); 
		 }
		 
	 }
	 
	 if (empty($fields)) {
		 //n_log::warning('attempt to insert empty values to '.$table); 
		 return; 
	 }
	 
	
	 $q = 'insert into `'.$table.'` (';
	 $qu = 'update `'.$table.'` set '; 
	 $keys = ''; 
	 $vals = ''; 
	 $i = 0; 
	 $c = count($fields); 
	 $quq = array(); 
	 foreach ($fields as $key=>$val)
	 {
	  $keys .= '`'.$key.'`'; 
	  $i++;
	  
	  if ($val === 'NULL')
	   $val = 'NULL'; 
      elseif ($val === 'NOW()') {
		  $val = 'NOW()'; 
	  }
	  else 
	   $val = "'".$dbvv->escape($val)."'"; 
	  
	  $vals .= $val; 
	  
	  if ($i < $c) { 
	   $keys .= ', ';
	   $vals .= ', ';
	   }
	   
	   if ($key !== $primary)
	   $quq[] = ' `'.$key."`='".$dbvv->escape($val)."'"; 
	  
	 }
	 $q .= $keys.') values ('.$vals.') ';
	 $q .= ' ON DUPLICATE KEY UPDATE '; 
	 $u = false; 
	 foreach ($fields as $key=>$val)
	 {
	  if ($u) $q .= ','; 
	  $q .= '`'.$key.'` = '; 
	  $q .= "'".$dbvv->escape($val)."'"; 
	  $u = true; 
	 }
	 
	 
	 //n_log::_($q); 
	 
	 if ((!empty($primary)) && (!empty($primary_id))) {
	   $qu .= ' set '.implode(', ', $quq). ' where `'.$primary.'` = '.(int)$primary_id; 
	   //n_log::_('UPDATETEST: '.$qu); 
	 }
	 
	 $dbvv->setQuery($q); 
	 $dbvv->query();
	 
	 
	 
	 if ((!empty($primary)) && ((empty($primary_id)) || ($primary_id === 'NULL'))) {
	   $primary_id = $dbvv->insertid(); 
	   $fields[$primary] = $primary_id; 
	 }
	 
	  
 }
 
 public static function insertArrayRemoved($table, $fields, $def=array())
 {
	 if (empty($def)) {
		 $def = self::getColumns($table); 
	 }
	 foreach ($fields as $k=>$v)
	 {
		 if (!isset($def[$k])) unset($fields[$k]); 
	 }
	 
	 if (empty($fields)) return; 
	 
	 $dbvv = JFactory::getDBO(); 
	 $q = 'insert into `'.$table.'` (';
	 $keys = ''; 
	 $vals = ''; 
	 $i = 0; 
	 $c = count($fields); 
	 foreach ($fields as $key=>$val)
	 {
	  $keys .= '`'.$key.'`'; 
	  $i++;
	  
	  if ($val === 'NULL')
	   $vals .= 'NULL'; 
	  else 
	   $vals .= "'".$dbvv->escape($val)."'"; 

	  if ($i < $c) { 
	   $keys .= ', ';
	   $vals .= ', ';
	   }
	  
	 }
	 $q .= $keys.') values ('.$vals.') ';
	 $q .= ' ON DUPLICATE KEY UPDATE '; 
	 $u = false; 
	 foreach ($fields as $key=>$val)
	 {
	  if ($u) $q .= ','; 
	  $q .= '`'.$key.'` = '; 
	  $q .= "'".$dbvv->escape($val)."'"; 
	  $u = true; 
	 }
	 
	 $dbvv->setQuery($q); 
	 $dbvv->query();
 }
 public static function getColumns($table) {
   if (!self::tableExists($table)) return array(); 
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
	 
   if (isset(OPCmini::$cache['columns_'.$table])) return OPCmini::$cache['columns_'.$table]; 
   // here we load a first row of a table to get columns
   $db = JFactory::getDBO(); 
   $q = 'SHOW COLUMNS FROM '.$table; 
   $db->setQuery($q); 
   $res = $db->loadAssocList(); 
  
   $new = array(); 
   if (!empty($res)) {
    foreach ($res as $k=>$v)
	{
		
		$new[$v['Field']] = $v['Field']; 
	}
	OPCmini::$cache['columns_'.$table] = $new; 
	return $new; 
   }
   OPCmini::$cache['columns_'.$table] = array(); 
   return array(); 
   
   
 }
 
   public static function getVMTemplate($view='', $layout='') {
	   jimport('joomla.filesystem.file');
	   $view = JFile::makeSafe($view); 
	   $view = strtolower($view); 
	   $layout = strtolower($layout); 
	   $layout = JFile::makeSafe($layout); 
	   
		$vmtemplate = VmConfig::get( 'vmtemplate', 'default' );
		
		
		
		
			$template = JFactory::getApplication()->getTemplate(); 
			
			  
			  $pj = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart';
			  
			  if (!empty($view)) {
			  $pj .= DIRECTORY_SEPARATOR.$view; 
			  }
			  
			  if (!empty($layout)) { 
			   $pj .= DIRECTORY_SEPARATOR.$layout.'.php'; 
			    
			  }
			  
			  
			  
			  if (file_exists($pj)) {
				  
				  if (empty($view) && (empty($layout))) return $template; 
				  
				  return $pj; 
			  }
			  
			  if($vmtemplate !== 'default') {
			   $template = $vmtemplate; 
			   $pj = JPATH_SITE.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart';
			  
			  if (!empty($view)) {
			  $pj .= DIRECTORY_SEPARATOR.$view; 
			  }
			  
			  if (!empty($layout)) { 
			   $pj .= DIRECTORY_SEPARATOR.$layout.'.php'; 
			    
			  }
			  
			  if (file_exists($pj)) {
				  
				  if (empty($view) && (empty($layout))) return $template; 
				  
				  
				  return $pj; 
			  }
			  }
			  
			  
			
		  $pv = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'; 
		  
		    if (!empty($view)) {
		    $pv .= DIRECTORY_SEPARATOR.$view.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR;
		    }
			
			if (!empty($layout)) { 
			$pv .= $layout.'.php'; 
			
			}
			if (file_exists($pv)) { 
			
			if (empty($view) && (empty($layout))) return 'default'; 
			
			return $pv; 
			}
			
		 
		
		
		
		
		
   
   }
 
    public static $selected_template; 
	
	
	public static function getSelectedTemplate($selected_template_config=null, $mobile_template_config=null)
	{
	 
	 
	 if (!empty(OPCmini::$selected_template)) 
	 {
	
		 return OPCmini::$selected_template; 
	 }
	 
	 $app = JFactory::getApplication(); 
	 if (($app->isAdmin()) && (!is_null($selected_template_config)))
	 {
		 OPCmini::$selected_template = $selected_template_config; 
		 return OPCmini::$selected_template; 
	 }
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');  
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php');  
	 OPCplugin::detectMobile(); 
	 
	 $selected = JRequest::getVar('opc_theme', null); 
	  {
	    if (!empty($selected))
		 {
			 
		    jimport( 'joomla.filesystem.file' );
		    $selected = JFile::makeSafe($selected); 
			$dir = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected;
			
			if (file_exists($dir) && ($selected != 'extra'))
			{
			
		     OPCmini::$selected_template = $selected; 
			 if (!defined('OPC_DETECTED_DEVICE'))
			 define('OPC_DETECTED_DEVICE', 'DESKTOP'); 
			 return OPCmini::$selected_template; 
			}
		 }
		 else
		 {
			
		 }
	  }
	 
	 if (!defined('OPC_DETECTED_DEVICE'))
	 {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php'); 
	  OPCplugin::detectMobile(); 
	 }
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	 
	 if (is_null($selected_template_config))
	 {
	   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');  
	 }
	 else
	 {
		 $selected_template = $selected_template_config; 
		 $mobile_template = $mobile_template_config; 
	 }
	 
	 
	 if (!empty($selected_template) && ($selected_template != 'extra'))
     OPCmini::$selected_template = $selected_template; 
	 
	 if (defined('OPC_DETECTED_DEVICE') && (OPC_DETECTED_DEVICE != 'DESKTOP'))
     if (!empty($mobile_template)) {
		 OPCmini::$selected_template = $selected_template = $mobile_template; 	 
	 
	 
	 }
 
	 if (class_exists('OPCloader'))
	 if (OPCloader::checkOPCSecret())
     {
	  if (!empty($selected_template) && ($selected_template != 'extra'))
	  OPCmini::$selected_template .= '_preview'; 
     }
	 
	 
	 

	 
	 return OPCmini::$selected_template; 
	}
 public static function isSuperVendor(){

 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
 
	if ((!defined('VM_VERSION')) || (VM_VERSION < 3))
			{
			if (!class_exists('Permissions'))
			require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				return true; 
				
			}
			}
			else
			{
			 $text = '';
			$user = JFactory::getUser();
			if($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart') or VmConfig::isSuperVendor()) {
			  return true; 
			}
			}
			
			return false; 
	}
 
   public static $cache; 
   static function clearTableExistsCache()
   {
    OPCmini::$cache = array(); 
   }
   
   // -1 for a DB error, true for has index and false for does not have index
   public static function hasIndex($table, $column, $isunique=false)
   {
	   
	   
	    $db = JFactory::getDBO(); 
		$prefix = $db->getPrefix();
	    $table = str_replace('#__', '', $table); 
		$table = str_replace($prefix, '', $table); 
		$table = $db->getPrefix().$table; 
	    if (!OPCmini::tableExists($table)) return -1; 
	    $q = "SHOW INDEX FROM `".$table."`"; 
		try
		{
		 $db->setQuery($q); 
		 $r = $db->loadAssocList(); 
		}
		catch (Exception $e)
		{
			//JFactory::getApplication()->enqueueMessage($e); 
			
			return -1; 
		}
		
		if (empty($r)) return false; 
		
		$composite = array(); 

		$toreturn = -1; 
		
		
		foreach ($r as $k=>$row)
		{
		OPCmini::toUpperKeys($row); 
		
		if ((!empty($row['NON_UNIQUE'])) && (!empty($isunique))) {
			
			
			continue; 
		}
		
		if (isset($row['KEY_NAME'])) {
		  if (empty($composite[$row['KEY_NAME']])) $composite[$row['KEY_NAME']] = array(); 
		  $composite[$row['KEY_NAME']][] = $row['COLUMN_NAME']; 
		}
		/*
		foreach ($row as $kn=>$data)
		{
			$kk = strtolower($kn); 
			
			if (($kk === 'key_name') || ($kk === 'column_name'))
			{
				
				
				$dt = strtolower($data); 
				$c = strtolower($column); 
				if ($dt === $c) $toreturn = true; 
				if ($dt === $c.'_index') $toreturn = true; 
			}
		}
		*/
		}
		if (!is_array($column)) {
			
		  $c = strtolower($column);
		foreach ($composite as $z=>$r2) {
		  $first = $r2[0]; 
		  $first = strtolower($first); 

		  if ($first === $c) {
			 
			  return true; 
		  }
		  if ($first === $c.'_index') return true; 
		  
		 //echo 'first: '.$first."<br />\n";
		 //echo 'c: '.$c."<br />\n";
		  
		}
		
		 
		
		}
		else
		{
			foreach ($composite as $z=>$r2) {
				$ok = false; 
				foreach ($column as $c) {
				   $rx = $r2; 
				   if (!in_array($c, $r2)) {
					   $ok = false; 
					   continue; 
				   }
				   $ok = true; 
				}
				
				if ($ok) return true; 
				
			}
		}
		
		
		
		return false; 
	   
   }
   public static function toUpperKeys(&$arr) {
	   $arr2 = array(); 
	   foreach ($arr as $k=>$v)
	   {
		   if (is_string($k)) {
		     $arr2[strtoupper($k)] = $v; 
		   }
		   else
		   {
			   $arr2[$k] = $v; 
		   }
	   }
	   $arr = $arr2; 
   
   }
   static function addIndex($table, $cols=array(), $isUnique=false)
   {
	   if (empty($cols)) return; 
	   $db = JFactory::getDBO(); 
		$prefix = $db->getPrefix();
	    $table = str_replace('#__', '', $table); 
		$table = str_replace($prefix, '', $table); 
		$table = $db->getPrefix().$table; 
		if (!OPCmini::tableExists($table)) return 'Table does not exist !'; 
		
		$name = reset($cols); 
		if ($isUnique) {
			$name .= '_uindex'; 
		}
		else {
		 $name .= '_index'; 
		}
		foreach ($cols as $k=>$v)
		{
			if (!is_numeric($k)) { $name = $k; }
			$cols[$k] = '`'.$db->escape($v).'`'; 
		}
		$cols = implode(', ', $cols); 
		
		if ($isUnique) {
		 //ALTER TABLE `vepao_virtuemart_products` ADD UNIQUE `product_sku` (`product_sku`);
		 $q = "ALTER TABLE  `".$table."` ADD UNIQUE  `".$db->escape($name)."` (  ".$cols." ) "; 
		}
		else {
		 $q = "ALTER TABLE  `".$table."` ADD INDEX  `".$db->escape($name)."` (  ".$cols." ) "; 
		}
		try {
		 $db->setQuery($q); 
		 $db->query(); 
		}
		catch (Exception $e)
		{
		   //JFactory::getApplication()->enqueueMessage($e); 
		   return (string)$e; 
		}
		return 'O.K.'; 
   }
   public static function getCountryByID($id, $what = 'country_name' ) {
		static $c; 
		if (isset($c[$id.'_'.$what])) return $c[$id.'_'.$what]; 
		
		if (!class_exists('ShopFunctions'))
		   require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
	   
		$ret = (string)shopFunctions::getCountryByID($id, $what); 
	    $c[$id.'_'.$what] = $ret; 
		return $ret; 
	}
	
   static function tableExists($table)
  {
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
   
   
   if (isset(OPCmini::$cache[$table])) return OPCmini::$cache[$table]; 
   
   $q = 'select * from '.$table.' where 1 limit 0,1';
   // stAn, it's much faster to do a positive select then to do a show tables like...
    /*
	if(version_compare(JVERSION,'3.0.0','ge')) 
	{
	try
    {
		$db->setQuery($q); 
		$res = $db->loadResult(); 
		
		if (!empty($res))
		{
			OPCmini::$cache[$table] = true; 
			return true;
		}
		$er = $db->getErrorMsg(); 
		if (empty($er))
		{
			OPCmini::$cache[$table] = true; 
			return true;
		}
		
		
    } catch (Exception $e)
	{
		  $e = (string)$e; 
	}
	}
    */	
   
   $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (empty(OPCmini::$cache)) OPCmini::$cache = array(); 
	   
	   if (!empty($r)) 
	    {
		OPCmini::$cache[$table] = true; 
		return true;
		}
		OPCmini::$cache[$table] = false; 
   return false;
  }

     // moved from opc loaders so we do not load loader when not needed
	static $modelCache; 
   	public static function getModel($model)
	 {
	 
	 // make sure VM is loaded:
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
		if (empty(OPCmini::$modelCache)) OPCmini::$modelCache = array(); 
	    if (!empty(OPCmini::$modelCache[$model])) return OPCmini::$modelCache[$model]; 
		
		
	    if (!class_exists('VirtueMartModel'.ucfirst($model)))
		require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. strtolower($model).'.php');
		if ((method_exists('VmModel', 'getModel')))
		{
		
		$view = JRequest::getWord('view','virtuemart');
		
		$resetview = false; 
		if (empty($view))
		{
			$view = JRequest::setVar('view','virtuemart');
			$resetview = true; 
		}
		
		$Omodel = VmModel::getModel($model); 
		
		if ($resetview)
		{
			$view = JRequest::setVar('view','');
		}
		
		OPCmini::$modelCache[$model] = $Omodel; 
		return $Omodel; 
		}
		else
		{
			// this section loads models for VM2.0.0 to VM2.0.4
		   $class = 'VirtueMartModel'.ucfirst($model); 
		   if (class_exists($class))
		    {
				
				if ($class == 'VirtueMartModelUser')
				{
				
				//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'user.php'); 
				//$class .= 'Override'; 
				
				 $Omodel = new VirtueMartModelUser; 
				 
				 return $Omodel; 
				 $Omodel->setMainTable('virtuemart_vmusers');
				 
				}
				
				
			    $Omodel = new $class(); 
				
			  OPCmini::$modelCache[$model] = $Omodel; 
			  return $Omodel; 
			}
			else
			{  
			  echo 'Class not found: '.$class; 
			  $app = JFactory::getApplication()->close(); 
			}
			
		}
		echo 'Model not found: '.$model; 
		$app = JFactory::getApplication()->close(); 
		
		//return new ${'VirtueMartModel'.ucfirst($model)}(); 
	 
	 }	
	 
	 public static function slash($string, $insingle = true)
	 {
	    $string = str_replace("\r\r\n", " ", $string); 
   $string = str_replace("\r\n", " ", $string); 
   $string = str_replace("\n", " ", $string); 
   $string = (string)$string; 
   if ($insingle)
    {
	 $string = addslashes($string); 
     $string = str_replace('/"', '"', $string); 
	 return $string; 
	}
	else
	{
	  $string = addslashes($string); 
	  $string = str_replace("/'", "'", $string); 
	  return $string; 
	}
	 
	 }


 
}