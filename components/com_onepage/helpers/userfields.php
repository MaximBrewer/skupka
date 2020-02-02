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
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCUserFields {
 public static function handleDisabledEmail(&$cart) {
	 $user = JFactory::getUser(); 
	 if (empty($user->id)) {
		 if (empty($cart->BT['email'])) {
			 $opc_disable_customer_email_address = OPCconfig::get('opc_disable_customer_email_address', ''); 
			 if (!empty($opc_disable_customer_email_address)) {
				 JRequest::setVar('email', $opc_disable_customer_email_address); 
				 $cart->BT['email'] = $opc_disable_customer_email_address; 
			 }
		 }
	 }
	 
	 
	 
	 
 }
 public static function getUserinfoid($user_id, $type='BT') {
	 $db = JFactory::getDBO(); 
	 
	 $q = 'select `virtuemart_userinfo_id` from `#__virtuemart_userinfos` where virtuemart_user_id = '.(int)$user_id.' and address_type = "'.$db->escape($type).'" limit 0,1'; 
		$db->setQuery($q); 
		$virtuemart_userinfo_id = $db->loadResult(); 
		$virtuemart_userinfo_id = (int)$virtuemart_userinfo_id; 
		return $virtuemart_userinfo_id; 
 }
 
 public static function getAcyFields() {
			
			$ret = array(); 
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'regacymailing')) 
			{
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');  
					if (OPCmini::tableExists('acymailing_subscriber')) {
						$db = JFactory::getDBO(); 
						$t = $db->getPrefix().'acymailing_subscriber';
						$ret = OPCmini::getColumns($t); 
						$core = array ( 'subid' => 'subid', 'email' => 'email', 'userid' => 'userid', 'name' => 'name', 'created' => 'created', 'confirmed' => 'confirmed', 'enabled' => 'enabled', 'accept' => 'accept', 'ip' => 'ip', 'html' => 'html', 'key' => 'key', 'confirmed_date' => 'confirmed_date', 'confirmed_ip' => 'confirmed_ip', 'lastopen_date' => 'lastopen_date', 'lastopen_ip' => 'lastopen_ip', 'lastclick_date' => 'lastclick_date', 'lastsent_date' => 'lastsent_date', 'source' => 'source' );
						foreach ($core as $v) {
							unset($ret[$v]); 
						}
						
						
					}
			}
			return $ret; 
			
		}
		
	public static function transformAcyFields($cart) {
		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');  
	  if (class_exists('plgSystemRegacymailing'))
	  {
		  
		  if (OPCmini::tableExists('acymailing_subscriber')) {
		 $acysub = JRequest::getVar('acysub', array(), '', 'array');
		 if (!empty($acysub)) {
		$session = JFactory::getSession();
		if(!empty($acysub)){
			$session->set('acysub', $acysub);
		}

		$acysubhidden = JRequest::getVar('acysubhidden');
		if(!empty($acysubhidden)){
			$session->set('acysubhidden', $acysubhidden);
		}
		
		$regacy = JRequest::getVar('regacy', array(), '', 'array');
		
		if(!empty($regacy)){
			$session->set('regacy', $regacy);
		}
		else {
		$arr = $cart->BT; 
		$db = JFactory::getDBO(); 
		$t = $db->getPrefix().'acymailing_subscriber';
		$res = self::getAcyFields(); 
		$na = array(); 
		foreach ($arr as $k=>$v) {
		   $default = ''; 
		   $con = OPCconfig::getValue('acymailing_fields', $k, 0, $default); 
		   if (!empty($con))
		   if (isset($res[$con])) {
			   $na[$con] = $v; 
		   }
		}
		
		
			
			$session->set('regacy', $na);
			JRequest::setVar('regacy', $na); 
		}
		 }
		  
		  
		  }
		   
	  }
		   
		   
		
	}
 
 public static function getUserfTypes() {
   $ignore = array(
   'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___GETSTFIELDS___RENDERONEPAGE___DISPLAY___DISPLAY___X',
   'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___GETREGISTRATIONHHTML___GETREGISTRATIONHHTML___RENDERONEPAGE___DISPLAY___X', 
   'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___GETUSERFIELDS___GETJAVASCRIPT___GETJAVASCRIPT___RENDERONEPAGE___X',
	'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___GETCARTFIELDS___RENDERONEPAGE___DISPLAY___DISPLAY___X',
  'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___SETADDRESS___GETSHIPPING___RENDERONEPAGE___DISPLAY___X',
	'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___GETBTFIELDS___RENDERONEPAGE___DISPLAY___DISPLAY___X',
   'COM_ONEPAGE_USERFIELDS_GETUSERFIELDSFOR___PREPAREADDRESSFIELDSINCART___POPULATECART___SETADDRESS___OPC___EXECUTE___REQUIRE_ONCE___X'
   );
   $cols = OPCconfig::getValues('vm_userfields'); 
   $retA = array(); 
   foreach ($cols as $k=>&$row) {
				$ret = new stdClass(); 
				foreach ($row as $ku=>$vu) {
				 $ret->$ku = $vu; 
				}
				$data = @json_decode($row['value'], false); 
				
				
				
				foreach ($data as $k2=>$v2)
				{
					$ret->$k2 = $v2; 
					if (is_array($ret->$k2)) {
					  $z = $ret->$k2; 
					  $ret->$k2 = new stdClass(); 
					  foreach ($z as $kx=>$vx)
					  {
						  $ret->$k2->$kx = $vx; 
					  }
					}
				}
				
				if (!empty($data))
				if (!empty($data->path)) {
			    $title = 'COM_ONEPAGE_USERFIELDS_'.strtoupper($data->path); 
				if (in_array($title, $ignore)) {
					unset($cols[$k]); 
					continue; 
				}
				
				$title = JText::_($title); 
				$title2 = str_replace('_', ' ', $title); 
				$ret->header = $title; 
				$ret->header2 = $title2; 
				$retA[] = $ret; 
				
				
   }
   }
   
   return $retA; 
 }
 
 public static function storeFieldPaths() {
 $x = debug_backtrace(); 
		$zx = array(); $s = false; $c = 0; 
		$path = ''; 
		foreach ($x as $l) {
			if (!isset($l['file'])) continue; 
			
			if ($l['function'] === 'getUserFieldsFor') {
				$s = true; 
			    
			}
			if ($s) {
				$l2 = array(); 
				//$l2['function'] = $l['function']; 
				//if (!isset($l['class'])) continue; 
				//$l2['class'] = $l['class']; 
				//$l2['args'] = $l['args']; 
			    //$zx[] = $l2; 
				$path .= $l['function'].'___'; 
				$c++; 
				
				$x = pathinfo($l['file']); 
			    if (isset($x['basename'])) { 
						$x['basename'] = str_replace('.php', '', $x['basename']); 
						$x['basename'] = str_replace('.', '_', $x['basename']); 
						$x['basename'] = str_replace(' ', '_', $x['basename']); 
						$x['basename'] = preg_replace('/[^\x20-\x7E]/','', $x['basename']);
						$path .= '_'.$x['basename']; 
					}
				
			}
			
			
			
			if ($l['function'] === 'executeComponent') break; 
			//echo $l['function'].': '.$l['file'].$l['line'].' '."<br />\n"; 
		}
		$path .= 'X';
		

    $hash = hash('sha256', $path, false);
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	$default = new stdClass; 
	$default->enabled = false; 
	$default->path = ''; 
	$default->hash = $hash; 
	$ret = OPCconfig::getValue('vm_userfields', $hash, 0, $default, false); 
	if ((empty($ret)) || (!is_object($ret)) || (empty($ret->path))) {

		$default = new stdClass; 
		$default->enabled = false; 
		$default->path = $path; 
        $default->hash = $hash; 
	
	  OPCconfig::store('vm_userfields', $hash, 0, $default, false); 
	}
	else
	{
		$default = $ret; 
	}
	
		return $default; 
	
 }
 
 
 public static $cacheDisabled; 
 public static function addDelimiters(&$rowFields)
 {
		
	 

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		if (!$renderer->hasDel()) 
		{
			
			return; 
		}
		
		OPCrenderer::$num_delimiter = 0; 
		OPCrenderer::$delStarted = false; 
		$dn = 0; 
		$last = $last_type = 0; 
		foreach ($rowFields['fields'] as $k=> &$f)
		{
			if (!empty($last))
			{
			 $last_type = $rowFields['fields'][$last]['type']; 
			}
			if ($last_type !== 'delimiter')
			if ($f['type'] === 'delimiter')
			{
			    $dn++; 	
				$f['type'] = 'delimiter'; 
				$f['formcode'] = $renderer->delStart(JText::_($f['title'])); 
			}
			
			$last = $k; 
			
		}
		if (!empty($last))
			{
			 $last_type = $rowFields['fields'][$last]['type']; 
			}
			
			if ($last_type === 'delimiter')
			{
				$dn--; 
				unset($rowFields['fields'][$last]); 
			}
		if ($dn > 0)
		if (!empty(OPCrenderer::$delStarted))
		{
			$dk = array(); 
			$dk['type'] = 'delimiter'; 
			$dk['name'] = $dk['title'] = 'last_delimiter_end'; 
			$dk['value'] = 'last_delimiter_end'; 
			$dk['formcode'] = $renderer->delEnd(); 
			$rowFields['fields'][] = $dk; 
		}
		OPCrenderer::$delStarted = false; 
		OPCrenderer::$num_delimiter = 0; 
		
 }
	public static function isBusiness($cart) {
		
		
		
		 // this got priority: 
		 $is_business = JRequest::getVar('opc_is_business', -1); 
		 if ($is_business != -1) {
			 
			 OPCloader::opcDebug('business state set by request variable', 'business_fields');
			 
			 return $is_business; 
		 }
		
	  $business_selector = OPCconfig::get('business_selector','');
	  
	  if (!empty($business_selector)) {
		  
		  
		  
		  
		  if (isset( $cart->BT[$business_selector])) {
		 $fx = $cart->BT[$business_selector];
	     
		 
		 $business2_value = OPCconfig::get('business2_value','');
		 
		 {
		 $val = $cart->BT[$business_selector]; 
		 
		 
		 
		 if ($val === $business2_value)
		 {
			 OPCloader::opcDebug('Found business selector field: '.$business_selector.': '.$business2_value, 'business_fields');
			 //JRequest::setVar('opc_is_business', 1); 
			 return true; 
		 }
		 else {
			 $ret = false; 
		 }
		  }
		  }
	  }
	  
	 
	  $business_fields = OPCconfig::get('business_fields',array());
	  if (!empty($business_fields))
	  foreach ($business_fields as $bfn) {
		  if ((!empty($business_selector)) && ($business_selector === $bfn)) continue; 
		  if (isset($cart->BT[$bfn])) {
	    $fx = $cart->BT[$bfn]; 
		if (!empty($fx)) { 
		   OPCloader::opcDebug('Found business field: '.$bfn.': '.$fx, 'business_fields');
		  return true; 
		
		}
		  
		  }
		
	  }
	  $business_fields2 = OPCconfig::get('business_fields2',array());
	  if (!empty($business_fields2))
	  foreach ($business_fields2 as $bfn) {
		  if (isset($cart->BT[$bfn])) {
	    $fx = $cart->BT[$bfn]; 
		if (!empty($fx)) {
			OPCloader::opcDebug('Found business field: '.$bfn.': '.$fx, 'business_fields');
			return true; 
		}
		  }
		
	  }
	  // if we don't use business selector field, let's use standards: 
	  if (!empty($cart->BT['company'])) {
		  OPCloader::opcDebug('Found generic business field: company: '.$cart->BT['company'], 'business_fields');
		  return true; 
	  }
	  $opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
	  if (!empty($cart->BT[$opc_vat_key])) {
		  OPCloader::opcDebug('Found generic business field: '.$opc_vat_key.': '.$cart->BT[$opc_vat_key], 'business_fields');
		  return true; 
	  }
	  OPCloader::opcDebug('No business field filled', 'business_fields');	   

	  
	  return false; 
	  
	  
	}
	
	public static function setNotRequired($fieldname) {
	    $db = JFactory::getDBO();
		$q = "update #__virtuemart_userfields  set `required` = 0 WHERE `name` = '".$db->escape($fieldname)."' limit 1";
		$db->setQuery($q);
		$result = $db->query();
		
		if(empty($result)){
		
			return false; 
		}
		
		return true;
	
	}
	
	
	public static function createField($fieldname, $attribs=array()) {
	     
 $db = JFactory::getDBO(); 
 $q = 'select * from #__virtuemart_userfields where 1 limit 0,1'; 
 $db->setQuery($q); 
 $cols = $db->loadAssoc(); 
 
 foreach ($cols as $key=>$val) {
   if ($key === 'name') $cols[$key] = $fieldname; 
   if ((!empty($attribs)) && (isset($attribs[$key]))) {
	   $cols[$key] = $attribs[$key]; 
	   continue; 
   }
   switch ($key) {
     case 'created_on': $cols[$key] = 'NOW()'; break; 
	 case 'modified_on': $cols[$key] = 'NOW()'; break; 
	 case 'published': if (!isset($attribs['published'])) $cols[$key] = 1; break; 
	 case 'virtuemart_userfield_id': $cols[$key] = 'NULL'; break; 
	 case 'virtuemart_vendor_id': if (!isset($attribs['virtuemart_vendor_id'])) $cols[$key] = 0; break; 
	 case 'userfield_jplugin_id': if (!isset($attribs['userfield_jplugin_id'])) $cols[$key] = 0; break; 
	 case 'maxlength': if (!isset($attribs['maxlength'])) $cols[$key] = 15000; break; 
	 case 'size': if (!isset($attribs['size'])) $cols[$key] = 15000; break; 
	 case 'required': if (!isset($attribs['required'])) $cols[$key] = 0; break; 
	 case 'cols': if (!isset($attribs['cols'])) $cols[$key] = 0; break;  
	 case 'rows': if (!isset($attribs['rows'])) $cols[$key] = 0; break; 
	 
	 case 'value': if (!isset($attribs['rows'])) $cols[$key] = ""; break; 
	 case 'default': if (!isset($attribs['default'])) $cols[$key] = "NULL"; break; 
	 case 'default': if (!isset($attribs['default'])) $cols[$key] = "NULL"; break; 
	 case 'registration': if (!isset($attribs['registration'])) $cols[$key] = 0; break; 
	 case 'shipment': if (!isset($attribs['shipment'])) $cols[$key] = 0; break; 
	case 'account': if (!isset($attribs['account'])) $cols[$key] = 0; break; 
	 case 'readonly': if (!isset($attribs['readonly'])) $cols[$key] = 0; break; 
	case 'calculated': if (!isset($attribs['calculated'])) $cols[$key] = 0; break; 
	case 'sys': if (!isset($attribs['sys'])) $cols[$key] = 0; break; 
	case 'params': if (!isset($attribs['params'])) $cols[$key] = ""; break; 
	case 'ordering': if (!isset($attribs['ordering'])) $cols[$key] = 999; break; 
	case 'shared': if (!isset($attribs['shared'])) $cols[$key] = 0; break; 
	case 'published': if (!isset($attribs['published'])) $cols[$key] = 1; break; 
	case 'created_by': if (!isset($attribs['created_by'])) $cols[$key] = JFactory::getUser()->get('id'); break; 
	case 'modified_by': if (!isset($attribs['modified_by'])) $cols[$key] = JFactory::getUser()->get('id'); break; 
	case 'type': if (!isset($attribs['type'])) $cols[$key] = 'text'; break; 
	 
	 
   }
   
 }
 
 if (empty($cols['vNames'])) $cols['vNames'] = array(); 
 if (empty($cols['vValues'])) $cols['vValues'] = array(); 
 
  	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');    
	   $modelu = OPCmini::getModel('userfields'); 
	   $modelu->store($cols); 
	   
   
	}
	
	public static function fieldExists($fieldname) {

			static $cache; 
			if (empty(OPCUserFields::$cacheDisabled))
			if (isset($cache[$fieldname.'_exists'])) return $fieldname; 
			
		$db = JFactory::getDBO();
		$q = "SELECT `name` FROM #__virtuemart_userfields WHERE `name` = '".$db->escape($fieldname)."' ";
		$db->setQuery($q);
		$result = $db->loadResult();
		if(empty($result)){
			$cache[$fieldname.'_exists'] = false; 
			return false; 
		}
		$cache[$fieldname.'_exists'] = true; 
		return true;

	}
	
	public static function getIfRequired($fieldname) {

			static $cache; 
			if (empty(OPCUserFields::$cacheDisabled))
			if (isset($cache[$fieldname])) return $fieldname; 
			
		$db = JFactory::getDBO();
		$q = "SELECT `required` FROM #__virtuemart_userfields WHERE `name` = '".$db->escape($fieldname)."' ";
		$db->setQuery($q);
		$result = $db->loadResult();
		if(empty($result)){
			$cache[$fieldname] = false; 
			return false; 
		}
		$cache[$fieldname] = true; 
		return true;

	}
 // populates cart->BTaddress
 public static function populateCart(&$cart, $type='BT', $new=1)
 {
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');    
	 
    $savedBT = OPCloader::copyAddress($cart->BT); 
    $savedST = OPCloader::copyAddress($cart->ST); 
	if (isset($cart->cartfields))
	$cart_fields = OPCloader::copyAddress($cart->cartfields); 
	
    if ($type == 'BT')
	unset($cart->BTaddress);
	
	if (defined('VM_VERSION') && (VM_VERSION >= 3))
	{
	$userFieldsModel = OPCmini::getModel ('userfields');
	
	$params = array('captcha' => true, 'delimiters' => true, 'published' => true); // Ignore these types
	$ignore = array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type', 'tos', 'customer_note', 'address_type'); 
	
	$opc_disable_customer_email = OPCconfig::get('opc_disable_customer_email', false); 
	 if (!empty($opc_disable_customer_email)) $ignore[] = 'email'; 
	
	$userFields = $userFieldsModel->getUserFields(
				'cart'
				, $params
				, $ignore // Skips
			);
		
	//stAn note: customer_comment and tos are rendered at differenct positions
	if (!empty($cart->cartfields))
	$cart->cartfields_stored = $cart->cartfields; 
	}
	
	
   
    if (method_exists($cart, 'prepareAddressDataInCart'))
	{
    
	$cart->prepareAddressDataInCart($type, true); // in 258 updated  from:
	// virtuemart2 only code: 
	OPCloader::restoreDataInCart($cart->BT, $savedBT); 
	OPCloader::restoreDataInCart($cart->ST, $savedST); 
	
	$cart->prepareAddressDataInCart($type, false);
	
	}
	

	/*
	if (method_exists($cart, 'prepareAddressDataInCart'))
    $cart->prepareAddressDataInCart($type, false); // in 258 updated  from: $cart->prepareAddressDataInCart($type, false);
	*/
	
	
	
    if (method_exists($cart, 'prepareAddressFieldsInCart'))
    $cart->prepareAddressFieldsInCart();
	
	
	
	if ((defined('VM_VERSION') && (VM_VERSION >= 3)) && (!empty($userFields)))
	{
	$copy = $cart->cartfields; 
	$userFields = $userFieldsModel->getUserFieldsFilled(
				$userFields
				,$copy
			);
			
			foreach ($userFields['fields'] as $k=>$v)
			{
			   if (!empty($cart->BTaddress['fields'][$k]))
			   {
				   unset($cart->BTaddress['fields'][$k]); 
			   }
			   
			   self::escapeFormField($userFields['fields'][$k]); 
			   
			}
			
		$cart->cartfieldsaddress = $userFields; 
	}
	
	
	
	
	if (isset($cart_fields))
	{
		OPCloader::restoreDataInCart($cart->cartfields, $cart_fields); 
	}
	OPCloader::restoreDataInCart($cart->BT, $savedBT); 
	OPCloader::restoreDataInCart($cart->ST, $savedST); 
	
	self::checkCart($cart); 
	
	$fieldKey = $type.'address'; 
	$fields =& $cart->{$fieldKey}; 
	if ((!empty($fields)) && (isset($fields['fields']))) {
		foreach ($fields['fields'] as $k=>$v) {
			self::escapeFormField($fields['fields'][$k]); 
			
			
		}
	}
	
 }
 
 public static function checkCart(&$cart)
 {
	 if (!empty($cart->BT['virtuemart_state_id']))
	if (!OPCUserFields::checkCountryState($cart->BT['virtuemart_country_id'], $cart->BT['virtuemart_state_id']))
	{
		$cart->BT['virtuemart_state_id'] = 0; 
		if (!empty($cart->ST))
		if (isset($cart->ST['virtuemart_state_id']))
		{
			
			if (OPCUserFields::checkCountryState($cart->ST['virtuemart_country_id'], $cart->ST['virtuemart_state_id']))
			{
				$cart->BT['virtuemart_state_id']  = $cart->ST['virtuemart_state_id'] ; 
			}
		}
	}
	if (!empty($cart->ST))
	if (!empty($cart->ST['virtuemart_state_id']))
	if (!OPCUserFields::checkCountryState($cart->ST['virtuemart_country_id'], $cart->ST['virtuemart_state_id']))
	{
		$cart->ST['virtuemart_state_id'] = 0; 
		if (isset($cart->BT['virtuemart_state_id']))
		{
			if (OPCUserFields::checkCountryState($cart->BT['virtuemart_country_id'], $cart->BT['virtuemart_state_id']))
			{
				$cart->ST['virtuemart_state_id']  = $cart->BT['virtuemart_state_id'] ; 
			}
		}
	}
	
	
 }
 public static function fieldExistsPublished($f, $type='BT', &$ret=null) {
	 $db = JFactory::getDBO(); 
	 $q = "select * from `#__virtuemart_userfields` where `name` = '".$db->escape($f)."' limit 0,1"; 
	 $db->setquery($q); 
	 $ret = $db->loadAssoc(); 
	 if (empty($ret)) {
		 $ret = array(); 
		 return false; 
	 }
	 if (empty($ret['published'])) return false; 
	 if (empty($type)) {
	   return true; 
	 }
	 if ($type === 'BT') {
		 if (!empty($ret['account'])) return true; 
		 else return false; 
	 }
	 if ($type === 'ST') {
		 if (!empty($ret['shipment'])) return true; 
		 else return false; 
	 }
	 return true; 
	 
	 
	 
	 
 }
 public static function processAdminFields(&$userFields)
 {
      include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	  
	  
      foreach ($userFields['fields'] as $k=>$f)
		 {
			self::escapeFormField($userFields['fields'][$k]); 
			 
		    if ($f['type'] == 'emailaddress')
			 {
			   $userFields['fields'][$k]['formcode'] = '<input type="email" id="'
							. $k . '_field" name="' . $k.'" value="'.$userFields['fields'][$k]['value'].'" '
							. ($f['required'] ? ' class="required"' : '')
							. ' /> ';
				
			 }
			 
			 	if (!empty($admin_shopper_fields))
				{
					if (!in_array($k, $admin_shopper_fields))
					 {
					    unset($userFields['fields'][$k]); 
						continue; 
					 }
				}
				
				if ($k === 'password')
				 {
				    if (!empty($admin_shopper_fields))    
				    if (in_array($k, $admin_shopper_fields) && (!in_array('password2', $admin_shopper_fields)))
					 {
					    $userFields['fields'][$k]['formcode'] = '<input type="text" name="password" value="" required="required" class="required" />'; 
					 }
				 }
				 else
				 if ($k === 'username')
				  {
				    self::usernameExistsCode($userFields); 
				  }
				 else
				 if ($k === 'email')
				 {
				    self::emailHtml5($userFields); 
				    self::emailExistsCode($userFields); 
				 }

			 
			 
		 }
		 
		 
		 
		 
 }
 
 
 public static function escapeFormField(&$field, $val2='') {
	 
	 if (isset($field['formcode'])) {
		 
		 if ((!empty($field['htmlentities'])) && ($field['htmlentities'] === true)) return; 
		 if (empty($field['encoded'])) $field['encoded'] = array(); 
		 
		 if (!empty($field['value'])) {
			 $value = $field['value']; 
		 }
		 elseif (!empty($val2)) $value = $val2; 
		 
		 if (empty($value)) return; 
		 
		 $val = $value; 
		 
		 $val = html_entity_decode($val); 
		 $val = html_entity_decode($val); 
		 
		 
		 if (!empty($field['encoded'][$val])) { 
			return; 
		 }
		 if (!defined('ENT_HTML401')) define('ENT_HTML401', ENT_COMPAT); 
		 $field['formcode'] = str_replace('value="'.$value.'"', 'value="'.htmlentities($value,ENT_COMPAT | ENT_HTML401, 'UTF-8', false).'"', $field['formcode']); 
		 $field['formcode'] = str_replace("value='".$value."'", "value='".htmlentities($value,ENT_COMPAT | ENT_HTML401, 'UTF-8', false)."'", $field['formcode']); 
		 $field['value'] = htmlentities($field['value'],ENT_COMPAT | ENT_HTML401, 'UTF-8', false); 
		 $field['encoded'][$value] = true; 
	 }
	 
 }
 
 public static function checkCountryState($country, $state)
 {
    
	static $cache; 
	
	if (empty(OPCUserFields::$cacheDisabled))
	if (isset($cache[$country.'_'.$state])) return $cache[$country.'_'.$state]; 
	if (empty($cache)) $cache = array(); 
	
    $db = JFactory::getDBO(); 
	
	$q = 'select `virtuemart_country_id` from `#__virtuemart_states` where `virtuemart_state_id` = '.(int)$state.' limit 0,1';  
	$db->setQuery($q); 
	$country_id = $db->loadResult(); 
	if (!empty($country_id))
	{
	  $country = (int)$country; 
	  $country_id = (int)$country_id; 
	  if ($country_id === $country) 
	  {
	  $cache[$country.'_'.$state] = true; 
	  return true; 
	  }
	  $cache[$country.'_'.$state] = false; 
	  return false; 
	}
	$cache[$country.'_'.$state] = true; 
	// default: 
	return true; 
	
 }

 public static function emailHtml5(&$userFields, $key='email')
 {
   // $userFields['fields'][$key]['formcode'] = str_replace('type="text"', 'type="email"', $userFields['fields'][$key]['formcode']); 
 }
 
 public static function usernameExistsCode(&$userFields)
 {
    	     $u = OPCLang::_('COM_VIRTUEMART_REGISTER_UNAME'); 
	     $un = $userFields['fields']['username']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.username_check(this);" id=', $un);
		 $un .=  '<span class="username_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="username_already_exists">';
		 $un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', $u); 
		 $un .= '</span>'; 
		 $userFields['fields']['username']['formcode'] = $un; 

 }
 
 public static function emailExistsCode(&$userFields)
 {
    $un = $userFields['fields']['email']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.email_check(this);" id=', $un);
		 $un .=  '<span class="email_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email_already_exists">';
		 $un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL')); 
		 $un .= '</span>'; 
		 $userFields['fields']['email']['formcode'] = $un; 
 }
 
 public static function getUserFields(&$userFields, &$OPCloader, &$cart, $remove=array(), $only=array(), $skipreorder=array())
  {
     if (!defined('VM_REGISTRATION_TYPE')) $OPCloader->setRegType(); 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
     include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	 
	 $user = JFactory::getUser(); 
	  $uid = $user->get('id');
	 
	 $disable = array('tos', 'agreed'); 
	 $opc_disable_customer_email = OPCconfig::get('opc_disable_customer_email', false); 
	 if (!empty($opc_disable_customer_email)) $disable[] = 'email'; 
	 
       //$userFields = $userFieldsOrig; 
	   if (!empty($userFields))
       foreach ($userFields['fields'] as $key=>$uf)   
	    {
			
			 
			 if (in_array($key, $disable))
			 {
				 unset($userFields['fields'][$key]); 
				 continue; 
			 }
			 
			 self::escapeFormField($userFields['fields'][$key]);
			
		 $userFields['fields'][$key]['formcode'] = str_replace('width:', 'max-width:', $userFields['fields'][$key]['formcode']); 
		 $userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']);  
		 $userFields['fields'][$key]['formcode'] = str_replace('maxlength', 'disabledmaxlength', $userFields['fields'][$key]['formcode']);  
		 
		  if ($key == 'password')
		  $userFields['fields'][$key]['required'] = true; 
	  
		  if (stripos('virtuemart_country_id', $key)!==false)
		  {
			   $userFields['fields'][$key]['formcode'] = str_replace('virtuemart_country_id_field', 'virtuemart_country_id', $userFields['fields'][$key]['formcode']);  
			   
			   $clist = array(); 
			   OPCcommonhtml::getCountriesOptionsVals($clist); 
			   $userFields['fields'][$key]['options'] = $clist; 
			   $userFields['fields'][$key]['type'] = 'select'; 
			   
			   
		  }
		  
		  if (isset($userFields['fields']['email'])) {
			  $userFields['fields']['email']['type'] = 'email'; 
		  }
		  
		  
		  $tt = $userFields['fields'][$key]['type']; 
		  $sa = array('select', 'multiselect'); 
		  $ign = array('virtuemart_country_id', 'virtuemart_state_id'); 
		  if (!in_array($key, $ign))
		  if (in_array($tt, $sa)) {
			  $userFields['fields'][$key]['options'] = OPCcommonhtml::getOptionVals($key); 
		  }
		  
		  if (stripos('virtuemart_state_id', $key)!==false)
		  {
			   $userFields['fields'][$key]['formcode'] = str_replace('virtuemart_state_id_field', 'virtuemart_state_id', $userFields['fields'][$key]['formcode']);  
		  }
	  
		  if ($key == 'password2')
		  $userFields['fields'][$key]['required'] = true; 
		  
		   $arr = array ('name', 'username'); 
		   if (in_array($key, $arr))
			{	
		    $userFields['fields'][$key]['required'] = 1; 
			}
			
		if (!empty($custom_rendering_fields))
		if (in_array($userFields['fields'][$key]['name'], $custom_rendering_fields))
				    {
					  unset($userFields['fields'][$key]); 
					  continue; 
					}
		  
		   if ($key != 'email')
			{
			$userFields['fields'][$key]['formcode'] = str_replace('/>', ' autocomplete="off" />', $userFields['fields'][$key]['formcode']); 
			}
			
			
			if ($key == 'email')
			if (!empty($cart->BT['email']))
			{
			  $userFields['fields'][$key]['formcode'] = str_replace('value=""', ' value="'.$cart->BT['email'].'"', $userFields['fields'][$key]['formcode']); 
			  
			  self::emailHtml5($userFields); 
			}
			
			$userFields['fields'][$key]['formcode'] = str_replace('size="0"', '', $userFields['fields'][$key]['formcode']); 
			
		// get proper state listing: 
		if (($key == 'virtuemart_state_id'))
	  {
	    if (!empty($cart->BT['virtuemart_country_id']))
	  $c = $cart->BT['virtuemart_country_id']; 
	  else $c = $default_shipping_country; 
	  
	  
	  
	  if (empty($c))
	  {
	    $vendor = $OPCloader->getVendorInfo($cart); 
		$c = $vendor['virtuemart_country_id']; 
	  }
	  
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
	  $options = array(); 
	  $html = OPCCommonHtml::getStateHtmlOptions($cart, $c, 'BT', $options);
	  $userFields['fields']['virtuemart_state_id']['options'] = $options; 
	  $userFields['fields']['virtuemart_country_id']['value'] = $c; 
	  
	   
		 if (!empty($cart->BT['virtuemart_state_id']))
		 {
		   $userFields['fields']['virtuemart_state_id']['value'] = $cart->BT['virtuemart_state_id']; 
		   $html = str_replace('value="'.$cart->BT['virtuemart_state_id'].'"', 'value="'.(int)$cart->BT['virtuemart_state_id'].'" selected="selected"', $html); 
		 }
		
	  
	    //
		if (!empty($userFields['fields']['virtuemart_state_id']['required']))
		$userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple opcrequired" id="virtuemart_state_id" opcrequired="opcrequired" size="1"  name="virtuemart_state_id" >'.$html.'</select>'; 
		 else
	     $userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple" id="virtuemart_state_id"  size="1"  name="virtuemart_state_id" >'.$html.'</select>';
		
		//$userFields['fields'][$key]['formcode'] = '<select class="inputbox multiple" id="virtuemart_state_id"  size="1"  name="virtuemart_state_id" >'.$html.'</select>'; 
	  }
		
		
		
		// add klarna button: 
		 if (!empty($klarna_se_get_address))
	  if (($key == 'socialNumber'))
		{
		  $newhtml = '<input type="button" id="klarna_get_address_button" onclick="return Onepage.send_special_cmd(this, \'get_klarna_address\' );" value="'.OPCLang::_('COM_ONEPAGE_KLARNA_GET_ADDRESS').'" />';
		  //$userFields['fields'][$key]['formcode'] = str_replace('name="socialNumber"', ' style="width: 70%;" name="socialNumber"', $userFields['fields'][$key]['formcode']).$newhtml;
		  $userFields['fields'][$key]['formcode'] .= $newhtml; 
		}
		
		// mark email read only when logged in
		if ($key == 'email')
	  {
	    
		// user is logged, but does not have a VM account
		if ((!OPCloader::logged($cart)) && (!empty($uid)))
		{
		  // the user is logged in only in joomla, but does not have an account with virtuemart
		  $userFields['fields'][$key]['formcode'] = str_replace('/>', ' readonly="readonly" />',  $userFields['fields'][$key]['formcode']); 
		}
		else
		{
		$userFields['fields'][$key]['formcode'] = str_replace('type="text"', 'type="email"', $userFields['fields'][$key]['formcode']); 
		}
	   }
		
		
		// remove autocomplete for multi dependant fields
	if (($key == 'virtuemart_country_id'))
	   {
	      $userFields['fields'][$key]['formcode'] = str_replace('name=', ' autocomplete="off" name=', $userFields['fields'][$key]['formcode']); 
	   }
		
	// set required properly: 
	if (isset($userFields['fields'][$key]['name']))
	 if (!empty($uf['required']) && (strpos($uf['formcode'], 'required')===false))
	 if ($userFields['fields'][$key]['name'] != 'virtuemart_state_id')
	  {
	    
	    $x1 = strpos($uf['formcode'], 'class="');
		if ($x1 !==false)
		{
		  $userFields['fields'][$key]['formcode'] = str_replace('class="', 'class="required ', $uf['formcode']);
		}
		else
		{
		$userFields['fields'][$key]['formcode'] = str_replace('name="', 'class="required" name="', $uf['formcode']);
		 
		 
		}
		
		
	  }
		
	if ($uf['type'] == 'date')
	 {
		 $userFields['fields'][$key]['formcode'] = str_replace(OPCLang::_('COM_VIRTUEMART_NEVER'), $userFields['fields'][$key]['title'], $userFields['fields'][$key]['formcode']); 
	 }
		
		
		
			
			
			
			if (!empty($op_no_display_name))
	 if ($userFields['fields'][$key]['name'] == 'name')
	  {
		unset($userFields['fields'][$key]); 
	    continue; 
	  }
	  
	  	 if ($key == 'username')
     if (!empty($op_usernameisemail) && ($userFields['fields'][$key]['name'] == 'username')) 
	 {
		 
	  unset($userFields['fields'][$key]); 
	  continue; 
	 }

	 if (($key == 'password') )
	   {
		   
		   
	     $userFields['fields']['opc_password'] = $userFields['fields'][$key];
		 $userFields['fields']['opc_password']['formcode'] = str_replace('password', 'opc_password', $userFields['fields']['opc_password']['formcode']); 
		 $userFields['fields']['opc_password']['formcode'] = str_replace('type="opc_password"', 'type="password" autocomplete="off" ', $userFields['fields']['opc_password']['formcode']); 
		 $userFields['fields']['opc_password']['name'] = 'opc_password'; 
		 //unset($userFields['fields'][$key]); 
		  if (!empty($password_clear_text))
		  {
		  
		  
				$userFields['fields']['opc_password']['formcode'] = str_replace('type="password"', 'type="text" ', $userFields['fields']['opc_password']['formcode']); 
		  }
		  unset($userFields['fields']['password']);
		 //$l = $userFields['fields']['opc_password'];
		
	   }
	   
	if ($key == 'password2')
    {
		
		
	   		 if (!empty($password_clear_text))
		  {
				$userFields['fields']['password2']['formcode'] = str_replace('type="password"', 'type="text" ', $userFields['fields']['password2']['formcode']); 
		  }
		  
		  $userFields['fields']['opc_password2'] = $userFields['fields']['password2']; 
		  unset($userFields['fields']['password2']); 

	}	
	 
	 
	 // only for those that are unlogged: 
	   if ((!OPCloader::logged($cart)) && (empty($uid)))
	 if ($key == 'email')
     {
	
	
	
	  $userFields['fields'][$key]['formcode'] = str_replace('class="required', 'class="required email ', $userFields['fields']['email']['formcode']); 
      $userFields['fields'][$key]['formcode'] = str_replace('type="text"', 'type="email"', $userFields['fields'][$key]['formcode']); 	  
	  if (!empty($double_email))
	  {
	    $email2 = $userFields['fields']['email'];
		$email2['name'] = 'email2'; 
		$title = OPCLang::_('COM_ONEPAGE_EMAIL2'); 
		if ($title != 'COM_ONEPAGE_EMAIL2')
		$email2['title'] = $title;
		$email2['formcode'] = str_replace('"email', '"email2', $email2['formcode']); 
		$email2['formcode'] = str_replace('id=', ' onblur="javascript: doublemail_checkMail();" id=', $email2['formcode']);
		$email2['javascript:onblur'] = 'Onepage.doublemail_checkMail()'; 
		$email2['formcode'] = str_replace('type="email2"', 'type="email"', $email2['formcode']); 
		$h = '<span style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email2_info" class="email2_class">';
		$emailerr = OPCLang::_('COM_ONEPAGE_EMAIL_DONT_MATCH');
		if ($emailerr != 'COM_ONEPAGE_EMAIL_DONT_MATCH')
		$h .= $emailerr;
		else $h .= "Emails don't match!";
		$h .= '</span>';
		$email2['formcode'] .= $h;
	  }
	  
	   if (!empty($opc_check_email))
	  if ((!OPCloader::logged($cart)) && (empty($uid)))
	  if (!empty($userFields['fields']['email']))
	  {
		 self::emailExistsCode($userFields); 
	     
	  }
	  
	  
	  
	  
	  
	 }
	 $opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
	 $ax = array('EUVatID', 'eu_vat_id'); 
	 if (!in_array($opc_vat_key, $ax))
	if (($key === 'EUVatID') || ($key === 'eu_vat_id'))
	  {
	    $h = '<br /><span style="display: none; position: relative; float: left; clear: both; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="vat_info" class="vat_info">';
		$h .= '</span>';
		$userFields['fields'][$key]['formcode'] .= $h; 
		
		
	  }	 
	  
	 if ($key === $opc_vat_key)
	 {
	 
	 if (!empty($opc_euvat))
	  if (!empty($userFields['fields'][$opc_vat_key]))
	  {

	     $un = $userFields['fields'][$opc_vat_key]['formcode']; 
		 if (!empty($opc_euvat_button))
		 {
		    $un .= '<br /><input type="button" value="'.OPCLang::_('COM_ONEPAGE_VALIDATE_VAT_BUTTON').'" onclick="javascript:  Onepage.validateOpcEuVat(this);" class="opc_euvat_button" />'; 
		 }
		 $un .=  '<br /><span class="vat_info" style="display: none; position: relative;  color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="vat_info">';
		 $un .= OPCLang::_('COM_ONEPAGE_VAT_CHECKER_INVALID'); 
		 $un .= '</span>'; 
		 $userFields['fields'][$opc_vat_key]['formcode'] = $un; 
	  }
	}
	 
	 
	  if ($key == 'username')
	   {
	   
	   
	       if (!empty($opc_check_username))
	 if ((!OPCloader::logged($cart)) && (empty($uid)))
	 if (!empty($userFields['fields']['username']))
	  {
	     self::usernameExistsCode($userFields); 
	  }
	   }
	  
	  
	}
	
	if (!empty($email2))
	$userFields['fields']['email2'] = $email2; 
	
	//commented in 334 since the registration themes may include it's own checkbox: if (!defined('OPC_IN_REGISTRATION_MODE'))
	{
	jimport( 'joomla.html.parameter' );
			$plugin = JPluginHelper::getPlugin('system', 'vm_mailchimp');
			if (!empty($plugin))
			{
			
			if (class_exists('JParameter'))
			$params = new JParameter( $plugin->params );
			else
			$params = new JRegistry( $plugin->params );
			
			$opc = $params->get('disable_in_opc', false); 
			if (!empty($opc))
			 {
			    unset($userFields['fields']['mailchimp']); 
				unset($userFields['fields']['mailchimp_checkbox']); 
			 }
			
	        }
	}
	
	
	
	
	if (!empty($userFields))
	self::reorderFields($userFields, $skipreorder); 
	 
		  
		   
		  
	
		
		
  }
  
  public static function addListenersToFields(&$userFields)
	{
			$business_selector = OPCconfig::get('business_selector','');
		$opc_ajax_fields = OPCconfig::get('opc_ajax_fields',array());
		
		$brow = array(); 
		if (!empty($business_selector) || (!empty($opc_ajax_fields)))
		{
			/*
			$db = JFactory::getDBO(); 
			$q = 'select * from `#__virtuemart_userfields` where 1'; 
			$db->setQuery($q); 
			$browd = $db->loadAssocList(); 
			foreach ($browd as $key=>$val)
			{
				$brow[$val['name']] = $val; 
			}
			*/
			
		}


		if (!isset($opc_ajax_fields))
		{
			$opc_ajax_fields = array(); 
			$opc_ajax_fields[] = 'zip'; 
			$opc_ajax_fields[] = 'address_1'; 
			$opc_ajax_fields[] = 'address_2'; 
			$opc_ajax_fields[] = 'virtuemart_state_id'; 
			$opc_ajax_fields[] = 'virtuemart_country_id'; 
			
		}
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		
		$later = array('virtuemart_country_id', 'virtuemart_state_id',$opc_vat_key.'_field', 'pluginistraxx_euvatchecker' ); 
		
		foreach ($userFields['fields'] as $k=> &$field) {
		
		$html =& $userFields['fields'][$k]['formcode']; 
		$field_name = $field['name']; 
		
		if (!empty($business_selector) && (in_array($business_selector, $opc_ajax_fields)))
		{
			$ajaxf = $business_selector; 
			
			
				$arr = array('select', 'checkbox', 'multicheckbox', 'radio', 'select-one'); 
				$type = $field['type']; 
				if (in_array($type, $arr)) $onblur = 'onchange'; 
				
			
			if (($field_name === $ajaxf) || ($field_name === 'shipto_'.$ajaxf)) {
				$html = str_replace('name="shipto_'.$ajaxf.'"', 'name="shipto_'.$ajaxf.'"'.' onchange="javascript:Onepage.business2field(this, true);" name="shipto_'.$ajaxf.'"', $html);
				$html = str_replace('name="'.$ajaxf.'"', 'name="'.$ajaxf.'"'.' onchange="javascript:Onepage.business2field(this, true);" id="'.$ajaxf.'"', $html);
				$field['javascript:onchange'] = 'Onepage.business2field(this, true)'; 
			}
			
			
			$ajaxf = $business_selector; 
			if (($field_name === $ajaxf) || ($field_name === 'shipto_'.$ajaxf)) {
			 $html = str_replace('name="shipto_'.$ajaxf.'[]"', 'name="shipto_'.$ajaxf.'[]"'.' onchange="javascript:Onepage.business2field(this, true);" ', $html);
			 $html = str_replace('name="'.$ajaxf.'[]"', 'name="'.$ajaxf.'[]"'.' onchange="javascript:Onepage.business2field(this, true);" id="'.$ajaxf.'[]"', $html);
			 $field['javascript:onchange'] = 'Onepage.business2field(this, true)'; 
			}
		}
		else
		if (!empty($business_selector))
		{
			$ajaxf = $business_selector; 
			if (($field_name === $ajaxf) || ($field_name === 'shipto_'.$ajaxf)) {
			 $html = str_replace('name="shipto_'.$ajaxf.'"', ' onchange="javascript:Onepage.business2field(this, false);" name="shipto_'.$ajaxf.'"', $html);
			 $html = str_replace('name="'.$ajaxf.'"', ' onchange="javascript:Onepage.business2field(this, false);" name="'.$ajaxf.'"', $html);
			 $field['javascript:onchange'] = 'Onepage.business2field(this, false)'; 
			}
			$ajaxf = $business_selector; 
			if (($field_name === $ajaxf) || ($field_name === 'shipto_'.$ajaxf)) {
			 $html = str_replace('name="shipto_'.$ajaxf.'[]"', ' onchange="javascript:Onepage.business2field(this, false);" name="shipto_'.$ajaxf.'[]"', $html);
			 $html = str_replace('name="'.$ajaxf.'[]"', ' onchange="javascript:Onepage.business2field(this, false);" name="'.$ajaxf.'[]"', $html);
			 $field['javascript:onchange'] = 'Onepage.business2field(this, false)'; 
			}
			
			
			
			
		}
		
		
		
			
			foreach ($opc_ajax_fields as $ajaxf)
			{
				$onblur = 'onblur'; 
				
				
					$arr = array('select', 'checkbox', 'multicheckbox', 'radio', 'select-one'); 
					$type = $field['type']; 
					if (in_array($type, $arr)) $onblur = 'onchange'; 
					
				
				
				if (!in_array($ajaxf, $later))
				{
				

				if (($field_name === $ajaxf) || ($field_name === 'shipto_'.$ajaxf)) {
					
					$html = str_replace('id="shipto_'.$ajaxf.'_field"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="shipto_'.$ajaxf.'_field"', $html);
					$html = str_replace('id="shipto_'.$ajaxf.'"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="shipto_'.$ajaxf.'_field"', $html);
					$html = str_replace('id="'.$ajaxf.'_field"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="'.$ajaxf.'_field"', $html);
					$html = str_replace('id="'.$ajaxf.'"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="'.$ajaxf.'_field"', $html);
					$field['javascript:'.$onblur] = 'Onepage.op_runSS(this)'; 
				}
				
				}
			}
			if (($field_name === 'username') || ($field_name === 'opc_username')) {
			
			$user = JFactory::getUser(); 
			$uid = $user->get('id'); 
			$usersConfig = JComponentHelper::getParams( 'com_users' );
			$usernamechange = $usersConfig->get( 'change_login_name', true );
			if (empty($usernamechange))
			if (!empty($uid))
			{
				// username readonly
				$html = str_replace('name="username"', ' readonly="readonly" name="username"', $html); 
				$html = str_replace('name="opc_username"', ' readonly="readonly" name="opc_username"', $html); 
			}
			}
			
			if (($field_name === 'virtuemart_state_id') || (($field_name === 'shipto_virtuemart_state_id') ))
			{
			if (in_array('virtuemart_state_id', $opc_ajax_fields))
			{
				$html = str_replace('id="shipto_virtuemart_state_id"', 'id="shipto_virtuemart_state_id" onchange="javascript:Onepage.op_runSS(this);" ', $html);
				$html = str_replace('id="virtuemart_state_id"', 'id="virtuemart_state_id" onchange="javascript:Onepage.op_runSS(this);" ', $html);
				$field['javascript:onchange'] = 'Onepage.op_runSS(this)'; 
			}
			

			
			}
			if (($field_name === 'virtuemart_country_id') || (($field_name === 'shipto_virtuemart_country_id') ))
			{
			if (in_array('virtuemart_country_id', $opc_ajax_fields))
			{
				if (isset($userfields['fields']['virtuemart_state_id'])) {
					$par = "'true', ";
				}
				else {
					$par = "'false', "; 
				}
				
				$html = str_replace('id="shipto_virtuemart_country_id', ' onchange="javascript: Onepage.op_validateCountryOp2('.$par.'\'true\', this);" id="shipto_virtuemart_country_id', $html);
				$html = str_replace('id="virtuemart_country_id', ' onchange="javascript: Onepage.op_validateCountryOp2('.$par.'\'false\', this);" id="virtuemart_country_id', $html);
				$field['javascript:onchange'] = 'Onepage.op_validateCountryOp2('.$par.'\'true\', this)'; 
			}
			else
			{
				
				$html = str_replace('id="shipto_virtuemart_country_id"', 'id="shipto_virtuemart_country_id" onchange="javascript: Onepage.changeStateList(this);" ', $html);
				$html = str_replace('id="virtuemart_country_id"', 'id="virtuemart_country_id" onchange="javascript: Onepage.changeStateList(this);" ', $html);
				$field['javascript:onchange'] = 'Onepage.changeStateList(this)'; 
			}
			}
		
		
		
		
		$opc_euvat = OPCconfig::get('opc_euvat',false);
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		if ($field_name === $opc_vat_key) {
		$opc_euvat_button = OPCconfig::get('opc_euvat_button',false);
		
		//		if (empty($opc_euvat_button))
		if (!empty($opc_euvat))
		{
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
			$html = str_replace('id="'.$opc_vat_key.'_field"', 'id="'.$opc_vat_key.'_field" onchange="javascript: return Onepage.validateOpcEuVat(this);" ', $html);
			$field['javascript:onchange'] = 'Onepage.validateOpcEuVat(this)'; 
			
			
			
		}
		}
		
		
		if (stripos($html, 'pluginistraxx_euvatchecker_field')!==false) {
		//pluginistraxx_euvatchecker_field
		$html = str_replace('id="pluginistraxx_euvatchecker_field"', ' id="pluginistraxx_euvatchecker_field" onblur="javascript:Onepage.op_runSS(this, false, true);" ', $html); 
		$field['javascript:onblur'] = 'Onepage.op_runSS(this, false, true)'; 
		}
		
		// support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
		
		/* disabled in march 2017
			
		if (version_compare(JVERSION, '1.6.0', '<')) 
		{ 
			$plugin_short_path = 'plugins/system/bitvatidchecker/';
		}
		else {
			$plugin_short_path = 'plugins/system/bit_vm_check_vatid/bitvatidchecker/';
		}
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$plugin_short_path))
		{
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'bit_vm_check_vatid'.DIRECTORY_SEPARATOR.'include.php'); 
		}
		*/
		// end support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
		
		
		
		
		}
	}
  
  
  public static function customizeFieldsPerOPCConfig(&$userFields, &$OPCloader)
	{
		

		if (empty($userFields)) return;
		if (count($userFields['fields'])===0) 
		{
			// no fields found
			return '';
		}
		if (empty(OPCloader::$fields_names)) OPCloader::$fields_names = array(); 
		foreach ($userFields['fields'] as $key=>$val)
		{
			
			OPCloader::$fields_names[$key] = $userFields['fields'][$key]['title']; 
			
			OPCloader::$fields_names['shipto_'.$key] = $userFields['fields'][$key]['title']; 
			OPCloader::$fields_names['third_'.$key] = $userFields['fields'][$key]['title']; 
		}
		

		
		
		$op_usernameisemail = OPCconfig::get('op_usernameisemail', false); 
		
		$op_create_account_unchecked = OPCconfig::get('op_create_account_unchecked', true); 
		
		
		

		$user = JFactory::getUser(); 
		$user_id = $user->get('id'); 
		if (empty($user_id))  {
		if (isset($userFields['fields']['password']))
		if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
		{
			$ra = array(); 
			$ra['formcode'] = '<input type="checkbox" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox checkbox inline" onchange="Onepage.showFields( this.checked, new Array('; 
			
			
			$onchange = 'Onepage.showFields( this.checked, new Array('; 
			if (empty($op_usernameisemail))
			$onchange .= '\'username\', \'password\', \'password2\', \'opc_password\''; 
			else $ra['formcode'] .= '\'password\', \'password2\', \'opc_password\''; 
			$onchange .= ') )'; 
			$ra['javascript:onchange'] = $onchange;
			
			if (empty($op_usernameisemail))
			$ra['formcode'] .= '\'username\', \'password\', \'password2\', \'opc_password\''; 
			else $ra['formcode'] .= '\'password\', \'password2\', \'opc_password\''; 
			$ra['formcode'] .= ') );" '; 
			if (empty($op_create_account_unchecked)) 
			$ra['formcode'] .= ' checked="checked" '; 
			$ra['formcode'] .= '/>';
			$ra['name'] = 'register_account'; 
			$ra['title'] = OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER'); 
			$ra['required'] = false; 
			$ra['type'] = 'checkbox'; 
			$ra['readonly'] = false; 
			$ra['hidden'] = false; 
			$ra['description'] = ''; 
			
			$userFields['fields']['register_account'] = $ra; 
		}
		} 
		else {
			$ra = array(); 
			$ra['formcode'] = '<input type="hidden" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox checkbox inline" />'; 
			$ra['name'] = 'register_account'; 
			$ra['title'] = OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER'); 
			$ra['required'] = false; 
			$ra['type'] = 'checkbox'; 
			$ra['readonly'] = false; 
			$ra['hidden'] = true; 
			$ra['description'] = ''; 
			
			$userFields['fields']['register_account'] = $ra; 
		}
		
		
		$op_usernameisemail = OPCconfig::get('op_usernameisemail', false); 


		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		
		$cart = VirtuemartCart::getCart(); 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		OPCUserFields::getUserFields($userFields, $OPCloader, $cart); 
		
		$user = JFactory::getUser(); 
		$uid = $user->get('id'); 
		$guest = $user->get('guest'); 
		if (empty($guest) || (!empty($uid)))
		{
			
			$arr = array('password', 'opc_password', 'password2', 'opc_password2', 'username', 'virtuemart_state_id', 'shipto_virtuemart_state_id'); 
			
			
			
			
			foreach ($userFields['fields'] as $key=>$f)
			{
				$kname = $f['name']; 
				if (in_array($kname, $arr))
				{
					
					$userFields['fields'][$key]['formcode'] = str_replace('required', 'notrequired', $f['formcode']); 
					$userFields['fields'][$key]['required'] = false; 
					
					
				}
				
				
				
				
			}
		}
		
		
		
	}
  
  //this function is called upon all OPC rendered fields...   
  public static function addSpecialRequired(&$fields, $type='') {
     
	 $business_obligatory_fields = OPCconfig::get('business_obligatory_fields', array()); 
	 
	 foreach ($fields['fields'] as $k=>$field) {
	    $name = $field['name']; 
		if (in_array($name, $business_obligatory_fields)) {
		   $fields['fields'][$k]['required'] = true; 
		}
	 }
	 
	 if ($type === 'ST') {
	   $shipping_obligatory_fields = OPCconfig::get('shipping_obligatory_fields', array()); 
	   
	   foreach ($fields['fields'] as $k=>$field) {
	    
		if (in_array($k, $shipping_obligatory_fields)) {
		   $fields['fields'][$k]['required'] = true; 
		}
	  } 
	 
	 
	 }
	 
	 $html5_fields = OPCconfig::get('html5_fields', array()); 
	 $html5_fields['email'] = 'email'; 
	 $html5_fields_extra = OPCconfig::get('html5_fields_extra', array()); 
	 $html5_placeholder = OPCconfig::get('html5_placeholder', array()); 
	 $html5_validation_error = OPCconfig::get('html5_validation_error', array()); 
	 
	 foreach ($fields['fields'] as $k=>$field) {
			
			if (empty($field['name'])) continue; 
			
			$prefix = ''; 
			$inloop = false; 
			start:
			    $name = $field['name']; 
			    if ((!empty($prefix)) && (stripos($name, $prefix)!==0)) {
					continue; 
				}
				if ((!empty($prefix)) && (stripos($name, $prefix)===0)) {
					$name = str_replace('shipto_', '', $name); 
				}
				
				
				 {
				
				if (empty($field['opc_html5'])) {
				if (!empty($html5_fields[$name])) {
					
					
				   $code = $field['formcode']; 
				   $types = OPCTransform::getFT($code, 'input', 'type', 'type', 'text', '>', 'type');
				   if (!empty($types)) {
				    $firsttype = reset($types); 
				    if ($firsttype === 'text') {
						$code = str_replace('type="text"', 'type="'.$html5_fields[$name].'"', $code); 
						$code = str_replace('type=\'text\'', 'type=\''.$html5_fields[$name].'\'', $code); 
						$fields['fields'][$k]['formcode'] = $field['formcode'] = $code; 
						$fields['fields'][$k]['opc_html5'] = true; 
						
						$field['type'] = $html5_fields[$name]; 
					}
				   }
				}
				}
				
				if (!empty($html5_fields_extra))
				if (empty($field['opc_html5_pat'])) {
				if (!empty($html5_fields_extra[$name])) {
					
					
				   $code = $field['formcode']; 
				   if (stripos($code, $html5_fields_extra[$name])===false) {
					   $ins = ' validate="validate" '.urldecode($html5_fields_extra[$name]);
						$code = str_replace(' name="', $ins.' name="', $code); 
						$code = str_replace(' name=\'', $ins.' name=\'', $code); 
						
						$field['formcode'] = $fields['fields'][$k]['formcode'] = $code; 
						$fields['fields'][$k]['opc_html5_pat'] = true; 
					
				   }
				   }
				}
				
				
				
				if (!empty($html5_placeholder))
				if (empty($field['placeholder'])) {
				if (!empty($html5_placeholder[$name])) {
					
					
				   $code = $field['formcode']; 
				   if (stripos($code, 'placeholder=')===false) {
					   $ins = ' validate="validate" placeholder="'.htmlentities(JText::_(urldecode($html5_placeholder[$name]))).'" ';
						$code = str_replace(' name="', $ins.' name="', $code); 
						$code = str_replace(' name=\'', $ins.' name=\'', $code); 
						
						$field['formcode'] = $fields['fields'][$k]['formcode'] = $code; 
						$fields['fields'][$k]['placeholder'] = urldecode($html5_placeholder[$name]); 
					
				   }
				   }
				}
				
				if (!empty($html5_validation_error))
				if (empty($field['errormsg'])) {
				if (!empty($html5_validation_error[$name])) {
					
					
				   $code = $field['formcode']; 
				   if (stripos($code, 'onerrormsg=')===false) {
					   $ins = ' validate="validate" onerrormsg="'.htmlentities(JText::_(urldecode($html5_validation_error[$name]))).'" ';
						$code = str_replace(' name="', $ins.' name="', $code); 
						$code = str_replace(' name=\'', $ins.' name=\'', $code); 
						
						$field['formcode'] = $fields['fields'][$k]['formcode'] = $code; 
						$fields['fields'][$k]['errormsg'] = urldecode($html5_validation_error[$name]); 
					
				   }
				   }
				}
				
				}
				
				if (empty($inloop)) {
				$prefix = 'shipto_'; 
				$inloop = true; 
				goto start; 
				}
				
				if (!empty($field['required'])) {
					 $code = $field['formcode']; 
					 if (stripos($code, 'required="required')==false) {
						 
						 $ins = ' required="required" '; 
						 $code = str_replace(' name="', ' '.$ins.' name="', $code); 
						$code = str_replace(' name=\'', ' '.$ins.' name=\'', $code);
						$fields['fields'][$k]['formcode'] = $code; 
						 
					 }
				}
				
				
				
				
				
		}
	 
	 
	 
	 
	 
  }
  
  public static function reorderFields(&$userFields, $skip=array())
 {


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 

 
	 if (OPCrenderer::hasDel()) return; 
	 
 if (empty($userFields)) return;
 if (empty($userFields['fields'])) return;
 
 include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
    // reorder the registration fields (display name, email, email2, username, pwd1, pwd2): 
$orig = $userFields; 
$newf = array(); 
$newf['fields'] = array(); 

if (!empty($userFields['fields']['delimiter_userinfo']))
{
 $newf['fields']['delimiter_userinfo'] = $userFields['fields']['delimiter_userinfo']; 
}



if (isset($userFields['fields']['name']))
$newf['fields']['name'] = $userFields['fields']['name']; 



if (!in_array('email', $skip))
if (isset($userFields['fields']['email']))
$newf['fields']['email'] = $userFields['fields']['email']; 

//if (isset($email2))
if (!in_array('email', $skip))
if (!empty($userFields['fields']['email2']))
$newf['fields']['email2'] = $userFields['fields']['email2']; //$email2;

if (isset($userFields['fields']['register_account']))
if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
if ((isset($userFields['fields']['password'])) || (isset($userFields['fields']['opc_password'])))
{
  $newf['fields']['register_account'] = $userFields['fields']['register_account']; 
}

if (VM_REGISTRATION_TYPE != 'OPTIONAL_REGISTRATION')
if (isset($userFields['fields']['username']))
{
$newf['fields']['username'] = $userFields['fields']['username']; 
}

if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION')
if (isset($userFields['fields']['username']))
{
$newf['fields']['username'] = $userFields['fields']['username']; 
}


if (isset($userFields['fields']['opc_password']))
$newf['fields']['opc_password'] = $userFields['fields']['opc_password']; 

if (isset($userFields['fields']['opc_password2']))
$newf['fields']['opc_password2'] = $userFields['fields']['opc_password2']; 


if (isset($userFields['fields']['password']))
$newf['fields']['password'] = $userFields['fields']['password']; 

if (isset($userFields['fields']['password2']))
$newf['fields']['password2'] = $userFields['fields']['password2']; 

//delimiter_billto
if (!empty($userFields['fields']['delimiter_billto']))
{
 $newf['fields']['delimiter_billto'] = $userFields['fields']['delimiter_billto']; 
}

//delimiter_userinfo

if (!empty($klarna_se_get_address))
if (!empty($userFields['fields']['socialNumber']))
{
 $newf['fields']['socialNumber'] = $userFields['fields']['socialNumber']; 
 $newf['fields']['socialNumber']['formcode'] = str_replace('name="', ' autocomplete="off" name="', $userFields['fields']['socialNumber']['formcode']); 
 
}

$ret = array(); 
$ret['fields'] = array(); 
// adding reg f

	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
	  if (in_array('email', $skip))
	  if (isset($userFields['fields']['email2']))
	  {
	  $email2 = $userFields['fields']['email2']; 
	  OPCTransform::insertAfter($userFields['fields'], 'email', $email2, 'email2'); 
	  }

$ins = array(); 
foreach ($newf['fields'] as $key=>$val)
 {
   $ret['fields'][$key] = $val;
   $ins[] = $key; 
 }
 if (!empty($ins))
 {
 foreach ($userFields['fields'] as $key2=>$val2)
 {
   if (!in_array($key2, $ins))
   $ret['fields'][$key2] = $val2; 
 }
 }
 else return $userFields; 
 
 
 $userFields['fields'] = $ret['fields']; 
 return $userFields; 

 }

  
  public static function hasMissingFields(&$BTaddress, $cart=null) 
  {
	 if (empty($cart)) $cart = new stdClass(); 
	  
    $ignore = array('delimiter', 'captcha', 'hidden'); 
	$ignore_fields = array('password2', 'tos'); 
	
	$opc_disable_customer_email = OPCconfig::get('opc_disable_customer_email', false); 
	 if (!empty($opc_disable_customer_email)) $ignore_fields[] = 'email'; 
	
  $types = array(); 
   foreach ($BTaddress as $key=>$val)
     {
	   //if (in_array($val['name'], $corefields)) continue; 
	   if (in_array($val['type'], $ignore)) continue; 
	    if (in_array($val['name'], $ignore_fields)) continue; 
	   if (empty($val['value']))
	   if (!empty($val['required']))
	    {
		  if ($key == 'virtuemart_state_id')
				{
				  if (isset($BTaddress['virtuemart_country_id'])) {
				  $c = $BTaddress['virtuemart_country_id']['value']; 
				  $stateModel = OPCmini::getModel('state'); //new VirtueMartModelState();
	
				  $states = $stateModel->getStates( $c, true, true );
				  if (!empty($states)) 
				  {
				  
				  return true; 
				  }
				  }
				  continue; 
				}
				OPCloader::opcDebug('virtuemart_country_id and virtuemart_state_id do not match or are empty and thus loading unlogged them'); 
				return true; 
		}
		
		
		
		if (!empty($cart))
		if (!empty($cart->BT)) {
			if (is_array($cart->BT[$key])) {
				$val = $val['value']; 
				if (!is_array($val)) $val = explode('|*|', $val); 
				if (is_array($val)) {
					
					
					
				  $dif = array_diff($val, $cart->BT[$key]); 
				  
				  
				  if (!empty($dif)) return true; 
				}
			}
			else
		   if ((!empty($cart->BT[$key])) && (!empty($val['value'])) && ($val['value'] != $cart->BT[$key]))  {
			   OPCloader::opcDebug('displayed value '.$val['value'].' does not match cart value '.$cart->BT[$key]); 
			   return true; 
		   }
		}
		
	    //$types[] = $val['type']; 
	 }
	 return false; 
  }
  
  
  
  
}