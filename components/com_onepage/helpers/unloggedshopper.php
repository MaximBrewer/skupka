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

class OPCUnloggedShopper {

 public static function getRegistrationHhtml(&$obj, &$OPCloader, $force=false)
 {
       // if (!empty($no_login_in_template)) return "";
    include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
    if (!class_exists('VirtueMartCart'))
	 require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
	 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
	 
	if (!empty($obj->cart))
	$cart =& $obj->cart; 
	else
	$cart = VirtueMartCart::getCart();
  
    $type = 'BT'; 
   
   $user_id = JFactory::getUser()->get('id'); 
   if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
	
   $userModel = new VirtuemartModelUser();
   if (method_exists($userModel, 'setId')) {
	   $userModel->setId($user_id);
		}
   // for unlogged
   if (($force) && (!empty($user_id))) {
	  
	   
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
	   $userFields = OPCLoggedShopper::getSetAllBt($cart); 
	   
	   $OPCloader->customizeFieldsPerOPCConfig($userFields); 
	   if (isset($cart->BT['virtuemart_userinfo_id'])) 
		   $virtuemart_userinfo_id = $cart->BT['virtuemart_userinfo_id']; 
			
		
	  
	   
   }
   else {
     $virtuemart_userinfo_id = 0;
   }
  
   $new = 1; 
   $fieldtype = $type . 'address';
   
   /*
   if (method_exists($cart, 'prepareAddressDataInCart'))
   $cart->prepareAddressDataInCart($type, $new);
   */
   
 
   OPCUserFields::populateCart($cart, $type, true); 
   
   /*
   if (method_exists($cart, 'prepareAddressFieldsInCart'))
   $cart->prepareAddressFieldsInCart();
   */
   
   OPCloader::setRegType(); 		

   
   if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php');
   
   $corefields = array( 'name','username', 'email', 'password', 'password2', 'opc_password', 'opc_password2', 'agreed','language', 'tos');
   
   
   if ((empty($force)) || (empty($userFields)))
   $userFields = $cart->$fieldtype;
   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
   
    $layout = 'default';
   


   foreach ($userFields['fields'] as $key=>$uf)   
   {
	 
	   if (!in_array($key, $corefields) || ($key=='agreed'))
	   {
		   unset($userFields['fields'][$key]); 
		   continue; 
	   }
	   		
	 if (!empty($opc_email_in_bt) || ($OPCloader->isNoLogin()) || ($OPCloader->isNoLogin()))
	 {
	   if ($userFields['fields'][$key]['name'] == 'email') 
	   {
	    unset($userFields['fields'][$key]); 
	    continue; 
	   }
	 }
	
	if (empty($user_id)) {
	 $dis = array('username', 'password', 'password2', 'opc_password', 'opc_password2'); 
	if (VM_REGISTRATION_TYPE === 'NO_REGISTRATION') {
		if (in_array($fn, $dis)) {
		  unset($userFields['fields'][$key]); 
		  continue; 
		}
		if ($fn === 'register_account') {
			$userFields['fields'][$key]['formcode'] = '<input type="hidden" value="0" name="register_account" id="register_account" />'; 
		}
	}
	}

	   if ($key == 'email')
	  {
	   
	    $user = JFactory::getUser();
		
		{
		$uid = $user->get('id');
		// user is logged, but does not have a VM account
		if (((!OPCloader::logged($cart)) && (!empty($uid))) && (empty($force)))
		{
		  // the user is logged in only in joomla, but does not have an account with virtuemart
		  $userFields['fields'][$key]['formcode'] = str_replace('/>', ' readonly="readonly" />', $userFields['fields'][$key]['formcode']); 
		}
		}
	  }
	 

	
	
	
	
	 
	 
	 
	
   }
     

   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
   OPCUserFields::getUserFields($userFields, $OPCloader, $cart); 
     // lets move email to the top
		$copy = array(); 
	
		
		
		
   	// we will reorder the fields, so the email is first when used as username
	
		
		$u = OPCLang::_('COM_VIRTUEMART_REGISTER_UNAME'); 
		
		//$e = OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL'); 
		
   
	// disable when used for logged in 
	if (!empty($userFields['fields']))
	{
	 /*
     if (empty($opc_email_in_bt) && (!empty($double_email)))
	  {
	    // email is in BT, let's check for double mail
		
		$email2 = $userFields['fields']['email'];
		$email2['name'] = 'email2'; 
		$title = OPCLang::_('COM_ONEPAGE_EMAIL2'); 
		if ($title != 'COM_ONEPAGE_EMAIL2')
		$email2['title'] = $title;
		$email2['formcode'] = str_replace('"email', '"email2', $email2['formcode']); 
		$email2['formcode'] = str_replace('id=', ' onblur="javascript: doublemail_checkMail();" id=', $email2['formcode']);
		
		$h = '<span style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email2_info" class="email2_class">';
		$emailerr = OPCLang::_('COM_ONEPAGE_EMAIL_DONT_MATCH');
		if ($emailerr != 'COM_ONEPAGE_EMAIL_DONT_MATCH')
		$h .= $emailerr;
		else $h .= "Emails don't match!";
		$h .= '</span>';
		$email2['formcode'] .= $h;
	  }
	  */
	  
	  /*
	 if (!empty($opc_check_username))
	 if ((!OPCloader::logged($cart)) && (empty($uid)))
	 if (!empty($userFields['fields']['username']))
	  {
	   
	     $un = $userFields['fields']['username']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.username_check(this);" id=', $un);
		 $un .=  '<span class="username_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="username_already_exists">';
		 $un .= OPCLang::sprintf('COM_VIRTUEMART_STRING_ERROR_NOT_UNIQUE_NAME', $u); 
		 $un .= '</span>'; 
		 $userFields['fields']['username']['formcode'] = $un; 
	  }
	  */
	  
	  /*
	  if (!empty($opc_check_email))
	  if ((!OPCloader::logged($cart)) && (empty($uid)))
	  if (!empty($userFields['fields']['email']))
	  {

	     $un = $userFields['fields']['email']['formcode']; 
		 $un = str_replace('id=', ' onblur="javascript: Onepage.email_check(this);" id=', $un);
		 $un .=  '<span class="email_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email_already_exists">';
		 $un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL')); 
		 $un .= '</span>'; 
		 $userFields['fields']['email']['formcode'] = $un; 
	  }
	  */
	  
	}
	/*
	$OPCloader->reorderFields($userFields); 
    */
    if (count($userFields['fields'])===0) 
	{
	 // no fields found
	 return '';
	}
   
   
   
   //if (empty($opc_email_in_bt) && (!empty($double_email)))
   //$OPCloader->insertAfter($userFields['fields'], 'email', $email2, 'email2'); 

    $hidden_html = ''; 
	$render_as_hidden = OPCconfig::get('render_as_hidden', array()); 
		if (!empty($userFields['fields']))
		foreach ($userFields['fields'] as $key=>$val)
		{
			OPCloader::$fields_names[$key] = $userFields['fields'][$key]['title']; 
			
			if (in_array($val['name'], $render_as_hidden)) {
			 $val['hidden'] = true; 
			}
			
			if (!empty($val['hidden']))
			{
				$hidden[] = $val; 
				$hidden_html .= $val['formcode']; 
				unset($userFields['fields'][$key]); 
			}
			
		}

   $vars = array(
		'rowFields' => $userFields, 
		'rowFields_reg' => $userFields, 
		'cart'=> $cart,
		'is_registration' => true);
   $html = $OPCloader->fetch($OPCloader, 'list_user_fields_registration.tpl', $vars); 
   
   $hidden_html = str_replace('"required"', '""', $hidden_html); 
   $hidden_html = '<div style="display:none;">'.$hidden_html.'</div>'; 
   $html .= $hidden_html; 
   
   $html = str_replace("'password'", "'opc_password'", $html); 
   $html = str_replace("password2", "opc_password2", $html); 
   
   if (strpos($html, 'email_field')!==false) $html .= '<input type="hidden" name="email_in_registration" value="1" id="email_in_registration" />'; 
   else $html .= '<input type="hidden" name="email_in_registration" value="0" id="email_in_registration" />'; 
   
   
   
   return $html; 
 }
 
 public static function setAddress($cart, $ajax, $force_only_st, $no_post, $post, $userFields, $userFieldsst) {
	 // unlogged users get data from the form BT address
		$stopen = VirtueMartControllerOpc::stOpen(); 
		static $types = array('text', 'select', 'emailaddress', 'password'); 
		$address = array(); 
		//BT addres: 
		foreach ($userFields['fields'] as $key=>$uf33)   
		{
			$keyname = $uf33['name']; 
			if (isset($post[$keyname]))
			{
				$address[$keyname] = $post[$keyname]; 
			}
			else {
				
				if (!in_array($uf33['type'],$types))
				{
					$address[$keyname] = ''; 
				}
			}
			
		}
		//STaddress
        if ($stopen) {
		foreach ($userFieldsst['fields'] as $key=>$uf44)   
		{
			$keyname = $uf44['name']; 
			
			if (substr($keyname, 0, strlen('shipto_'))!=='shipto_') $keyname = 'shipto_'.$keyname; 
			
			if (isset($post[$keyname]))
			{
				$address[$keyname] = $post[$keyname]; 
			}
			else {
				if (!in_array($uf33['type'],$types))
				{
					$address[$keyname] = ''; 
				}
			}
			
		}
		}
		
		self::addressToBt($cart, $address); 
		if ($stopen) {
		  self::addressToSt($cart, $address); 
		  $cart->STsameAsBT = 0; 
		} 
		else {
			$cart->STsameAsBT = 1; 
			$cart->ST = 0; 
		}
			
		return; 
		
	
	}
 
 
	private static function addressToSt(&$cart, $address) {
		
		VirtueMartControllerOpc::addressToSt($cart, $address);
	}
	
	private static function addressToBt(&$cart, $address) {
		VirtueMartControllerOpc::addressToBt($cart, $address);
	}
 
 

}