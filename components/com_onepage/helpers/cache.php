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

// load OPC loader
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
defined('_JEXEC') or die('Restricted access');
class OPCcache {
	
	public static $totalWeight; 
	public static $cartHash; 
	public static $cachedResult; 
	
	function createHash()
	{
		$args = func_get_args(); 
		foreach ($args as $k=>$l)
		{
			
		}
	}
	
	public static function getValue($hash)
	{
		//$cache = JFactory::getCache('_onepage','');
		
		 if (isset(self::$cachedResult))
		 if (!empty(OPCcache::$cachedResult[$hash]))
		 {
			 return OPCcache::$cachedResult[$hash]; 
		 }
		 
		 $res = OPCcache::get($hash); 
		 
		 if (!empty($res)) return $res; 
		 
		 
	}
	
	public static function getStoredCalculation($cart, &$cart_prices, &$cart_prices_name) 
	{
		
	  $hash = abs($cart->virtuemart_shipmentmethod_id).'_'.OPCcache::$cachedResult['currentshipping'];
	  if (isset(OPCcache::$cachedResult[$hash])) 
	  {
		
		foreach (OPCcache::$cachedResult[$hash] as $k=>$v) 
		{
	      $cart_prices[$k] = $v; 
		}
		$cart_prices_name = $cart_prices['shipmentName']; 
		
		
		
		return true; 
	  }
	  else
	  {
		  
	  }
	  
	}
	
	// $idth is a multi shipping id 
	public static function storeShipingCalculation(&$cart, &$cart_prices, &$cart_prices_name, $idth) 
	{
	  $id = (int)$cart->virtuemart_shipmentmethod_id;
	  $id = abs($id); 
	  
	  require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'third_party_request_cache.php'); 
	  
	  $found = false; 
	  foreach ($cache_only as $se)
	  {
		  if (stripos(OPCcache::$cachedResult['currentshipping'], $se)!==false)
			  $found = true; 
	  }
	  if (!$found) return;
	  
	  $hash = $id.'_'.OPCcache::$cachedResult['currentshipping'];
	  OPCcache::$cachedResult[$hash] = $cart_prices; 
	  
	 //OPCcache::$cachedResult['shippingcache'][abs($cart->virtuemart_shipmentmethod_id)] = true; 
	  OPCcache::$cachedResult['shippingcache'][$idth] = true; 
	  
	}
	
	function getGeneralCacheHash($name, &$cart, &$data=array())
	{

	   // will create product hash (quantity, attributes, weight), order of products do not play role: 
	   OPCcache::createCartHash($cart); 
	   $carthash = OPCcache::$cartHash; 
	   // end of product hash
	   
	   // will create custom cache per fedex and similar: 
	   
	   // zip and country are the basic cache dimensions, add more here: 
	   if (empty($cart->virtuemart_shipmentmethod_id))
	   {
		   $returnValues = array(); 
	   }
	   else
	   if (isset(OPCcache::$cachedResult[$cart->virtuemart_shipmentmethod_id]))
	   {
		   // per request cache: 
		   $returnValues = OPCcache::$cachedResult[$cart->virtuemart_shipmentmethod_id]; 
	   }
	   else
	   {
	   JPluginHelper::importPlugin('shipment');
	   $dispatcher = JDispatcher::getInstance();		   
	   $returnValues = $dispatcher->trigger('plgVmGetSpecificCache', array($cart));
	   }
	   $spec_cache = ''; 
	   foreach ($returnValues as $val)
	   {
		   if (!empty($val))
		   $spec_cache .= $val; 
	   }
		
		if (!empty($returnValues))
		OPCloader::opcDebug($returnValues, 'opc_cache'); 
	   // end of custom
	   
	   // any extras: 
	   if (!empty($data))
	   $datahash = md5(serialize($data));
       else $datahash = ''; 
	   // end of extras

	   // basic address: 
	   $to_address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
	   $zip = @$to_address['zip']; 
	   $country = @$to_address['virtuemart_country_id']; 
	   $country = @$to_address['virtuemart_state_id']; 
	   // end of basic address
	   
	   $alldimensions = $name.md5($carthash.$zip.$country.$datahash.$spec_cache); 
		
	   return $alldimensions; 
	   
		
	}
	
	// will serialize also product's attributes, quantities and other
	function createCartHash(&$cart)
	{
		if (!empty(OPCcache::$totalWeight)) 
		return OPCcache::$totalWeight; 

		OPCcache::$totalWeight = 0; 
		$order_weight = 0; 
		$hash = ''; 
		
		foreach ($cart->products as $product) {

			$h = serialize($product); 
			$md5 = $h; 
			$hash[$md5] = 1; 
			$order_weight += $product->product_weight * $product->quantity;
			
		}
		OPCcache::$cartHash = md5(serialize($hash));
		OPCcache::$totalWeight = $order_weight; 
	}

	function setValue($dimension, $value)
	{
		
		if (!isset(OPCcache::$cachedResult)) OPCcache::$cachedResult = array(); 
		OPCcache::$cachedResult[$dimension] = $value; 
		
		
		OPCcache::store($value, $dimension); 
		
		
	}
	public static function installCache()
	{
		if (defined('OPCCACHEINSTALLED')) return; 
		
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		$path = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'; 
		if (!file_exists($path))
		{
			if (@JFolder::create($path)==false)
			{
				define('OPCCACHEINSTALLED', false); 
			}
		}
		if (!file_exists($path.DIRECTORY_SEPARATOR.'index.html'))
		{
			$data = ' '; 
			if (@JFile::write($path.DIRECTORY_SEPARATOR.'index.html', $data)==false)
				define('OPCCACHEINSTALLED', false); 
		}
		define('OPCCACHEINSTALLED', true); 
		define('OPCCACHESTART', '<?php die(\'access denied\'); ?>'); 
		define('OPCCACHESEP', 'OPCSEPARATOR12349'); 
		define('OPCCACHEPATH', $path); 
	}
	static $iCount; 
	public static function get($hash)
	{
		if (!defined('OPCCACHEPATH')) self::installCache(); 
		self::$iCount++; 
		
		$path = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'; 
		if (defined('OPCCACHEPROBLEM')) return null; 
		//$md = md5($hash); 
		jimport( 'joomla.filesystem.file' );
		$hash = JFile::makeSafe($hash);
		$md = $hash; 
		if (!file_exists(OPCCACHEPATH.DIRECTORY_SEPARATOR.$md.'_timeout.php')) return null; 
		include(OPCCACHEPATH.DIRECTORY_SEPARATOR.$md.'_timeout.php'); 
		$time = time(); 
		

		
		if ((!empty($timeout)) && ($time <= $timeout))
		{
			
			include(OPCCACHEPATH.DIRECTORY_SEPARATOR.$md.'.php'); 
			
			return @$data; 			
		}
		else
		if ((!empty($timeout)) && ($time > $timeout))
		{
			jimport( 'joomla.filesystem.file' );
			JFile::delete(OPCCACHEPATH.DIRECTORY_SEPARATOR.$md.'_timeout.php'); 
			JFile::delete(OPCCACHEPATH.DIRECTORY_SEPARATOR.$md.'.php'); 
		}

		
		
		return null; 
	}
	public static function store($value, $hash, $timeout=72000)
	{
		if (empty($hash)) return; 
		
		$path = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'; 
		if (defined('OPCCACHEPROBLEM')) return; 
		
		//$hash = md5($hash); 
		
		jimport( 'joomla.filesystem.file' );
		$hash = JFile::makeSafe($hash); 
		$time = time(); 
		// 1 day right now
		$time += $timeout; 
		
$data = '<?php if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' ); 
$data = '.var_export($value, true).'; 
'; 

		//$data = OPCCACHESTART.$value; 
		if (@JFile::write($path.DIRECTORY_SEPARATOR.$hash.'.php', $data)==false)
			{
				define('OPCCACHEPROBLEM', false); 
				
			}
			else
			{
				$data2 = '<?php if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' ); 
$timeout = '.$time.'; 
'; 
				if (@JFile::write($path.DIRECTORY_SEPARATOR.$hash.'_timeout.php', $data2)==false)
				{
					define('OPCCACHEPROBLEM', false); 

				}
			}
			
			
			
	}
	
	function getSearch($what, $prices)
	{	
		$ret = array(); 
		foreach ($prices as $k=>$l)
		{
		if (stripos($k, $what)!==false)
		{
			$ret[$k] = $l; 	  
		}	  
  
		}
			return $ret; 
	}
	function arrayMerge(&$to, $from)
	{
		foreach ($from as $k=>$val)
		{
		$to[$k] = $val; 	 
		}	 
	}	

	
}