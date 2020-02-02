<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z  $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class pfHelper {
 
 public static function getDays()
 {
   		   return array(
    'Routes',
	JText::_('MONDAY'), 
	JText::_('TUESDAY'), 
	JText::_('WEDNESDAY'), 
	JText::_('THURSDAY'), 
	JText::_('FRIDAY'), 
	JText::_('SATURDAY'), 
	JText::_('SUNDAY'), 
	);

 }
 
 //check if there is any delivery on specific timestamp:
 public static function hasDelivery($ct, $retData=false) {
	 $day_of_week = date('N', $ct);
	 
	 $dn = date('Y-n-j', $ct); //2017-1-1 without leading zeros
	 $e = explode('-', $dn); 
	 $day_of_month = $e[2]; 
	 $month = $e[1]; 
	 $year = $e[0]; 
	 $db = JFactory::getDBO(); 
	 
	 //get custom delivery for specific day: 
	 $table = '`#__virtuemart_shipment_plg_pickup_or_free_config`'; 
	 $q = 'select * from '.$table.' where year = '.(int)$year.' and day = '.(int)$day_of_month.' and month = '.(int)$month.' and route <> 999'; 
	 
	 $db = JFactory::getDBO(); 
	 $db->setQuery($q); 
	 $res1 = $db->loadAssocList(); 
	 
	 if (!empty($res1)) {
		 if (empty($retData))
		 return true; 
	 }
	 
	 $table = '`#__virtuemart_shipment_plg_pickup_or_free_config`'; 
	 $q = 'select * from '.$table.' where year = 0 and day = '.(int)$day_of_week.' and month = 0 and route <> 999'; 
	 $db = JFactory::getDBO(); 
	 $db->setQuery($q); 
	 $res2 = $db->loadAssocList(); 
	 
	 if (!empty($res2)) {
		 if (empty($retData))
		 return true; 
	 }
	 
	 //retData returns routes per specific day: 
	 if (!empty($retData)) {
		 $data = new stdClass(); 
		 $data->routes = array(); 
		 $data->slots = array(); 
		 if (!empty($res1)) {
			 foreach ($res1 as $row) {
				 $data->routes[(int)$row['route']] = (int)$row['route']; 
				 $data->slots[(int)$row['slot']] = (int)$row['slot']; 
				 
			 }
		 }
		 if (!empty($res2)) {
			 foreach ($res2 as $row) {
				 $data->routes[(int)$row['route']] = (int)$row['route']; 
				 $data->slots[(int)$row['slot']] = (int)$row['slot']; 
			 }
		 }
		 return $data; 
		 
	 }
	 
	 return false; 
	 
 }
 
 public static function getPickupSlots(&$ref, $method) {
	 
  if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php')) return array(); 
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
  $model = new configModelConfig(); 

	 
	 $ret = array(); 
   	 $slots = $ref->getPickupSlots($method); 
 $ct = time(); 
 $d = date('N', $ct); 
 $pickuproute = $k2 = 999; 
 $r = 'Shop'; ; 
 
 $days = self::getDays(); 
 
 foreach ($slots as $sk=>$slot)
 {
	 $sk = (int)$sk; 
 foreach ($days as $k=>$v)
 {
	 
	 $k = (int)$k; 
	 
 if ($k === 0)
 {
  
  continue; 
 }
 else
 {
    $tt  = $ct - (($d - $k) * 24*60*60);
    $cl = ''; 
    $key = date('Y_m_d', $tt); 
  
   
   $name = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $model->getConfig($name); 
   
   if (!empty($cfg2)) {
	   
	   
	  $aslots = $ref->getSlotFromConfig($cfg2, $method); 
	  if (empty($ret[$k])) $ret[$k] = array(); 
	  $ret[$k][$sk] = $aslots; 
   }
  
  
  
  }
  

 }
 }
 
 
 
 
	return $ret; 
 }
 
 public static function getActiveDays($vehicles, $routes, $slots, &$cw, &$default)
  {
  if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php')) return array(); 
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
  $model = new configModelConfig(); 
  
  $cw = array(); 
  
  $days = self::getDays(); 
  
  foreach ($routes as $k2=>$r)
  foreach ($slots as $sk=>$slot)
  foreach ($days as $k=>$v)
  {
   $ct = time(); 
   $d = date('N', $ct); 
   if ($k == 0) continue; 
   $tt  = $ct - (($d - $k) * 24*60*60);
   if (($d - $k) == 0) $cl = ' today'; 
   else $cl = ''; 
   
   $key = date('Y_m_d', $tt); 
   
   // current week:
   $name = 'vehicle_cday_'.$key.'_route_'.$k2.'_slot_'.$sk; 
   $cfg = $model->getConfig($name); 
   
   // default days:
   $name2 = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $model->getConfig($name); 
   
   
   // -2 means it's an overrided default to disable it for the current week
   // if default (cfg2) is not empty, check if current week is overrided (cfg): 
   if (!empty($cfg2))
   {
    //$table .= '<option value="-2"'; 
    if (in_array(-2, $cfg))
    {
	    // set current week to disabled:
	    $cw[$name] = -2;
	}
  }
  
  // if current week is not set yet, or it's not overrided
  if ((!isset($cw[$name])) || ($cw[$name] != -2))
  foreach ($vehicles as $vk=>$kk)
  {
   
   if (!empty($cfg))
   if (in_array($vk, $cfg))
   {
     $cw[$name][$vk] = $vk;
   }
   
   if (!empty($cfg2))
   if (in_array($vk, $cfg2))
   {
     $default[$name2][$vk] = $vk;
   }
   
  }

 }
 
   

  }
  
  public static function getNextMonth($cd2='')
  {
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
      $model = new configModelConfig(); 
	  $class = $model->getShipments(); 
	  $method =& $class->opcref->methods[0]; 
	  $pfClass =&  $class->opcref; 
	  
	  
     $format = 'Y-n-j'; // 2014-1-1 , no leading zeros, working with js
     $ct = time(); 
	 
		  //today: 
	 	  $cd = date($format, $ct); 
		  if (!empty($cd2))
		  if ($cd2 != $cd)
		   {
		      $ct = strtotime($cd2); 
			  
		   }

	 
	 $routes = new stdClass(); 
	 for ($i = 0; $i < 100; $i++)
	   {
	      $cd = date($format, $ct+($i*60*60*24)); 
		  if ($cd2 != $cd)
		   {
		      $st = strtotime($cd2); 
			  
		   }
		  // date('Y-n-j')  
	      $data[$cd] = self::checkDate($cd, $method); 
		  if (!empty($data[$cd]))  {
		  foreach ($data[$cd] as $key=>$val)
		    {
			   $ka = explode('_', $key); 
				// defined in : C:\Users\RuposTel\AppData\Local\Temp\scp45985\root@192.168.122.1\srv\www\rupostel.com\web\vm2onj25\administrator\components\com_delivery\models\config.php
				//$newa[$v['route'].'_'.$v['slot'].'_'.$v['vehicle']] = $v['vehicle']; 
				
				$route = $ka[0]; 
				$slot = $ka[1]; 
				$vehicle = $ka[2]; 
	  
			    if (!isset($routes->$route)) $routes->$route = new stdClass(); 
				if (!isset($routes->$route->$cd)) $routes->$route->$cd = new stdClass(); 
				if (!isset($routes->$route->$cd->$slot)) $routes->$route->$cd->$slot = $vehicle;  
				$routes->{$route}->{$cd}->{$slot} = $vehicle; 
				
				
				if (!isset($route_a[$route])) $route_a[$route] = array(); 
				if (!isset($route_a[$route][$cd])) $route_a[$route][$cd] = array(); 
				if (!isset($route_a[$route][$cd][$slot])) $route_a[$route][$cd][$slot] = $vehicle;  
				$route_a[$route][$cd][$slot] = $vehicle; 
				
			}
	   }
	    else {
		   //no config for the Date:
		    $data[$cd] = array(); 
	   }
	   }
	  
	   /*
	  foreach ($routes as $k1=>$route)
	  foreach ($route as $time=>$cd)
	  foreach ($cd as $slot=>$val)
	   {
	      $html = ''; 
	   }
	   */
	   
	   // baause it's not clear how will the deserialization be done within the javascript, we are going to parse this purely as an object: 
	   
	   
	   
	   $ret = new stdClass(); 
	   $ret->data = (array)$data; 
	   $ret->routes = (array)$route_a; 
	   
	   $ret->allroutes = $routes = $pfClass->getRoutes($method); 
	   $ret->slots = $pfClass->getSlots($method); 
	   $ret->zip_delivery_ranges = $method->zip_delivery_ranges; 
	   return $ret; 
	  
	   
  }
  
  // cd in format: YYYY-mm-dd
  public static function checkDate($cd, $method)
  {
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
    $model = new configModelConfig(); 
	
	
	 $cw = array(); 
  
  $days = self::getDays(); 
  
  $st = strtotime($cd); 
  if (empty($st)) return; 
  $date = date('Y-m-d', $st); 
  //day of a week: 
  $cw = date('N', $st); 
  //date: 
  $year = date('Y', $st); 
  $month = date('m', $st); 
  $day = date('d', $st); 
  
  
  
  $arr = $model->getConfigD($day, $month, $year); 
  
  if (empty($arr))
  {
     $arr = $model->getConfigD($cw, 0, 0); 
  }
  
  if (empty($arr)) return ''; 
  
  // if only empty override: 
  if (count($arr)==1)
  if (reset($arr)==-2) return ''; 
  //if ($arr == array(-2)) return array(); 
  // default: 
  if ((!empty($method)) && ($method->reservations)) {
   self::checkOrders($arr, $date); 
  }
  
  //self::getHtml($arr, $date); 
  // now we need to check the capacity: 
  return $arr; 


}

public static function getHtml(&$arr, $date)
{
static $cfg; 
	if (empty($cfg))
    $cfg = self::getConfigRSV(); 
	// provides: $pfClass
	
	extract($cfg); 
	
    foreach ($arr as $key=>$opt)
	{
	  $ka = explode('_', $key); 
	  // defined in : C:\Users\RuposTel\AppData\Local\Temp\scp45985\root@192.168.122.1\srv\www\rupostel.com\web\vm2onj25\administrator\components\com_delivery\models\config.php
	  //$newa[$v['route'].'_'.$v['slot'].'_'.$v['vehicle']] = $v['vehicle']; 
	  
	  $route = $ka[0]; 
	  $slot = $ka[1]; 
	  $vehicle = $ka[2]; 
	  
	  if ($vehicle == -2) 
	  {
	     unset($arr[$key]); 
		 continue; 
	  }
	  
	  $route_name = @$routes[$route]; 
	  $vehicle_name = @$vehicles[$vehicle]; 
	  $slot_name = @$slots[$slot]; 

  
    
	   $arr[$key] = new stdClass(); 
	   $arr[$key]['key'] = $opt; 
	   $html = '';
	   $arr[$key]['options'] = $html; 
	}
}

 public static function &getConfigRSV(&$pfClass=null)
 {
    static $ret; 
	if (!empty($ret)) return $ret; 
	
     if (empty($pfClass))
	 {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_delivery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
      $model = new configModelConfig(); 
	  $class = $model->getShipments(); 
	  $method =& $class->opcref->methods[0]; 
	  $pfClass =&  $class->opcref; 
	 }
	 else
	 $method = $pfClass->methods[0]; 
	 
	 if (!empty($pfClass))
	 {
	   $routes = $pfClass->getRoutes($method); 
	   $vehicles = $pfClass->getVehicles($method); 
	   $slots = $pfClass->getSlots($method); 
	   
	 $config = new stdClass(); 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 
	 foreach ($vehicles as $v=>$vk)
	  {
	    $config->vehicles[$vk] = OPCconfig::getValue('opc_delivery', 'vehicle', $vk, 400); 
	  }
	  
	  $ret = array('pfClass' => $pfClass, 'routes'=>$routes, 'vehicles'=>$vehicles, 'slots'=>$slots, 'config'=>$config, 'method'=>$method); 
	  return $ret; 
	 }
	 die('Error, OPC vmplugin not installed !'); 
     
 }

 public static function checkOrders(&$arr, $date)
  {
  
    static $cfg; 
	if (empty($cfg))
    $cfg = self::getConfigRSV(); 
	// provides: $pfClass
	
	extract($cfg); 
	
    foreach ($arr as $key=>$opt)
	{
	  $ka = explode('_', $key); 
	  // defined in : C:\Users\RuposTel\AppData\Local\Temp\scp45985\root@192.168.122.1\srv\www\rupostel.com\web\vm2onj25\administrator\components\com_delivery\models\config.php
	  //$newa[$v['route'].'_'.$v['slot'].'_'.$v['vehicle']] = $v['vehicle']; 
	  
	  $route = $ka[0]; 
	  $slot = $ka[1]; 
	  $vehicle = $ka[2]; 
	  
	  if ($vehicle == -2) 
	  {
	     unset($arr[$key]); 
		 continue; 
	  }
	  
	  $route_name = @$routes[$route]; 
	  $vehicle_name = @$vehicles[$vehicle]; 
	  $slot_name = @$slots[$slot]; 
	  
	  
	  if ((!isset($route_name)) || (!isset($vehicle_name)) || (!isset($slot_name))) 
	   {
	   
	   //var_dump($route_name); var_dump($vehicle_name); var_dump($slot_name); die(); 
	   
	     unset($arr[$key]); 
		 continue; 
	   }
	  
      $db = JFactory::getDBO(); 
      $datect = (int)strtotime($date); 
      $q = 'select sum(order_weight) from `#__virtuemart_shipment_plg_pickup_or_free` '; 
	  $q .= ' where `delivery_stamp` = '.$datect." and `shipment_type`='free' and order_weight > 0 ";
	  $q .= ' and route LIKE "'.$db->escape($route_name).'" and vehicle LIKE "'.$db->escape($vehicle).'" and slot LIKE "'.$db->escape($slot).'"'; 
	  
	  $db->setQuery($q); 
	  $sum = $db->loadResult(); 
	  
	  if (!empty($sum))
	   {
	      $maxw = @$config->vehicles[$vehicle]; 
		  if (isset($maxw))
	      if ($sum >= $maxw)
		   {
		     unset($arr[$key]); 
		   }
		  else
		   {
		      // $this->getOrderWeight ($cart, $method->weight_unit);
			  $cart = VirtuemartCart::getCart(); 
			  $cw = $pfClass->_getOrderWeight($cart, $method->wight_unit); 
			  if (!empty($cw))
			   {
			      if (  ($sum + $cw) >= $maxw)
				    {
					  unset($arr[$key]); 
					}
			   }
			  
		   }
		  
	   }
	  
    }
   
  }
}