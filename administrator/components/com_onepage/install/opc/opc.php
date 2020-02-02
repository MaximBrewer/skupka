<?php 
/** 
 * @version		$Id: opc.php$
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport( 'joomla.session.session' );

//stAn - this line will make sure that joomla uses it's session handler all the time. If any other extension is using $ _SESSION before this line, the session may not be consistent
JFactory::getSession(); 

JFactory::getApplication()->set('is_rupostel_opc', true);

// many 3rd party plugins faild on JParameter not found: 
jimport( 'joomla.html.parameter' );
// many 3rd party plugins also fail on JRegistry not found: 
jimport( 'joomla.registry.registry' );
// basic security classes should also be globally included: 
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 

	
		
	

/*
if(version_compare(JVERSION,'3.0.0','ge')) {
  if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
  JLoader::register('JDate', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'joomla3'.DIRECTORY_SEPARATOR.'date.php'); 
 // require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'joomla3'.DIRECTORY_SEPARATOR.'date.php'); 
// Joomla! 1.7 code here
}
*/



//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
class plgSystemOpc extends JPlugin
{
	
	/*
	function plgVmOnCheckoutCheckStock( &$cart, &$product, &$quantity, &$errorMsg, &$adjustQ, $inCheckout )
    {
		
		
		
		
		$doc = JFactory::getDocument(); 
	 $class = get_class($doc); 
	 $class = strtoupper($class); 
	 // never run in an ajax context !
	 $arr = array('JDOCUMENTHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT'); 
	    if (!in_array($class, $arr)) 
	    {
		
			
		 // we are in ajax context
		}
		else
		{
		 // we are in user context:
		 if(!$inCheckout) return null;
		}
		// we are in order processing user context
		
		$quantity = 2; 
       
        if(!empty($errorMsg)) $errorMsg.='<br />';
        $errorMsg .= 'error quantity !';
		
		
        return false;
    }
	*/
	
	
	public function __construct(&$subject, $config)
	{
		/*test start
  if (!class_exists('VmConfig'))	
		{
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	    VmConfig::loadConfig(); 
	   }	   
	   $e = $_GET['error_redirect']; 
	   if (!empty($e)) {
	   }
	   if (!class_exists('VirtueMartCart')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php'); $cart = VirtueMartCart::getCart(false);  
		test end*/
	   
		parent::__construct($subject, $config);
		
		//fix VM2 and VM3 add-to-cart popup problems: 
		if (defined('INPUT_POST'))
		{
		$task_post = filter_input(INPUT_POST, 'task'); 
		$task_get = filter_input(INPUT_GET, 'task'); 
		
		if (!empty($task_post))
		if (($task_post === 'add') && ($task_get === 'addJS'))
		{
			
			if (class_exists('JRequest'))
			{
			 JRequest::setVar('task', 'addJS'); 
			}
		}
		
		}
		else
		{
		$taskP = filter_input(INPUT_GET, 'task'); 
			$taskG = filter_input(INPUT_POST, 'task'); 
			
		if ((!empty($taskP)) && (!empty($taskG)))
		if (($taskP === 'add') && ($taskG === 'addJS'))
		{
			$_POST['task'] = 'addJS';
			if (class_exists('JRequest'))
			{
			 JRequest::setVar('task', 'addJS'); 
			}
		}
		}
	}
	
	public function plgVmBuildTabs(&$view, &$tabs)
	{
				
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
			if (!empty($render_in_third_address)) {
			   
			   
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php');
					OPCthirdAddress::getTabs($view, $tabs); 



			
			
				
				 
				}
		
		
			  
	}
	
	public function hasVM() {
		static $c; 
		if (isset($c)) return $c; 
		if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) {
			$c = true; 
		}
		else $c = false; 
		return $c; 
	}
	
	public function onAfterInitialise()
	{
		if (!$this->hasVM()) return; 
		$view = filter_input(INPUT_GET, 'view'); 
		if (!empty($view) && ($view === 'image')) return; 
		
		$format = filter_input(INPUT_GET, 'format'); 
		if ((!empty($format)) && ($format != 'html')) return; 
		
		$Itemid = JRequest::getVar('Itemid'); 
		if (empty($Itemid))
		{
		if (!self::_check(false, false)) {
	
			return;
		}
		OPCplugin::setItemid(); 
		}
		
		
	
		
	}	
	// vm3.0.6 forces automatic shipment and payment even when disabled: 
	public function plgVmOnCheckAutomaticSelectedShipment($cart, $prices, &$counter)
	{
		if (!self::_check())  return; 		
		if (empty($counter)) $counter = 1; 
		return "-0"; 
	}
	public function plgVmOnCheckAutomaticSelectedPayment($cart, $prices, &$counter)
	{
		if (!self::_check())  return; 
		if (empty($counter)) $counter = 1; 
		return "-0"; 
	}


	
    public static $opc_jquery_loaded; 
    public function onBeforeCompileHead() {
	  if (!$this->hasVM()) return; 
	
	  if (empty(self::$opc_jquery_loaded)) return; 
	  if (self::_check(true)) 
	  {
	  
	  
	    return OPCplugin::modifyHeader(); 
	  }
	  
	}
	
	function plgVmConfirmedOrderUNITTEST ($cart, $order) {
		echo 'plgVmConfirmedOrder opc...'; 
	}
	
	
	function plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id)
	{
		
		$virtuemart_order_id = (int)$virtuemart_order_id; 
		if (empty($virtuemart_order_id)) return; 
		
		JFactory::getLanguage()->load('com_onepage', JPATH_ADMINISTRATOR);
		
	
		$f = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'; 
		
		
		if (!file_exists($f)) return; 
		
		$html = ''; 
		
 		require_once($f); 
	    
		
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			
			$orders = OPCmini::getModel('orders'); 
			$order = $orders->getOrder($virtuemart_order_id); 
			
			if (empty($order['details'])) return; 
			if (empty($order['details']['BT'])) return; 
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
			if (!empty($opc_vat_key))
			if (isset($order['details']['BT']->$opc_vat_key))
			{
				
				
				$html .= OPCvat::plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id, $order); 
				
			}
			
			
			$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
			if (!empty($render_in_third_address)) {
			   
			   
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php');
					$html .= OPCthirdAddress::renderThirdAddressWrap($virtuemart_order_id, $order); 



			
			
				
				 
				}
				
				return $html; 
	}
	public function plgVmOnAddToCartFilter(&$product, &$customfield, &$customProductData, &$customFiltered)
	{
	   $session = JFactory::getSession(); 
	   $urls = $session->get('product_urls', array()); 
	   if (empty($urls)) $urls = array();
       if (!empty($urls)) $urls = json_decode($urls, true); 	   
	   //if (!isset($urls[$product->virtuemart_product_id]))
	   {
		   $t1 = JRequest::getVar('product_addtocart_url'); 
		   
		
		   if (!empty($t1))
		   {
			   $urls[$product->virtuemart_product_id] = base64_decode($t1); 
			   
			   $session->set('product_urls', json_encode($urls)); 
		  
		  $session->set('lastcontiuelink', $urls[$product->virtuemart_product_id], 'opc');
			   
		   }
		   //else
		   //$urls[$product->virtuemart_product_id] = Juri::current(); 
	   
	      
	   }
	   
	}
	
	
	
	
    public function onAfterRoute() {

	if (!$this->hasVM()) return; 
	
    // to support opc vm fields: 
	JFactory::getLanguage()->load('com_onepage', JPATH_SITE); 
	  
	  $rt = true; 
	  // loads classes: 
	  if (!self::_check())  $rt = false; 
	  
      if (!$rt) {
	    if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return; 
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php'); 
		
	  }
	  OPCplugin::loadVM(); 
	  OPCplugin::checkTasks(); 
	  OPCplugin::getContinueLink(); 
	  // but opc is not enabled or not in html context... 
	  if (!$rt) return; 

	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	   
	   $opc_load_jquery = OPCConfig::getValue('opc_load_jquery', '', 0, false, false); 
	   self::$opc_jquery_loaded = (bool)$opc_load_jquery; 
	   if (!empty($opc_load_jquery))
	   {
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		OPCJavascript::loadJquery();  
	
	   }
	
	   
	  

	  
	
	
	

	
	
	
	if (!OPCplugin::checkLoad()) return; 
	
	OPCplugin::loadShoppergroups();
	  
	if (!OPCplugin::isOPCcheckoutEnabled()) return;
	
	OPCplugin::getCache(); 

	  if (OPCplugin::alterActivation()) return; 
	  if (OPCplugin::alterRegistration()) return; 
	  
	  
	  
	  OPCplugin::enableSilentRegistration(); 
	  if (!OPCplugin::checkOPCtask()) return; 
	  OPCplugin::keyCaptchaSupport(); 
	  
	  
	 
	  if (!OPCplugin::loadOPCcartView()) return; 
	  
	  
	  
	  OPCplugin::fixVMbugVirtuemartUser(); 
	  OPCplugin::fixVMbugNewShippingAddress(); 
	  
	  OPCplugin::setItemid(); 
	  OPCplugin::loadOpcForLoggedUser(); 
	  OPCplugin::updateJoomlaCredentials(); 
	  OPCplugin::updateAmericanTax(); 
	
	  
	  
	  
	
	
	}
	
	
	public function onVmSiteController($controller)
	{
	   
	   if ($controller == 'opc')
	   if (!class_exists('VirtueMartControllerOpc'))
	    {
		   
		   
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
		   
		   
		   
		   
		}
	}

    public static function _check($allowAdmin=false, $allowconst=true)
	{
		self::_getIncludes(); 
		
	    if (defined('OPC_GENERAL_CHECK_OPCPLUGIN')) return OPC_GENERAL_CHECK_OPCPLUGIN; 
	
		if (!$allowAdmin)
		{
	  	$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			define('OPC_GENERAL_CHECK_OPCPLUGIN', false); 
			return false;
		}
		}
				if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) 
				{
					
				define('OPC_GENERAL_CHECK_OPCPLUGIN', false); 
				return false;
				
				}
		//disable all opc features if amazon is detected: 
	$amazon = JRequest::getVar('nt', JRequest::getVar('amp;nt')); 
	$session = JFactory::getSession(); 
	if ($amazon === 'getAmazonSessionId')
	{
		
		if (!class_exists( 'VmConfig' )) 
			{
				require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig(); 
			}		
		 
		 if (!class_exists('VirtueMartCart'))
		 require(JPATH_VM_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');

		$cart = VirtuemartCart::getCart(); 
		if (stripos($cart->layoutPath, 'com_onepage')!==false) {
		 $cart->layoutPath = ''; 
		 $cart->layout = ''; 
		}
		
		$session->set('disableopc', true); 
	}
	self::checkAmazon(); 
	
	$isdisabled = $session->get('disableopc', false); 
	
	$option = JRequest::getVar('option', ''); 
	$task = JRequest::getVar('task'); 
	$view = JRequest::getVar('view'); 
	$va = array('cart', 'vmplg', 'pluginresponse'); 
	if (($option == 'com_virtuemart') && (!in_array($view, $va))) {
		$session->set('disableopc', false); 
	}
	
	
	//updateCartWithAmazonAddress&virtuemart_paymentmethod_id=6&lang=en".
	$action = JRequest::getVar('action', JRequest::getVar('amp;action')); 
	if (!empty($isdisabled)) 
	{
		
		if ($action === 'leaveAmazonCheckout')
		{
			$session->set('disableopc', false); 
			// it needs a page refresh
			return false; 
		}
		else
		{
			return false; 
		}
		
		//'action=leaveAmazonCheckout&virtuemart_paymentmethod_id=6&lang=en".
		//return false; 
		
	}
	else
	{
		if ($action === 'updateCartWithAmazonAddress')
		{
			$session->set('disableopc', true); 
			return false; 
		}
	}
		
		
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') 
		if ($format != 'opchtml')	
		return false;

		if ($allowconst) {
		$doc = JFactory::getDocument(); 
		if (method_exists($doc, 'getType')) {
			$type = $doc->getType(); 
			if (($type === 'html') || ($type === 'opchtml')) {
				if ($allowconst)
				{
					define('OPC_GENERAL_CHECK_OPCPLUGIN', true); 
				}
		
			   return true; 
			}
		}
		$class = strtoupper(get_class($doc)); 

		$arr = array('JDOCUMENTHTML', 'JDOCUMENTOPCHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT', 'JOOMLA\CMS\DOCUMENT\OPCHTMLDOCUMENT'); 
		if (!in_array($class, $arr)) 
		{
			
		define('OPC_GENERAL_CHECK_OPCPLUGIN', false); 
		return false; 
		}
		}

	   
		if ($allowconst)
		{
		define('OPC_GENERAL_CHECK_OPCPLUGIN', true); 
		}
		
		return true; 

	}
	
	private static function checkAmazon() {
		 $session = JFactory::getSession(); 
		 $sessionAmazon = $session->get('amazon', 0, 'vm');

  if($sessionAmazon) {
   $sessionAmazonData = json_decode($sessionAmazon, true);
   if(isset($sessionAmazonData[$paymId])) {
	   if (!empty($sessionAmazonData[$paymId]['_amazonOrderReferenceId'])) {
		   $session->set('disableopc', true); 
		   return true; 
	   }
    //return $sessionAmazonData[$paymId]['_amazonOrderReferenceId'];
   }
  }
  $session->set('disableopc', false); 
  return false; 
	}
	
	public static function _getIncludes() {
	
	    if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) 
		{
		define('OPC_GENERAL_CHECK_OPCPLUGIN', false); 
		return false;
		}
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) 
		{
		define('OPC_GENERAL_CHECK_OPCPLUGIN', false); 
		return false;
		}
		
		JLoader::register('OPCplugin', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php' );
		
		JLoader::register('OPCconfig', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php' );
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php')) 
		{
		define('OPC_GENERAL_CHECK_OPCPLUGIN', false); 
		return false;
		}
		
		JLoader::register('OPCtrackingHelper', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php' );
		
		
		JLoader::register('OPCLang', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php' );
		
	}
	

	
	/**
	 * Converting the site URL to fit to the HTTP request
	 */
	public function onAfterRender()
	{
		
		//opc tracking start
		 //if (!empty(self::$delay)) return; 
	   
	   
	   
	   if (!$this->_check()) return; 

	   if (!$this->hasVM()) return; 

		$app = JFactory::getApplication();


		$format = JRequest::getVar('format', 'html'); 
		
		
		$task = JRequest::getVar('task', ''); 
		$task = strtolower($task); 
		$view = JRequest::getCMD('view'); 
		$option = JRequest::getVar('option', ''); 
		 //if(('com_virtuemart' == JRequest::getCMD('option') && !$app->isAdmin()) && (('cart'==$view) || ($view=='pluginresponse') || ((($view=='user') && ($task=='editaddresscheckout'))))) 
		 
		 
		 // see also: \components\com_onepage\helpers\plugin.php
		 $opc_url_addtocart = OPCconfig::get('opc_url_addtocart', false); 
		 if (!empty($opc_url_addtocart)) {
		  $buffer = JResponse::getBody();
		  $search = '<input type="hidden" name="virtuemart_product_id'; 
		 
		  $uri = JFactory::getURI();
		  $absolute_url = $uri->toString();
		  //JURI::current(); 
		  $new = '<input type="hidden" name="product_addtocart_url" value="'.base64_encode($absolute_url).'" />'; 
		  $buffer = str_replace($search, $new.$search, $buffer); 
		 }
		 
		 if (defined('OPC_IN_REGISTRATION_MODE') || (defined('OPC_CHECKOUT_RENDERED')))
		 {
		 
		 
		 
		 if (empty($opc_url_addtocart)) {
			 $buffer = JResponse::getBody();
		 }
	  
		//Replace src links
		$base	= JURI::base(true).'/';
		
		 //orig opc: 
		 $buffer = str_replace('$(".virtuemart_country_id").vm2', '// $(".virtuemart_country_id").vm2', $buffer); 
		 $buffer = str_replace('$("select.virtuemart_country_id").vm2', '// $("select.virtuemart_country_id").vm2', $buffer); 
		 $buffer = str_replace('$("select.shipto_virtuemart_country_id").vm2', '// $("select.shipto_virtuemart_country_id").vm2', $buffer); 
		 $buffer = str_replace('$(".virtuemart_country_id").vm2front', '// $(".virtuemart_country_id").vm2front', $buffer); 
		 $buffer = str_replace('$("#virtuemart_country_id").vm2front', '// $("#virtuemart_country_id").vm2front', $buffer);
		 $buffer = str_replace('$("#shipto_virtuemart_country_id").vm2front', '// $("#shipto_virtuemart_country_id").vm2front', $buffer);
		 $buffer = str_replace('jQuery(\'#zip_field, #shipto_zip_field\')', '// jQuery(\'#zip_field, #shipto_zip_field\')', $buffer); 
		 $buffer = str_replace('jQuery(\'select\').chosen', ' if (jQuery(\'select.chzn\').length > 0) jQuery(\'select.chzn\').chosen', $buffer); 

		 //jQuery('select').selectric()
		 $buffer = str_replace('jQuery(\'select\').selectric', ' if (jQuery(\'select.chzn\').length > 0) jQuery(\'select.chzn\').selectric', $buffer); 
		 
		 if (stripos($buffer, '/components/com_onepage/assets/js/vmcreditcard.js')!==false)
		 {
			 $buffer = str_replace('/components/com_virtuemart/assets/js/vmcreditcard.js', '/components/com_onepage/assets/js/emptyscript.js', $buffer); 
		 }
		 else
		 {
		 $buffer = str_replace('/components/com_virtuemart/assets/js/vmcreditcard.js', '/components/com_onepage/assets/js/vmcreditcard.js', $buffer); 
		 }
		 //$buffer = str_replace('$(".vm-chzn-select").chosen', '// $(".vm-chzn-select").chosen', $buffer);
		 $buffer = str_replace('/plugins/vmpayment/klarna/klarna/assets/js/klarna_general.js', '/components/com_onepage/overrides/payment/klarna/klarna_general.js', $buffer); 
		 
		 $inside = JRequest::getVar('insideiframe', ''); 
		 if (!empty($inside))
		  {
		    $buffer = str_replace('<body', '<body onload="javascript: return parent.resizeIframe(document.body.scrollHeight);"', $buffer); 
		  }
		 //$buffer = str_replace('$(".virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		$buffer = str_replace('jQuery("input").click', 'jQuery(null).click', $buffer);
		$buffer = str_replace('#paymentForm', '#adminForm', $buffer);
		
		
		 
		
		    if (class_exists('plgSystemBit_vm_change_shoppergroup'))
			{
				$js_text = "<script type=\"text/javascript\">location.reload()</script>";
				//$js_text = "location.reload()";
				$c = 0; 
				$buffer = str_replace($js_text, '', $buffer, $c); 
				
				 
				
			}
			// removing stupid VM messages which have nothing to do with hacking attempts:
			$buffer = str_replace('Hacking attempt loading userinfo, you got logged', '', $buffer); 
			
			if (empty($opc_url_addtocart)) {
			JResponse::setBody($buffer);
			}
		
		
		}
		if (!empty($opc_url_addtocart)) {
		 JResponse::setBody($buffer);
		}
		return true;
	}
	// we will disable the 
	public function plgVmInterpreteMathOp2($calc, $rule, $price, $revert)
	{
		//return false; 
	}
	
	public function plgVmonSelectedCalculatePriceShipment(VirtueMartCart $cart, &$cart_prices, &$cart_prices_name) {
	  
	  if (!empty($cart->virtuemart_shipmentmethod_id))
	  if ($cart->virtuemart_shipmentmethod_id < 0)
	  if (class_exists('OPCcache'))
	  if (!empty(OPCcache::$cachedResult['currentshipping']))
	  {
		return OPCcache::getStoredCalculation($cart, $cart_prices, $cart_prices_name); 
	  }
	}
	// triggered from: \administrator\components\com_virtuemart\models\orders.php
	public function plgVmOnUserOrder(&$_orderData)
	{
		// fix vm2.0.22 bug
		if (empty($_orderData->order_payment) && (empty($_orderData->order_payment_tax)))
		{
			
		 if (!class_exists('VirtueMartCart'))
		 require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');

			
					$cart = VirtueMartCart::getCart();
					$prices = $cart->getCartPrices();
					if (!empty($prices['salesPricePayment']))
					{
						$_orderData->order_payment = (float)$prices['salesPricePayment'];
					}
			
		}
	}
	function plgVmOnUserStore(&$data)
	{
	  
	  //if ((empty($data['username'])) && (!empty($data['email']))) $data['username'] = $data['email']; 
	}
	public function plgVmRemoveCoupon($_code, $_force)
	{
	   if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	   if (empty($_force))
	    {
		   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		   if (!empty($do_not_allow_gift_deletion)) return true; 
		}
		return null; 
	}
	
	/*
	public function plgVmOnUpdateOrderPayment(&$data,$old_order_status)
	{
	  if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) return;
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php'); 
	  OPCplugin::checkGiftCoupon($data, $old_order_status);  
	  
	  
	}
	*/
	
	// code to update shopper groups: 
	public static $saveGroups; 
	public function onUserLogin($user, $options = array())
	{
	   if (!$this->hasVM()) return; 
	   if(JFactory::getApplication()->isAdmin()) return; 
	   $session = JFactory::getSession();
	   self::$saveGroups = $session->get('vm_shoppergroups_add',array(),'vm'); 
	   $session->set('vm_shoppergroups_add',array(),'vm'); 
	   
	}
	public function onUserLoginFailure($resp)
	{
		if (!$this->hasVM()) return; 
	   if(JFactory::getApplication()->isAdmin()) return; 
	   if (!empty(self::$saveGroups))
	   {
	    $session = JFactory::getSession();
	    $session->set('vm_shoppergroups_add',self::$saveGroups,'vm'); 
	   }
	}
	
	public function onUserLogout($user, $options = array())
	{
	 
	}
	
	// deprecated: 
	public static function registerCart()
	{
	}
	
	
	function onUserAfterSave($user, $isNew, $result, $error)
	{
		if (!$this->hasVM()) return; 
		if(JFactory::getApplication()->isAdmin()) return; 
		
	  return $this->onAfterStoreUser($user, $isNew, $result, $error); 
	}
	
	function onAfterStoreUser($user, $isnew, $success, $msg){
	   if (!$this->hasVM()) return; 
	   if(JFactory::getApplication()->isAdmin()) return; 
	
	   if(is_object($user)) $user = get_object_vars($user);

	   if($success===false OR empty($user['email'])) return true;
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		$opc_vat = JRequest::getVar($opc_vat_key, ''); 
		
	    if (!empty($opc_vat))
		{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 			    
		//$VirtueMartControllerOpc = new VirtueMartControllerOpc(); 
		$country = JRequest::getVar('virtuemart_country_id'); 
		$resp = 1; 
		
		
		$checkvat = VirtueMartControllerOpc::checkOPCVat($opc_vat, $country, $resp); 
		
		if ($resp === 0)
		{
			JFactory::getLanguage()->load('com_onepage'); 
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ONEPAGE_VAT_CHECKER_INVALID')); 
		}
		
		
		
		}
		
		
		if (!empty($user['id']))
		{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		$ids = OPCShopperGroups::getOPCshopperGroups(); 
		
		if (!empty($ids))
		{
		$data = array(); 
		$user_id = $user['id']; 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		OPCShopperGroups::updateSG($data, $user_id, $ids); 
		}
		OPCShopperGroups::forceShopperGroups($ids); 
		}
		
	}
	
	  //support for disabled istraxx euvat plugin: 
    public function plgVmOnUserfieldDisplay ($_prefix, $field, $userId, &$return) {
	  if ($field->type === 'pluginistraxx_euvatchecker') {
		  $db = JFactory::getDBO(); 
		  $q = 'select `enabled` from #__extensions where `element` = "istraxx_euvatchecker" and `enabled` = 1'; 
		  $db->setQuery($q); 
		  $res = $db->loadResult(); 
		  if (empty($res)) {
		   $return['fields'][$field->name]['formcode'] = '<input type="text" id="'.$_prefix.$field->name.'_field" name="'.$_prefix.$field->name.'" value="'.str_replace('"', '\"', $return['fields'][$field->name]['value']).'" '. ' /> ';
		  }
	
		}
  
	}
	
	//remove references to the VM cart
	function awoDefineCartItemsBefore(&$cart_products, $awoinstance) {
		$awoinstance->vmcartProducts = array(); 
		
		if ( defined('VM_VERSION') && (VM_VERSION >=3 ) ) {
			$awoinstance->vmcartProducts = array(); 
			foreach ( $awoinstance->vmcart->cartProductsData as $k => $item ) {
				$newItem = (object)$item; 
				$awoinstance->vmcartProducts[ $k ] = $newItem;
			}
		}
		else {
			$awoinstance->vmcartProducts = array(); 
			foreach ( $awoinstance->vmcart->products as $k => $item ) {
				$newItem = (array)$item; 
				$otheritem = (object)$newItem; 
				$awoinstance->vmcartProducts[ $k ] = $otheritem;
			}
		}
		$cart_products = $awoinstance->vmcartProducts;
		
		
	}

	


		
	
}



// php 5.3 does not support static loading of this function with the same syntax as php5.5, therefore we included it outside the class scope
function fatal_error_catch()
	{
	
	
	
	if (function_exists('fastcgi_finish_request')) fastcgi_finish_request(); 
	
	$errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;

  $error = error_get_last();
 
 //blank_screens_email
 
  if( $error !== NULL) {
  
    $types = array(E_ERROR,  E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR); 
	
    $errno   = $error["type"];
	
	if (!in_array($errno, $types)) return;
	
	
	 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
     if (empty($opc_debug2)) return;
	
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];
	if (class_exists('JDate'))
	{
	$date = new JDate(); 
	$date = $date->toISO8601();
	}
	else
	$date = date('c'); 
	
    $dataMsg = $errno.' '.$errstr.' in file: '.$errfile.' line: '.$errline." timestamp: ".$date."\n";
   $f = JPATH_SITE. DIRECTORY_SEPARATOR .'logs'. DIRECTORY_SEPARATOR .'php_errors.log.php'; 
   if (!file_exists($f))
   {
     $data = '<?php die(); ?>'."\n".$dataMsg; 
     @file_put_contents($f, $data); 
   }
   else
    {
	   @file_put_contents($f, $dataMsg, FILE_APPEND); 
	}
	
	JLoader::register('OPCconfig', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php' );
	$blank_screens_email = OPCconfig::get('blank_screens_email', ''); 
	$email = $blank_screens_email; 
	  if (!empty($email))
	  {
	    $mailer = JFactory::getMailer();
		$mailer->addRecipient( $blank_screens_email );
		$subject = 'Fatal Error Detected on your Joomla Site'; 
		$mailer->setSubject(  html_entity_decode( $subject) );
		$mailer->isHTML( false );
		
		$body = "RuposTel OPC plugin detected a problem with your site. \nYour site caused a blank screen upon a visit of this URL: \n"; 

	 $pageURL = 'http';
     if ((isset($_SERVER['HTTPS'])) && ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }		
			$body .= $pageURL."\n"; 
			$body .= 'Error message data: '."\n"; 
			$body .= $dataMsg; 
			$body .= "\n\nTo disable these emails proceed to your Components -> Onepage -> General tab -> Log Blank Screens\n";
			$body .= "It is very important that you fix all php fatal errors on your site. Resend this email to your developer."; 
		$config = JFactory::getConfig();
		$sender = array( $config->get( 'mailfrom' ), $config->get( 'fromname' ) );
		$mailer->setBody( $body );
		$mailer->setSender( $sender );
		$mailer->Send();
	  }
	
  }
  
  
  
	}

/* global config */
//if (plgSystemOpc::_check(false, false))
if (!class_exists('vmPlugin'))
{
if (!JFactory::getApplication()->isAdmin())
	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'vmplugin.php'); 
}


	
if (function_exists('register_shutdown_function'))
register_shutdown_function( "fatal_error_catch" );
	