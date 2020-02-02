<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

if(!class_exists('VmView'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
else
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmview.php');
}
 
class virtuemartViewThird_address extends OPCView
{
	 private $_model;
    private $_currentUser = 0;
    private $_cuid = 0;
    private $_userDetails = 0;
    private $_userFieldsModel = 0;
    private $_userInfoID = 0;
    private $_list = 0;
    private $_orderList = 0;
    private $_openTab = 0;

  
    function display($tpl = null) {
	    
		 $template = JFactory::getApplication('site')->getTemplate();
		$viewName = $this->getName(); 
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'default.php'; 
	    $dir = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR;
		if (file_exists($path)) 
		{
				if (method_exists($this, 'addTemplatePath')) {
					$this->addTemplatePath($dir);
				}
				else
				{
					if (method_exists($this, 'addIncludePath')) 
					{
					$this->addIncludePath( $dir );
					}
				}
			
		
		}
		
		@header('Content-Type: text/html; charset=utf-8');
	   @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	   @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		
		$user_id = JRequest::getVar('user_id', 0); 
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
	   }
	   
	   if (empty($user_id)) {
		   JFactory::getApplication()->enqueueMessage('You must be logged in to the site to use this feature', 'error'); 
		   return; 
	   }
   
	   
	   $model = $this->getModel(); 
	   $model->loadVm(); 
	
	$useSSL = VmConfig::get('useSSL', 0);
	$useXHTML = true;
	$this->assignRef('useSSL', $useSSL);
	$this->assignRef('useXHTML', $useXHTML);

	$mainframe = JFactory::getApplication();
	
	
	
	if (!defined('OPC_IN_REGISTRATION_MODE'))
	define('OPC_IN_REGISTRATION_MODE', 1); 
	
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	OPCLang::loadLang(); 
	$this->loadOPC(); 		
	$layoutName = 'default'; 
    $this->setLayout($layoutName);
	$ftask = 'saveUser';
	$this->assignRef('fTask', $ftask);
	
	  

	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php');
	


	if (!class_exists('ShopFunctions'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');

	if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
	$userM = new VirtuemartModelUser();

	
	

	//the cuid is the id of the current user
	$this->_currentUser = JFactory::getUser();
	$this->_cuid = $this->_lists['current_id'] = $user_id; 
	
	$this->assignRef('userId', $this->_cuid);

	$userM->setId($user_id); 
	$this->_userDetails = $userM->getUser($user_id);

	
	
	$this->assignRef('userDetails', $this->_userDetails);

	
	$this->address_type = 'RD';

	//New Address is filled here with the data of the cart (we are in the cart)
	$db = JFactory::getDBO(); 
	$q = 'select * from #__virtuemart_userinfos where virtuemart_user_id = '.(int)$user_id.' and address_type = "RD" limit 0,1'; 
	$db->setQuery($q); 
	$data_third = $db->loadAssoc(); 
	
	$virtuemart_userinfo_id = 0;
	$new = true;
	if (!empty($data_third)) {
	  $virtuemart_userinfo_id = (int)$data_third['virtuemart_userinfo_id']; 
	  $new = false; 
	}
	
	
	
	
	
	
	$this->assignRef('virtuemart_userinfo_id', $virtuemart_userinfo_id);

	
	  if (method_exists('VmConfig', 'loadJLang'))
	  VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
	  
	  
		
		
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
	
	
    require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	$OPCloader = new OPCloader(); 
	
	$root = Juri::root(); 
	if (substr($root, -1) !== '/') $root .= '/';  

	
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
	 $reg_html = OPCCommonHtml::getFormVarsRegistration($OPCloader, 'opcthird'); 
	 
	 $op_formvars = $reg_html;
	
	
	$isSite = JFactory::getApplication()->isSite(); 
	if (!$isSite) {
       $root .= 'administrator/'; 
    }

		$action_url = $root.'index.php?option=com_onepage&amp;view=opc&amp;controller=opc&amp;task=opcthird&amp;nosef=1';
	
	  $tmpl = JRequest::getVar('tmpl', ''); 
	  if (!empty($tmpl)) {
	    $action_url .= '&tmpl='.$tmpl; 
		$op_formvars .=  '<input type="hidden" name="tmpl" value="'.$this->escape($tmpl).'" />'; 
	  }
	  
	  $Itemid = JRequest::getVar('Itemid', ''); 
	  if (!empty($Itemid)) {
	    $action_url .= '&Itemid='.$Itemid; 
	  }
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php'); 
    OPCJavascript::getJavascript($this, $OPCloader, false, $action_url, 'com_onepage', 'opcthird'); 
	OPCloader::loadJavascriptFiles($this); 
	
	$registration_html = ''; 
	
	
	
	
	 $op_formvars .= '<input type="hidden" name="admin_add_shopper_type" value="1" />'; 
	 $op_formvars .= '<input type="hidden" name="user_id" value="'.(int)$user_id.'" />'; 
	 $op_formvars .= '<input type="hidden" name="third_address_opened" value="1" />'; 
	 if (!$admin)  {
	   $order_id = 0; 
	 }
	 else {
	 $order_id = JRequest::getInt('virtuemart_order_id', false); 
	 
	 if (!empty($order_id)) {
	   $op_formvars .= '<input type="hidden" name="virtuemart_order_id" value="'.(int)$order_id.'" />'; 
	 }
	 }
	 
	//third_address_opened
	 $onsubmit = $op_onclick = $OPCloader->getJSValidator($this);
	 
	 $op_onclick = ' onclick="'.$op_onclick.'" '; 
		
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
	    $cart = VirtueMartCart::getCart();
		
		
	
	


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
	
	$this->assignRef('op_onclick', $op_onclick); 
	$this->assignRef('onsubmit', $onsubmit); 
	
	
	
	$lang = JFactory::getLanguage(); 
	$lang->load('com_onepage', JPATH_ADMINISTRATOR, 'en-GB', true);
	$lang->load('com_onepage', JPATH_ADMINISTRATOR, null, true);
	$lang->load('com_virtuemart', JPATH_SITE);
	//COM_VIRTUEMART_SHOPPER_FORM_GROUP
    //$lang->load('com_virtuemart_shopper', JPATH_ADMINISTRATOR, 'en-GB', true);
	//$lang->load('com_virtuemart_shopper', JPATH_ADMINISTRATOR, null, true);
	OPCloader::setRegType(true); 
	$this->assignRef('op_formvars', $op_formvars); 
	$this->assignRef('registration_html', $registration_html); 
	$selected_template = OPCrenderer::getSelectedTemplate(); 
	$this->assignRef('selected_template', $selected_template); 
	
	if ((!isset($cart->RDopen)) && (empty($cart->RDopen))) $set = false; 
	
	$cart->RDopen = true; 
	$suffix = ' readonly="readonly" style="display: none;" '; 
	$html = OPCthirdAddress::renderThirdAddress($cart, $user_id, $suffix, $order_id); 

	$this->assignRef('fields_html',  $html); 
	$button_lbl = OPCLang::_('COM_VIRTUEMART_REGISTER');
	$this->assignRef('button_lbl', $button_lbl);
	$this->assignRef('action_url', $action_url); 
	ob_start(); 
	parent::display($tpl); 
	$x = ob_get_clean(); 
	echo $x; 
	
	if (!empty($set)) {
	   unset($cart->RDopen); 
	}
	
	return;
	
	 
	}
	
	
	
	
	
	 
	 
	 
	function loadOPC()
	 {
	    $language = JFactory::getLanguage();
		$language->load('com_onepage', JPATH_SITE, 'en-GB', true);
		$language->load('com_onepage', JPATH_SITE, null, true);
		
	 }
	 
	 
	 
	 
	 
}

