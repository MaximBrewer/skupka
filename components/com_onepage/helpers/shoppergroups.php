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

class OPCShopperGroups {
	
  public static function checkVmV()
  {
	   if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php' );
		else return false;
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return false;

	 
	 
    return true; 	 
  }
  public static function setShopperGroups($id, $remove=array())
  {
	  
	 //if ($id === -1) return; 
	 
	$app = JFactory::getApplication(); 
	if (!$app->isSite()) return; 
	
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
    
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
    
	//include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
    $user = JFactory::getUser(); 
	
	$allow_sg_update_logged = OPCconfig::get('allow_sg_update_logged', false); 
	
	if (empty($allow_sg_update_logged))
	if (($user->id != 0) && (empty($user->guest))) 
	return $id; 
	
	
   
   if (!self::checkVmV()) return 1; 
  
	OPCloader::opcDebug('OPC: setShopperGroup: '.$id,  'setShoppergroup');  
    
	$arr = array(1, 2); 
	
	if (!empty($id) && ($id>0) && (!in_array($id, $arr)))
	{
	 //remove default and anonymous
	 $remove[] =1; 
	 $remove[] =2; 
	}
	
	
    
	
	if (!empty($id))
	{
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	 $shoppergroupmodel = OPCmini::getModel('ShopperGroup'); //new VirtueMartModelShopperGroup(); 
	 if (method_exists($shoppergroupmodel, 'removeSessionSgrps'))
	 if (method_exists($shoppergroupmodel, 'appendShopperGroups'))
	 {
		
	
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 
	
	 
	 
	 //$shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = $shoppergroup_ids;
	 foreach ($remove as $rid)
	 foreach ($new_shoppergroups as $key=>$val)
	  {
	    if ($rid == $val)
		unset($new_shoppergroups[$key]); 
	  }
	 
	 
	  
	  $session->set('vm_shoppergroups_remove', $remove, 'vm');
	  
	 
	 if ($id > 0)
	 if (!in_array($id, $shoppergroup_ids))
	 {
	   $new_shoppergroups[] = $id;  
	   JRequest::setVar('virtuemart_shoppergroup_id', $id, 'post');
	 }
	 $session = JFactory::getSession(); 
	 $shoppergroup_ids = $session->set('vm_shoppergroups_add',$new_shoppergroups,'vm');
	 $user = JFactory::getUser(); 
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, $user); 
	 
	
	 
	OPCloader::opcDebug('OPC: setShopperGroup changed: '.$id, 'setShoppergroup'); 
	
	
	// it's crazy we have to use this.... 
	if (!class_exists('calculationHelper'))
	{
	  if(!class_exists('calculationHelper')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
	  
	}

	if ((!empty($id)) && ($id > 2))
	if (class_exists('ReflectionClass'))
	{
	   
	    $val = array($id); 
	   $calc = calculationHelper::getInstance();
	   if (function_exists('ReflectionMethod'))
	   {
	   $reflectionMethod = new ReflectionMethod($calc, 'setShopperGroupIds');
	   $reflectionMethod->setAccessible(true);
	   $reflectionMethod->invokeArgs($calc, array($val)); 
	   }
	  
	   
	}
	
	
	
	 
	
	
	  if ($id > 0)
	 return $id; 
	}
	}
	static $default_id; 
	if (!empty($default_id)) return $default_id; 
	// else 
	// this is a VM default group:
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
	$cart = VirtueMartCart::getCart();
	
	if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
	 $cart->products = array(); 
	 ob_start(); 
	 $cart->prepareCartData(false); 
	 $zz = ob_get_clean(); 
	 $cart->order_language = JRequest::getString('order_language', $cart->order_language);
	}
	
	if (empty($cart->vendorId))
	$vid = 1; 
	else
	$vid = (int)$cart->vendorId;
	
	$user = JFactory::getDBO(); 
	$sid = $user->get('id'); 
	if (empty($id))
	{
	if (empty($sid) || ($user->guest))
	 {
	   //anonymous: 
	    $db = JFactory::getDBO(); 
		$q = "select `virtuemart_shoppergroup_id` from `#__virtuemart_shoppergroups` where `default` = '2' and `virtuemart_vendor_id` = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	   
	 }
	 else
	 {
		$db = JFactory::getDBO(); 
		$q = "select `virtuemart_shoppergroup_id` from `#__virtuemart_shoppergroups` where `default` = '1' and `virtuemart_vendor_id` = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	 
	 }
	}
	
	return $id;
    
  }
  
  public static function getDefault($cart)
  {
    if (!empty($cart) && (isset($cart->vendorId))) $vid = $cart->vendorId; 
	else $vid = 1; 

   
    $user = JFactory::getDBO(); 
	$sid = $user->get('id'); 
	
	{
	if (empty($sid) || ($user->guest))
	 {
	   //anonymous: 
	    $db = JFactory::getDBO(); 
		$q = "select `virtuemart_shoppergroup_id` from `#__virtuemart_shoppergroups` where `default` = '2' and `virtuemart_vendor_id` = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	   
	 }
	 else
	 {
		$db = JFactory::getDBO(); 
		$q = "select `virtuemart_shoppergroup_id` from `#__virtuemart_shoppergroups` where `default` = '1' and `virtuemart_vendor_id` = ".$vid." limit 1"; 
		$db->setQuery($q); 
		$id = $db->loadResult(); 
		$default_id = $id; 
	 
	 }
	}
	return $default_id; 
  }
  
  public static function getAllDefault($cart)
  {
  
	if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php' );
		else return;
	 }
	if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 
    if (!empty($cart) && (isset($cart->vendorId))) $vid = $cart->vendorId; 
	else $vid = 1; 
	
    $db = JFactory::getDBO(); 
	$q = "select `virtuemart_shoppergroup_id` from `#__virtuemart_shoppergroups` where (`default` = '1' or `default` = '2') and `virtuemart_vendor_id` = ".$vid." limit 1"; 
	$db->setQuery($q); 
	$ids = $db->loadAssocList(); 
	if (empty($ids)) $ids = array(1,2); 
	return $ids; 
  }
  
  public static function removeShopperGroups($arr)
 {
   return; 
   if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php' );
	  else return 1;
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 
   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
   $shoppergroupmodel = OPCmini::getModel('ShopperGroup'); //new VirtueMartModelShopperGroup(); 
   
   
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 /*
	 foreach ($arr as $id)
	  {
	     if (in_array($id, $shoppergroup_ids))
		  {
		     
		  }
	  }
	 */
   
 }
 
 public static function getOPCshopperGroups()
 {
	 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 
	 $session = JFactory::getSession(); 
	 $sga = array(); 
	 if (OPCShopperGroups::notConfigured()) return $sga; 
	 
   
     $option_sgroup = OPCconfig::get('option_sgroup', array()); 
   
     if (!empty($option_sgroup)) 
	 {
   
	  if (!empty($option_sgroup) && ($option_sgroup===1))
	  {
	  $lang = JFactory::getLanguage();
	  $tag = $lang->getTag();
	  
	  $lang_shopper_group = OPCconfig::get('lang_shopper_group', array()); 
	  
	  if (!empty($tag))
	  if (!empty($lang_shopper_group))
	  if (!empty($lang_shopper_group[$tag]))
	   {
	    // end of lang shopper group
		
		$sga[$lang_shopper_group[$tag]] = $lang_shopper_group[$tag]; 
		
	
		
	   }
	 }
	 else
	 if ($option_sgroup == 2)
	 {
	 
	  // geo ip based shopper group: 
	  $ip_vm_country = $session->get('opc_ip_country', 0); 
	  $ip_sg = $session->get('opc_ip_sg');
	  if (!empty($ip_sg))
	    {
		  // ip sg was already set
		  //return OPCloader::setShopperGroup($lang_shopper_group_ip[$ip_vm_country]); 
		}
		
		
	  if (empty($ip_vm_country))
	  {
	  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_geolocator".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."helper.php"))
	 {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_geolocator".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		
		 if (!empty($country_2_code))
		 {
		 $country_2_code = strtolower($country_2_code); 
		 $db=JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = '".$country_2_code."' limit 1 "); 
		 $r = $db->loadResult(); 
		 
		 if (!empty($r)) 
		 $ip_vm_country = $r; 
		 
		 
		 }
	     
	   }
	  }
	  }
	  
	   $lang_shopper_group_ip = OPCconfig::get('lang_shopper_group_ip', array()); 
	  
	   if (!empty($lang_shopper_group_ip[$ip_vm_country]))
	   {
	   $id = OPCShopperGroups::setShopperGroups($lang_shopper_group_ip[$ip_vm_country]); 
	   $sga[$id] = $id; 
	   
	   
	    $session->set('opc_ip_country', $ip_vm_country); 
	    $session->set('opc_ip_sg', $id); 
		
	   }
	 }
	 
	

	 }
	 
	 
	 /* business/visitor/euvat */
	 $business_shopper_group = OPCconfig::get('business_shopper_group', null); 
	 $visitor_shopper_group = OPCconfig::get('visitor_shopper_group', null); 
	 $is_business = JRequest::getVar('opc_is_business', -1); 
	   
		 
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		 if ($is_business == -1) {
			 if (empty($cart)) {
				 if (!class_exists('VirtueMartCart'))
				 require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			     $cart = VirtuemartCart::getCart(); 
			 }
			 $is_business = OPCUserFields::isBusiness($cart); 
			 
			 }

			 
     if (!empty($business_shopper_group) || (!empty($visitor_shopper_group)))
     {	 
     
	
	
	 
	
	 
	     if ($is_business === -1) 
		 {
		    //continue...
		 }
		 else
		 {
		 if (!empty($is_business))
		  {
		 // ID 2 is for the default group in core VM
		 $business_shopper_group = (int)$business_shopper_group; 
		   if ((!empty($business_shopper_group)) && ($business_shopper_group > 2))
		   {
		$sga[$business_shopper_group] = $business_shopper_group; 
	       }
	
	
	
	    }
		else
		{
			$visitor_shopper_group = (int)$visitor_shopper_group; 
			if ((!empty($visitor_shopper_group)) && ($visitor_shopper_group > 1))
			{
				$sga[$visitor_shopper_group] = $visitor_shopper_group; 
	 
		    }
	     }
		 }
	  
	 }
		$euvat_shopper_group = OPCconfig::get('euvat_shopper_group', 0); 
		
		// EU VAT shopper group: 
		if (!empty($euvat_shopper_group))
		{
	     $home_vat_countries = OPCconfig::get('home_vat_countries', ''); 
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'); 
		 $is_valid = OPCvat::detectVatCache(); 
		 
		  
		
		
		
		
		 
		 
		  if (!class_exists('VirtuemartCart'))
			   {
				   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
			   }
			   $cart = VirtuemartCart::getCart(); 
		   
		   $country = JRequest::getVar('virtuemart_country_id', ''); 
		   if (empty($country))
		   if (!empty($cart))
		   {
		    if (method_exists($cart, 'getST'))
		   {
			  $address = $cart->getST(); 
		   }
		   else
		   {
		    $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		   }
		   if (!empty($address))
		   $country = $address['virtuemart_country_id']; 
		   }
		   
		   
		   if (!class_exists('ShopFunctions'))
		   require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
		   
		   if (!empty($country))
		   {
		    $country_2_code = shopFunctions::getCountryByID($country, 'country_2_code'); 
		   }
		  
		  
		   
		   $home = explode(',', $home_vat_countries); 
		   $list = array(); 
		   if (is_array($home))
		   {
		     foreach ($home as $k=>$v)
			  {
			    $list[] = strtoupper(trim($v)); 
			  }
			 
		   }
		  
		   
		   if ((!in_array($country_2_code, $list)) || (empty($list)))
		    {
			
			 if ($is_valid)
			   {
				$sga[$euvat_shopper_group] = $euvat_shopper_group; 
				
			   }
			   else
			   {
				   // maybe the validation haven't been done yet: 
				   // please call the eu vat validation before calling this fucntion !
			   }

			}
			
		 
		}
		
		
		
		
		 
		

	 /*  business/visitor/euvat */
	 
	 

	 
     $sga = array_unique($sga); 
	 foreach ($sga as $k=>$v)
	 {
		 $v = (int)$v;
         if ($v <= 1) unset($sga[$k]); 		 
		 
	 }

  
	  return $sga;

 }
 
 public static function forceShopperGroups($sga)
 {
	 if (!self::checkVmV()) return false;
	 $session = JFactory::getSession(); 
	 $session->set('vm_shoppergroups_add', $sga,'vm');
	 
	 
	if (!class_exists('calculationHelper'))
	{
	  if(!class_exists('calculationHelper')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
	  
	}

	
	if (class_exists('ReflectionClass'))
	{
	   
	
	   $calc = calculationHelper::getInstance();
	   if (function_exists('ReflectionMethod'))
	   {
	   $reflectionMethod = new ReflectionMethod($calc, 'setShopperGroupIds');
	   $reflectionMethod->setAccessible(true);
	   $reflectionMethod->invokeArgs($calc, $sga); 
	   }
	  
	   
	}

	 
 }
 
 public static function getSetShopperGroup($debug=false) 
 {
	 
	 	$app = JFactory::getApplication(); 
	    if (!$app->isSite()) return; 
	 
         $user = JFactory::getUser(); 
	    
	   $uid = (int)$user->get('id'); 
	   
	  if ($uid > 0) return; 
	 
	$session = JFactory::getSession();
    
	
   
   //include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
   
   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
   
   $option_sgroup = OPCconfig::get('option_sgroup', array()); 
   
   if (empty($option_sgroup)) return;
   // language shopper group
   //$lang_shopper_group['en-GB'] = '4'; 
	  if (!empty($option_sgroup) && ($option_sgroup===1))
	  {
	  $lang = JFactory::getLanguage();
	  $tag = $lang->getTag();
	  
	  $lang_shopper_group = OPCconfig::get('lang_shopper_group', array()); 
	  
	  if (!empty($tag))
	  if (!empty($lang_shopper_group))
	  if (!empty($lang_shopper_group[$tag]))
	   {
	    // end of lang shopper group
		
		return OPCShopperGroups::setShopperGroups($lang_shopper_group[$tag]); 
		
	
		
	   }
	 }
	 else
	 if ($option_sgroup == 2)
	 {
	 
	  // geo ip based shopper group: 
	  $ip_vm_country = $session->get('opc_ip_country', 0); 
	  $ip_sg = $session->get('opc_ip_sg');
	  if (!empty($ip_sg))
	    {
		  // ip sg was already set
		  //return OPCloader::setShopperGroup($lang_shopper_group_ip[$ip_vm_country]); 
		}
		
		
	  if (empty($ip_vm_country))
	  {
	  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_geolocator".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."helper.php"))
	 {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_geolocator".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		
		 if (!empty($country_2_code))
		 {
		 $country_2_code = strtolower($country_2_code); 
		 $db=JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = '".$country_2_code."' limit 1 "); 
		 $r = $db->loadResult(); 
		 //$e = $db->getErrorMsg(); echo $e;
		 if (!empty($r)) 
		 $ip_vm_country = $r; 
		 
		 
		 }
	     
	   }
	  }
	  }
	  
	  $lang_shopper_group_ip = OPCconfig::get('lang_shopper_group_ip', array()); 
	  
	   if (!empty($lang_shopper_group_ip[$ip_vm_country]))
	   {
	   $id = OPCShopperGroups::setShopperGroups($lang_shopper_group_ip[$ip_vm_country]); 
	   
	   
	   
	    $session->set('opc_ip_country', $ip_vm_country); 
	    $session->set('opc_ip_sg', $id); 
		return $id; 
	   }
	 
	

	 }
	 

	 
	  // we should set default here
	  $a = $session->get('vm_shoppergroups_add', null, 'vm'); 
	  
	  if (!empty($a))
	  $session->set('vm_shoppergroups_add', array(),'vm');
  

  
	  return;
	 
 }
    public static function updateSG(&$data, $user_id, $ids)
	{
		
		if (empty($ids)) return; 
		if (!is_array($ids))
		{
			$ids = array($ids); 
		}
		foreach ($ids as $k=>$v)
		{
			$v = (int)$v; 
			if (empty($v))
			{
				unset($ids[$k]); 
			}
		}
		
		if (empty($user_id)) return; 
		
		$newId = $user_id; 
					$ids = array_unique($ids); 
			//stAn, opc 250: $data['virtuemart_shoppergroup_id'] = $sg; 
			$data['virtuemart_shoppergroup_id'] = $ids; 
			// Bind the form fields to the table
			$db = JFactory::getDBO(); 
			if (!empty($ids))
			foreach ($ids as $ssg)
			{
			
			$q = 'select * from #__virtuemart_vmuser_shoppergroups where virtuemart_user_id = '.(int)$newId.' and virtuemart_shoppergroup_id = '.(int)$ssg.' limit 0,1'; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			if (empty($res))
			{
			$q = "insert into `#__virtuemart_vmuser_shoppergroups` (id, virtuemart_user_id, virtuemart_shoppergroup_id) values (NULL, ".(int)$newId.", ".(int)$ssg.")"; 
				$db->setQuery($q); 
				$db->query(); 
			}
			}
	}
 	public static function setShopperGroupsController($cart=null)
	{
	
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	
	  // we need to alter shopper group for business when set to: 
	     $is_business = JRequest::getVar('opc_is_business', -1); 
		 
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		 if ($is_business == -1) {
			 if (empty($cart)) {
				 if (!class_exists('VirtueMartCart'))
				 require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			     $cart = VirtuemartCart::getCart(); 
			 }
			 $is_business = OPCUserFields::isBusiness($cart); 
			 
			 }
  
	  $remove = array(); 
     
 
	  OPCShopperGroups::getSetShopperGroup(); 
	
	if (!class_exists('VirtueMartModelShopperGroup'))
	 {
	 if (file_exists(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php'))
			    require( JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php' );
		else return;
	 }
	 if (!method_exists('VirtueMartModelShopperGroup', 'appendShopperGroups')) return 1; 

	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	//include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	
	$business_shopper_group = OPCconfig::get('business_shopper_group', 0); 
	$visitor_shopper_group = OPCconfig::get('visitor_shopper_group', 0); 
	$lang_shopper_group = OPCconfig::get('lang_shopper_group', array()); 
	$option_sgroup = OPCconfig::get('option_sgroup', array()); 
	$lang_shopper_group_ip = OPCconfig::get('lang_shopper_group_ip', array()); 
	$euvat_shopper_group = OPCconfig::get('euvat_shopper_group', 0); 
	
     if (!empty($business_shopper_group) || (!empty($visitor_shopper_group)))
     {	 
     
	
	 if (class_exists('VirtueMartModelShopperGroup')) 
	 {
	 $shoppergroupmodel = new VirtueMartModelShopperGroup(); 
	 if (method_exists($shoppergroupmodel, 'removeSessionSgrps'))
	 if (method_exists($shoppergroupmodel, 'appendShopperGroups'))
	 {
	 
	     if ($is_business === -1) 
		 {
		    //continue...
		 }
		 else
		 if (!empty($is_business))
		  {
		 
		
		 
		    // we will differenciate between default and anonymous shopper group
			// default is used for non-logged users
			// anononymous is used for logged in users as guests
     OPCShopperGroups::setShopperGroups($business_shopper_group); 
	 $remove[] = $visitor_shopper_group; 
	 // function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){
	 // remove previous: 
	 /*
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 $shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = array(); 
	 $new_shoppergroups[] = $business_shopper_group;  
	 $shoppergroup_ids = $session->set('vm_shoppergroups_add',$new_shoppergroups,'vm');
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, null); 
	 
	 JRequest::setVar('virtuemart_shoppergroup_id', $new_shoppergroups, 'post');
	 */
	
	//appendShopperGroups
	
	    }
		else
		{
	 OPCShopperGroups::setShopperGroups($visitor_shopper_group); 
	 $remove[] = $business_shopper_group; 
	 /*
	 $shoppergroupmodel = new VirtueMartModelShopperGroup(); 
	 // function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){
	 // remove previous: 
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 $shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = array(); 
	 $new_shoppergroups[] = $visitor_shopper_group; 
	 $shoppergroup_ids = $session->set('vm_shoppergroups_add',$new_shoppergroups,'vm');
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, null); 
	 JRequest::setVar('virtuemart_shoppergroup_id', $new_shoppergroups, 'post');
		 */
		  }
	  }
	  }
		}
		$euvat_shopper_group = OPCconfig::get('euvat_shopper_group', 0); 
		$home_vat_countries = OPCconfig::get('home_vat_countries', ''); 
		$allow_sg_update_logged = OPCconfig::get('allow_sg_update_logged', false); 
		
		// EU VAT shopper group: 
		if (!empty($euvat_shopper_group))
		{
		$removeu = true; 
		$session = JFactory::getSession(); 
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		$vatids = $session->get($opc_vat_key, array());
	
		if (!is_array($vatids))
		$vatids = json_decode($vatids, true); 
	   
		//BIT vat checker: 
		if (!empty($vatids['field']))
		{
		 
		   $euvat = JRequest::getVar($vatids['field'], ''); 
		   $euvat = preg_replace("/[^a-zA-Z0-9]/", "", $euvat);
		   $euvat = strtoupper($euvat); 
		   if (!empty($euvat)) {
		   if (!isset($cart))
		   {
			   if (!class_exists('VirtuemartCart'))
			   {
				   require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
			   }
			   $cart = VirtuemartCart::getCart(); 
			   
		   }
		   if (!empty($cart))
		   {
		   $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		   if (isset($address['virtuemart_country_id']))
		   $country = $address['virtuemart_country_id']; 
		   }
		   
		   if (empty($country))
		   {
		     $country = JRequest::getVar('virtuemart_country_id'); 
		   }
		   
		   if (!empty($country)) {
		   
		   $vathash = $country.'_'.$euvat; 
		   
		   
		   
		   if (!class_exists('ShopFunctions'))
		   require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
		   
		   $country_2_code = shopFunctions::getCountryByID($country, 'country_2_code'); 
		   
		   $home = explode(',', $home_vat_countries); 
		   $list = array(); 
		   if (is_array($home))
		   {
		     foreach ($home as $k=>$v)
			  {
			    $list[] = strtoupper(trim($v)); 
			  }
			 
		   }
		   /*
		   else
		   $list[] = $v; 
		   */
		   if (!in_array($country_2_code, $list))
		   if (!empty($euvat))
		    {
			  $euvat = strtoupper($euvat); 
			  if (!empty($vatids[$vathash]))
			   {
			     //change OPC VAT shopper group: 
				OPCShopperGroups::setShopperGroups($euvat_shopper_group); 
				$removeu = false; 
				
			   }

			}
		}
		   }
		}
		
		  $user_id = JFactory::getUser()->get('id'); 
		  if ((!empty($user_id) && ($allow_sg_update_logged)) || (empty($user_id)))
		  if ($removeu) {
		   $remove[] = $euvat_shopper_group; 
		  }
		}
		
		 if (!empty($remove)) {
			OPCShopperGroups::setShopperGroups(-1, $remove); 
		 }
		 
		 if (class_exists('calculationHelper'))
		 calculationHelper::$_instance = null; 
	  	 
		  $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');

		
		
	}
	public static function notConfigured() {
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 	
		$business_shopper_group = OPCconfig::get('business_shopper_group', 0); 
		$visitor_shopper_group = OPCconfig::get('visitor_shopper_group', 0); 
		$lang_shopper_group = OPCconfig::get('lang_shopper_group', array()); 
		$option_sgroup = OPCconfig::get('option_sgroup', array()); 
		$lang_shopper_group_ip = OPCconfig::get('lang_shopper_group_ip', array()); 
		$euvat_shopper_group = OPCconfig::get('euvat_shopper_group', 0); 
		
		if (!empty($option_sgroup)) {
		  if ((!empty($lang_shopper_group)) || (!empty($lang_shopper_group_ip))) return false; 
		}; 
		
		if ((!empty($business_shopper_group)) || (!empty($visitor_shopper_group))) return false;  

		if (!empty($euvat_shopper_group)) return false;
		// return true if no config exists: 
		return true; 
	}
	public static function getSetInitShopperGroup()
	{
			$app = JFactory::getApplication(); 
			if (!$app->isSite()) return; 
		
		if (OPCShopperGroups::notConfigured()) return; 
		
		
		$lang_shopper_group_ip = OPCconfig::get('lang_shopper_group_ip', array()); 
		$allow_sg_update_logged = OPCconfig::get('allow_sg_update_logged', false); 
		$option_sgroup = OPCconfig::get('option_sgroup', array()); 
		$lang_shopper_group = OPCconfig::get('lang_shopper_group', array()); 
		$business_shopper_group = OPCconfig::get('business_shopper_group', 0); 
	    $visitor_shopper_group = OPCconfig::get('visitor_shopper_group', 0); 
		
		
		
		
		if (((!empty($option_sgroup)) && ((!empty($lang_shopper_group)) || (!empty($lang_shopper_group_ip)))) || (!empty($business_shopper_group)) || (!empty($visitor_shopper_group)))
		{
		$session = JFactory::getSession(); 
		$session->set('vm_shoppergroups_add', array(),'vm');
		$session->set('vm_shoppergroups_remove', array(), 'vm');
		}
		self::setShopperGroupsController(); 
	}

 

}