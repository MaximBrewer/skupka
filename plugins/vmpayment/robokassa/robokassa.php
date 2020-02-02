<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentRobokassa extends vmPSPlugin {

    // instance of class
    public static $_this = false;

    function __construct(& $subject, $config) {
	//if (self::$_this)
	 //   return self::$_this;
    		ob_start();
	parent::__construct($subject, $config);
		
	    $this->_loggable = true;
	    $this->tableFields = array_keys($this->getTableSQLFields());
	    if(version_compare(JVM_VERSION,'3','ge')){
			$varsToPush = $this->getVarsToPush ();
		} else {
		    $varsToPush = array('payment_logos' => array('', 'char'),
			'countries' => array(0, 'int'),
			'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\' ',
			'payment_currency' =>  array(0, 'int'),
			'min_amount' => array(0, 'int'),
			'max_amount' => array(0, 'int'),
			'cost_per_transaction' => array(0, 'int'),
			'cost_percent_total' => array(0, 'int'),
			'tax_id' => array(0, 'int'),
			'robokassa_login' => array('', 'string'),
			'robokassa_password1' => array('', 'string'),
			'robokassa_password2' => array('', 'string'),
			'robokassa_payment_type' => array('0', 'string'),
			'robokassa_demo' => array(1, 'int'),
			'robokassa_fee' => array(0, 'string'),
		    'status_success' => array('', 'char'),
		    'status_ordered' => array('', 'char'),
		    'robokassa_payment_type'=>array('0','int'),
		    'shipping_methods' => array(0, 'int'),
		    'payment_message'=>array('','string'),
		    'status_for_payment' => array('', 'char'),
		    'license'=>array('','string')
		    );
		}
		$this->info = ob_get_contents();
	    $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
	   // self::$_this = $this;
	    ob_end_clean();
    }

    protected function getVmPluginCreateTableSQL() {
	return $this->createTableSQL('Payment Robokassa Table');
    }

    function getTableSQLFields() {
	$SQLfields = array(
	    'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
	    'virtuemart_order_id' => 'int(11) UNSIGNED',
	    'order_number' => 'char(32)',
	    'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED',
	    'payment_name' => 'TEXT NOT NULL',
	    'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\'',
	    'payment_currency' => 'char(3) ',
	    'cost_per_transaction' => ' decimal(10,2)',
	    'cost_percent_total' => ' decimal(10,2)',
	    'tax_id' => 'smallint(11)',
	    'calc_total'=>' decimal(10,2)'
	);

	return $SQLfields;
    }

    function plgVmConfirmedOrder($cart, $order) {

		if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
		    return null; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
		    return false;
		}
		//$params = new JParameter($payment->payment_params);
		$lang = JFactory::getLanguage();
		$filename = 'com_virtuemart';
		$lang->load($filename, JPATH_ADMINISTRATOR);
		$vendorId = 0;


		$session = JFactory::getSession();
		$return_context = $session->getId();
		//$this->_debug = $method->debug;
		$this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

		$html = "";

		if (!class_exists('VirtueMartModelOrders'))
		    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
		if(!$method->payment_currency)$this->getPaymentCurrency($method);

		$dbValues = array();
		$dbValues['payment_name'] = $this->renderPluginName($method);
	//	$this->loadInfo($method);
		robokassaapi::confirmOrder($cart,$order,$method,$html,$dbValues,$this);
		$this->storePSPluginInternalData($dbValues);
		$modelOrder = VmModel::getModel ('orders');
		$order['order_status'] = $method->status_ordered;
		$order['customer_notified'] = 1;
		$order['comments'] = '';
		$modelOrder->updateStatusForOneOrder ($order['details']['BT']->virtuemart_order_id, $order, TRUE);

		//We delete the old stuff
		$cart->emptyCart ();
		JRequest::setVar ('html', $html);
		return TRUE;

    }

    /**
     * Display stored payment data for an order
     *
     */
    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_payment_id) {
	if (!$this->selectedThisByMethodId($virtuemart_payment_id)) {
	    return null; // Another method was selected, do nothing
	}

	$db = JFactory::getDBO();
	$q = 'SELECT * FROM `' . $this->_tablename . '` '
		. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
	$db->setQuery($q);
	if (!($paymentTable = $db->loadObject())) {
	    vmWarn(500, $q . " " . $db->getErrorMsg());
	    return '';
	}
	$this->getPaymentCurrency($paymentTable);

	$html = '<table class="adminlist">' . "\n";
	$html .=$this->getHtmlHeaderBE();
	$html .= $this->getHtmlRowBE('STANDARD_PAYMENT_NAME', $paymentTable->payment_name);
	$html .= $this->getHtmlRowBE('STANDARD_PAYMENT_TOTAL_CURRENCY', $paymentTable->payment_order_total.' '.$paymentTable->payment_currency);
	$html .= '</table>' . "\n";
	return $html;
    }

    function getCosts(VirtueMartCart $cart, $method, $cart_prices) {
	if (preg_match('/%$/', $method->cost_percent_total)) {
	    $cost_percent_total = substr($method->cost_percent_total, 0, -1);
	} else {
	    $cost_percent_total = $method->cost_percent_total;
	}
	return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
    }

    protected function checkConditions($cart, $method, $cart_prices) {

//	$this->loadInfo($method);
    	return robokassaapi::check($cart, $method, $cart_prices);
    }

    function getOrder($virtuemart_order_id){
    	return $this->getDataByOrderId($virtuemart_order_id);
    }
    function loadMethod($pid){
    	return $this->getVmPluginMethod($pid);
    }
    protected function crc($num){
		$crc = crc32($num);
		if($crc & 0x80000000){
			$crc ^= 0xffffffff;
			$crc += 1;
			$crc = -$crc;
		}
		return $crc;
	}

    protected function loadInfo($method){
   return true;
}
	

    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
	return $this->onStoreInstallPluginTable($jplugin_id);
    }

    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {
	return $this->OnSelectCheck($cart);
    }

    function plgVmDeclarePluginParamsPaymentVM3( &$data) {
		return $this->declarePluginParams('payment', $data);
	}

    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
	return $this->displayListFE($cart, $selected, $htmlIn);
    }


    public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
	return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }

    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

	if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
	    return null; // Another method was selected, do nothing
	}
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}
	 $this->getPaymentCurrency($method);

	$paymentCurrencyId = $method->payment_currency;
    }

    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {
	return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }

    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
	        return null; // Another method was selected, do nothing
	    }
	    if (!$this->selectedThisElement($method->payment_element)) {
	        return false;
	    }
		$result = $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
		if  (JRequest::getVar('option')=='com_virtuemart'&&
	        Jrequest::getVar('view')=='orders'&&
	        Jrequest::getVar('layout')=='details'){
	        if (!class_exists('CurrencyDisplay'))
	            require( JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php' );
	        if (!class_exists('VirtueMartModelOrders'))
	            require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
	        $orderModel = VmModel::getModel('orders');
	        $order = $orderModel->getOrder($virtuemart_order_id);
	        $this->getPaymentCurrency($method);
	        $this->loadInfo($method);
	        $dbValues = array();

	        robokassaapi::showButton($order,$method,$payment_name,$result,$this,$dbValues);
	        if(!empty($dbValues)){
	        	$dbValues['payment_name'] = $this->renderPluginName($method);
	        	$this->storePSPluginInternalData($dbValues);
	        }

    }
    return $result;
    }

    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
	return $this->onShowOrderPrint($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsPayment($name, $id, &$data) {
	return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
	return $this->setOnTablePluginParams($name, $id, $table);
    }


    public function plgVmOnPaymentNotification() {
		if (JRequest::getVar('pelement')!='robokassa'){
			return null;
		}
		if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
		$virtuemart_order_id = JRequest::getInt('InvId',0);
		$payment = $this->getOrder($virtuemart_order_id);
		$method = $this->loadMethod($payment->virtuemart_paymentmethod_id);
		$this->loadInfo($method);
		return robokassaapi::notify($this, $method,$this->_tablename);
    }

    function plgVmOnPaymentResponseReceived(  &$html) {

// the payment itself should send the parameter needed.
		$virtuemart_paymentmethod_id = JRequest::getInt('pm', JRequest::getInt('SHPPM',0));

		$vendorId = 0;
		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return null; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}

		if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

		$order_pass = JRequest::getVar('SHPPASS');
		$order_number = JRequest::getVar('SHPON');
		JFactory::getApplication()->redirect("index.php?option=com_virtuemart&view=orders&layout=details&order_number=$order_number&order_pass=$order_pass",'Заказ оплачен');

		return true;
    }

    function plgVmOnUserPaymentCancel() {

		if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

		$order_number = JRequest::getVar('on',JRequest::getVar('SHPON',''));
		if (!$order_number)
			return false;
		$db = JFactory::getDBO();
		$query = 'SELECT ' . $this->_tablename . '.`virtuemart_order_id` FROM ' . $this->_tablename. " WHERE  `order_number`= " . $db->Quote($order_number) . "";

		$db->setQuery($query);
		$virtuemart_order_id = $db->loadResult();

		if (!$virtuemart_order_id) {
			return null;
		}
		$this->handlePaymentUserCancel($virtuemart_order_id);

		//JRequest::setVar('paymentResponse', $returnValue);
		return true;
    }

    protected function displayLogos($logo_list) {

	$img = "";

	if (!(empty($logo_list))) {
	    $url = JURI::root() . str_replace(JPATH_ROOT,'',dirname(__FILE__)).'/';
	    if (!is_array($logo_list))
		$logo_list = (array) $logo_list;
	    foreach ($logo_list as $logo) {
		$alt_text = substr($logo, 0, strpos($logo, '.'));
		$img .= '<img align="middle" src="' . $url . $logo . '"  alt="' . $alt_text . '" /> ';
	    }
	}
	return $img;
    }


	private function notifyCustomer($order, $order_info ) {
					if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
					shopFunctionsF::sentOrderConfirmedEmail($order_info);
	}
}

// No closing tag
class robokassaapi{

	static function confirmOrder($cart,$order,$method,&$html,&$dbValues,$obj){

		$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
		$db = &JFactory::getDBO();
		$db->setQuery($q);
		$currency_code_3 = $db->loadResult();
		$paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
		$totalInPaymentCurrency = number_format(round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2),2,'.','');
		$cd = CurrencyDisplay::getInstance($cart->pricesCurrency);
		$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order['details']['BT']->order_number);
		if ($method->status_for_payment == $method->status_ordered) {
			$realprice = robokassaapi::getCalculatedValue($method, $totalInPaymentCurrency);
			$html = robokassaapi::form($method,$virtuemart_order_id,$order,$realprice,1);
		} else {
			$html = '<div class="robokassa_message">'.$method->payment_message.'</div>';
		}
		$obj->_virtuemart_paymentmethod_id = $order['details']['BT']->virtuemart_paymentmethod_id;
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['virtuemart_paymentmethod_id'] = $obj->_virtuemart_paymentmethod_id;
		$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
		$dbValues['cost_percent_total'] = $method->cost_percent_total;
		$dbValues['payment_currency'] = $currency_code_3 ;
		$dbValues['payment_order_total'] = $totalInPaymentCurrency;
		$dbValues['tax_id'] = $method->tax_id;
		//TODO вычисленное значение, его и проверять при нотификации
		$dbValues['calc_total'] = $realprice;
	}

	static function showButton($order,$method,&$payment_name,&$result,$obj,&$dbValues){
		$html = '';
        if ($method->status_for_payment == $order['details']['BT']->order_status){

        	$virtuemart_order_id = $order['details']['BT']->virtuemart_order_id;
        	$paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
            $totalInPaymentCurrency = round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2);
            $realprice = robokassaapi::getCalculatedValue($method, $totalInPaymentCurrency);
            $html = robokassaapi::form($method,$virtuemart_order_id,$order,$realprice,0);

            $dbValues['order_number'] = $order['details']['BT']->order_number;
			$dbValues['virtuemart_paymentmethod_id'] = $obj->_virtuemart_paymentmethod_id;
			$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
			$dbValues['cost_percent_total'] = $method->cost_percent_total;
			$dbValues['payment_currency'] = $currency_code_3 ;
			$dbValues['payment_order_total'] = $totalInPaymentCurrency;
			$dbValues['tax_id'] = $method->tax_id;
			//TODO вычисленное значение, его и проверять при нотификации
			$dbValues['calc_total'] = $realprice;
        } else {
        	if($order['details']['BT']->order_status==$method->status_ordered)
	        	$html = '<div class="robokassa">'.$method->payment_message.'</div>';
        }
        if($html){
	        $payment_name .="<br>".$html;
	    }
    }

    static function form($method,$virtuemart_order_id,$order,$totalInPaymentCurrency,$redirect){
    	$sig = md5("{$method->robokassa_login}:$totalInPaymentCurrency:$virtuemart_order_id:".trim($method->robokassa_password1).":SHPON={$order['details']['BT']->order_number}:SHPPASS={$order['details']['BT']->order_pass}:SHPPM={$order['details']['BT']->virtuemart_paymentmethod_id}");
		if($method->robokassa_demo==0){
			$url = 'https://merchant.roboxchange.com/Index.aspx';
		} else {
			$url = 'http://test.robokassa.ru/Index.aspx';
			//$url = 'http://localhost.ru/tools/payments/robokassa/index.php';
		}
		$html = '<form method="post" action="'.$url.'" name="vm_robokassa_form">';
		$html .= "<input type='hidden' name='MrchLogin' value='".trim($method->robokassa_login)."' />";
		$html .= "<input type='hidden' name='OutSum' value='$totalInPaymentCurrency'>";
		$html .= "<input type='hidden' name='Desc' value='Оплата за заказ № {$order['details']['BT']->order_number}. Спасибо за покупку! ' />";
	    $html .= "<input type='hidden' name='InvId' value='$virtuemart_order_id'>";
		$html .= "<input type='hidden' name='SignatureValue' value='$sig'>";
		if ($method->robokassa_payment_type){
			$html .= "<input type='hidden' name='IncCurrLabel' value='{$method->robokassa_payment_type}'>";
		}
		$html .= "<input type='hidden' name='SHPON' value='{$order['details']['BT']->order_number}'>";
		$html .= "<input type='hidden' name='SHPPM' value='{$order['details']['BT']->virtuemart_paymentmethod_id}'>";
		$html .= "<input type='hidden' name='SHPPASS' value='{$order['details']['BT']->order_pass}'>";
		if(!$redirect){
			$html .= "<input type='submit' value='Оплатить'>";
		}
		$html .= "</form>";
		if($redirect){
			$html .= 'Сейчас вы бедете перемещены на страницу оплаты';
			$html.= ' <script type="text/javascript">';
			$html.= ' document.forms.vm_robokassa_form.submit();';
			$html.= ' </script>';
		}
		return $html;
    }

	static function check($cart, $method, $cart_prices){
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

		$amount = $cart_prices['salesPrice'];
		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0) ));
		if (!$amount_cond) {
		    return false;
		}

		$shipping_methods = array();
		if (!empty($method->shipping_methods)) {
		    if (!is_array($method->shipping_methods)) {
			$shipping_methods = $method->shipping_methods;
		    } else {
			$shipping_methods = $method->shipping_methods;
		    }
			if (!in_array($cart->virtuemart_shipmentmethod_id, $shipping_methods)){
				return false;
			}
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

		if (!isset($address['virtuemart_country_id']))
		    $address['virtuemart_country_id'] = 0;
		if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
		    return true;
		}

		return false;
	}

	static function notify($thus,$method, $table){
		$orderid = JRequest::getInt('InvId',0);
		$postprice = JRequest::getVar('OutSum');
        $order_model = new VirtueMartModelOrders();
        $order_info = $order_model->getOrder($orderid);
        $order_number = $order_info['details']['BT']->order_number;
		$string = "{$postprice}:{$orderid}:".trim($method->robokassa_password2).":SHPON=".JRequest::getVar('SHPON').':SHPPASS='.JRequest::getVar('SHPPASS').':SHPPM='.JRequest::getVar('SHPPM');
		$sig = strtoupper(md5($string));
		$db = JFactory::getDBO();
		$db->setQuery('select calc_total from '.$table.' where virtuemart_order_id='.$orderid);
		$totalInPaymentCurrency = $db->loadResult();
		if ($sig == strtoupper(JRequest::getVar('SignatureValue')) && $totalInPaymentCurrency == floatval($postprice)) {

			$order['order_status'] = $method->status_success;
			$order['virtuemart_order_id'] = $orderid;
			$order['customer_notified'] = 1;
			$order['comments'] = '';
			if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
			$modelOrder = new VirtueMartModelOrders();
			ob_start();
			$modelOrder->updateStatusForOneOrder($orderid, $order, true);
			ob_end_clean();
			echo 'OK'.$orderid;
			return true;
		}
		echo 'FAIL';
		return null;
	}

	static function getCalculatedValue($method, $value){
		if($method->robokassa_fee==1 && $method->robokassa_payment_type!==0){
			jimport( 'joomla.client.http' );
			$opt = new JRegistry;
			if (function_exists('curl_version') && curl_version()){
				$trans = new JHttpTransportCurl($opt);
			} elseif (function_exists('fopen') && is_callable('fopen') && ini_get('allow_url_fopen')){
				$trans = new JHttpTransportStream($opt);
			} elseif(function_exists('fsockopen') && is_callable('fsockopen')){
				$trans = new JHttpTransportSocket($opt);
			} else {
				JError::raiseError(500, "Can't initialise http transport ");
			}
			$http = new JHttp($opt,$trans);
			$result = $http->get('https://auth.robokassa.ru/Merchant/WebService/Service.asmx/CalcOutSumm?MerchantLogin='.trim($method->robokassa_login).'&IncCurrLabel='.$method->robokassa_payment_type.'&IncSum='.$value);
			$xml = $result->body;
			if($xml){
				$xml = @simplexml_load_string($xml);
			}
			if($xml && $xml->Result->Code==0 && $xml->OutSum){
				return floatval($xml->OutSum);
			} else {
				return $value;
			}
		} else {
			/*
			$link = 'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/GetRates?MerchantLogin='.trim($method->robokassa_login).'&IncCurrLabel='.$method->robokassa_payment_type.'&OutSum='.$value.'&Language=ru';
			$xml = @file_get_contents($link);
			if($xml){
				$xml = simplexml_load_string($xml);
			}
			if($xml && $xml->Result->Code==0 && $xml->Groups){
				$attr = $xml->Groups->Group[0]->Items->Currency->Rate->attributes();
				if(isset($attr['IncSum'])){
					return strval($attr['IncSum']);
				} else {
					return $value;
				}
			} else {*/
				return $value;
			/*}*/
		}
	}
}