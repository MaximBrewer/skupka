<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class SendcloudHelper {
  public static $lastError; 
  
 
  public static $cache; 
  public static function getShippingMethods($method, $id=0)
  {
	  		 
	  if (empty($method->api_key)) 
	  {

		  JFactory::getApplication()->enqueueMessage(__LINE__.': '.'Please fill the API credentials and click save, and then choose your shipping method below.'); 
		  return array(); 
	  }
	  
	  require_once(__DIR__.DIRECTORY_SEPARATOR.'SendCloudApi.php'); 
	  $api_key = $method->api_key; 
	  $api_secret = $method->api_secret; 
	  
	  $debug = $method->is_live; 
	  if (!empty($debug))
	  {		  
        $mode = 'live'; 
	  }
	  else {
		  $mode = 'live'; 
	  }
	  
	  $cache_key = $mode.$api_key.$api_secret.$id; 
	  if (isset(self::$cache[$cache_key])) return self::$cache[$cache_key]; 
	  
	  try
	  {
	  $api = new SendcloudApi($mode, $api_key,$api_secret);
	  $api->setMethod('get'); 
	  if (empty($id))
	  {
	   $shipping_methods = $api->shipping_methods->get();
	   foreach ($shipping_methods as $k=>&$v)
	   {
		   self::updateCountries($v); 
	   }
	  }
	  else
	  {
		  $shipping_methods = $api->shipping_methods->get($id);
		  self::updateCountries($shipping_methods); 
	  }
	  
	  self::$cache[$cache_key] = $shipping_methods; 
	  
	  return $shipping_methods; 
	  }
	  catch (Exception $e)
	  {
		  $msg = 'Sendcloud Debug: '.$e->code.': '.$e->message; 
		  //JFactory::getApplication()->enqueueMessage($msg, 'error'); 
		  
		  if (!empty($method->debug)) {
			  $x = debug_backtrace(); 
			  $xt = "<br />\n"; 
			  foreach ($x as $l) $xt .= @$l['file'].' '.@$l['line']."<br />\n"; 
		   JFactory::getApplication()->enqueueMessage($msg.$xt, 'error'); 
		  }
		  
		  
	  }
	  
	  self::$cache[$cache_key] = array(); 
	  return array(); 
  }
  
  public static function changeParcel($method, $parcel_id, $data)
  {
	  
	  static $justOnce; 
	  if (!empty($justOnce)) return $justOnce;
	  
	  
	   if (empty($method->api_key)) 
	  {

		  JFactory::getApplication()->enqueueMessage('Please fill the API credentials and click save, and then choose your shipping method below.'); 
		  return array(); 
	  }
	  
	  require_once(__DIR__.DIRECTORY_SEPARATOR.'SendCloudApi.php'); 
	  $api_key = $method->api_key; 
	  $api_secret = $method->api_secret; 
	  
	  $debug = $method->is_live; 
	  if (!empty($debug))
	  {		  
        $mode = 'live'; 
	  }
	  else {
		  $mode = 'live'; 
	  }
	  
	 
	  
	  try
	  {
	  $api = new SendcloudApi($mode, $api_key,$api_secret);
	  $api->setMethod('put'); 
	  $data['id'] = (string)$parcel_id; 
	  $parcel = $api->parcels->update($parcel_id,
			$data
		);
		  
		  $justOnce = $parcel; 
		  
	      return $parcel; 
	  }
	  catch (Exception $e)
	  {
		  $msg = $e->code.': '.$e->message; 
		  //JFactory::getApplication()->enqueueMessage($msg, 'error'); 
		  
		  if (!empty($method->debug)) {
			  $x = debug_backtrace(); 
			  $xt = "<br />\n"; 
			  foreach ($x as $l) $xt .= $l['file'].' '.$l['line']."<br />\n"; 
		   JFactory::getApplication()->enqueueMessage($msg.$xt, 'error'); 
		  }
		  
	  }
	  
	  return false; 
	  
	  
	 
		
  }
  
  public static function getFirstMethod($cid=0)
  {
	  $db = JFactory::getDBO(); 
	
		$q = 'select `shipment_params` from `#__virtuemart_shipmentmethods` where `shipment_element` = \'rupostel_sendcloud\'  '; 
		
		if (!empty($cid)) {
		 $q .= ' and virtuemart_shipmentmethod_id = '.$cid; 
		}
		else
		{
			$q .= ' and published = 1 '; 
		}
		
		$db->setQuery($q); 
		$params = $db->loadResult(); 
		
		if (empty($params))
		{
			$q = 'select `shipment_params` from `#__virtuemart_shipmentmethods` where `shipment_element` = \'rupostel_sendcloud\'  '; 
			$db->setQuery($q); 
			$params = $db->loadResult(); 
			
			if (empty($params)) return false;
		}
		
		
		$obj = new stdClass(); 
		
		$e = $db->getErrorMsg(); 
		if (!empty($e)){
		 JFactory::getApplication()->enqueueMessage($e); 
		  return; 
		}
		$err = true; 
		if (empty($params)) {
			
			return; 
			$err = true; 
		}
		else
		{
		$a = explode('|', $params); 
		
		foreach ($a as $p)
		 {
		    $a2 = explode('=', $p); 
			if (!empty($a2) && (count($a2)==2))
			 {
			   $key = $a2[0]; 
			   $obj->{$key} = json_decode($a2[1]); 
			 }
		 }
		 return $obj; 
		}
		
		 
  }
  
  
  public static function registerParcel($method, $data)
  {
	  if (empty($method->api_key)) 
	  {

		  JFactory::getApplication()->enqueueMessage(__LINE__.': '.'Please fill the API credentials and click save, and then choose your shipping method below.'); 
		  return array(); 
	  }
	  
	  require_once(__DIR__.DIRECTORY_SEPARATOR.'SendCloudApi.php'); 
	  $api_key = $method->api_key; 
	  $api_secret = $method->api_secret; 
	  
	  $debug = $method->is_live; 
	  if (!empty($debug))
	  {		  
        $mode = 'live'; 
	  }
	  else {
		  $mode = 'live'; 
	  }
	  
	 
	  
	  try
	  {
	  $api = new SendcloudApi($mode, $api_key,$api_secret);
	 
	      $api->setMethod('post'); 
		  $resp = $api->parcels->create($data);
		 
	      return $resp; 
	  }
	  catch (Exception $e)
	  {
		 
		  $msg = $e->code.': '.$e->message; 
		  $xt = ''; 
		  if (!empty($method->debug)) {
			  $x = debug_backtrace(); 
			  $xt = "<br />\n"; 
			  foreach ($x as $l) $xt .= $l['file'].' '.$l['line']."<br />\n"; 
		   
		  }
		  JFactory::getApplication()->enqueueMessage($msg.$xt, 'error'); 
	  }
	  
	  return false; 
	  
  }
  
  
  public static function getParcel($method, $parcel_id)
  {
	  
	  if (empty($method->api_key)) 
	  {

		  JFactory::getApplication()->enqueueMessage(__LINE__.': '.'Please fill the API credentials and click save, and then choose your shipping method below.'); 
		  return array(); 
	  }
	  
	  require_once(__DIR__.DIRECTORY_SEPARATOR.'SendCloudApi.php'); 
	  $api_key = $method->api_key; 
	  $api_secret = $method->api_secret; 
	  
	  $debug = $method->is_live; 
	  if (!empty($debug))
	  {		  
        $mode = 'live'; 
	  }
	  else {
		  $mode = 'live'; 
	  }
	  
	 
	  
	  try
	  {
	  $api = new SendcloudApi($mode, $api_key,$api_secret);
	  $api->setMethod('get'); 
	     $parcel = $api->parcels->get($parcel_id);
		  
	      return $parcel; 
	  }
	  catch (Exception $e)
	  {
		  $msg = $e->code.': '.$e->message; 
		  //JFactory::getApplication()->enqueueMessage($msg, 'error'); 
		  
		  if (!empty($method->debug)) {
			  $x = debug_backtrace(); 
			  $xt = "<br />\n"; 
			  foreach ($x as $l) $xt .= $l['file'].' '.$l['line']."<br />\n"; 
		   JFactory::getApplication()->enqueueMessage($msg.$xt, 'error'); 
		  }
		  
	  }
	  
	  return false; 
  }

   public static function getLabels($method, $parcel_id, $force=false)
  {
	  if (empty($method->api_key)) 
	  {

		  JFactory::getApplication()->enqueueMessage('Please fill the API credentials and click save, and then choose your shipping method below.'); 
		  return array(); 
	  }
	  
	  require_once(__DIR__.DIRECTORY_SEPARATOR.'SendCloudApi.php'); 
	  $api_key = $method->api_key; 
	  $api_secret = $method->api_secret; 
	  
	  $debug = $method->is_live; 
	  if (!empty($debug))
	  {		  
        $mode = 'live'; 
	  }
	  else {
		  $mode = 'live'; 
	  }
	  
	 
	  
	  try
	  {
	  $api = new SendcloudApi($mode, $api_key,$api_secret);
	  
	      if ((!empty($api->labels)) && (empty($force)))
		  {
			  $api->setMethod('get'); 
		  $resp = $api->labels->get($parcel_id);
		  }
		  else
		  {
			  $api->setMethod('post'); 
			  $resp = $api->label->create(array('parcels' => array($parcel_id)));
			  
			  
		  }
		  
	      return $resp; 
	  }
	  catch (Exception $e)
	  {
		  $msg = $e->code.': '.$e->message; 
		  //JFactory::getApplication()->enqueueMessage($msg, 'error'); 
		  
		  if (!empty($method->debug)) {
			  $x = debug_backtrace(); 
			  $xt = "<br />\n"; 
			  foreach ($x as $l) $xt .= $l['file'].' '.$l['line']."<br />\n"; 
		   JFactory::getApplication()->enqueueMessage($msg.$xt, 'error'); 
		  }
		  
	  }
	  
	  return false; 
	  
  }
  
   private static function updateCountries(&$shipping)
   {
	  $c2 = array(); 
	  if (!empty($shipping['countries']))
	  {
		 $countries = self::_getCountries($shipping); 
		 foreach ($shipping['countries'] as $k=>&$v)
		 {
			 if (isset($countries[$v['iso_2']]))
			 {
				 $v['virtuemart_country_id'] = $countries[$v['iso_2']]; 
				 $c2[$v['virtuemart_country_id']] = $v; 
			 }
		 }
	  }
	  $shipping['countries'] = $c2; 
	  
   }
   public static function _getCountries($shipping)
	{
		$db = JFactory::getDBO(); 
		$c = array(); 
								foreach ($shipping['countries'] as $c2)
								{
									$c[] = "'".$db->escape($c2['iso_2'])."'"; 
								}
								$q_in = ' ('.implode(',', $c).')'; 
       $q = 'select `virtuemart_country_id`, `country_2_code` from `#__virtuemart_countries` where `published` = 1 and `country_2_code` IN '.$q_in;	
	   $db->setQuery($q); 	   
	   $res = $db->loadAssocList(); 
	   if (!empty($res))
	   {
		   $ret = array(); 
		   foreach ($res as $k=>$v)
		   {
			   $c2 = $v['country_2_code']; 
			   $ret[$c2] = (int)$v['virtuemart_country_id']; 
		   }
		   return $ret; 
		   
	   }
	   return array(); 
	   
								
	}
 
  
  
  
  private static function getCache($hash)
  {
	  jimport('joomla.filesystem.file');
	  $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	  
	   
	   jimport('joomla.filesystem.folder');
	   
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud')===false) 
		   {
			
		   }
		 }
		 
		$ts_filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'.DIRECTORY_SEPARATOR.'timestamp.txt';
		$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'.DIRECTORY_SEPARATOR.$hash.'.json';
		
		if (file_exists($filename))
		{
		$data = file_get_contents($filename); 
		$datat = json_decode($data, true); 
	  
	  if (!empty($datat))
	  {
	  $e = $datat['errors']; 
	  if (!empty($e))
	  {
		  self::clearCache($hash); 
		  return ''; 
	  }
	  }
		
		if (!empty($datat))
		return $datat; 
		
		}
		
		return ''; 
	
		
	   
  }
  public static function clearCache($hash)
  {
	  jimport('joomla.filesystem.file');
	   $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	  
	$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'.DIRECTORY_SEPARATOR.$hash.'.json';
	  if (file_exists($filename))
	  {
		  @JFile::delete($filename); 
	  }
	  //JFile::delete($ts_filename);
	  
	  
  }
  
  private static function writeCache($hash, $data)
  {
	  if (empty($data)) return; 
	  
	  $time = time(); 
	  			 jimport('joomla.filesystem.file');
	   jimport('joomla.filesystem.folder');
	   
	    $hash = JFile::makeSafe($hash); 
	  if (empty($hash)) return ''; 
	   
	   if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'))
		 {
		   if (@JFolder::create(JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud')===false) 
		   {
			
		   }
		 }
		 
		$ts_filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'.DIRECTORY_SEPARATOR.'timestamp.txt';
		$filename = JPATH_ROOT.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'sendcloud'.DIRECTORY_SEPARATOR.$hash.'.json';
		
		
				if (!empty($data))
				 {
				  JFile::write($filename.'.tmp', $data);
				  JFile::move($filename.'.tmp', $filename); 
				  JFile::write($ts_filename.'.tmp', $time);
				  JFile::move($ts_filename.'.tmp', $ts_filename); 
				  
				 }

  }
  
  

   
   
}