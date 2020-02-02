<?php

defined('_JEXEC') or die('Restricted access');

if (!class_exists('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

if (!class_exists('vm2smsapi')) {
class vm2smsapi{
	static private $innerdata;
	
	static function send($data,$old_status){
		$db = JFactory::getDBO();
		$db->setQuery('select * from #__virtuemart_order_userinfos where virtuemart_order_id='.$data->virtuemart_order_id);
		$userdata = $db->loadObjectList('address_type');
		$phone = '';
		$phone_field = JComponentHelper::getParams( 'com_vm2sms' )->get('phone_field');
		$custom_phone_field = JComponentHelper::getParams( 'com_vm2sms' )->get('custom_phone_field');
		if($custom_phone_field) {
			$phone_field = $custom_phone_field;
		}
		
		if (isset($userdata['BT'])&&isset($userdata['BT']->$phone_field)){
			$phone = $userdata['BT']->$phone_field;
		}
		
		if (isset($userdata['ST'])&&isset($userdata['ST']->$phone_field)){
			$phone = $userdata['ST']->$phone_field;
		}
		self::$innerdata = array($data, $userdata);
		$repl = array(' ','-','(',')');
		$phone = str_replace($repl,"",$phone);
		$db->setQuery('SELECT send_sms,text_sms,worktime, manager_text_sms,manager_send_sms,manager_worktime,include_comment from #__vm2sms where `status`='.$db->quote($data->order_status));
		$param = $db->loadObject();
		
		JPluginHelper::importPlugin('vm2sms');
		$dispatcher	= JDispatcher::getInstance();
		$sender = JComponentHelper::getParams( 'com_vm2sms' )->get('sender_name');
		if ($phone && $param->send_sms){
			$text = preg_replace_callback('/%((comment)\.{0,1}(\d*)|ordernumber|orderpass|(price)\.{0,1}(\d*)|first_name)%/',array('vm2smsapi',"text_replace"),$param->text_sms);
			if ($param->include_comment){
				$backtrace = debug_backtrace();
				foreach($backtrace as $line){
					if($line['function']=='updateStatusForOneOrder' && $line['class'] == 'VirtueMartModelOrders' && isset($line['args'][1]) && is_array($line['args'][1])){
						$text .= "\n".$line['args'][1]['comments'];
					}
				}
				/* $orders = jrequest::getVar('orders');
				if (isset($orders[$data->virtuemart_order_id])){
				$text .= "\n".$orders[$data->virtuemart_order_id]['comments'];
				}*/
			}
			$results = $dispatcher->trigger('onSendSMS',array($phone,$text,$param->worktime,$sender));
		}
		
		$phones = explode(',',JComponentHelper::getParams( 'com_vm2sms' )->get('manager_phones'));
		foreach($phones as $phone){
			$phone = trim($phone);
			if ($phone && $param->manager_send_sms){
				$text = preg_replace_callback('/%((comment)\.{0,1}(\d*)|ordernumber|orderpass|(price)\.{0,1}(\d*)|first_name)%/',array('vm2smsapi',"text_replace"),$param->manager_text_sms);
				$results = $dispatcher->trigger('onSendSMS',array($phone,$text,$param->manager_worktime,$sender));
			}
		}
	}
	
	static function text_replace($m){
		$result = '';
		switch($m[1]){
			case 'ordernumber':
				$result=self::$innerdata[0]->order_number;
				break;
			case 'orderpass':
				$result=self::$innerdata[0]->order_pass;
				break;
			case 'first_name':
				$result=self::$innerdata[1]['BT']->first_name;
				break;
			default:
				$result=$m[0];
				if(isset($m[4]) && $m[4] =='price'){
					$dec = 2;
					if(!($m[5]=='')){
						$dec = $m[5];
					}
					$result = number_format(self::$innerdata[0]->order_total,$dec,'.','');
				}
				if(isset($m[2]) && $m[2] =='comment'){
					$limit = $m[3]-1;
					$db = JFactory::getDBO();
					$db->setQuery('select comments from #__virtuemart_order_histories where virtuemart_order_id='.self::$innerdata[0]->virtuemart_order_id.' order by virtuemart_order_history_id desc');
					$comments = $db->loadObjectList();
					$result = str_replace("
					", "\n", $comments[$limit]->comments);
				}
				break;
		}
		return $result;
	}
}
}

class plgVmPaymentVm2sms extends vmPSPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);
		$this->_loggable   = false;
	    $varsToPush = array();

	    $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * @author Valérie Isaksen
	 */
	public function getVmPluginCreateTableSQL() {
		return null;
	}

	/**
	 * Fields to create the payment table
	 * @return string SQL Fileds
	 */
	function getTableSQLFields() {
		$SQLfields = array();
		return $SQLfields;
	}

	function plgVmConfirmedOrder($cart, $order) {
		return null;
	}
	function plgVmOnUpdateOrderPayment($data,$old_status){
		vm2smsapi::send($data,$old_status);
		return null;
	}
	
	/**
	 * Display stored payment data for an order
	 *
	 */
	function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_payment_id) {
		return null;
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
		$this->convert($method);
		// 		$params = new JParameter($payment->payment_params);
		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

		$amount      = $cart_prices['salesPrice'];
		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0)));
		if (!$amount_cond) {
			return false;
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
			$address                          = array();
			$address['virtuemart_country_id'] = 0;
		}

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}
		if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
			return true;
		}

		return false;
	}

	function convert($method) {

		$method->min_amount = (float)$method->min_amount;
		$method->max_amount = (float)$method->max_amount;
	}

	function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
		return $this->onStoreInstallPluginTable($jplugin_id);
	}

	public function plgVmOnSelectCheckPayment (VirtueMartCart $cart,  &$msg) {
		return $this->OnSelectCheck($cart);
	}

	public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
		return $this->displayListFE($cart, $selected, $htmlIn);
	}


	public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
		return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
	}

	function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}
		$this->getPaymentCurrency($method);

		$paymentCurrencyId = $method->payment_currency;
		return;
	}

	function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter) {
		return $this->onCheckAutomaticSelected($cart, $cart_prices, $paymentCounter);
	}

	public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
		$this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
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

}
