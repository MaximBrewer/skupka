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

	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	jimport( 'joomla.filesystem.file' );
	
	 
    
  // Load the virtuemart main parse code

	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
//	require_once( JPATH_ROOT . '/includes/domit/xml_domit_lite_include.php' );
//	require_once( JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'ajax'.DS.'ajaxhelper.php' );	
	

   require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'compatibility.php'); 	
	class configModelConfig extends OPCModel
	{	
		function __construct()
		{
			
			parent::__construct();
			$this->createTable(); 
		
		}
		
		function &getShipments()
		{
		   
jimport( 'joomla.plugin.helper' );
$plugin = JPluginHelper::getPlugin('vmshipment', 'pickup_or_free'); 
   if (!class_exists('VmConfig'))
    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');

VmConfig::loadConfig(); 
if (!class_exists('vmPlugin'))
			 {
			 
			 require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
			 
			 
			 
			 }
			 
			 
			 
			 $dispatcher = JDispatcher::getInstance(); 
		
		$shipments = array(); 
		JPluginHelper::importPlugin('vmshipment');
		$results = $dispatcher->trigger('getShipmentMethodsOPC', array( 1, &$shipments)); 
		
		foreach ($shipments as $sh)
		 {
		   if ($sh->shipment_element == 'pickup_or_free')
		   return $sh; 
		 }
		  $null = ''; 
		  JFactory::getApplication()->enqueueMessage('Pickup or Delivery Plugin is not enabled OR OPC plugin is not activated!', 'error'); 
		  return $null; 
		}
		
		
		function fillPost(&$post)
		{
		   $days = array(
    'Routes',
	JText::_('MONDAY'), 
	JText::_('TUESDAY'), 
	JText::_('WEDNESDAY'), 
	JText::_('THURSDAY'), 
	JText::_('FRIDAY'), 
	JText::_('SATURDAY'), 
	JText::_('SUNDAY'), 
	);
	   
$this->shipment =& $this->getShipments(); 	 
$method =& $this->shipment->opcref->methods[0];   
$pfClass =&  $this->shipment->opcref; 
$routes = $pfClass->getRoutes($method); 
$vehicles = $pfClass->getVehicles($method); 
      $slots = $pfClass->getSlots($method); 
	  
	  
	  $ct = time(); 
	  $d = date('N', $ct); 
	  foreach ($slots as $sk=>$slot)
	  foreach ($routes as $kk=>$rr)
	  foreach ($days as $k=>$v)
	   {
	     if ($k == 0) continue; 
		 if (!isset($post['vehicle_day_'.$k.'_route_'.$kk.'_slot_'.$sk]))
		 $post['vehicle_day_'.$k.'_route_'.$kk.'_slot_'.$sk] = 0; 
		 
		 $tt  = $ct - (($d - $k) * 24*60*60);
    if (($d - $k) == 0) $cl = ' today'; 
   else $cl = ''; 
  $table .= '<td class="day '.$cl.'">'; 
  $key = date('Y_m_d', $tt); 
		 
		 if (!isset($post['vehicle_cday_'.$key.'_route_'.$kk.'_slot_'.$sk]))
		 $post['vehicle_cday_'.$key.'_route_'.$kk.'_slot_'.$sk] = 0; 
		 
	   }
	
		}
		
		function getConfig($key)
		{
		   if (stripos($key, 'vehicle_day_')!==false)
		   {
		   
		  
		      $a = explode('_', $key); 
			  $dw = $a[2]; 
			  $route = $a[4]; 
			  $slot = $a[6]; 
			  
			  
			  
			  
			  $ret = $this->getConfigD($dw, 0, 0, $route, $slot); 
			  
			  return $ret; 
			}
			else
			{
			   $a = explode('_', $key); 
			  
			  $year = $a[2]; 
			  $month = $a[3]; 
			  $day = $a[4]; 
			  $slot = $a[8];
			  $route = $a[6]; 
			  $arr = $this->getConfigD($day, $month, $year, $route, $slot); 

			  if ($key == 'vehicle_cday_2014_05_20_route_0_slot_1')
			  {
			  //vehicle_cday_2014_05_20_route_0_slot_1
			    //var_dump($arr); die(); 
			  }

				
			  if (!empty($arr)) return $arr; 
			  $date = strtotime($day.'-'.$month.'-'.$year); 
			  $dw = date('N', $date); 
			  return $this->getConfigD($dw, 0, 0, $route, $slot); 
			  
			}
			return array(); 
		}
		
		
		function getConfigD($day, $month, $year, $route=-1, $slot=-1)
		{
		    $table = '`#__virtuemart_shipment_plg_pickup_or_free_config`'; 
		   $q = 'select vehicle, route, slot from '.$table.' ';
		   $q .= ' where year = '.(int)$year.' and month = '.(int)$month.' and day = '.(int)$day.' '; 
		   
		   // to check just the availability of the day 
		   if ($route >= 0)
		   $q .= ' and route = '.(int)$route.' and slot = '.(int)$slot;
		   
		   $db = JFactory::getDBO(); 
		   $db->setQuery($q); 
		   $res = $db->loadAssocList(); 
		   
		   
		   
		   if (empty($res)) return array(); 
		   
		   $newa = array(); 
		   foreach ($res as $k=>$v) 
		   {
		    //if ($v['vehicle'] == -2) return array(-2); 
		    $newa[$v['route'].'_'.$v['slot'].'_'.$v['vehicle']] = $v['vehicle']; 
		   }
		   
		   return $newa; 
		   
		}
		
		function findErrors(&$post)
		{
		    	   $days = array(
    'Routes',
	JText::_('MONDAY'), 
	JText::_('TUESDAY'), 
	JText::_('WEDNESDAY'), 
	JText::_('THURSDAY'), 
	JText::_('FRIDAY'), 
	JText::_('SATURDAY'), 
	JText::_('SUNDAY'), 
	);
	   
$this->shipment =& $this->getShipments(); 	 
$method =& $this->shipment->opcref->methods[0];   
$pfClass =&  $this->shipment->opcref; 
$routes = $pfClass->getRoutes($method); 
$vehicles = $pfClass->getVehicles($method); 
      $slots = $pfClass->getSlots($method); 
	  
	  $reserved = array(); 
	  
	  $ct = time(); 
	  $d = date('N', $ct); 
	  foreach ($slots as $sk=>$slot)
	  foreach ($routes as $kk=>$rr)
	  foreach ($days as $k=>$v)
	   {
	     if ($k == 0) continue; 
		
		 $key = 'vehicle_day_'.$k.'_route_'.$kk.'_slot_'.$sk; 
		  if (!empty($post[$key]))
		  {
		    //var_dump($post['vehicle_cday_'.$key.'_route_'.$kk.'_slot_'.$sk]); die();  
			foreach ($post[$key] as $vh22=>$vehicleN)
			 {
			    // [slot][day][vehicle] = route
				$reserved[$sk][$days[$k].'(default)'][$vehicleN][rand()] = $kk; 
				if ($k == 1)
				{
				//echo $key."\n"; 
				$nk = 'slot_'.$sk.'_'.$days[$k].'(default)'.'vehicle_'.$vh22.'_route_'.$kk;
				/*
				//echo $nk."\n"; 
				
				var_dump($reserved[$sk][$days[$k].'(default)'][$vh22]); 
				echo 'post:';
				var_dump($post[$key]);
				*/
				//if (count($reserved[$sk][$days[$k].'(default)'][$vh22])>1) die('h'); 
				}
				
			 }
		  }
		 
		 
		 $tt  = $ct - (($d - $k) * 24*60*60);
    if (($d - $k) == 0) $cl = ' today'; 
   else $cl = ''; 
  $table .= '<td class="day '.$cl.'">'; 
  $key = date('Y_m_d', $tt); 
		 
		 
		 
		 if (!empty($post['vehicle_cday_'.$key.'_route_'.$kk.'_slot_'.$sk]))
		  {
		    //var_dump($post['vehicle_cday_'.$key.'_route_'.$kk.'_slot_'.$sk]); die();  
			$key = 'vehicle_cday_'.$key.'_route_'.$kk.'_slot_'.$sk; 
			foreach ($post[$key] as $v2=>$vehicle)
			 {
			    // [slot][day][vehicle] = route
				$dof = date('N', $tt); 
				/*
				var_dump($days[$dof]);
				var_dump($sk); 
				var_dump($v); 
				var_dump($vehicle); 
				die(); 
				*/
				if (!empty($reserved['slot: '.$sk][$days[$dof]]['vehicle: '.$v2]))
				 {
				  //echo '--'.$key.'-- (veh: '.$v2.')'."\n"; 
				  
				  //var_dump($reserved[$sk][$days[$dof]]); 
				  //var_dump($reserved['slot: '.$sk][$days[$dof]]); 
				 }
				// slot day vehicle
				//echo $key." (v:)".$v2." d: (".$days[$dof].") \n"; 
				//$reserved['slot: '.$sk][$days[$dof]]['vehicle: '.$v2][rand()] = $kk; 
				//$reserved[$sk][$days[$dof]][$v2][rand()] = $kk; 
				$reserved[$sk][$key][$vehicle][rand()] = $kk; 
				
				
			 }
		  }
		  
		  
		 
		 
	   }
	  
	   //var_dump($post['vehicle_cday_0_route_1_slot_0']); 
	   ///var_dump($post['vehicle_cday_0_route_0_slot_0']); 
	   //var_dump($post['vehicle_cday_2014_05_19_route_0_slot_0']); 
	   //var_dump($post['vehicle_cday_2014_05_19_route_0_slot_0']); 
	 
	   //var_dump($reserved); die(); 
	   // die('h'); 
	   $error = ''; 
	  // var_dump($reserved); 
	   // now check for duplicities: 
	   foreach ($reserved as $sk=>$x1)
	   foreach ($x1 as $k=>$x2)
	   foreach ($x2 as $v=>$x3)
	   {
	   
	    if ((!empty($x3) && (count($x3)>1))) 
		{
		
		$error .= 'The vehicle is used at more routes at the same time:<br/>'; 
		$error .= 'Vehicle: '.$vehicles[$v].' on '.$k.' at time slot '.$slots[$sk].' is used on these routes: '; 
	     foreach ($x3 as $kk=>$x4)
		 {
		  $error .= $routes[$kk].', '; 
		 }
		}
	   }
	   //echo $error; die(); 
	   return $error; 
	   }
		
		function store()
		{
		
		$post = JRequest::get('post'); 
		//var_dump($post); die(); 
		
		$this->fillPost($post); 
		
		$this->shipment =& $this->getShipments(); 
		$method =& $this->shipment->opcref->methods[0]; 
		$pfClass =&  $this->shipment->opcref; 
		$routes = $pfClass->getRoutes($method); 
		
		
		$db = JFactory::getDBO(); 
		$table = '`#__virtuemart_shipment_plg_pickup_or_free_config`'; 
		//reset config for PICKUP
		/*
		foreach ($routes as $kk=>$rn) {
			$q = 'delete from '.$table.' where year = 0 and month = 0 and day = 0 and route = '.(int)$kk; 
			$db->setQuery($q); 
			$db->query(); 
		}
		*/
		$q = 'delete from '.$table.' where 1=1'; //year = 0 and month = 0 and route = 999'; 
		$db->setQuery($q); 
		$db->query(); 
		/*
		$q = 'select * from '.$table.' where 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		print_r($res); die(); 
		*/
		$error = $this->findErrors($post); 
		
		foreach ($post as $key=>$val)
		{
		   if (stripos($key, 'vehicle_day_')!==false)
		   {
		   
		  
		      $a = explode('_', $key); 
			  $dw = $a[2]; 
			  $route = $a[4]; 
			  $slot = $a[6]; 
			  
			 
			  
			  //if (empty($val))
			   {
				  //first reset config: 
			     $this->insertUpdate($dw, 0, 0, -1, $route, $slot); 
			   }
			   
			  if ((!empty($val)) && (is_array($val)))
			   {
			      foreach ($val as $kv=>$vv)
				   {
				     $this->insertUpdate($dw, 0, 0, $vv, $route, $slot); 
				   }
			   }
			   else
			   if (!empty($val))
			  if (is_numeric($val))
			   $this->insertUpdate($dw, 0, 0, $val, $route, $slot); 
		   
		  
		   
		   
		   }
		   else
		   if (stripos($key, 'vehicle_cday_')!==false)
		   {
			  $a = explode('_', $key); 
			  
			  $year = $a[2]; 
			  $month = $a[3]; 
			  $day = $a[4]; 
			  $slot = $a[8];
			  $route = $a[6]; 
			  
			  //if (empty($val))
			   
			     // first delete old values:
				 if ($key=='vehicle_cday_2014_05_22_route_1_slot_0')
				  {
				  //var_dump($val); die(); 
				  }
				  // reset config: 
			     $this->insertUpdate($day, $month, $year, -1, $route, $slot); 
			   
			   //else
			   
			  if ((!empty($val)) && (is_array($val)))
			   {
			  
			      foreach ($val as $kv=>$vv)
				   {
				     $this->insertUpdate($day, $month, $year, $vv, $route, $slot); 
				   }
			   }
			   else
			   if (!empty($val))
			   {
			     if (is_numeric($val))
			     $this->insertUpdate($day, $month, $year, $val, $route, $slot); 
			   }
			   else
			   {
				   
				  
			      // if default exists, but we removed all fields, let's make it empty
			      $tm = strtotime($year.'-'.$month.'-'.$day); 
			      $dw = date('N', $tm); 
				  if (!empty($post['vehicle_day_'.$dw.'_route_'.$route.'_slot_'.$slot])) {
					  $defualt = $post['vehicle_day_'.$dw.'_route_'.$route.'_slot_'.$slot]; 
				   //$this->insertUpdate($day, $month, $year, -2, $route, $slot); 
				   $this->insertUpdate($day, $month, $year, $defualt, $route, $slot); 
				  }
			   }
		   
		   }
		}
		
		$config = JRequest::getVar('config'); 
		if (!empty($config))
		foreach ($config as  $key2=>$v2)
		{
		  if ($key2 == 'vehicles')
		   foreach ($v2 as $vh=>$val)
		    {
			   require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
			   OPCconfig::store('opc_delivery', 'vehicle', (int)$vh, (double)$val); 
			   
			}
		}
		//echo OPCconfig::getValue('opc_delivery', 'vehicle', 0, 400); 
		//die('ok'); 
	    require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
	    // load basic stuff:
	    OPCLang::loadLang(); 
		if (!empty($error)) return $error; 
		return true; 
		}
		
		
		public function createTable() {
			$q = '
CREATE TABLE IF NOT EXISTS  `#__virtuemart_shipment_plg_pickup_or_free_config` (
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `route` int(11) NOT NULL,
  `vehicle` int(11) NOT NULL,
  `slot` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;'; 
$db = JFactory::getDBO(); 
$db->setQuery($q); 
$db->query(); 

		}
		
		private function insertUpdate($day, $month, $year, $vehicle, $route, $slot, $d=false)
		{
		  $db = JFactory::getDBO(); 
		  //first delete old data: 
		  $table = '`#__virtuemart_shipment_plg_pickup_or_free_config`'; 
		  
		  $q = 'delete from '.$table; 
		  $q .= ' where year = '.(int)$year.' and month = '.(int)$month.' and day = '.(int)$day.' and route = '.(int)$route.' and slot = '.(int)$slot; 
		  if ($vehicle != -1)
		  $q .= ' and vehicle = '.(int)$vehicle; 
		  
		  
		  
		  if ($d)
		   {
		     echo $q; die(); 
		   }
		  $db->setQuery($q); 
		  $db->query(); 
		  $e = $db->getErrorMsg();  if (!empty($e)) { echo $e; die(); }
		  if ($vehicle != -1)
		  {
		    $q = 'insert into '.$table.' (`year`, `month`, `day`, `vehicle`, `route`, `slot`) values ('.(int)$year.', '.(int)$month.','.(int)$day.','.(int)$vehicle.','.(int)$route.', '.(int)$slot.')'; 
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg();
			if (!empty($e)) { echo $e; die(); }			
		  }
		  
		  
		}
}		