<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class OPCtrackingHelper {
  
  // will create cookie hash and register it in the database
  public static function registerCart($hash)
  {
    $res = OPCtrackingHelper::getEmptyLine(0, 0, $hash); 
	$user = JFactory::getUser(); 
	$user_id = (int)$user->get('id', 0); 
	$db = JFactory::getDBO(); 
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 
	
	   jimport( 'joomla.utilities.date' );
	   
	   $date = new JDate('now');
	   
	   if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s"); 
	   

	
	if (empty($res))
	 {
	  if (defined('OPCJ3') && (!OPCJ3))
	  {
	   $q = "insert into #__virtuemart_plg_opctracking (virtuemart_order_id, hash, shown, created, created_by, modified, modified_by) values ('0', '".$db->escape($hash)."', '', '".$dd."', '".(int)$user_id."', '".$dd."', '".(int)$user_id."' )"; 
	  }
	  else
	  {
	   $q = "insert into #__virtuemart_plg_opctracking (virtuemart_order_id, hash, shown, created, created_by, modified, modified_by) values ('0', '".$db->escape($hash)."', '', '".$dd."', '".(int)$user_id."', '".$dd."', '".(int)$user_id."' )"; 
	  }
	  
			   $db->setQuery($q); 
			   $db->query(); 
			   
	 }
	 else
	 {
	  
	   self::updateLine($res['id'], $res['virtuemart_order_id'], $hash, $res['shown']); 
	 }
	
	self::clearOld();

  } 

  public static function clearOld()
  {
	  
	    $date = new JDate('now -30 day');
	   
	   if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s"); 
	  $db = JFactory::getDBO(); 
	 
	  $q = "delete from `#__virtuemart_plg_opctracking` where `modified` < '".$db->escape($dd)."' limit 999999"; 
	  
	   $db->setQuery($q); 
	  $db->query(); 
	  
	
  }
	
	public static function checkOK() {
		$tmpl = JRequest::getVar('tmpl', null); 
		$tmpl = strtolower($tmpl); 
		$dis = array('component', 'ajax', 'json', 'error', 'offline', 'module'); 
		
		if (in_array($tmpl, $dis)) return false; 
	    $format = JRequest::getVar('format', 'html'); 
		if ($format !== 'html') return false; 
		return true; 
		
	}
	
    public static function categoryViewEvent()
  {
     
	 if (!self::checkOK()) return; 
	 
	 if (empty(plgSystemOpctrackingsystem::$_product_impressions)) return; 
	 if (empty(plgSystemOpctrackingsystem::$_product_impressions['category'])) return; 
	 $products = plgSystemOpctrackingsystem::$_product_impressions['category']; 
	
	 $cat_id = JRequest::getInt('virtuemart_category_id', 0); 
	 if (empty($cat_id)) return; 
	 
	 
	 
	 $cat = OPCmini::getModel('category'); 
	 $cat->getCategory($cat_id, false, true); 
	 
     $files = self::getEnabledTracking(); 
	 $html = ''; 
	 $js = '';  
	 if (!empty($files))
	  {
	  foreach ($files as $mf)
	   {
	    
		 
		 $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 if (!empty($config->run_at_category_view_event))
		  {
			  
			   
			   self::loadAsFirstPageEvent($mf); 
			   $OPCeventview = new OPCeventview('category', $mf,  $products, null, $config); 
			   $OPCeventview->category = $cat; 
			   OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			   
			   $output = $OPCeventview->fetchFile($mf, '_category'); 
			   if (!empty($OPCeventview->isPureJavascript))
			   $js .= $output; 
			   else
			   $html .= $output; 
			   
			   
			   
			   if (!empty($html))
			   {
			    if (empty(self::$loadLast)) self::$loadLast = array(); 
			    self::$loadLast[$mf] = $mf; 
			   }
			
		  }
		 
	 
	   }
      }
	  OPCtrackingHelper::$html .= $html; 
	  OPCtrackingHelper::$js .= $js; 
	 
  }
  
  
  public static function imprViewEvent()
  {
	  if (!self::checkOK()) return; 
     if (!class_exists('plgSystemOpctrackingsystem')) return; 
	 if (empty(plgSystemOpctrackingsystem::$_product_impressions)) return;
     $files = self::getEnabledTracking(); 
	 $html = ''; 
	 $js = '';  
	 if (!empty($files))
	  {
	  foreach ($files as $mf)
	   {
	    
		 
		 $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 if (!empty($config->run_at_impr_view_event))
		  {
			  
			   
			   self::loadAsFirstPageEvent($mf); 
			   foreach (plgSystemOpctrackingsystem::$_product_impressions as $listName => $idsI) {

			   $ids = $idsI; 
			   if (empty($ids)) continue; 
			   
			   $OPCeventview = new OPCeventview('impression', $mf,  $ids, null, $config); 
			   $OPCeventview->allowMultiple = true; 
			   $OPCeventview->listName = $listName; 
			   OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			   
			   $output = $OPCeventview->fetchFile($mf, '_impr'); 
			   if (!empty($OPCeventview->isPureJavascript))
			   $js .= $output; 
			   else
			   $html .= $output; 
			   
			   }
			  
			   
			   
			   if (!empty($html))
			   {
			    if (empty(self::$loadLast)) self::$loadLast = array(); 
			    self::$loadLast[$mf] = $mf; 
			   }
			
		  }
		 
	 
	   }
      }
	  OPCtrackingHelper::$html .= $html; 
	  OPCtrackingHelper::$js .= $js; 
	 
  }
  

  
  public static function cartViewEvent()
  {
     if (!self::checkOK()) return; 
     $files = self::getEnabledTracking(); 
	 $html = ''; 
	 $js = ''; 
	 if (!empty($files))
	  {
	  foreach ($files as $mf)
	   {
	    
		 
		 $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 if (!empty($config->run_at_cart_view_event))
		  {
			  if (!class_exists('VmConfig'))	  
				{
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				VmConfig::loadConfig(); 
				}
			  
			  if (!class_exists('VirtueMartCart'))
			  require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
			  
			   $cart = VirtuemartCart::getCart(); 
			   if (defined('VM_VERSION') && (VM_VERSION >= 3))
			   {
				    if (method_exists($cart, 'prepareCartData')) {
						ob_start(); 
				     $cart->prepareCartData(); 
					   $zz = ob_get_clean(); 
					}
			   }
			   
			   
			   if (empty($cart->products)) return; 
			   
			   self::loadAsFirstPageEvent($mf); 
			   $OPCeventview = new OPCeventview('cart', $mf,  $cart->products, $cart, $config); 
			   
			   OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			   
			   $output = $OPCeventview->fetchFile($mf, '_cart'); 
			   
			   
			   
			   if (!empty($output)) {
			    $dispatcher = JDispatcher::getInstance(); 
				$reason = "OPC Tracking Cart"; 
				$dispatcher->trigger('plgDisablePageCache', array( $reason)); 
			   
			   }
			   
			   if (!empty($OPCeventview->isPureJavascript))
			   $js .= $output; 
			   else
			   $html .= $output; 
			   
			   
			   
			   if (!empty($html))
			   {
			    if (empty(self::$loadLast)) self::$loadLast = array(); 
			    self::$loadLast[$mf] = $mf; 
			   }
			
		  }
		 
	 
	   }
      }
	  OPCtrackingHelper::$html .= $html; 
	  OPCtrackingHelper::$js .= $js; 
	 
  }
  
  public static function loadAsFirstPageEventAlways()
  {
	  if (!self::checkOK()) return; 
	  $files = self::getEnabledTracking(); 
	 $html = $js = ''; 
	 
	 if (!empty($files))
	  {
	  foreach ($files as $mf)
	  {
		 $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 if (!empty($config->run_always))
		 {
			 $OPCeventview = new OPCeventview('first', $mf,  array(), null, $config); 
			 
			 OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			 
			 $htmlR = $OPCeventview->fetchFile($mf, '_first'); 
			 
			 if (!empty($htmlR))
			 {
				 
				
			   if (!empty($OPCeventview->isPureJavascript))
			   $js .= $htmlR; 
			   else
			   $html .= $htmlR; 
				 
				 if (!empty($OPCeventview->isPureJavascript))
				 {
					
				 }
				 
				 if (!empty($htmlR))
				 {
			      if (empty(self::$loadLast)) self::$loadLast = array(); 
			      self::$loadLast[$mf] = $mf; 
				 }
			 }
			
		 }
	  }
	  OPCtrackingHelper::$html .= $html; 
	  OPCtrackingHelper::$js .= $js; 
	  }
	  
  }
  
  private static function loadAsFirstPageEvent($mf)
  {
	  
	 if (!self::checkOK()) return; 
	 $html = $js = ''; 
	 
	 
	  
	  
	  
		 $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 {
			 $OPCeventview = new OPCeventview('first', $mf,  array(), null, $config);

			 OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			 
			 $htmlR = $OPCeventview->fetchFile($mf, '_first'); 
			 
			 if (!empty($htmlR))
			 {
				 
				 if (!empty($OPCeventview->isPureJavascript))
			   $js .= $htmlR; 
			   else
			   $html .= $htmlR; 
				 

			     if (empty(self::$loadLast)) self::$loadLast = array(); 
			     self::$loadLast[$mf] = $mf; 
			 
			   OPCtrackingHelper::$html = $htmlR.OPCtrackingHelper::$html; 
			   OPCtrackingHelper::$js = $js.OPCtrackingHelper::$js; 
			 }
			
		 }
	  
	  
	  
	  
  }
  
  
  
  public static function loadAsLastPageEvent()
  {
	  if (!self::checkOK()) return; 
	  if (empty(self::$loadLast)) return; 
	   $html = $js = ''; 
	  foreach (self::$loadLast as $mf)
	  {
		    $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 {
			 $OPCeventview = new OPCeventview('last', $mf,  array(), null, $config); 
			 
			 OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			 
			 // this allows to add any info to both at the end of <head and also to the beggining of <body
			 $htmlR = $OPCeventview->fetchFile($mf, '_last_head'); 
			 
			 if (!empty($OPCeventview->isPureJavascript))
			   $js .= $htmlR; 
			   else
			   $html .= $htmlR; 
			 
			 $htmlR = $OPCeventview->fetchFile($mf, '_last'); 
			 
			 if (!empty($OPCeventview->isPureJavascript))
			   $js .= $htmlR; 
			   else
			   $html .= $htmlR; 
		

		
		 }
	  }
	  OPCtrackingHelper::$html .= $html; 
	  OPCtrackingHelper::$js .= $js; 
  }
  public static function productViewEvent($virtuemart_product_id)
  {
     if (!self::checkOK()) return; 
     $files = self::getEnabledTracking(); 
	 $html = $js = ''; 
	 
	 if (!empty($files))
	  {
	  foreach ($files as $f)
	   {
	     $mf = $f; 
		 
		 $default = new stdClass(); 
		 $config = OPCconfig::getValue('tracking_config', $mf, 0, $default); 
		 $config->key_name = $mf; 
		 if (!empty($config->enabled))
		 if (!empty($config->run_at_product_view_event))
		  {
			  
			   self::loadAsFirstPageEvent($mf); 
			   $OPCeventview = new OPCeventview('product', $mf,  array($virtuemart_product_id), null, $config); 
			   
			   OPCtrackingHelper::addCurrentGlobals($OPCeventview); 
			   
			   $htmlR = $OPCeventview->fetchFile($mf, '_product'); 
			
			if (!empty($htmlR))
			{
				
				 if (!empty($OPCeventview->isPureJavascript))
			   $js .= $htmlR; 
			   else
			   $html .= $htmlR; 
				
				
			    if (empty(self::$loadLast)) self::$loadLast = array(); 
			    self::$loadLast[$mf] = $mf; 
			}
			
		  }
		 
	 
	   }
      }
	  
	  OPCtrackingHelper::$html .= $html; 
	  OPCtrackingHelper::$js .= $js; 
	 
  }
  
  
  public static function getOrderExtras(&$order)
  {
     
	
	 if (!empty($order['items']))
     foreach ($order['items'] as $key=>$order_item)
	 {
	    if (empty($order_item->order_item_sku))
		$order['items'][$key]->order_item_sku = $order['items'][$key]->virtuemart_product_id; 
		
		
		
	 }
	 
	
	 
	 
  
  }
  
  public static function getOrderIdandStatus($data) {
	  
	  
	  if ((is_array($data)) && (isset($data['virtuemart_order_id']))) $order_id = $data['virtuemart_order_id']; 
			else
			if ((is_object($data)) && (isset($data->virtuemart_order_id))) 
				$order_id = $data->virtuemart_order_id; 
			if (empty($order_id) && (isset($data['details']['BT'])) && (isset($data['details']['BT']->virtuemart_order_id))) $order_id = $data['details']['BT']->virtuemart_order_id; 
			$order_id = (int)$order_id; 
			if (empty($order_id)) return array(); 
	
	if ((is_array($data)) && (isset($data['order_status']))) $order_status = $data['order_status']; 
			else
			if ((is_object($data)) && (isset($data->order_status))) 
				$order_status = $data->order_status; 
			if (empty($order_status) && (isset($data['details']['BT'])) && (isset($data['details']['BT']->order_status))) $order_status = $data['details']['BT']->order_status; 
			
			if (empty($order_status)) return array(); 
			
			return array('order_id' => (int)$order_id, 'order_status'=>$order_status); 
	  
	  
  }
  // will associate cookie hash with order
  public static function orderCreated($hash, &$data, $order_last_state)
  {
    $oa = self::getOrderIdandStatus($data); 
	if (empty($oa)) return; 
	$order_id = $oa['order_id']; 
	$order_status = $oa['order_status']; 
	
	
		   
		 
		   $tracking = OPCtrackingHelper::getEmptyLine(0, $order_id, $hash); 
		   if (!empty($tracking))
		   {
		   
		   
		   if ($tracking['virtuemart_order_id'] != $order_id)
		    {
			  OPCtrackingHelper::updateLine($tracking['id'], $order_id, $hash); 
			}
		   }
		   else
		   {
		     // will do insert: 
		     OPCtrackingHelper::registerCart($hash); 
			 // will update the order_id 
			 OPCtrackingHelper::updateLine(0, $order_id, $hash); 
			 
		   }
			
		
		
		
  }
  // sets cookie (general)
  public static function setCookie($hash, $timeout=0)
  {
	if (php_sapi_name() === 'cli') return; 
    if (headers_sent()) return; 
   
   
	  $app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			return;
		}
   
    if (empty($timeout)) $timeout = time()+60*60*24*30; 
	else $timeout += time(); 
	if ($timeout<0) $timeout = 1; 
	
	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
	$config =  JFactory::getConfig(); 
	 if (!OPCJ3)
	 {
	 $domain = $config->getValue('config.cookie_domain'); 
	 $path = $config->getValue('config.cookie_path'); 
	 }
	 else
	 {
	 $domain = $config->get('cookie_domain'); 
	 $path = $config->get('cookie_path'); 
	 }
	 if (empty($path)) $path = '/'; 

	 if (empty($domain))
	 @setcookie($hashn, $hash, $timeout, $path);
	 else
	 @setcookie($hashn, $hash, $timeout, $path, $domain);
	
	
  }
  
  public static function getUserHash()
   {
   return;
      if (method_exists('JApplication', 'getHash'))
	  $hashn = JApplication::getHash('opctracking'); 
	  else $hashn = JUtility::getHash('opctracking'); 
	  $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	  if (empty($opchash))
	   {
		  OPCtrackingHelper::setCookie($opchash); 
		  $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	   }
	  return $opchash; 
   }
  
  // 
  public static function getHTML()
  {
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 

	 
	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
	 
     $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	   if (empty($opchash)) return; 
	   $db = JFactory::getDBO(); 
	   if (defined('OPCJ3') && (!OPCJ3))
	   {
	   $q = "select o.order_status from #__virtuemart_plg_opctracking as t, #__virtuemart_plg_opctracking as o  where t.hash = '".$db->escape($opchash)."' and t.virtuemart_order_id = o.virtuemart_order_id limit 0,1"; 
	   }
	   else
	   {
	   $q = "select o.order_status from #__virtuemart_plg_opctracking as t, #__virtuemart_plg_opctracking as o  where t.hash = '".$db->escape($opchash)."' and t.virtuemart_order_id = o.virtuemart_order_id limit 0,1"; 
	   }
	   $db->setQuery($q); 
	   $state = $db->loadResult(); 
	   if (empty($state)) return;
	   if ($state == 'C')
	    {
		
		//$bodyp = stripos($buffer, '</body'); 

		//$buffer = substr($buffer, 0, $bodyp).$html.substr($buffer, $bodyp); 
		
		}
  }
  public static $html; 
  public static $js; 
  public static $html_array; 
  public static $config; 
  public static $current_hash; 
  public static $orderCache; 
  public static $loadLast; 
  public static $isPurchaseEvent; 
  public static $isCartEvent; 
  public static $isProductEvent; 
  
  public static function checkHTML()
  {
    if ((!empty(OPCtrackingHelper::$html)) || (!empty(OPCtrackingHelper::$js))) 
	{
	
	return true; 
	}
	
	return false; 
  }
  
  public static function getOrderHistory($order_id, $config) {
  $min_a = array(); 
	 foreach ($config as $status=>$t)
	 foreach ($t as $mkey=>$val)
	  {
	     if (stripos($mkey, 'since'))
		 if (is_numeric($val))
		  {
		    $min_a[$val] = $val; 
		  }
	  }
	  
	  
	  
	  	   $time = time()-(24*60*60*30); 
	  if (!empty($min_a))
	  {
	  $min = min($min_a); 
	  
	
	   //only take orders less than one month

	   
	   // only run tracking since it was enabled and ignore one month old data
	   if (is_numeric($min))
	   if ($min > $time) $time = $min; 
	   } 
	   
	   $date = new JDate($time);
	
	 if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s", $time); 

	 $q = 'select * from #__virtuemart_order_histories where virtuemart_order_id = '.$order_id.' and created_on > \''.$dd.'\' order by virtuemart_order_history_id desc'; 
	 $db = JFactory::getDBO(); 
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 return $res; 
  }
  
  public static function checkStatusBE() {
		  
		  
		  
		  
		  if (empty(self::$actions))
		  self::$actions = array(); 
	  
		
			$session = JFactory::getSession(); 
			$data = $session->get('opctrackingbe', array()); 
			
			
			
			
			
			if (empty($data)) return; 
			foreach ($data as $order_idK => $order_status) {
			
			$order_id = (int)$order_idK; 
			
	  
		  
		  
		 if (empty(self::$actions))
		 self::$actions = array(); 
	 
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'tracking.php'); 
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 $ret = false; 
	  $config = new JModelConfig(); 
	 $config->loadVmConfig(); 
	 //$files = $config->getPhpTrackingThemes();
	 $statuses = $config->getOrderStatuses();
     $trackingModel = new JModelTracking(); 
	 self::$config = $config = $trackingModel->getStatusConfig($statuses); 
	 
	 if (empty($config[$order_status])) continue; 
		else $lc = $config[$order_status]; 
	  
	 
	 if (!empty($config[$order_status]->code))
				  {
				  self::prepareAction($order_status, 'code', $config[$order_status]->code);   
				  }
	
		
		
		
	if (!empty($lc))		
	foreach ($lc as $key=>$file)
		   {
		     
		   
				
			  
		    
					if (stripos($key, '_enabled')!==false)
						{
							// check if we have adwords_enabled=1
							$rev = strrev($key); 
							if (stripos($rev, 'delbane_')!==0) continue; 
							// it's not set to 1
							if (empty($file)) continue; 
							
							$nf = str_replace('_enabled', '', $key); 
							
							 $default = new stdClass(); 
			$config = OPCconfig::getValue('tracking_config', $nf, 0, $default); 
			if (empty($config->run_admin)) {
				
				continue; 
			}
			
							
							self::prepareAction($order_status, $nf);   
							
						}

			 
			
			 
		   }
		   //checkActions
			$tracking = array(); 
			$tracking['virtuemart_order_id'] = $order_id; 
			$tracking['shown'] = false; 
			
			
			
			
			self::checkActions($tracking); 
			
			
			
			
			if (empty(self::$actions)) continue;
			self::purchaseEvent($tracking); 
			$ret = true; 
			

			$session = JFactory::getSession(); 
			$data = $session->get('opctrackingbe', array()); 
			
						
			unset($data[$order_id]); 
			$session->set('opctrackingbe', $data); 

		   
		   
			}  
			
			
			
			 return $ret; 
			
			
  }
  
  public static function checkStatus()
  {
  
     // ALTER TABLE  `#__virtuemart_order_histories` ADD INDEX (  `virtuemart_order_id` )
    if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
	
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'tracking.php'); 
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	 $hash = JRequest::getVar($hashn, false, 'COOKIE'); 
	 if (empty($hash)) return self::checkHTML(); 
	 
	 if (empty(OPCtrackingHelper::$html))
	 OPCtrackingHelper::$html = '';

	 OPCtrackingHelper::$current_hash = $hash; 
	 $tracking_s = OPCtrackingHelper::getLines(0, 0, $hash); 
	 
	 
	 
	 
	 jimport('joomla.filesystem.file');
	 jimport( 'joomla.utilities.date' );
	 
	 // we are in opc checkout: 
	 if (class_exists('VirtueMartViewCart') && (class_exists('op_languageHelper')))
	 {
	    //trigger cart events: 
		
	 }
	 
	 //added in 320
	  if (empty(self::$actions))
	 self::$actions = array(); 
	 
	 if (empty($tracking_s)) return self::checkHTML(); 
	 foreach ($tracking_s as $tracking)
	 {
	 //commented in 320: self::$actions = array(); 
	 $order_id = (int)$tracking['virtuemart_order_id']; 
	 
	 if (empty($order_id)) continue; 
	 
	 //
	 	   
	   
	   
	   
	   
	   
	  
	 
	
	 $config = new JModelConfig(); 
	 $config->loadVmConfig(); 
	 //$files = $config->getPhpTrackingThemes();
	 $statuses = $config->getOrderStatuses();
     $trackingModel = new JModelTracking(); 
	 self::$config = $config = $trackingModel->getStatusConfig($statuses); 
	
	 
	 $res = self::getOrderHistory($order_id, $config); 
	 
	 
	 
	 if (empty($res)) continue; // this should not happen
	

	
	 $ind = 0; 
	
	
	
	 foreach ($res as $state)
	 {
	 
	     if (empty($config[$state['order_status_code']])) continue; 
		  else $lc = $config[$state['order_status_code']]; 
		  

		 
		 if (!empty($lc->only_when))
		 {
		 
		    $newa = array_slice($res, $ind+1); 
			
			foreach ($newa as $ns)
			 {
			 
			 
			   if ($ns['order_status_code']==$lc->only_when)
			    {
				  //OK, do an action
				  if (!empty($config[$state['order_status_code']]->code))
				  self::prepareAction($state['order_status_code'], 'code', $config[$state['order_status_code']]->code);   
				  foreach ($lc as $key=>$file)
					{
						// inbuilt limitations
						/*
						if ($file == 'only_when') continue; 
						if ($file == 'code') continue; 
						 if (stripos($file, 'since')===0) continue; 
						$file = JFile::makeSafe($file);
						 if (empty($lc->{$key.'_enabled'})) continue; 
						if (empty($lc->$file)) continue; 
						*/
						
						//self::prepareAction($state['order_status_code'], $file); 
						
				       	if (stripos($key, '_enabled')!==false)
						{
							// check if we have adwords_enabled=1
							$rev = strrev($key); 
							if (stripos($rev, 'delbane_')!==0) continue; 
							// it's not set to 1
							if (empty($file)) continue; 
							
							$nf = str_replace('_enabled', '', $key); 
							self::prepareAction($state['order_status_code'], $nf);   
							
						}


						
					}
				
				
				
				}
			 }
		 }
		 else
		 {
			if (!empty($config[$state['order_status_code']]->code))
				  {
				  self::prepareAction($state['order_status_code'], 'code', $config[$state['order_status_code']]->code);   
				  }

		   // only when is not set: 
		   $cx = 0; 
		   
		   foreach ($lc as $key=>$file)
		   {
		     
		     $cx++; 
			 /*
		     // inbuilt limitations
			 if (stripos($file, 'since')===0) continue; 
		     if ($file == 'only_when') continue; 
			 if ($file == 'code') continue; 
			 if (empty($lc->{$key.'_enabled'})) continue; 
			*/
			  
		    
					if (stripos($key, '_enabled')!==false)
						{
							// check if we have adwords_enabled=1
							$rev = strrev($key); 
							if (stripos($rev, 'delbane_')!==0) continue; 
							// it's not set to 1
							if (empty($file)) continue; 
							
							$nf = str_replace('_enabled', '', $key); 
							self::prepareAction($state['order_status_code'], $nf);   
							
						}

			 
			
			 
		   }
		   
		 }
	
	
	
			 
			   
			 
		 
		 $ind++; 
		  
	 }
	 
	 self::checkActions($tracking); 
	  
	 self::purchaseEvent($tracking); 
	 
	 
	 
	  }
	 
	 
	 return self::checkHTML(); 
  }
  
  
  public static function getFileName($lc)
  {
   
  }
  // will check if they were already shown to the users
  public static function checkActions($tracking)
  {
    
	
	
    if (empty(self::$actions)) return; 
	
	
    $shown = $tracking['shown']; 
	if (!empty($shown))
	 {
	   $so = @json_decode($shown, true); 
	 }
	
	if (empty($so)) return; 
	
	// obj to array: 
	$so2 = array(); 
	foreach ($so as $key=>$val)
	{
	  $so2[$key] = $val; 
	}
	
    
	
    foreach (self::$actions as $status=>$data)
	 if (!empty($so2[$status]))
	 foreach ($data as $name=>$extra)
	 {
	   
	    
		  // do not perform the action once it was done
		  
		  {
		  if (!empty($so2[$status][$name])) {
		   $sostat = (int)$so2[$status][$name]; 
		   if ($sostat === 2)
		   {
		   unset(self::$actions[$status][$name]);
		   
		   }
		   
		    }
		   }
		
	 }
	 
	 foreach (self::$actions as $status=>$data)
	 if (empty($data)) unset(self::$actions[$status]); 
	
	
  
  }
  
  public static function getReplaceVars($html, $tracking)
  {
    
	$order_id = $tracking['virtuemart_order_id']; 
	if (empty($order_id)) return ''; 
	$array = array(); $order = new stdClass(); 
	self::getOrderVars($order_id, $array, $order); 
	foreach ($array as $key => $val)
	 {
	   if (is_string($val))
	   {
	   $html = str_replace('{'.$key.'}', $val, $html); 
	   }
	   if (is_array($val)) {
	    foreach ($val as $k=>$v)
		{
			if (is_string($v))
			{
				$html = str_replace('{'.$key.'_'.$k.'}', $v, $html); 
			}
		}
	   }
	 }
	 return $html; 
	
	
  }
  
  
  //this is purchase event , purchaseEvent
  private static function purchaseEvent($tracking)
  {
    
	if (!self::checkOK()) return; 
	if (empty(self::$actions)) return;
	 
	//debug: 
	
	
	
    foreach (self::$actions as $status=>$data)
	{
	 if (empty($data)) continue; 
	 foreach ($data as $name=>$extra)
	   {
	     
		 if (ctype_digit($name)) continue; 
		 
		 
		 
		 
		$config = new stdClass(); 
	    $pConfig = OPCconfig::getValue('tracking_config', $name, 0, $config); 	
		
		if (!empty($pConfig->advanced)) {
			$pConfig->advanced = (array)$pConfig->advanced; 
			if (!empty($pConfig->advanced)) {
				
			   if (!empty($pConfig->advanced[$status])) {
				   
				   
				   
				   $pConfig->advanced[$status] = (object)$pConfig->advanced[$status]; 
				   $order_id = (int)$tracking['virtuemart_order_id'];
				   if (!empty($pConfig->advanced[$status]->payment_id)) {
				      $order = self::getOrderCached($order_id); 
					  
					  
					  
					  
					  if (!empty($order['details']['BT']->virtuemart_paymentmethod_id)) {
						  
						  $order['details']['BT']->virtuemart_paymentmethod_id = (int)$order['details']['BT']->virtuemart_paymentmethod_id; 
						  $pConfig->advanced[$status]->payment_id = (int)$pConfig->advanced[$status]->payment_id; 
						  if ($pConfig->advanced[$status]->payment_id !== $order['details']['BT']->virtuemart_paymentmethod_id) {
						     
							 OPCtrackingHelper::ping($name, OPCtrackingHelper::$current_hash, (int)$order_id, $status, 2); 
							 
							 continue; 
						  }
						  
					  }
				   }
				   if (!empty($pConfig->advanced[$status]->language)) {
				      $order = self::getOrderCached($order_id); 
					  if (!empty($order['details']['BT']->virtuemart_paymentmethod_id)) {
						  
						 
						  if ($pConfig->advanced[$status]->language !== $order['details']['BT']->order_language) {
						     
							 OPCtrackingHelper::ping($name, OPCtrackingHelper::$current_hash, (int)$order_id, $status, 2); 
							 
							 continue; 
						  }
						  
					  }
				   }
				   
				   
			   }
			}
		}
			
		
		
	     if ($name !== 'code')
		  {
			
			
			
		    $html = self::getFileRendered($tracking, $name, $status); 
			
			if (empty($data->run_ajax)) {
				
			 OPCtrackingHelper::ping($name, OPCtrackingHelper::$current_hash, (int)$tracking['virtuemart_order_id'], $status, 2); 
			
			}
			
	        if (!empty($html))
			{
			OPCtrackingHelper::loadAsFirstPageEvent($name); 
			if (empty(OPCtrackingHelper::$loadLast)) OPCtrackingHelper::$loadLast = array(); 
			OPCtrackingHelper::$loadLast[$name] = $name; 
		
		
		
			OPCtrackingHelper::$html .= $html; 
			
			
			if (!empty($output)) {
			    $dispatcher = JDispatcher::getInstance(); 
				$reason = "OPC Tracking Purchase"; 
				$dispatcher->trigger('plgDisablePageCache', array( $reason)); 
			   
			   }
			
			}
			
		  }
		  else
		  {
		    
			
			
			
		    $html = ''; 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$default = new stdClass(); 
			$params = OPCconfig::getValue('tracking_config', $name, 0, $default); 
			
			$order_id = $tracking['virtuemart_order_id']; 
			$trackingrenderer = new OPCtrackingview($order_id, 'check_js', $status, $params, $name); 
			$app = JFactory::getApplication();
			if ($app->getName() === 'administrator') {
			   $trackingrenderer->allowMultiple = true; 	
			}
			
			
			
		
			if ($trackingrenderer->error) continue;
			$trackingrenderer->params = $params; 
		    $trackingrenderer->pingData .= '&file='.str_replace('&', '&amp;', $name); 
			$html = ''; 
			$html .= $trackingrenderer->fetchFile('check_js'); 
			$html .= self::getReplaceVars($data['code'], $tracking); 
			$trackingrenderer->pingData .= '&end=2'; 
			$html .= $trackingrenderer->fetchFile('check_js'); 
			OPCtrackingHelper::$html .= $html; 
			
			if (!empty($html)) {
			if (!empty($output)) {
			    $dispatcher = JDispatcher::getInstance(); 
				$reason = "OPC Tracking Custom"; 
				$dispatcher->trigger('plgDisablePageCache', array( $reason)); 
			   
			   }
			}
			
		  }
	     
	   }
	  }
	   
	  // unset cache: 
	  //does not work: unset(OPCtrackingHelper::$orderCache); 
	   
  }
  public static function emptyOrder() {
    $order = array(); 
	$order['details'] = array(); 
	$order['details']['BT'] = $order['details']['ST'] = new stdClass(); 
	$order['items'] = array(); 
	
	return $order; 
  }
  public static function getOrderCached($order_id) {
     static $cache; 
	 if (isset($cache[$order_id])) return $cache[$order_id]; 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
     $modelOrder = OPCmini::getModel('orders');
	 $order_id = (int)$order_id; 
	 if (empty($order_id)) return; 
	
	
	 $order = $modelOrder->getOrder($order_id);
	 if (empty($order)) $order = self::emptyOrder(); 
	 OPCmini::toObject($order, 5); 
	
	
	
	 $cache[$order_id] = $order; 
	 return $cache[$order_id];
  }
  
    // returns 
  public static function getOrderVars($order_id=0, &$order_array, &$order_object, $show=false, &$named_obj=array(), $order_ind=0)
  {
     $db = JFactory::getDBO(); 
    if (empty($order_id))
	{
	 $app = JFactory::getApplication(); 
	 // do not allow random order for FE
	 if (!$app->isAdmin()) return; 
	 $order_id = JRequest::getInt('order_id', 0); 
	 if (empty($order_id))
	 {
     
      $q = 'select virtuemart_order_id from #__virtuemart_orders where 1 order by rand() limit 0,1';
	  $db->setQuery($q); 
	  $order_id = $db->loadResult($q); 
	 }
	}
    //require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
    $modelOrder = OPCmini::getModel('orders');
	$order_id = (int)$order_id; 
	if (empty($order_id)) return; 
	
	
	//$order = $modelOrder->getOrder($order_id);
	$order = self::getOrderCached($order_id); 
	
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	if (OPCmini::tableExists('virtuemart_invoices')) 
	if (empty($order['invoice']))
	{
	//
	$q = 'SELECT * FROM `#__virtuemart_invoices` WHERE `virtuemart_order_id` = '.$order_id.' limit 0,1';
	 $db->setQuery($q); 

  $invoice_data = $db->loadAssoc();
   if (empty($invoice_data)) {
     $invoice_data = array(); 
   }   
  $order['invoice'] = $invoice_data; 
  
    }
	
	
	
	if (isset($order['details']['']))
	{
	$order['details']['BT'] = $order['details']['']; 
	$order['details']['ST'] = $order['details']['BT']; 
	}
	
	if (empty($order['details']))
	{
		$order['details']['BT'] = $order['details']['ST'] = new stdClass(); 
		return;
		
	}
	if (empty($order['details']['ST']))
	{
		$order['details']['ST'] =& $order['details']['BT']; 
	}
	
	$order['details']['BT']->order_discount = floatval($order['details']['BT']->order_discount); 
	
	$order['details']['BT']->order_billDiscountAmount  = floatval($order['details']['BT']->order_billDiscountAmount ); 
	
	$order['details']['BT']->order_discountAmount = floatval($order['details']['BT']->order_discountAmount); 
	
	if ((empty($order['details']['BT']->order_billDiscountAmount )) && (!empty($order['details']['BT']->coupon_discount)))
	{
		
		
		$order['details']['BT']->order_billDiscountAmount  = $order['details']['ST']->order_billDiscountAmount  = $order['details']['BT']->coupon_discount; 
		
		if (empty($order['details']['BT']->order_discount))
		{
			$order['details']['BT']->order_discount = $order['details']['ST']->order_discount = $order['details']['BT']->coupon_discount; 
		}
		
		if (empty($order['details']['BT']->order_discountAmount ))
		{
			$order['details']['BT']->order_discountAmount  = $order['details']['ST']->order_discountAmount  = $order['details']['BT']->coupon_discount; 
		}
		
	}
	
	
	
	//if (empty($order['details']['ST']))
	//	$order['details']['ST'] = $order['details']['BT']; 
	
	
	
	
	OPCtrackingHelper::getTextFields($order); 
	
	
	
	$order['details']['BT']->coupon_discount=floatval($order['details']['BT']->coupon_discount); 
	$order['details']['BT']->order_tax=floatval($order['details']['BT']->order_tax); 
	
	if (empty($order['details']['BT']->coupon_discount) && (!empty($order['details']['BT']->order_billDiscountAmount)))
	$order['details']['BT']->coupon_discount = $order['details']['BT']->order_billDiscountAmount; 

if ((empty($order['details']['BT']->order_tax) && (!empty($order['details']['BT']->order_billTaxAmount))) || ($order['details']['BT']->order_tax < $order['details']['BT']->order_billTaxAmount))
{
	$order['details']['BT']->order_tax = $order['details']['BT']->order_billTaxAmount; 
}
	$ret = array(); 
	$named_obj = array(); 
	if (!empty($order['details']['BT']))
	foreach ($order['details']['BT'] as $key=>$val)
	 {
	   if (empty($val)) $val = ''; 
	   $ret['bt_'.$key] = $val; 
	   //$ret['bt'.'_'.$key.'_'.$order_ind] = $val; 
	   
	   $k2 = str_replace('virtuemart_', '', $key); 
	   if ($k2 != $key)
	   {
		   $ret[$k2] = $val; 
	   }
	   
	   $k3 = str_replace('shipment', 'shipping', $k2); 
	   if ($k2 != $k3)
	   {
		   $ret[$k3] = $val; 
	   }
	   
	   //BT will get copied directly as well: 
	   $ret[$key] = $val; 
	   
	   $named_obj['bt_'.$key] = '$order[\'details\'][\'BT\']->'.$key; 
	   if ($show)
	    {
		   echo '$order[\'details\'][\'BT\']->'.$key.' = "'.$val."\";<br />\n"; 
		}
		
		
		
	 }
	
	if (!empty($order['details']['ST']))
	foreach ($order['details']['ST'] as $key=>$val)
	 {
	   
	   

	   if (empty($val)) $val = ''; 
	   $ret['st_'.$key] = $val; 
	   $ret['st'.'_'.$key] = $val; 
	   
	   
	   
	   $named_obj['st_'.$key] = '$order[\'details\'][\'ST\']->'.$key; 
	   
	    if ($show)
	    {
		   echo '$order[\'details\'][\'ST\']->'.$key.' = "'.$val."\";<br />\n"; 
		}
		
		
	   
	 }
	$i =0; 
	
	
	
	
	foreach ($order['history'] as $key=>$val)
	{
	  foreach ($val as $key2=>$val2)
	   {
	     if (empty($val2)) $val2 = ''; 
	     $ret['history_'.$key2.'_'.$key] = $val2;
		 
		 if (($key2 == 'created_on') && (empty($key)))
		 {
		 if (!isset($ret['date_added_0']))
		 {
		   // payment date of the latest update: 
		   $ret['date_added_0'] = $val2; 
		 }
		 
		 if ((empty($order['details']['BT']->created_on)) ||($order['details']['BT']->created_on === '0000-00-00 00:00:00'))
		 {
			 $order['details']['BT']->created_on = $val2;
			 $ret['bt_created_on'] = $val2; 
			 $ret['created_on'] = $val2; 
			 $ret['st_created_on'] = $val2; 
		 }
		 }
		 $ret[$key2.'_'.$key] = $val2; 
		 $named_obj['history_'.$key.'_'.$key2] = '$order[\'history\'][\''.$key.'\']->'.$key2;
		if ($show)
	    {
		   echo '$order[\'history\']['.$key.']->'.$key2.' = "'.$val2."\";<br />\n"; 
		}

		 
	   }
	}
	
	
	if (!empty($order['items']))
	foreach ($order['items'] as $key=>$val)
	foreach ($val as $key2=>$val2)
	{
		
	  if (is_array($val2))
	  {
		  foreach ($val2 as $k3=>$v3)
		  {
			  if (is_object($v3))
			  {
				  foreach ($v3 as $k4=>$v4)
				  {
	  $ret['items_'.$key.'_'.$key2.'_'.$k3.'_'.$k4] = $v4; 
	  
	  $named_obj['items_'.$key.'_'.$key2.'_'.$k3.'_'.$k4] = '$order[\'items\'][\''.$key.'\'][\''.$key2.'\'][\''.$k3.'\']->'.$k4; 
					  
				  }
				  continue; 
			  }
	  $ret['items_'.$key.'_'.$key2.'_'.$k3] = $v3; 
	  
	  if (!is_array($v3))
	  $named_obj['items_'.$key.'_'.$key2.'_'.$k3] = '$order[\'items\'][\''.$key.'\'][\''.$key2.'\'][\''.$k3.'\']->'.$v3; 
			  
		  }
		  continue; 
	  }
	
	  $ret['items_'.$key.'_'.$key2] = $val2; 
	  $ret[$key2.'_'.$key] = $val2; 
	  $named_obj['items_'.$key.'_'.$key2] = '$order[\'items\'][\''.$key.'\']->'.$key2; 
	  
	  	if ($show)
	    {
		   echo '$order[\'items\']['.$key.']->'.$key2.' = "'.$val2."\";<br />\n"; 
		}
		
	  
		
	 
	}
    
	$vendor_id = $order['details']['BT']->virtuemart_vendor_id;  
	if (empty($vendor_id)) $vendor_id = 1; 
	
    $vendor_data = self::getVendorInfo($vendor_id); 
	$order['vendor'] = new stdClass(); 
	foreach ($vendor_data as $key2=>$val2)
	{
	  $ret['vendor_'.$key2] = $val2; 
	  
	  $order['vendor']->$key2 = $val2; 
	  $named_obj['vendor_'.$key2] = '$order[\'vendor\']->'.$key2; 
	  
	  	if ($show)
	    {
		   echo '$order[\'vendor\']->'.$key2.' = "'.$val2."\";<br />\n"; 
		}
	}
	
	 $order_array = $ret; 
	 $order_object = $order; 
	 return $order; 
  }

  public static function pingstatus() {
	  
	  $data = JRequest::getVar('orderdata', array()); 
	  $db = JFactory::getDBO(); 
	  
	  @header('Content-Type: text/html; charset=utf-8');
	  @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	  @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	  
	  $change_found = false; 
	  
	  foreach ($data as $virtuemart_order_id=>$val) {
		  $statuses = array(); 
		  $stamps = array(); 
		  $virtuemart_order_id = (int)$virtuemart_order_id; 
		  if (empty($virtuemart_order_id)) continue; 
		  
		  foreach ($val as $order_status => $stamp) {
				$order_status = strtoupper($order_status); 
				if (strlen($order_status)>1) continue; 
				$phpdate = strtotime( $stamp );
				if (empty($phpdate)) continue; 
				
				$statuses[$order_status] = "'".$db->escape($order_status)."'"; 
				$stamps[$stamp] = " (`modified_on` > '".$db->escape($stamp)."' and created_on > '".$db->escape($stamp)."') ";
		  }
		  if (!empty($statuses)) {
		    //do we have any additional statuses set here after the thank you page (IPN): 
		    $q = 'select `order_status_code` from `#__virtuemart_order_histories` where `virtuemart_order_id` = '.(int)$virtuemart_order_id." and (".implode(' AND ', $stamps).") and `order_status_code` NOT IN (".implode(',', $statuses).") order by `modified_on`, `created_on` desc limit 1"; 
		    $db->setQuery($q); 
		    $res = $db->loadResult(); 
			
		    if (!empty($res)) {
				echo '<div style="display:none;">opc_order_status_changed</div>'; 
				$change_found = true; 
				break; 
			}
		  }
	  }
	  if (empty($change_found)) {
	   echo '<div style="display:none;">opc_no_changes_found</div>'; 
	  }
	  JFactory::getApplication()->close(); 
	  
	  
  }
  
  public static function ping($file='', $hash='', $order_id='', $order_status='', $end=0)
  {
  
	 if (empty($file))
	 {
	 $terminate = true; 
     $file = JRequest::getVar('file', ''); 
	 jimport('joomla.filesystem.file');
	 $file = JFile::makeSafe($file);
	 $hash = JRequest::getVar('hash', ''); 
	 $order_id = (int)JRequest::getVar('order_id', 0);
	 
	 
	 $order_status = JRequest::getVar('order_status', ' '); 
	 
	 $end = JRequest::getVar('end', 1); 
	 }
	 else
	 {
	   $terminate = false; 
	 }
	 $res = OPCtrackingHelper::getLine(0, $order_id, $hash); 
	 if (!empty($res['shown']))
	  {
	    
	    $data = @json_decode($res['shown'], true); 
		if (empty($data)) $data = array(); 
		if (empty($data[$order_status])) $data[$order_status] = array(); 
		
		if (!empty($data[$order_status][$file]))
		if ((int)$data[$order_status][$file] >= (int)$end) 
		{
		  if (!empty($terminate))
		  {
				$app  = JFactory::getApplication(); 
				$app->close(); 
				
		  }
		  else		  
		  {
		    return;
		  }
		
		}
		
		$data[$order_status][$file] = $end; 
		$new = json_encode($data); 
		
	  }
	 else
	  {
	    $newa = array(); 
		$newa[$order_status] = array();
		$newa[$order_status][$file] = $end; 
		
		$new = json_encode($newa); 
	  }
	  
	 OPCtrackingHelper::updateLine($res['id'], $res['virtuemart_order_id'], $hash, $new); 
	 if (!empty($terminate))
	 {
	$app  = JFactory::getApplication(); 
    $app->close(); 
    
	 }
	 
  }
  public static function getEnabledTracking()
	{
	   $ret = array(); 
	   static $cache; 
	   if (!empty($cache)) return $cache; 
	   
	   $db = JFactory::getDBO(); 
	   $q = "select config_subname from #__onepage_config where config_name = 'tracking_config' and config_ref = 1"; 
	   try
	   {
	   $db->setQuery($q); 
	   $res = $db->loadAssocList();
	   
	   
	   }
	   catch (Exception $e)
	    {
		 
		  $cache = $ret; 
		  return $ret; 
		}
	   if (!empty($res))
	    {
		  jimport('joomla.filesystem.folder');
		  foreach($res as $k=>$f)
		    {
			   $fn = JFile::makeSafe($f['config_subname']); 
			   
			   
			   if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$fn.'.php'))
			     {
				    $ret[$fn] = $fn; 
				 }
			}
		}
		
		
		$cache = $ret; 
		return $ret; 
	   
	   
	}  
  
  public static function getCheckJs($tracking, $file, $status, $overridename='')
  {
  
  }
  
  //purchase event redering, purchaseEvent:  
  public static function getFileRendered($tracking, $file, $status, $overridename='', $type='purchase')
   {
     
     require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 $default = new stdClass(); 
     $data = OPCconfig::getValue('tracking_config', $file, 0, $default); 
	 $order_id = $tracking['virtuemart_order_id']; 
	 $trackingrenderer = new OPCtrackingview($order_id, $file, $status, $data, $file); 
	 
	 $app = JFactory::getApplication();
			if ($app->getName() === 'administrator') {
			   $trackingrenderer->allowMultiple = true; 	
			}
	 
	  
	 // last check if we should get this executed: 
	 if ($type == 'purchase')
	 if (!empty($data->run_just_once))
	 {
	 
	    $res = OPCtrackingHelper::getLine(0, $order_id, OPCtrackingHelper::$current_hash); 
		if (!empty($res['shown']))
		 {
		    $mdata = @json_decode($res['shown'], true); 
		    foreach ($mdata as $status2=>$d)
			  {
			      if (isset($d[$file]))
				   {
				     // we aleready ran negative status order
					 if (is_array($trackingrenderer->negative_statuses))
				     if (in_array($status, $trackingrenderer->negative_statuses))
					   {
					 
					      return ''; 
					   }
					  else
					   {
					   
					     // we already ran positive status order
						 return ''; 
					   }
				   
				   }
			  }
		 }
		
		
	 }

	 
	
	 
	 if (!empty($trackingrenderer->errorMsg)) 
	 {
	   
	 }
	 
	 		

	 
	 if ($trackingrenderer->error) return '';
	 $trackingrenderer->params = $data; 
	 
	 
	 if ($type === 'purchase')
	 if (!empty($data->run_ajax))
	 {
	  if (!empty($overridename))
	  $trackingrenderer->pingData .= '&file='.str_replace('&', '&amp;', $overridename); 
	  else
	  $trackingrenderer->pingData .= '&file='.str_replace('&', '&amp;', $file); 
	 }
	 else
	 {
	 
	 }
	 
	 $html = ''; 
	 
	 if ($type === 'purchase')
	 if (!empty($data->run_ajax))
	 if ($file != 'check_js')
	 $html .= $trackingrenderer->fetchFile('check_js'); 
	 
	 
	 
	 $html .= $trackingrenderer->fetchFile($file); 
	 
	

	
	
	
	 if ($type === 'purchase')
	 {
	 if (!empty($data->run_ajax))
	 {
	   
	   $trackingrenderer->pingData .= '&end=2'; 
	 }
	 else
	 {
	    OPCtrackingHelper::ping($file, OPCtrackingHelper::$current_hash, $order_id, $status, 2); 
	 }
	 
	 
	 if (!empty($data->run_ajax))
	 $html .= $trackingrenderer->fetchFile('check_js'); 
	 }
	 return $html; 
	 
   }
   
  static $actions; 
  public static function prepareAction($state, $what, $extra='')
  {


  
    if (empty($what)) return; 
	if (ctype_digit($what)) return; 


    if (empty(self::$actions)) self::$actions = array(); 
	
	
	
	if (empty(self::$actions[$state])) self::$actions[$state] = array(); 

	  


	self::$actions[$state][$what] = $extra ; 
	
 	
	
	
  }
  
  public static function getEmptyLine($id=0, $order_id=0, $hash=0)
  {
    
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 

	
    if (empty($id))
	{
     $db = JFactory::getDBO(); 
	 if (defined('OPCJ3') && (!OPCJ3))
	 {
     $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc limit 0,1"; 
	 }
	 else
	 {
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc limit 0,1"; 
	 }
	 $db->setQuery($q); 
	 return $db->loadAssoc(); 
	}
  }
  
 public static function getLines($id=0, $order_id=0, $hash=0)
  {
  
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
   if (!OPCmini::tableExists('virtuemart_plg_opctracking')) return; 
  
     if ((empty($id) && (!empty($hash))))
	{
     $db = JFactory::getDBO(); 
	 if (defined('OPCJ3') && (!OPCJ3))
     $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' "; 
	 else
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' "; 
	 
	 if (!empty($order_id))
	 $q .= ' and virtuemart_order_id = '.(int)$order_id.' limit 0,1'; 
	 else
	 $q .= ' order by virtuemart_order_id desc limit 0,2'; 
	 
	 $db->setQuery($q); 
	 $ret = $db->loadAssocList(); 
	 
	 return $ret; 
	}
	elseif (!empty($id))
	{
      $db = JFactory::getDBO(); 
      $q = "select * from #__virtuemart_plg_opctracking where id='".(int)$id."' limit 0,1"; 
	  $db->setQuery($q); 
	  return $db->loadAssocList(); 
	}
	if (!empty($order_id))
	{
      $db = JFactory::getDBO(); 
      $q = "select * from #__virtuemart_plg_opctracking where virtuemart_order_id='".(int)$order_id."' limit 0,5"; 
	  $db->setQuery($q); 
	  return $db->loadAssocList(); 
	}
	return array(); 
  }
 public static  function getLine($id=0, $order_id=0, $hash=0)
  {
    if (empty($id))
	{
     $db = JFactory::getDBO(); 
     if (defined('OPCJ3') && (!OPCJ3))
	 {
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc"; 
	 }
	 else
	 {
	 $q = "select * from #__virtuemart_plg_opctracking where hash='".$db->escape($hash)."' and (virtuemart_order_id = ".(int)$order_id." or virtuemart_order_id = 0) order by virtuemart_order_id desc"; 
	 }
	 //if (!empty($order_id))
	 //$q .= ' and virtuemart_order_id = '.(int)$order_id.' '; 
	 $q .= " limit 0,1"; 
	 $db->setQuery($q); 
	 return $db->loadAssoc(); 
	}
	else
	{
      $db = JFactory::getDBO(); 
      $q = "select * from #__virtuemart_plg_opctracking where id='".(int)$id."' limit 0,1"; 
	  $db->setQuery($q); 
	  return $db->loadAssoc(); 
	}
	if (!empty($order_id))
	{
      $db = JFactory::getDBO(); 
	  if (defined('OPCJ3') && (!OPCJ3))
	  {
      $q = "select * from #__virtuemart_plg_opctracking where virtuemart_order_id='".(int)$order_id."' and hash='".$db->escape($hash)."' limit 0,1"; 
	  }
	  else
	  {
	  $q = "select * from #__virtuemart_plg_opctracking where virtuemart_order_id='".(int)$order_id."' and hash='".$db->escape($hash)."' limit 0,1"; 
	  }
	  $db->setQuery($q); 
	  return $db->loadAssoc(); 
	}
	return false; 
	
  }
  
  public static function updateLine($id=0, $order_id=0, $hash=0, $shown='')
   {
      $db = JFactory::getDBO(); 
	  
	   jimport( 'joomla.utilities.date' );
	   $date = new JDate('now');
	   if (method_exists($date, 'toSql'))
	   $dd = $date->toSql();
	   else $dd = date("Y-m-d H:i:s"); 

	   
	  $user = JFactory::getUser(); 
	  $user_id = (int)$user->get('id', 0); 

	  
	  if (empty($id))
	  {
	  if (defined('OPCJ3') && (!OPCJ3))
	  {
	  $q2 = "select id from #__virtuemart_plg_opctracking where (virtuemart_order_id = '".(int)$order_id."' or virtuemart_order_id = 0) and hash = '".$db->escape($hash)."' order by virtuemart_order_id desc limit 0,1"; 
	  }
	  else
	  {
	  $q2 = "select id from #__virtuemart_plg_opctracking where (virtuemart_order_id = '".(int)$order_id."' or virtuemart_order_id = 0) and hash = '".$db->escape($hash)."' order by virtuemart_order_id desc limit 0,1"; 
	  }
	  $db->setQuery($q2);
	  $id = $db->loadResult(); 
	  //echo '...id:'.$id;
	  
	  }
	  //else { $q=$z; echo $id.'....:'; }
	
	  
	  if (empty($id))
	   {
	   if (defined('OPCJ3') && (!OPCJ3))
	   {
        $q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', shown='".$db->escape($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where hash = '".$db->escape($hash)."' ";  	  
		}
		else
		{
		$q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', shown='".$db->escape($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where hash = '".$db->escape($hash)."' ";  	  
		}
	    $db->setQuery($q); 
	    $db->query(); 
		
	   }
	   else
	   {
	     if (defined('OPCJ3') && (!OPCJ3))
		 {
		  $q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', hash = '".$db->escape($hash)."', shown='".$db->escape($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where id = ".(int)$id." ";  	     
		  }
		  else
		  {
		  $q = "update #__virtuemart_plg_opctracking set virtuemart_order_id = '".(int)$order_id."', hash = '".$db->escape($hash)."', shown='".$db->escape($shown)."', modified='".$dd."', modified_by='".(int)$user_id."' where id = ".(int)$id." ";  	     
		  }
		  $db->setQuery($q); 
	      $db->query(); 

	   }
	  
		
   }
   
  public static function getVendorInfo($vendorid)
  {
   static $c; 
   if (isset($c[$vendorid])) return $c[$vendorid];    
	 
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
   if (!OPCmini::tableExists('virtuemart_userinfos')) return array(); 
   if (empty($vendorid)) $vendorid = 1; 
   

   $dbj = JFactory::getDBO(); 

   $q = "SELECT * FROM `#__virtuemart_userinfos` as ui, #__virtuemart_vmusers as uu WHERE ui.virtuemart_user_id = uu.virtuemart_user_id and uu.virtuemart_vendor_id = '".(int)$vendorid."' and uu.user_is_vendor = 1 limit 0,1";
   $dbj->setQuery($q);
	
    $vendorinfo = $dbj->loadAssoc();
	
	if (empty($vendorinfo)) $vendorinfo = array(); 
	if (empty($vendorinfo['company'])) $vendorinfo['company'] = $_SERVER['SERVER_NAME']; 
	if (empty($vendorinfo['virtuemart_user_id'])) return $vendorinfo; 
	
	$q = 'select name, email  from #__users where id = '.(int)$vendorinfo['virtuemart_user_id']; 
	   $dbj->setQuery($q);
	
    $vendorinfo2 = $dbj->loadAssoc();
	if (!empty($vendorinfo2))
	foreach ($vendorinfo2 as $k=>$v) {
		$vendorinfo[$k] = $v; 
	}
	
	if (!empty($vendorinfo))
	{
	if (!isset($vendorinfo['company'])) {
		$config =  JFactory::getConfig(); 
	   if (!OPCJ3) {
	     $vendorinfo['company'] = JFactory::getConfig()->getValue('config.sitename'); 
	    }
		else
		{
			$vendorinfo['company'] = JFactory::getConfig()->get('sitename'); 
		}
	}
	OPCmini::toObject($vendorinfo); 
	
	}


	$c[$vendorid] = $vendorinfo; 
	return $vendorinfo;  
  }
  
   public static function getOrderLang(&$order)
   {
   // setes default language: 
	$db = JFactory::getDBO(); 
	$lang = JFactory::getLanguage(); 
	$lang = $lang->getDefault(); 
	
	$jlang = $lang; 
	
	
   if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }

	
	if (!empty($order['details']['BT']))
	if (!empty($order['details']['BT']->order_language))
	 {
	   $lang = $order['details']['BT']->order_language; 
	 }
	 else
	 {
	   $langs = VmConfig::get('active_languages', array($lang)); 
	   
	   $user_id = $order['details']['BT']->virtuemart_user_id; 
	   if (!empty($user_id))
	   {
	     $q = 'select params from #__users where id = '.(int)$user_id.' limit 0,1'; 
		 $db->setQuery($q); 
		 $lang_json = $db->loadResult(); 
		 $testa = @json_decode($lang_json, true); 
		 if (isset($testa['language']))
		 {
		  $test2 = $testa['language']; 
		  
		  if (in_array($test2, $langs))
		  $user_lang = $test2; 
		  
		 }
	   }
	   
	   if (isset($user_lang))
	   $lang = $user_lang; 
	   else
	   if (in_array($jlang, $langs))
	   {
	     // joomla's default language: 
	     $lang = $jlang; 
	   }
	   else
	   {
	   // take first
	   foreach ($langs as $lang2)
	    {
		  // first VM language found: 
		  $lang = $lang2; 
		  break; 
		}
	   }
	 } 
	 
	$vmlang = strtolower($lang); 
	$vmlang = str_replace('-', '_', $vmlang); 
    
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
	
	if (defined('VMLANG'))
	{
	$vmlang_c = VMLANG; 
    if (empty($vmlang) && (!empty($vmlang_c))) $vmlang = VMLANG; 
	}
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	if (!OPCmini::tableExists('virtuemart_categories_'.$vmlang))
	{
	   // failsafe lang: 
	   if (OPCmini::tableExists('virtuemart_categories_en_gb'))
	    {
		   $vmlang = 'en_gb'; 
		}
	}

	return $vmlang; 
   }
   
   
   public static function getCurInfo($currency)
   {
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	   return OPCmini::getCurInfo($currency); 
   }
   
   public static function getTextFields(&$order)
  {
    
	OPCtrackingHelper::getOrderExtras($order); 
	
     $db = JFactory::getDBO(); 
	$vmlang = self::getOrderLang($order); 
	
	if (empty($order['details']['BT'])) return;
	
	$currency = (int)$order['details']['BT']->order_currency; 
	
	$user_currency = (int)$order['details']['BT']->user_currency_id; 
	
	$order['details']['BT']->virtuemart_order_id = $order['details']['BT']->virtuemart_order_id;
	
	if (empty($order['details']['BT']->virtuemart_order_id)) return; 
	
	if (empty($currency)) $currency = $user_currency; 
	
	
	
	if (!empty($currency))
	{
	 
	   $res = self::getCurInfo($currency); 
	   
	   
	   foreach ($res as $key5=>$val5)
	   {
	    $order['details']['BT']->$key5 = $val5; 
	   }
	}
	
	if (!empty($user_currency))
	{
	 
	   $res = self::getCurInfo($user_currency); 
	   foreach ($res as $key5=>$val5)
	   {
		  $ckey = 'user_'.$key5; 
	    $order['details']['BT']->$ckey = $val5; 
	   }
	}
	
	
   if (!empty($order['details']['BT']->user_currency_id))
   {
	   $user_currency = $order['details']['BT']->user_currency_id; 
	   $user_exange_rate = (float)$order['details']['BT']->user_currency_rate; 
   }
   else
   {
	   $user_currency = $currency; 
	   $user_exange_rate = 1; 
   }
  
  
  
   
   	if (!class_exists('CurrencyDisplay'))
	require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
    if ((!empty($user_currency)) && ($user_currency != $currency))
	{
	 $currencyDisplay = CurrencyDisplay::getInstance($user_currency);
	 //if ($user_exange_rate != 1)
	 $currencyDisplay->exchangeRateShopper = $user_exange_rate; 
	}
	else
	{
	if (!empty($currency))
	$currencyDisplay = CurrencyDisplay::getInstance($currency);
    else
	$currencyDisplay = CurrencyDisplay::getInstance();
    }
	
   // $test = $currencyDisplay->priceDisplay($order['details']['BT']->order_total, $currency); 
	$totals = array('order_total', 'order_salesPrice', 'order_billTaxAmount', 'order_billTax', 'order_billDiscountAmount', 'order_discountAmount', 'order_subtotal', 'order_tax', 'order_shipment', 'order_shipment_tax', 'order_payment', 'order_payment_tax', 'coupon_discount', 'coupon_code', 'order_discount', 'product_quantity', 'product_subtotal_with_tax'); 
	$product = 	 array ('product_item_price', 'product_final_price', 'product_basePriceWithTax', 'product_discountedPriceWithoutTax', 'product_priceWithoutTax', 'product_subtotal_with_tax', 'product_subtotal_discount', 'product_tax'); 
	
	foreach ($totals as $k1=>$v1)
	{
		
		
	 if (isset($order['details']['BT']->$v1))
	 {
		 
		 $order['details']['BT']->$v1 = (float)$order['details']['BT']->$v1; 
		 
		 $val = $order['details']['BT']->$v1; 
		 if (empty($val)) $val = 0; 
		 $val = (float)$val; 
		 
		 $new_key = $v1.'_txt'; 
		 
		 if ($currency != $user_currency)
		 {
			 $val = $currencyDisplay->convertCurrencyTo($currencyDisplay, $val); 
		 }
		 
		 if (method_exists($currencyDisplay, 'priceDisplay'))
		 {
		 $order['details']['BT']->$new_key = $currencyDisplay->priceDisplay($val, $currency); 
		 
		 
		
		 }
		 
		 $new_key = $v1.'_usercurrency'; 
		 $order['details']['BT']->$new_key = (float)$val; 
	 
	 
	 }
	}
	
	
	
	$paymenttext = ''; 
	$payment_id = (int)$order['details']['BT']->virtuemart_paymentmethod_id; 
	if (!empty($payment_id))
	 {
		
		$orig1 = JFactory::getApplication()->get('messageQueue', array()); 
		$orig2 = JFactory::getApplication()->get('_messageQueue', array()); 
		
	    		JPluginHelper::importPlugin('vmpayment');
		$_dispatcher = JDispatcher::getInstance();
		$ordercopy = $order; 
		ob_start();  
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) $ordercopy = ''; 
		try
		{
		  $_returnValues = $_dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $order['details']['BT']->virtuemart_order_id,$order['details']['BT']->virtuemart_paymentmethod_id, &$ordercopy));
		}
		catch (Exception $e)
		{
			
		}
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) $paymenttext = strip_tags($ordercopy); 
		else
		foreach ($_returnValues as $_returnValue) {
			if ($_returnValue !== null) {
				$paymenttext .= $_returnValue;
			}
		}
		
		JFactory::getApplication()->set('messageQueue', $orig1); 
		JFactory::getApplication()->set('_messageQueue', $orig2); 
		
		$delete = ob_get_clean(); 
	 }
	 
	 $order['details']['BT']->payment_name = $order['details']['BT']->payment_method_name = $paymenttext; 
	 $order['details']['BT']->payment_name_txt = strip_tags($order['details']['BT']->payment_method_name = $paymenttext); 
	 $order['details']['BT']->payment_name_txt = str_replace("\r\r\n", "", $order['details']['BT']->payment_name_txt); 
	$order['details']['BT']->payment_name_txt = str_replace("\r\n", "", $order['details']['BT']->payment_name_txt); 
	$order['details']['BT']->payment_name_txt = str_replace("\n", "", $order['details']['BT']->payment_name_txt); 
	
	 
	 $shipment = ''; 
	 $shipment_id = $order['details']['BT']->virtuemart_shipmentmethod_id; 
	 if (!empty($shipment_id))
	 {
		ob_start(); 
		$orig1 = JFactory::getApplication()->get('messageQueue', array()); 
		$orig2 = JFactory::getApplication()->get('_messageQueue', array());
		
	   		JPluginHelper::importPlugin('vmshipment');
		$_dispatcher = JDispatcher::getInstance();
		$ordercopy2 = $order; 
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) $ordercopy2 = ''; 
		try
		{
		 $returnValues = $_dispatcher->trigger('plgVmOnShowOrderFEShipment',array( $order['details']['BT']->virtuemart_order_id,$order['details']['BT']->virtuemart_shipmentmethod_id, &$ordercopy2));
		}
		catch (Exception $e)
		{
			
		}
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) $shipment = strip_tags($ordercopy2); 
		else
		foreach ($returnValues as $returnValue) {
			if ($returnValue !== null) {
			   $shipment .= $returnValue;
				
			}
		}
	   
	   JFactory::getApplication()->set('messageQueue', $orig1); 
	   JFactory::getApplication()->set('_messageQueue', $orig2); 
	   $delete = ob_get_clean(); 

	 }
	$order['details']['BT']->shipment_name = $order['details']['BT']->shipment_method_name = $order['details']['BT']->shipping_method_name = $shipment; 
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	
	
	$order['details']['BT']->shipment_name_txt = strip_tags($order['details']['BT']->shipment_name); 
	$order['details']['BT']->shipment_name_txt = str_replace("\r\r\n", "", $order['details']['BT']->shipment_name_txt); 
	$order['details']['BT']->shipment_name_txt = str_replace("\r\n", "", $order['details']['BT']->shipment_name_txt); 
	$order['details']['BT']->shipment_name_txt = str_replace("\n", "", $order['details']['BT']->shipment_name_txt); 
	
	$db = JFactory::getDBO(); 
	
	if (OPCmini::tableExists('virtuemart_products_'.$vmlang))
	{
       $extra_desc = 'virtuemart_products_'.$vmlang; 
	}
	
	
	if (OPCmini::tableExists('virtuemart_categories_'.$vmlang))
    if (!empty($order['items']))
    foreach ($order['items'] as $key=>$item)
	{
     
     $id = 	 $order['items'][$key]->virtuemart_product_id; 
	 $order['items'][$key]->product_desc = $order['items'][$key]->product_s_desc = ''; 
	 if (!empty($id))
	 if (!empty($extra_desc))
	 {
		 
		 $q = 'select `product_desc`, `product_s_desc` from `#__'.$extra_desc.'` where virtuemart_product_id = '.(int)$id.' limit 0,1'; 
		 $db->setQuery($q); 
		 $extra = $db->loadAssoc(); 
		 if (!empty($extra))
		 foreach ($extra as $k=>$v)
		 {
		  $order['items'][$key]->$k = $v; 
		 }
		 
	 }
	 
	 foreach ($product as $k1=>$v1)
	{
		
		
	 if (isset($order['items'][$key]->$v1))
	 {
		 
		 $order['items'][$key]->$v1 = (float)$order['items'][$key]->$v1; 
		 
		 $val = $order['items'][$key]->$v1; 
		 if (empty($val)) $val = 0; 
		 $val = (float)$val; 
		 
		 if ($currency != $user_currency)
		 {
			 $val = $currencyDisplay->convertCurrencyTo($currencyDisplay, $val); 
		 }
		 
		 $new_key = $v1.'_txt'; 
		 if (method_exists($currencyDisplay, 'priceDisplay'))
		 $order['items'][$key]->$new_key = $currencyDisplay->priceDisplay($val, $currency); 
	 
	     $new_key = $v1.'_usercurrency'; 
		 $order['items'][$key]->$new_key = $val; 
	 
	 
	 
	 
	 }
	}
	 
	 
	  if (empty($item->virtuemart_category_id)) 
	  {
	   //stAn - check if we have category_id info elsewhere:
	   $q = 'select virtuemart_category_id from #__virtuemart_product_categories where virtuemart_product_id = '.(int)$item->virtuemart_product_id.' order by ordering asc limit 0,1'; 
	   $db->setQuery($q); 
	   $cat_id = $db->loadResult(); 
	   
	   
	   if (!empty($cat_id))
	    {
		   $order['items'][$key]->virtuemart_category_id = $cat_id;
		   $order['items'][$key]->virtuemart_category_name = ''; 
		   $order['items'][$key]->category_name = ''; 
		}
		else
		{
		
	     $order['items'][$key]->virtuemart_category_id = 0; 
	     $order['items'][$key]->virtuemart_category_name = ''; 
		 $order['items'][$key]->category_name = ''; 
	    }
		
		
	   //continue; 
	  }
	  if (empty($order['items'][$key]->product_sku))
	  $order['items'][$key]->product_sku = $order['items'][$key]->order_item_sku; 
      if (empty($order['items'][$key]->product_name))
	  $order['items'][$key]->product_name = $order['items'][$key]->order_item_name; 
	  
	  
	  if (!isset($order['items'][$key]->prices))
	  {
		  $order['items'][$key]->prices = array(); 
		  $order['items'][$key]->prices['salesPrice'] = $order['items'][$key]->product_final_price; 
		  $order['items'][$key]->prices['priceWithoutTax'] = $order['items'][$key]->product_priceWithoutTax; 
		  $order['items'][$key]->prices['tax'] = $order['items'][$key]->product_tax; 
		  $order['items'][$key]->prices['basePriceWithTax'] = $order['items'][$key]->	product_basePriceWithTax; 
		  
		  $order['items'][$key]->prices['discountedPriceWithoutTax'] = $order['items'][$key]->product_discountedPriceWithoutTax; 
		  
	  }
	  
	  $cat_id = (int)$item->virtuemart_category_id; 
	  if (OPCmini::tableExists('virtuemart_categories_'.$vmlang))
	  if (!empty($cat_id))
	  {
	  $q = 'select * from `#__virtuemart_categories_'.$vmlang.'` where virtuemart_category_id = '.$cat_id.' limit 0,1'; 
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  
	
	  if (!empty($res))
	  {
	  foreach ($res as $key5=>$val5)
	   {
	    $order['items'][$key]->$key5 = $val5; 
	   }
	   
	   $order['items'][$key]->virtuemart_category_name = $order['items'][$key]->category_name; 
	   
	   
	  }
	  }
	   
	    if (empty($item->virtuemart_manufacturer_id)) 
	  {
	   //stAn - check if we have category_id info elsewhere:
	   $q = 'select virtuemart_manufacturer_id from #__virtuemart_product_manufacturers where virtuemart_product_id = '.(int)$item->virtuemart_product_id.'  limit 0,1'; 
	   $db->setQuery($q); 
	   $man_id = $db->loadResult(); 
	   
	   if (!empty($man_id))
	    {
		   $order['items'][$key]->virtuemart_manufacturer_id = $man_id;
		   $order['items'][$key]->virtuemart_manufacturer_name = ''; 
		   $order['items'][$key]->manufacturer_name = ''; 
		   $order['items'][$key]->mf_name = ''; 
		}
		else
		{
		
	     $order['items'][$key]->virtuemart_manufacturer_id = 0; 
	     $order['items'][$key]->virtuemart_manufacturer_name = ''; 
		   $order['items'][$key]->manufacturer_name = ''; 
		   $order['items'][$key]->mf_name = ''; 
	    }
		
		
		 $mf_id = (int)$item->virtuemart_manufacturer_id; 
		 if (OPCmini::tableExists('virtuemart_manufacturers_'.$vmlang))
		 if (!empty($mf_id))
		 {
	  $q = 'select * from `#__virtuemart_manufacturers_'.$vmlang.'` where virtuemart_manufacturer_id = '.$mf_id.' limit 0,1'; 
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  
	  
	  if (!empty($res))
	  {
	  foreach ($res as $key5=>$val5)
	   {
	    $order['items'][$key]->$key5 = $val5; 
	   }
	   $order['items'][$key]->virtuemart_manufacturer_name = $order['items'][$key]->manufacturer_name = $order['items'][$key]->mf_name;  
	   }
	   }
		
	   
	  }
	   
	   
	}
  
    if (!empty($order['details']['BT']->virtuemart_country_id))
		{
		   $val = (int)$order['details']['BT']->virtuemart_country_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_countries where virtuemart_country_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   if (!empty($res))
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['BT']->$key5 = $val5; 
			}
		}
		
		$order['details']['BT']->state_name = ''; 
		
	
	if (!empty($order['details']['BT']->virtuemart_state_id))
		{
		   $val = (int)$order['details']['BT']->virtuemart_state_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_states where virtuemart_state_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   
		   if (!empty($res))
		   {
		    $emptystate = array(); 
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['BT']->$key5 = $val5; 
			   $emptystate[$key5] = '';   
			}
		   }
		   else
		   {
		     $q = "select * from #__virtuemart_states where 1 limit 0,1"; 
		     $db->setQuery($q); 
		     $res = $db->loadAssoc(); 
			 $emptystate = array(); 
			  foreach ($res as $key5=>$val5)
		      {
			   $order['details']['BT']->$key5 = ''; 
			   $emptystate[$key5] = '';   
			  }
		   }
		}
	$order['details']['bt'] =& $order['details']['BT']; 
	
	$history_sorted = array(); 
	
	if (!empty($order['history']))
	foreach ($order['history'] as $key=>$val)
	{
	  $sql = $order['history'][$key]->modified_on; 
	  $date = new JDate($sql); 
	  $time = $date->toUnix(); 
	  
	  
	  if (empty($val)) $val = ''; 
	  if (isset($history_sorted[$time]))
	  $time++; 
	  $history_sorted[$time] = $order['history'][$key]; 
	  //['history_'.$i.'_'.$key] = $val; 
	}
	
	ksort($history_sorted, SORT_NUMERIC); 
	$history_sorted = array_reverse($history_sorted);

	unset($order['history']); 
	$order['history'] = $history_sorted; 
	
	if (empty($order['details']['ST'])) return; 
	
		
		if (empty($order['details']['ST']->email))
		 {
		   $order['details']['ST']->email =  $order['details']['BT']->email; 
		 }
	
    if (!empty($order['details']['ST']->virtuemart_country_id))
		{
		   $val = (int)$order['details']['ST']->virtuemart_country_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_countries where virtuemart_country_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   if (!empty($res))
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['ST']->$key5 = $val5; 
			}
		}
	if (!empty($order['details']['ST']->virtuemart_state_id))
		{
		   $val = (int)$order['details']['ST']->virtuemart_state_id; 
		   $db = JFactory::getDBO(); 
		   $q = "select * from #__virtuemart_states where virtuemart_state_id = '".(int)$val."' limit 0,1"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssoc(); 
		   if (empty($res)) $res = $emptystate;
		   
		   foreach ($res as $key5=>$val5)
		    {
			   $order['details']['ST']->$key5 = $val5; 
			   
			}
		  
		}		
		$order['details']['st'] =& $order['details']['ST']; 
		
		
		
  }
  
   // returns the domain url ending with slash
 public static function getUrl($rel = false)
 {
   $url = JURI::root(); 
   if ($rel) $url = JURI::root(true);
   if (empty($url)) return '/';    
   if (substr($url, -1)!='/')
   $url .= '/'; 
   return $url; 
 }

  public static function getNegativeOrder(&$order)
   {
      $totals = array('order_total', 'order_salesPrice', 'order_billTaxAmount', 'order_billTax', 'order_billDiscountAmount', 'order_discountAmount', 'order_subtotal', 'order_tax', 'order_shipment', 'order_shipment_tax', 'order_payment', 'order_payment_tax', 'coupon_discount', 'coupon_code', 'order_discount', 'product_quantity', 'product_subtotal_with_tax'); 
	$product = 	 array ('product_item_price', 'product_final_price', 'product_basePriceWithTax', 'product_discountedPriceWithoutTax', 'product_priceWithoutTax', 'product_subtotal_with_tax', 'product_subtotal_discount', 'product_tax'); 
   
  
   
   if (!empty($order['details']['BT']))
   foreach ($order['details']['BT'] as $key=>$val)
     {
	    if (in_array($key, $totals))
		 {
		   $val2 = floatval($val)*(-1); 
		   $order['details']['BT']->$key = $val2; 
		 }
	 }
	if (!empty($order['details']['ST']))
   foreach ($order['details']['ST'] as $key=>$val)
     {
	    if (in_array($key, $totals))
		 {
		   $val2 = floatval($val)*(-1); 
		   $order['details']['ST']->$key = $val2; 
		 }
	 }
	
	if (!empty($order['items']))
   foreach ($order['items'] as $key2=>$item)
   foreach ($item as $key=>$val)
     {
	    if (in_array($key, $totals))
		 {
		   $val2 = floatval($val)*(-1); 
		   $order['items'][$key2]->$key = $val2; 
		 }
	 }
	 
	
	 
	}
	
	
	public static function getOrderData($order_id)
	 {
	   
		if (!empty(self::$orderCache[$order_id])) return self::$orderCache[$order_id]; 
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	    $orderModel = OPCmini::getModel('orders');
	    $order = self::$orderCache[$order_id] = $orderModel->getOrder($order_id);
	    return $order; 
		
		
	 }
	 
	 public static function addCurrentGlobals(&$OPCeventview) {
	
	 
		 
	 if (!class_exists('VmConfig'))	  
	 {
	  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  VmConfig::loadConfig(); 
	 }
		 
		  if (!class_exists('CurrencyDisplay'))
	require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		 
		 
		 if (!class_exists('VirtueMartCart'))
			  require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		 
	   $mainframe = JFactory::getApplication();
		$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 

	 		
		if (!empty($virtuemart_currency_id))
		{
		  $currencyDisplay = CurrencyDisplay::getInstance($virtuemart_currency_id);
		}
else
{	
     $app = JFactory::getApplication();
	if ($app->getName() === 'site') {
			
	$cart = VirtuemartCart::getCart(); 
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
			   {
				    if (method_exists($cart, 'prepareCartData')) {
						ob_start(); 
				     $cart->prepareCartData(); 
					 $zz = ob_get_clean(); 
					}
			   }
	$currencyDisplay = CurrencyDisplay::getInstance($cart->paymentCurrency);
	$virtuemart_currency_id = $cart->paymentCurrency;
	}
	else {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		$virtuemart_currency_id = OPCmini::getVendorCurrency(); 
		
	}
}


	  
	  $OPCeventview->currency = self::getCurInfo($virtuemart_currency_id); 
	  if (isset($OPCeventview->products)) {
	     foreach ($OPCeventview->products as &$v) {
		   self::updateProductPricesCurrent($v, $virtuemart_currency_id); 
		 }
	  }
	  
	  
	   if (!isset($OPCeventview->order))
	 {
		 $OPCeventview->order = array(); 
		 $OPCeventview->order['details'] = array(); 
		 $OPCeventview->order['details']['BT'] = new stdClass(); 
		 $OPCeventview->order['items'] = array(); 
		 
		 $OPCeventview->order['details']['BT']->order_language = JFactory::getLanguage()->getTag();
		 $OPCeventview->order['details']['ST'] =& $OPCeventview->order['details']['BT']; 
		 
		 foreach ($OPCeventview->currency as $k=>$v) {
		  $OPCeventview->order['details']['BT']->$k = $v; 
		 }
	 }
	
	 
	 }
	 
	 //converts the prices of the product into the currently selected currency
	public static function updateProductPricesCurrent(&$product, $currency=0) {
	   
	   if (!empty($product->pricesCurrencyUpdated)) return; 
	   
	   $g = new stdClass(); 
	   
	   OPCtrackingHelper::addCurrentGlobals($g); 
	   $eP = self::getProductPriceEmpty(); 
	   
	   if (!empty($currency)) {
	      
		  
		  $cid = (int)$currency; 
		  if (!isset($product->prices['product_currency'])) return; 
		  $pcid = $product->prices['product_currency'] = (int)$product->prices['product_currency'];
		  
		  if ($pcid !== $cid) {

		         foreach ($product->prices as $k=>$v) {
						if (isset($eP[$k])) {
							
							if (!empty($v)) { 
							$v = (float)$v; 
							$product->prices[$k] = OPCmini::convertPrice($v,  $pcid, $cid); 
							}
							
						}
				 }
				 $product->prices['product_currency'] = $cid; 
				 $product->pricesCurrencyUpdated = true; 
				 
				
				 
		    }
	   }
	}
	
	public static function getProductPriceEmpty() {
	  return array (

  'product_price' => 0,
  'costPrice' => 0,
  'basePrice' => 0,
  'basePriceVariant' => 0,
  'basePriceWithTax' => 0,
  'discountedPriceWithoutTax' => 0,
  'priceBeforeTax' => 0,
  'salesPrice' => 0,
  'taxAmount' => 0,
  'salesPriceWithDiscount' => 0,
  'salesPriceTemp' => 0,
  'unitPrice' => 0,
  'priceWithoutTax' => 0,
  'discountAmount' => 0,
  'variantModification' => 0,
  'subtotal_with_tax' => 0,
  'subtotal_tax_amount' => 0,
  'subtotal_discount' => 0,
  'subtotal' => 0,
  'shipmentPrice' => 0,
  'shipmentTax' => 0,
  'product_final_price' => 0,
  'product_item_price' => 0, 
  'product_basePriceWithTax' => 0, 
  'product_discountedPriceWithoutTax' => 0, 
  'product_priceWithoutTax' => 0, 
  'product_subtotal_with_tax' => 0, 
  'product_subtotal_discount' => 0, 
  'product_tax' => 0, 
  
	); 
	}
	
	 
	 
}

class OPCeventview  extends OPCgeneralview  {
  var $params; 
  var $vendor; 
  var $cookieHash; 
  var $error; 
  var $errorMsg;
  var $products; 
  var $product; 
  var $order_item; 
    
  public function __construct($type, $file, $products=array(), $cart=null, $config=null)
    {
	  $this->errorMsg = ''; 
	   if (method_exists('JApplication', 'getHash'))
	   $hashn = JApplication::getHash('opctracking'); 
	   else $hashn = JUtility::getHash('opctracking'); 
	$this->isPureJavascript = false; 
	
	
     $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	 $this->cookieHash = $opchash; 
	 $this->error = false; 
	 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 
	 if (empty($config))
	 {
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 
	 $this->params = $params = OPCconfig::getValue('tracking_config', $file, 0, $default); 
	 }
	 else
	 {
	   $this->params = $config; 
	 }
	 
	$lang = JFactory::getLanguage()->getTag(); 
	
	
	$lang = str_replace('-', '_', $lang); 
	$lang = strtolower($lang); 
	
	$sku_prefix_key = 'sku_prefix_'.$lang; 
	$sku_suffix_key = 'sku_suffix_'.$lang; 
	 
	 
	 $prefix = ''; 
	if (isset($this->params->$sku_prefix_key))
	{
		$prefix = $this->params->{$sku_prefix_key}; 
	}
	
	$this->params->pid_prefix = $prefix; 
	
	$suffix = ''; 
	if (isset($this->params->$sku_suffix_key))
	{
		$suffix = $this->params->{$sku_suffix_key}; 
	}
	
	$this->params->pid_suffix = $suffix; 
	 
	 
	 $productClass = OPCmini::getModel('product');
	 $this->products = array(); 
	 
	
	 
	 
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'xmlexport.php');
	
	
	
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
	  
	  if (!function_exists('simplexml_load_file')) return; 
	  
	  $xmlexport = new JModelXmlexport(); 
	$general = new stdClass(); 
	  $xmlexport->getGeneral($general); 
	  OPCXmlExport::$config = $general; 
	
	if (empty($this->order)) {
		$this->order = array(); 
		$this->order['details'] = array(); 
		$this->order['details']['BT'] = new stdClass(); 
		$this->order['items'] = array(); 
	}
	
	
	
	 foreach ($products as $pid)
	   {
		  
		   
		  if ($type != 'cart')
	      $product = $productClass->getProduct($pid, true);
	      else
		  $product = $pid; 
	      
		  if (empty($product)) continue; 
		  
		  $vm1 = array(); 
		  $class = new stdClass(); 
		  $class->config = $config; 
		  if (!isset($class->config->language))
		  $class->config->language = JFactory::getLanguage()->getTag(); 
		  if (!isset($class->config->cname))
		  $class->config->cname = $config->key_name; 
		  if (!isset($class->config->url_type))
		  $class->config->url_type = 2;
		  if (!isset($class->config->shopper_group))
		  $class->config->shopper_group = 1; 
	      if (!isset($class->config->avaitext))
		  $class->config->avaitext = ''; 
	      if (!isset($class->config->avaidays))
		  $class->config->avaidays = 0; 

	  		 
			  

		  
		  OPCXmlExport::updateProduct($product, $class, $vm1); 
			
		
		
		  foreach ($vm1 as $z=>$q) {
		    if (empty($product->{$z})) {
			   
			   $product->{$z} = $q; 
			}
			$product->{'vm1_'.$z} = $q; 
		  }
	  
		  $product->product_final_price =& $product->prices['salesPrice']; 
		  
		  //OPCtrackingHelper::updateProductPricesCurrent($product); 
		  
		  $product->order_item_sku = $product->product_sku; 
		  $product->order_item_name = $product->product_name; 
		  
		  if (!empty($product->quantity))
		  $product->product_quantity =& $product->quantity; 
		  
		  if (empty($product->product_quantity))
		  $product->product_quantity = 1; 
		  $this->product =& $product;
		  $this->order_item =& $product; 
		  $id = (int)$product->virtuemart_product_id; 
		  $this->products[$id] = $product; 
		  $this->setPid($product); 
		  $this->order['items'][$id] = $product; 
		  if (empty($this->order['details']['BT']->order_total)) {
			  $this->order['details']['BT']->order_total = $product->product_final_price; 
		  }
		  else {
			  $this->order['details']['BT']->order_total += $product->product_final_price; 
		  }
		 
		  
	   }
	
	if (empty($this->order['details']['BT']->order_language))
	$this->order['details']['BT']->order_language = JFactory::getLanguage()->getTag(); 
	if (empty($this->order['details']['BT']->virtuemart_user_id))
	$this->order['details']['BT']->virtuemart_user_id = JFactory::getUser()->get('id'); 


	if (empty($this->order['details']['BT']->virtuemart_order_id))
	$this->order['details']['BT']->virtuemart_order_id = 0; 

	if (empty($this->order['details']['BT']->order_number))
	$this->order['details']['BT']->order_number = 0; 

	$this->setIdformat($this->order); 
	// default vendorId is always 1 here !
	if (!empty($cart)) $vid = $cart->vendorId; 
	else $vid = 1; 
	$this->vendor = OPCtrackingHelper::getVendorInfo($vid); 
	
	
	
	
	}
	
	
	
	
	
}

class OPCtrackingview extends OPCgeneralview {
  
  var $order; 
  var $vendor; 
  var $pingUrl;  
  var $pingData;
  var $cookieHash; 
  var $params; 
  var $error; 
  var $errorMsg;
  var $negative_statuses; 
  
 

  
  
  public function __construct($orderID, $params2, $status, $params, $file='')
  {
	 $this->allowMultiple = false; 
	$this->errorMsg = ''; 
    $this->isPureJavascript = false; 
	if (empty(OPCtrackingHelper::$config[$status]))
	{
	   $this->errorMsg .= 'Config not found for status: '.$status."<br />\n"; 
	   $this->error = true; 
	   return;
	}
  
  
    
  
    $this->params = new stdClass();
    if (!empty($params))
	$this->params = $params; 
	$this->pingUrl = OPCtrackingHelper::getUrl().'index.php?option=com_onepage&task=ping&nosef=1&format=raw&tmpl=component'; 
	
	
	if (method_exists('JApplication', 'getHash'))
	$hashn = JApplication::getHash('opctracking'); 
	else $hashn = JUtility::getHash('opctracking'); 
	
     $opchash = JRequest::getVar($hashn, false, 'COOKIE');
	$this->cookieHash = $opchash; 
	$this->pingData = 'hash='.$this->escapeSingle(str_replace('&', '&amp;', $opchash)); 
	
	
	
	
	$this->order = OPCtrackingHelper::getOrderData($orderID); 
	
	
	if (empty($this->order)) 
	{
	 $this->errorMsg .= 'Order not found: '.$orderID."<br />\n"; 
	 
	 $this->error = true; 
	 return;
	}
	
	if ((empty($this->order['items'])) || (!is_array($this->order['items']))) 
	{
	  
	  $this->error = true; 
	return; 
	}
	
	 
	
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	$negative_statuses = OPCconfig::getValue('tracking_negative', 'negative_statuses', 0, array()); 
	
	
	if (!empty($negative_statuses))
	{
	$copy_negative_statuses = array(); 
	foreach ($negative_statuses as $key=>$ng)
	 {
	   $copy_negative_statuses[$key] = $ng; 
	 }
	
	$negative_statuses = $copy_negative_statuses; 
	$this->negative_statuses = $negative_statuses; 
	
	if (is_array($negative_statuses))
	{
	   if (isset($this->order['details']['BT']))
	   if (in_array($this->order['details']['BT']->order_status, $negative_statuses))
	    {
		   OPCtrackingHelper::getNegativeOrder($this->order); 
		}
	   
	}
	}
	if (empty($this->order['details'])) 
	{
	 $this->errorMsg .= 'Order details not found: '.var_export($config, true)."<br />\n"; 
	 $this->error = true; 
	 return;
	}
	

	
	// check if the tracking was enabled before or after the order was created
	if (is_array($this->order))
	if (!empty($this->order['details']['BT']))
	{
	 $c = $this->order['details']['BT']->created_on; 
     $this->setIdformat($this->order); 
	 //2015-01-18 17:32:00
	 //strtotime('Y-m-d H:i:s')
	 /*
	 $time = strtotime($c); 
	 
	 */
	 
	 if (empty($c) || ($c === '0000-00-00 00:00:00'))
	 {
		  if (isset($this->order['history'][0]))
			  if (isset($this->order['history']->created_on))
			  {
				   $c = $this->order['history']->created_on; 
			  }
	 }
	 
	  $sql = $c; 
	  $date = new JDate($sql); 
	  $time = $date->toUnix(); 
    
	

	 if ((!empty($c)) && ($c !== '0000-00-00 00:00:00'))
	if (!empty(OPCtrackingHelper::$config))
	 if (!empty(OPCtrackingHelper::$config[$status]))
	 {
	 
	   // opc update, old codee: 
	   $key = 'since'.$file; 
	   if (!empty(OPCtrackingHelper::$config[$status]->$key))
	    {
			$session = JFactory::getSession(); 
			$opctrackingbe = $session->get('opctrackingbe', array()); 
			if ((!empty($opctrackingbe)) && (!isset($opctrackingbe[$this->order['details']['BT']->virtuemart_order_id]))) {
			
		    $since = OPCtrackingHelper::$config[$status]->$key;
			
			if ($since > $time) 
			{
			 $this->errorMsg .= 'OPC tracking was created AFTER the order was created: '.var_export(OPCtrackingHelper::$config, true).'order created on '.date(DATE_RFC2822, $time).' tracking created on '.date(DATE_RFC2822, $since)." for order ".$this->order['details']['BT']->virtuemart_order_id." <br />\n"; 
			 $this->error = true; 
			 return;	  
			}
			}
		}
	   
	   $key = $file.'_since'; 
	   
	   if (!empty(OPCtrackingHelper::$config[$status]->$key))
	    {
			
			$session = JFactory::getSession(); 
			$opctrackingbe = $session->get('opctrackingbe', array()); 
			if ((!empty($opctrackingbe)) && (!isset($opctrackingbe[$this->order['details']['BT']->virtuemart_order_id]))) {
		    $since = OPCtrackingHelper::$config[$status]->$key;
			
			if ($since > $time) 
			{
			
			   $this->errorMsg .= 'OPC tracking system plugin was set up AFTER the order was created: '.var_export(OPCtrackingHelper::$config, true).'order created on '.date(DATE_RFC2822, $time).' ('.$time.') tracking created on '.date(DATE_RFC2822, $since)." (".$since.") for order ".$this->order['details']['BT']->virtuemart_order_id." <br />\n"; 
			
			 $this->error = true; 
			 return;	  
			}
			}
		}
		
		
		
	 }
	   foreach ($this->order['items'] as $k=>$i) {
			$this->setPid($this->order['items'][$k]); 
		}
	
	}
	
	$this->error = false; 
	$this->pingData .= '&order_status='.$status; 
	$this->pingData .= '&order_id='.$orderID; 
	
	OPCtrackingHelper::getTextFields($this->order); 
	

	$this->vendor = OPCtrackingHelper::getVendorInfo($this->order['details']['BT']->virtuemart_vendor_id); 
	
	OPCtrackingHelper::$isPurchaseEvent = true; 
	$this->isPurchaseEvent = true; 
	
	
	
	if (!empty(OPCtrackingHelper::$isCartEvent))
	{
		$this->isCartEvent = true; 
	}
	if (!empty(OPCtrackingHelper::$isProductEvent))
	{
		$this->isCartEvent = true; 
	}


	
  }
 
 
  

}

class OPCgeneralview {
  var $isPurchaseEvent; 
  var $isCartEvent; 
  var $isProductEvent; 
  var $isPureJavascript; 
  var $allowMultiple; 
public function escapeSingle($string)
   {
     $string = str_replace("'", "\'", $string); 
	 // MacOS: 
	 $string =  str_replace("\r\r\n", '', $string); 
	 $string =  str_replace("\r\n", '', $string); 
	 $string =  str_replace("\n", '', $string); 
	 $string = trim($string); 
	 return $string; 
   }
  public function escapeDouble($string)
   {
     // in double quotes the end line is not supported
     $string =  str_replace('"', '\"', $string); 
	 $string =  str_replace("\r\r\n", '\r\n', $string); 
	 $string =  str_replace("\r\n", '\n', $string); 
	 $string =  str_replace("\n", ' ', $string); 
	 return $string; 
   }
   
   public function assignEvents()
   {
	if (!empty(OPCtrackingHelper::$isPurchaseEvent))
	{
		   $this->isPurchaseEvent = true; 
	}
	 if (!empty(OPCtrackingHelper::$isCartEvent))
	{
		$this->isCartEvent = true; 
	}
	if (!empty(OPCtrackingHelper::$isProductEvent))
	{
		$this->isCartEvent = true; 
	}

	   
   }
   
    public function fetchFile($file, $suffix='')
   {
	
	
     jimport('joomla.filesystem.file');
	 $this->assignEvents(); 
	 
	
	 
	 $file = JFile::makeSafe($file);
	 $filei = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.$suffix.'.php'; 
     
	 if (!file_exists($filei)) 
	 {
	 
	 return ''; 
	 }
	 
	 if (($suffix === '_cart') || (!empty(OPCtrackingHelper::$isCartEvent)))
	 {
		 OPCtrackingHelper::$isCartEvent = true; 
		 $this->isCartEvent = true; 
		 
		
	 }
	 else
	 if (($suffix === '_product') || (!empty(OPCtrackingHelper::$isProductEvent)))
	 {
		 OPCtrackingHelper::$isProductEvent = true; 
		 $this->isProductEvent = true; 
		 
	 }
	 
	 /* product ID format last change... */
	
	 /* product ID end... */
	 
	 
	 if (empty($this->allowMultiple)) {
	 ob_start(); 
     include_once($filei); 
	 return ob_get_clean(); 
	 }
	 else {
		ob_start(); 
		include($filei); 
		return ob_get_clean(); 
		 
	 }
   }
   
   
   public function setPid(&$product, $lang='') {
		$product->pid = $this->getPID($product->virtuemart_product_id, $product->product_sku, $lang); 
	}
   
   public function setIdformat(&$order) {
	   
	   
  $idformat = (int)$this->order['details']['BT']->virtuemart_order_id; 
 
$this->params->idformat = (int)$this->params->idformat; 
 
if ($this->params->idformat===1)
{
  $idformat = $this->order['details']['BT']->virtuemart_order_id.'_'.$this->order['details']['BT']->order_number;
}
else
if ($this->params->idformat===2)
 {
   $idformat = $this->order['details']['BT']->order_number; 
 }
 
 $this->idformat = $idformat; 
 return $idformat; 
	   
   }
   
   public function getPID($product_id, $product_sku, $lang='')
   {
	   
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
	   
	   
	   if (empty($lang)) {
	if (isset($this->order) && (isset($this->order['details']['BT'])) && (isset($this->order['details']['BT']->order_language)))
	{
	$lang = $this->order['details']['BT']->order_language; 
	}
	else
	{
		$lang = JFactory::getLanguage()->getTag(); 
	}
	}
	$lang = str_replace('-', '_', $lang); 
	$lang = strtolower($lang); 
	
	$sku_prefix_key = 'sku_prefix_'.$lang; 
	$sku_suffix_key = 'sku_suffix_'.$lang; 
	
	
	$prefix = ''; 
	if (isset($this->params->$sku_prefix_key))
	{
		$prefix = $this->params->{$sku_prefix_key}; 
	}
	
	$this->params->pid_prefix = $prefix; 
	
	$suffix = ''; 
	if (isset($this->params->$sku_suffix_key))
	{
		$suffix = $this->params->{$sku_suffix_key}; 
	}
	
	$this->params->pid_suffix = $suffix; 
	   
	   
	   return OPCXmlExport::getPID($this->params->pidformat, $product_id, $product_sku, $lang, $prefix, $suffix); 
	   
	
   }

}

