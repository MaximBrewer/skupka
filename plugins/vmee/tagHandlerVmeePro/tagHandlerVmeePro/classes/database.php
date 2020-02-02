<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright	Copyright (C) 2013 InteraMind Advanced Analytics. All rights reserved.
 
 **/

class vmeeProDb {
	static $isInitialzedVM = false;
	static $vmModels = array();

	static function getVmModels($vmModelName){
		if(!key_exists($vmModelName, self::$vmModels)){
			self::loadVmModel($vmModelName);
		}
		return self::$vmModels[$vmModelName];
	}

	static private function loadVmModel($vmModelName){
		self::$vmModels[$vmModelName] = JModelLegacy::getInstance($vmModelName, 'VirtuemartModel');
	}
	static function loadVirtueMartFiles(){
		if(!self::$isInitialzedVM){
			if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			if (!class_exists('ShopFunctions')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctions.php');
			if (!class_exists('VirtueMartModelCustomfields')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'customfields.php');
			if (!class_exists('CurrencyDisplay')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');

			VmConfig::loadConfig();

			$lang = JFactory::getLanguage();
			$extension = 'com_virtuemart_orders';
			$base_dir = JPATH_SITE . '/components/com_virtuemart';
			$language_tag = null;
			$reload = true;
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$extension = 'com_virtuemart_shoppers';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$extension = 'com_virtuemart';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$base_dir = JPATH_ADMINISTRATOR . '/components/com_virtuemart';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$lang->load('com_vmeeplus', JPATH_SITE, null, true);
			$lang->load('com_vmeeplus', JPATH_ADMINISTRATOR, null, true);

			self::$isInitialzedVM = true;
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'models');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_virtuemart/tables');
		}
	}

	//The order is an array of arrays [details[BT], history[], items[]]
	static function getOrderByOrderNumber($orderNumber){
		$orderModel = self::getVmModels('orders');
		$virtuemart_order_id = $orderModel->getOrderIdByOrderNumber($orderNumber);
		$order = self::getOrderByOrderId($virtuemart_order_id);

		return $order;
	}

	static function getOrderByOrderId($virtuemart_order_id){
		$orderModel = self::getVmModels('orders');
		$order = $orderModel->getOrder($virtuemart_order_id);
		if(!empty($order['details'])){
			$order['virtuemart_order_id'] = $virtuemart_order_id;
			$order['order_number'] = $order['details']['BT']->order_number;
		}

		//more items information
		foreach ($order['items'] as $item){
			$productModel = self::getVmModels('product');
			$product = $productModel->getProductSingle($item->virtuemart_product_id);
			$productModel->addImages($product,$item->virtuemart_product_id);
			$imgsrc = preg_replace('/[\s\+]/','%20',$product->images[0]->file_url_thumb);
			$item->image_thumb = $imgsrc;
			$item->vendor_id = $product->virtuemart_vendor_id;
			$item->manufacturer_id = $product->virtuemart_manufacturer_id;
		}
		
		//more order status information
		$db	= JFactory::getDBO();
		$q = "SELECT s.order_status_description FROM #__virtuemart_orderstates s
		LEFT JOIN #__virtuemart_orders o ON s.order_status_code = o.order_status
		WHERE o.virtuemart_order_id =".$virtuemart_order_id;
		$db->setQuery($q);
		$order['details']['BT']->order_status_description = $db->loadResult();
		
		//invoice number
		$q = "SELECT invoice_number FROM #__virtuemart_invoices WHERE virtuemart_order_id=" . $virtuemart_order_id;
		$db->setQuery($q);
		$order['invoice_number'] = $db->loadResult();
		
		return $order;
	}
	
	static function getProductThumb($productId){
		$productModel = self::getVmModels('product');
		$product = $productModel->getProductSingle($productId);
		$productModel->addImages($product,$productId);
		return isset($product->images[0]->file_url_thumb) ? $product->images[0]->file_url_thumb : false;
	}
	
	static function getVendor($vendorId = null){
		$vendorModel = self::getVmModels('vendor');
		$vendor = $vendorModel->getVendor($vendorId);
		$vendorModel->addImages($vendor,$vendor->virtuemart_vendor_id);
		$vendor->email = $vendorModel->getVendorEmail($vendor->virtuemart_vendor_id);
		$vendor->currency = CurrencyDisplay::getInstance('', $vendor->virtuemart_vendor_id);
	
		return $vendor;
	}
	
	static function getPaymentName(&$order_details){
		/* $paymentMethodId = $order_details->virtuemart_paymentmethod_id;
		$paymentMethodModel = self::getVmModels('paymentmethod');
		$paymentMethodModel->setId($paymentMethodId);
		$payment = $paymentMethodModel->getPayment();
		return $payment->payment_name; */
		
		$paymentName = '';
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $order_details->virtuemart_order_id, $order_details->virtuemart_paymentmethod_id,  &$paymentName));
		$paymentName = str_replace('class="vmpayment_name"', 'style="margin-right: 10px;"', $paymentName);
		return $paymentName;
	}
	
	static function getShippingName(&$order_details){
		/* $shipmentMethodId = $order_details->virtuemart_shipmentmethod_id;
		$paymentMethodModel = self::getVmModels('shipmentmethod');
		$paymentMethodModel->setId($shipmentMethodId);
		$shipment = $paymentMethodModel->getShipment();
		return $shipment->shipment_desc; */
		
		$shipmentName = '';
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DIRECTORY_SEPARATOR . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnShowOrderFEShipment',array(  $order_details->virtuemart_order_id, $order_details->virtuemart_shipmentmethod_id, &$shipmentName));
		$shipmentName = str_replace('class="vmshipment_name"', 'style="margin-right: 10px;"', $shipmentName);
		return $shipmentName;
		
	}
	
	//known issue with this function - it will take only the last ST row
	static function getUserInfoIds($userId){
		$db	= JFactory::getDBO();
		$q = 'SELECT address_type, virtuemart_userinfo_id FROM #__virtuemart_userinfos WHERE virtuemart_user_id = ' .(int)$userId;
		$db->setQuery($q);
		$result = $db->loadAssocList('address_type');
		return $result;
	}
	
	static function getDBO($order_id){
		$dbo = new ps_DB;

		$q = "SELECT #__{vm}_orders.user_id, first_name, last_name, user_email, order_status_name, order_status_code,
		order_status_description, order_currency, order_shipping, order_shipping_tax, coupon_code,
		order_tax, order_total,order_discount, coupon_discount, customer_note, #__{vm}_orders.user_id, #__{vm}_orders.cdate, #__{vm}_orders.ship_method_id
		FROM #__{vm}_order_user_info, #__{vm}_orders, #__{vm}_order_status ";
		$q .= "WHERE #__{vm}_orders.order_id = '".$order_id."' ";
		$q .= "AND #__{vm}_orders.user_id = #__{vm}_order_user_info.user_id ";
		$q .= "AND #__{vm}_orders.order_id = #__{vm}_order_user_info.order_id ";
		$q .= "AND order_status = order_status_code ";
		$dbo->execute($q);
		$dbo->next_record();
		return $dbo;
	}

	static function getVendorDB(){
		$ps_vendor_id = vmGet( $_SESSION, 'ps_vendor_id', 1 );
		$dbVendor = new ps_DB;
		$q = "SELECT * FROM #__{vm}_vendor ";
		$q .= "WHERE vendor_id='".$ps_vendor_id."'";
		$dbVendor->execute($q);
		$dbVendor->next_record();
		return $dbVendor;
	}

	static function getDBOI($order_id){
		global $database;

		$dboi = new ps_DB;
		$q_oi = "SELECT * FROM #__{vm}_product, #__{vm}_order_item, #__{vm}_orders ";
		$q_oi .= "WHERE #__{vm}_product.product_id=#__{vm}_order_item.product_id ";
		$q_oi .= "AND #__{vm}_order_item.order_id='".$order_id."' ";
		$q_oi .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id";
		$dboi->execute($q_oi);

		return $dboi;
	}

	static function getVendorDBOI($order_id){
		global $database;

		$dboi = new ps_DB;
		$q_oi = "SELECT #__{vm}_product.vendor_id, product_quantity, product_final_price, product_item_price, #__{vm}_product.product_id, product_name, product_attribute ";
		$q_oi .= "FROM #__{vm}_product, #__{vm}_order_item, #__{vm}_orders ";
		$q_oi .= "WHERE #__{vm}_product.product_id=#__{vm}_order_item.product_id ";
		$q_oi .= "AND #__{vm}_order_item.order_id='".$order_id."' ";
		$q_oi .= "AND #__{vm}_orders.order_id=#__{vm}_order_item.order_id";
		$dboi->execute($q_oi);
		return $dboi;
	}

	/**
	 *
	 * @param string $cartProductIds - comma seperated product IDs
	 */
	static function getDBOC($cartProductIds){
		global $database;

		$dboc = new ps_DB;
		$q_oc = "SELECT * FROM #__{vm}_product ";
		$q_oc .= "WHERE #__{vm}_product.product_id IN(" . $cartProductIds . ") ";
		$dboc->execute($q_oc);

		return $dboc;
	}

	static function getHistoryResultSet($order_id){
		$history_db	= JFactory::getDBO();

		$q = "SELECT * FROM #__virtuemart_order_histories ";
		$q .= "WHERE virtuemart_order_id = '".$order_id."' ORDER BY created_on DESC";
		$history_db->setQuery($q);
		return $history_db->loadAssoc();
	}

	static function getOrderItemsCount($dboi){
		$dboi->reset();
		$imtesNum = 0;
		while($dboi->next_record()) {
			$imtesNum += $dboi->f("product_quantity");
		}
		$dboi->reset();
		return $imtesNum;
	}

	static function get_shoppergroup_by_id($user_id){
		$shopperGroupModel = self::getVmModels('shoppergroup');
		$groups = $shopperGroupModel->getShoppergroupById($user_id);
		
		$ret = array();
		
		if (empty($groups)) return $ret;
		
		if (isset($groups['shopper_group_name'])){
			$ret[] = $groups['shopper_group_name'];
			return $ret;
		}
		
		foreach ($groups as $group) $ret[] = $group['shopper_group_name'];
		return $ret;
	}
	
	static function getOrderResultSet($order_id){
		$orders_db	= JFactory::getDBO();

		$q = "SELECT * FROM #__virtuemart_orders ";
		$q .= "WHERE virtuemart_order_id = '".$order_id."' ";
		$orders_db->setQuery($q);
		return $orders_db->loadAssoc();
	}

	static function getCustomerTotal($user_id){
		$db = JFactory::getDBO();
		$q = "SELECT SUM(order_total) FROM #__virtuemart_orders WHERE virtuemart_user_id=".$user_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	static function getCustomerOrdersCount($user_id){
		$db = JFactory::getDBO();
		$q = "SELECT count(1) FROM #__virtuemart_orders WHERE virtuemart_user_id=" . $user_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	static function getUserInfoDb($order_id, $user_id, $address_type = 'BT'){
		$db = JFactory::getDBO();
		if(!is_null($order_id)){
			$uidatabse =  "#__virtuemart_order_userinfos";
			$where = "ui.virtuemart_order_id = '$order_id'";
		}
		else{
			$uidatabse = "#__virtuemart_userinfos";	
			$where = "ui.virtuemart_user_id = '$user_id'";
		}

		$q = "SELECT ui.*,s.state_name as state FROM " . $uidatabse . " ui LEFT JOIN #__virtuemart_states s ON ui.virtuemart_state_id = s.virtuemart_state_id WHERE " . $where . " AND ui.address_type='".$address_type."'";
		$db->setQuery($q);
		$result = $db->loadAssoc();
		if(empty($result) && $address_type == 'ST'){
			return  self::getUserInfoDb($order_id, 'BT');
		}
		return $result;
	}
	
	static function getOrderUserInfoDb($order_id, $address_type = 'BT'){
		$userInfoDb = new ps_DB;
		$q = "SELECT * FROM #__{vm}_order_user_info WHERE order_id = '$order_id' AND address_type='".$address_type."'";
		$userInfoDb->execute($q);
		$userInfoDb->next_record();
		if($userInfoDb->num_rows() < 1 && $address_type == 'ST'){
			return  vmeeProDb::getOrderUserInfoDb($order_id, 'BT');
		}
		return $userInfoDb;
	}

	static function getDbPayment($order_id){
		global $database;
		$db_payment = new ps_DB;
		$q  = "SELECT op.payment_method_id, pm.payment_method_name FROM #__{vm}_order_payment as op, #__{vm}_payment_method as pm
		WHERE order_id='$order_id' AND op.payment_method_id=pm.payment_method_id";
		$db_payment->execute($q);
		$db_payment->next_record();
		return $db_payment;
	}

	static function getUserIdByEmail($email){
		$db	= JFactory::getDBO();
		$query = 'SELECT id FROM #__users WHERE email = "'.$email.'"';
		$db->setQuery($query);
		return $db->loadResult();
	}

	static function getUserIdByUserName($user_name){
		$db	= JFactory::getDBO();
		$query = 'SELECT id FROM #__users WHERE username = "'.$user_name.'"';
		$db->setQuery($query);
		return $db->loadResult();
	}

	static function getUserNameById($user_id){
		$db	= JFactory::getDBO();
		$query = 'SELECT username FROM #__users WHERE id = "'.$user_id.'"';
		$db->setQuery($query);
		return $db->loadResult();
	}

	static function getUserNameAndActivation($user_id){
		$db	= JFactory::getDBO();
		$query = 'SELECT name,activation FROM #__users WHERE id = "'.$user_id.'"';
		$db->setQuery($query);
		return $db->loadAssoc();
	}

	static function getShopItemid() {
		global $database;
		$db_temp = new ps_DB;
		$db_temp->execute( "SELECT id FROM #__menu WHERE link='index.php?option=com_virtuemart' AND published=1");
		if( $db_temp->next_record() )
			return $db_temp->f("id");
		return 1;
	}
}


?>