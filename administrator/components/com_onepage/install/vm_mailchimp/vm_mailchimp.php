<?php
/**
 * @version		$Id: vm_mailchimp.php$
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemVm_mailchimp extends JPlugin {
	
		public static $mailchimp_title; 
	public static $mailchimp_checked; 
	public static $mailchimp_wasregistered; 
	public static $is_subscribed;
    public static $debug; 
	public static $api_id; 
	public static $list_id; 

     /**
	 * Object Constructor.
	 *
	 * @access	public
	 * @param	object	The object to observe -- event dispatcher.
	 * @param	object	The configuration object for the plugin.
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$subject, $config)
	{
		
		JFactory::getLanguage()->load('plg_system_vm_mailchimp', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vm_mailchimp', 'en-GB'); 
		JFactory::getLanguage()->load('plg_system_vm_mailchimp');
		
		parent::__construct($subject, $config);
		
		// Set the error handler for E_ERROR to be the class handleError method.
		//$this->params->get
		$this->_loggable = false;
		$this->tableFields = array(); 
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		
		$this->_userFieldName = 'mailchimp_checkbox';
		
		
		
		if (empty($this->params))
		{
			jimport( 'joomla.plugin.helper' );
		    $plugin = JPluginHelper::getPlugin('system', 'vm_mailchimp');
			jimport( 'joomla.html.parameter' );
			
			//$this->id = $plugin->id; 
			if (class_exists('JParameter'))
			$this->params = new JParameter( $plugin->params );
			else
			$this->params = new JRegistry( $plugin->params );
			
		}
		
		self::$api_id = $this->params->get('api_id'); 
	    self::$list_id = $this->params->get('list_id'); 
		self::$debug = $this->params->get('debug', false); 
		
	}
	
	// under J3 only: 
	function onUserAfterLogin()
	{
		
	}
	
	function onUserLogin($user, $options)
	{
		
		if (method_exists($user, 'get'))
		{
			$email = $user->get('email'); 
			$is_reg = $this->_isRegistered($email); 
		}
	}
	
	function onUserAfterSave($user, $isNew, $result, $error)
	{
	  return $this->onAfterStoreUser($user, $isNew, $result, $error); 
	}
	
	
	function onContentPrepareForm($form, $data)
	{
		
	    $user_id = JFactory::getUser()->get('id'); 
		//if (!empty($user_id)) return true; 
		
	
		if (!($form instanceof JForm)) {
		
			return true;
		}
		
		$all = $this->params->get('subscribe_all', false); 
		if ($all) return true; 
	    $own_theme = $this->params->get('self_handle_checboxes', false); 
		
		
		if (empty($own_theme)) return true; 
		
		
		
		// Check we are manipulating a valid form.
		if (!in_array($form->getName(), array('com_users.profile', 'com_users.registration','com_users.user','com_admin.profile'))) {
			return true;
		}
		
	  
	   $title = $this->params->get('checkbox_text'); 
	   $title = JText::_($title);
	   plgSystemVm_mailchimp::$mailchimp_title = $title; 
	 if (!empty($user_id))
	 {
		 $email = JFactory::getUser()->get('email'); 
	 if (!empty($email))
	  {
	    $is_reg = $this->_isRegistered($email); 
		
	  }
	 
	  if ($is_reg === true)
	  {
	    plgSystemVm_mailchimp::$mailchimp_wasregistered = '<input type="hidden" name="was_mregistered" value="1" />'; ; 
	  }
	  else
	  {		  
		  plgSystemVm_mailchimp::$mailchimp_wasregistered = ''; 
	  }
	  
	  
	  if (isset($is_reg) && ($is_reg === false)) $checked = ''; 
	  else $checked = ' checked="checked" '; 
	  
	  plgSystemVm_mailchimp::$mailchimp_checked = $checked; 
	 }
	else
	{
		
	}	
	   
	
	   jimport('joomla.form.formfield');
	   jimport('joomla.form.helper');
	   JFormHelper::loadFieldClass('checkbox');
	   JForm::addFieldPath(dirname(__FILE__). '/form/elements');
	   JForm::addFormPath(dirname(__FILE__).'/form');
	   
	   
	   $form->loadFile('mailchimp', false);
	  
	   
	}
	function plgVmConfirmedOrder($cart, $order)
	{
		$this->plgOpcOrderCreated($cart, $order); 
	}
	function plgOpcOrderCreated($cart, $order)
	{
		if (defined('UNDOREGMAILCHIMP')) return; 
		define('UNDOREGMAILCHIMP', 1); 
		
		if (!empty($cart->BT))
		if (!empty($cart->BT['email']))
		{
			$email = $cart->BT['email']; 
		}
		
		if (empty($email))
		{
			$email = JFactory::getUser()->get('email'); 
		}
		
		if (empty($email)) return; 
		
		$test = JRequest::getVar('was_mregistered'); 
					if (!empty($test))
					{
						$chk = $this->_checkCheckBox(); 
						if (!$chk)
						{
						 $this->_undoRegistration($email); 
						}
					}
	}
	
	function plgMailchimpCheckRegistered($email, &$return, &$appendhtml)
	{
		
		if (empty($append)) $appendhtml = ''; 
		$is_reg = $this->_isRegistered($email); 
		if ($is_reg === true) {
			$appendhtml .= plgSystemVm_mailchimp::$mailchimp_wasregistered = '<input type="hidden" name="was_mregistered" value="1" />';  
			$return = true; 
			
			
			return true; 
		
		}
		
		 
		$return = false; 
		return false; 
	}
	        
   public function plgVmOnUserfieldDisplay($_prefix, $field,$virtuemart_user_id,  &$_return)
   {
		

      	if ('pluginMailchimp' != $field->type) {
			return;
		}
		
		$user_id = JFactory::getUser()->get('id'); 
			$readonly = ''; 
		
		
       $title = $this->params->get('checkbox_text'); 
	   $title = JText::_($title);

	  $email = JFactory::getUser()->get('email', ''); 
	  if (!empty($email))
	  {
	    $is_reg = $this->_isRegistered($email); 
		
		
	  }
	  else
	  {
	     if (isset($_return['fields']['email']['value']))
		  {
		    $email = $_return['fields']['email']['value']; 
			$is_reg = $this->_isRegistered($email); 
		  }
	  }
	  
	 
	  
	  if (isset($is_reg) && ($is_reg === false)) $checked = ''; 
	  else $checked = ' checked="checked" '; 
	  
	  $add_e = ''; 
	  if (!empty($is_reg))
	  if ($is_reg === true)
	  {
		  $add_e = '<input type="hidden" name="was_mregistered" value="1" />'; 
	  }
	  
	  $label = '<label for="mailchimp_register"  style="max-width: 90%;">'.$title.'</label>'; 
	  
	  if (defined('VM_VERSION') and (VM_VERSION >= 3)) $label = ''; 
	  
      $_return['fields'][$field->name]['formcode'] =  $add_e.'<input type="checkbox" value="1" name="mailchimp_register" '.$checked.' '.$readonly.' id="mailchimp_register" style="max-width: 5%;"/>'.$label; 
      

	  
	  
   }
   
   function plgVmPrepareUserfieldDataSave($fieldType, $fieldName, &$data, &$value, $params) 
   {

	   $first_name = $data['first_name']; 
	   $last_name = $data['last_name']; 
	   if (isset($data['middle_name'])) {
	     $middle_name = $data['middle_name']; 
	   }
	   else {
		   $middle_name = ''; 
	   }
	   $email = $data['email']; 
	   
	   $m = $this->_checkCheckBox(); 
	   if (!empty($data['mailchimp']) || $m || (!empty($data['mailchimp_checkbox'])))
	   {
	      $this->_doRegistration($first_name, $last_name, $middle_name, $email);
	   }
   }
	
	
	
	
	function onAfterStoreUser($user, $isnew, $success, $msg){

		if(is_object($user)) $user = get_object_vars($user);

		if($success===false OR empty($user['email'])) return true;
		
		$chk = $this->_checkCheckBox(); 
		
		
				if ($chk)
				{
				   $email = $user['email']; 
				   $name = $user['name']; 
				   $name = trim($name); 
				   if (stripos($name, ' ')!==false)
				    {
					  $a = explode(' ', $name); 
					  $first_name = $a[0]; 
					  $middle_name = $a[1]; 
					  $last_name = $a[count($a)-1]; 
					  if ($last_name == $middle_name) $middle_name = ''; 
					}
					else
					{
					  $first_name = $name; 
					  $last_name = ''; 
					}
				   if (empty($first_name))
				   {
				      $first_name = JRequest::getVar('first_name', ''); 
				   }
				   if (empty($last_name))
				   {
				     $last_name = JRequest::getVar('last_name', ''); 
				   }
				   
				   if (empty($middle_name)) {
					   $middle_name = JRequest::getVar('middle_name', ''); 
				   }
				   
				  $this->_doRegistration($first_name, $last_name, $middle_name, $email); 
				}
				else
				{
					if (!empty($user['email']))
					{
						$email =  $user['email']; ; 
					$test = JRequest::getVar('was_mregistered'); 
					if (!empty($test))
					{
						$this->_undoRegistration($email); 
					}
					
					}
				}
		
		
	}
	
	public function onExtensionAfterSave ($a, $test)
	{
	  if (empty($test)) return; 
	  if (!is_object($test)) return; 
	  
	  if (!(($test->element === 'vm_mailchimp')))
	  return; 
	  if ($test->folder !== 'system') return; 
	
	  $subscribe_all = false; 
	  if (!empty($_POST['jform']))
	  if (!empty($_POST['jform']['params']['subscribe_all']))
	  {
	        $subscribe_all = true; 
			
	  }
	  //self_handle_checboxes
	  $self_handle_checboxes = false; 
	  if (!empty($_POST['jform']))
	  if (!empty($_POST['jform']['params']['self_handle_checboxes']))
	  {
	        $self_handle_checboxes = true; 
			
	  }
	  
	 
	  if ($this->_tableExists('virtuemart_userfields'))
	  {
		  
		  
	     if (($subscribe_all) || (empty($self_handle_checboxes)))
		 {
			 
			 
			 
		    $db = JFactory::getDBO(); 
			$q = 'update #__virtuemart_userfields set published = "0" where name="mailchimp_checkbox" and type="pluginMailchimp" '; 
			try
			{
				
			$db->setQuery($q); 
			$db->query(); 
			}
			catch (Exception $e)
			{
				return; 
			}
			
			return; 
		 }
	      
	     $db = JFactory::getDBO(); 
		 $q = "select `virtuemart_userfield_id` from `#__virtuemart_userfields` where name LIKE 'mailchimp_checkbox' and (type LIKE 'pluginMailchimp' OR type LIKE 'plugin') limit 1"; 
		 $db->setQuery($q); 
		 $r = $db->loadResult(); 
		 $e = $db->getErrorMsg(); echo $e; 
		 
		 
		
		 if (empty($r))
		 {
		    
			$q = 'select * from #__virtuemart_userfields where 1 order by ordering desc limit 0,1'; 
		    $db->setQuery($q); 
		    $r = $db->loadAssoc(); 
			
			$ins = array(); 
			$vm = $this->_getVmData($r); 
			if (!empty($r))
			{
			   foreach ($r as $k=>$v)
			    {
				   if (isset($vm[$k]))
				   $ins[$k] = $vm[$k]; 
				}
			}
			
			
		    $q = 'insert into #__virtuemart_userfields '; 
			$q .= '('; 
			$cols = ''; 
			$vals = ''; 
			$i = 0; 
			
			
			
			foreach ($ins as $k=>$v)
			{
			 $v = $vm[$k]; 
			 
			 $i++; 
			 $cols .= $db->quoteName($k); 
			 $vals .= $db->quote($v); 
			 if ($i != count($ins)) 
			 {
			  $cols .= ', '; 
			  $vals .= ', '; 
			 }
			 
			
			}
			
			 $q .= $cols.') values ('.$vals.')'; 
			
			try
			{
			 $db->setQuery($q); 
			 $db->query(); 
			}
			catch(Exception $e)
			{
			 $e = $db->getErrorMsg(); 
			 
			
			
			}
			
		 }
		 
	  }
	
	}
	
	private function _getVmData($r)
	{
	   $arr = array(); 
	   $arr['virtuemart_userfield_id'] = 'NULL'; 
	   $arr['virtuemart_vendor_id'] = 1; 
	   
	   $arr['userfield_jplugin_id'] = 0;
	   $arr['name'] = 'mailchimp_checkbox'; 
	   $arr['title'] = $this->params->get('checkbox_text', 'Subscribe to our Newsletter'); 
	   $arr['description'] = ''; 
	   $arr['type'] = 'pluginMailchimp'; 
	   $arr['maxlength'] = 1; 
	   $arr['size'] = 1; 
	   $arr['required'] = 0; 
	   $arr['cols'] = 'NULL'; 
	   $arr['rows'] = 'NULL'; 
	   $arr['value'] = 1; 
	   $arr['sys'] = 0; 
	   $arr['default'] = 1; 
	   $arr['registration'] = 1; 
	   $arr['shipment'] = 0; 
	   $arr['account'] = 1; 
	   $arr['readonly'] = 0; 
	   $arr['calculated'] = 0; 
	   $arr['params'] = 'NULL'; 
	   $o = (int)$r['ordering']; 
	   $o += 10; 
	   $arr['ordering'] = $o; 
	   $arr['shared'] = 0;
	   $arr['published'] = 1;
	   $arr['created_on'] = '0000-00-00 00:00:00';
	   $arr['created_by'] = JFactory::getUser()->get('id');
	   $arr['modified_on'] = '0000-00-00 00:00:00';
	   $arr['modified_by'] = 0;
	   $arr['locked_on'] = '0000-00-00 00:00:00';
	   $arr['locked_by'] = 0;
	   
	   return $arr; 
	   
	   
	}
	
  private function _tableExists($table)
  {
   
   
   $db = JFactory::getDBO();
   $prefix = $db->getPrefix();
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
   $table = $db->getPrefix().$table; 
   
 
   
    $q = "SHOW TABLES LIKE '".$table."'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	  
	   
	   if (!empty($r)) 
	    {
		
		return true;
		}
		
   return false;
  }
	
	
	private function _getcheckboxnames()
	{
	   static $ret; 
	   if (!empty($ret)) return $ret; 
	   
	   if(!isset($this->params)){
		    jimport( 'joomla.html.parameter' );
			$plugin = JPluginHelper::getPlugin('system', 'vm_mailchimp');
			if (class_exists('JParameter'))
			$this->params = new JParameter( $plugin->params );
			else
			$this->params = new JRegistry($plugin->params); 
		}
		
		$checkboxes = $this->params->get('checkbox_names'); 
		$a = explode(',', $checkboxes); 
		$an = array(); 
		foreach ($a as $c) 
		{
		  $an[] = trim($c); 
		}
		
		$ret = $an; 
		
		return $an; 
	}
	
	
	public function _isRegistered($email)
	{
		
		
		
	   
	   $api_id = self::$api_id; 
	   if (empty($api_id)) return; 
	   
	   
	   
	   
	   $list_id = self::$list_id; 
	   if (empty($list_id)) return; 
	   
	   $session = JFactory::getSession(); 
	   
	   $is = $session->get('vm_mailchimp_registered', null); 
	  
	   if ($is === false) return false; 
	   
	   if (is_null($is) || ((($is !== false) && ($is != $email))))
	    {
			
			
			
		   if (!class_exists('MailChimpDrewm'))
		   {
		     require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'MailChimp.php'); 
		   }
		   
		    $MailChimp = new MailChimpDrewm($api_id, $this);
			$result = $MailChimp->call('lists/member-info', array(	
			'id'                => $list_id,
			'emails' => array(
                          0 => array(
                              'email' => $email,
                          ),
                        ),
			
			)); 
			
			if (!empty($result) && (is_array($result)) && (!empty($result['data'])))
			{
			 $u = $result['data'][0]['status'];
			 if ($u == 'unsubscribed') 
			 {
				 
				 if (self::$debug) JFactory::getApplication()->enqueueMessage(__LINE__.'vm_mailchimp debug: Email address is not subscribed '.$email); 
					 
				 $session->set('vm_mailchimp_registered', false); 
				 return false; 
			 }
			}
			
			if (!empty($result))
			if (is_array($result))
			if (!empty($result['success_count'])) 
			{
				$session->set('vm_mailchimp_registered', $email); 
				return true; 
			}
			if (self::$debug) JFactory::getApplication()->enqueueMessage(__LINE__.'vm_mailchimp debug: Email address is not subscribed '.$email); 
			$session->set('vm_mailchimp_registered', false); 
			return false; 
		}
		else 
		{
		 if ($is === false) return false; 
		 
		 return true; 
		}
		
		if (self::$debug) JFactory::getApplication()->enqueueMessage(__LINE__.'vm_mailchimp debug: Email address is not subscribed '.$email); 
		$session->set('vm_mailchimp_registered', false); 
		return false; 
	   
	   
	}
	private function _undoRegistration($email)
	{
	   $session = JFactory::getSession(); 
	   
	   
	    $api_id = $this->params->get('api_id'); 
	   if (empty($api_id)) return; 
	   
	    $list_id = $this->params->get('list_id'); 
	   if (empty($list_id)) return; 
	   
	    if (!class_exists('MailChimpDrewm'))
		    {
		     require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'MailChimp.php'); 
		    }
			
		   if (!class_exists('MailChimpDrewm'))
		   {
		     require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'MailChimp.php'); 
		   }
		   
		    $MailChimp = new MailChimpDrewm($api_id, $this);
			$result = $MailChimp->call('/lists/unsubscribe', array(	
			'id'                => $list_id,
			'email' => array( 'email' => $email, ),
			
			)); 
			
			if ((!empty($result)) && ($result['complete']==true))
			{
				if (self::$debug) JFactory::getApplication()->enqueueMessage(__LINE__.'vm_mailchimp debug: Email address is not subscribed '.$email); 
				$session->set('vm_mailchimp_registered', false); 
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_VM_MAILCHIMP_YOU_UNSUBSCRIBED'), 'notice'); 
			}
			
			
			
			
	}
	// is loaded only once per request and once per session of the email
	private function _doRegistration($firstname, $lastname, $middle_name, $email)
	{
	   
	   $session = JFactory::getSession(); 
	   static $is2;
	   if (!empty($is2)) return; 
	   
	   
	   $is = $session->get('vm_mailchimp_registered', false); 
	   
	   $api_id = $this->params->get('api_id'); 
	   if (empty($api_id)) return; 
	   
	   $list_id = $this->params->get('list_id'); 
	   if (empty($list_id)) return; 
	   
	   $send_welcome = $this->params->get('send_welcome', false); 
	   if (empty($send_welcome)) $send_welcome = false; 
	   else $send_welcome = true; 
	   
	   if (empty($is) || ($is != $email))
	    {
		    if (!class_exists('MailChimpDrewm'))
		    {
		     require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'MailChimp.php'); 
		    }
			
		    $MailChimp = new MailChimpDrewm($api_id, $this);
			
			$result = $MailChimp->call('lists/subscribe', array(
                'id'                => $list_id,
                'email'             => array('email'=>$email),
                'merge_vars'        => array('FNAME'=>$firstname, 'LNAME'=>$lastname, 'MNAME'=>$middle_name),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => $send_welcome,
            ));
		  
		
		  
		  $session->set('vm_mailchimp_registered', $email);
		  
		  
		  
		  $rs = "<br />\n".var_export($result, true); 
		  
		  
		  $d = $this->params->get('debug', false); 
		  if ($d)
		  {
		   JFactory::getApplication()->enqueueMessage('Your were signed up for our Newsletter'.$rs, 'notice');
		  }
		}
	   
	
	}
	
	private function _checkCheckBox($cart=null)
	{
	   $subscribe_all = $this->params->get('subscribe_all'); 
	   if (!empty($subscribe_all)) return true; 
	   
	       
				$jinput = JFactory::getApplication()->input;
				
	   		    $cn = $this->_getcheckboxnames(); 
				$cn[] = 'mailchimp_register';
				$cn[] = 'mailchimp_checkbox';
				$cn[] = 'mailchimp';
				
				foreach ($cn as $chk)
				 {
				    if (!empty($cart))
					{
				    if (!empty($cart->BT[$chk])) 
					return true; 
					}
					
					if (!empty($jinput->post))
					{
					  $cht = $jinput->post->get($chk, false, 'RAW');
					  
					  if (!empty($cht)) return true; 
					}
					
					$cht = JRequest::getVar($chk, false); 
					if (!empty($cht)) return true; 
					
				 }
			
		return false; 	
	
	
	}
	// only calls mailchimp when the user is not yet registered
	public function plgVmOnUserOrder(&$_orderData) {
	    //$user_id = JFactory::getUser()->get('id'); 
		//if (empty($user_id))
		 
		 {
		    // guest registration: 
			$cart = VirtuemartCart::getCart(); 
			if (!empty($cart->BT))
			  {
			    $email = $cart->BT['email']; 
				$first_name = $cart->BT['first_name']; 
				$last_name = $cart->BT['last_name']; 
				if (isset($cart->BT['middle_name'])) {
					$middle_name = $cart->BT['middle_name']; 
				}
				else {
					$middle_name = ''; 
				}
				if ($this->_checkCheckBox())
				{
				  $this->_doRegistration($first_name, $last_name, $middle_name, $email); 
				}
				
				
			  }
			
		 }
		
	}
	
	
}
/*
function vm_mailchimp_async()
{
	if (function_exists('fastcgi_finish_request')) fastcgi_finish_request(); 
	$session = JFactory::getSession(); 
	   
	   $is = $session->get('vm_mailchimp_registered', null); 
	   if (is_null($is))
	   {
		   $email = JFactory::getUser()->get('email'); 
		   plgSystemVm_mailchimp::_isRegistered($email); 
	   }
	
	
	
}

if (function_exists('register_shutdown_function'))
register_shutdown_function( "vm_mailchimp_async" );
*/