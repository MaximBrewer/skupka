<?php

defined ('_JEXEC') or die('Restricted access');

/**
 *
 * a special type of 'paypal ':
 *
 * @author Max Milbers
 * @author Valérie Isaksen
 * @version $Id: paypal.php 5177 2011-12-28 18:44:10Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
if (!class_exists ('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmpsplugin.php');
}

class plgVmPaymentCertitrade extends vmPSPlugin {

	// instance of class
	public static $_this = FALSE;

	function __construct (& $subject, $config) {

		//if (self::$_this)
		//   return self::$_this;
		parent::__construct ($subject, $config);

		$this->_loggable = TRUE;
		$this->tableFields = array_keys ($this->getTableSQLFields ());
		$this->_tablepkey = 'id'; 
		$this->_tableId = 'id'; 
		$varsToPush = $this->getVarsToPush ();
		
		$this->setConfigParameterable ($this->_configTableFieldName, $varsToPush);

		//self::$_this = $this;
	}

	/**
	 * @return string
	 */
	public function getVmPluginCreateTableSQL () {

		return $this->createTableSQL ('Payment Certitrade Table');
	}

	/**
	 * @return array
	 */
	function getTableSQLFields () {

		$SQLfields = array(
			'id'                                     => 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id'                    => 'int(1) UNSIGNED',
			'order_number'                           => 'char(64)',
			'virtuemart_paymentmethod_id'            => 'mediumint(1) UNSIGNED',
			'payment_name'                           => 'varchar(5000)',
			'payment_order_total'                    => 'decimal(15,5) NOT NULL',
			'payment_currency'                       => 'smallint(1)',
			'email_currency'                         => 'smallint(1)',
			'cost_per_transaction'                   => 'decimal(10,2)',
			'cost_percent_total'                     => 'decimal(10,2)',
			'tax_id'                                 => 'smallint(1)',
			 'paytrans_id' => 'int(11) unsigned NOT NULL ',
  'payment_processor' => 'varchar(40) NOT NULL default \'\'',
  'cart_id_number' => 'varchar(40) NOT NULL default \'\'',
  'customers_id' => 'varchar(40) NOT NULL',
  'trans_order_id' => 'varchar(40) default NULL',
  'orders_id' => 'int(11) default NULL',
  'amount' => 'decimal(15,4) default NULL',
  'currency' => 'char(3) default NULL',
  'result' => 'char(8) default NULL',
  'result_code' => 'tinytext',
  'bank_code' => 'tinytext',
  'trnumber' => 'tinytext',
  'authcode' => 'tinytext',
  'pay_result' => 'tinytext',
  'pay_counter' => 'int(11) default 0',
  'cc_descr' => 'tinytext',
  'created' => 'varchar(30) default NULL',
  'completed' => 'varchar(30) default NULL',
  'temp_data' => 'mediumtext',
		);
		return $SQLfields;
	}

	/**
	 * @param $cart
	 * @param $order
	 * @return bool|null
	 */
	function plgVmConfirmedOrder ($cart, $order) {

		if (!($method = $this->getVmPluginMethod ($order['details']['BT']->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
	
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}
		$session = JFactory::getSession ();
		$return_context = $session->getId ();
		$this->_debug = $method->CERTI_TESTMODE;
		$this->logInfo ('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'orders.php');
		}
		if (!class_exists ('VirtueMartModelCurrency')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'currency.php');
		}

		$address = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);

		if (!class_exists ('TableVendors')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'table' .DIRECTORY_SEPARATOR. 'vendors.php');
		}
		$vendorModel = VmModel::getModel ('Vendor');
		$vendorModel->setId (1);
		$vendor = $vendorModel->getVendor ();
		$vendorModel->addImages ($vendor, 1);
		$this->getPaymentCurrency ($method);
		$email_currency = $this->getEmailCurrency ($method);
		$currency_code_3 = shopFunctions::getCurrencyByID ($method->payment_currency, 'currency_code_3');

		$paymentCurrency = CurrencyDisplay::getInstance ($method->payment_currency);
		$method->payment_currency = 'SEK'; 
		$totalInPaymentCurrency = round ($paymentCurrency->convertCurrencyTo ('SEK', $order['details']['BT']->order_total, FALSE), 2);
		$cd = CurrencyDisplay::getInstance ($cart->pricesCurrency);
		if ($totalInPaymentCurrency <= 0) {
			vmInfo (JText::_ ('VMPAYMENT_PAYPAL_PAYMENT_AMOUNT_INCORRECT'));
			return FALSE;
		}

		
		$d = array(); 
		$d['ship_to_info_id'] = $order['details']['BT']->virtuemart_order_userinfo_id; 
		// prevent second call tp ps_checkout::add() to pick another order_number
      	   $d['order_number'] = $order_number = $order['details']['BT']->order_number;
   	       $d_commasep = "";	
   	       foreach ($d as $key => $value) {
              //echo "Parameter: $key; Value: $value<br />\n";
              $d_commasep .= $key ."," . $value .";";
           }	
		   
		   $order_total = $totalInPaymentCurrency; 
		   
		   
   	      $now=date("Y-m-d H:i:s");
   	     
	     
		
		
		

		
				  

		

		
	  // From Customer Name and Billing Address
      $cust_phone = $address->phone_1;
      $cust_email = $address->email;
   
   // Customer Shipping Address
	 $cust_name = $address->first_name . " " . $address->last_name;
	 if (isset($address->company))
	 $company = $address->company;
	 else $company = ''; 
	 
	 if ($company != ""){
	   $cust_name .= " ". $company;	
	 }
	 $cust_address1  = $address->address_1;
	 $cust_city      = $address->city;
	 if (isset($address->state))
	 $cust_address2  = $address->state;
	 else $cust_address2  = '';
	 
	 $cust_zip       = $address->zip;
	 if ((isset($address->virtuemart_country_id) && (is_numeric($address->virtuemart_country_id))))
	 {
	 $q = 'select country_3_code from #__virtuemart_countries where virtuemart_country_id = "'.$address->virtuemart_country_id.'"'; 
	 $dbj = JFactory::getDBO(); 
	 $dbj->setQuery($q); 
	 $c2 = $dbj->loadResult(); 
	 $cust_country   = $c2;
	 }
	 else $cust_country = 'SWE'; 
     $cust_address3  = "";

     $valuta = $method->payment_currency;
	 if (empty($valuta)) $valuta = 'SEK'; 
	 
	if ( $valuta == "SEK"){
	$curr_code = "752";
	} else {
		
		if (!class_exists ('VirtueMartModelCurrency')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'currency.php');
		}
		$paymentCurrency = CurrencyDisplay::getInstance ($method->payment_currency);
		$totalInPaymentCurrency = round ($paymentCurrency->convertCurrencyTo ('SEK', $order['details']['BT']->order_total, FALSE), 0);
	/*
		echo "Valutan". $valuta . "stöds ej fn utan endast SEK";
	return false;
	*/
	}
	
	 $cost_dotsep    = $order_total = $totalInPaymentCurrency;
	 // Prepare data that should be stored in the database
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['payment_name'] = $this->renderPluginName ($method, $order);
		$dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;
		$dbValues['paypal_custom'] = $return_context;
		$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
		$dbValues['cost_percent_total'] = $method->cost_percent_total;
		$dbValues['payment_currency'] = $method->payment_currency;
		$dbValues['email_currency'] = $email_currency;
		$dbValues['payment_order_total'] = $totalInPaymentCurrency;
		$dbValues['tax_id'] = $method->tax_id;
		$dbValues['trans_order_id'] = $order_number; 
		$dbValues['amount'] = $order_total;
		$dbValues['currency'] = $method->payment_currency;
		$dbValues['created'] = $now; 
		$dbValues['temp_data'] = $d_commasep;
		$dbValues['customers_id'] = $d['ship_to_info_id'];
		
		$this->storePSPluginInternalData ($dbValues);
if ($method->CERTI_TESTMODE == 'Y'){
  $url = "https://www.certitrade.net/webshophtml/e/auth.php";	
  //$url = "https://www.certitrade.net/webshophtml/e/eko.php";		
  $md5key = "AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHH";
  //unique MD5 hash
  $orderid = $order_number;
} else {
  $url = "https://payment.certitrade.net/webshophtml/e/auth.php";	
  $md5key = $method->CERTI_MD5;
  //easy sequence number, 1,2,3,4 etc
  $orderid = $order_number;
}
$lang = "sv";
$lang_codes = array("danish" => "da","swedish" => "sv","norwegian" =>"no","english" => "en");
//if(isset($lang_codes[$mosConfig_lang]))
//$lang = $lang_codes[$mosConfig_lang];

$rev = "E";
$debug = 0;
$httpdebug = 0;
$returwindow ="";
$cust_id = "";

$thisurl   = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$baseurl   = str_replace('certitrade_post.php','',$thisurl);

$baseurl = JURI::root(); 

$retururl   = JROUTE::_ (JURI::root () . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id . '&Itemid=' . JRequest::getInt ('Itemid'));
//$approveurl = $baseurl ."certitrade_vm.php?mode=approve";
$approveurl = JROUTE::_ (JURI::root () . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&no_sef=1&nosef=1&pm='.$order['details']['BT']->virtuemart_paymentmethod_id.'&mode=approve&custom='.$return_context);
//$declineurl = $baseurl ."certitrade_vm.php?mode=decline";
$declineurl = JROUTE::_ (JURI::root () . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&no_sef=1&nosef=1&pm='.$order['details']['BT']->virtuemart_paymentmethod_id.'&mode=decline');

//$cancelurl  = $baseurl ."certitrade_vm.php?mode=cancel";
$cancelurl = JROUTE::_ (JURI::root () . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&no_sef=1&nosef=1&pm='.$order['details']['BT']->virtuemart_paymentmethod_id.'&mode=cancel');

$md5str = $method->CERTI_MID;
$md5str .= $rev;
$md5str .= $orderid;
$md5str .= $order_total;
$md5str .= $curr_code;
$md5str .= $retururl;
$md5str .= $approveurl;
$md5str .= $declineurl;
$md5str .= $cancelurl;
$md5str .= $returwindow;
$md5str .= $lang;
$md5str .= $cust_id;
$md5str .= $cust_name;
$md5str .= $cust_address1;
$md5str .= $cust_address2;
$md5str .= $cust_address3;
$md5str .= $cust_zip;
$md5str .= $cust_city;
$md5str .= $cust_phone;
$md5str .= $cust_email;
$md5str .= $debug;
$md5str .= $httpdebug;

$md5code = md5($md5key.$md5str);

$post_variables = Array(
"merchantid"    => $method->CERTI_MID,
"md5code"       => $md5code,
"rev"           => $rev,
"orderid"       => $orderid,
"amount"        => $order_total,
"currency"      => $curr_code,
"retururl"      => $retururl,
"cancelurl"     => $cancelurl,
"declineurl"    => $declineurl,
"approveurl"    => $approveurl,
"returwindow"   => $returwindow,
"lang"          => $lang,
"cust_id"       => $cust_id,
"cust_name"     => $cust_name,
"cust_address1" => $cust_address1,
"cust_address2" => $cust_address2,
"cust_address3" => $cust_address3,
"cust_zip"      => $cust_zip,
"cust_city"     => $cust_city,
"cust_phone"    => $cust_phone,
"cust_email"    => $cust_email,
"DEBUG"         => $debug,
"HTTPDEBUG"     => $httpdebug);

     //build the post string
	 $poststring = '';
	 foreach($post_variables AS $key => $val){
	   $poststring .= "<input type='hidden' name='$key' value='$val' />";
	 }

$html = '
<html><head></head><body>
<form id="ctform" name="ctform" action="'.$url.'" method="post">'.$poststring.'</form>
<script type="text/javascript" language="JavaScript">
    document.ctform.submit();
</script>
</body></html>'; 

		
		
		

		// 	2 = don't delete the cart, don't send email and don't redirect
		$cart->_confirmDone = FALSE;
		$cart->_dataValidated = FALSE;
		$cart->setCartIntoSession ();
		JRequest::setVar ('html', $html);

	}

	/**
	 * @param $virtuemart_paymentmethod_id
	 * @param $paymentCurrencyId
	 * @return bool|null
	 */
	function plgVmgetPaymentCurrency ($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}
		$this->getPaymentCurrency ($method);
		$paymentCurrencyId = $method->payment_currency;
	}



	/**
	 * @param $html
	 * @return bool|null|string
	 */
	function plgVmOnPaymentResponseReceived (&$html) {

		if (!class_exists ('VirtueMartCart')) {
			require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		}
		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctionsf.php');
		}
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'orders.php');
		}

		//vmdebug('PAYPAL plgVmOnPaymentResponseReceived', $paypal_data);
		// the payment itself should send the parameter needed.
		$virtuemart_paymentmethod_id = JRequest::getInt ('pm', 0);
		$order_number = JRequest::getString ('on', 0);
		$vendorId = 0;
		if (!($method = $this->getVmPluginMethod ($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return NULL;
		}

		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($order_number))) {
			return NULL;
		}
		
		$dbj = JFactory::getDBO();
		$q = "select * from #__virtuemart_payment_plg_certitrade where virtuemart_order_id = '".(int)$virtuemart_order_id."' order by id desc limit 0,1"; 
		$dbj->setQuery($q); 
		$res = $dbj->loadObject(); 
		
		
		
		if (!($paymentTable = $this->getDataByOrderId ($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}
		$payment_name = $this->renderPluginName ($method);
		$html = $this->_getPaymentResponseHtml ($res, $payment_name);

		//We delete the old stuff
		// get the correct cart / session
		$cart = VirtueMartCart::getCart ();
		if (!empty($html))
		$cart->emptyCart ();
		return TRUE;
	}

	/**
	 * @return bool|null
	 */
	function plgVmOnUserPaymentCancel () {

		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'orders.php');
		}

		$order_number = JRequest::getString ('on', '');
		$virtuemart_paymentmethod_id = JRequest::getInt ('pm', '');
		if (empty($order_number) or empty($virtuemart_paymentmethod_id) or !$this->selectedThisByMethodId ($virtuemart_paymentmethod_id)) {
			return NULL;
		}
		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($order_number))) {
			return NULL;
		}
		if (!($paymentTable = $this->getDataByOrderId ($virtuemart_order_id))) {
			return NULL;
		}

		VmInfo (Jtext::_ ('VMPAYMENT_CERTITRADE_PAYMENT_CANCELLED'));
		$session = JFactory::getSession ();
		$return_context = $session->getId ();
		if (strcmp ($paymentTable->paypal_custom, $return_context) === 0) {
			$this->handlePaymentUserCancel ($virtuemart_order_id);
		}
		return TRUE;
	}

	/*
		 *   plgVmOnPaymentNotification() - This event is fired by Offline Payment. It can be used to validate the payment data as entered by the user.
		 * Return:
		 * Parameters:
		 *  None
		 *  @author Valerie Isaksen
		 */

	/**
	 * @return bool|null
	 */
	function plgVmOnPaymentNotification () {

		//$this->_debug = true;
		if (!class_exists ('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'orders.php');
		}
		$paypal_data = JRequest::get ('post');
		
		$order_number = $paypal_data['orderid'];
		if (!($virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber ($paypal_data['orderid']))) {
			return NULL;
		}

		$vendorId = 0;
		if (!($payments = $this->getDatasByOrderId ($virtuemart_order_id))) {
			return NULL;
		}
		
		$method = $this->getVmPluginMethod ($payments[0]->virtuemart_paymentmethod_id);
		if (!$this->selectedThisElement ($method->payment_element)) {
			return FALSE;
		}
		$virtuemart_paymentmethod_id = $payments[0]->virtuemart_paymentmethod_id; 
		
		 // Parameters from CertiTrade in posting
  $ct_md5code       = $paypal_data['md5code'];
  $ct_merchantid    = $paypal_data['merchantid'];
  $ct_ordertrans_id = $paypal_data['orderid'];
  $ct_amount        = $paypal_data['amount'];
  $ct_currency      = $paypal_data['currency'];
  $ct_result        = $paypal_data['result'];
  $ct_result_code   = $paypal_data['result_code'];
  $ct_bank_code     = $paypal_data['bank_code'];
  $ct_trnumber      = $paypal_data['trnumber'];
  $ct_authcode      = $paypal_data['authcode'];
  $lang             = $paypal_data['lang'];
  $ch_name          = $paypal_data['ch_name'];
  $ch_address1      = $paypal_data['ch_address1'];
  $ch_address2      = $paypal_data['ch_address2'];
  $ch_address3      = $paypal_data['ch_address3'];
  $ch_zip           = $paypal_data['ch_zip'];
  $ch_city          = $paypal_data['ch_city'];
  $ch_phone         = $paypal_data['ch_phone'];
  $ch_email         = $paypal_data['ch_email'];
  $cc_descr         = $paypal_data['cc_descr'];
  //--
  $md5str  = $ct_merchantid;
  $md5str .= $ct_ordertrans_id;
  $md5str .= $ct_amount;
  $md5str .= $ct_currency;
  $md5str .= $ct_result;
  $md5str .= $ct_result_code;
  $md5str .= $ct_bank_code;
  $md5str .= $ct_trnumber;
  $md5str .= $ct_authcode;
  $md5str .= $lang;
  $md5str .= $ch_name;
  $md5str .= $ch_address1;
  $md5str .= $ch_address2;
  $md5str .= $ch_address3;
  $md5str .= $ch_zip;
  $md5str .= $ch_city;
  $md5str .= $ch_phone;
  $md5str .= $ch_email;
		
		 // SELECTING MD5 KEY
  // In Demo Mode the MD5 Hash is AAAA...
  if ( $method->CERTI_TESTMODE == 'Y'){
	$md5key = "AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHH";
  } else {
    $md5key = CERTI_MD5;
  }
  
  $calculated_md5code=md5($md5key.$md5str);
  $mode = JRequest::getVar('mode', 'cancel'); 
  
   if ($calculated_md5code != $ct_md5code ){
    $payment_result_string = "MD5_ERROR";
    exit;
  }else if ($mode=="approve"){
		// APPROVE
		$inc_ok=0;
		if ($ct_result_code =="00") $inc_ok=1;
		if (!$ct_authcode) $inc_ok=0;
		if ($inc_ok == 1){
		  $payment_result_string = "APPROVED";
		} else {
		  $payment_result_string = "FAIL_APPROVE";
		}		
	}else if ($mode=="decline"){
	    //DECLINE	
	    $payment_result_string = "DECLINE";
	}else if ($mode=="cancel"){
	    //CANCEL
	    $payment_result_string = "CANCEL";
  }	 
  if (empty($payment_result_string)) $payment_result_string = 'UNKNOWN'; 
		
		$db = JFactory::getDBO(); 
		$q1 = "SELECT pay_result, pay_counter FROM #__vm_paytrans WHERE trans_order_id='".$order_number."'";
         $db->setQuery($q1); 
         
	     $result_row1 = $db->loadAssoc(); 
	  
         $pay_counter     = $result_row1["pay_counter"];
         $pay_result      = $result_row1["pay_result"];
         $allow_update = true;
         
		 if ($pay_result == 'APPROVED'){
			  $allow_update = false;	
		 } else {
			  $pay_counter++;	
		 }	
		 		 
		
		
		$this->_debug = $method->debug;
		$this->logInfo ('paypal_data ' . implode ('   ', $paypal_data), 'message');

		//$this->_storePaypalInternalData ($method, $paypal_data, $virtuemart_order_id, $payment->virtuemart_paymentmethod_id);
		$modelOrder = VmModel::getModel ('orders');
		$order = array();

		
		$lang = JFactory::getLanguage ();
		$order['customer_notified'] = 1;

		// 1. check the payment_status is Completed
		if (strcmp ($payment_result_string, 'APPROVED') == 0) {
			// 2. check that txn_id has not been previously processed
			// 3. check email and amount currency is correct
			// now we can process the payment
			$order['order_status'] = $method->CERTI_VERIFIED_STATUS;
			//$order['comments'] = JText::sprintf ('VMPAYMENT_CERTITRADE_PAYMENT_STATUS_CONFIRMED', $order_number);
		} else {
			$order['order_status'] = $method->CERTI_INVALID_STATUS;
		} 
		
		//$response_fields[$this->_tablepkey] = $this->_getTablepkeyValue($virtuemart_order_id);
		$response_fields['payment_name'] = $this->renderPluginName ($method);
		$response_fields['paypalresponse_raw'] = $post_msg;
		$response_fields['order_number'] = $order_number;
		$response_fields['virtuemart_order_id'] = $virtuemart_order_id;
		$response_fields['virtuemart_paymentmethod_id'] = $virtuemart_paymentmethod_id;
		$response_fields['paypal_custom'] = $paypal_data['custom'];

           $now=date("Y-m-d H:i:s");
   	       //$q2  = "UPDATE  #__vm_paytrans SET 
		   $response_fields['pay_result'] = $payment_result_string;
		   $response_fields['result'] = $ct_result;
		   $response_fields['bank_code'] = $ct_bank_code;
		   $response_fields['trnumber'] = $ct_trnumber;
		   $response_fields['authcode'] = $ct_authcode;
		   $response_fields['cc_descr'] = $cc_descr;
		   $response_fields['completed'] = $now;
		   
			
		  
		 
         
		//$preload=true   preload the data here too preserve not updated data
		$this->storePSPluginInternalData ($response_fields);
		

		$modelOrder->updateStatusForOneOrder ($virtuemart_order_id, $order, TRUE);
		//// remove vmcart
		
		if (false)
		if (isset($paypal_data['custom'])) {
			$this->emptyCart ($paypal_data['custom'], $order_number);
		}
		
	}


	/**
	 * @param $method
	 * @param $paypal_data
	 * @param $virtuemart_order_id
	 */
	function _storePaypalInternalData ($method, $paypal_data, $virtuemart_order_id, $virtuemart_paymentmethod_id) {

		// get all know columns of the table
		$db = JFactory::getDBO ();
		$query = 'SHOW COLUMNS FROM `' . $this->_tablename . '` ';
		$db->setQuery ($query);
		$columns = $db->loadResultArray (0);
		$post_msg = '';
		foreach ($paypal_data as $key => $value) {
			$post_msg .= $key . "=" . $value . "<br />";
			$table_key = 'paypal_response_' . $key;
			if (in_array ($table_key, $columns)) {
				$response_fields[$table_key] = $value;
			}
		}

		//$response_fields[$this->_tablepkey] = $this->_getTablepkeyValue($virtuemart_order_id);
		$response_fields['payment_name'] = $this->renderPluginName ($method);
		$response_fields['paypalresponse_raw'] = $post_msg;
		$response_fields['order_number'] = $paypal_data['invoice'];
		$response_fields['virtuemart_order_id'] = $virtuemart_order_id;
		$response_fields['virtuemart_paymentmethod_id'] = $virtuemart_paymentmethod_id;
		$response_fields['paypal_custom'] = $paypal_data['custom'];

		//$preload=true   preload the data here too preserve not updated data
		$this->storePSPluginInternalData ($response_fields);
	}

	/**
	 * Display stored payment data for an order
	 *
	 * @see components/com_virtuemart/helpers/vmPSPlugin::plgVmOnShowOrderBEPayment()
	 */
	function plgVmOnShowOrderBEPayment ($virtuemart_order_id, $payment_method_id) {

		if (!$this->selectedThisByMethodId ($payment_method_id)) {
			return NULL; // Another method was selected, do nothing
		}

		if (!($payments = $this->_getPaypalInternalData ($virtuemart_order_id))) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}

		$html = '<table class="adminlist" width="50%">' . "\n";
		$html .= $this->getHtmlHeaderBE ();
		$code = "paypal_response_";
		$first = TRUE;
		foreach ($payments as $payment) {
			$html .= '<tr class="row1"><td>' . JText::_ ('VMPAYMENT_PAYPAL_DATE') . '</td><td align="left">' . $payment->created_on . '</td></tr>';
			// Now only the first entry has this data when creating the order
			if ($first) {
				$html .= $this->getHtmlRowBE ('PAYPAL_PAYMENT_NAME', $payment->payment_name);
				// keep that test to have it backwards compatible. Old version was deleting that column  when receiving an IPN notification
				if ($payment->payment_order_total and  $payment->payment_order_total != 0.00) {
					$html .= $this->getHtmlRowBE ('PAYPAL_PAYMENT_ORDER_TOTAL', $payment->payment_order_total . " " . shopFunctions::getCurrencyByID ($payment->payment_currency, 'currency_code_3'));
				}
				
				$first = FALSE;
			}
			foreach ($payment as $key => $value) {
				// only displays if there is a value or the value is different from 0.00 and the value
				if ($value) {
					if (substr ($key, 0, strlen ($code)) == $code) {
						$html .= $this->getHtmlRowBE ($key, $value);
					}
				}
			}

		}
		$html .= '</table>' . "\n";
		return $html;
	}

	/**
	 * @param        $virtuemart_order_id
	 * @param string $order_number
	 * @return mixed|string
	 */
	function _getPaypalInternalData ($virtuemart_order_id, $order_number = '') {

		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `' . $this->_tablename . '` WHERE ';
		if ($order_number) {
			$q .= " `order_number` = '" . $order_number . "'";
		} else {
			$q .= ' `virtuemart_order_id` = ' . $virtuemart_order_id;
		}

		$db->setQuery ($q);
		if (!($payments = $db->loadObjectList ())) {
			// JError::raiseWarning(500, $db->getErrorMsg());
			return '';
		}
		return $payments;
	}

	

	/**
	 * @param $paypalTable
	 * @param $payment_name
	 * @return string
	 */
	function _getPaymentResponseHtml ($paypalTable, $payment_name) {
		if ($paypalTable->pay_result!=='APPROVED')
		 {
		   $app= JFactory::getApplication(); 
		   $app->redirect (JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=editpayment'), 'Köp avbrutet, varukorgen är sparad.');
		   return ''; 
		 }
		 else
		 {
		 
		 if (JVM_VERSION === 2) {
			$img = JURI::root() .  '/plugins/vmpayment/certitrade/certitrade/button_ok.png';
			} else {
			$img = JURI::root() .  '/plugins/vmpayment/certitrade/button_ok.png';
		  }
		 
		 
		$html = '<div style="border: 0px solid transparent; border-width: 0px;">' . "\n";
		$html .= '<h3>'.$payment_name.'</h3>';
		$html = '<div><div style="height: 400px; width: 100px; float: left; display: inline-block;"><img src="'.$img.'" alt="OK" /></div><div style="float: left;">';
		$html .= '<div style="clear: both; ">Transaktionen har godkänts av CertiTrade kortbetalning</div>';
		if (!empty($paypalTable)) {
			$html .= '<div style="clear: left; font-weight: bold; float: left;">'.JText::_('VMPAYMENT_PAYPAL_ORDER_NUMBER').':</div><div style="float: left; clear: right; padding-left: 20px;  ">'.$paypalTable->order_number.'</div><br />';
			
			$html .= '<div style="float: left; font-weight: bold;">med Tr-nummer:</div><div style="padding-left: 20px; float: left; clear: right;">'.$paypalTable->trnumber.'</div><br />';
			$html .= '<div style="float: left; clear: left; font-weight: bold;">och Auktorisationskod:</div><div style="float: left; clear: right; padding-left: 20px;">'.$paypalTable->authcode.'</div>';
			//$html .= $this->getHtmlRow('PAYPAL_AMOUNT', $paypalTable->payment_order_total. " " . $paypalTable->payment_currency);
		}
		$html .= '</div></div></div>' . "\n";
		
		
		
		}
		return $html;
	}

	/**
	 * @param VirtueMartCart $cart
	 * @param                $method
	 * @param                $cart_prices
	 * @return int
	 */
	function getCosts (VirtueMartCart $cart, $method, $cart_prices) {

		if (preg_match ('/%$/', $method->cost_percent_total)) {
			$cost_percent_total = substr ($method->cost_percent_total, 0, -1);
		} else {
			$cost_percent_total = $method->cost_percent_total;
		}
		return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
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
	protected function checkConditions ($cart, $method, $cart_prices) {

		$this->convert ($method);

		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

		$amount = $cart_prices['salesPrice'];
		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0)));

		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array ($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}
		// probably did not gave his BT:ST address
		if (!is_array ($address)) {
			$address = array();
			$address['virtuemart_country_id'] = 0;
		}

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}
		if (in_array ($address['virtuemart_country_id'], $countries) || count ($countries) == 0) {
			if ($amount_cond) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param $method
	 */
	function convert ($method) {

		$method->min_amount = (float)$method->min_amount;
		$method->max_amount = (float)$method->max_amount;
	}

	/**
	 * We must reimplement this triggers for joomla 1.7
	 */

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPaymentPluginTable ($jplugin_id) {

		return $this->onStoreInstallPluginTable ($jplugin_id);
	}

	/**
	 * This event is fired after the payment method has been selected. It can be used to store
	 * additional payment info in the cart.
	 *
	 * @author Max Milbers
	 * @author Valérie isaksen
	 *
	 * @param VirtueMartCart $cart: the actual cart
	 * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
	 *
	 */
	public function plgVmOnSelectCheckPayment (VirtueMartCart $cart, &$msg) {

		return $this->OnSelectCheck ($cart);
	}

	/**
	 * plgVmDisplayListFEPayment
	 * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
	 *
	 * @param object  $cart Cart object
	 * @param integer $selected ID of the method selected
	 * @return boolean True on succes, false on failures, null when this plugin was not selected.
	 * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
	 *
	 * @author Valerie Isaksen
	 * @author Max Milbers
	 */
	public function plgVmDisplayListFEPayment (VirtueMartCart $cart, $selected = 0, &$htmlIn) {

		return $this->displayListFE ($cart, $selected, $htmlIn);
	}

	/*
		 * plgVmonSelectedCalculatePricePayment
		 * Calculate the price (value, tax_id) of the selected method
		 * It is called by the calculator
		 * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
		 * @author Valerie Isaksen
		 * @cart: VirtueMartCart the current cart
		 * @cart_prices: array the new cart prices
		 * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
		 *
		 *
		 */

	/**
	 * @param VirtueMartCart $cart
	 * @param array          $cart_prices
	 * @param                $cart_prices_name
	 * @return bool|null
	 */
	public function plgVmonSelectedCalculatePricePayment (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

		return $this->onSelectedCalculatePrice ($cart, $cart_prices, $cart_prices_name);
	}

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
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 */
	public function plgVmOnShowOrderFEPayment ($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {

		$this->onShowOrderFE ($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
	}

	/**
	 * This event is fired during the checkout process. It can be used to validate the
	 * method data as entered by the user.
	 *
	 * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
	 * @author Max Milbers

	public function plgVmOnCheckoutCheckDataPayment($psType, VirtueMartCart $cart) {
	return null;
	}
	 */

	/**
	 * This method is fired when showing when priting an Order
	 * It displays the the payment method-specific data.
	 *
	 * @param integer $_virtuemart_order_id The order ID
	 * @param integer $method_id  method used for this order
	 * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	function plgVmonShowOrderPrintPayment ($order_number, $method_id) {

		return $this->onShowOrderPrint ($order_number, $method_id);
	}

	/**
	 * Save updated order data to the method specific table
	 *
	 * @param array $_formData Form data
	 * @return mixed, True on success, false on failures (the rest of the save-process will be
	 * skipped!), or null when this method is not actived.
	 * @author Oscar van Eijk

	public function plgVmOnUpdateOrderPayment(  $_formData) {
	return null;
	}
	 */
	/**
	 * Save updated orderline data to the method specific table
	 *
	 * @param array $_formData Form data
	 * @return mixed, True on success, false on failures (the rest of the save-process will be
	 * skipped!), or null when this method is not actived.
	 * @author Oscar van Eijk

	public function plgVmOnUpdateOrderLine(  $_formData) {
	return null;
	}
	 */
	/**
	 * plgVmOnEditOrderLineBE
	 * This method is fired when editing the order line details in the backend.
	 * It can be used to add line specific package codes
	 *
	 * @param integer $_orderId The order ID
	 * @param integer $_lineId
	 * @return mixed Null for method that aren't active, text (HTML) otherwise
	 * @author Oscar van Eijk

	public function plgVmOnEditOrderLineBE(  $_orderId, $_lineId) {
	return null;
	}
	 */

	/**
	 * This method is fired when showing the order details in the frontend, for every orderline.
	 * It can be used to display line specific package codes, e.g. with a link to external tracking and
	 * tracing systems
	 *
	 * @param integer $_orderId The order ID
	 * @param integer $_lineId
	 * @return mixed Null for method that aren't active, text (HTML) otherwise
	 * @author Oscar van Eijk

	public function plgVmOnShowOrderLineFE(  $_orderId, $_lineId) {
	return null;
	}
	 */
	function plgVmDeclarePluginParamsPayment ($name, $id, &$data) {

		return $this->declarePluginParams ('payment', $name, $id, $data);
	}

	/**
	 * @param $name
	 * @param $id
	 * @param $table
	 * @return bool
	 */
	function plgVmSetOnTablePluginParamsPayment ($name, $id, &$table) {

		return $this->setOnTablePluginParams ($name, $id, $table);
	}

}

// No closing tag
