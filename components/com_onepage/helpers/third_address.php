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

class OPCthirdAddress {

  public static function getUserFieldsThird(&$cart, $user_id=0, $order_id=0) {
	  
 	 $render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
	 if (empty($render_in_third_address)) return ''; 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
	 
	 if (!isset($cart->RD)) $cart->RD = array(); 
	 /*
     if (isset($cart->STaddress))
	 $t = $stf = $cart->STaddress; 
     else
	 if (isset($cart->BTaddress))
	 $t = $stf = $cart->BTaddress; 
	 
	 if (isset($cart->BTaddress))
	 $t = $btf = $cart->BTaddress; 
	 
	 
	 if (empty($t)) 
		 */
	 {
		 
		 $data_third = array(); 
		  $db = JFactory::getDBO(); 
		 if (!empty($order_id)) {
			$q = 'select * from #__virtuemart_order_userinfos where virtuemart_order_id = '.(int)$order_id.' and address_type = "RD" limit 0,1'; 		    
		    $db->setQuery($q); 
			$data_third = $db->loadAssoc(); 
		 }
		 
		 if (empty($data_third))
		 if (!empty($user_id)) {
		
		$q = 'select * from #__virtuemart_userinfos where virtuemart_user_id = '.(int)$user_id.' and address_type = "RD" limit 0,1'; 
		$db->setQuery($q); 
		$data_third = $db->loadAssoc(); 
	
	
	
		}
		
	
	
	
	
	if (!empty($cart->RD)) {
	  foreach ($cart->RD as $k=>$v) {
		 if (!empty($v))
	     $data_third[$k] = $v; 
	  }
	
		 }
		 
		 if (!empty($data_third))
		 foreach ($data_third as $k=>$v) {
		    if (!isset($cart->RD[$k]))
			$cart->RD[$k] = $v; 
		 }
		 
		 
		 
		 
		 
		 
	   $userFields = null;
    
	
				$empty = ''; 
			$array = array(); 
	
	
	if (!class_exists('VirtueMartModelUserfields'))
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'userfields.php');
		$userFieldsModel = VmModel::getModel('userfields');
	$prepareUserFields = $userFieldsModel->getUserFields(
					 'account'
			,	$array // Default toggles
			,	$array 
			);
			
	
	  VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
	  
	    $array = $data_third; 
		$empty = ''; 
		$btf = $userFieldsModel->getUserFieldsFilled($prepareUserFields,$array,$empty);
		
			
	 }
 
	 $result = array(); 
	 
	 $iterate = $render_in_third_address; 
	 $switch_rd = OPCconfig::get('opc_switch_rd', false); 
	 
	 if (!empty($order_id) && (!empty($switch_rd))) {
		 $iterate = array(); 
		 if (!empty($btf))
		 foreach ($btf['fields'] as $field) {
			 if ($field['type'] === 'delimiter') continue; 
			 $iterate[$field['name']] = $field['name']; 
		 }
		 
		 unset($iterate['username']);
		 unset($iterate['password']);
		 unset($iterate['password2']);
		 unset($iterate['email']);
		 unset($iterate['tos']);
		 unset($iterate['name']);
		 
		 
	 }
	 
	 
	 
	 
	 foreach($iterate as $k=>$name)
	 {
		 
		 $cf = ''; 
		 if (isset($btf['fields'][$name]))
		     $cf = $btf['fields'][$name]; 
		 else
		 if (isset($stf['fields'][$name]))
		 {
			 $cf = $stf['fields'][$name]; 
		 }
		 
		 if (empty($cf)) continue; 
		 
		 $r = str_replace('"shipto_', '"', $cf['formcode']); 
		 
		 if (stripos($name, 'virtuemart_country_id')!==false)
		 {
			 $r = str_replace('id=', ' prefix="third_" onchange="Onepage.changeStateList(this)" id=', $r); 
		 }
		 
		 
		 
			 $r = str_replace('"'.$name, '"third_'.$name, $r); 
			 $value = ''; 
			 if (isset($cart->RD) && (isset($cart->RD[$name]))) {
			  $value = $cart->RD[$name]; 
			 }
			 else
			 {
				
			 }
			 if (!empty($value))
			 $r = str_replace('"'.$cf['value'].'"', '"'.$value.'"', $r); 
			 
			
			 
			 if ($name == 'virtuemart_country_id') {
				 
				
				 
				 $r = str_replace('virtuemart_country_id_field', 'virtuemart_country_id', $r); 
				 
				 if (!empty($value)) $dc = $value; 
				 else
				 {
					 if (!class_exists('OPCloader')) {
					 		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');  

					 }
					 if (empty($cart->RD['virtuemart_country_id']))
				     $dc = OPCloader::getDefaultCountry($cart, true); 
					 else $dc = $cart->RD['virtuemart_country_id']; 
					 
					 
					
					 
				 }
				 if (!empty($dc)) {
					 $r = str_replace('selected="selected"', '', $r); 
				     $r = str_replace('value="'.$dc.'"', ' selected="selected" value="'.$dc.'"', $r); 
					 
					 
				 
				 }
				 
				 $name = 'virtuemart_country_id'; 
			 }
			 
			 if ($name == 'virtuemart_state_id') {
				 $r = str_replace('virtuemart_state_id_field', 'virtuemart_state_id', $r); 
				 
				 
				 
				 if (!empty($value)) $ds = $value; 
				 else
				 if (!empty($cart->BT['virtuemart_state_id']))
				 $ds = $cart->BT['virtuemart_state_id']; 
				 else
				 $ds = 0; 
			 
				 $cart->RD['virtuemart_state_id'] = $ds; 
				 
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
				 
				 $html = OPCCommonHtml::getStateHtmlSelectByStateAndCountry($ds, $dc, 'third_', false, false); 
				 $name = 'virtuemart_state_id'; 
				 $r = $html; 
				 
			 }
		     $toA = $cf; 
			 $toA['value'] = $value; 
			 $toA['formcode'] = $r; 
			 $toA['name'] = 'third_'.$name; 
			 $result['third_'.$name] = $toA; 
			 
		 
	 }
	 
	 return $result; 
	 
	 
 }
 
 public static function renderThirdAddressWrap($virtuemart_order_id, $order=null) {
	 $thirdaddress = OPCthirdAddress::populateAddressFields($virtuemart_order_id); 
	 
	 
	 
	 if (empty($order)) {
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			
			$orders = OPCmini::getModel('orders'); 
			$order = $orders->getOrder($virtuemart_order_id); 
			
	 }
	 
    ob_start(); 
	?><div class="third_address_backend">
<strong><?php echo JText::_('COM_ONEPAGE_THIRD_ADDRESS') ?></strong><br/>
<table border="0"><?php
if (!empty($thirdaddress)) 
foreach ($thirdaddress['fields'] as $field) {
if (!empty($field['value']))
{ 
  echo '<tr><td class="key">' . $field['title'] . '</td>' . '<td>' . $field['value'] . '</td></tr>'; 
}
}

$root = Juri::root(); 
if (substr($root, -1) !== '/') $root .= '/';  

$isSite = JFactory::getApplication()->isSite(); 
if (!$isSite) {
  $root .= 'administrator/'; 
}

//JHTMLOPC::_('behavior.modal', '.thirdaddressm'); 
JHTMLOPC::script('fancybinder.js', 'components/com_onepage/assets/js/', false);
echo '<tr><td colspan=2"><a  class="thirdaddressm" rel="{handler: \'iframe\', size: {x: 1000, y: 800}}" href="'.$root.'index.php?option=com_onepage&view=third_address&user_id='.$order['details']['BT']->virtuemart_user_id.'&virtuemart_order_id='.$order['details']['BT']->virtuemart_order_id.'&nosef=1&tmpl=component" onclick="javascript: return op_openlink(this);">'.JText::_('COM_ONEPAGE_THIRD_ADDRESS_EDIT').'...</a> </td></tr>'; 
?></table></div>
<?php 
$css = '
div.fancybox-content {
  min-width: 70%; 
  max-width: 90%; 
  min-height: 70%
  max-height: 85%; 
}
'; 
JFactory::getDocument()->addStyleDeclaration($css); 
   $html = ob_get_clean(); 
   return $html; 
 }
 public static function renderThirdAddress(&$cart, $user_id=0, $suffix='', $order_id=0)
 {
	

	 $result = OPCthirdAddress::getUserFieldsThird($cart, $user_id, $order_id); 
	 
	 
	
	 $userFields = array(); 
	 $userFields['fields'] = $result; 
	 $cart->RDaddress = $userFields; 
	 //OPCUserFields::getUserFields($userFields, $OPCloader, $cart); 
	  $vars = array('rowFields' => $userFields, 
				 'cart'=> $cart,
				 'is_registration' => false);
				 
				 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
			
		
		$renderer = OPCrenderer::getInstance(); 
		
		$renderer->cart =& $cart; 
		
      $html = $renderer->fetch($renderer, 'list_user_fields.tpl', $vars); 
	  $vars['fields_html'] = $html; 
	  $vars['checked'] = false; 
	  if (!empty($cart->RDopen)) 
	  {
		  $vars['checked'] = ' checked="checked" selected="selected" '.$suffix; 
	  }
	  
	  $html = $renderer->fetch($renderer, 'third_address', $vars); 
	  return $html; 
	
 }
 public static function validateThirdAddress(&$cart)
 {
	 $render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
		
		$missing = $missinga = array(); 
		
		
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');  
	
		$ff = OPCthirdAddress::getUserFieldsThird($cart);
		foreach ($render_in_third_address as $k=>$v)
		{
		    	
			$val = JRequest::getVar('third_'.$v, ''); 
			if (empty($val)) {
			$required = OPCUserFields::getIfRequired($v);
			if (!empty($required)) {
				$missing[$v] = $v; 
			}
			
			}
			
			$cart->RD[$v] = $val; 
			
			
		}
			if (!empty($missing)) {
			  foreach ($missing as $z=>$vv)
			  {
				  if ($z == 'virtuemart_state_id') {
				   if (!class_exists('VirtueMartModelState')) require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'state.php');
				   
				   $country = JRequest::getVar('third_virtuemart_country_id', 0); 
				   if (empty($country)) unset($missing[$z]); 
				   
				   if (VirtueMartModelState::testStateCountry($country, $vv)) {
						
						unset($missing[$z]); 
					}
				  }
				  if (!isset($ff['third_'.$z])) continue; 
				  $field = (array)$ff['third_'.$z]; 
				  
				  if (!empty($field)) {
				  $missinga[] = OPCLang::sprintf('COM_VIRTUEMART_MISSING_VALUE_FOR_FIELD',OPCLang::_($field['title']) );
				  }
			  }
			}
			
		return $missinga; 
 }
 public static function getUserinfosids($user_id=0, $order_id=0, $isAdmin=false) {
    $db = JFactory::getDBO(); 
	if (!empty($user_id)) {
	 if ($isAdmin) {
	 
	   $q = 'select virtuemart_userinfo_id from #__virtuemart_userinfos where address_type = "BT" and virtuemart_user_id = '.(int)$user_id.' limit 0,1'; 
	 
	 }
	 else
	 {
		$q = 'select virtuemart_userinfo_id from #__virtuemart_userinfos where address_type = "RD" and virtuemart_user_id = '.(int)$user_id.' limit 0,1'; 		 
	 }
	 $db->setQuery($q); 
	 $virtuemart_userinfo_id = $db->loadResult(); 
	 
	}
	
	if (!empty($order_id)) {
	 $q = 'select `virtuemart_order_userinfo_id` from `#__virtuemart_order_userinfos` where `address_type` = "RD" and `virtuemart_order_id` = '.(int)$order_id.' limit 0,1'; 
	 $db->setQuery($q); 
	 $virtuemart_order_userinfo_id = $db->loadResult(); 
	}
	
	$ret = array(); 
	if (!empty($virtuemart_userinfo_id)) $ret['virtuemart_userinfo_id'] = $virtuemart_userinfo_id; 
	else $ret['virtuemart_userinfo_id'] = 'NULL'; 
	
	if (!empty($virtuemart_order_userinfo_id)) $ret['virtuemart_order_userinfo_id'] = $virtuemart_order_userinfo_id; 
	else $ret['virtuemart_order_userinfo_id'] = 'NULL'; 
	
	return $ret; 
	
 }
 public static function storeThirdAddress(&$cart, &$order, $user_id=0, $isCheckout=false) {
	

	$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
	$switch_rd = OPCconfig::get('opc_switch_rd', false); 
	$skip = array('virtuemart_userinfo_id', 'virtuemart_order_userinfo_id', 'virtuemart_user_id', 'address_type'); 
	if (empty($render_in_third_address)) return; 
	$db = JFactory::getDBO(); 
	$opc_btrd_def = OPCconfig::get('opc_btrd_def', false); 
	
	
	//copy bt to st if needed: 
	$opc_copy_bt_st = OPCconfig::get('opc_copy_bt_st', false); 
			if (!empty($opc_copy_bt_st)) {
				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
				OPCLoggedShopper::copyBTintoST($order); 
			}
	
	
	
    
		
		
		
	   
	   if (empty($user_id)) {
	     $user_id = JFactory::getUser()->get('id'); 
	   
	   if (empty($user_id))
	   {
		   if (!empty($GLOBALS['opc_new_user'])) $user_id = (int)$GLOBALS['opc_new_user']; 
		   else
		   if (!empty($GLOBALS['is_dup'])) $user_id = (int)$GLOBALS['is_dup']; 
	   }
	   }
	   
	   if (!empty($order)) {
		   
		   $isSite = JFactory::getApplication()->isSite(); 
		   $admin = false; 
		   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	       if ((OPCmini::isSuperVendor()) || ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser()->authorise('core.admin', 'com_virtuemart')))) { 
			  $admin = true; 
			}
			if ($isSite) $admin = false; 
			// edit BT address of a registered user only if we got the switch ON
			if (empty($switch_rd)) $admin = false; 
		   
	     $ids = self::getUserinfosids($user_id, $order['details']['BT']->virtuemart_order_id, $admin); 
		 
		 if (!empty($order['details']['BT']->virtuemart_user_id))
		 $user_id = $order['details']['BT']->virtuemart_user_id; 
		 
			}
			else
			{
				$ids = self::getUserinfosids($user_id, 0); 
			}
	   
	   
	   if (((empty($cart->RD)) && (!empty($cart->ST))) && (empty($opc_btrd_def))) {
		$cart->RD = $cart->ST; 
		// create new RD address in userinfos, do not update if already exists:  
		if (($admin) || ((empty($ids['virtuemart_userinfo_id'])) || ($ids['virtuemart_userinfo_id']==='NULL'))) {
		   $cart->RDopen = true; 		
		}
		else {
			$cart->RDopen = false; 		
		}
	}
	else
	if ((empty($cart->RD)) && (!empty($cart->BT))) {
		$cart->RD = $cart->BT;
		// create new RD address in userinfos, do not update if already exists: 
		if (($admin) || ((empty($ids['virtuemart_userinfo_id'])) || ($ids['virtuemart_userinfo_id']==='NULL'))) {
		  $cart->RDopen = true;
		}
		else {
		  $cart->RDopen = false; 		
		}
		
	}
	   
	    $insert = array(); 
	    $jnow = JFactory::getDate();
		if (method_exists($jnow, 'toMySQL'))
		$now = $jnow->toMySQL();
		else $now = $jnow->toSQL(); 
	   
	   //generic insert data: 
	   $insert['created_on'] = $now; 
	   $insert['modified_on'] = $now;
	   $insert['modified_by'] = $user_id;
	   $insert['created_by'] = $user_id;
	   $insert['virtuemart_user_id'] = $user_id;
	   
	   
	   if (!empty($cart->RD)) {
	   
	    
	   
	   
	   
	   
	  
	  
	   
	    if (empty($admin)) 
		$insert['address_type'] = 'RD';   
		else
		$insert['address_type'] = 'BT';   
	   
	   $insert['address_type_name'] = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_RD'));
	   if (!empty($user_id)) {
		   
		   $table = '#__virtuemart_userinfos';
	       $ui = OPCmini::getColumns($table); 
	   
	  
	  
	   $emptyData = true; 
	    foreach ($render_in_third_address as $field_name) {
		
		   if (isset($ui[$field_name])) {
			 if (isset($cart->RD[$field_name])) {
				 
		    if (in_array($field_name, $skip)) continue; 
				 
		     $insert[$field_name] = $cart->RD[$field_name]; 
			 $emptyData = false; 
			 }
		   }
		}
		
		if ($emptyData) return; 
	    
		//$insert['virtuemart_userinfo_id'] = 'NULL'; 
		$insert['virtuemart_userinfo_id'] = $ids['virtuemart_userinfo_id']; 
		
		
		
		
		
		
		// inserts OR updates the row, if the address is not selected, it won't get updated in userinfos !
	    if ($cart->RDopen) {
		  OPCmini::insertArray($table, $insert, $ui); 
		}
		
		foreach ($insert as $k=>$v) {
		  $cart->RD[$k] = $v; 
		}
		
		
		if ($cart->RDopen) {
		if (method_exists($cart, 'setCartIntoSession')) {
			// only for FE
		  $cart->setCartIntoSession(); 
		  $session = JFactory::getSession(); 
		  $data = $session->get('opc_fields', '', 'opc'); 
		 if (!empty($data)) {
		  $fields = @json_decode($data, true); 
		  $fields['RD'] = $insert; 
		  $txt = json_encode($fields); 
		  $session = JFactory::getSession(); 
		  $session->set('opc_fields', $txt, 'opc'); 
		  
		 }
		
		}
		}
		
		
		// so this can be used to store the order userinfos as below
		unset($insert['virtuemart_userinfo_id']); 
	   
	     //echo 'Userinfo updated'."<br />"; 
	   
	   }
	   
	   
	   // we've got order information here: 
	   if ((!empty($order)) && (isset($order['details']['BT'])) && (!empty($order['details']['BT']->virtuemart_order_id))) {
	   
	   
	    $table = '#__virtuemart_order_userinfos';
	    $oi = OPCmini::getColumns($table); 
	   
	     $order_id = (int)$order['details']['BT']->virtuemart_order_id; 
	     $insert['virtuemart_order_userinfo_id'] = $ids['virtuemart_order_userinfo_id']; 
		 $insert['address_type'] = 'RD'; 
		//note: $ids['virtuemart_order_userinfo_id'] shoudl be 'NULL' here unless double orders are used... 
	    if (($switch_rd) && (!empty($order)) && ($isCheckout))  {
			
			
		 $q = 'select * from `#__virtuemart_order_userinfos` where `address_type` = "RD" and `virtuemart_order_id` = '.(int)$order['details']['BT']->virtuemart_order_id; 
		 $db->setQuery($q); 
		 $res = $db->loadAssocList(); 
		
		  if (empty($res)) {
		   $db = JFactory::getDBO(); 
		   
		   // WE ARE CHANGING FROM BT TO RD, BUT WE STILL HAVE THE NAME:
		   $rdtypnema = OPCLang::_(OPCLang::_('COM_ONEPAGE_FE_BT'));
		   $q = 'update `#__virtuemart_order_userinfos` set `address_type` = "RD", `address_type_name` = "'.$db->escape($rdtypnema).'" where `virtuemart_order_userinfo_id` = '.(int)$order['details']['BT']->virtuemart_order_userinfo_id; 
		   $db->setQuery($q); 
		   $db->query(); 
		  }
		   
		   
		   
		   // in checkout we must unset this in case it was anyhow filled
		   if (!empty($res)) {
			   $insert['virtuemart_order_userinfo_id'] = (int)$order['details']['BT']->virtuemart_order_userinfo_id; 
		   }
		   else {
		    $insert['virtuemart_order_userinfo_id'] = 'NULL'; 
		   }
		   
	       $insert['address_type'] = 'BT';   
		   $insert['email'] = $order['details']['BT']->email; 
		   if (isset( $order['details']['BT']->customer_note ))
		   $insert['customer_note'] =  $order['details']['BT']->customer_note; 
		  
		   $opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		   if (isset($order['details']['BT']->$opc_vat_key)) {
		     $insert[$opc_vat_key] = $order['details']['BT']->$opc_vat_key; 
		   }
		   if (isset($order['details']['BT']->opc_vat_info)) {
		     $insert['opc_vat_info'] = $order['details']['BT']->opc_vat_info; 
		   }
		   
		   //copy fields which are not yet set
		   foreach ($order['details']['BT'] as $k=>$v) {
			   		if ((!isset($insert[$k] )) && (!in_array($k, $render_in_third_address)))
					{
						$insert[$k] = $v; 
					}
			   }
		   
		   
		  
	   }
	   
	   
	  
	   //$insert['virtuemart_order_userinfo_id'] = 'NULL'; 
	  
	   $insert['virtuemart_order_id'] = $order_id; 
	  
	    $emptyData = true; 
	    foreach ($render_in_third_address as $field_name) {
		   if (in_array($field_name, $skip)) continue; 
		   if (isset($oi[$field_name])) {
			 if (isset($cart->RD[$field_name])) {
		     $insert[$field_name] = $cart->RD[$field_name]; 
			 $emptyData = false; 
			 }
		   }
		}
		 
		 OPCmini::insertArray($table, $insert, $oi); 
	     //echo 'Order updated'."<br />"; 
	   }
	   
	  
	   
	}
	
	
	
 }
 
 // used to store data at FE and BE
 public static function opcthird($be = false, $ref = null) {
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	    $user_id = JRequest::getVar('user_id', JRequest::getVar('virtuemart_user_id', 0)); 
		if (is_array($user_id)) $user_id = reset($user_id); 
		$user_id = (int)$user_id; 
		$user_id_logged = JFactory::getUser()->get('id'); 
		$user_id_logged = (int)$user_id_logged; 
		if (empty($user_id)) {
		  $user_id = $user_id_logged;
		}
		
		$admin = false; 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	     if ((OPCmini::isSuperVendor()) || ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser()->authorise('core.admin', 'com_virtuemart')))) { 
			  $admin = true; 
			}
	 
	   if (!$admin) 
	   {
		   if ($user_id !== $user_id_logged) {
		   
		    JFactory::getApplication()->enqueueMessage('Access Denied', 'error'); 
		    return; 
		   }
		   
		   
		    if (empty($user_id)) return; 
	   }
	   
	   
	    $order_id = JRequest::getInt('virtuemart_order_id', 0); 
		
		 
		 
		$third_address_opened = true;
		$saved_fields['third_address_opened'] = $third_address_opened; 
		$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
		$arr = array(); 
		foreach ($render_in_third_address as $k=>$v)
		{
			$val = JRequest::getVar('third_'.$v, ''); 
			$arr[$v] = $val; 
			
		}
		$arr['virtuemart_user_id'] = $user_id; 
		
		$saved_fields['RD'] = $arr;  
		
		$isSite = JFactory::getApplication()->isSite(); 
		if (!empty($isSite)) {

		if (!class_exists('VmConfig'))
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		 if (!defined('VMPATH_SITE'))
		 VmConfig::loadConfig(); 
		
			if (!class_exists('VirtueMartCart'))
		    require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		
		     $cart = VirtuemartCart::getCart(); 


		
		}
		else {
		  $cart = new stdClass(); 
		}
		$cart->RDopen = true; 
		$cart->RD = $arr; 
		
		if (!empty($arr)) 
		  {
					
					
					if ($admin) {
					    $order_id = JRequest::getInt('virtuemart_order_id', false); 
						if (!empty($order_id)) {
					    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
						$orders = OPCmini::getModel('orders'); 
						$order = $orders->getOrder($order_id); 
						}
						else $order = null; 
					}
					else {
					  $order = null; 
					}
					
					
					OPCthirdAddress::storeThirdAddress($cart, $order, $user_id);
			}	
		
		
		$language = JFactory::getLanguage();
		$language->load('com_onepage', JPATH_SITE, 'en-GB', true);
		$language->load('com_onepage', JPATH_SITE, null, true);
		
		$msg = JText::_('COM_ONEPAGE_THIRD_ADDRESS_CHANGED'); 
		
		
		if (empty($be))
		return $ref->returnTerminate($msg, '', 'index.php?option=com_onepage&view=third_address', 'notice'); 
		 
		 
 }
 
 private static function _auth() {
	   
		 $action = 'vm.product'; 
		 $assetName = 'com_virtuemart.product'; 
		 $z = JFactory::getUser()->authorise($action, $assetName);
		 return $z; 
		
	}
 public static function checkTasks() {
	$s = JFactory::getApplication()->isSite(); 
	if ($s) return; 
    $option = JRequest::getVar('option', ''); 
	$task = JRequest::getVar('task', ''); 
	$view = JRequest::getVar('view', ''); 
	
	
	if ($option === 'com_virtuemart')
		if ($view === 'user') 
			if (($task === 'apply') || ($task === 'save'))
			{
				$rid = JRequest::getVar('third_virtuemart_userinfo_id', null); 
				if (!empty($rid)) {
						self::opcthird(true); 
				}
			}
	
 }
 public static function getTabs(&$view, &$tabs) {
	 
	 if (!self::_auth()) return; 
		
		JFactory::getLanguage()->load('com_onepage', JPATH_ADMINISTRATOR); 
		$langtag = JFactory::getLanguage()->getTag(); 
		JFactory::getLanguage()->load('com_onepage', JPATH_SITE, $langtag, true); 
		
		$class = get_class($view); 
		switch ($class)
		{
			case 'VirtuemartViewUser': 
			
			  $virtuemart_user_id = $vmid = JRequest::getVar('virtuemart_user_id'); 
			  $st = JRequest::getVar('task', false); 
			  if ($st === 'addST') return; 
			  
			  // unknown category ID: 
			  if (empty($virtuemart_user_id)) return; 
			  if (is_array($virtuemart_user_id)) $virtuemart_user_id = reset($virtuemart_user_id); 
			  $virtuemart_user_id = (int)$virtuemart_user_id; 


			 
			
			    $cart = new stdClass(); 
				$cart->RD = array(); 
				$cart->RDaddress = array(); 
				
				$data = self::getUserFieldsThird($cart, $virtuemart_user_id, 0); 
				
				$cart->RDopen = true; 
				$suffix = ' readonly="readonly" style="display: none;" '; 
				$third_html = self::renderThirdAddress($cart, $virtuemart_user_id, $suffix);
				
				$paths = array(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR);
				
				$css = ' #third_address_label { display: none; } '; 
				$doc = JFactory::getDocument(); 
				if (method_exists($doc, 'addStyleDeclaration')) $doc->addStyleDeclaration($css); 
				
				$tabs['thirdaddress'] = JTExt::_('COM_ONEPAGE_THIRD_ADDRESS_TAB'); 
				
				$db = JFactory::getDBO(); 
				$q = 'select virtuemart_userinfo_id from #__virtuemart_userinfos where virtuemart_user_id = '.(int)$virtuemart_user_id.' and address_type="ST" order by created_on asc'; 
				$db->setQuery($q); 
				$sid = $db->loadResult(); 
				if (!empty($sid)) {
				 $view->shipToId = (int)$sid; 
				 $tabs['shipto'] = JTExt::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); 
				 $model = VmModel::getModel('User');
				 $model->setId($virtuemart_user_id);
				 $userFieldsArray = $model->getUserInfoInUserFields('edit_shipto','ST',$sid,false);
				 $userFieldsST = $userFieldsArray[$sid];
				 $view->assignRef('shipToFields', $userFieldsST);
				 $third_html .= '<input type="hidden" name="shipto_virtuemart_userinfo_id" value="'.(int)$sid.'" />'; 
				 
				}
				
				
				$sids = self::getUserinfosids($virtuemart_user_id, 0, false); 
				if (!empty($sids['virtuemart_userinfo_id'])) {
				   $third_html .= '<input type="hidden" name="third_virtuemart_userinfo_id" value="'.(int)$sids['virtuemart_userinfo_id'].'" />'; 
				}
				
				foreach ($paths as $p) {
				  $view->addTemplatePath( $p );
				}
					$view->assignRef('cart', $cart); 
					$view->assignRef('third_html', $third_html); 
					
			  break; 
			  
		}
 }
 
 public static function populateAddressFields($order_id)
 {
	 $order_id = (int)$order_id; 
	 $ret = array(); 
	 
	 if (empty($order_id)) return $ret;
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');
	 
	 $orderModel = OPCmini::getModel('Orders'); 
	 $order= $orderModel->getOrder($order_id);
	 if (empty($order)) return $ret; 
	 if (empty($order['details']['BT'])) return $ret; 
	 
	 if (isset($order['details']['BT']->order_language))
	 $langO = $order['details']['BT']->order_language; 
	 
	 $lang = JFactory::getLanguage();
	 $extension = 'com_onepage';
	 $base_dir = JPATH_SITE;
	 if (!empty($langO))
	 $language_tag = $langO; 
     else
	 $language_tag = $lang->getTag(); 
 
	 $reload = true;
	 
	 
	 $lang->load($extension, $base_dir, 'en-GB', $reload);
	 $lang->load($extension, $base_dir, $language_tag, $reload);
	 
	 
	 $render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
	 $db = JFactory::getDBO(); 
	 $switch_rd = OPCconfig::get('opc_switch_rd', false); 
	 if (!empty($switch_rd)) {
		 $q = 'select * from #__virtuemart_userfields where `account` = 1'; 
		 $db->setQuery($q); 
		 $res = $db->loadAssocList(); 
	 }
	 else {
	 $cols = array(); 
	
	 
	 foreach ($render_in_third_address as $f)
	 {
		 $cols[] = "'".$db->escape($f)."'"; 
	 }
	 
	 if (empty($cols)) return $ret;
	 
	 //$q = 'select `name`, `title`, `type` from #__virtuemart_userfields where `name` IN ('.implode(',', $cols).') '; 
	 $q = 'select * from #__virtuemart_userfields where `name` IN ('.implode(',', $cols).') '; 
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 }
	
	 
	 if (empty($res)) return $ret;
	 
	 $_selection = array(); 
	 foreach ($res as $row)
	 {
		 $item = (object)$row; 
		 $_selection[$row['name']] = $item; 
	 }
		
	 $q = 'select * from #__virtuemart_order_userinfos where `virtuemart_order_id` = '.(int)$order_id.' and address_type = "RD" order by modified_on desc limit 0,1'; 
	 $db->setQuery($q); 
	 $data = $db->loadAssoc();
	 
	 
	 if (empty($data)) return $ret;
	 $userFieldsModel = OPCmini::getModel('userfields');
	 $_userData = array(); 
	 
	 
	 $iterate = $render_in_third_address; 
	 $switch_rd = OPCconfig::get('opc_switch_rd', false); 
	 
	 if (!empty($order_id) && (!empty($switch_rd))) {
		 $iterate = array(); 
		 if (!empty($res))
		 foreach ($res as $field) {
			 if ($field['type'] === 'delimiter') continue; 
			 $iterate[$field['name']] = $field['name']; 
		 }
		 
		 unset($iterate['username']);
		 unset($iterate['password']);
		 unset($iterate['password2']);
		 unset($iterate['email']);
		 unset($iterate['tos']);
		 unset($iterate['name']);
		 
		 
		 
		 
	 }
	 
	 foreach ($iterate as $f) {
		 if (isset($data[$f])) {
		   
		   $value = $data[$f]; 
		   
		   //$item['value'] = $data[$f]; 
		   $_userData[$f] = $data[$f]; 
		   
		 }
	  
	  }
	 
	 
	 
	 $ret = $userFieldsModel->getUserFieldsFilled($_selection,  $_userData, 'third_'); 
	
	 
	 
	 return $ret; 
	 
	
	 
	 
	 
 }
 
 
}