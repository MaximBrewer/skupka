<?php
/**
 * @version		$Id: view.html.php 21705 2011-06-28 21:19:50Z RuposTel.com $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of banners.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
 jimport('joomla.application.component.view');

if(!class_exists('VmView'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
else
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmview.php');
}
 
class virtuemartViewAdd_shopper extends OPCView
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

    /**
     * Displays the view, collects needed data for the different layouts
     *
     * Okey I try now a completly new idea.
     * We make a function for every tab and the display is getting the right tabs by an own function
     * putting that in an array and after that we call the preparedataforlayoutBlub
     *
     * @author Oscar van Eijk
     * @author Max Milbers
	 *
	 *  Original code from: \components\com_virtuemart\views\user\view.html.php
	 *
     */
    function display($tpl = null) {
	
	
	$model = $this->getModel(); 
	   $model->loadVm(); 
	   
		$admin = false; 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	     if ((OPCmini::isSuperVendor()) || ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser()->authorise('core.admin', 'com_virtuemart')))) { 
			  $admin = true; 
			}
	   
	   if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
	
	  if (!class_exists('shopFunctionsF'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctionsf.php');
	   
	   if (!$admin) 
	   {
		   JFactory::getApplication()->enqueueMessage('Access Denied', 'error'); 
		   $return_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
		   
		   $cart = VirtuemartCart::getCart(); 
		   
		   //$uriby_vm = vmURI::getCurrentUrlBy('request');
		   
		   $html = shopFunctionsF::getLoginForm ($cart, FALSE,$return_url);
		   if (method_exists('vmURI', 'getCurrentUrlBy')) {
		     //$uriby_vm = vmURI::getCurrentUrlBy('request');
		     //$html = str_replace('"'.$uriby_vm.'"', JRoute::_('index.php?option=com_users&task=user.login', true, true), $html); 
		   }
		   echo $html; 
		   
		   return; 
	   }
   
	   
	   
	
	$useSSL = VmConfig::get('useSSL', 0);
	$useXHTML = true;
	$this->assignRef('useSSL', $useSSL);
	$this->assignRef('useXHTML', $useXHTML);

	$mainframe = JFactory::getApplication();
	$pathway = $mainframe->getPathway();
	$layoutName = $this->getLayout();
	
	if (!defined('OPC_IN_REGISTRATION_MODE'))
	define('OPC_IN_REGISTRATION_MODE', 1); 
	
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	OPCLang::loadLang(); 
	$this->loadOPC(); 		
	$layoutName = 'default'; 
    $this->setLayout($layoutName);
	$ftask = 'saveUser';
	$this->assignRef('fTask', $ftask);
	$user_id = JRequest::getInt('user_id', 0); 
	
	   $sgs = $model->getSgs(); 
	if (!empty($user_id)) {
		
		$db = JFactory::getDBO(); 
		$q = 'select `virtuemart_shoppergroup_id` from `#__virtuemart_vmuser_shoppergroups` where `virtuemart_user_id` = '.(int)$user_id; 
		$db->setQuery($q); 
		$gg = $db->loadAssocList(); 
		$gg_list = array(); 
		
		
		foreach ($gg as $kg => $vg) {
			$gavl = (int)$vg['virtuemart_shoppergroup_id']; 
			$gg_list[$gavl] = $gavl; 
		}
		foreach ($sgs as $k=>$row) {
			$row['virtuemart_shoppergroup_id'] = (int)$row['virtuemart_shoppergroup_id']; 
			$gid = $row['virtuemart_shoppergroup_id'];
			if (isset($gg_list[$gid])) {
				$sgs[$k]['selected'] = ' selected="selected" '; 
			}
			else 
			{
				$sgs[$k]['selected'] = ''; 
			}
		}
	}
	
	$this->assignRef('groups', $sgs);  

	


	if (!class_exists('ShopFunctions'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');

	if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
	$this->_model = new VirtuemartModelUser();

	//		$this->_model->setCurrent(); //without this, the administrator can edit users in the FE, permission is handled in the usermodel, but maybe unsecure?
	$editor = JFactory::getEditor();

	//the cuid is the id of the current user
	
	
	
	$this->_currentUser = JFactory::getUser($user_id);
	$this->_cuid = $this->_lists['current_id'] = $user_id; 
	
	$this->assignRef('userId', $this->_cuid);

	$this->_model->setId($user_id); 
	$this->_userDetails = $this->_model->getUser($user_id);

	$this->assignRef('userDetails', $this->_userDetails);

	$address_type = JRequest::getWord('addrtype', 'BT');
	$this->assignRef('address_type', $address_type);

	//New Address is filled here with the data of the cart (we are in the cart)
	
	$field_data = array(); 
	$virtuemart_userinfo_id = 0;
	if (!empty($user_id)) {
		$db = JFactory::getDBO(); 
		$q = 'select * from #__virtuemart_userinfos where virtuemart_user_id = '.(int)$user_id.' and address_type = "BT" limit 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		if (!empty($res)) {
		  $res = (array)$res; 
		  $virtuemart_userinfo_id = $res['virtuemart_userinfo_id']; 
		  $field_data = array(); 
		  foreach ($res as $ik=>$va) {
			  $field_data[$ik] = $va; 
		  }
		  
		  
		  
		}
		
		
		if (empty($virtuemart_userinfo_id)) $virtuemart_userinfo_id = 0; 
		$virtuemart_userinfo_id = (int)$virtuemart_userinfo_id;
	}
	
	$new = true;
	
	$this->assignRef('virtuemart_userinfo_id', $virtuemart_userinfo_id);

	$userFields = null;
    
	
			
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
	    $empty = ''; 
		$userFields = $userFieldsModel->getUserFieldsFilled($prepareUserFields,$field_data,$empty);
			
	    if (!empty($user_id)) {
			unset($userFields['fields']['password']); 
			unset($userFields['fields']['password2']); 
			if (isset($userFields['fields']['username'])) {
			$userFields['fields']['username']['value'] = $this->_currentUser->get('username');
			$userFields['fields']['username']['formcode'] = '<input type="username" value="'.htmlentities($this->_currentUser->get('username')).'" id="username" />'; 
			}
			if (isset($userFields['fields']['email'])) {
			$userFields['fields']['email']['value'] = $this->_currentUser->get('email');
			$userFields['fields']['email']['formcode'] = '<input type="email" value="'.htmlentities($this->_currentUser->get('email')).'" id="email_field" />'; 
			}
			if (isset($userFields['fields']['name'])) {
			$userFields['fields']['name']['value'] = $this->_currentUser->get('name');
			$userFields['fields']['name']['formcode'] = '<input type="text" value="'.htmlentities($this->_currentUser->get('name')).'" name="name" id="name_field" />';  
			}
		}
		else {
			if (isset($userFields['fields']['username'])) {
			  $userFields['fields']['username']['value'] = ''; 
			  $userFields['fields']['username']['formcode'] = '<input type="username" value="" id="username" />'; 
			}
			
			if (isset($userFields['fields']['email'])) {
				$userFields['fields']['email']['value'] = ''; 
				$userFields['fields']['email']['formcode'] = '<input type="email" value="" id="email_field" />'; 
			}
			
			if (isset($userFields['fields']['name'])) {
				$userFields['fields']['name']['value'] = '';
				$userFields['fields']['name']['formcode'] = '<input type="text" value="" name="name" id="name_field" />';  
			}
			
		}

		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		OPCUserFields::processAdminFields($userFields); 
	
    require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
	$OPCloader = new OPCloader(); 
	$action_url = JURI::root(true).'/index.php?option=com_onepage&amp;view=opc&amp;controller=opc&amp;task=opcregister&amp;nosef=1';
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php'); 
    OPCJavascript::getJavascript($this, $OPCloader, false, $action_url, 'com_onepage', 'opcregister'); 
	OPCloader::loadJavascriptFiles($this); 
	
	$registration_html = ''; 
	
	$op_formvars = OPCloader::getFormVars($this);
	
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
	 $reg_html = OPCCommonHtml::getFormVarsRegistration($OPCloader); 
	 
	 $op_formvars = $reg_html;
	 $op_formvars .= '<input type="hidden" name="admin_add_shopper_type" value="1" />'; 
	
	
	 if ((!empty($user_id)) && (!empty($virtuemart_userinfo_id))) {
		 $op_formvars .= '<input type="hidden" name="virtuemart_userinfo_id" value="'.(int)$virtuemart_userinfo_id.'" />'; 
		 $op_formvars .= '<input type="hidden" name="virtuemart_user_id" value="'.(int)$user_id.'" />'; 
		 
		 
		$op_formvars .= '<input type="hidden" name="addrtype" value="BT" id="field_addrtype" />'; 
		$op_formvars .= '<input type="hidden" name="address_type" value="BT" id="field_address_type" />'; 
		if (!empty($virtuemart_userinfo_id)) {
			$op_formvars .= '<input type="hidden" name="shipto_virtuemart_userinfo_id" value="'.(int)$virtuemart_userinfo_id.'" id="field_shipto_virtuemart_userinfo_id" />'; 
			$op_formvars .= '<input type="hidden" name="ship_to_info_id_bt" value="'.(int)$virtuemart_userinfo_id.'" id="field_ship_to_info_id" />'; 
		}
		 
	 }
	
	 $onsubmit = $op_onclick = $OPCloader->getJSValidator($this);
	 
	 $op_onclick = ' onclick="'.$op_onclick.'" '; 
		
	
	    $cart = VirtueMartCart::getCart();
		
		
	$vars = array('rowFields' => $userFields, 
				 'cart'=> $cart, 
				 'is_logged'=> false);
				 
	


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
	
	$this->assignRef('op_onclick', $op_onclick); 
	$this->assignRef('onsubmit', $onsubmit); 
	
	$lang = JFactory::getLanguage(); 
	$lang->load('com_onepage', JPATH_ADMINISTRATOR, 'en-GB', true);
	$lang->load('com_onepage', JPATH_ADMINISTRATOR, null, true);
	//COM_VIRTUEMART_SHOPPER_FORM_GROUP
    //$lang->load('com_virtuemart_shopper', JPATH_ADMINISTRATOR, 'en-GB', true);
	//$lang->load('com_virtuemart_shopper', JPATH_ADMINISTRATOR, null, true);
	OPCloader::setRegType(true); 
	$this->assignRef('op_formvars', $op_formvars); 
	$this->assignRef('registration_html', $registration_html); 
	$selected_template = OPCrenderer::getSelectedTemplate(); 
	$this->assignRef('selected_template', $selected_template); 
	$html = $renderer->fetch($OPCloader, 'list_user_fields_admin.tpl', $vars); 
	if (empty($html))
	$html = $renderer->fetch($OPCloader, 'list_user_fields_shipping.tpl', $vars); 

	$this->assignRef('fields_html',  $html); 
	$button_lbl = JText::_('COM_VIRTUEMART_REGISTER');
	$this->assignRef('button_lbl', $button_lbl);
	$this->assignRef('action_url', $action_url); 
	parent::display($tpl); 
	return;
	/*
	OPCloader::setRegType(); 
	
	$action_url = JURI::root(true).'/index.php?option=com_onepage&amp;view=opc&amp;controller=opc&amp;task=opcregister&amp;nosef=1';
	
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php'); 
    OPCJavascript::getJavascript($this, $OPCloader, false, $action_url, 'com_onepage', 'opcregister'); 
	
	$OPCloader->customizeFieldsPerOPCConfig($userFields);
	OPCloader::loadJavascriptFiles($this); 
	$op_formvars = OPCloader::getFormVars($this);
	$this->assignRef('userFields', $userFields);
	
	$this->lUser();
		
	if (JVM_VERSION <= 2)
	$this->shopper($userFields);
	else
	$this->shopper3($userFields);
		
	    
	    $this->lOrderlist();
	    $this->lVendor();
	

	
	//$this->_lists['shipTo'] = $this->generateStAddressList($this,$this->_model, $task,$cart);
	
	

    $_paneOffset = array();
	

	// Implement the Joomla panels. If we need a ShipTo tab, make it the active one.
	// In tmpl/edit.php, this is the 4th tab (0-based, so set to 3 above)
	jimport('joomla.html.pane');
	$pane = OPCPane::getInstance((defined('__VM_USER_USE_SLIDERS') ? 'Sliders' : 'Tabs'), $_paneOffset);

	$this->assignRef('lists', $this->_lists);

	$this->assignRef('editor', $editor);
	$this->assignRef('pane', $pane);

	

	
	$corefield_title = JText::_('COM_VIRTUEMART_USER_CART_INFO_CREATE_ACCOUNT');
	$pathway_text = JText::_('COM_VIRTUEMART_YOUR_ACCOUNT_DETAILS');
	$vmfield_title = JText::_('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL');
	$add_product_link="";

	  

	$document = JFactory::getDocument();
	$document->setTitle($pathway_text);
	$pathway->additem($pathway_text);
	$document->setMetaData('robots','NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET');
	$this->assignRef('page_title', $pathway_text);
	$this->assignRef('corefield_title', $corefield_title);
	$this->assignRef('vmfield_title', $vmfield_title);
	
	//   if ($onlyindex) return JURI::root(true).'/index.php'; 
			
	
	//shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 
	  OPCremoveMsgs::removeMsgs($cart); 
	 
	 $jsvalidator = $OPCloader->getJSValidatorScript($this); 
	 $op_userfields = $OPCloader->getBTfields($this, true); 
	 
	 
	 $registration_html = $OPCloader->getRegistrationHhtml($this);
	 
	 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
	 $reg_html = OPCCommonHtml::getFormVarsRegistration($OPCloader); 
	 
	 $op_formvars = $reg_html.$jsvalidator;
	 $op_userfields .= $op_formvars; 
	 $onsubmit = $op_onclick = $OPCloader->getJSValidator($this);
	 
	 $op_onclick = ' onclick="'.$op_onclick.'" '; 
	 
	 ob_start(); 
	 //parent::display($tpl);
	 include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'registration_templates.php');  
	 $html = ob_get_clean(); 
	 
	 //$html = str_replace('"com_virtuemart"', '"com_onepage"', $html); 
	 //$html = str_replace('=com_virtuemart', '=com_onepage', $html); 
	 
	 $this->getActions($html); 
	 
	 $html = $OPCloader->addListeners($html);
	 echo $html; 
	 
	 
	 
	 $extras = $OPCloader->getExtras($this); 
	 echo $extras; 
	 
	  */
	 
	}
	function getExtraHTML()
	{
	 $html = '<input type="hidden" name="task_opcreplace" value="opcregister" />
	 <input type="hidden" name="controller_opcreplace" value="opc" />
	 <input type="hidden" name="view_opcreplace" value="opc" />
	 '; 
	 return $html; 
	}
	
	function script($file, $path, $arg, $onload="")
	{
	  
	 
	  
	  JHTMLOPC::script($file, $path, $arg);
	  
	}
	
	/**
	 * This generates the list when the user have different ST addresses saved
	 *
	 * @author Oscar van Eijk
	 */
	function generateStAddressList ($view, $userModel, $task, $cart) {
	include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	if (!empty($op_disable_shipto)) return ''; 
	
		// Shipment address(es)
		$_addressList = $userModel->getUserAddressList ($userModel->getId (), 'ST');
		if (count ($_addressList) == 1 && empty($_addressList[0]->address_type_name)) {
			return JText::_ ('COM_VIRTUEMART_USER_NOSHIPPINGADDR');
		} else {
			$_shipTo = array();
			$useXHTTML = empty($view->useXHTML) ? false : $view->useXHTML;
			$useSSL = empty($view->useSSL) ? FALSE : $view->useSSL;

			for ($_i = 0; $_i < count ($_addressList); $_i++) {
				if (empty($_addressList[$_i]->virtuemart_user_id)) {
					$_addressList[$_i]->virtuemart_user_id = JFactory::getUser ()->id;
				}
				if (empty($_addressList[$_i]->virtuemart_userinfo_id)) {
					$_addressList[$_i]->virtuemart_userinfo_id = 0;
				}
				if (empty($_addressList[$_i]->address_type_name)) {
					$_addressList[$_i]->address_type_name = 0;
				}

				$_shipTo[] = '<li>' . '<a href="'.JRoute::_('index.php'
					. '?option=com_virtuemart'
					. '&view=user'
					. '&nosef=1'
					. '&task=' . $task
					. '&addrtype=ST'
					. '&virtuemart_user_id[]=' . $_addressList[$_i]->virtuemart_user_id
					. '&virtuemart_userinfo_id=' . $_addressList[$_i]->virtuemart_userinfo_id
					 ).'">' . $_addressList[$_i]->address_type_name . '</a> ' ;

				$_shipTo[] = '&nbsp;&nbsp;<a href="'.JRoute::_ ('index.php?option=com_virtuemart&nosef=1&view=user&task=removeAddressST&virtuemart_user_id[]=' . $_addressList[$_i]->virtuemart_user_id . '&virtuemart_userinfo_id=' . $_addressList[$_i]->virtuemart_userinfo_id, $useXHTTML, $useSSL ). '" class="icon_delete">'.JText::_('COM_VIRTUEMART_USER_DELETE_ST').'</a></li>';

			}


			$addLink = '<a href="' . JRoute::_ ('index.php?option=com_virtuemart&view=user&task=' . $task . '&new=1&addrtype=ST&nosef=1&virtuemart_user_id[]=' . $userModel->getId (), $useXHTTML, $useSSL) . '"><span class="vmicon vmicon-16-editadd"></span> ';
			$addLink .= JText::_ ('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL') . ' </a>';

			return $addLink . '<ul>' . join ('', $_shipTo) . '</ul>';
		}
	}
	
	function getActions(&$html)
	 {
	   $h2 = '<input type="hidden" name="opcregistration" value="1" />';
	   $html = str_replace('</form', $h2.'</form', $html); 
	   $html = str_replace('</FORM', $h2.'</FORM', $html); 
	   return; 
	   $x0 = stripos($html, '<input type="hidden'); 
	   $html = substr($html, 0, $x0).$h2.substr($html, $x0); 
	   /*
	   //$html = str_replace('view="user"', 'view="opc"', $html); 
	   $html = str_replace('name="view"', 'name="view_orig"', $html); 
	   //$html = str_replace('controller="user"', 'controller="opc"', $html); 
	   $html = str_replace('name="controller"', 'name="controller_orig"', $html); 
	   $html = str_replace('name="task"', 'name="task_original"', $html); 
	   
	  
	   $html = str_replace('action="', 'action="'.JRoute::_('index.php').'" actionold="', $html); 
	   $h2 = $this->getExtraHTML(); 
	   $count = 1;
	   $x0 = stripos($html, '<input type="hidden'); 
	   $html = substr($html, 0, $x0).$h2.substr($html, $x0); 
	   $html = str_replace('_opcreplace', '', $html); 
	   //$html = str_replace('<input type="hidden', $h2.'<input type="hidden', $html, $count); 
	   $this->actionUrl($html); 
	   */
	 }
	 
	 function actionUrl(&$html)
	 {
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
	   $action = OPCTransform::getFT($html, 'form', 'adminForm', 'name', 'userForm', '>', 'action');
	   $newaction = JRoute::_('index.php?option=com_virtuemart&controller=opc&task=opcregistration&nosef=1&view=opc&format=opchtml&tmpl=component'); 
	   foreach ($action as $str)
	    {
		  $html = str_replace('action="'.$str.'"', 'action="'.$newaction.'"', $html); 
		}

	   /*
	   $x1 = stripos('<form ', $html); 
	   $x2 = stripos('action=', $html, $x1); 
	   if ($x2 !== false)
	    {
		  $x3 = stripos('"', $html, $x2); 
		  $x3
		}
		*/
	 }
	 
	function loadOPC()
	 {
	    $language = JFactory::getLanguage();
		$language->load('com_onepage', JPATH_SITE, 'en-GB', true);
		$language->load('com_onepage', JPATH_SITE, null, true);
		
	 }
	 
	 function checkEnabledShipto()
	 {
	 
	   $op_disable_shipto = OPCloader::getShiptoEnabled($this->cart); 
	 }
	 
	 //stAn: original functions: 
	 function payment() {

    }

    function lOrderlist() {
	// Check for existing orders for this user
	$orders = VmModel::getModel('orders');

	
	    // getOrdersList() returns all orders when no userID is set (admin function),
	    // so explicetly define an empty array when not logged in.
	    $this->_orderList = array();
	 
		if($this->_orderList){
			VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		}
		$this->assignRef('orderlist', $this->_orderList);
    }

	 function shopper3($userFields) {

	// Shopper info
	if (!class_exists('VirtueMartModelShopperGroup'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php');

	$_shoppergroup = VirtueMartModelShopperGroup::getShoppergroupById($this->_model->getId());

		$user = JFactory::getUser();
		if($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart')) {

		$shoppergrps = array();
		foreach($_shoppergroup as $group){
			$shoppergrps[] = $group['virtuemart_shoppergroup_id'];
		}
	   $this->_lists['shoppergroups'] = ShopFunctions::renderShopperGroupList($shoppergrps);
	   $this->_lists['vendors'] = ShopFunctions::renderVendorList($this->userDetails->virtuemart_vendor_id);
	} else {
		$this->_lists['shoppergroups'] = '';
		foreach($_shoppergroup as $group){
			$this->_lists['shoppergroups'] .= $group['shopper_group_name'].', ';
		}
		$this->_lists['shoppergroups'] = substr($this->_lists['shoppergroups'],0,-2);

	    if (!empty($this->userDetails->virtuemart_vendor_id)) {
		$this->_lists['vendors'] = $this->userDetails->virtuemart_vendor_id;
	    }

	    if (empty($this->_lists['vendors'])) {
		$this->_lists['vendors'] = JText::_('COM_VIRTUEMART_USER_NOT_A_VENDOR'); // . $_setVendor;
	    }
	}

	//todo here is something broken we use $userDetailsList->perms and $this->userDetailsList->perms and perms seems not longer to exist
	//todo we should list here the joomla ACL groups

	// Load the required scripts
	if (count($userFields['scripts']) > 0) {
	    foreach ($userFields['scripts'] as $_script => $_path) {
		JHTMLOPC::script($_script, $_path);
	    }
	}
	// Load the required styresheets
	if (count($userFields['links']) > 0) {
	    foreach ($userFields['links'] as $_link => $_path) {
		JHTMLOPC::stylesheet($_link, $_path);
	    }
	}
    }

	
	
    function shopper($userFields) {

	// Shopper info
	if (!class_exists('VirtueMartModelShopperGroup'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shoppergroup.php');

	$_shoppergroup = VirtueMartModelShopperGroup::getShoppergroupById($this->_model->getId());

	
	if (!class_exists('Permissions'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'permissions.php');

	if (Permissions::getInstance()->check('admin,storeadmin')) {

		$shoppergrps = array();
		foreach($_shoppergroup as $group){
			$shoppergrps[] = $group['virtuemart_shoppergroup_id'];
		}
	   $this->_lists['shoppergroups'] = ShopFunctions::renderShopperGroupList($shoppergrps);
	   $this->_lists['vendors'] = ShopFunctions::renderVendorList($this->_userDetails->virtuemart_vendor_id);
	} else {
		$this->_lists['shoppergroups'] = '';
		foreach($_shoppergroup as $group){
			$this->_lists['shoppergroups'] .= $group['shopper_group_name'].', ';
		}
		$this->_lists['shoppergroups'] = substr($this->_lists['shoppergroups'],0,-2);

	    if (!empty($this->_userDetails->virtuemart_vendor_id)) {
		$this->_lists['vendors'] = $this->_userDetails->virtuemart_vendor_id;
	    }

	    if (empty($this->_lists['vendors'])) {
		$this->_lists['vendors'] = JText::_('COM_VIRTUEMART_USER_NOT_A_VENDOR'); // . $_setVendor;
	    }
	}
	if (method_exists($this->_model, 'getGroupList'))
	{
	  $_groupList = $this->_model->getGroupList();
	  if (!is_array($_groupList)) {
	    $this->_lists['gid'] = '<input type="hidden" name="gid" value="' . $this->_userDetails->JUser->get('gid') . '" /><strong>' . JText::_($_groupList) . '</strong>';
	} else {
	    $this->_lists['gid'] = JHTML::_('select.genericlist', $_groupList, 'gid', 'size="10"', 'value', 'text', $this->_userDetails->JUser->get('gid'));
	}
	}
	else
	 {
	 
	 
	//todo here is something broken we use $_userDetailsList->perms and $this->_userDetailsList->perms and perms seems not longer to exist
	if (Permissions::getInstance()->check("admin,storeadmin")) {
	    $this->_lists['perms'] = JHTML::_('select.genericlist', Permissions::getUserGroups(), 'perms', '', 'group_name', 'group_name', $this->_userDetails->perms);
	} else {
	    if (!empty($this->_userDetails->perms)) {
		$this->_lists['perms'] = $this->_userDetails->perms;

		$_hiddenInfo = '<input type="hidden" name="perms" value = "' . $this->_lists['perms'] . '" />';
		$this->_lists['perms'] .= $_hiddenInfo;
	    }
	}
	}

	// Load the required scripts
	if (count($userFields['scripts']) > 0) {
	    foreach ($userFields['scripts'] as $_script => $_path) {
		JHTMLOPC::script($_script, $_path);
	    }
	}
	// Load the required styresheets
	if (count($userFields['links']) > 0) {
	    foreach ($userFields['links'] as $_link => $_path) {
		JHTMLOPC::stylesheet($_link, $_path);
	    }
	}
    }

    function lUser() {
	
	if (!defined('VM_VERSION') || (VM_VERSION < 3))
	{
	$_groupList = $this->_model->getGroupList();

	if (!is_array($_groupList)) {
	    $this->_lists['gid'] = '<input type="hidden" name="gid" value="' . $this->_userDetails->JUser->get('gid') . '" /><strong>' . JText::_($_groupList) . '</strong>';
	} else {
	    $this->_lists['gid'] = JHTML::_('select.genericlist', $_groupList, 'gid', 'size="10"', 'value', 'text', $this->_userDetails->JUser->get('gid'));
	}

	if (!class_exists('shopFunctionsF'))
	    require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctionsf.php');
	
	$comUserOption = 'com_users'; 

	$this->_lists['canBlock'] = ($this->_currentUser->authorize($comUserOption, 'block user')
		&& ($this->_model->getId() != $this->_cuid)); // Can't block myself TODO I broke that, please retest if it is working again
	$this->_lists['canSetMailopt'] = $this->_currentUser->authorize('workflow', 'email_events');
	$this->_lists['block'] = JHTML::_('select.booleanlist', 'block', 'class="inputbox"', $this->_userDetails->JUser->get('block'), 'COM_VIRTUEMART_YES', 'COM_VIRTUEMART_NO');
	$this->_lists['sendEmail'] = JHTML::_('select.booleanlist', 'sendEmail', 'class="inputbox"', $this->_userDetails->JUser->get('sendEmail'), 'COM_VIRTUEMART_YES', 'COM_VIRTUEMART_NO');

	$this->_lists['params'] = $this->_userDetails->JUser->getParameters(true);

	$this->_lists['custnumber'] = $this->_model->getCustomerNumberById();

	//TODO I do not understand for what we have that by Max.
	if ($this->_model->getId() < 1) {
	    $this->_lists['register_new'] = 1;
	} else {
	    $this->_lists['register_new'] = 0;
	}
	
	}
	else
	{
	   // VM3 code here: 
	   $currentUser = JFactory::getUser();
		// Can't block myself TODO I broke that, please retest if it is working again
		$this->lists['canBlock'] = ($currentUser->authorise('com_users', 'block user') && ($this->_model->getId() != $this->_cuid));
		$this->lists['canSetMailopt'] = $currentUser->authorise('workflow', 'email_events');
		$this->_lists['block'] = JHtml::_('select.booleanlist', 'block', 'class="inputbox"', $this->userDetails->JUser->get('block'), 'COM_VIRTUEMART_YES', 'COM_VIRTUEMART_NO');
		$this->_lists['sendEmail'] = JHtml::_('select.booleanlist', 'sendEmail', 'class="inputbox"', $this->userDetails->JUser->get('sendEmail'), 'COM_VIRTUEMART_YES', 'COM_VIRTUEMART_NO');

		$this->_lists['params'] = $this->userDetails->JUser->getParameters(true);

		$this->_lists['custnumber'] = $this->_model->getCustomerNumberById();

	}

	
    }

    function lVendor() {

	// If the current user is a vendor, load the store data
	if ($this->_userDetails->user_is_vendor) {

	    $currencymodel = VmModel::getModel('currency', 'VirtuemartModel');
	    $currencies = $currencymodel->getCurrencies();
	    $this->assignRef('currencies', $currencies);

	    if (!$this->_orderList) {
			$this->lOrderlist();
	    }

	    $vendorModel = VmModel::getModel('vendor');

	    if (Vmconfig::get('multix', 'none') === 'none') {
		$vendorModel->setId(1);
	    } else {
		$vendorModel->setId($this->_userDetails->virtuemart_vendor_id);
	    }
	    $vendor = $vendorModel->getVendor();
	    $vendorModel->addImages($vendor);
	    $this->assignRef('vendor', $vendor);
	}
    }

    /*
     * renderMailLayout
     *
     * @author Max Milbers
     * @author Valerie Isaksen
     */

    public function renderMailLayout($doVendor, $recipient) {

	$useSSL = VmConfig::get('useSSL', 0);
	$useXHTML = true;
	$this->assignRef('useSSL', $useSSL);
	$this->assignRef('useXHTML', $useXHTML);
	$userFieldsModel = VmModel::getModel('UserFields');
	$userFields = $userFieldsModel->getUserFields();
	
	
	/*
	
	$this->userFields = $userFieldsModel->getUserFieldsFilled($userFields, $this->user);
    */
	$usermodel = VmModel::getModel('user');
	$usermodel->_data = null; 
	if (empty($usermodel->_id))
	{
	 if (!empty($GLOBALS['opc_new_user']))
	 $usermodel->_id = $GLOBALS['opc_new_user']; 
	}
	if (isset($this->user))
	if (isset($this->user->userInfo))
	{
	 $vmuser = $this->user->userInfo; 
	}
	else
	{
	$vmuser = $usermodel->getUser();
	$vmuser = reset($vmuser->userInfo);
	}
	$this->userFields = $userFieldsModel->getUserFieldsFilled($userFields, $vmuser);
	
	

    if (VmConfig::get('order_mail_html')) {
	    $mailFormat = 'html';
	    $lineSeparator="<br />";
    } else {
	    $mailFormat = 'raw';
	    $lineSeparator="\n";
    }

    $virtuemart_vendor_id=1;
    $vendorModel = VmModel::getModel('vendor');
    $vendor = $vendorModel->getVendor($virtuemart_vendor_id);
    $vendorModel->addImages($vendor);
	
	if (method_exists($vendorModel, 'getVendorAddressFields'))
	$vendor->vendorFields = $vendorModel->getVendorAddressFields();
    $this->assignRef('vendor', $vendor);

	if (!$doVendor) {
	    $this->subject = JText::sprintf('COM_VIRTUEMART_NEW_SHOPPER_SUBJECT', $this->user->username, $this->vendor->vendor_store_name);
	    $tpl = 'mail_' . $mailFormat . '_reguser';
	} else {
	    $this->subject = JText::sprintf('COM_VIRTUEMART_VENDOR_NEW_SHOPPER_SUBJECT', $this->user->username, $this->vendor->vendor_store_name);
	    $tpl = 'mail_' . $mailFormat . '_regvendor';
	}

	$this->assignRef('recipient', $recipient);
	$this->vendorEmail = $vendorModel->getVendorEmail($this->vendor->virtuemart_vendor_id);
	$this->layoutName = $tpl;
	$this->setLayout($tpl);
	parent::display();
    }

	 
	 
}

