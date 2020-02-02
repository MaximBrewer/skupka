<?php  
/** 
 * Overrided Cart View class for the One Page Checkout and Virtuemart 2
 * This is the main loader of the checkout view itself independent on user selected template in virtuemart
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 *
 * ORIGINAL LICENSE AND COPYRIGHT NOTICE
 *
 * View for the shopping cart, modified for One Page Checkout by RuposTel
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
 * @author Oscar van Eijk
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 4999 2011-12-09 21:31:02Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport('joomla.application.component.view');



require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'version.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ajaxhelper.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 

if(!class_exists('VmView'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
else
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmview.php');
}

if (class_exists('vRequest'))
if (!class_exists('vmRequest'))
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmrequest.php'); 


			
			
			if (!class_exists( 'VmConfig' )) 
			{
				require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig(); 
			}

//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
/**
 * View for the shopping cart
 * @package VirtueMart
 * @author Max Milbers
 * @author Patrick Kohl
 */
class VirtueMartViewCart extends VmView {
	
	public function display($tpl = null) {

	
		JFactory::getApplication()->set('is_rupostel_opc', true);
		if (!class_exists('ShopFunctions'))
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
		
	    // let's remove vm's missing data: 
		$layoutName = $this->getLayout();
		if (!$layoutName)
		$layoutName = JRequest::getWord('layout', 'default');
	
		if (((!empty($layoutName)) && ($layoutName === 'order_done')) || ((!empty($this->layout)) && ($this->layout === 'order_done')))
		{
			return $this->renderOrderDone($tpl); 
		}
		
	     $this->found_payment_method = 0;
		
		if(!class_exists('calculationHelper')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
			$calc = calculationHelper::getInstance();
		
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');  
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');  
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
		
		//view=vmplg&task=pluginUserPaymentCancel
		$view = JRequest::getVar('view', ''); 
		$task = strtolower(JRequest::getVar('task', '')); 
		if (($view === 'vmplg') && ($task === 'pluginuserpaymentcancel')) {
				$cancel_page_url = OPCconfig::get('cancel_page_url', ''); 
				if (!empty($cancel_page_url)) {
					JFactory::getApplication()->redirect($cancel_page_url); 
					JFactory::getApplication()->close(); 
				}
			
		}

		
		$OPCloader = new OPCloader; 
		
		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$cart = OPCmini::getCart(); 

		
		
			//testing: 
		$this->cart =& $cart; 
			
		$task2 = JRequest::getVar('task2', ''); 
		if ($task2 == 'deletecoupons') {
		  $session = JFactory::getSession(); 
		  $session->set('opc_last_coupon', ''); 
		  $session->set('coupon', '', 'awocoupon'); 
		  $cart->couponCode = '';   
		  $opc_debug = OPCconfig::get('opc_debug', false);  
		  if (!empty($opc_debug)) 
		  { 
	        JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon cleared at '.__FILE__.':'.__LINE__); 
		  }
		}
		
		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$cart = OPCmini::getCart(); 
		$this->cart =& $cart; 
		
		
		
		
		//don't validate coupons during DISPLAY
		$store_coupon = $cart->couponCode; 
		if (!empty($store_coupon)) {
			  if (is_array($cart->_triesValidateCoupon)) {
				  $cart->_triesValidateCoupon[$store_coupon] = $store_coupon; 
			  }
			}
		
		
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'cart_override.php');
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php');		
		$OPCcheckout = new OPCcheckout($cart); 
		
		
		
		
		$startM = OPCremoveMsgs::prepareClearMsgs(true);  
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		  $e = ''; 
		  foreach ($cart->cartProductsData as $k=>$p) {
		  
		  $p = (array)$p; 
		  $cart->cartProductsData[$k] = (array)$p; 
		  
	      OPCcheckout::$current_cart =& $cart; 
		  $adjustQ = true; 
		 
		   
		   $pid = $p['virtuemart_product_id']; 
		   $productModel = VmModel::getModel('product');
	       $productTemp = $productModel->getProduct($pid);
		   
		   $cart->cartProductsData[$k]['quantity'] = (float)$p['quantity']; 
		   
		   $res = $OPCcheckout->checkForQuantities($productTemp, $cart->cartProductsData[$k]['quantity'], $e, $adjustQ); 
		 
		 
		   }
		
		}
		else {
		foreach ($cart->products as &$p) {
		 
	     OPCcheckout::$current_cart =& $cart; 
		 $adjustQ = true; 
		 $e = ''; 
		
		 $res = $OPCcheckout->checkForQuantities($p, $p->quantity, $e, $adjustQ); 
		 
		}
		}
		
		
		
		
		
		if ((!empty($e)) || (empty($res))) {
			
		 if (defined('VM_VERSION') && (VM_VERSION >= 3))
		 {
			
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			$cart = OPCmini::getCart(); 
			
			if (!empty($e))
			JFactory::getApplication('site')->enqueueMessage($e, 'error');
		 }
		}
		
		//VirtueMartControllerOpc::clearMsgs($startM);  
		

		
		$OPCloader->loadStored($cart); 
		
		$layout_path = $cart->layoutPath; 
		
	    $saved_p = $cart->virtuemart_paymentmethod_id; 
		$saved_s = $cart->virtuemart_shipmentmethod_id; 
		$cart->saved_p = $saved_p; 
		$cart->saved_s = $saved_s; 
		
		
		if (empty($cart->vendorId))
		{
			$cart->vendorId = 1; 
		}
		//$this->cart =& $cart; 
		
		
		//mobile handling: 
		if (!defined('OPC_DETECTED_DEVICE'))
		{
		   if (class_exists('OPCplugin'))
		    {
			  OPCplugin::detectMobile(); 
			}
		}
		
		$htmlExtra = array(); 
		$isexpress = OPCloader::isExpress($cart, $htmlExtra); 
	

	    /*
		if ($isexpress === 5)
		{
			
		   $cart->virtuemart_paymentmethod_id = $saved_p; 
		   $cart->virtuemart_shipmentmethod_id = $saved_s; 

		    $cart->virtuemart_paymentmethod_id = 6; 			
			return $this->amazon($cart, $tpl); 
		}
		*/
		
		
		
		
		if (!isset($cart->savedST))
		if (!empty($cart->ST))
		if ($cart->STsameAsBT === 0)
		  {
				$cart->savedST = $cart->ST; 
		  }

		if (!isset($cart->savedBT))
		if (!empty($cart->BT))
		if ($cart->STsameAsBT === 0)
		  {
				$cart->savedBT = $cart->BT; 
		  }


		  
		if (!isset($cart->pricesUnformatted['billTotal']))
		{
	  
		$vm15 = false;
		$cart->virtuemart_shipmentmethod_id = 0; 
		$cart->pricesUnformatted = $OPCloader->getCheckoutPrices($cart, false, $vm15); 
	  
		}

		
		
		//vm2.015+
		if (method_exists($calc, 'setCartPrices')) 
		if (function_exists('ReflectionObject'))
		{
		$reflection  = new ReflectionObject($calc);
		$prop = $reflection->getProperty('_cartData');
		// prevent vm2.0.18a
		if (!$prop->isPrivate())
		 {
		 
		if (!isset($calc->_cartData['VatTax']))
		{
		 $calc->_cartData['VatTax'] = array(); 
		}
		if (!isset($calc->_cartData['taxRulesBill']))
		{
		 $calc->_cartData['taxRulesBill'] = array(); 
		}
		}
		}
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) 
		{
		  if (!isset($calc->_cart)) $calc->_cart =& $this->cart;
		  
		  $this->setUpUserList(); 
		  
		}
		
		
		
		
		
	// since vm2.0.21a we need to load the language files here

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
		
	
	OPCLang::loadLang(); 
		
		
	    // opc reset defaults
		$session = JFactory::getSession(); 
	    $rand = uniqid('', true); 
        $session->set('opcuniq', $rand);
        $session->set($rand, '0');

		
	
		$mainframe = JFactory::getApplication('site');
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();

		
		
		$product = JRequest::getVar('virtuemart_product_id', null); 
		$quantity = JRequest::getVar('quantity', null); 
		
		$invalidlayout = false; 
		// extra layouts here
		if (($layoutName == 'blog') || ($layoutName == 'category') || ($layoutName == 'product') || ($layoutName == 'order') )
		{
			$layoutName = 'default'; 
			JRequest::setVar('layout', 'default'); 
			$invalidlayout = true; 
		}
		 $task = JRequest::getVar('task', null); 
		 if ($task == 'emptycart')
		  {
		     $cart->emptyCart(); 
		  }
		// fix add to cart on broken scripts
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		if (is_array($product))
		 {
		   
			if (empty($task) || ($invalidlayout))
			 {
					$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
					if ($cart->add($virtuemart_product_ids,$success))
					{
					 //$msg = JText::_('COM_VIRTUEMART_PRODUCT_ADDED_SUCCESSFULLY');
					 //$mainframe->enqueueMessage($msg);
					}
			 }
		 }
		 
		
		
		
		
		$this->assignRef('layoutName', $layoutName);
		$format = JRequest::getWord('format');
		// if(!class_exists('virtueMartModelCart')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'cart.php');
		// $model = new VirtueMartModelCart;

		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		// was till 2.0.126: $cart = VirtueMartCart::getCart(false, true);
		
		// do not allow update of shipment or payment from user object: 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$userModel = OPCmini::getModel('user');
		if (method_exists($userModel, 'getCurrentUser'))
		{
		$user = $userModel->getCurrentUser();
		if (!empty($user->virtuemart_shipmentmethod_id))
		{
			
		if (!defined('VM_VERSION'))
		{
		$user->virtuemart_shipmentmethod_id = 0; 
		$user->virtuemart_paymentmethod_id = 0; 
		}
	    }
		}
		
		if (!isset($cart->vendorId))
	    {
	     $cart->vendorId = 1; 
	    }
		
		
		

		
		
		
			$VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
			
			$exhtml = $OPCloader->addtocartaslink($this); 
			if (empty($exhtml)) $exhtml = ''; 

		$cart = OPCmini::getCart(); 
			
		$this->user = $OPCloader->getUser($cart); 
		
		
		//Why is this here, when we have view.raw.php
		if ($format == 'raw') {
		    if (method_exists($cart, 'prepareCartViewData')) {
				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
				$cart = OPCmini::getCart(); 
				
				ob_start(); 
				$cart->prepareCartViewData();
				ob_get_clean(); 
			}
			JRequest::setVar('layout', 'mini_cart');
			$this->setLayout('mini_cart');
			$this->prepareContinueLink();
		}
		$opclayouts = array('select_payment', 'select_shipment', 'default');
		/*
	  if($layoutName=='edit_coupon'){

		$cart->prepareCartViewData();
		$this->lSelectCoupon();
		$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'),JRoute::_('index.php?option=com_virtuemart&view=cart'));
		$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_SELECTCOUPON'));
		$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_SELECTCOUPON'));

		} else */
		
		$this->assignRef('cart', $cart);
		$useSSL = VmConfig::get('useSSL', 0);
		$useXHTML = true;
		$this->assignRef('useSSL', $useSSL);
		$this->assignRef('useXHTML', $useXHTML);
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
		 $customfieldsModel = VmModel::getModel ('Customfields');
		 $this->assignRef('customfieldsModel',$customfieldsModel);
		 
		
		  
		}
		
		if (!class_exists ('CurrencyDisplay'))
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
		
		if (!empty($cart) && (!empty($cart->pricesCurrency)))
			{
			$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
			$this->currencyDisplay = $currencyDisplay; 
			$this->assignRef('currencyDisplay', $currencyDisplay); 
			}
			else
			{
			$currencyDisplay = CurrencyDisplay::getInstance();
			$this->currencyDisplay = $currencyDisplay; 
			$this->assignRef('currencyDisplay', $currencyDisplay); 
			}
		
		if ($layoutName == 'order_done') {

			$language = JFactory::getLanguage();
			//$language->load('com_virtuemart', JPATH_SITE);

			$this->lOrderDone();

			$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
			$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
		} 
		else 
		if (in_array($layoutName, $opclayouts))
		{
			if ($layoutName == 'select_shipment') {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			}

			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart'));
			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
			//$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
		

			/* Load the cart helper */
			//			$cartModel = $this->getModel('cart');

			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart'));
			//$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
			//$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
		
			$cart->virtuemart_shipmentmethod_id = 0; 
			if (empty($isexpress))
			$cart->virtuemart_paymentmethod_id = 0; 
			$cart->setCartIntoSession();
			if (method_exists($calc, 'setCartPrices')) 
			{
			$calc->setCartPrices(array()); 
			}
			$vm15 = false;
			//$calc->getCheckoutPrices($cart, false); 
			$OPCloader->getCheckoutPrices($cart, false, $vm15); 
			
			if (!defined('VM_VERSION') || (VM_VERSION < 3))
			if (method_exists($cart, 'prepareAjaxData'));
		    $data = $cart->prepareAjaxData(false);

			if (method_exists($cart, 'prepareCartViewData')) {
				ob_start(); 
				$cart->prepareCartViewData();
			    ob_get_clean(); 
			}
			if (method_exists($cart, 'prepareAddressRadioSelection'))
			$cart->prepareAddressRadioSelection();

			//$this->prepareContinueLink();
			$this->lSelectCoupon();
			
			
			$totalInPaymentCurrency =$this->getTotalInPaymentCurrency();
			if ($cart->getDataValidated()) {
				$pathway->addItem(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$document->setTitle(OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$text = OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
				$checkout_task = 'confirm';
			} else {
				$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$text = OPCLang::_('COM_VIRTUEMART_CHECKOUT_TITLE');
				$checkout_task = 'checkout';
			}
			
			$this->assignRef('checkout_task', $checkout_task);
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			if ($cart->virtuemart_shipmentmethod_id) {
			    $change_s = OPCLang::_('COM_VIRTUEMART_CART_CHANGE_SHIPPING'); 
				$this->assignRef('select_shipment_text', $change_s );
			} else {
			    $edit_shipping = OPCLang::_('COM_VIRTUEMART_CART_EDIT_SHIPPING'); 
				$this->assignRef('select_shipment_text', $edit_shipping);
			}
			if ($cart->virtuemart_paymentmethod_id) {
			    $change_p = OPCLang::_('COM_VIRTUEMART_CART_CHANGE_PAYMENT'); 
				$this->assignRef('select_payment_text', $change_p);
			} else {
			    $edit_p = OPCLang::_('COM_VIRTUEMART_CART_EDIT_PAYMENT'); 
				$this->assignRef('select_payment_text', $edit_p);
			}
			
			
			
			if (!VmConfig::get('use_as_catalog')) {
				$checkout_link_html = '<a class="vm-button-correct" href="javascript:document.checkoutForm.submit();" ><span>' . $text . '</span></a>';
			} else {
				$checkout_link_html = '';
			}
			$this->assignRef('checkout_link_html', $checkout_link_html);
		}
		else
		{
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			{
				return $this->amazon($cart, $tpl); 
			}
		}
		//dump ($cart,'cart');
		
		$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
 
		// @max: quicknirty
		$cart->setCartIntoSession();
		if (method_exists('shopFunctionsF', 'setVmTemplate'))
		shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
		
		// 		vmdebug('my cart',$cart);
		if (($layoutName == 'default') || ($layoutName == 'select_shipment') || ($layoutName=='select_payment')) 
		{
		 
		// $this->lSelectShipment();
		// $this->lSelectPayment();
		 

		
		 $this->prepareVendor($cart);
		 $only_page = JRequest::getCmd('only_page', ''); 
		 $inside = JRequest::getCmd('insideiframe', ''); 
		 
		$url = JURI::base(true); 
		if (empty($url)) $url = '/'; 
		if (substr($url, strlen($url)-1)!=='/') $url .= '/'; 

		 
		 if (!empty($only_page) && (empty($inside))) 
		 {
		   echo '<iframe id="opciframe" src="'.JRoute::_($url.'index.php?option=com_virtuemart&view=cart&insideiframe=1&template=system').'" style="width: 100%; height: 2000px; margin:0; padding:0; border: 0 none;" ></iframe>'; 
		 }
		 if ($inside)
		 {
			$document->addStyleDeclaration('
			  body { width:95% !important; }
			  div#dockcart { display: none !important; }
			  '); 
		 }
		 $document->addStyleDeclaration('
			  
			  div#dockcart { display: none !important; }
			  '); 
		 @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		 @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		 
		
		 
		 $opc_was_loaded = true; 
		 
		 //$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart')); 
		 if (empty($only_page) || (!empty($inside)))
		 {
		 $this->renderOnepage($cart, $exhtml, $isexpress, $OPCloader, $htmlExtra);
		 $this->printDebugMsgs(); 
		 }
		 
		 $cart->virtuemart_shipmentmethod_id = 0; 
		 if (empty($isexpress))
		 $cart->virtuemart_paymentmethod_id = 0; 
		 $cart->setCartIntoSession();
		 
		}
		else		
		{
		if (method_exists('shopFunctionsF', 'setVmTemplate'))
		shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
		parent::display($tpl);
		}
		
		  if (class_exists('vmJsApi'))
		  if (method_exists('vmJsApi', 'writeJS'))
		  $ret = vmJsApi::writeJS(); 
	      if (!empty($ret) && (is_string($ret)))
		  {
			  echo $ret; 
		  }
	  
	  
		$cart->virtuemart_paymentmethod_id = $saved_p; 
		$cart->virtuemart_shipmentmethod_id = $saved_s;
		$cart->setCartIntoSession();
		
		
		
		
	}
	var $pointAddress = false;
	
	private function printDebugMsgs() {
	  if (!class_exists('OPCconfig')) return; 
	  $doc = JFactory::getDocument(); 
	  $c = strtolower(get_class($doc)); 
	  
	  if (method_exists($doc, 'getType')) {
			$type = $doc->getType(); 
			if ($type !== 'html') {
			   return;
			}
			else {
				if ($c !== 'jdocumenthtml') return; 
			}
	  }
	  
	  $d = OPCconfig::get('opc_debug', false); 
	  if (empty($d)) return; 
	  
	  if (class_exists('OPCloader')) {
	   if (!empty(OPCloader::$debugMsg)) {
		   //php5.3 as far as it's using latest json: 
		   if (!defined('JSON_PRETTY_PRINT')) define('JSON_PRETTY_PRINT', 128); 
		   
		     if (!empty(OPCloader::$debug_disabled)) return; 
			 $json = json_encode(array('debug' => OPCloader::$debugMsg), JSON_PRETTY_PRINT); 
			 $jsc = '  '."\n \n if (typeof Onepage != 'undefined') { var opc_debug_msgs = ".$json."; Onepage.op_log(opc_debug_msgs); } "; 
			 $js = "<script type=\"text/javascript\">\n"; 
			 $js .= "//<![CDATA[\n";
			 $js .= $jsc."\n";
			 $js .= "//]]>\n";
			 $js .= "</script>\n"; 

   echo $js; 
			 
	   
	   }
	  }
	}
	public function renderOrderDone($tpl) {
			$mainframe = JFactory::getApplication('site');
			$pathway = $mainframe->getPathway();
			$document = JFactory::getDocument();
			$this->lOrderDone();
			
			$pathway->addItem(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
			$document->setTitle(OPCLang::_('COM_VIRTUEMART_CART_THANKYOU'));
			$layoutName = 'order_done'; 
			if (method_exists('shopFunctionsF', 'setVmTemplate'))
			shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
			parent::display($tpl);
			
		  if (class_exists('vmJsApi'))
		  if (method_exists('vmJsApi', 'writeJS'))
		  $ret = vmJsApi::writeJS(); 
	      if (!empty($ret) && (is_string($ret)))
		  {
			  echo $ret; 
		  }
	}
	public function amazon(&$cart, $tpl=null)
	{
			


	


		$app = JFactory::getApplication('site');

		//$this->prepareContinueLink();
		if (VmConfig::get('use_as_catalog',0)) {
			vmInfo('This is a catalogue, you cannot access the cart');
			$app->redirect($this->continue_link);
		}

		$pathway = $app->getPathway();
		$document = JFactory::getDocument();
		$document->setMetaData('robots','NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET');

		$this->layoutName = $this->getLayout();
		if (!$this->layoutName) $this->layoutName = vRequest::getCmd('layout', 'default');

		$format = vRequest::getCmd('format');

		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
	    if (!isset($this->cart))
		$this->cart = VirtueMartCart::getCart();

		$this->cart->prepareVendor();

		
		
		
		//Why is this here, when we have view.raw.php
		if ($format == 'raw') {
			vRequest::setVar('layout', 'mini_cart');
			$this->setLayout('mini_cart');
			$this->prepareContinueLink();
		}

		if (VmConfig::get('oncheckout_opc', 1)) {
				if (!class_exists('vmPSPlugin')) 
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'. DIRECTORY_SEPARATOR . 'vmpsplugin.php');	
				
				JPluginHelper::importPlugin('vmshipment');
				JPluginHelper::importPlugin('vmpayment');
				
				if((!$this->lSelectPayment()) || (!$this->lSelectShipment() )) {
					
					$this->pointAddress = true;
				}
		
		}
		
		if ($this->layoutName == 'select_shipment') {
			if (method_exists($this->cart, 'prepareCartData')) {
				ob_start(); 
				$this->cart->prepareCartData();
				ob_get_clean(); 
			}
			$this->lSelectShipment();

			$pathway->addItem(vmText::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));
			$pathway->addItem(vmText::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
			$document->setTitle(vmText::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
		} else if ($this->layoutName == 'select_payment') {

			if (method_exists($this->cart, 'prepareCartData')) {
				ob_start(); 
				$this->cart->prepareCartData();
				ob_get_clean(); 
			}
			$this->lSelectPayment();

			$pathway->addItem(vmText::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));
			$pathway->addItem(vmText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
			$document->setTitle(vmText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
		} else if ($this->layoutName == 'order_done') {
			VmConfig::loadJLang( 'com_virtuemart_shoppers', true );
			$this->lOrderDone();

			$pathway->addItem( vmText::_( 'COM_VIRTUEMART_CART_THANKYOU' ) );
			$document->setTitle( vmText::_( 'COM_VIRTUEMART_CART_THANKYOU' ) );
		} else {
			VmConfig::loadJLang('com_virtuemart_shoppers', true);

			$this->renderCompleteAddressList();

			if (!class_exists ('VirtueMartModelUserfields')) {
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'userfields.php');
			}

			$userFieldsModel = VmModel::getModel ('userfields');

			$userFieldsCart = $userFieldsModel->getUserFields(
				'cart'
				, array('captcha' => true, 'delimiters' => true) // Ignore these types
				, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);

			$this->userFieldsCart = $userFieldsModel->getUserFieldsFilled(
				$userFieldsCart
				,$this->cart->cartfields
			);

			if (!class_exists ('CurrencyDisplay'))
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');

			$this->currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);

			$customfieldsModel = VmModel::getModel ('Customfields');
			$this->assignRef('customfieldsModel',$customfieldsModel);

			$this->lSelectCoupon();

			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();

			$checkoutAdvertise =$this->getCheckoutAdvertise();

			if ($this->cart->getDataValidated()) {
				if($this->cart->_inConfirm){
					$pathway->addItem(vmText::_('COM_VIRTUEMART_CANCEL_CONFIRM_MNU'));
					$document->setTitle(vmText::_('COM_VIRTUEMART_CANCEL_CONFIRM_MNU'));
					$text = vmText::_('COM_VIRTUEMART_CANCEL_CONFIRM');
					$this->checkout_task = 'cancel';
				} else {
					$pathway->addItem(vmText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
					$document->setTitle(vmText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
					$text = vmText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
					$this->checkout_task = 'confirm';
				}
			} else {
				$pathway->addItem(vmText::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$document->setTitle(vmText::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$text = vmText::_('COM_VIRTUEMART_CHECKOUT_TITLE');
				$this->checkout_task = 'checkout';
			}
			$this->checkout_link_html = '<button type="submit"  id="checkoutFormSubmit" name="'.$this->checkout_task.'" value="1" class="vm-button-correct" ><span>' . $text . '</span> </button>';


			if (VmConfig::get('oncheckout_opc', 1)) {
				if (!class_exists('vmPSPlugin')) 
					require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'. DIRECTORY_SEPARATOR . 'vmpsplugin.php');
				
				JPluginHelper::importPlugin('vmshipment');
				JPluginHelper::importPlugin('vmpayment');
				//vmdebug('cart view oncheckout_opc ');
				if(!$this->lSelectShipment() or !$this->lSelectPayment()){
					vmInfo('COM_VIRTUEMART_CART_ENTER_ADDRESS_FIRST');
					$this->pointAddress = true;
				}
			} else {
				$this->checkPaymentMethodsConfigured();
				$this->checkShipmentMethodsConfigured();
			}

			if ($this->cart->virtuemart_shipmentmethod_id) {
				$shippingText =  vmText::_('COM_VIRTUEMART_CART_CHANGE_SHIPPING');
			} else {
				$shippingText = vmText::_('COM_VIRTUEMART_CART_EDIT_SHIPPING');
			}
			$this->assignRef('select_shipment_text', $shippingText);

			if ($this->cart->virtuemart_paymentmethod_id) {
				$paymentText = vmText::_('COM_VIRTUEMART_CART_CHANGE_PAYMENT');
			} else {
				$paymentText = vmText::_('COM_VIRTUEMART_CART_EDIT_PAYMENT');
			}
			$this->assignRef('select_payment_text', $paymentText);

			$this->cart->prepareAddressFieldsInCart();

			$this->layoutName = $this->cart->layout;
			if(empty($this->layoutName)) $this->layoutName = 'default';

			if ($this->cart->layoutPath) {
				if (method_exists($this, 'addTemplatePath')) {
				$this->addTemplatePath($this->cart->layoutPath);
				}
				else
				{
					if (method_exists($this, 'addIncludePath')) 
					{
					$this->addIncludePath( $this->cart->layoutPath );
					}
				}
			}

			if(!empty($this->layoutName) and $this->layoutName!='default'){
				$this->setLayout( strtolower( $this->layoutName ) );
			}
			//set order language
			$lang = JFactory::getLanguage();
			$order_language = $lang->getTag();
			$this->assignRef('order_language',$order_language);
			
			
		}

		

		$this->useSSL = VmConfig::get('useSSL', 0);
		$this->useXHTML = false;

		$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
		$this->assignRef('checkoutAdvertise', $checkoutAdvertise);


		//We never want that the cart is indexed
		$document->setMetaData('robots','NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET');

		if ($this->cart->_inConfirm) vmInfo('COM_VIRTUEMART_IN_CONFIRM');

		$current = JFactory::getUser();
		$this->allowChangeShopper = false;
		$this->adminID = false;
		if(VmConfig::get ('oncheckout_change_shopper')){
			if($current->authorise('core.admin', 'com_virtuemart') or $current->authorise('vm.user', 'com_virtuemart')){
				$this->allowChangeShopper = true;
			} else {
				$this->adminID = JFactory::getSession()->get('vmAdminID',false);
				if($this->adminID){
					if(!class_exists('vmCrypt'))
						require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmcrypt.php');
					$this->adminID = vmCrypt::decrypt($this->adminID);
					$adminIdUser = JFactory::getUser($this->adminID);
					if($adminIdUser->authorise('core.admin', 'com_virtuemart') or $adminIdUser->authorise('vm.user', 'com_virtuemart')){
						$this->allowChangeShopper = true;
					}
				}
			}
		}
		if($this->allowChangeShopper){
			$this->userList = $this->getUserList();
		}

		parent::display($tpl);
	

	}
	
	
	public function renderOnepage(&$cart, &$exhtml, $isexpress, &$OPCloader, &$htmlExtra, $startM=array())
	{
 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		// load selected theme: 
		$selected_template = OPCrenderer::getSelectedTemplate();  
		
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'removemsgs.php'); 




	   
	   $is_error = JRequest::getVar('error_redirect', false); 
	   $session = JFactory::getSession(); 
	   $opc_in_trigger = $session->get('opc_in_trigger', false); 
	   if (!empty($opc_in_trigger)) $is_error = true; 
       $task = JRequest::getVar('task', 'display'); 
	   if ($task !== 'display') $is_error = true; 
	   
	   if (empty($cart->couponCode)) 
	   {
		   
			     $session = JFactory::getSession(); 
				 $nc = $session->get('opc_last_coupon', ''); 
		
				 if (!empty($nc)) {
				    //only for awocoupons !
					$q = 'select `enabled` from #__extensions where `element` = "com_awocoupon" and `enabled` = 1'; 
					$db = JFactory::getDBO(); 
					$db->setQuery($q); 
					$r = $db->loadResult(); 
					
					 if (!empty($r))
					 {
				       $cart->couponCode = $nc; 
					  $opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon changed to '.$nc.' at '.__FILE__.':'.__LINE__); }
					  
					 }
				 }
				 
			 }
			 
			$store_coupon = $cart->couponCode; 
			if (!empty($store_coupon)) {
			  if (is_array($cart->_triesValidateCoupon)) {
				  $cart->_triesValidateCoupon[$store_coupon] = $store_coupon; 
			  }
			}
			 
	   
	  
		OPCloader::loadJavascriptFiles($this); 			
		define('OPC_CHECKOUT_RENDERED', 1); 
		
		/* include all cart files */
			   	if(!class_exists( 'VirtueMartControllerVirtuemart' )) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'virtuemart.php');

		$controller = new VirtueMartControllerVirtuemart();
		if (method_exists($controller, 'addViewPath'))
		$controller->addViewPath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views' );
		else
		if (method_exists($controller, 'addIncludePath'))
		$controller->addIncludePath( JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views' );
	
		$controllerClassName = 'VirtueMartControllerCart';
		if(!class_exists( $controllerClassName )) 
		require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'cart.php');
		
		
		
		if (method_exists($this, 'addTemplatePath'))
		$this->addTemplatePath( JPATH_VM_SITE.'/views/cart/tmpl' );
		else
		if (method_exists($this, 'addIncludePath')) 
		{
			$this->addIncludePath( JPATH_VM_SITE.'/views/cart/tmpl' );
		}

		$vmtemplate = VmConfig::get( 'vmtemplate', 'default' );
		if($vmtemplate == 'default') {
			if(JVM_VERSION >= 2) {
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery( $q );
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template) {
			if (method_exists($this, 'addTemplatePath')) {
			$this->addTemplatePath( JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart' );
			}
			else
			{
				if (method_exists($this, 'addIncludePath')) 
					$this->addIncludePath( JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart' );
			}
		} 
			


		/* end include */ 
		if (OPCloader::logged($cart))
			 {
			   	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
				$c = new VirtueMartControllerOpc(); 
				
				$c->setAddress($cart, true, false, true); 
				
			 }		
		
		 
		$cart_start = VirtueMartCart::getCart(false);
		
	$mainframe = JFactory::getApplication('site'); 
	$useSSL = VmConfig::get('useSSL', 0);
	
	
	$lang = OPCloader::getLangCode(); 
	
	$opc_hasowntheme = OPCconfig::get('opc_hasowntheme', false); 
	$customurl = ''; 
	if (!empty($opc_hasowntheme)) {
		$customurl = '&format=opchtml&tmpl=component'; 
	}
	
	if ((!empty($useSSL)) || (!empty($opc_hasowntheme)))	
	{
	
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) {

      $isHttps = true;
	}
	else $isHttps = false;
	
	$op_redirected = JFactory::getSession()->get('op_redirected', JRequest::getVar('op_redirected', false)); 
    //$lang = JRequest::getVar('lang', ''); 
	
	
	
	
	if 	(((empty($isHttps))) || ($opc_hasowntheme))
	if (empty($op_redirected))
	{
		$urlh = 'index.php?option=com_virtuemart&view=cart'.$customurl; 
		
		$urlh .= '&op_redirected=1'; 
		
		if (!empty($lang))
		$urlh .= '&lang='.$lang; 
		
		$add_id = JRequest::getVar('add_id', array()); 
		if (!empty($add_id))
		 {
		   if (is_array($add_id))
		   {
		   foreach ($add_id as $key=>$val)
		     {
			    $urlh .= '&add_id[]='.$val; 
				$q = JRequest::getVar('qadd_'.$val); 
				if (!empty($q))
				 {
				   $urlh .= '&qadd_'.$val.'='.$q; 
				 }
			 }
		   }
		   else 
		    {
			  $urlh .= '&add_id='.$add_id; 
			  $q = JRequest::getVar('qadd'); 
			  if (!empty($q)) $urlh .= 'quadd='.$q; 
			}
		 }
		$theme = JRequest::getVar('opc_theme'); 
		
		if (!empty($theme))
		$urlh .= '&opc_theme='.$theme; 
		
		$newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
		if (!empty($newitemid)) {
			$urlh .= '&Itemid='.$newitemid; 
		}
		if ((!empty($useSSL)) && (empty($isHttps))) {
		  $url = JRoute::_($urlh, false, true);
		}
		else {
			$url = JRoute::_($urlh, false); 
		}
		JFactory::getSession()->set('op_redirected', true); 
		$mainframe->redirect($url);
		$mainframe->close(); 
	}
	
	JFactory::getSession()->clear('op_redirected'); 
	
	}
	
	
	
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'basket.php'); 

    require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
	
	    $language = JFactory::getLanguage();
		//$language->load('com_onepage', JPATH_SITE, 'en-GB', true);
		//$language->load('com_onepage', JPATH_SITE, null, true);
		
		
	    //require_once(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'version.php'); 
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');  
		//$x = version_compare(vmVersion::$RELEASE, '2.0.0', '>'); 
	    
		// in j1.7+ we have a special case of a guest login where user is logged in (and does not know it) and the registration fields in VM don't show up
			
		   //here we decide if to unlog user before purchase if he is somehow logged
		   include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 

		
		 
		   if (!empty($opc_calc_cache))
		   {
			 require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
		     OPCcache::installCache(); 
		   }
		   
		   if ($unlog_all_shoppers)
		   {
		     	$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				if (!empty($uid))
				 {
				   
				  
				   $mainframe->logout(); 
				 }

		   }
		   
		   
		   $newitemid = OPCconfig::getValue('opc_config', 'newitemid', 0, 0, true); 
		   
	       if (!empty($newitemid))
		   {
		     if (is_numeric($newitemid))
			  {
			   JRequest::setVar('Itemid', $newitemid); 
			   $GLOBALS['Itemid'] = $newitemid; 
			  }
		   }
		  
		  $op_disable_shipto = OPCloader::getShiptoEnabled($this->cart); 
		 
        
		  
			
			
			$OPCloader->addtocartaslink($this); 
			$continue_link = $OPCloader->getContinueLink($this); 
		    if (empty($cart) || (empty($cart->products)))
			{
			  OPCremoveMsgs::removeMsgs($cart); 
				
			  $tpla = array('continue_link' => $continue_link); 
			  $cart->couponCode = ''; 
			  $opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon cleared at '.__FILE__.':'.__LINE__); }
			  
			}
			else
			{
			
			// we will force this plugins to load for tracking and similar: 
			$advertises = $this->getCheckoutAdvertise(); 
	
			if (!empty($hide_advertise))
			$advertises = array(); 

		
			if (empty($ajaxify_cart))
			$advertises2 = $OPCloader->getAdminTools($this); 
			if (!empty($advertises2)) $advertises[] = $advertises2; 

			
			$VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
		
		 
			
				//$advertises[] = '<div class="payments-signin-button"></div>'; 
			


			//if (empty($ajaxify_cart))
			$op_coupon = $OPCloader->getCoupon($this);
			//else $op_coupon = ''; 
			
			$min_reached = OPCloader::checkPurchaseValue($cart); 
			
		    
			
			$op_userfields = $OPCloader->getBTfields($this); 
			$op_userfields_cart = $OPCloader->getCartfields($this); 
			
			$has_own_cart_postion_cart_fields = OPCconfig::get('has_own_cart_postion_cart_fields', false); 
			
			if (empty($has_own_cart_postion_cart_fields)) {
			  $op_userfields .= $op_userfields_cart; 
			}
			
			$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
			
			if (empty($op_disable_shipping))
			{
			if (!$this->checkShipmentMethodsConfigured()) 
			{
			  $no_shipping = true; 
			}
			else
			{
				
			 $no_shipping = $op_disable_shipping; 
			}
			}
			else
			{
			$no_shipping = 1; 
			}
			//FIX BROKEN PLUGINS
			if (!empty($cart->BT))
			$storedBT2 = (array)$cart->BT; 
			if (!empty($cart->ST))
			$storedST2 = (array)$cart->ST; 
		    if (!empty($cart->RD))
			$storedRD2 = (array)$cart->RD; 
			//FIX BROKEN PLUGINS
			
			$OPCloader->prepareMethods($cart, $no_shipping); 
			
			
			$num = 0; 
			if (empty($cart->BT['virtuemart_country_id']))
			{
				$bhelper = new basketHelper;	
				basketHelper::createDefaultAddress($this, $this->cart); 	
				$op_payment_a = $OPCloader->getPayment($this, $num, false, $isexpress); 
				$op_payment = $op_payment_a['html']; 
				basketHelper::restoreDefaultAddress($this, $this->cart);
			}
			else
			{
			
			 if (!class_exists ('calculationHelper')) {
			 require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');
			 }
			 
			 /*
			 $calc = calculationHelper::getInstance ();
			 if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
			 else $vm2015 = false; 
			 
			 OPCloader::getCheckoutPrices(  $cart, false, $vm2015, 'opc');
			 */
			 $op_payment_a = $OPCloader->getPayment($this, $num, false, $isexpress); 
			 $op_payment = $op_payment_a['html']; 
			}
			
			//FIX BROKEN PLUGINS
			if (isset($storedBT2))
			$cart->BT = $storedBT2; 
			if (isset($storedST2))
			$cart->ST = (array)$storedST2;
		    if (isset($storedRD2))
		    $cart->RD = (array)$storedRD2;
			//FIX BROKEN PLUGINS
			
			
			
			
			$op_payment = '<div id="payment_html">'.$op_payment.'</div>'; 
			
			if (isset($cart->BT['name']))
			$name = $cart->BT['name']; 
			
			//FIX BROKEN PLUGINS
			if (!empty($cart->BT))
			$storedBT = (array)$cart->BT; 
			if (!empty($cart->ST))
			$storedST = (array)$cart->ST; 
		    if (!empty($cart->RD))
			$storedRD = (array)$cart->RD; 
			//FIX BROKEN PLUGINS
			
			$shipping_method_html = $OPCloader->getShipping($this, $cart, false); 
			
			//FIX BROKEN PLUGINS
			if (isset($storedBT))
			$cart->BT = $storedBT; 
			if (isset($storedST))
			$cart->ST = (array)$storedST;
		    if (isset($storedRD))
		    $cart->RD = (array)$storedRD;
			//FIX BROKEN PLUGINS
			
			if (!empty($name))
			if (empty($cart->BT['name'])) $cart->BT['name'] = $name; 

		
			
			OPCrenderer::registerVar('shipping_method_html', $shipping_method_html); 
			OPCrenderer::registerVar('op_payment', $op_payment); 
			
			$OPCloader->registerCurrency($cart); 
			

			
		    $op_basket = OPCBasket::getBasket($this, $OPCloader, true, $op_coupon, $shipping_method_html, $op_payment, $isexpress); 

			//$op_basket = $OPCloader->getBasket($this, true, $op_coupon, $shipping_method_html, $op_payment, $isexpress); 
			
			
			$hascoupon = stripos($op_basket, $op_coupon); 
			
			if ($hascoupon) $op_coupon = '';  
			
			if (empty($ajaxify_cart))
			{
			if ($shipping_inside_basket)
			{
			  $shipping_method_html = '<input type="hidden" name="virtuemart_shipmentmethod_id" value="" id="new_shipping" />'; 
			}
			if (($payment_inside_basket) && (empty($isexpress)))
			{
			  $op_payment = '<input type="hidden" name="virtuemart_paymentmethod_id" value="" id="new_payment" />'; 
			}
			}
			else
			{
				if (($shipping_inside_basket))
				$shipping_method_html = ''; 
				
				if ($payment_inside_basket)
			    $op_payment = ''; 
			}
			  $op_payment .= '<div id="payment_extra_outside_basket">'; 
			  if (!empty($op_payment_a['extra']))
			   { 
			     //foreach ($op_payment_a['extra'] as $key=>$hp)
				  {
				  
				    foreach ($op_payment_a['extra'] as $ht)
				    $op_payment .= $ht; 
					
					
				  }
			   }
			   $op_payment .= '</div>'; 
			  
			
			
			
		
			
			
			
			
			if (empty($no_login_in_template))
			$registration_html = $OPCloader->getRegistrationHhtml($this);
			else $registration_html = ''; 
			
			
			
			$jsvalidator = $OPCloader->getJSValidatorScript($this); 
			
			$return_url = $OPCloader->getReturnLink($this); 
			
			
			
			
			
			
			
			
			if (((!empty($hide_payment_if_one) && ($num === 1)) || (($payment_inside_basket))) || (!empty($isexpress)))
			$force_hide_payment = true; 
			else $force_hide_payment = false; 
			
			
			
			
			$op_shipto = $OPCloader->getSTfields($this); 
			
			$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
			
			$third_address = ''; 
			if (!empty($render_in_third_address)) {
			   $third_address = $OPCloader->getThirdAddress($this); 
			}
			
			$op_formvars = OPCloader::getFormVars($this).$jsvalidator;
			
			
			$op_userfields .= $op_formvars.$exhtml; 
			
			
			

			//$action_url = $OPCloader->getActionUrl($this); 
			
			//   if ($onlyindex) return JURI::root(true).'/index.php'; 
			$action_url = JURI::root(true).'/index.php?option=com_virtuemart&amp;view=opc&amp;controller=opc&amp;task=checkout&amp;nosef=1';
			
			$p_id = 0; 
			if (count($cart->products) === 1)
			{
				$first = reset($cart->products); 
				$p_id = (int)$first->virtuemart_product_id; 
			}
			//   if ($onlyindex) return JURI::root(true).'/index.php'; 
			$action_url = JURI::root(true).'/index.php?option=com_virtuemart&amp;view=opc&amp;controller=opc&amp;task=checkout&amp;nosef=1';
			
			$p_ty = OPCconfig::get('product_id_ty', false); 
			if (empty($p_ty))
			if (!empty($p_id)) {
				$action_url .= '&amp;virtuemart_product_id='.(int)$p_id; 
			}
			
			
			
			
			
			if (!empty($lang))
		    $action_url .= '&amp;lang='.$lang; 
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		    $op_customitemidty = OPCconfig::getValue('opc_config', 'op_customitemidty', 0, 0, true); 
			
			if (!empty($op_customitemidty))
			$action_url .= '&Itemid='.$op_customitemidty; 
			
			$OPCloader->getJavascript($this, $isexpress, $action_url); 
		    
			
			$this->assignRef('action_url', $action_url); 
			
			$captcha = $OPCloader->getCaptcha($this); 

			$OPCloader->getMainJs(); 
			
			
			
			$op_login_t = ''; 
			$html_in_between = $OPCloader->getHtmlInBetween($this);
			
			//$op_userfields = ''; 
			
			$shippingtxt = ''; 
			$chkship = ''; 
			
			
			
			$tos_required = $OPCloader->getTosRequired($this); 
			
			$op_tos = ''; 
			$extras = $OPCloader->getExtras($this); 
			$tos_con = $OPCloader->getTos($this); 
			$agreement_txt = ''; 
			$show_full_tos = $OPCloader->getShowFullTos($this); //VmConfig::get('oncheckout_show_legal_info', 0); 
			
			$agree_checked = intval(!$agreed_notchecked); 
			$intro_article = $OPCloader->getIntroArticle($this); 
		    
			$delivery_date = $OPCloader->showDeliveryDate($this); 

			$italian_checkbox = $OPCloader->getItalianCheckbox($this); 
		    $subscription_checkbox = $OPCloader->getSubscriptionCheckbox($this); 
			
			// 202 transform old themes; 
			$op_shipto = str_replace('"showSA', '"Onepage.showSA', $op_shipto); 
			$op_shipto = str_replace('"showSA', '"Onepage.showSA', $op_shipto); 
			$op_shipto = str_replace(' op_unhide', ' Onepage.op_unhide', $op_shipto); 
			 
			 
			 
			$cart_rendered = false; 
			if (stripos($this->cart->layoutPath, DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR)!==false)
			{
				$f = $this->cart->layoutPath.DIRECTORY_SEPARATOR.$this->cart->layout.'.php';

				$true = true; 
			   $this->assignRef('found_shipment_method',$true);
			   $this->assignRef('shipping_method_html', $shipping_method_html); 
			   
			   
			   //$this->lSelectShipment(true); 
			   $f2 = str_replace(DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'payment'.DIRECTORY_SEPARATOR, $this->cart->layoutPath); 
			   $f2 = str_replace(DIRECTORY_SEPARATOR.'tmpl', '', $f2); 
			   
			   
			   //$this->cart->layoutPath
			   
			   if (file_exists($f))
			   {
				   if (isset($cart->custom_payment_id))
				   $this->cart->virtuemart_paymentmethod_id = $this->cart->custom_payment_id; 
			       $this->basket = $op_basket; 
				   $this->assignRef('basket', $op_basket); 
				   
				   if (method_exists($this, 'addTemplatePath'))
				   $this->addTemplatePath($this->cart->layoutPath);
				    else
				   if (method_exists($this, 'addIncludePath')) 
					$this->addIncludePath( $this->cart->layoutPath );
			   
				   $f2ok = false; 
				   if (file_exists($f2)) 
				   {
					   if (method_exists($this, 'addTemplatePath'))
					   {
					   $this->addTemplatePath($f2);
					   }
				   else
				   {
		if (method_exists($this, 'addIncludePath')) 
		{
			$this->addIncludePath($f2 );
		}
				   }
					   

						if (file_exists($f2.DIRECTORY_SEPARATOR.$this->cart->layout.'.php'))
						{
						$f2ok = true;	
						}
				   }
				   
				   $this->setLayout( strtolower( $this->cart->layout ) );
				   $cart_rendered = true; 
				   if (empty($f2ok))
				   include($f); 
			       else
				   include($f2.DIRECTORY_SEPARATOR.$this->cart->layout.'.php'); 
			   }

			}

			
			 
			 
			 
			$google_checkout_button = ''; 
			$paypal_express_button = ''; 
			$related_products = ''; 
			$onsubmit = $OPCloader->getJSValidator($this);
			$op_onclick = $onsubmit; 
			$ref = $this;
			$tos_link = $OPCloader->getTosLink($this); 
			$tpla = Array(
			"shipping_estimator" => '',
			"force_hide_payment" => $force_hide_payment, 
			"hide_payment" => $force_hide_payment,
			"min_reached_text" => $min_reached,
			"checkoutAdvertises" => $advertises, 
			"intro_article" => $intro_article, 
			"return_url" => $return_url, 
			"captcha" 	=> $captcha, 
			"delivery_date" => $delivery_date, 
			"no_shipping" => $no_shipping,
			"op_onclick" => ' onclick="'.$onsubmit.'" ', 
			"no_shipto" => NO_SHIPTO, 
			'op_userfields_cart'=>$op_userfields_cart,
			"action_url" => $action_url,
			"tos_required" => $tos_required,
			"op_userinfo_st" => "",
            "op_basket" => $op_basket,
            "op_coupon" => $op_coupon, 
            "html_in_between" => $html_in_between, 
            "continue_link" => $continue_link, 
            "op_login_t" => $op_login_t,
            "shipping_method_html" => $shipping_method_html.JHtml::_('form.token'),
            "op_userfields" => $op_userfields,
            "shippingtxt" => $shippingtxt,
            "chkship" => $chkship,
            "op_shipto" => $op_shipto,
			"third_address" => $third_address,
            "op_tos" => $op_tos,
             "op_payment" => $op_payment.JHtml::_('form.token'),
             "tos_con" => $tos_con, 
             "agreement_txt" => $agreement_txt,
             "show_full_tos" => $show_full_tos,
             "google_checkout_button" => $google_checkout_button,
             "paypal_express_button" => $paypal_express_button,
             "related_products" => $related_products, 
             "registration_html" => $registration_html,
			 "onsubmit" => $onsubmit,
			 "tos_link" => $tos_link,
			 "checkbox_products" => '',
			 'italian_checkbox' => $subscription_checkbox.$italian_checkbox, 
			 'privacy_checkbox' => $italian_checkbox,
			 'subscription_checkbox' => $subscription_checkbox, 
			 'acymailing_checkbox' => $subscription_checkbox, 
          
           ) ;
			}

			
			
			OPCrenderer::addModules($this, $tpla); 
			
			if (empty($extras)) $extras = ''; 
			
			ob_start(); 
			include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'legacy_templates.php');  
			$hhh = ob_get_clean(); 
			
			OPCrenderer::parseTemplate($hhh); 
			
			
			
			if (empty($cart_rendered))
			{
				
				
				if (!empty($opc_hasowntheme)) {
					
				  $renderer = OPCrenderer::getInstance(); 
				  $root = OPCloader::getUrl(); 
				   $selected_template = OPCrenderer::getSelectedTemplate(); 
				  $templateurl = $root.'components/com_onepage/themes/'.$selected_template.'/'; 
				  echo $renderer->fetch($this, 'index', array('html'=>$hhh, 'root'=>$root, 'templateurl'=>$templateurl, 'extras'=>$extras)); 
				}
				else {
				  echo $hhh; 
				  
				}
			}
			if (empty($opc_hasowntheme))
			if (!empty($extras)) echo $extras; 
			
			// will not be included inside form
			
			// newScript.onload=scriptLoaded;
			
		    $cart = VirtueMartCart::getCart(false);
			$calc = calculationHelper::getInstance();
			if (method_exists($calc, 'setCartPrices')) 
			{
			$calc->setCartPrices(array()); 
			}
			
				
			
			
			
			$store_coupon = $cart->couponCode; 
			if (!empty($store_coupon)) {
			  if (is_array($cart->_triesValidateCoupon)) {
				  $cart->_triesValidateCoupon[$store_coupon] = $store_coupon; 
			  }
			}
			
			
			if (method_exists($cart, 'prepareAjaxData'));
		    $data = $cart->prepareAjaxData(false);
			
			
			
			$cart->couponCode = $store_coupon; 
			
			
		
			
			
			// disable execution of other plugins after rendering: 
			//JRequest::setVar('view', 'opccart'); 
			//JRequest::setVar('controller', 'opc'); 
			
	//register cart: 
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('registerCartEnter', array());  
	
	
			
	
	  
		
		//is_error disables filter for messages
		$is_error = JRequest::getVar('error_redirect', false); 
		
		
	    
		
		$page = JRequest::getVar('task', ''); 
		$ina = array('editpayment', 'editshipment', 'pluginUserPaymentCancel', 'pluginresponsereceived', 'notify', 'setcoupon'); 
		if (in_array($page, $ina)) $is_error = true; 
		
		$session = JFactory::getSession(); 
		$opc_in_trigger = $session->get('opc_in_trigger', false); 
		if (!empty($opc_in_trigger)) $is_error = true; 
			
			
		if (!empty($cart->products) || (!empty($cart->cartProductsData)))
		if (empty($is_error)) {
			//filters standard opc filter msgs
			OPCremoveMsgs::removeMsgs($cart); 
			$startM3 = array(); 
			
			$startM2 = OPCremoveMsgs::prepareClearMsgs(true);  
			
			if (!empty($startM))
			OPCremoveMsgs::setMsgsInSession($startM); 
		    if (!empty($startM2))
			OPCremoveMsgs::setMsgsInSession($startM2); 
		    OPCremoveMsgs::clearMsgs($startM3);  
		}
		else {
			OPCremoveMsgs::makeUnique(); 
		}
		
		
	    $session = JFactory::getSession(); 
		$t = $session->get('opc_in_trigger', false); 
		
		$opc_in_trigger = $session->set('opc_in_trigger', false); 
		if (!empty($opc_in_trigger)) $is_error = true; 
	 
			
}
	/*
	 * Trigger to place Coupon, payment, shipment advertisement on the cart
	 */
	public function getCheckoutAdvertise() {
		$checkoutAdvertise=array();
		JPluginHelper::importPlugin('vmextended');
		JPluginHelper::importPlugin('vmcoupon');
		JPluginHelper::importPlugin('vmshipment');
		JPluginHelper::importPlugin('vmpayment');
		JPluginHelper::importPlugin('vmuserfield');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertiseOPC', array( $this->cart, &$checkoutAdvertise));
		
		
		if (empty($checkoutAdvertise)) $checkoutAdvertise = array(); 
		
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pluginhelper.php');
			OPCPluginHelper::triggerSystemPlugin('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertise));
		
		$checkoutAdvertise[] = '<div class="payments-signin-button" ></div>';
		if (method_exists('vmJsApi', 'writeJs')) {
		  $checkoutAdvertise[] = vmJsApi::writeJs(); 
		}
		return $checkoutAdvertise;
	}
	
	
	public function logged($cart)
	{
	  //$OPCloader = new OPCloader; 
	  return OPCloader::logged($cart); 
	}
	public function renderMailLayout($doVendor=false) {
		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');

		$cart = VirtueMartCart::getCart(false);
		$this->assignRef('cart', $cart);
		if (method_exists($cart, 'prepareCartViewData')) {
		ob_start(); 
		$cart->prepareCartViewData();
		$zz = ob_get_clean(); 
		}
		$cart->prepareMailData();

		if ($doVendor) {
			$this->subject = OPCLang::sprintf('COM_VIRTUEMART_VENDOR_NEW_ORDER_CONFIRMED', $this->shopperName, $this->cart->prices['billTotal'], $this->order['details']['BT']->order_number);
			$recipient = 'vendor';
		} else {
			$this->subject = OPCLang::sprintf('COM_VIRTUEMART_ACC_ORDER_INFO', $this->cart->vendor->vendor_store_name, $this->cart->prices['billTotal'], $this->order['details']['BT']->order_number, $this->order['details']['BT']->order_pass);
			$recipient = 'shopper';
		}
		$this->doVendor = true;
		if (VmConfig::get('order_mail_html'))
		$tpl = 'mail_html';
		else
		$tpl = 'mail_raw';
		$this->assignRef('recipient', $recipient);
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$vendorModel = OPCmini::getModel('vendor');
		$this->vendorEmail = $vendorModel->getVendorEmail($cart->vendor->virtuemart_vendor_id);
		
		$this->layoutName = $tpl;
		$this->setLayout($tpl);
		parent::display();
	}
	
	public function prepareContinueLink() {
		// Get a continue link */
		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		$categoryLink = '';
		if ($virtuemart_category_id) {
			$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		}
		$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink);

		$continue_link_html = '<a class="continue_link" href="' . $continue_link . '" >' . OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
		$this->assignRef('continue_link_html', $continue_link_html);
		$this->assignRef('continue_link', $continue_link);
		
		$menuid = JRequest::getVar('Itemid','');
		if(!empty($menuid)){
			$menuid = '&Itemid='.$menuid;
		}
		$this->cart_link = JRoute::_('index.php?option=com_virtuemart&view=cart'.$menuid, FALSE);
		$this->assignRef('cart_link', $cart_link);
	}

	public function lSelectCoupon() {
		
		$this->couponCode = (isset($this->cart->couponCode) ? $this->cart->couponCode : '');
		$coupon_text = $this->cart->couponCode ? OPCLang::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : OPCLang::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
		
		$this->assignRef('coupon_text', $coupon_text);
	}

	/*
	 * lSelectShipment
	* find al shipment rates available for this cart
	*
	* @author Valerie Isaksen
	*/

	public function lSelectShipment($force=false) {
	  include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
	  
	  $op_disable_shipping = OPCloader::getShippingEnabled(); 
	  if (!$force)
	  {
	  if (!empty($op_delay_ship)) return;
	  if (!empty($op_disable_shipto)) return;
	  
	  
	  if (!empty($op_disable_shipping)) return;
	  }
	  
	  $x = null; 
	  basketHelper::createDefaultAddress($x, $this->cart); 
	  // USPS returns redirect when no BT address is set here
	
		$found_shipment_method=false;
		$shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);

		$shipments_shipment_rates=array();
		if (!$this->checkShipmentMethodsConfigured()) {
			$this->assignRef('shipments_shipment_rates',$shipments_shipment_rates);
			$this->assignRef('found_shipment_method', $found_shipment_method);
			return;
		}
		$selectedShipment = (empty($this->cart->virtuemart_shipmentmethod_id) ? 0 : $this->cart->virtuemart_shipmentmethod_id);

		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $this->cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				break;
			}
		}
		
		
		$shipment_not_found_text = OPCLang::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		
		if (!$force)
		if (!class_exists('OPCPluginLoaded'))
		$shipment_not_found_text .= '<br />OPC: OPC was not able to load vmplugin.php override! Please contact webmaster.'; 
		
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);
		$this->assignRef('shipments_shipment_rates', $shipments_shipment_rates);
		$this->assignRef('found_shipment_method', $found_shipment_method);
		$x = null; 
		basketHelper::restoreDefaultAddress($x, $this->cart); 
		return;
	}

	/*
	 * lSelectPayment
	* find al payment available for this cart
	*
	* @author Valerie Isaksen
	*/

	public function lSelectPayment() {
	
	$opc = VmConfig::get('oncheckout_opc', 1); 
	if (empty($opc)) 
	{
		
		return true; 
	}
	
	
		// let's try deleyad payment
		//return;
		$payment_not_found_text='';
		$payments_payment_rates=array();
		if (!$this->checkPaymentMethodsConfigured()) {
			$this->assignRef('paymentplugins_payments', $payments_payment_rates);
			$this->assignRef('found_payment_method', $found_payment_method);
		}

		$selectedPayment = empty($this->cart->virtuemart_paymentmethod_id) ? 0 : $this->cart->virtuemart_paymentmethod_id;

		$paymentplugins_payments = array();
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($this->cart, $selectedPayment, &$paymentplugins_payments));
		// if no payment defined
		$found_payment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_payment_method = true;
				break;
			}
		}

		if (!$found_payment_method) {
			$link=''; // todo
			$payment_not_found_text = OPCLang::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}

		$this->assignRef('payment_not_found_text', $payment_not_found_text);
		$this->assignRef('paymentplugins_payments', $paymentplugins_payments);
		$this->assignRef('found_payment_method', $found_payment_method);
	}

	public function getTotalInPaymentCurrency() {

		if (empty($this->cart->virtuemart_paymentmethod_id)) {
			return null;
		}

		if (!$this->cart->paymentCurrency or ($this->cart->paymentCurrency==$this->cart->pricesCurrency)) {
			return null;
		}

		$paymentCurrency = CurrencyDisplay::getInstance($this->cart->paymentCurrency);

		$totalInPaymentCurrency = $paymentCurrency->priceDisplay( $this->cart->pricesUnformatted['billTotal'],$this->cart->paymentCurrency) ;

		$cd = CurrencyDisplay::getInstance($this->cart->pricesCurrency);


		return $totalInPaymentCurrency;
	}

	public function lOrderDone() {
		//$html = JRequest::getVar('html', OPCLang::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'post', 'STRING', JREQUEST_ALLOWRAW);
		
		$display_title = (bool)JRequest::getVar('display_title',true);
		$this->assignRef('display_title', $display_title);
		
		$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
		$this->assignRef('html', $html);

		$display_title = (bool)JRequest::getVar('display_title',true);
		$this->assignRef('display_title', $display_title);
		
		$template = JFactory::getApplication('site')->getTemplate();
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'order_done.php'; 
	    $dir = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR;
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
		
		
	
                $orderModel = VmModel::getModel('orders');
				
				if (class_exists('OPCcheckout'))
				if (isset(OPCcheckout::$new_order))
				{
				  $orderDetails =& OPCcheckout::$new_order; 
				}
				else
				{
				if ((!empty($this->cart)) && (isset($this->cart->virtuemart_order_id)))
				{
                $orderDetails = $orderModel->getOrder( $this->cart->virtuemart_order_id );
				}
				else 
				{
				return;
				}
				}

                $userFieldsModel = VmModel::getModel('userfields');
                $_userFields = $userFieldsModel->getUserFields(
                        'account'
                        , array('captcha' => true, 'delimiters' => true) // Ignore these types
                        , array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type', 'tos') // Skips
                );
                $orderbt = $orderDetails['details']['BT'];
                $orderst = (array_key_exists('ST', $orderDetails['details'])) ? $orderDetails['details']['ST'] : $orderbt;
                $userfields = $userFieldsModel->getUserFieldsFilled(
                    $_userFields
                    ,$orderbt
                );
                $_userFields = $userFieldsModel->getUserFields(
                        'shipment'
                        , array() // Default switches
                        , array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'tos', 'address_type') // Skips
                );

                $shipmentfields = $userFieldsModel->getUserFieldsFilled(
                    $_userFields
                    ,$orderst
                );

                $shipment_name='';
                if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
                JPluginHelper::importPlugin('vmshipment');
                $dispatcher = JDispatcher::getInstance();
                $returnValues = $dispatcher->trigger('plgVmOnShowOrderFEShipment',array(  $orderDetails['details']['BT']->virtuemart_order_id, $orderDetails['details']['BT']->virtuemart_shipmentmethod_id, &$shipment_name));

                $payment_name='';
                if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
                JPluginHelper::importPlugin('vmpayment');
                $dispatcher = JDispatcher::getInstance();
                $returnValues = $dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $orderDetails['details']['BT']->virtuemart_order_id, $orderDetails['details']['BT']->virtuemart_paymentmethod_id,  &$payment_name));

                $this->assignRef('userfields', $userfields);
                $this->assignRef('shipmentfields', $shipmentfields);
                $this->assignRef('shipment_name', $shipment_name);
                $this->assignRef('payment_name', $payment_name);
                $this->assignRef('orderdetails', $orderDetails);

		if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');

		$currency = CurrencyDisplay::getInstance();
		$this->assignRef('currency', $currency);
		
		
		//Show Thank you page or error due payment plugins like paypal express
	}

	public function checkPaymentMethodsConfigured() {
		if (!class_exists('VirtueMartModelPaymentmethod'))
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'paymentmethod.php');
	
		
		//For the selection of the payment method we need the total amount to pay.
		$paymentModel = new VirtueMartModelPaymentmethod();
		$payments = $paymentModel->getPayments(true, false);
		if (empty($payments)) {

			$text = '';
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 			
			if (OPCmini::isSuperVendor())
			 {
			   $uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=paymentmethod';
				$text = OPCLang::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			 }
			
			
			
			if (!defined('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED'))
			{
			 
			 vmInfo('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text); 
			}
			

			$tmp = 0;
			$this->assignRef('found_payment_method', $tmp);

			return false;
		}
		return true;
	}

	public function checkShipmentMethodsConfigured() {
		if (!class_exists('VirtueMartModelShipmentMethod'))
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'shipmentmethod.php');
		//For the selection of the shipment method we need the total amount to pay.
		$shipmentModel = new VirtueMartModelShipmentmethod();
		$shipments = $shipmentModel->getShipments();
		if (empty($shipments)) {

			$text = '';
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			if (OPCmini::isSuperVendor())
			 {
			   $uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=shipmentmethod';
				$text = OPCLang::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED_LINK', '<a href="' . $link . '" rel="nofollow">' . $link . '</a>');
			 }
			
			
		
			if (!defined('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED'))
			{
			 vmInfo('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text); 
			}

			$tmp = 0;
			$this->assignRef('found_shipment_method', $tmp);

			return false;
		}
		return true;
	}

	public function stylesheet($file, $path, $arg=array())
	{
	  $onlypage = JRequest::getCmd('only_page', ''); 
	  if (false)
	  if (!empty($onlypage))
	  {
	    echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		 // content of your Javascript goes here
		 var headID = document.getElementsByTagName("head")[0];    
		 var cssNode = document.createElement(\'link\'); 
		 cssNode.type = \'text/css\';
		 cssNode.rel = \'stylesheet\';
		 cssNode.href = \''.$path.$file.'\';
		 cssNode.media = \'screen\';
		 headID.appendChild(cssNode);
		
		/* ]]> */
		</script>';

	  }
	  //else
	  JHTMLOPC::stylesheet($file, $path, $arg);
	}
	public function script($file, $path, $arg, $onload="")
	{
	  
	 
	  
	  JHTMLOPC::script($file, $path, $arg);
	  
	}
	
	/**
	 * moved to shopfunctionf
	 * @deprecated
	 */
	// add vendor for cart
	public function prepareVendor(&$cart){
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$vendorModel = OPCmini::getModel('vendor');
		$cart->vendor = $vendorModel->getVendor();
		$vendorModel->addImages($cart->vendor,1);
		return $cart->vendor;
	}
	
	public function getUserList() {
		
		if (defined('VM_VERSION') && (VM_VERSION >=3))
		{
				$result = false;

		if($this->allowChangeShopper){
			$this->adminID = JFactory::getSession()->get('vmAdminID',false);
			if($this->adminID) {
				if(!class_exists('vmCrypt'))
					require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmcrypt.php');
				$this->adminID = vmCrypt::decrypt( $this->adminID );
			}
			$superVendor = VmConfig::isSuperVendor($this->adminID);
			if($superVendor){
				$uModel = VmModel::getModel('user');
				if (!method_exists($uModel, 'getSwitchUserList')) return array(); 
				$result = $uModel->getSwitchUserList($superVendor,$this->adminID);
			}
		}
		if(!$result) $this->allowChangeShopper = false;
		return $result;
		}
		
		$db = JFactory::getDbo();
		$q = 'SELECT * FROM #__users ORDER BY username';
		$db->setQuery($q);
		$result = $db->loadObjectList();
		foreach($result as $user) {
			$user->displayedName = $user->name .'&nbsp;&nbsp;( '. $user->username .' )';
		}
		return $result;
	}
	
	public function setUpUserList()
	{
		$current = JFactory::getUser();
		$this->allowChangeShopper = false;
		$this->adminID = false;
		if(VmConfig::get ('oncheckout_change_shopper')){
			if($current->authorise('core.admin', 'com_virtuemart') or $current->authorise('vm.user', 'com_virtuemart')){
				$this->allowChangeShopper = true;
			} else {
				$this->adminID = JFactory::getSession()->get('vmAdminID',false);
				if($this->adminID){
					if(!class_exists('vmCrypt'))
						require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmcrypt.php');
					$this->adminID = vmCrypt::decrypt($this->adminID);
					$adminIdUser = JFactory::getUser($this->adminID);
					if($adminIdUser->authorise('core.admin', 'com_virtuemart') or $adminIdUser->authorise('vm.user', 'com_virtuemart')){
						$this->allowChangeShopper = true;
					}
				}
			}
		}
		if($this->allowChangeShopper){
			$this->userList = $this->getUserList();
		}
	}

	
	//vm3.0.10 update: 
	static public function addCheckRequiredJs(){
		$j='jQuery(document).ready(function(){

    jQuery(".output-shipto").find(":radio").change(function(){
        var form = jQuery("#checkoutFormSubmit");
        jQuery(this).vm2front("startVmLoading");
		document.checkoutForm.submit();
    });
    jQuery(".required").change(function(){
    	var count = 0;
    	var hit = 0;
    	jQuery.each(jQuery(".required"), function (key, value){
    		count++;
    		if(jQuery(this).attr("checked")){
        		hit++;
       		}
    	});
        if(count==hit){
        	jQuery(this).vm2front("startVmLoading");
        	var form = jQuery("#checkoutFormSubmit");
        	//document.checkoutForm.task = "checkout";
			document.checkoutForm.submit();
        }
    });
});';
		vmJsApi::addJScript('autocheck',$j);
	}
	function renderCompleteAddressList(){

		$addressList = false;

		if($this->cart->user->virtuemart_user_id){
			$addressList = array();
			$newBT = '<a href="index.php'
				.'?option=com_virtuemart'
				.'&view=user'
				.'&task=editaddresscart'
				.'&addrtype=BT'
				. '">'.vmText::_('COM_VIRTUEMART_ACC_BILL_DEF').'</a></br>';
			foreach($this->cart->user->userInfo as $userInfo){
				$address = $userInfo->loadFieldValues(false);
				if($address->address_type=='BT'){
					$address->virtuemart_userinfo_id = 0;
					$address->address_type_name = $newBT;
					array_unshift($addressList,$address);
				} else {
					$address->address_type_name = '<a href="index.php'
					.'?option=com_virtuemart'
					.'&view=user'
					.'&task=editaddresscart'
					.'&addrtype=ST'
					.'&virtuemart_userinfo_id='.$address->virtuemart_userinfo_id
					. '" rel="nofollow">'.$address->address_type_name.'</a></br>';
					$addressList[] = $address;
				}
			}
			if(count($addressList)==0){
				$addressList[0] = new stdClass();
				$addressList[0]->virtuemart_userinfo_id = 0;
				$addressList[0]->address_type_name = $newBT;
			}

			$_selectedAddress = (
			empty($this->cart->selected_shipto)
				? $addressList[0]->virtuemart_userinfo_id // Defaults to 1st BillTo
				: $this->cart->selected_shipto
			);

			$this->cart->lists['shipTo'] = JHtml::_('select.radiolist', $addressList, 'shipto', null, 'virtuemart_userinfo_id', 'address_type_name', $_selectedAddress);
			$this->cart->lists['billTo'] = empty($addressList[0]->virtuemart_userinfo_id)? 0 : $addressList[0]->virtuemart_userinfo_id;
		} else {
			$this->cart->lists['shipTo'] = false;
			$this->cart->lists['billTo'] = false;
		}
	}

	

	
}

class op_languageHelper 
{
 public function _($val)
 {
   $v2 = str_replace('PHPSHOP_', 'COM_VIRTUEMART_', $val); 
   return OPCLang::_($v2);  
 }
 public function load($str='')
 {
 }
}
//no closing tag
if (!function_exists('mm_showMyFileName'))
{
function mm_showMyFileName()
{
}
}
if (!function_exists('vmIsJoomla'))
{
 function vmIsJoomla()
 {
   return false;
 }
}


