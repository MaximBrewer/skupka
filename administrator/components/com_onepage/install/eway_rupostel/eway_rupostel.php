<?php
/**
 *
 * @author stAn, RuposTel.com
 * @version $Id: eway_rupostel.php 
 * @package eWay Payment Plugin
 * @subpackage payment
 * @copyright Copyright (C) RuposTel.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * eWay Payment is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * Based on Authorize.net plugin by Virtuemart.net team
 *
 * http://rupostel.com
 */
defined('_JEXEC') or die('Restricted access');

if (!class_exists('Creditcard')) {
	require_once(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'creditcard.php');
}
if (!class_exists('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
}

class plgVmpaymentEway_rupostel extends vmPSPlugin
{

	private $_cc_name = '';
	private $_cc_type = '';
	private $_cc_number = '';
	private $_cc_cvv = '';
	private $_cc_expire_month = '';
	private $_cc_expire_year = '';
	private $_cc_valid = FALSE;
	private $_errormessage = array();
	private $_cc_cardholder = ''; 
	
	public $approved;
	public $declined;
	public $error;
	public $held;

	const APPROVED = 1;
	const DECLINED = 2;
	const ERROR = 3;
	const HELD = 4;

	public static $currentMethod; 
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	// instance of class
	function __construct (& $subject, $config)
	{

		parent::__construct($subject, $config);

		$this->_loggable = TRUE;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		
		
		$this->tableFields = array_keys($this->getTableSQLFields());
		$varsToPush = $this->getVarsToPush();

		$this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
		
if (!class_exists ('calculationHelper')) {
  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');
}
if (!class_exists ('CurrencyDisplay')) {
  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
}
if (!class_exists ('VirtueMartModelVendor')) {
  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'vendor.php');
}	
		
		if (!class_exists('VirtueMartCart'))
	    require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		$cart = VirtueMartCart::getCart();
		if (empty($cart->vendorId)) $vId = 1; 
		else $vId = $cart->vendorId; 
		if ($this->getPluginMethods($vId) === 0) {
			return;
		}
		foreach ($this->methods as $method)
		 {
		   plgVmpaymentEway_rupostel::$currentMethod = $method; 
		   return;
		 }
		
	}

	protected function getVmPluginCreateTableSQL ()
	{
		return $this->createTableSQL('Payment eWay Table');
	}

	function createTableSQL ($tableComment,$tablesFields=0) {

		$query = "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (";
		if(!empty($tablesFields)){
			foreach ($tablesFields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
		} else {
			$SQLfields = $this->getTableSQLFields ();
			$loggablefields = $this->getTableSQLLoggablefields ();
			foreach ($SQLfields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
			foreach ($loggablefields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . ", ";
			}
		}

		$query .= "	      PRIMARY KEY (`id`),
			KEY `eway_hash` (`eway_hash`),
			KEY `virtuemart_order_id` (`virtuemart_order_id`),
			KEY `order_number` (`order_number`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='" . $tableComment . "' AUTO_INCREMENT=1 ;";
		
		
		return $query;
	}
	
	function getTableSQLFields ()
	{

		$SQLfields = array(
			'id' => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id' => 'int(11) UNSIGNED',
			'order_number' => 'char(64)',
			'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
			'payment_name' => 'varchar(5000)',
			'payment_order_total' => 'decimal(15,5) NOT NULL',
			'payment_currency' => 'int(11)',
			'return_context' => 'char(255)',
			'cost_per_transaction' => 'decimal(10,2)',
			'cost_percent_total' => 'char(10)',
			'tax_id' => 'int(11)',
			'eway_response_authorization_code' => 'char(10)',
			'eway_response_transaction_id' => 'char(128)',
			'eway_response_response_code' => 'char(128)',
			'eway_response_response_subcode' => 'char(13)',
			'eway_response_response_reason_code' => 'char(10)',
			'eway_response_response_reason_text' => 'text',
			'eway_response_transaction_type' => 'char(50)',
			'eway_response_account_number' => 'char(4)',
			'eway_response_card_type' => 'char(128)',
			'eway_response_card_code_response' => 'char(5)',
			'eway_response_cavv_response' => 'char(1)',
			'ewayresponse_raw' => 'text',
			'eway_hash'=>'char(50)'
		);
		return $SQLfields;
	}

	/**
	 * This shows the plugin for choosing in the payment list of the checkout process.
	 *
	 * @author Valerie Cartan Isaksen
	 */
	function plgVmDisplayListFEPayment (VirtueMartCart $cart, $selected = 0, &$htmlIn)
	{



		if ($this->getPluginMethods($cart->vendorId) === 0) {
			if (empty($this->_name)) {
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_VIRTUEMART_CART_NO_' . strtoupper($this->_psType)));
				return FALSE;
			} else {
				return FALSE;
			}
		}
		$html = array();
		$method_name = $this->_psType . '_name';

		JHTML::script('vmcreditcard.js', 'components/com_virtuemart/assets/js/', FALSE);
		JFactory::getLanguage()->load('com_virtuemart');
		JFactory::getLanguage()->load('plg_vmpayment_eway_rupostel', dirname(__FILE__).DIRECTORY_SEPARATOR);
		
		
		
		vmJsApi::jCreditCard();
		$htmla = '';
		$html = array();
		foreach ($this->methods as $this->_currentMethod) {
		
			if ($this->checkConditions($cart, $this->_currentMethod, $cart->pricesUnformatted)) {
				$methodSalesPrice = $this->setCartPrices($cart, $cart->pricesUnformatted, $this->_currentMethod);
				$this->_currentMethod->$method_name = $this->renderPluginName($this->_currentMethod);
				$html = $this->getPluginHtml($this->_currentMethod, $selected, $methodSalesPrice);
				if ($selected == $this->_currentMethod->virtuemart_paymentmethod_id) {
					$this->_getAuthorizeNetFromSession();
				} else {
					$this->_cc_type = '';
					$this->_cc_number = '';
					$this->_cc_cvv = '';
					$this->_cc_expire_month = '';
					$this->_cc_expire_year = '';
				}

				if (empty($this->_currentMethod->creditcards)) {
					$this->_currentMethod->creditcards = self::getCreditCards();
				} elseif (!is_array($this->_currentMethod->creditcards)) {
					$this->_currentMethod->creditcards = (array)$this->_currentMethod->creditcards;
				}
				
				
				$creditCards = $this->_currentMethod->creditcards;
				VmConfig::loadJLang('plg_vmpayment_authorizenet',TRUE);
				$creditCardList = '';
				if ($creditCards) {
					$creditCardList = ($this->_renderCreditCardList($creditCards, $this->_cc_type, $this->_currentMethod->virtuemart_paymentmethod_id, FALSE));
				}
				
				
				$sandbox_msg = "";
				if ($this->_currentMethod->sandbox) {
					$sandbox_msg .= '<br />eWay is in sandbox mode. Use credit cards provided by the sandbox (Visa: 4444333322221111 OR 424212132325454 , MasterCard: 5105105105105100). To get get a confirmed transaction the order total must be rounded to zero decimals.'; 
				}

				$cvv_images = $this->_displayCVVImages($this->_currentMethod);
				include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'eway_rupostel'.DIRECTORY_SEPARATOR.'eway_files'.DIRECTORY_SEPARATOR."EwayConfig.inc.php");
				require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'eway_rupostel'.DIRECTORY_SEPARATOR.'eway_files'.DIRECTORY_SEPARATOR.'EwayPaymentLive.php'); 
				$eway = new RupEwayPaymentLive($this->_currentMethod->customer_id, $this->_currentMethod->EWAY_DEFAULT_PAYMENT_METHOD, (!(bool)$this->_currentMethod->sandbox));
				
				$months = shopfunctions::listMonths('cc_expire_month_'.$this->_currentMethod->virtuemart_paymentmethod_id, $this->_cc_expire_month);
				$years = shopfunctions::listYears('cc_expire_year_'.$this->_currentMethod->virtuemart_paymentmethod_id, $this->_cc_expire_year, NULL, 2022, " onchange=\"javascript:changeDate(" . $this->_currentMethod->virtuemart_paymentmethod_id . ", this);\" ");
				$pm = $this->_currentMethod->virtuemart_paymentmethod_id;
				$html = $this->getPluginHtml($this->_currentMethod, $selected, $methodSalesPrice);
				$html .= $this->renderByLayout('payment_form', array('method' => $this->_currentMethod, 'eway'=>$eway, 'creditCardList'=>$creditCardList, 'months'=> $months, 'years'=>$years, 'vmid'=>$pm, 'sandbox_msg'=>$sandbox_msg, 'cc_number'=>$this->_cc_number, 'cc_cvv'=>$this->_cc_cvv, 'cvv_images'=>$cvv_images, 'cc_cardholder'=>''));
				$html .= $this->getHashHtml($cart); 


				$htmla[] = $html;
			}
		}
		$htmlIn[] = $htmla;

		return TRUE;
	}
	/**

	 */
	static function getCreditCards() {
		return array(
			'Visa',
			'Mastercard',
			'AmericanExpress',

		);

	}
	/**
	 * Check if the payment conditions are fulfilled for this payment method
	 *
	 * @author: Valerie Isaksen
	 *
	 * @param $cart_prices: cart prices
	 * @param $payment
	 * @return true: if the conditions are fulfilled, false otherwise
	 *
	 */
	protected function checkConditions ($cart, $method, $cart_prices)
	{
		$this->convert_condition_amount($method);
		$amount = $this->getCartAmount($cart_prices);
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0)));
		if (!$amount_cond) {
			return FALSE;
		}
		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}

		// probably did not gave his BT:ST address
		if (!is_array($address)) {
			$address = array();
			$address['virtuemart_country_id'] = 0;
		}

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}
		if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
			return TRUE;
		}

		return FALSE;
	}


	function _setAuthorizeNetIntoSession ()
	{

		$session = JFactory::getSession();
		$sessionAuthorizeNet = new stdClass();
		// card information
		$sessionAuthorizeNet->cc_cardholder = $this->_cc_cardholder;
		$sessionAuthorizeNet->cc_type = $this->_cc_type;
		$sessionAuthorizeNet->cc_number = $this->_cc_number;
		$sessionAuthorizeNet->cc_cvv = $this->_cc_cvv;
		$sessionAuthorizeNet->cc_expire_month = $this->_cc_expire_month;
		$sessionAuthorizeNet->cc_expire_year = $this->_cc_expire_year;
		$sessionAuthorizeNet->cc_valid = $this->_cc_valid;
		
		$dt = $this->_prepareForSession($sessionAuthorizeNet); 
		$s2 = $this->_getDecodeData($dt); 
		$session->set('ewaydata', $dt , 'vm');
		
	}
	// input 
	private function _getDecodeData($data='')
	{
	  // as per: http://stackoverflow.com/questions/9262109/php-simplest-two-way-encryption
	  $session = JFactory::getSession(); 	
	  if (empty($data))
	  $data = $session->get('ewaydata', array() , 'vm');
	  if (!function_exists('mcrypt_decrypt'))
	  {
	    return unserialize($data); 
	  }
	  $encrypted = $data; 
	   $id = $session->getId();
	   /*
	   if (method_exists('JUtility', 'getHash'))
	   $key = JUtility::getHash($id.'rand'); 
       else
	   $key =  JApplication::getHash($id.'rand'); 
   */
   $key = $this->generateHash($id); 
	
	   //echo 'key: '.$key."<br />\n"; 
       $result = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key)));
	   $decrypted = rtrim($result, "\0");
	   
	   $data = @unserialize($decrypted); 
	   return $data; 
	   
	}
	
	// input is an object stdClass
	// output is a session save encrypted string
	private function _prepareForSession($sessionAuthorizeNet)
	{
	  // as per: http://stackoverflow.com/questions/9262109/php-simplest-two-way-encryption
	  $string = serialize($sessionAuthorizeNet); 
	  if (!function_exists('mcrypt_decrypt'))
	  {
	    return $dt; 
	  }
	  $session = JFactory::getSession(); 
	 
	  $id = $session->getId();
	  //$key = JUtility::getHash($id.'rand'); 
	  $key = $this->generateHash($id); 
	  
	  //echo 'key: '.$key."<br />\n"; 
	  $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
      return $encrypted; 

	}
	function _getAuthorizeNetFromSession ()
	{

		$session = JFactory::getSession();
		$ewaysess = $session->get('ewaydata', 0, 'vm');
		
		if (!empty($ewaysess)) {
			$ewayData = $this->_getDecodeData($ewaysess); 
			if (empty($ewayData))
			{
			    $this->_cc_cardholder = '';
			  	$this->_cc_type = '';
				$this->_cc_number = '';
				$this->_cc_cvv = '';
				$this->_cc_expire_month = '';
				$this->_cc_expire_year = '';
				$this->_cc_valid = '';

			    return; 
			}
			
			//unserialize($ewaysess);
			$this->_cc_type = $ewayData->cc_type;
			$this->_cc_number = $ewayData->cc_number;
			$this->_cc_cvv = $ewayData->cc_cvv;
			$this->_cc_expire_month = $ewayData->cc_expire_month;
			$this->_cc_expire_year = $ewayData->cc_expire_year;
			$this->_cc_valid = $ewayData->cc_valid;
			$this->_cc_cardholder = $ewayData->cc_cardholder;
			return;
		}
		
			    $this->_cc_type = '';
				$this->_cc_number = '';
				$this->_cc_cvv = '';
				$this->_cc_expire_month = '';
				$this->_cc_expire_year = '';
				$this->_cc_valid = '';
				$this->_cc_cardholder = '';

		
	}

	/**
	 * This is for checking the input data of the payment method within the checkout
	 *
	 * @author Valerie Cartan Isaksen
	 */
	function plgVmOnCheckoutCheckDataPayment (VirtueMartCart $cart)
	{

		if (!$this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}
		$this->_getAuthorizeNetFromSession();
		
        $ret = $this->_validate_creditcard_data(TRUE);
		

	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPaymentPluginTable ($jplugin_id)
	{

		return parent::onStoreInstallPluginTable($jplugin_id);
	}

	/**
	 * This is for adding the input data of the payment method to the cart, after selecting
	 *
	 * @author Valerie Isaksen
	 *
	 * @param VirtueMartCart $cart
	 * @return null if payment not selected; true if card infos are correct; string containing the errors id cc is not valid
	 */
	public function plgVmOnSelectCheckPayment (VirtueMartCart $cart, &$msg)
	{

		if (!$this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}

		//$cart->creditcard_id = JRequest::getVar('creditcard', '0');
		$this->_cc_cardholder = JRequest::getVar('cc_cardholder_' . $cart->virtuemart_paymentmethod_id, '');
		$this->_cc_type = JRequest::getVar('cc_type_' . $cart->virtuemart_paymentmethod_id, '');
		$this->_cc_name = JRequest::getVar('cc_name_' . $cart->virtuemart_paymentmethod_id, '');
		$this->_cc_number = str_replace(" ", "", JRequest::getVar('cc_number_' . $cart->virtuemart_paymentmethod_id, ''));
		
		$this->_cc_cvv = JRequest::getVar('cc_cvv_' . $cart->virtuemart_paymentmethod_id, '');
		$this->_cc_expire_month = JRequest::getVar('cc_expire_month_' . $cart->virtuemart_paymentmethod_id, '');
		$this->_cc_expire_year = JRequest::getVar('cc_expire_year_' . $cart->virtuemart_paymentmethod_id, '');

		if (!$this->_validate_creditcard_data(TRUE)) {
			return FALSE; // returns string containing errors
		}
		
		
		$this->_setAuthorizeNetIntoSession();
		
		return TRUE;
	}

	public function plgVmOnSelectedCalculatePricePayment (VirtueMartCart $cart, array &$cart_prices, &$payment_name)
	{

		if (!($this->_currentMethod = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
			return FALSE;
		}

		$this->_getAuthorizeNetFromSession();
		$cart_prices['payment_tax_id'] = 0;
		$cart_prices['payment_value'] = 0;

		if (!$this->checkConditions($cart, $this->_currentMethod, $cart_prices)) {
			return FALSE;
		}
		$payment_name = $this->renderPluginName($this->_currentMethod);

		$this->setCartPrices($cart, $cart_prices, $this->_currentMethod);

		return TRUE;
	}
	/*
		 * @param $plugin plugin
		 */

	protected function renderPluginName ($plugin)
	{

		$return = '';
		$plugin_name = $this->_psType . '_name';
		$plugin_desc = $this->_psType . '_desc';
		$description = '';
		// 		$params = new JParameter($plugin->$plugin_params);
		// 		$logo = $params->get($this->_psType . '_logos');
		$logosFieldName = $this->_psType . '_logos';
		$logos = $plugin->$logosFieldName;
		if (!empty($logos)) {
			$return = $this->displayLogos($logos) . ' ';
		}
		$sandboxWarning='';
		if ($plugin->sandbox ) {
		$sandboxWarning .= ' <span style="color:red;font-weight:bold">Sandbox (' . $plugin->virtuemart_paymentmethod_id . ')</span><br />';
		}
		if (!empty($plugin->$plugin_desc)) {
			$description = '<span class="' . $this->_type . '_description">' . $plugin->$plugin_desc  . '</span>';
		}
		$this->_getAuthorizeNetFromSession();
		$extrainfo = $this->getExtraPluginNameInfo();
		$pluginName = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' .$description;
		$pluginName .=  $sandboxWarning.$extrainfo;
		return $pluginName;
	}

	/**
	 * Display stored payment data for an order
	 *
	 * @see components/com_virtuemart/helpers/vmPaymentPlugin::plgVmOnShowOrderPaymentBE()
	 */
	function plgVmOnShowOrderBEPayment ($virtuemart_order_id, $virtuemart_payment_id)
	{

		if (!($this->_currentMethod=$this->selectedThisByMethodId($virtuemart_payment_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
			return NULL;
		}
		VmConfig::loadJLang('com_virtuemart');
		VmConfig::loadJLang('plg_vmpayment_authorizenet',TRUE); 
		VmConfig::loadJLang('plg_vmpayment_eway_rupostel',TRUE); 
		$html = '<table class="adminlist">' . "\n";
		$html .= $this->getHtmlHeaderBE();
		
		$html .= $this->getHtmlRowBE('COM_VIRTUEMART_PAYMENT_NAME', $paymentTable->payment_name);
		$html .= $this->getHtmlRowBE('VMPAYMENT_AUTHORIZENET_PAYMENT_ORDER_TOTAL', $paymentTable->payment_order_total . " AUD");
		if (!empty($paymentTable->cost_per_transaction))
		$html .= $this->getHtmlRowBE('VMPAYMENT_AUTHORIZENET_COST_PER_TRANSACTION', $paymentTable->cost_per_transaction);
		
		if (!empty($paymentTable->cost_percent_total) && ($paymentTable->cost_percent_total!='0.00'))
		{
		if (empty($paymentTable->cost_percent_total)) $pt = '0'; 
		else $pt = $paymentTable->cost_percent_total; 
		$html .= $this->getHtmlRowBE('VMPAYMENT_AUTHORIZENET_COST_PERCENT_TOTAL', $pt);
		}
		$code = "eway_response_";
		$reserved = array('eway_response_authorization_code', 'eway_response_response_subcode', 'eway_response_response_reason_code', 'eway_response_transaction_type', 'eway_response_cavv_response', 'eway_response_response_code'); 
		//eway_response_card_type = name
		foreach ($paymentTable as $key => $value) {
			
			
			if (in_array($key, $reserved)) continue; 
			if (substr($key, 0, strlen($code)) == $code) 
			{
			if ($key == 'eway_response_card_code_response')
			    {
				 $value =  "**** **** **** " . $value;
				}
				$html .= $this->getHtmlRowBE(JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_'.strtoupper($key)), $value);
			}
		}
		
		$html .= '</table>' . "\n";
		return $html;
	}

	function plgVmConfirmedOrder (VirtueMartCart $cart, $order)
	{
		VmConfig::loadJLang('plg_vmpayment_authorizenet',TRUE);
		VmConfig::loadJLang('plg_vmpayment_eway_rupostel',TRUE);
		if (!($this->_currentMethod = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
			return FALSE;
		}
		
		
		
		$usrBT = $order['details']['BT'];
		$usrST = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);
		$session = JFactory::getSession();
		$return_context = $session->getId();
		$transaction_key = $this->get_passkey();
		if ($transaction_key === FALSE) {
			return FALSE;
		}

		$payment_currency_id = shopFunctions::getCurrencyIDByName('AUD');
		//stAn, we will not check on currency in this stage
		$totalInPaymentCurrency = round($order['details']['BT']->order_total, 2); //
		
		if (!empty($order['details']['BT']->user_currency_id))
		if ($order['details']['BT']->user_currency_id != $payment_currency_id)
		{
		  $formatted = $totalInPaymentCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total,$payment_currency_id);
		  $totalInPaymentCurrency = $totalInPaymentCurrency['value']; 
		}
		
		
		$cd = CurrencyDisplay::getInstance($cart->pricesCurrency);

		//eWay live start
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'eway_rupostel'.DIRECTORY_SEPARATOR.'eway_files'.DIRECTORY_SEPARATOR."EwayConfig.inc.php");
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'eway_rupostel'.DIRECTORY_SEPARATOR.'eway_files'.DIRECTORY_SEPARATOR.'EwayPaymentLive.php'); 
		$eway = new RupEwayPaymentLive($this->_currentMethod->customer_id, $this->_currentMethod->EWAY_DEFAULT_PAYMENT_METHOD, (!(bool)$this->_currentMethod->sandbox));
		$debug = $this->_currentMethod->debug; 
		if (!empty($this->_currentMethod->sandbox))
		{
		$totalInPaymentCurrency = round($totalInPaymentCurrency, 0); 
		
		}
		// in cents
		$totalInPaymentCurrency = $totalInPaymentCurrency*100; 
		$totalInPaymentCurrency = number_format($totalInPaymentCurrency, 0, '', '');
		$this->_getAuthorizeNetFromSession();
		$eway->setTransactionData("TotalAmount", $totalInPaymentCurrency); //mandatory field
		$eway->setTransactionData("CustomerFirstName", $usrBT->first_name);
		$eway->setTransactionData("CustomerLastName", $usrBT->last_name);
		$eway->setTransactionData("CustomerEmail", $usrBT->email);
		$eway->setTransactionData("CustomerAddress", $usrBT->address_1.' '.$usrBT->address_2);
		$eway->setTransactionData("CustomerPostcode", $usrBT->zip);
		$eway->setTransactionData("CustomerInvoiceDescription", 'Order number: '.$usrBT->order_number.' Order ID: '.$usrBT->virtuemart_order_id);
		$eway->setTransactionData("CustomerInvoiceRef", $usrBT->order_number);
		$eway->setTransactionData("CardHoldersName", $this->_cc_cardholder ); //mandatory field
		$eway->setTransactionData("CardNumber", $this->_cc_number); //mandatory field
		$eway->setTransactionData("CardExpiryMonth", $this->_cc_expire_month); //mandatory field
		$eway->setTransactionData("CardExpiryYear", $this->_cc_expire_year); //mandatory field
		//for REAL_TIME_CVN
		$eway->setTransactionData("CVN", $this->_cc_cvv); //mandatory field
		//
		$eway->setTransactionData("TrxnNumber", $usrBT->order_number);
		$eway->setTransactionData("Option1", "");
		$eway->setTransactionData("Option2", "");
		$eway->setTransactionData("Option3", "");
	
	
	

	//for GEO_IP_ANTI_FRAUD
	$eway->setTransactionData("CustomerIPAddress", $eway->getVisitorIP()); //mandatory field when using Geo-IP Anti-Fraud
	$eway->setTransactionData("CustomerBillingCountry", "AU"); //mandatory field when using Geo-IP Anti-Fraud
	
	
	//special preferences for php Curl
	$eway->setCurlPreferences(CURLOPT_SSL_VERIFYPEER, 0);  //pass a long that is set to a zero value to stop curl from verifying the peer's certificate 
	//$eway->setCurlPreferences(CURLOPT_CAINFO, "/usr/share/ssl/certs/my.cert.crt"); //Pass a filename of a file holding one or more certificates to verify the peer with. This only makes sense when used in combination with the CURLOPT_SSL_VERIFYPEER option. 
	//$eway->setCurlPreferences(CURLOPT_CAPATH, "/usr/share/ssl/certs/my.cert.path");
	//$eway->setCurlPreferences(CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //use CURL proxy, for example godaddy.com hosting requires it
	//$eway->setCurlPreferences(CURLOPT_PROXY, "http://proxy.shr.secureserver.net:3128"); //use CURL proxy, for example godaddy.com hosting requires it
		$hash = $this->getHash(); 
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_order_id from `'.$this->_tablename."` where eway_hash = '".$db->escape($hash)."' limit 0,1"; 
		$db->setQuery($q);
		$res = $db->loadResult(); 
	if (!empty($res))
	 {
	   // prevent double submission

			$html = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_POSSIBLE_DOUBLE_ORDER'); 
			JRequest::setVar('html', $html);
			$this->_clearAuthorizeNetSession();
			$this->_handlePaymentCancel($order['details']['BT']->virtuemart_order_id, $html);
			return; 
	   
	 }
	
	// Prepare data that should be stored in the database
		$dbValues = array(); 
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
		$dbValues['payment_method_id'] = $order['details']['BT']->virtuemart_paymentmethod_id;
		$dbValues['return_context'] = $return_context;
		$dbValues['payment_name'] = $plgname = $this->renderPluginName($this->_currentMethod); //parent::renderPluginName($this->_currentMethod);
		$dbValues['cost_per_transaction'] = $this->_currentMethod->cost_per_transaction;
		$dbValues['cost_percent_total'] = $this->_currentMethod->cost_percent_total;
//		$dbValues['payment_order_total'] = $totalInPaymentCurrency/100;
		$dbValues['payment_currency'] = $this->_currentMethod->payment_currency = $payment_currency_id;
		$dbValues['payment_order_total'] = $this->_currentMethod->payment_order_total = $totalInPaymentCurrency/100;
		$dbValues['eway_response_card_code_response'] = substr($this->_cc_number, -4); 
		$dbValues['eway_hash'] = $this->getHash(); 
		//getHash
		/*
		'eway_response_authorization_code' => 'char(10)',
			'eway_response_transaction_id' => 'char(128)',
			'eway_response_response_code' => 'char(128)',
			'eway_response_response_subcode' => 'char(13)',
			'eway_response_response_reason_code' => 'decimal(10,2)',
			'eway_response_response_reason_text' => 'text',
			'eway_response_transaction_type' => 'char(50)',
			'eway_response_account_number' => 'char(4)',
			'eway_response_card_type' => 'char(128)',
			'eway_response_card_code_response' => 'char(5)',
			'eway_response_cavv_response' => 'char(1)',
			'ewayresponse_raw' => 'text'
			*/
		
		$this->storePSPluginInternalData($dbValues);
		
		//let's wait until we really write the data here: 
		$hash = $this->getHash(); 
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_order_id from `'.$this->_tablename."` where eway_hash = '".$db->escape($hash)."' limit 0,2"; 
		$db->setQuery($q);
		$res = $db->loadAssocList(); 
		$e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
	if (count($res)>1)
	 {
	   // prevent double submission

			$html = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_POSSIBLE_DOUBLE_ORDER'); 
			JRequest::setVar('html', $html);
			$this->_clearAuthorizeNetSession();
			$this->_handlePaymentCancel($order['details']['BT']->virtuemart_order_id, $html);
			return; 
	   
	 }
	 else
	 {
	    if (empty($res))
		 {
		    $html = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_ERROR'); 
			JRequest::setVar('html', $html);
			$this->_clearAuthorizeNetSession();
			$this->_handlePaymentCancel($order['details']['BT']->virtuemart_order_id, $html);
			return; 
		 }
	 }
		
	$error = ''; 
	$html = ''; 
	$xml = ''; 
	$ewayResponseFields = $eway->doPayment($error, $xml);
	VmConfig::loadJLang('com_virtuemart_orders',TRUE);
	if (empty($ewayResponseFields))
	{
			$new_status = $this->_currentMethod->payment_declined_status;
			$html = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_GATEWAY_ERROR'); 
			$html .= $error."<br />\n"; 
			if ($debug)
			$html .= $xml; 
			JRequest::setVar('html', $html);
			
			//$app = JFactory::getApplication();
			//$app->enqueueMessage($html);
			
			$this->_clearAuthorizeNetSession();
			$this->_handlePaymentCancel($order['details']['BT']->virtuemart_order_id, $html);

			return;
	   
	}
	if($ewayResponseFields["EWAYTRXNSTATUS"]=="False"){
			$new_status = $this->_currentMethod->payment_declined_status;
			$html = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_TRANSACTION_ERROR').' ' . $ewayResponseFields["EWAYTRXNERROR"] . "<br>\n";		
			if ($debug)
			{
		    $html .= "eWay URL: ".$eway->myGatewayURL."<br />\n"; 
			foreach($ewayResponseFields as $key => $value)
			 $html .= "\n<br>\$ewayResponseFields[\"$key\"] = $value";
			$html .= 'Sent XML data: '."<br />\n".htmlentities($xml)."<br />"; 
			}
			JRequest::setVar('html', $html);
			$new_status = $this->_currentMethod->payment_declined_status;
			$this->_clearAuthorizeNetSession();
			$this->_handlePaymentCancel($order['details']['BT']->virtuemart_order_id, $html);

			return;
		
	}
	else 
	if($ewayResponseFields["EWAYTRXNSTATUS"]=="True"){
		$this->_clearAuthorizeNetSession();
		$new_status = $this->_currentMethod->payment_approved_status;
		if ($debug)
		foreach($ewayResponseFields as $key => $value)
			$html .= "\n<br>\$ewayResponseFields[\"$key\"] = $value";
		
	}
	$this->_clearAuthorizeNetSession();
	if (!isset($new_status))
	{
	  $html = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_UNKNOWN_ERROR'); 
	  $html .= var_export($ewayResponseFields, true); 
	  JRequest::setVar('html', $html);
	  return; 
	  
	}
		$modelOrder = VmModel::getModel('orders');
		$order['order_status'] = $new_status;
		$order['customer_notified'] = 1;
		$order['comments'] = '';
		$modelOrder->updateStatusForOneOrder($order['details']['BT']->virtuemart_order_id, $order, TRUE);
		//We delete the old stuff
		if (!class_exists('CurrencyDisplay'))
		require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		
		$currencyDisplay = CurrencyDisplay::getInstance ('', $order['details']['BT']->virtuemart_vendor_id);
		$responsemsg = $ewayResponseFields["EWAYTRXNERROR"]; 
		$html .= $this->renderByLayout('thankyou', array('payment' => $this->_currentMethod, 'eway'=>$eway, 'vmid'=>$this->_currentMethod->virtuemart_paymentmethod_id, 'totalInPaymentCurrencyCents'=>$totalInPaymentCurrency, 'order'=>$order, 'cart'=>$cart, 'currencyDisplay'=>$currencyDisplay, 'payment_name'=>$plgname, 'responsemsg'=>$responsemsg));
		$cart->emptyCart();
		JRequest::setVar('html', $html);
	
	
		$dbValues['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
		
		
		$code = ''; 
		$msg = $ewayResponseFields["EWAYTRXNERROR"];
		$dbValues['eway_response_card_code_response'] = substr($this->_cc_number, -4); 
		$dbValues['eway_response_response_reason_text'] = $msg; 
		$dbValues['eway_response_response_reason_code'] = $code; 
		$dbValues['eway_response_transaction_id'] = $ewayResponseFields['EWAYTRXNNUMBER']; 
		$dbValues['eway_response_account_number'] = $ewayResponseFields['EWAYAUTHCODE']; 
		if (function_exists('mb_substr'))
		$dbValues['eway_response_card_type'] = mb_substr($this->_cc_cardholder, 0,128); 
		else
		$dbValues['eway_response_card_type'] = substr($this->_cc_cardholder, 0,128); 
		//EWAYAUTHCODE
		//EWAYTRXNNUMBER
		$dbValues['ewayresponse_raw'] = json_encode($ewayResponseFields);
		/*
		'eway_response_authorization_code' => 'char(10)',
			'eway_response_transaction_id' => 'char(128)',
			'eway_response_response_code' => 'char(128)',
			'eway_response_response_subcode' => 'char(13)',
			'eway_response_response_reason_code' => 'decimal(10,2)',
			'eway_response_response_reason_text' => 'text',
			'eway_response_transaction_type' => 'char(50)',
			'eway_response_account_number' => 'char(4)',
			'eway_response_card_type' => 'char(128)',
			'eway_response_card_code_response' => 'char(5)',
			'eway_response_cavv_response' => 'char(1)',
			'ewayresponse_raw' => 'text'
			*/
		$this->storePSPluginInternalData($dbValues, 'virtuemart_order_id', TRUE);

	
	
	
	
	
	
	
		//eWay live end
	}

	function _handlePaymentCancel ($virtuemart_order_id, $html)
	{

		if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'orders.php');
		}
		$modelOrder = VmModel::getModel('orders');
		$modelOrder->remove(array('virtuemart_order_id' => $virtuemart_order_id));
		// error while processing the payment
		$mainframe = JFactory::getApplication();
		$mainframe->enqueueMessage($html);
		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment',FALSE), JText::_('COM_VIRTUEMART_CART_ORDERDONE_DATA_NOT_VALID'));
	}

	
	function _clearAuthorizeNetSession ()
	{

		$session = JFactory::getSession();
		$session->clear('ewaydata', 'vm');
	}
	private function generateHash($id) {
		  if (method_exists('JUtility', 'getHash'))
	   $key = JUtility::getHash($id.'rand'); 
       else
	   $key =  JApplication::getHash($id.'rand'); 
   
       return $key; 
	}
	function getHash()
	{
	  $session = JFactory::getSession();
	  $x = JRequest::getVar('opcmc', ''); 
	  if (empty($x))
	   {
	      $x = $session->get('opcmc', ''); 
	   }
	   return $x; 
	}
	function getHashHtml($cart)
	{
	   $md5 = md5(serialize($cart)); 
	   $val = microtime(true).'_'.$md5; 
	   $ret = '<input type="hidden" name="opcmc" value="'.$val.'" />'; 
	   $session = JFactory::getSession(); 
	   $session->set('opcmc', $val); 
	   return $ret; 
	}
	/**
	 * renderPluginName
	 * Get the name of the payment method
	 *
	 * @author Valerie Isaksen
	 * @param  $payment
	 * @return string Payment method name
	 */
	function getExtraPluginNameInfo ()
	{
		VmConfig::loadJLang('plg_vmpayment_authorizenet',TRUE);
		$creditCardInfos = '';
		if ($this->_validate_creditcard_data(FALSE)) {
			$cc_number = "**** **** **** " . substr($this->_cc_number, -4);
			$creditCardInfos .= '<br /><span class="vmpayment_cardinfo">' . JText::_('VMPAYMENT_AUTHORIZENET_CCTYPE') . $this->_cc_type . '<br />';
			$creditCardInfos .= JText::_('VMPAYMENT_AUTHORIZENET_CCNUM') . $cc_number . '<br />';
			$creditCardInfos .= JText::_('VMPAYMENT_AUTHORIZENET_CVV2') . '****' . '<br />';
			$creditCardInfos .= JText::_('VMPAYMENT_AUTHORIZENET_EXDATE') . $this->_cc_expire_month . '/' . $this->_cc_expire_year;
			$creditCardInfos .= "</span>";
		}
		return $creditCardInfos;
	}

	/**
	 * Creates a Drop Down list of available Creditcards
	 *
	 * @author Valerie Isaksen
	 */
	function _renderCreditCardList ($creditCards, $selected_cc_type, $paymentmethod_id, $multiple = FALSE, $attrs = '')
	{

		$idA = $id = 'cc_type_' . $paymentmethod_id;
		//$options[] = JHTML::_('select.option', '', JText::_('VMPAYMENT_eway_SELECT_CC_TYPE'), 'creditcard_type', $name);
		if (!is_array($creditCards)) {
			$creditCards = (array)$creditCards;
		}
		foreach ($creditCards as $creditCard) {
			$options[] = JHTML::_('select.option', $creditCard, JText::_('VMPAYMENT_AUTHORIZENET_' . strtoupper($creditCard)));
		}
		if ($multiple) {
			$attrs = 'multiple="multiple"';
			$idA .= '[]';
		}
		return JHTML::_('select.genericlist', $options, $idA, $attrs, 'value', 'text', $selected_cc_type);
	}

	/*
		 * validate_creditcard_data
		 * @author Valerie isaksen
		 */

	function _validate_creditcard_data ($enqueueMessage = TRUE)
	{
	   VmConfig::loadJLang('plg_vmpayment_authorizenet',TRUE);
		$html = '';
		$this->_cc_valid = TRUE;

		if (!Creditcard::validate_credit_card_number($this->_cc_type, $this->_cc_number)) {
			$this->_errormessage[] = 'VMPAYMENT_AUTHORIZENET_CARD_NUMBER_INVALID';
			$this->_cc_valid = FALSE;
		}

		if (!Creditcard::validate_credit_card_cvv($this->_cc_type, $this->_cc_cvv)) {
			$this->_errormessage[] = 'VMPAYMENT_AUTHORIZENET_CARD_CVV_INVALID';
			$this->_cc_valid = FALSE;
		}
		
		if (!Creditcard::validate_credit_card_date($this->_cc_type, $this->_cc_expire_month, $this->_cc_expire_year)) {
			$this->_errormessage[] = 'VMPAYMENT_AUTHORIZENET_CARD_EXPIRATION_DATE_INVALID';
			$this->_cc_valid = FALSE;
		}
		
		if (!$this->_cc_valid) {
			//$html.= "<ul>";
			foreach ($this->_errormessage as $msg) {
				//$html .= "<li>" . Jtext::_($msg) . "</li>";
				$html .= Jtext::_($msg) . "<br/>";
			}
			//$html.= "</ul>";
		}
		
		if (!$this->_cc_valid && $enqueueMessage) {
			$app = JFactory::getApplication();
			$app->enqueueMessage($html);
		}

		return $this->_cc_valid;
	}




	
	/**
	 * displays the CVV images of for CVV tooltip plugin
	 *
	 * @author Valerie Isaksen
	 * @param array $logo_list
	 * @return html with logos
	 */
	public function _displayCVVImages ($method)
	{

		$cvv_images = $method->cvv_images;
		$img = '';
		if ($cvv_images) {
			$img = $this->displayLogos($cvv_images);
			$img = str_replace('"', "'", $img);
		}
		return $img;
	}

	/**
	 * We must reimplement this triggers for joomla 1.7
	 */

	/**
	 * plgVmOnCheckAutomaticSelectedPayment
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 *
	 * @author Valerie Isaksen
	 * @param VirtueMartCart cart: the cart object
	 * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
	 *
	 */

	function plgVmOnCheckAutomaticSelectedPayment (VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter) {

		return $this->onCheckAutomaticSelected ($cart, $cart_prices, $paymentCounter);
	}

	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the method-specific data.
	 *
	 * @param integer $order_id The order ID
	 * @return mixed Null for methods that aren't active, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	protected function plgVmOnShowOrderFEPayment ($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name)
	{

		$this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
		return TRUE;
	}

	/**
	 * This method is fired when showing when priting an Order
	 * It displays the the payment method-specific data.
	 *
	 * @param integer $_virtuemart_order_id The order ID
	 * @param integer $method_id  method used for this order
	 * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	function plgVmOnShowOrderPrintPayment ($order_number, $method_id)
	{

		return parent::onShowOrderPrint($order_number, $method_id);
	}

	
	function plgVmDeclarePluginParamsPayment ($name, $id, &$data)
	{

		return $this->declarePluginParams('payment', $name, $id, $data);
	}

	function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table)
	{

		return $this->setOnTablePluginParams($name, $id, $table);
	}
	
	
	function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($this->_currentMethod = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($this->_currentMethod->payment_element)) {
			return FALSE;
		}
		$paymentCurrencyId = shopFunctions::getCurrencyIDByName('AUD');
		
	}
function plgVmDeclarePluginParamsPaymentVM3( &$data) {
		return $this->declarePluginParams('payment', $data);
	}
	/**
	 * @param $virtuemart_paymentmethod_id
	 * @param $paymentCurrencyId
	 * @return bool|null
	 */
	function plgVmgetEmailCurrency($virtuemart_paymentmethod_id, $virtuemart_order_id, &$emailCurrencyId) {

		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return FALSE;
		}
		if (!($payments = $this->getDatasByOrderId($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}
		if (empty($payments[0]->email_currency)) {
			$vendorId = 1; //VirtueMartModelVendor::getLoggedVendor();
			$db = JFactory::getDBO();
			$q = 'SELECT   `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`=' . $vendorId;
			$db->setQuery($q);
			$emailCurrencyId = $db->loadResult();
		} else {
			$emailCurrencyId = $payments[0]->email_currency;
		}

	}
	
	
	
	
}

// No closing tag
