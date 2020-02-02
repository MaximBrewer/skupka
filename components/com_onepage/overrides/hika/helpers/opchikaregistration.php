<?php
defined('_JEXEC') or die('Restricted access');

class OPChikaregistration {
	public static function setRegType() {
		if (!defined('VM_REGISTRATION_TYPE'))
		{
			define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
		}
			
	}
	
	
	public static function usernameExists($username, &$return=array(), $cmd='checkusername') {
		if ($cmd == 'checkusername')
		{
			$db = JFactory::getDBO(); 
			$user = JFactory::getUser(); 
			$un = $user->get('username'); 
			if ($un == $username)
			{
				// do not complain if entering the same username of already registered
				$return['username_exists'] = false; 
			}
			else
			if (!empty($username))
			{
				$q = "select `username` from #__users where `username` = '".$db->escape($username)."' limit 0,1"; 
				$db->setQuery($q); 
				$r = $db->loadResult(); 
				if (!empty($r))
				{
					$return['username_exists'] = true; 
				}
				else {
				  $return['username_exists'] = false; 
				}
				
			}
		}
	}
	
	public static function emailExists($email='', &$return=array(), $cmd='checkemail') {
		$return['email_exists'] = false; 
		
		$opc_no_duplicit_email = OPChikaconfig::get('opc_no_duplicit_email', true); 
		$op_usernameisemail = OPChikaconfig::get('op_usernameisemail', true); 
		$opc_no_duplicit_username = OPChikaconfig::get('opc_no_duplicit_username', true); 
		
		if (($cmd === 'checkemail') 
				|| ((!empty($email)) 
					&& ((!empty($opc_no_duplicit_email))) 
					|| (!empty($opc_no_duplicit_username) && ((!empty($op_usernameisemail))))))
		{
			
			
			
			$return['email_to_check'] = $email; 
			
			$return['email'] = $email;
			$user = JFactory::getUser(); 
			$ue = $user->get('email'); 
			$user_id = $user->get('id'); 
			
			
			if (!empty($user_id) && ($email === $ue))
			{
				// do not complain if user is logged in and enters the same email address
				$return['email_exists'] = false; 
				$return['user_equals_login'] = true; 
			}
			else
			if (!empty($email))
			{
				$db = JFactory::getDBO(); 
				$q = "select email from #__users where username = '".$db->escape($email)."' or email = '".$db->escape($email)."' limit 0,1"; 
				$db->setQuery($q); 
				$r = $db->loadResult(); 
				
				
				$return['q'] = $q; 
				if (!empty($r))
				{
					$return['email_exists'] = true; 
				}
				
				
			}
			
			$return['email_was_checked'] = true; 
		}
		
	}
	
	public static function getRegistrationFieldsHTML() {
		
		$ref = OPChikaRef::getInstance(); 
		$rowFields = self::getRegistrationFields(); 
		$unlg = OPChikauser::logged(); 
		
		$vars = array(
		'rowFields' => $rowFields, 
		'rowFields_st' => $rowFields, 
		'cart' => $ref->cart, 
		'opc_logged' => $unlg,
		);
		
		$html = OPChikarenderer::fetch('list_user_fields.tpl', $vars); 
		
		return $html;  
	}
	
	public static function getRegistrationFields() {
		$fields = array(); 
		$fields['fields'] = array(); 
		
		$field = array(); 
		$field['required'] = true; 
		$field['title'] = JText::_('HIKA_USER_NAME'); 
		$field['placeholder'] = $field['title'];
		$field['value'] = JFactory::getUser()->get('name', '');
		$field['type'] = 'text'; 
		$field['formcode'] = '<input name="name" id="name_field" type="text" value="'.htmlentities($field['value']).'" autocomplete="name" />'; 
		$field['name'] = 'name'; 
		$field['ready_only'] = false; 
		$field['published'] = true; 
		$field['field_realname'] = $field['title'];
		$field['field_namekey'] = $field['name'];
		$fields['fields']['name'] = $field; 
				
		
		$field = array(); 
		$field['required'] = true; 
		$field['title'] = JText::_('HIKA_USERNAME'); 
		$field['placeholder'] = $field['title'];
		$field['value'] = JFactory::getUser()->get('username', '');
		$field['type'] = 'text'; 
		$field['formcode'] = '<input name="username" id="username_field" type="username" value="'.htmlentities($field['value']).'" autocomplete="username" />'; 
		$field['name'] = 'username'; 
		$field['ready_only'] = false; 
		$field['published'] = true; 
		$field['field_realname'] = $field['title'];
		$field['field_namekey'] = $field['name'];
		$fields['fields']['username'] = $field; 
		
		$field = array(); 
		$field['required'] = true; 
		$field['title'] = JText::_('HIKA_EMAIL'); 
		$field['placeholder'] = $field['title'];
		$field['value'] = JFactory::getUser()->get('email', '');
		$field['type'] = 'text'; 
		$field['formcode'] = '<input name="email" id="email_field" type="email" value="'.htmlentities($field['value']).'" autocomplete="email" />'; 
		$field['name'] = 'email'; 
		$field['ready_only'] = false; 
		$field['published'] = true; 
		$field['field_realname'] = $field['title'];
		$field['field_namekey'] = $field['name'];
		$fields['fields']['email'] = $field; 

		$field = array(); 
		$field['required'] = true; 
		$field['title'] = JText::_('HIKA_EMAIL_CONFIRM'); 
		$field['placeholder'] = $field['title'];
		$field['value'] = JFactory::getUser()->get('email', '');
		$field['type'] = 'text'; 
		$field['formcode'] = '<input name="email2" id="email2_field"  type="email" value="'.htmlentities($field['value']).'" autocomplete="email" />'; 
		$field['name'] = 'email2'; 
		$field['ready_only'] = false; 
		$field['published'] = true; 
		$field['field_realname'] = $field['title'];
		$field['field_namekey'] = $field['name'];
		$fields['fields']['email2'] = $field; 
		
		
		$field = array(); 
		$field['required'] = true; 
		$field['title'] = JText::_('HIKA_PASSWORD'); 
		$field['placeholder'] = $field['title'];
		$field['value'] = '';
		$field['type'] = 'text'; 
		$field['formcode'] = '<input name="password" id="password_field"  type="password" value="" autocomplete="password" />'; 
		$field['name'] = 'password'; 
		$field['ready_only'] = false; 
		$field['published'] = true; 
		$field['field_realname'] = $field['title'];
		$field['field_namekey'] = $field['name'];
		$fields['fields']['password'] = $field; 
		
		
		$field = array(); 
		$field['required'] = true; 
		$field['title'] = JText::_('HIKA_VERIFY_PASSWORD'); 
		$field['placeholder'] = $field['title'];
		$field['value'] = '';
		$field['type'] = 'text'; 
		$field['formcode'] = '<input name="password2" id="password2_field"  type="password" value="" autocomplete="password" />'; 
		$field['name'] = 'password2'; 
		$field['ready_only'] = false; 
		$field['published'] = true; 
		$field['field_realname'] = $field['title'];
		$field['field_namekey'] = $field['name'];
		$fields['fields']['password2'] = $field; 

		return $fields; 
		
		
	}
}