<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright	Copyright (C) 2013 InteraMind Advanced Analytics. All rights reserved.
 
 **/

require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'database.php';

class orderTagsHandler {
	
	function replaceTags(&$str, &$errors, &$resources){

		vmeeProDb::loadVirtueMartFiles();

		$vendor = vmeeProDb::getVendor();
		$store_address = $this->getStoreAddressHeader($vendor);
		$str = str_replace( '[STORE_ADDRESS_FULL_HEADER]', $store_address, $str);
		
		$config = JFactory::getConfig();
		$mailTimeZone = $config->get('offset');
		
		$order_id = isset($resources['order_id']) ? $resources['order_id'] : 0;

		if(isset($resources['cart']) && !empty($resources['cart'])){
			$cart = $resources['cart'];
			$this->getUserLang(null, null, $resources['lang']);
			if(strpos($str,'[CART_INFO]') !== false){
			foreach ($cart->products as &$prow) {
				$thumb = vmeeProDb::getProductThumb($prow->virtuemart_product_id);
				if($thumb !== false){
					$prow->thumbUrl = $thumb;
				}
			}
			ob_start();
			$items_info_vars = $this->getItemsInfoVars('_cart');
			extract($items_info_vars);
			include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DIRECTORY_SEPARATOR .'tagTemplates'. DIRECTORY_SEPARATOR .'cart_information.tpl.php');
			$cart_info = ob_get_contents();
			ob_end_clean();
			$str = str_replace( '[CART_INFO]', $cart_info, $str);
			}
			$BTuserFields = $this->getUserAddressFieldsFromCart($cart, 'BT');
			$STuserFields = $this->getUserAddressFieldsFromCart($cart, 'ST');
			$customerEmail = $cart->BT['email'];
			$str = $this->replaceCartProductsLinksTags($cart, $str);
			
		}

		if(isset($resources['user_name']) && !empty($resources['user_name'])){
			$user_id = vmeeProDb::getUserIdByUserName($resources['user_name']);
			$resources['user_id'] = $user_id;
		}

		if(isset($resources['password']) && !empty($resources['password'])){
			$str = str_replace( '[CUSTOMER_PASSWORD]', $resources['password'], $str);
		}
		
		if(isset($resources['product_id']) && !empty($resources['product_id'])){
			$productId = $resources['product_id'];
			$linkArr = $this->getProductLink($productId);
			$link = sprintf("<a href=\"%s\">%s</a>", $linkArr['href'],$linkArr['link_text']);
			$productImage = $this->getProductImage($productId);
			$image = sprintf("<img src=\"%s\" />", $productImage);
			$str = str_replace('[WAITING_LIST_PRODUCT_LINK]', $link, $str);
			$str = str_replace('[WAITING_LIST_PRODUCT_NAME]', $linkArr['link_text'], $str);
			$str = str_replace('[WAITING_LIST_PRODUCT_IMG]', $image, $str);
		}
			
		if(!empty($order_id)){
			$order = vmeeProDb::getOrderByOrderId($order_id);
			$order_number = $order['order_number'];
			$order_details = $order['details']['BT'];
			$invoice_number = $order['invoice_number'];
			$user_id = !empty($order_details->virtuemart_user_id) ? $order_details->virtuemart_user_id : null;
			if(!is_null($user_id)){
				$this->getUserLang($user_id, $order_details->order_language, $resources['lang']);
			}
			else{
				$this->getUserLang(null, $order_details->order_language, $resources['lang']);
			}
			$BTuserFields = $this->getUserAddressFieldsFromOrder($order);	
			$STuserFields = $this->getUserAddressFieldsFromOrder($order,'ST');

			$payment_info_details = 	vmeeProDb::getPaymentName($order_details);
			$str = str_replace( '[PAYMENT_INFO_DETAILS]', $payment_info_details, $str);
			$shipment_info_details = 	vmeeProDb::getShippingName($order_details);
			$str = str_replace( '[SHIPPING_INFO_DETAILS]', $shipment_info_details, $str);
			
			$order_shipping = 			$vendor->currency->priceDisplay($order_details->order_shipment);
			$order_tax = 				$vendor->currency->priceDisplay(/* $order_details->order_tax */$order_details->order_billTaxAmount);
			$order_total = 				$vendor->currency->priceDisplay($order_details->order_total);
			$order_subtotal =  			$vendor->currency->priceDisplay(/*$order_details->order_subtotal*/$order_details->order_salesPrice);
			$order_discount = 			$vendor->currency->priceDisplay(/* $order_details->order_discountAmount */$order_details->order_billDiscountAmount);
			$coupon_discount = 			$vendor->currency->priceDisplay($order_details->coupon_discount);
			$order_status = 			JText::_($order_details->order_status_name);
			$order_status_description = $order_details->order_status_description;
		
			$defTZ = date_default_timezone_get();
			date_default_timezone_set('UTC');
			$order_date = strtotime($order_details->created_on);	// VM stores UTC order dates
			$delivery_date = '';
			if(!empty($order_details->delivery_date)){
				$delivery_date = $order_details->delivery_date;
				if(preg_match('/[0-9]+/', $order_details->delivery_date)){
					$delivery_date = strtotime($order_details->delivery_date);	// VM stores UTC order dates
				}
			}
			date_default_timezone_set($defTZ);
			
			if (!is_null($user_id)){
				$user = JFactory::getUser($user_id);
				$mailTimeZone = $user->getParam('timezone', $mailTimeZone);
			}
				
			$order_items_count = 0;
			$order_products_count = $this->getProductsAndItemsCount($order,$order_items_count);
			$customerEmail = $order_details->email;
			
			$currency = $this->getCurrency($order);
			$shipment_name = $this->getShipmentName($order);
			$payment_name = $this->getPaymentName($order);
			
			if(strpos($str, '[ORDER_ITEMS_INFO]') !== false){
				ob_start();
				$items_info_vars = $this->getItemsInfoVars();
				extract($items_info_vars);
				include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DIRECTORY_SEPARATOR .'tagTemplates'. DIRECTORY_SEPARATOR .'items_information.tpl.php');
				$items_info = ob_get_contents();
				ob_end_clean();
				$str = str_replace( '[ORDER_ITEMS_INFO]', $items_info, $str);
			}
				
			if(strpos($str, '[ADMIN_ORDER_ITEMS_INFO]') !== false){
				ob_start();
				$items_info_vars = $this->getItemsInfoVars('_admin');
				extract($items_info_vars);
				include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagTemplates'.DIRECTORY_SEPARATOR.'admin_items_information.tpl.php');
				$admin_items_info = ob_get_contents();
				ob_end_clean();
				
				$str = str_replace( '[ADMIN_ORDER_ITEMS_INFO]', $admin_items_info, $str);
			}
			
			$order_pass = $order_details->order_pass;
			$str = str_replace( '[ORDER_PASS]', $order_pass, $str);

			$url = JUri::root()."index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&virtuemart_order_id={$order_id}&order_number={$order_number}&order_pass={$order_pass}&create_invoice=1";
			$str = $this->replaceLinkTag('INVOICE_LINK',$url,$str);
			
			$shopper_order_link = $this->getSiteURL()."index.php?option=com_virtuemart&view=orders&layout=details&order_number=" . $order_details->order_number.'&order_pass='.$order_pass;
			$str = $this->replaceLinkTag('ORDER_LINK',$shopper_order_link,$str);
			
			$admin_order_link = $this->getSiteURL()."administrator/index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=" . $order_id;
			$str = $this->replaceLinkTag('ADMIN_ORDER_LINK',$admin_order_link,$str);
							
			$customer_note  = $order_details->customer_note;
			$customer_note  = nl2br($customer_note);
			$str = str_replace( '[CUSTOMER_NOTE]', nl2br($customer_note), $str);
			$str = $this->replaceCustomOrdersFields($str, $order_id);
			//???$str = $this->replaceCustomHistoryFields($str, $order_id);
			$str = $this->replaceDroppshipItemsInfo($order_id, $vendor, $order, $order_details, $str,$resources['lang'][0]);
			$str = str_replace( '[ORDER_ID]', $order_number, $str);
			$str = str_replace( '[VM_ORDER_ID]', $order_id, $str);
			$str = str_replace( '[INVOICE_NUMBER]', $invoice_number, $str);
			$str = str_replace( '[ORDER_STATUS]', $order_status, $str);
			$str = str_replace( '[ORDER_STATUS_DESCRIPTION]', $order_status_description, $str);
			$str = str_replace( '[ORDER_DISCOUNT]', $order_discount, $str);
			$str = str_replace( '[COUPON_DISCOUNT]', $coupon_discount, $str);
			$str = str_replace( '[ORDER_DATE]', $this->userFormattedDate($order_date, $mailTimeZone), $str);
			$str = $this->replaceDateTag('ORDER_DATE_LOCALE', $order_date, $mailTimeZone, $str);
			if(preg_match('/[0-9]+/', $delivery_date)){
				$str = str_replace( '[ORDER_DELIVERY_DATE]', $this->userFormattedDate($delivery_date, $mailTimeZone), $str);
			}
			else{
				$str = str_replace( '[ORDER_DELIVERY_DATE]', $delivery_date, $str);
			}
				
			$str = str_replace( '[ORDER_SUB_TOTAL]', $order_subtotal, $str);
			$str = str_replace( '[ORDER_SHIPPING]', $order_shipping, $str);
			$str = str_replace( '[ORDER_TAX]', $order_tax, $str);
			$str = str_replace( '[ORDER_TOTAL]', $order_total, $str);
			$str = str_replace( '[PRODUCTS_COUNT]', $order_products_count, $str);
			$str = str_replace( '[ITEMS_COUNT]',  $order_items_count, $str);
			$str = $this->replaceStatusChangedComment($str, $order_id);
			$str = $this->replaceManufacturerTags($str,$order);
			$str = $this->replaceProductsLinksTags($order, $str);
		}
		elseif(isset($resources['user_id']) && !empty($resources['user_id'])){
			$user_id = $resources['user_id'];
			$BTuserFields = $this->getUserAddressFieldsFromUser($user_id);
			$STuserFields = $this->getUserAddressFieldsFromUser($user_id,'ST');
			$this->getUserLang($user_id, null, $resources['lang']);
		}
		
		if(isset($user_id) && !empty($user_id)){
			$user = JFactory::getUser($user_id);
			//VM2 does not support downloadable products for sale in the core
			//$str = $this->replaceDownloadIdTags($str, $order_id);
			
			$activation = $user->get('activation');
			$name = $user->get('name');
			$str = str_replace( '[CUSTOMER_NAME]', $name, $str);
			$activation_link = $this->getSiteURL().'index.php?option=com_users&task=registration.activate&token='.$activation;
			$str = $this->replaceLinkTag('ACTIVATION_LINK', $activation_link, $str);

			$customerEmail = $user->get('email');
			$str = str_replace( '[CUSTOMER_USER_NAME]', $user->get('username'), $str);
			$str = str_replace( '[USER_ID]', $user_id, $str);
			$str = str_replace( '[SHOPPER_GROUP]',  $this->getShoppoerGroups($user_id), $str);
			$str = str_replace( '[CUSTOMER_TOTAL]', $vendor->currency->priceDisplay(vmeeProDb::getCustomerTotal($user_id)), $str);
			$str = str_replace( '[CUSTOMER_ORDERS_COUNT]', vmeeProDb::getCustomerOrdersCount($user_id), $str);
		}
		
		if(!empty($order_id)){
			$str = $this->replaceCustomUserInfoFields($str, $order_id,null);
		}
		elseif(!empty($user_id)){
			$str = $this->replaceCustomUserInfoFields($str, null,$user_id);
		}
		
		$billTo_shipTo = $this->getBillToShipTo($BTuserFields,$STuserFields,$str);
		if(!empty($billTo_shipTo)){
			$str = str_replace( '[BILL_TO_SHIP_TO]', $billTo_shipTo, $str);
		}
		
		$str = $this->replaceBillToFields($str, $BTuserFields);
		$str = $this->replaceShipToFields($str, $STuserFields);
		$str = $this->replaceLinkTag('SITEURL', $this->getSiteURL(), $str);
		$str = str_replace( '[CUSTOMER_EMAIL]',$customerEmail ,$str);
		$str = str_replace( '[CUSTOMER_FIRST_NAME]', $this->parseUserField($BTuserFields,'first_name'),$str);
		$str = str_replace( '[CUSTOMER_LAST_NAME]', $this->parseUserField($BTuserFields,'last_name'),$str);
		$str = str_replace( '[SITENAME]', $this->getSiteName(), $str);
		$str = str_replace( '[VENDOR_NAME]',$vendor->vendor_store_name, $str);
		$str = str_replace( '[CONTACT_EMAIL]', $vendor->email, $str);
		$str = str_replace( '[TODAY_DATE]', $this->userFormattedDate(time(), $mailTimeZone), $str);
		$str = $this->replaceDateTag('TODAY_DATE_LOCALE', time(), $mailTimeZone, $str);
		$str = $this->replaceDailyTotal($str,$vendor);
		
		return $str;
	}
	
	function getCurrency($order){
		return CurrencyDisplay::getInstance('', $order['details']['BT']->virtuemart_vendor_id);
	}
	
	function getBillToShipTo(&$BTuserFields,&$STuserFields, &$str){
		//sanity check
		if(strpos($str, '[BILL_TO_SHIP_TO]') === false){
			return '';
		}
		
		$billTo_shipTo = '';
		ob_start();
		include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DIRECTORY_SEPARATOR .'tagTemplates'. DIRECTORY_SEPARATOR .'billToShipTo.tpl.php');
		$billTo_shipTo = ob_get_contents();
		ob_end_clean();
		return $billTo_shipTo;
	}
	
	function getProductsAndItemsCount(&$order,&$itemsCount=null){
		$productsArr = array();
	
		foreach ($order['items'] as $item) {
			$productsArr[$item->virtuemart_product_id] = !isset($productsArr[$item->virtuemart_product_id]) ? 1 : $productsArr[$item->virtuemart_product_id] + $item->product_quantity;
			if(!is_null($itemsCount) ){
				$itemsCount = array_sum($productsArr);
			} 
			
			return count($productsArr);
		}
	
		return sizeof($order_item_sku_arr);
	}

	function replaceStatusChangedComment($str, $orderId){
		$request = JRequest::get();
		$comment = '';
		if(isset($request['orders'][$orderId])){
			$orderArr = $request['orders'][$orderId];
			if(isset($orderArr['customer_send_comment']) && $orderArr['customer_send_comment'] == 1 && !empty($orderArr['comments'])){
				$comment = nl2br($orderArr['comments']);
			}
			elseif(isset($request['include_comment']) && $request['include_comment'] == 1 && !empty($request['comments'])){
				$comment = nl2br($request['comments']);
			}
		}
		
		$str = str_replace( '[ORDER_STATUS_COMMENT]', $comment, $str);
		
		//backward compatibility
		$str = str_replace( '[COMMENT]', $comment, $str);
		
		return $str;
	}

	function getStoreAddressHeader($vendor){
		$store_address = '';
		ob_start();
		$storeAddressLogoStyle = $this->getStoreAddressLogoStyle();
		include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DS.'tagTemplates/store_address.tpl.php');
		$store_address = ob_get_contents();
		ob_end_clean();
		return $store_address;
	}

	function getStoreAddressLogoStyle(){
		return $this->getParamByName('store_address_logo_style');
	}

	function getShoppoerGroups($user_id) {
		$groups = vmeeProDb::get_shoppergroup_by_id($user_id);
		$groups_str = '';
		ob_start();
		include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DS.'tagTemplates/shopperGroups.tpl.php');
		$groups_str = ob_get_contents();
		ob_end_clean();
		return $groups_str;
	}
	
	function getProductThumbWidth(){
		return $this->getParamByName('product_thumb_width');
	}

	function replaceBillToFields($body, & $BTuserFields){
		$body = str_replace( '[BT_COUNTRY]', $this->parseUserField($BTuserFields,'virtuemart_country_id'),$body);
		$body = str_replace( '[BT_COMPANY]', $this->parseUserField($BTuserFields,'company'),$body);
		$body = str_replace( '[BT_TITLE]', $this->parseUserField($BTuserFields,'title'),$body);
		$body = str_replace( '[BT_FIRST_NAME]', $this->parseUserField($BTuserFields,'first_name'),$body);
		$body = str_replace( '[BT_LAST_NAME]', $this->parseUserField($BTuserFields,'last_name'),$body);
		$body = str_replace( '[BT_MIDDLE_NAME]', $this->parseUserField($BTuserFields,'middle_name'),$body);
		$body = str_replace( '[BT_ADDRESS_1]', $this->parseUserField($BTuserFields,'address_1'),$body);
		$body = str_replace( '[BT_ADDRESS_2]', $this->parseUserField($BTuserFields,'address_2'),$body);
		$body = str_replace( '[BT_CITY]',$this->parseUserField($BTuserFields,'city'),$body);
		$body = str_replace( '[BT_ZIP]', $this->parseUserField($BTuserFields,'zip'),$body);
		$body = str_replace( '[BT_PHONE_1]', $this->parseUserField($BTuserFields,'phone_1'),$body);
		$body = str_replace( '[BT_PHONE_2]', $this->parseUserField($BTuserFields,'phone_2'),$body);
		$body = str_replace( '[BT_FAX]', $this->parseUserField($BTuserFields,'fax'),$body);
		$body = str_replace( '[BT_STATE]', $this->parseUserField($BTuserFields,'virtuemart_state_id'), $body);

		return $body;
	}

	function replaceShipToFields($body, & $STuserFields){

		$body = str_replace( '[ST_COUNTRY]', $this->parseUserField($STuserFields,'virtuemart_country_id'),$body);
		$body = str_replace( '[ST_COMPANY]', $this->parseUserField($STuserFields,'company'),$body);
		$body = str_replace( '[ST_TITLE]', $this->parseUserField($STuserFields,'title'),$body);
		$body = str_replace( '[ST_FIRST_NAME]', $this->parseUserField($STuserFields,'first_name'),$body);
		$body = str_replace( '[ST_LAST_NAME]', $this->parseUserField($STuserFields,'last_name'),$body);
		$body = str_replace( '[ST_MIDDLE_NAME]', $this->parseUserField($STuserFields,'middle_name'),$body);
		$body = str_replace( '[ST_ADDRESS_1]', $this->parseUserField($STuserFields,'address_1'),$body);
		$body = str_replace( '[ST_ADDRESS_2]', $this->parseUserField($STuserFields,'address_2'),$body);
		$body = str_replace( '[ST_CITY]',$this->parseUserField($STuserFields,'city'),$body);
		$body = str_replace( '[ST_ZIP]', $this->parseUserField($STuserFields,'zip'),$body);
		$body = str_replace( '[ST_PHONE_1]', $this->parseUserField($STuserFields,'phone_1'),$body);
		$body = str_replace( '[ST_PHONE_2]', $this->parseUserField($STuserFields,'phone_2'),$body);
		$body = str_replace( '[ST_FAX]', $this->parseUserField($STuserFields,'fax'),$body);
		$body = str_replace( '[ST_STATE]', $this->parseUserField($STuserFields,'virtuemart_state_id'), $body);
		$body = str_replace( '[ST_ADDRESS_TYPE_NAME]', $this->parseUserField($STuserFields,'address_type_name'),$body);
		
		return $body;
	}

	function getCountry($country_db){
		$country = "";
		$countryStr = $country_db->f("country");
		if(!empty($countryStr)){
			require_once(CLASSPATH.'ps_country.php');
			$ps_country = new ps_country();
			$dbc = $ps_country->get_country_by_code($country_db->f("country"));
			if( $dbc !== false )
				$country = $dbc->f('country_name');
		}
			
		return $country;
	}


	public function isStoreHeaderTagExists($text){
		return strpos($text, '[STORE_ADDRESS_FULL_HEADER]') !== false;
	}

	function getSiteName(){
		$config = JFactory::getConfig();
		$sitename = $config->get('sitename');
		return $sitename;
	}

	function getSiteURL(){
		$url = JUri::root();
// 		if(stripos($url, 'virtuemart') !== false){
// 			//$url is not built right probably due to inialization that was done via Pypal notify.php
// 			//use virtuemart constant instead
// 			$url = substr_replace(URL, '', -1, 1)."/";
// 		}
		return $url;
	}

	function image_tag($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0 ) {
		global $mosConfig_live_site, $mosConfig_absolute_path;

		require_once( CLASSPATH . 'imageTools.class.php');
		$border = 'border="0"';
		$height = '';
		$width = !empty($thumb_width) ? $thumb_width : 100;

		if ($image != "") {
			if( substr( $image, 0, 4) == "http" ) {
				$url = $image;
			}
			else {
				$url = IMAGEURL.$path_appendix.'/'.$image;
				if( file_exists($image)) {
					$url = str_replace( $mosConfig_absolute_path, "", $image );
				} elseif( file_exists($mosConfig_absolute_path.'/'.$image)) {
					$url = $image;
				}
				$url = str_replace( basename( $url ), $GLOBALS['VM_LANG']->convert(basename($url)), $url );
			}
		}
		else {
			$url = VM_THEMEURL.'images/'.NO_IMAGE;
		}
		$url = str_replace( $mosConfig_live_site, "", $url );
		return vmCommonHTML::imageTag( $url, '', '', $height, $width, '', '', $args.' '.$border );
	}

	function replaceCustomOrdersFields($str, $order_id){

		$custom_orders_db =  vmeeProDb::getOrderResultSet($order_id);

		return $this->replaceCustom($str, 'CUSTOM_ORDERS:', $custom_orders_db, '#__virtuemart_orders');
	}

	function replaceCustom($str, $label, $db, $table_name){
		preg_match_all('/\['.$label.'[^\]]*\]/s', $str, $arr, PREG_PATTERN_ORDER);
		if(is_array($arr[0])){
			foreach ($arr[0] as $custom_label){
				preg_match('/\['.$label.'([^\]]*)\]/', $custom_label, $inner_arr);
				$field_name = trim($inner_arr[1]);
				$replace = '';
				if(isset($db[$field_name])){
					$replace = $db[$field_name];
				}
				$str = str_replace( $custom_label, $replace, $str);
			}
		}
		return $str;
	}



	function replaceCustomUserInfoFields($str, $order_id, $user_id = null){

		$custom_user_info_db_st =  vmeeProDb::getUserInfoDb($order_id, $user_id, 'ST');
		$custom_user_info_db =  vmeeProDb::getUserInfoDb($order_id, $user_id);
		
		$str = $this->replaceCustom($str, 'CUSTOM_USER_INFO_ST:', $custom_user_info_db_st, '#__virtuemart_user_infos');
		return $this->replaceCustom($str, 'CUSTOM_USER_INFO:', $custom_user_info_db, '#__virtuemart_user_infos');

	}

	function replaceCustomHistoryFields($str, $order_id){

		$custom_history_db =  vmeeProDb::getHistoryResultSet($order_id);

		return $this->replaceCustom($str, 'CUSTOM_HISTORY:', $custom_history_db, '#__virtuemart_order_histories');
	}

	function isCustomFieldExists($field_name, $table_name){
		$temp_db = new ps_DB;
		$q = "SHOW COLUMNS FROM ".$table_name;
		$temp_db->execute($q);
		$result = $temp_db->loadAssocList('Field');
		return array_key_exists($field_name, $result);
	}



	function getShopperName($order_id){
		//		$db = $this->getDBO($order_id);
		//		$user_id = $this->getUserIdByEmail($db->f('user_email') );
		$dbbt =  vmeeProDb::getOrderUserInfoDb($order_id);
		return $dbbt->f('first_name').' '.$dbbt->f('last_name');
	}

	private function replaceManufacturerTags($str, &$order){
		//sanity check
		if(strpos($str, '[MANUFACTURER_') === false){
			return $str;
		}
		
		$manufacturersArr  = array();
		$manufacturerModel = vmeeProDb::getVmModels('manufacturer');
		foreach ($order['items'] as $item){
			$manufacturerId = $item->manufacturer_id[0];
			if(!isset($manufacturersArr[$manufacturerId])){
				$manufacturerModel->setId($manufacturerId);
				$manufacturersArr[$manufacturerId] = $manufacturerModel->getManufacturer();
			}
		}
		
		if(!empty($manufacturersArr)){
			foreach ($manufacturersArr as $id=>$data){
				$str = str_replace( '[MANUFACTURER_ID]', $id, $str);
				$str = str_replace( '[MANUFACTURER_NAME]', $data->mf_name, $str);
				$str = str_replace( '[MANUFACTURER_EMAIL]', $data->mf_email, $str);
				$str = str_replace( '[MANUFACTURER_DESC]', $data->mf_desc, $str);
				$str = str_replace( '[MANUFACTURER_CAT_ID]',$data->virtuemart_manufacturercategories_id, $str);
				$str = str_replace( '[MANUFACTURER_URL]', '<a href="' . $data->mf_url . '">' . $data->mf_url . '</a>', $str);
				break;
			}

		}
		return $str;
	}

	private function getProductsLinks(&$order, $sefPrefix='', $anchorInPage='', $productId=null){

		$productsArr  = array();
		$anchor_in_page = '';
		$site_url = $this->getSiteUrl().$sefPrefix;
		if(!empty($anchorInPage)){
			$anchor_in_page = '#'.$anchorInPage;
		}
		if(empty($productId)){
			//get all order products links
			foreach ($order['items'] as $item){
				$linkArr = array();
				if(!isset($productsArr[$item->virtuemart_product_id])){
					$linkArr['href'] = $site_url . '?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $item->virtuemart_product_id . $anchor_in_page;
					$linkArr['link_text'] = $item->order_item_name;
					$productsArr[$item->virtuemart_product_id] = $linkArr;
				}
			}
		}
		else{
			//get specific product link
			$productsArr[$productId] = $this->getProductLink($productId,$sefPrefix,$anchorInPage);
		}
		
		return $productsArr;
	}
	
	private function getCartProductsLinks(&$cart, $sefPrefix='', $anchorInPage='', $productId=null){
	
		$productsArr  = array();
		$anchor_in_page = '';
		$site_url = $this->getSiteUrl().$sefPrefix;
		if(!empty($anchorInPage)){
			$anchor_in_page = '#'.$anchorInPage;
		}
		if(empty($productId)){
			//get all order products links
			foreach ($cart->products as $item){
				$linkArr = array();
				if(!isset($productsArr[$item->virtuemart_product_id])){
					$linkArr['href'] = $site_url . '?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $item->virtuemart_product_id . $anchor_in_page;
					$linkArr['link_text'] = $item->product_name;
					$productsArr[$item->virtuemart_product_id] = $linkArr;
				}
			}
		}
		else{
			//get specific product link
			$productsArr[$productId] = $this->getProductLink($productId,$sefPrefix,$anchorInPage);
		}
	
		return $productsArr;
	}

	private function getProductLink($productId, $sefPrefix='', $anchorInPage=''){
		$anchor_in_page = '';
		$site_url = $this->getSiteUrl().$sefPrefix;
		if(!empty($anchorInPage)){
			$anchor_in_page = '#'.$anchorInPage;
		}
		
		$linkArr = array();
		$linkArr['href'] = $site_url . '?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $productId . $anchor_in_page;
		$model = vmeeProDb::getVmModels('product');
		$product = $model->getProductSingle($productId);
		$linkArr['link_text'] = $product->product_name;
		
		return $linkArr;
		
	}
	
	private function getProductImage($productId){
		return vmeeProDb::getProductThumb($productId);
	}
	
	private function replaceProductsLinksTags(&$order,$str){
		$tagsArr = array();
		$pattern = '/\[PRODUCTS_LINKS\s*?(\|[^\|]*?){0,3}\]/';
		$tagspos = emp_helper::preg_pos_all($pattern,$str,$tagsArr);
		$offset = 0;
		foreach ($tagsArr as $idx=>$tag){
			$length = strlen($tag);
			$tag = trim($tag,'[]');
			$tagParts = explode('|', $tag);
			$sefPrefix = isset($tagParts[1]) && !empty($tagParts[1]) ? $tagParts[1] : '';
			$anchor = isset($tagParts[2]) && !empty($tagParts[2]) ? $tagParts[2] : '';
			$productId = isset($tagParts[3]) && !empty($tagParts[3]) ? (int)$tagParts[3] : null;
			$product_url_arr = $this->getProductsLinks($order,$sefPrefix,$anchor,$productId);
			ob_start();
			include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagTemplates'.DIRECTORY_SEPARATOR.'productLinks.tpl.php');
			$product_links_html = ob_get_contents();
			ob_end_clean();
			$str = substr_replace($str, $product_links_html, $tagspos[$idx]+$offset,$length);
			$offset -= $length;
			$offset += strlen($product_links_html);
		}
		return $str;
	}
	
	private function replaceCartProductsLinksTags(&$cart,$str){
		$tagsArr = array();
		$pattern = '/\[PRODUCTS_LINKS\s*?(\|[^\|]*?){0,3}\]/';
		$tagspos = emp_helper::preg_pos_all($pattern,$str,$tagsArr);
		$offset = 0;
		foreach ($tagsArr as $idx=>$tag){
			$length = strlen($tag);
			$tag = trim($tag,'[]');
			$tagParts = explode('|', $tag);
			$sefPrefix = isset($tagParts[1]) && !empty($tagParts[1]) ? $tagParts[1] : '';
			$anchor = isset($tagParts[2]) && !empty($tagParts[2]) ? $tagParts[2] : '';
			$productId = isset($tagParts[3]) && !empty($tagParts[3]) ? (int)$tagParts[3] : null;
			$product_url_arr = $this->getCartProductsLinks($cart,$sefPrefix,$anchor,$productId);
			ob_start();
			include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro'.DIRECTORY_SEPARATOR.'tagTemplates'.DIRECTORY_SEPARATOR.'productLinks.tpl.php');
			$product_links_html = ob_get_contents();
			ob_end_clean();
			$str = substr_replace($str, $product_links_html, $tagspos[$idx]+$offset,$length);
			$offset -= $length;
			$offset += strlen($product_links_html);
		}
		return $str;
	}

	private function replaceLinkTag($tagName, $url, $str){
		$tagsArr = array();
		$pattern = '/\[' . $tagName . '\s*?(\|[^\|]*?){0,1}\]/';
		$tagspos = emp_helper::preg_pos_all($pattern,$str,$tagsArr);
		$offset = 0;
		foreach ($tagsArr as $idx=>$tag){
			$length = strlen($tag);
			$tag = trim($tag,'[]');
			$tagParts = explode('|', $tag);
			$linkText = isset($tagParts[1]) && !empty($tagParts[1]) ? $tagParts[1] : $url;
			$link = '<a href="' . $url . '">' . $linkText . '</a>';
			$str = substr_replace($str, $link, $tagspos[$idx]+$offset,$length);
			$offset -= $length;
			$offset += strlen($link);
		}
		return $str;
	}

	private function getUserFieldFromDbOrRequest($dbFieldName,$dbv, $requestVarName=null){
		if(is_null($requestVarName)){
			$requestVarName = $dbFieldName;
		}
		$val = '';

		$value = $dbv->f($dbFieldName);
		if(!empty($value)){
			$val = $value;
		}
		else{
			$value = JFactory::getApplication()->input->get($requestVarName,null,'RAW');
			if(!empty($value)){
				$val = $value;
			}
		}

		return $val;
	}

	private function getItemsInfoVars($suffix = ''){
		$items_info_vars = array(
				'is_show_product_thumb'=>$this->getParamByName("is_show_product_thumb".$suffix) == 1 ? true : false,
				'productThumbWidth'=>$this->getParamByName("product_thumb_width".$suffix, 90),
				'is_show_product_quantity'=>$this->getParamByName("is_show_product_quantity".$suffix) == 1 ? true : false,
				'is_show_product_name'=>$this->getParamByName("is_show_product_name".$suffix) == 1 ? true : false,
				'is_show_product_sku'=>$this->getParamByName("is_show_product_sku".$suffix) == 1 ? true : false,
				'is_show_product_price'=>$this->getParamByName("is_show_product_price".$suffix) == 1 ? true : false,
				'is_show_product_tax'=>$this->getParamByName("is_show_product_tax".$suffix) == 1 ? true : false,
				'is_show_product_discount'=>$this->getParamByName("is_show_product_discount".$suffix) == 1 ? true : false,
				'is_show_product_total'=>$this->getParamByName("is_show_product_total".$suffix) == 1 ? true : false,
				'is_show_totals'=>$this->getParamByName("is_show_totals".$suffix) == 1 ? true : false,
				'is_show_subtotal'=>$this->getParamByName("is_show_subtotal".$suffix) == 1 ? true : false,
				'is_show_shipping'=>$this->getParamByName("is_show_shipping".$suffix) == 1 ? true : false,
				'is_show_payment_fee'=>$this->getParamByName("is_show_payment_fee".$suffix) == 1 ? true : false,
				'is_show_tax'=>$this->getParamByName("is_show_tax".$suffix) == 1 ? true : false,
				'is_show_order_discount'=>$this->getParamByName("is_show_order_discount".$suffix) == 1 ? true : false,
				'is_show_coupon_discount'=>$this->getParamByName("is_show_coupon_discount".$suffix) == 1 ? true : false,
		
				'main_table_style'=> $this->getParamByName("main_table_style".$suffix, ""),
				'table_th_style'=>$this->getParamByName("table_th_style".$suffix, "")
		);
		$is_items_info_empty = true;
		foreach ($items_info_vars as $key => $value) {
			if($value === true){
				$is_items_info_empty = false;
				break;
			}
		}
		$items_info_vars['is_items_info_empty'] = $is_items_info_empty;
		return $items_info_vars;
	}

	function getParamByName($paramName){
		return emp_helper::getGlobalParam($paramName);
	}


	private function replaceDroppshipItemsInfo($order_id, &$vendor, &$order, &$order_details, $str, $langPref){
		$tagsArr = array();
		$pattern = '/\[DS_ORDER_ITEMS_INFO\s*?(\|[^\|]*?){0,2}\]/';
		$tagspos = emp_helper::preg_pos_all($pattern,$str,$tagsArr);
		$offset = 0;
		foreach ($tagsArr as $idx=>$tag){
			$length = strlen($tag);
			$tag = trim($tag,'[]');
			$tagParts = explode('|', $tag);
			$filterName = isset($tagParts[1]) && !empty($tagParts[1]) ? $tagParts[1] : '';
			$filterVal = isset($tagParts[2]) && !empty($tagParts[2]) ? $tagParts[2] : '';
			ob_start();
			$items_info_vars = $this->getItemsInfoVars('_dropship');
			extract($items_info_vars);
			include(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmee'.DIRECTORY_SEPARATOR.'tagHandlerVmeePro' .DIRECTORY_SEPARATOR.'tagHandlerVmeePro'. DS.'tagTemplates'.DIRECTORY_SEPARATOR.'dropship_items_information.tpl.php');
			$ds_items_info = ob_get_contents();
			ob_end_clean();
			$str = substr_replace($str, $ds_items_info, $tagspos[$idx]+$offset,$length);
			$offset -= $length;
			$offset += strlen($ds_items_info);
		}
		return $str;
	}

	private function replaceDailyTotal($str, &$vendor){

		$finalStatusesArr = $this->getParamByName('finalStatuses');
		if(!is_array($finalStatusesArr)){
			$finalStatusesArr = explode(',', $finalStatusesArr);
		}
		foreach ($finalStatusesArr as &$status){
			$status = "'" . $status . "'";
		}
		$finalStatuses = implode(',', $finalStatusesArr);

		$today = strtotime("today UTC");
		$tomorrow = strtotime("tomorrow UTC")-1;
		$formattedToday = DateTime::createFromFormat('U', $today, new DateTimeZone('UTC'));
		$formattedTomorrow = DateTime::createFromFormat('U', $tomorrow, new DateTimeZone('UTC'));

		$db	= JFactory::getDBO();
		$q =  sprintf("SELECT SUM(order_total) as total, order_currency FROM #__virtuemart_orders WHERE order_status IN (%s) AND created_on BETWEEN '%s' AND '%s'", $finalStatuses, $formattedToday->format('Y-m-d H:i:s'), $formattedTomorrow->format('Y-m-d H:i:s'));
		$db->setQuery($q);
		$total = $db->loadResult();

		if(!is_null($total)){
			$daily_total = $vendor->currency->priceDisplay($total);
		}
		else{
			$daily_total = $vendor->currency->priceDisplay('0.00');
		}
		
		$str = str_replace( '[DAILY_TOTAL]', $daily_total, $str);
		return $str;
	}
	
	private function getUserAddressFieldsFromOrder(&$order, $type = 'BT'){
		$userFieldsModel = vmeeProDb::getVmModels('userfields');
		$userfields = null;
		if($type == 'BT'){
			$order_address_fields = $order['details']['BT'];
			$_userFields = $userFieldsModel->getUserFields(
					'account'
					, array('captcha' => true, 'delimiters' => true) // Ignore these types
					, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);
	
			$userfields = $userFieldsModel->getUserFieldsFilled(
					$_userFields
					,$order_address_fields
			);
		}else{
			$order_address_fields = (array_key_exists('ST', $order['details'])) ? $order['details']['ST'] : $order['details']['BT'];
			$_userFields = $userFieldsModel->getUserFields(
					'shipment'
					, array() // Default switches
					, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);
	
			$userfields = $userFieldsModel->getUserFieldsFilled(
					$_userFields
					,$order_address_fields
			);
		}
	
		return $userfields;
	}
	
	private function getUserAddressFieldsFromCart(&$cart, $type = 'BT'){
		$userFieldsModel = vmeeProDb::getVmModels('userfields');
		$userfields = null;
		if($type == 'BT'){
			$cart_address_fields = $cart->BT;
			$_userFields = $userFieldsModel->getUserFields(
					'account'
					, array('captcha' => true, 'delimiters' => true) // Ignore these types
					, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);
	
			$userfields = $userFieldsModel->getUserFieldsFilled(
					$_userFields
					,$cart_address_fields
			);
		}else{
			$cart_address_fields = !empty($cart->ST) ? $cart->ST : $cart->BT;
			$_userFields = $userFieldsModel->getUserFields(
					'shipment'
					, array() // Default switches
					, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);
	
			$userfields = $userFieldsModel->getUserFieldsFilled(
					$_userFields
					,$cart_address_fields
			);
		}
	
		return $userfields;
	}
	
	private function getUserAddressFieldsFromUser($user_id, $type = 'BT'){
		$vmUserModel = vmeeProDb::getVmModels('user');
		$vmUserModel->setId($user_id);
		$userIds = vmeeProDb::getUserInfoIds($user_id);
		
		if($type == 'ST' && !isset($userIds['ST'])){
			$type = 'BT';
		}
		
		$userFieldsArray = $vmUserModel->getUserInfoInUserFields('',$type,$userIds[$type]['virtuemart_userinfo_id'],true,true);
		$userFields = $userFieldsArray[$userIds[$type]['virtuemart_userinfo_id']];
	
		return $userFields;
	}
	
	public function parseUserField($userFields,$fieldName){
		$value = '';
		if(isset($userFields['fields'][$fieldName]) && $userFields['fields'][$fieldName]['value'] != null){
			$value = $userFields['fields'][$fieldName]['value'];
		}
		
		return $this->escape($value);
	}
	
	public function escape($var)
	{
		return htmlspecialchars($var,ENT_COMPAT,'UTF-8');
	}
	
	public function getUserLang($user_id, $order_language, $langPref){
		$langPref = $langPref[0];
		$sessionLang = JFactory::getApplication()->input->get('language', false ,'RAW');
		if(!empty($sessionLang) && $langPref == 'UD'){
			return $sessionLang;	
		}
		
		if(is_null($user_id) && $langPref == 'UD'){
			$langPref = 'OL';
		}
		
		$userLang = '';
		$lang = JFactory::getLanguage();
		
		switch ($langPref){
			case 'UD':
		//user default language
				$user = JFactory::getUser($user_id);
				$userLang = $user->getParam('language'); // Front-end language
				if(!empty($userLang)){
					break;
				}
				else{
					$userLang = $lang->getTag();
					if(!empty($userLang)){
						break;
					}
					else{
						$langPref = 'FD';
					}
				}
			case 'BD':
				//back-end default language
			case 'FD':
				//front-end default language
				$compParams =JComponentHelper::getParams( 'com_languages' );
				$siteLang = $compParams->get('site','en-GB');
				$adminLang = $compParams->get('administrator','en-GB');
				$userLang = $langPref == 'BD' ? $adminLang : $siteLang;
				break;
			case 'OL':
				$userLang = $order_language;
				break;
			default:
				//specific language
				$userLang = $langPref;
		}
		
		$extension = 'com_virtuemart_orders';
		$vm_base_dir = JPATH_SITE . '/components/com_virtuemart';
		$base_dir = JPATH_SITE;
		$language_tag = 'en-GB';
		$reload = true;
		$lang->load($extension, $vm_base_dir, $language_tag, $reload);
		$lang->load($extension, $vm_base_dir, null, $reload);
		$lang->load($extension, $vm_base_dir, $userLang, $reload);
			
		$lang->load($extension, $base_dir, $language_tag, $reload);
		$lang->load($extension, $base_dir, null, $reload);
		$lang->load($extension, $base_dir, $userLang, $reload);
		
		$extension = 'com_virtuemart_shoppers';
		$lang->load($extension, $vm_base_dir, $language_tag, $reload);
		$lang->load($extension, $vm_base_dir, null, $reload);
		$lang->load($extension, $vm_base_dir, $userLang, $reload);
		
		$lang->load($extension, $base_dir, $language_tag, $reload);
		$lang->load($extension, $base_dir, null, $reload);
		$lang->load($extension, $base_dir, $userLang, $reload);
		
		$extension = 'com_virtuemart';
		$lang->load($extension, $vm_base_dir, $language_tag, $reload);
		$lang->load($extension, $vm_base_dir, null, $reload);
		$lang->load($extension, $vm_base_dir, $userLang, $reload);
		
		$lang->load($extension, $base_dir, $language_tag, $reload);
		$lang->load($extension, $base_dir, null, $reload);
		$lang->load($extension, $base_dir, $userLang, $reload);
		
		$vm_base_dir = JPATH_ADMINISTRATOR . '/components/com_virtuemart';
		$base_dir = JPATH_ADMINISTRATOR;
		$lang->load($extension, $vm_base_dir, $language_tag, $reload);
		$lang->load($extension, $vm_base_dir, null, $reload);
		$lang->load($extension, $vm_base_dir, $userLang, $reload);
		
		$lang->load($extension, $base_dir, $language_tag, $reload);
		$lang->load($extension, $base_dir, null, $reload);
		$lang->load($extension, $base_dir, $userLang, $reload);
	}
	
	private function replaceDateTag($tagName, $timestamp, $timezone, $str){
		$tagsArr = array();
		$pattern = '/\[' . $tagName . '\s*?(\|[^\|]*?){0,1}\]/';
		$tagspos = emp_helper::preg_pos_all($pattern,$str,$tagsArr);
		$offset = 0;
	
		foreach ($tagsArr as $idx => $tag){
			$length = strlen($tag);
			$tag = trim($tag,'[]');
			$tagParts = explode('|', $tag);
			$locale = isset($tagParts[1]) && !empty($tagParts[1]) ? $tagParts[1] : null;
				
			$date = $this->userFormattedDateLocale($timestamp, $timezone, $locale);
			$str = substr_replace($str, $date, $tagspos[$idx] + $offset, $length);
	
			$offset -= $length;
			$offset += strlen($date);
		}
		return $str;
	}
	
	function getShipmentName($order){
		/* $shipmentMethodId = $order['details']['BT']->virtuemart_shipmentmethod_id;
        $shipmentMethodModel = $this->getVmModels('shipmentmethod');
        $shipmentMethodModel->setId($shipmentMethodId);
        $shipment = $shipmentMethodModel->getShipment();
        //return $shipment->shipment_desc;
        return $shipment->shipment_name; */
        
        $shipmentName = '';
        if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DIRECTORY_SEPARATOR . 'vmpsplugin.php');
        JPluginHelper::importPlugin('vmshipment');
        $dispatcher = JDispatcher::getInstance();
        $returnValues = $dispatcher->trigger('plgVmOnShowOrderFEShipment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_shipmentmethod_id, &$shipmentName));
        $shipmentName = str_replace('class="vmshipment_name"', 'style="margin-right: 10px;"', $shipmentName);
		return $shipmentName;
	}
	
	function getPaymentName($order){
      $paymentMethodId = $order['details']['BT']->virtuemart_paymentmethod_id;
        $paymentMethodModel = vmeeProDb::getVmModels('paymentmethod');
        $paymentMethodModel->setId($paymentMethodId);
        $payment = $paymentMethodModel->getPayment();
        return $payment->payment_name;
        /* $paymentName = '';
        if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DIRECTORY_SEPARATOR.'vmpsplugin.php');
        JPluginHelper::importPlugin('vmpayment');
        $dispatcher = JDispatcher::getInstance();
        $returnValues = $dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_paymentmethod_id,  &$paymentName));
        return strip_tags($paymentName); */
    }
    
    private function userFormattedDate($timestamp, $timezone) {
    	$dateFormat = emp_helper::getGlobalParam('date_format');
    
    	$date = new JDate();
    	
    	if(!function_exists('date_timestamp_set')){
    		$tmpdate = getdate( ( int ) $timestamp );
    		$date->setDate( $tmpdate['year'] , $tmpdate['mon'] , $tmpdate['mday'] );
    		$date->setTime( $tmpdate['hours'] , $tmpdate['minutes'] , $tmpdate['seconds'] );
    	}
    	else{
    		$date->setTimestamp($timestamp);
    	}
    	$date->setTimezone(new DateTimeZone($timezone));
    
    	$ret = $date->format($dateFormat, true);
    
    	return $ret;
    }
    
	private function userFormattedDateLocale($timestamp, $timezone, $locale = null) {
		if (!is_null($locale)){
			$curLocale = setlocale(LC_CTYPE, 0);
			setlocale(LC_CTYPE, $locale);
			setlocale(LC_TIME, $locale);
		}
		
		$dateFormat = emp_helper::getGlobalParam('date_format_locale');

		$defTZ = date_default_timezone_get();
		date_default_timezone_set($timezone);
		$date = strftime($dateFormat, $timestamp); 
		date_default_timezone_set($defTZ);
		
		if ($date === false){
			$dateFormat = "%b %d, %Y";
			$date = strftime($dateFormat); //March 10, 2011
		}

		if(!is_null($locale)){
			setlocale(LC_CTYPE, $curLocale);
			setlocale(LC_TIME, $curLocale);
		}

		return $date;
	}
}