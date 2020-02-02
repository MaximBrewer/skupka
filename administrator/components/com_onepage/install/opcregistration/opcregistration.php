<?php
/**
 * @version		One page checkout for Virtuemart - plugins gallery
 * @copyright	Copyright (C) 2005 - 2011 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemOpcregistration extends JPlugin
{
    public function onAfterRoute() {
	
	$user = JFactory::getUser(); 
	
	if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) return; 
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	$default = false; 
	$opc_override_registration_logged = OPCconfig::get('opc_override_registration_logged', $default); 
	if (empty($opc_override_registration_logged)) {
	 if (!empty($user->id)) return; 
	 if (empty($user->guest)) return; 
	}
	
	if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
	if (!file_exists(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'plugin.php')) return;
	require_once(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'pluginregistration.php'); 
	require_once(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'plugin.php'); 
	
	if (!OPCplugin::checkLoad()) return; 
	 if (OPCplugin::alterRegistration()) return; 
	 $option = JRequest::getVar('option', ''); 
	 
	 if ($option != 'com_virtuemart') return; 
	 $view = JRequest::getVar('view', ''); 
	 if (($view != 'user') && ($view != 'opcuser')) return;
	 
	 $task = JRequest::getVar('task', ''); 
	 
	 $controller = JRequest::getVar('controller', ''); 
	 if (($controller =='opc') && ($view == 'opcuser'))
	    {
		
		 JRequest::setVar('view', 'opc'); 
		 
	     if (strpos($controller, '..')!==false) die('?'); 
	     require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'controllers'. DIRECTORY_SEPARATOR .'opc.php'); 
		 
		 //OPC checkout loads the user page for editaddresscheckout, registerCheckoutUser, saveCheckoutUser
		 $allowed = array('saveuser', 'display', 'editaddressst', 'editaddresscart',  'savecartuser', 'registercartuser', 'saveuser', 'saveaddressst',  'cancelcartuser', 'cancelcheckoutuser', 'cancel', 'removeaddressst'); 
		 $task = strtolower($task); 
		 if (in_array($task, $allowed))
		   {
		     JRequest::setVar('task_original', $task); 
			 JRequest::setVar('task', 'opcregister');
			 
		   }
		 
	    }
	 
	 
	 // check OPC loaded
	 //if (!defined('JPATH_OPC')) return; 
	 // check OPC cart includes
	 
	 $ign = array('editaddresscheckout', 'pluginUserPaymentCancel', 'opc', 'cart');
	 if (in_array($task, $ign)) return; 
	 
	 // check includes that we do not need: 
	 $ign_layout = array('login', 'mailregisteruser', 'mail_html_reguser', 'mail_html_regvendor', 'mail_raw_reguser', 'mail_raw_regvendor', 'edit_vendor', 'edit_orderlist');
	 
	 $layout = JRequest::getVar('layout', 'default'); 
	 if (in_array($layout, $ign_layout)) return; 
	 
	 
	 if (!class_exists('VirtueMartViewUser'))
	 require(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'overrides'. DIRECTORY_SEPARATOR .'virtuemart.user.registration.view.html.php'); 
	 
	 
	 
	

	
	}
	
	public function plgVmOnMainController($_controller)
	{
	  
	 
	  
	   $arr = array('user', 'registration'); 
	   if (in_array($_controller, $arr))
	   {
		   if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) return; 
		   
		   
	   $isopc = JRequest::getVar('opcregistration', false); 
	   if (!empty($isopc))
	    {
		  if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);   
		  JRequest::setVar('task', 'opcregister'); 
		  require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'controllers'. DIRECTORY_SEPARATOR .'opc.php'); 
		  $opc = new VirtueMartControllerOpc(); 
		  $msg = $opc->opcregister(); 
		  $opc->setRedirect(JRoute::_( 'index.php?option=com_virtuemart&view=user',false),$msg);
		  
		  $app = JFactory::getApplication(); 
		  $app->close(); 
		  
		  
		}
	  }
	}
	
	
	
	
	
	
	public function onAfterRender()
	{

		return true;
	}
	
	// triggered from: \administrator\components\com_virtuemart\models\orders.php
	public function plgVmOnUserOrder(&$_orderData)
	{
		
	}
	function plgVmOnUserStore(&$data)
	{
	  
	  //if ((empty($data['username'])) && (!empty($data['email']))) $data['username'] = $data['email']; 
	}
	
	
	
}
