<?php  defined ('_JEXEC') or die('Restricted access');
/**
 * pickup or delivery plugin
 * license - commercial
 * author RuposTel.com
 *
 */
 
if (!class_exists ('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
}

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'))
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 

class plgVmShipmentPickup_or_free extends vmPSPlugin {

	// instance of class
	public static $_this = FALSE;
	
	
	public function getOpcJavascript($method) {
		if (!defined('JSADDED')) {
					JHtml::_('jquery.framework');
		JHtml::_('jquery.ui');
		JHTMLOPC::script('helper_v2.js', 'plugins/vmshipment/pickup_or_free/', false);
		JHTMLOPC::_('behavior.modal', 'a.pfdmodal'); 
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php')) {
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		   $selected_theme = OPCmini::getSelectedTemplate(); 
		   if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$selected_theme.DIRECTORY_SEPARATOR.'pickup_or_free.css')) {
		     JHTMLOPC::stylesheet('pickup_or_free.css', 'components/com_onepage/themes/'.$selected_theme.'/', false);
		   }
		   else {
			   JHTMLOPC::stylesheet('pickup_or_free.css', 'plugins/vmshipment/pickup_or_free/', false);
			   JHTMLOPC::stylesheet('wrapper.css', 'plugins/vmshipment/pickup_or_free/', false);
		   }
		}
		else {
		  JHTMLOPC::stylesheet('pickup_or_free.css', 'plugins/vmshipment/pickup_or_free/', false);
		  JHTMLOPC::stylesheet('wrapper.css', 'plugins/vmshipment/pickup_or_free/', false);
		}
		

			define('JSADDED', 1); 
		}
	}
	
	
	public function pickup_or_free_get_class(&$ret) {
		$ret = $this; 
	}
	
	/**
	 * @param object $subject
	 * @param array  $config
	 */
	function __construct (& $subject, $config) {
		
		
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'))
		{
			return;
		}
		$option = JRequest::getVar('option'); 
		$view = JRequest::getVar('view'); 
		
		parent::__construct ($subject, $config);
		// don't init this plugin outside cart !
		$this->_loggable = TRUE;
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$varsToPush = $this->getVarsToPush ();
		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);
		
		$document = JFactory::getDocument();
		$class = get_class($document); 
		$class = strtoupper($class); 
		$arrc = array('JDOCUMENTHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT'); 
		
		$arr = array ('cart', 'pluginresponse', 'user', 'opccart'); 
		
		$this->getPluginMethods(1);
		
		if (empty($this->methods)) return; 
		
		if (!defined('JSADDED'))
		if (($option == 'com_virtuemart')  && ((in_array($view, $arr))))
		if (in_array($class, $arrc))
		if (class_exists('JHTMLOPC'))
		if (!defined('pickup_helper'))
		{
			define('JSADDED', 1); 
			
		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui');
		JHTMLOPC::script('helper_v2.js', 'plugins/vmshipment/pickup_or_free/', false);
		JHTMLOPC::_('behavior.modal', 'a.pfdmodal'); 
		
		JHTMLOPC::stylesheet('pickup_or_free.css', 'plugins/vmshipment/pickup_or_free/', false);
		JHTMLOPC::stylesheet('wrapper.css', 'plugins/vmshipment/pickup_or_free/', false);
		//JHTMLOPC::_('behavior.calendar');
	
		
		define('pickup_helper', 1); 
		}
		
		
		
		// 		self::$_this
		//$this->createPluginTable($this->_tablename);
		//self::$_this = $this;
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 *
	 * @author Valérie Isaksen
	 */
	public function getVmPluginCreateTableSQLOld () {

		return $this->createTableSQL ('Pickup or Free Table');
	}
	
	public function _getOrderWeight($cart, $weight_unit)
	{
	  return $this->getOrderWeight ($cart, $weight_unit);
	}

	public function getVmPluginCreateTableSQL()
	{
	
	  $query = '
CREATE TABLE IF NOT EXISTS `#__virtuemart_shipment_plg_pickup_or_free` (
  `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `virtuemart_order_id` int(1) UNSIGNED DEFAULT NULL,
  `shipment_type` varchar(255) DEFAULT NULL,
  `delivery_date` varchar(255) DEFAULT NULL,
  `delivery_time` varchar(255) DEFAULT NULL,
  `delivery_stamp` int(12) NOT NULL COMMENT \'Delivery linux time stamp\',
  `order_number` char(32) DEFAULT NULL,
  `route` varchar(255) NOT NULL DEFAULT \'none\',
  `vehicle` int(11) default 0,
  `slot` int(11) default 0,
  `virtuemart_shipmentmethod_id` mediumint(1) UNSIGNED DEFAULT NULL,
  `shipment_name` varchar(5000) DEFAULT NULL,
  `order_weight` decimal(10,4) DEFAULT NULL,
  `shipment_weight_unit` char(3) DEFAULT \'KG\',
  `shipment_cost` decimal(10,2) DEFAULT NULL,
  `shipment_package_fee` decimal(10,2) DEFAULT NULL,
  `tax_id` smallint(1) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `created_by` int(11) NOT NULL DEFAULT \'0\',
  `modified_on` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `modified_by` int(11) NOT NULL DEFAULT \'0\',
  `locked_on` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `locked_by` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'Pickup or Free Table\' AUTO_INCREMENT=1 ;'; 
   return $query; 
	}
	
	/**
	 * @return array
	 */
	function getTableSQLFields () {
$SQLfields = array(
	'id' => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT', 
  'virtuemart_order_id' => 'int(11) UNSIGNED DEFAULT NULL',
  'shipment_type' => 'varchar(255) DEFAULT NULL',
  'delivery_date' => 'varchar(255) DEFAULT NULL',
  'delivery_time' => 'varchar(255) DEFAULT NULL',
  'delivery_stamp' => 'int(12) NOT NULL COMMENT \'Delivery linux time stamp\'',
  'order_number' => 'char(32) DEFAULT NULL',
  'route' => 'varchar(255) NOT NULL DEFAULT \'none\'',
  'vehicle' => 'int(11) UNSIGNED DEFAULT 0',
  'slot' => 'int(11) UNSIGNED DEFAULT 0',
  'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED DEFAULT NULL',
  'shipment_name' => 'varchar(5000) DEFAULT NULL',
  'order_weight' => 'decimal(10,4) DEFAULT NULL',
  'shipment_weight_unit' => 'char(3) DEFAULT \'KG\'',
  'shipment_cost' => 'decimal(10,2) DEFAULT NULL',
  'shipment_package_fee' => 'decimal(10,2) DEFAULT NULL',
  'tax_id' => 'smallint(1) DEFAULT NULL',
  ); 
	
	/*
	'created_on' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
  'created_by' => 'int(11) NOT NULL DEFAULT \'0\'',
  'modified_on' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
  'modified_by' => 'int(11) NOT NULL DEFAULT \'0\'',
  'locked_on' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
  'locked_by' => 'int(11) NOT NULL DEFAULT \'0\' '
  */
	
	/*
		$SQLfields = array(
			'id'                           => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id'          => 'int(11) UNSIGNED',
			'shipment_type'				   => 'varchar(255)', 
			'delivery_date'				   => 'varchar(255)', 			
			'delivery_time'				   => 'varchar(255)', 
			'delivery_stamp'		       => 'int(12) NOT NULL COMMENT  \'Delivery linux time stamp\'',
			'order_number'                 => 'char(32)',
			'virtuemart_shipmentmethod_id' => 'mediumint(1) UNSIGNED',
			'shipment_name'                => 'varchar(5000)',
			'order_weight'                 => 'decimal(10,4)',
			'shipment_weight_unit'         => 'char(3) DEFAULT \'KG\'',
			'shipment_cost'                => 'decimal(10,2)',
			'shipment_package_fee'         => 'decimal(10,2)',
			'tax_id'                       => 'smallint(1)'
			
		);
		
		*/
		//ALTER TABLE  `jos_virtuemart_shipment_plg_pickup_or_free` ADD  `delivery_stamp` INT( 12 ) NOT NULL COMMENT  'Delivery linux time stamp' AFTER  `delivery_time`
		return $SQLfields;
	}

	
	public function plgVmOnShowOrderFEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id, &$shipment_name) {
		/*
		$db = JFactory::getDBO(); 
		try {
		$q = 'select shipment_name from '.$this->_tablename.' where virtuemart_order_id = '.$virtuemart_order_id; 
		$db->setQuery($q); 
		$name = $db->loadResult(); 
		var_dump($name); die(); 
		}
		catch (Exception $e) {
		}
		*/
		$this->onShowOrderFE ($virtuemart_order_id, $virtuemart_shipmentmethod_id, $shipment_name);
		
		if (empty($shipment_name)) {
			$text = $this->getOrderShipmentHtml($virtuemart_order_id); 
			if (!empty($text)) {
				$shipment_name = $text; 
			}
		}
		
		
	}
	
	 function getRoutes($m)
	{
	  $arr = explode(';', $m->routes); 					  					  
	  $ret = array(); 
	  foreach ($arr as $key=>$val)
	   if (!empty($val))
	  $ret[$key] = $val; 
	  
	  return $ret; 
	}
	
	function getVehicles($m)
	{
	  $arr = explode(';', $m->vehicles); 					  					  
	  $ret = array(); 
	  foreach ($arr as $key=>$val)
	  if (!empty($val))
	  $ret[$key] = $val; 
	  
	  return $ret; 
	
	}
	
	function getRouteId($route, $m)
	{
	  $ret = array(); 
	  $arr = explode(';', $m->routes); 
	  foreach ($arr as $key=>$val)
	   if (!empty($val))
	  if ($val == $route) return $key; 
	  
	  return -1; 
	
	}
	function getSlotId($slot, $m)
	{
	  $ret = array(); 
	  $arr = explode(';', $m->slots); 
	  foreach ($arr as $key=>$val)
	  if ($val == $slot) return $key; 
	  
	  return -1; 
	}
	
	//true if it has passed 
	function wasToday($time = '00:00', $now = null) {
		
		  //$f1 = $method->pickup_end_time; 
			$f2 = explode(':', $time); 
			$hours = (int)$f2[0]; 
		    $min = (int)$f2[1]; 
			
		if (is_null($now)) $now = time(); 	
	    
		$h = (int)date('G', $now); 
		$m = (int)date('i', $now); 
		
		$current_t = $h*60+$m + 0.1; 
		$input_t = $hours*60+$min; 
		
		
		
		if ($current_t > $input_t) return true; 
		if ($current_t < $input_t) return false; 
		
		
		return true; 
		
	}
	
	function getPickupTimeSlots($method) {
		$it = (int)$method->time_period; 
		if (empty($it)) $it = 30; 
		$c = 60 / $it; 
		$f1 = $method->pickup_start_time; 
					    $f2 = explode(':', $f1); 
					    $from = $f2[0]; 

					    $f1 = $method->pickup_end_time; 
						
					    $f2 = explode(':', $f1); 
					    $to = $f2[0]; 
						$to_min = $f2[1]; 
		 
		 $options = array(); 
		 for ($i = $from; $i<=$to; $i++)
					  {
						  $options[$i.':00'] = $i.':00'; 
						  
						  if ($i != $to)
						  for ($q = 1; $q<$c; $q++)
						  {
						    
						    $j = $q*$it;
							$options[$i.':'.$j] = $i.':'.$j; 
							
						   }
					  }
					  
			return $options; 
	}
	function getDeliveryTimeSlots($method) {
		$it = (int)$method->time_period; 
		if (empty($it)) $it = 30; 
		$c = 60 / $it; 
		$f1 = $method->free_start_time; 
					    $f2 = explode(':', $f1); 
					    $from = $f2[0]; 

					    $f1 = $method->free_end_time; 
						
					    $f2 = explode(':', $f1); 
					    $to = $f2[0]; 
						$to_min = $f2[1]; 
		 
		 $options = array(); 
		 for ($i = $from; $i<=$to; $i++)
					  {
						  $options[$i.':00'] = $i.':00'; 
						  
						  if ($i != $to)
						  for ($q = 1; $q<$c; $q++)
						  {
						    
						    $j = $q*$it;
							$options[$i.':'.$j] = $i.':'.$j; 
							
						   }
					  }
					  
			return $options; 
	}
	
	function getSlots($m)
	{
		
	  if (!empty($m->custom_slots)) {
	  $ret = array(); 
	  $arr = explode(';', $m->slots); 
	  foreach ($arr as $key=>$val)
	  if (!empty($val))
	  $ret[$key] = $val; 
	  }
	  else {
		  return $this->getDeliveryTimeSlots($m); 
	  }
	  return $ret; 
	}
	
	
	function getSlotFromConfig($cfg, $method) {
			
			$slots = array(); 
			$slot_names = $this->getPickupSlots($method); 
			
			
			foreach ($cfg as $key=>$val) {
				
				$e = explode('_', $key); 
				$slots[$e[1]] = $slot_names[$e[1]]; 
			

				//only one slot per config: 
				return $slot_names[$e[1]];
				//config looks like:  $newa[$v['route'].'_'.$v['slot'].'_'.$v['vehicle']] = $v['vehicle']; 
			}
			
			return $slots; 
			
		}
	
	function getPickupSlots($m)
	{
		
	  if (!empty($m->pickup_custom_slots)) {
	  $ret = array(); 
	  $arr = explode(';', $m->pickup_slots); 
	  
	  
	  
	  foreach ($arr as $key=>$val)
	  if (!empty($val))
		$ret[$key] = $val; 
	  }
	  else {
		  return $this->getPickupTimeSlots($m); 
	  }
	  
	  
	  return $ret; 
	}
	
	function getMinutes($time)
	{
	  $e = explode(':', $time); 
	  return $e[0]*60+$e[1]; 
	}
	
	//this function calculates displayed time for pickup at the load time
	function checkCustomDeliveryTime($method, &$ct) {
		$pshift = 0; 
		
		if (!empty($method->disable_delivery_today)) {
						
						 $pshift = 1;
					 }
					 else {
						  if ($this->wasToday($method->pickup_end_time)) {
							
							$pshift = 1;
							
							
						 }
					 }
		
		if (!empty($method->custom_slots)) {
				
				$Cday = $Nday = date('N'); //1...7
				
					 
					 //for ($i=$Nday; $i<14+$Nday; $i++) 
				     //check next 14 days for delivery: 
					 for ($i=$pshift; $i<14; $i++) 
					 {
						 /*
						 $ind = $i; 
						 if ($ind > 7) 
							 $ind = ($i % 7);
						 if (empty($ind)) $ind = 7; 
						 */
						 
						 $ct = time() + ($i * 60*24*60); 
						 if (pfHelper::hasDelivery($ct)) {
							 
							 break; 
						 }
					 }
					
					
					  
					  
		}		 
		
	}
	
	
	
	function getNextOpeningDeliveryDate($m, $stamp=false)
	{
	  
		
		
	  $day = date('w'); 
	  
	  $date = date('Y-m-d'); 
	  $i = 0; 
	  
	  $ct = date('H:i'); 
	  $currenttime = $this->getMinutes($ct); 
	  $closingshop = $this->getMinutes($m->free_end_time); 
	  
	  for ($mw = $day;  $mw<($day+7); $mw++)
	  {
	    
	    if ($mw >= 7) $m2 = $mw-7; 
		else $m2 = $mw; 
		
		$day_x = 'day_'.$m2; 
		//echo $i.'_'.$day_x."<br />\n"; 
	    if (empty($m->$day_x))
		{
		  if (!(($i == 0) && ($closingshop < $currenttime)))
		  {
		   $time = time() + (24*60*60*$i); 
		   //echo date('Y-m-d', $time); die(); 
		   if ($stamp) return $time; 
		   return date('Y-m-d', $time); 
		  }
		}
		else
		{
		  
		}
		$i++; 
	  }
	  if ($stamp) return time(); 
	  return $date;
	  
	}
	function getSlotsRendered(&$m, &$disabled_slots=array(), $ct=null)
	{
	  
	  $hidden_select = '<select name="hidden_free_time" id="hidden_free_time" style="display: none;">';
	  $today_free = '<select name="today_free_time" id="today_free_time" style="display: none;">';
	  $no_options = '<select name="no_options" id="no_options" style="display: none;"><option value="-1">'.$m->no_options.'</option></select>';
	  $names = ''; 
	  
	  if (!empty($ct)) {
		  $data = pfHelper::hasDelivery($ct, true); 
		  if ((!empty($data)) && (!empty($data->slots))) {
			  $slots = $data->slots; 
			  $currentDay = date('N', $ct);
		  }
	  }
	  
	  if (empty($slots)) {
	   $currentDay = $this->getNextOpeningDeliveryDate($m); 
	   $slots = $this->getSlots($m); 
	  }	  
	  if (empty($m->default_selected))
	  $html = '<select id="free_time" name="free_time" class="inactive2">'; 
	  else
	  $html = '<select id="free_time" name="free_time" class="isselected2">'; 
	  $set = false; 
	  foreach ($slots as $key=>$val)
	   {
	     $hold = false; 
	     if (!empty($disabled_slots))
		 if (!empty($disabled_slots[$currentDay]))
		 foreach ($disabled_slots[$currentDay] as $key2=>$val2)
		 {
		  // slot_route (route is always zero when starting)
		  if ($val2 == $key.'_0')
          $hold = true; 
		 }
		 if (!$hold)
		 {
		  $html .= '<option value="'.$key.'">'.$val.'</option>';    
		  $names .= '<input type="hidden" id="slot_txt'.$key.'" value="'.htmlentities($val).'" />';    
		  $set = true; 
		 }
		 
		 $hidden_select .= '<option value="'.$key.'">'.$val.'</option>';    
		 $today_free .= '<option value="'.$key.'">'.$val.'</option>';    
		 
		 
	   }
	  if (!$set)
		{
		  $html .= '<option value="-1">'.$m->no_options.'</option>'; 
		  $names .= '<input type="hidden" id="slot_txt-1" value="'.htmlentities($m->no_options).'" />';    
		}
	   
	  $today_free .= '</select>'; 
	  $hidden_select .= '</select>'; 
	  $html .= '</select>'.$hidden_select.$today_free.$no_options.$names; 
	  
	  
	  
	  return $html; 
	}
	public function plgVmOnCheckoutCheckDataShipment($cart)
	 {
	 
	  	if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return null;
		}
		
		$date = JRequest::getVar('free_date', ''); 
		$time = JRequest::getVar('free_time', ''); 
		
		
		$db = JFactory::getDBO(); 
		if (!empty($date) && (!empty($time)))
		{
		$dt =& JFactory::getDate($date.' '.$time.':00');
		$lx = $dt->toUnix(); 
		
		$min = $method->free_disable;
		$ms = $lx - ($min * 60); 
		$mx = $lx + ($min * 60); 
		if (empty($method->custom_slots))
		{
		$q = 'select * from '.$this->_tablename.' where shipment_type = \'free\' and ((delivery_stamp >= '.$ms.') and (delivery_stamp <= '.$mx.')) limit 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		$e = $db->getErrorMsg(); if (!empty($e)) { echo $e; die(); }
		if (!empty($res))
		 {
		   // we have problem
		   $application = JFactory::getApplication();
		   $application->enqueueMessage($method->error_delivery_text, 'error');
		   return false; 
		 }
		}
		}
		$txt = $this->getTexts($method); 
		$delivery_date = JRequest::getVar('delivery_date', $txt); 
		JRequest::setVar('delivery_date', $delivery_date); 
		
	    return true; 
	 }
	
	function plgVmConfirmedOrder (VirtueMartCart $cart, $order) {

		if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return false;
		}
		 
		$x = JRequest::getVar('selected_method', ''); 
		if ($x == 'pickup')
		{
		$date = JRequest::getVar('my_date', ''); 
		$time = JRequest::getVar('pickup_time', ''); 
		}
		else
		{
		$date = JRequest::getVar('free_date', ''); 
		$time = JRequest::getVar('free_time', ''); 
		
		}
		

		if (!$this->selectedThisElement ($method->shipment_element)) {
			return FALSE;
		}
		$values['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
		$values['order_number'] = $order['details']['BT']->order_number;
		$values['virtuemart_shipmentmethod_id'] = $order['details']['BT']->virtuemart_shipmentmethod_id;
		
		$values['shipment_name'] = $this->getOrderShipmentHtml($order['details']['BT']->virtuemart_order_id, $method); 
		

		$values['order_weight'] = $this->getOrderWeight ($cart, $method->weight_unit);
		$values['shipment_weight_unit'] = $method->weight_unit;
		$values['shipment_cost'] = $method->cost;
		$values['shipment_package_fee'] = $method->package_fee;
		$values['tax_id'] = $method->tax_id;
		$values['shipment_type'] = $x; 
		// yyyy-mm-dd
		$mdate = $date; 
		
		
		if (empty($method->custom_slots))
		{
		if (!empty($date) && (!empty($time)))
		{
		$dt =& JFactory::getDate($date.' '.$time.':00');
		$lx = $dt->toUnix(); 
		
		

		$ldate = $this->formatDate($lx); 
		}
		else
		{
		  $lx = '0'; 
		}
		
		$values['delivery_stamp'] = $lx; //$mdate.' '.$time.':00'; 
		}
		else
		{
			if ($x !== 'pickup') {
			 $slots = $this->getSlots($method); 
			 if (isset($slots[$time]))
			 $time = $slots[$time];
			}
			
			/*
		  
		  
		  $dt = JFactory::getDate($date.' 00:01');
		  $lx = $dt->toUnix(); 
		  */
		  $values['delivery_stamp'] = -1; 
		}
		$route_id = JRequest::getInt('route_name', 'none'); 
		$routes = $this->getRoutes($method); 
		$values['route'] = $routes[$route_id]; 
		
		$values['delivery_date'] = $date; 
		$values['delivery_time'] = $time; 
		
		
		
		$this->storePSPluginInternalData ($values);
	
		return TRUE;
	}
    
	function formatDate($lx, $withday=true)
	{
	  /*
	  $lang     = JFactory::getLanguage();
      $tag = $lang->getTag(); 
	  $filename = '';
		$lang->load('', JPATH_SITE, $tag, true);
	  */
	  $month = date("F", $lx); 
	  $month = strtoupper($month);
	  $nl = JText::_($month); 
	  $day = date('l', $lx); 
	  
	  $daynl = JText::_($day); 
	  if (function_exists('mb_ucfirst'))
	  $daynl = mb_ucfirst($daynl); 
	  else $daynl = ucfirst($daynl); 
	  
	  if (function_exists('mb_ucfirst'))
	  $nl = mb_ucfirst($nl); 
	  else $nl = ucfirst($nl); 
	  $day = date('d', $lx); 
	  $year = date('Y', $lx);

	  if (empty($withday))
	  return $day.' '.$nl.' '.$year; 
	  else
	  return $daynl.' '.$day.' '.$nl.' '.$year; 
	 
	}
	function renderPluginName2($virtuemart_order_id)
	{
	  return ''; 
	  return $this->getOrderShipmentHtml($virtuemart_order_id);
	}
	
	
	public function plgVmOnShowOrderBEShipment ($virtuemart_order_id, $virtuemart_shipmentmethod_id) {

		if (!($this->selectedThisByMethodId ($virtuemart_shipmentmethod_id))) {
			return NULL;
		}
		$html = $this->getOrderShipmentHtml ($virtuemart_order_id);
		return $html;
	}

	
	function getTexts($method)
	{
	  $type = JRequest::getVar('selected_method', ''); 
	  if ($type == 'pickup')
		 {
		   $date = JRequest::getVar('my_date', ''); 
		   $dt = JFactory::getDate($date);
		   $lx = $dt->toUnix(); 
		   $ldate = $this->formatDate($lx); 
		   $time = JRequest::getVar('pickup_time', ''); 
		   if (!empty($method->pickup_custom_slots)) {
		   $pickup_slots = $this->getPickupSlots($method); 
		   if (!empty($pickup_slots)) {
			   $time = $pickup_slots[$time]; 
		   }
		   }
		   if (!empty($method)) $t = $method->pickup_text;
		   else
		   $t = $this->pickup_text; 
		   $t = str_replace('{date}', $ldate, $t); 
		   $t = str_replace('{time}', $time, $t); 
		   
		   
		    $route_id = JRequest::getInt('route_name', 'none'); 
			if ($route_id !== 'none') {
			$routes = $this->getRoutes($method); 
			$route_name = $routes[$route_id]; 
			$t = str_replace('{route_name}', $route_name, $t); 
			}
		   return $t; 
		 }
		else
		 {
		  $date = JRequest::getVar('free_date', '');
		  $dt =& JFactory::getDate($date);
		  $lx = $dt->toUnix(); 
		  $ldate = $this->formatDate($lx); 		  
		  $time = JRequest::getVar('free_time', ''); 
		  if (!empty($method->custom_slots))
		  {
		    $slots = $this->getSlots($method); 
			$time = $slots[$time]; 
		  }
		   if (!empty($method)) $t = $method->delivery_text;
		   else
		   $t = $this->delivery_text; 
		   $t = str_replace('{date}', $ldate, $t); 
		   $t = str_replace('{time}', $time, $t); 
		   $route_id = JRequest::getInt('route_name', 'none'); 
		  $routes = $this->getRoutes($method); 
		  $route_name = $routes[$route_id]; 
		   
		   $t = str_replace('{route_name}', $route_name, $t); 
		   
		   
		  		   //$r = JRequest::getVar('route_name', ''); 		  
				   //if (!empty($r))		   
				   //$t .= '<br />'.$r; 		   
		   return $t; 

		 }
	}
	
	//this function calculates displayed time for pickup at the load time
	function checkCustomPickupTime($method, &$ct) {
		if (!empty($method->pickup_custom_slots)) {
		 $rconfig = pfHelper::getPickupSlots($this, $method);
				$Cday = $Nday = date('N'); //1...7
				if (!empty($method->disable_pickup_today)) {
						 $Nday++; 
						 $pshift = 1;
					 }
					 else {
						  if ($this->wasToday($method->pickup_end_time)) {
							$Nday++; 
							$pshift = 1;
							
							
						 }
					 }
					 
					 for ($i=$Nday; $i<14+$Nday; $i++) {
						 $ind = $i; 
						 if ($ind > 7) 
							 $ind = ($i % 7);
						 if (empty($ind)) $ind = 7; 
						 
						 
						 if (!empty($rconfig[$ind])) {
							
							 break; 
						 }
					 }
					
					
					  
					  $pshift = $i - $Cday;
					  
					  
					  if (!empty($pshift)) {
						  $ct = time() + 60*24*60*$pshift;
						  
					  }
		}		 
	}
	
	/**
	 * @param $virtuemart_order_id
	 * @return string
	 */
	function getOrderShipmentHtml ($virtuemart_order_id, $method=null) {
		if (!is_numeric($virtuemart_order_id)) return;
		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` '
			. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
		$db->setQuery ($q);
		$new = false; 
		
		if (!($shipinfo = $db->loadObject ())) {
			$new = true; 
		}
		else
		 return $shipinfo->shipment_name; 

		 
		$type = JRequest::getVar('selected_method', ''); 
		if (empty($type)) return ''; 
		 
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		}
		/*
		not needed yet
		$currency = CurrencyDisplay::getInstance ();
		$tax = ShopFunctions::getTaxByID ($shipinfo->tax_id);
		$taxDisplay = is_array ($tax) ? $tax['calc_value'] . ' ' . $tax['calc_value_mathop'] : $shipinfo->tax_id;
		$taxDisplay = ($taxDisplay == -1) ? JText::_ ('COM_VIRTUEMART_PRODUCT_TAX_NONE') : $taxDisplay;
		*/
		
		if ($new && (empty($shipinfo)))
		{
		  $q = "select * from #__virtuemart_shipmentmethods where shipment_element = 'pickup_or_free' limit 0,1"; 
		  $db->setQuery($q); 
		  $shipinfo = $db->loadObject ();
		}
		
		if (empty($method) && (!empty($shipinfo)))
		$method = $this->getVmPluginMethod ($shipinfo->virtuemart_shipmentmethod_id);
		
		
		if (empty($method)) return;

		if (!$this->selectedThisElement ($method->shipment_element)) {
			return false;
		}
		
		
		return $this->getTexts($method); 
		$html = '<table class="adminlist">' . "\n";
		$html .= $this->getHtmlHeaderBE ();
		$html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_SHIPPING_NAME', $shipinfo->shipment_name);
		if (!empty($shipinfo->order_weight))
		$html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_WEIGHT', $shipinfo->order_weight . ' ' . ShopFunctions::renderWeightUnit ($shipinfo->shipment_weight_unit));
		if (!empty($shipinfo->shipment_cost))
		$html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_COST', $currency->priceDisplay ($shipinfo->shipment_cost));
		if (!empty($shipinfo->shipment_package_fee))
		$html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_PACKAGE_FEE', $currency->priceDisplay ($shipinfo->shipment_package_fee));
		$html .= $this->getHtmlRowBE ('WEIGHT_COUNTRIES_TAX', $taxDisplay);
		$html .= '</table>' . "\n";

		return $html;
	}

	
	function getCosts (VirtueMartCart $cart, $method, $cart_prices) {

		if ($method->free_shipment && $cart_prices['salesPrice'] >= $method->free_shipment) {
		return 0;
		} else {
		    if (empty($method->default_selected))
			$default = 'pickup'; 
			else
			$default = 'free'; 
			
		    $service = JRequest::getVar('service', $default); 
			
			if ($service == 'free')
			return $method->cost + $method->package_fee;
			else return 0; 
		}
		
	}
	
	public function plgGetOpcData(&$data, &$cart, $object)
	{
	   if ($this->getPluginMethods($cart->vendorId) === 0) {
		  return false;
		}
		foreach ($this->methods as $method)
		{
		
		$address = $cart->BT; 
		$what = ''; 
		$nbproducts_cond = $this->_nbproductsCond ($cart, $method, $address['zip'], $what);
		$object->where = 'msg_custom_pf_msg'; 
		$object->id = 'custom_pf_msg'; 
		$object->data = ''; 
		if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		{
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		$cond = $this->free_checkConditions($cart, $method, array());
		
		if (($op_default_zip == $address['zip']) || ($cond))
		  {
		    $data[] = $object; 
			return true; 
		  }
		}
		if (empty($address['zip'])) 
		$object->data = ''; 
		else
		if ($nbproducts_cond)
		 {
		   
		   $object->data = ''; 
		 }
		 else
		 {
		    $cr = $this->_validateMyCoupons($cart, $method); 
			if ($cr === false)
			$what = 'coupon_problem'; 
			if ($what == 'coupon_problem')
			{
			
			  $object->data = $method->coupon_error_text; 
			}
			else
			if ($what == 'zip_list')
			 {
			  $object->data = $method->zip_list_error_here; 
			 }
			else
			if ($what == 'zip_range')
			{
			  $object->data = $method->zip_range_error_here; 
			}
			else
			{
			  $object->data = $method->zip_general_error;
			}
		    
		 }
		break; 
		}
		$data[] = $object; 
	}
	
	
	function checkConditions ($cart, $method, $cart_prices) {
		// this method will always be shown
		return true; 
	}
	
	function free_checkConditions ($cart, $method, $cart_prices, $debug=false) {
		$this->convert ($method);
		//first let's validate the coupon 
		
		
		if (!empty($cart->couponCode))
			 {
			 $nbproducts = $this->_countProducts($cart); 
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free))
			 return true; 
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free5))
			 {
			 if ($nbproducts>=5)
			 return true; 
			 }
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free10))
			 if ($nbproducts>=10)
			 return true; 
			 }
	
		$orderWeight = $this->getOrderWeight ($cart, $method->weight_unit);
			
		
		//$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		$address = $cart->BT; 
		// only BT address is validated
		//$address = $cart->BT; 
		
		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array ($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}
		// probably did not gave his BT:ST address
		if (!is_array ($address)) {
			// there are some address dependant conditions, redirect then
			
			$address = array();
			$address['zip'] = 0;
			$address['virtuemart_country_id'] = 0;
		}
		
		if (!isset($address['zip'])) {
			$address['zip'] = 0;
		}		$address['zip'] = trim($address['zip']); 		if (!is_numeric($address['zip'])) $address['zip'] = 0; 
		
		$weight_cond = $this->_weightCond ($orderWeight, $method);
		
		$what = ''; 
		$nbproducts_cond = $this->_nbproductsCond ($cart, $method, $address['zip'], $what);
		
		
		if (empty($cart_prices)) 
		{
		if (!empty($cart->pricesUnformatted))
		$cart_prices = $cart->pricesUnformatted; 
		else $orderamount_cond = 1;
		}
		
		if (empty($orderamount_cond))
		$orderamount_cond = $this->_orderamountCond ($cart_prices, $method);
		

		$zip_cond = 1; 
		

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}
		
		
		
		if ((empty($countries) || in_array ($address['virtuemart_country_id'], $countries) )) {

		

			$allconditions = (int) $weight_cond + (int)$zip_cond + (int)$nbproducts_cond + (int)$orderamount_cond;
			if($allconditions === 4){
			
				return true;
			} else {
			
				return false;
			}
			
			
		}
		
		
		return false;
	}

	
	function convert (&$method) {

		//$method->weight_start = (float) $method->weight_start;
		//$method->weight_stop = (float) $method->weight_stop;
		$method->orderamount_start = (float)$method->orderamount_start;
		$method->orderamount_stop = (float)$method->orderamount_stop;
		$method->zip_start = (int)$method->zip_start;
		$method->zip_stop = (int)$method->zip_stop;
		$method->nbproducts_start = (int)$method->nbproducts_start;
		//$method->nbproducts_stop = (int)$method->nbproducts_stop;
		$method->free_shipment = (float)$method->free_shipment;
	}

	
	function _weightCond ($orderWeight, $method) {
	  
	    if (empty($method->weight_start)) return true; 
	    if (empty($method->weight_stop)) $method->weight_stop = 99999999; 
		
		$weight_cond = (($orderWeight >= $method->weight_start AND $orderWeight <= $method->weight_stop)
			OR
			($method->weight_start <= $orderWeight AND $method->weight_stop === ''));

		

		return $weight_cond;
	}
	function _countProducts($cart)
	{
	  $nbproducts = 0;
		foreach ($cart->products as $product) {
			$nbproducts += $product->quantity;
		}
		return $nbproducts; 
	}
	
	function _nbproductsCond ($cart, $method, $zip, &$what) {
		
		// nbproducts_start_ziplist, nbproducts_start
		// zip_start, zip_stop, zip_range
		$nbproducts = $this->_countProducts($cart); 
		
		// nbproducts_start per zip range from-to
		// nbproducts_start_ziplist per comma separated list
		
		
		
		// if not configured
		// if no product n. limitation
		if (empty($method->nbproducts_start_ziplist) && (empty($method->nbproducts_start))) 
		{
		return true; 
		}
		
		$zip = trim($zip); 
		$zip2 = ''; 
		if (!ctype_digit($zip))
		 {
		   for ($i=0; $i<strlen($zip); $i++)
		    {
			  $n = substr($zip, $i, 1); 
			  if (ctype_digit($n))
			   $zip2 .= $n; 
			}
			$zip = $zip2; 
			
		 }
	
		if (!empty($method->zip_range))
		 {
		    $a = explode(',', $method->zip_range); 
			if (!empty($a))
			 {
			   foreach ($a as $k=>$v)
			    {
				  $a[$k] = trim($v);
				}
			   if (in_array($zip, $a)) 
			    {
				if ($nbproducts >= $method->nbproducts_start_ziplist)
				 {
				  $what = 'zip_list'; 
				  return true; 
				  }
				  else
				  {
				  $what = 'zip_list'; 
				  return false; 
				  }
				}
			 }
		 }
		 
		 if (isset($method->zip_range1))
		 {
		  $method->zip_range1 = str_replace(' ', '', $method->zip_range1); 
		  $zipr2 = explode(',', $method->zip_range1); 
		 }
		 else 
		 $zipr2 = array(); 
		 
		 
		 if ((!empty($method->zip_stop)))
		  {
		  	if (empty($method->zip_start)) $method->zip_start = 0; 
			if (is_numeric($zip))
		    if ((($zip >= $method->zip_start) && ($zip <= $method->zip_stop)) || (in_array($zip, $zipr2)))
		    if ($nbproducts >= $method->nbproducts_start)
			 {
			   $what = 'zip_range'; 
			   return true; 
			 }
			 else
			 {
			    $what = 'zip_range'; 
			    return false; 
			 }
		  }
		  
		// if no zip limitation
		if (empty($method->zip_start) && (empty($method->zip_stop)) && (empty($method->zip_range))) 
		{
		  if (!empty($method->nbproducts_start_ziplist))
			{
			  if ($nbproducts >= $method->nbproducts_start_ziplist) 
			  {
			  $what = 'zip_range'; 
			  return true; 
			  }
			  else 
			  {
			  $what = 'zip_range'; 
			  return false; 
			  }
			}
		  else
		  if (!empty($method->nbproducts_start))
		    {
			  if ($nbproducts >= $method->nbproducts_start) 
			  {
			  $what = 'zip_range'; 
			  return true; 
			  }
			  else 
			  {
			  $what = 'zip_range'; 
			  return false;
			  }
			}
		  $what = 'zip_range'; 
		  return true; 
		}

		
		return false;
	}

	
	function _orderamountCond ($cart_prices, $method) {

		if (!isset($method->orderamount_start) AND !isset($method->orderamount_stop)) {
			return true;
		}
		if ($cart_prices['salesPrice']) {
			$orderamount_cond = ($cart_prices['salesPrice'] >= $method->orderamount_start AND $cart_prices['salesPrice'] <= $method->orderamount_stop
				OR
				($method->orderamount_start <= $cart_prices['salesPrice'] AND ($method->orderamount_stop == 0)));
		} else {
			$orderamount_cond = true;
		}

		return $orderamount_cond;
	}

	
	function _zipCond ($zip, $method) {

		$zip = (int)$zip;
		$zip_cond = true;
		if (!empty($zip) ) {

			if(!empty($method->zip_start) and !empty( $method->zip_stop)){
				$zip_cond = (($zip >= $method->zip_start AND $zip <= $method->zip_stop));
			} else if (!empty($method->zip_start)) {
				$zip_cond = ($zip >= $method->zip_start);
			} else if (!empty($method->zip_stop)) {
				$zip_cond = ($zip <= $method->zip_stop);
			}
		} else if(!empty($method->zip_start) or !empty( $method->zip_stop)){
			$zip_cond = false;
		}

		return $zip_cond;
	}

	
	
	function plgVmOnStoreInstallShipmentPluginTable ($jplugin_id) {

		return $this->onStoreInstallPluginTable ($jplugin_id);
	}

	
	public function plgVmOnSelectCheckShipment (VirtueMartCart &$cart) {

		return $this->OnSelectCheck ($cart);
	}
	
	function _validateMyCoupons($cart, $method)
	{
	  $nbproducts = $this->_countProducts($cart); 
	  		if (!empty($cart->couponCode))
			 {
			 
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free))
			 {
			 return true; 
			
			 }
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free5))
			 {
			 if ($nbproducts>=5)
			 return true; 
			 else return false; 
			 }
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free10))
			 {
			 
			 if ($nbproducts>=10)
			 return true; 
			 else return false; 
			 }
			 
			 }
		return null; 
	}
	// returns false if other coupon is used
	// return 0 when no coupon is used
	function _checkMyCoupons($cart, $method)
	{
	  		if (!empty($cart->couponCode))
			 {
			 
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free))
			 return true; 
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free5))
			 return true; 
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free10))
			 return true; 
			 
			 return false; 
			 }
			 return 0; 
			 

	}
	
	function _displaySingleVehicle(VirtueMartCart $cart, $selected = 0, &$htmlIn)
	{
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'pickup_or_free'.DIRECTORY_SEPARATOR.'pfs_display.php'); 
		$pfDisplay = new pfDisplay(); 
		$methods = $this->methods; 
		$tableName = $this->_tablename; 
		return $pfDisplay->display($cart, $selected, $htmlIn, $this, $methods, $tableName); 
	}
	
	
	function _displayMultiVehicle(&$cart, $selected, &$htmlIn)
	{
	    //pfs and pf are the same: March 2017	
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'pickup_or_free'.DIRECTORY_SEPARATOR.'pfs_display.php'); 
		$pfDisplay = new pfDisplay(); 
		$methods = $this->methods; 
		$tableName = $this->_tablename; 
		return $pfDisplay->display($cart, $selected, $htmlIn, $this, $methods, $tableName); 
	    

	}
	
	
	public function plgVmDisplayListFEShipment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {
		
		if ($this->getPluginMethods($cart->vendorId) === 0) {
		  return false;
		}
		
		$m = reset($this->methods);
		if (!empty($m->mode)) 
		{
		 return $this->_displaySingleVehicle($cart, $selected, $htmlIn); 
		}
		else
		{
		  return $this->_displayMultiVehicle($cart, $selected, $htmlIn); 
		}
		 

	

	}

	
	
	
	public function plgVmonSelectedCalculatePriceShipment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

		return $this->onSelectedCalculatePrice ($cart, $cart_prices, $cart_prices_name);
	}
	function plgVmOnCheckAutomaticSelectedShipment (VirtueMartCart $cart, array $cart_prices = array(), &$shipCounter) {

		if ($shipCounter > 1) {
			return 0;
		}
		return $this->onCheckAutomaticSelected ($cart, $cart_prices, $shipCounter);
	}

	
	
	function plgVmonShowOrderPrint ($order_number, $method_id) {

		return $this->onShowOrderPrint ($order_number, $method_id);
	}


	function plgVmDeclarePluginParamsShipment ($name, $id, &$dataOld) {
		return $this->declarePluginParams ('shipment', $name, $id, $dataOld);
	}

	function plgVmDeclarePluginParamsShipmentVM3 (&$data) {
		return $this->declarePluginParams ('shipment', $data);
	}
	
	function plgVmSetOnTablePluginParamsShipment ($name, $id, &$table) {

		return $this->setOnTablePluginParams ($name, $id, $table);
	}

		/*
	function plgVmOnUpdateOrderShipment(&$data, $old_order_status)
	{
	 if (!is_object($data)) return;
	 $t = $this->getTexts(); 
	 if (!empty($t) && empty($data->delivery_date)) $data->delivery_date = $t; 
	}
	
	public function plgVmOnUpdateOrder($psType, $_formData) {
	return null;
	}
	 
	public function plgVmOnUpdateOrderLine($psType, $_formData) {
	return null;
	}
	
	public function plgVmOnEditOrderLineBE($psType, $_orderId, $_lineId) {
	return null;
	}
	
	public function plgVmOnShowOrderLineFE($psType, $_orderId, $_lineId) {
	return null;
	}
	
	function plgVmOnResponseReceived($psType, &$virtuemart_order_id, &$html) {
	return null;
	}
	 
	public function plgVmOnCheckoutCheckData($psType, VirtueMartCart $cart) {
	return null;
	}
	 */

	
}
