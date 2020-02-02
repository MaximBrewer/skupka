<?php
/**
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
 **/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgvmeeConditionalContentInteraMind extends JPlugin {

	const ORIENTATION_ORDER = 1;
	const ORIENTATION_EXISTING_CUSTOMER = 2;
	const ORIENTATION_NEW_CUSTOMER = 4;
	const ORIENTATION_CART = 8;
	const ORIENTATION_WAITING_LIST = 16;

	public static $STATUS_CONDITION_TAG = "ORDER_STATUS";
	public static $PRODUCT_ID_CONDITION_TAG = "PRODUCT_ID";
	public static $ORDER_TOTAL_CONDITION_TAG = "ORDER_TOTAL";
	public static $PRODUCT_CATEGORY_CONDITION_TAG = "PRODUCT_CATEGORY_ID";
	public static $ORDERS_COUNT_CONDITION_TAG = "ORDERS_COUNT";
	public static $CUSTOMER_TOTAL_CONDITION_TAG = "CUSTOMER_TOTAL";
	public static $PAYMENT_METHOD_CONDITION_TAG = "PAYMENT_METHOD_CODE";
	public static $COUPON_USED_CONDITION_TAG = "COUPON_USED_BY_CUSTOMER";
	public static $COUPON_USED_THIS_ORDER_CONDITION_TAG = "COUPON_USED_IN_THIS_ORDER";
	public static $NUMBER_OF_ITEMS_IN_ORDER_TAG = "NUMBER_OF_ITEMS_IN_ORDER";
	public static $SHOPPER_GROUP_NAME_TAG = "SHOPPER_GROUP_NAME";
	public static $LANG_CONDITION_TAG = "EMAIL_LANG";
	public static $CUSTOMER_PRODUCT_TAG = "CUSTOMER_PRODUCT";
	public static $SHIPMENT_METHOD_CONDITION_TAG = "SHIPMENT_METHOD_CODE";

	function plgvmeeConditionalContentInteraMind(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function doPriority1(&$str, &$errors, &$resources){

		return $this->replaceConditionalTags($str, $errors, $resources);
	}

	function doPriority2(&$str, &$errors, &$resources){
	}

	function doPriority3(&$str, &$errors, &$resources){
	}

	function replaceTags(&$str, &$errors, &$resources){
	}

	function replaceConditionalTags(&$str, &$errors, &$resources){
		if(isset($resources['order_id'])){
			$str = $this->replaceOrderStatusConditionalTags($str, $errors, $resources);
			$str = $this->replaceProductsConditionalTags($str, $errors, $resources);
			$str = $this->replaceOrderTotalConditionalTags($str, $errors, $resources);
			$str = $this->replaceProductCategory($str, $errors, $resources);
			$str = $this->replacePaymentMethod($str, $errors, $resources);
			$str = $this->replaceShipmentMethod($str, $errors, $resources);
			$str = $this->replaceNumberOfItems($str, $errors, $resources);
			$str = $this->replaceCouponUsedLastOrder($str, $errors, $resources);
		}
		$str = $this->replaceOrdersCount($str, $errors, $resources);
		$str = $this->replaceCustomerTotal($str, $errors, $resources);
		$str = $this->replaceShopperGroupName($str, $errors, $resources);
		$str = $this->replaceCouponUsed($str, $errors, $resources);
		$str = $this->replaceEmailLang($str, $errors, $resources);
		$str = $this->replaceCustomerProduct($str, $errors, $resources);
		

		return $str;
	}

	function replaceOrderStatusConditionalTags(&$str, &$errors, &$resources){
		
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$STATUS_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$statusArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];

				if(!$this->isInStatus($statusArray, $resources['order_id'])){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}


			}
		}

		return $str;
	}

	function replaceProductsConditionalTags(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$PRODUCT_ID_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$productsArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];

				if(!$this->isProductsInOrder($productsArray, $resources['order_id'])){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}

		return $str;
	}



	function replaceOrderTotalConditionalTags(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$ORDER_TOTAL_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$orderTotalCond = $cond_block[2];
				$block_inner_content = $cond_block[3];


				$validOperands = array(
						'=' => 'eq',
						'&lt;' => 'lt',
						'&gt;' => 'gt',
						'&lt;=' => 'lteq',
						'&gt;=' => 'gteq',
						'between' => 'between',
						'BETWEEN' => 'between');

				if(array_key_exists($operand, $validOperands)){
					$order_total = $this->getOrderTotal($resources['order_id']);
						
					if(call_user_func(array($this, $validOperands[$operand]), $order_total, $orderTotalCond)){
						$this->checkSendConditionalEmail($block_inner_content, $resources);
						$str = str_replace( $whole_block, $block_inner_content, $str);
					}else{
						$str = str_replace( $whole_block, '', $str);
					}
				}
			}
		}

		return $str;
	}

	function replaceProductCategory(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$PRODUCT_CATEGORY_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$categoryArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];

				if(!$this->isProductsInCategory($categoryArray, $resources['order_id'])){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}

		return $str;
	}

	function replaceOrdersCount(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$ORDERS_COUNT_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$orderCountCond = $cond_block[2];
				$block_inner_content = $cond_block[3];

				$validOperands = array(
						'=' => 'eq',
						'&lt;' => 'lt',
						'&gt;' => 'gt',
						'&lt;=' => 'lteq',
						'&gt;=' => 'gteq',
						'between' => 'between',
						'BETWEEN' => 'between');

				if(array_key_exists($operand, $validOperands)){
					$user_id = isset($resources['user_id']) ? $resources['user_id'] : $this->getUserId($resources['order_id']);
					$order_count= $this->getOrderCount($user_id);
						
					if(call_user_func(array($this, $validOperands[$operand]), $order_count, $orderCountCond)){
						$this->checkSendConditionalEmail($block_inner_content, $resources);
						$str = str_replace( $whole_block, $block_inner_content, $str);
					}else{
						$str = str_replace( $whole_block, '', $str);
					}
				}
			}
		}

		return $str;
	}

	function replaceCustomerTotal(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$CUSTOMER_TOTAL_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$customerTotalCond = $cond_block[2];
				$block_inner_content = $cond_block[3];

				$validOperands = array(
						'=' => 'eq',
						'&lt;' => 'lt',
						'&gt;' => 'gt',
						'&lt;=' => 'lteq',
						'&gt;=' => 'gteq',
						'between' => 'between',
						'BETWEEN' => 'between');

				if(array_key_exists($operand, $validOperands)){
					$user_id = isset($resources['user_id']) ? $resources['user_id'] : $this->getUserId($resources['order_id']);
					$customer_total = $this->getCustomerTotal($user_id);
						
					if(call_user_func(array($this, $validOperands[$operand]), $customer_total, $customerTotalCond)){
						$this->checkSendConditionalEmail($block_inner_content, $resources);
						$str = str_replace( $whole_block, $block_inner_content, $str);
					}else{
						$str = str_replace( $whole_block, '', $str);
					}
				}
			}
		}

		return $str;
	}

	function replaceNumberOfItems(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$NUMBER_OF_ITEMS_IN_ORDER_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$itemCountCond = $cond_block[2];
				$block_inner_content = $cond_block[3];

				$validOperands = array(
						'=' => 'eq',
						'&lt;' => 'lt',
						'&gt;' => 'gt',
						'&lt;=' => 'lteq',
						'&gt;=' => 'gteq',
						'between' => 'between',
						'BETWEEN' => 'between');

				if(array_key_exists($operand, $validOperands)){
					$orderItemcount= $this->getOrderItemCount($resources['order_id']);
						
					if(call_user_func(array($this, $validOperands[$operand]), $orderItemcount, $itemCountCond)){
						$this->checkSendConditionalEmail($block_inner_content, $resources);
						$str = str_replace( $whole_block, $block_inner_content, $str);
					}else{
						$str = str_replace( $whole_block, '', $str);
					}
				}
			}
		}
		return $str;
	}

	function replaceShopperGroupName(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$SHOPPER_GROUP_NAME_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$shopperGroupArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];
				$user_id = isset($resources['user_id']) ? $resources['user_id'] : $this->getUserId($resources['order_id']);
				if(!$this->isUserInShopperGroup($shopperGroupArray, $user_id)){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}
		return $str;
	}

	function replacePaymentMethod(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$PAYMENT_METHOD_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$paymentIdArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];

				if(!$this->isPaymentInList($paymentIdArray, $resources['order_id'])){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}
		return $str;
	}
	
	function replaceShipmentMethod(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$SHIPMENT_METHOD_CONDITION_TAG, $str);
	
		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$shipmentIdArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];
	
				if(!$this->isShipmentInList($shipmentIdArray, $resources['order_id'])){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}
		return $str;
	}

	function replaceCouponUsedLastOrder(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$COUPON_USED_THIS_ORDER_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$couponsArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];

				if(!$this->isCouponInLastOrder($couponsArray, $resources['order_id'])){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}
		return $str;
	}

	function replaceCouponUsed(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$COUPON_USED_CONDITION_TAG, $str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$couponsArray = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];
				$user_id = isset($resources['user_id']) ? $resources['user_id'] : $this->getUserId($resources['order_id']);
				if(!$this->isCouponUsed($couponsArray, $user_id)){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$this->checkSendConditionalEmail($block_inner_content, $resources);
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}
		return $str;
	}

	function replaceEmailLang(&$str, &$errors, &$resources){
		$user_id = null;
		if(isset($resources['user_id'])){
			$user_id =  $resources['user_id'];
		}
		elseif(isset($resources['order_id'])){
			$user_id = $this->getUserId($resources['order_id']);
		}
		
		$orderLang = null;
		if(isset($resources['order_id'])){
			$orderDetails = $this->getOrderDetails($resources['order_id']);
			$orderLang = $orderDetails->order_language;
		}
			
		$langPref = array('FD');
		if(isset($resources['lang'])){
			$langPref = $resources['lang'];
		}
		
		$userLang = $this->getUserLang($user_id, $orderLang, $langPref);

		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$LANG_CONDITION_TAG, $str);
		
		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$langArr = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];
		
				if(strtolower($userLang) == strtolower($langArr[0])){
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
				else{
					$str = str_replace( $whole_block, '', $str);
				}
			}
		}
		
		return $str;
		
		
	}
	
	function replaceCustomerProduct(&$str, &$errors, &$resources){
		$cond_results = $this->getConditionArray(plgvmeeConditionalContentInteraMind::$CUSTOMER_PRODUCT_TAG, $str);
		
		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$operand = $cond_block[1];
				$productsArr = explode(',', $cond_block[2]);
				$block_inner_content = $cond_block[3];
				$user_id = isset($resources['user_id']) ? $resources['user_id'] : $this->getUserId($resources['order_id']);
				if(!$this->customerHasProduct($productsArr, $user_id)){
					$str = str_replace( $whole_block, '', $str);
				}else{
					$str = str_replace( $whole_block, $block_inner_content, $str);
				}
			}
		}
		return $str;
	} 
	
	private function eq($arg1, $arg2){
		return $arg1 == $arg2;
	}

	private function lt($arg1, $arg2){
		return $arg1 < $arg2;
	}

	private function gt($arg1, $arg2){
		return $arg1 > $arg2;
	}

	private function lteq($arg1, $arg2){
		return $arg1 <= $arg2;
	}

	private function gteq($arg1, $arg2){
		return $arg1 >= $arg2;
	}

	private function between($order_total, $condition_total){
		$betweenArr = split(',', $condition_total);
		if(sizeof($betweenArr) == 2){
			$floor = trim($betweenArr[0], " (");
			$ceil = trim($betweenArr[1], " )");
			if($order_total >= $floor && $order_total <= $ceil)
				return true;
			return false;
		}
		return false;
	}

	function getConditionArray($condition_tag, $str){
		$regex = '/<[^\/>]+>\s*\[IF:\s*'.$condition_tag.'\s*(&lt;=|&gt;=|=|&lt;|&gt;between|BETWEEN)\s*([^\]]*)\]\s*<\/[^>]*>(.*)\[ENDIF\]\s*<\/[^>]*>/Us';
		preg_match_all($regex, $str, $cond_results, PREG_SET_ORDER);

		$regex = '/\[IF:\s*'.$condition_tag.'\s*(&lt;=|&gt;=|=|&lt;|&gt;|between|BETWEEN)\s*([^\]]*)\](.*)\[ENDIF\]/Us';
		preg_match_all($regex, $str, $cond_results, PREG_SET_ORDER);
		return $cond_results;
	}

	private function isProductsInOrder($productsArray, $order_id){
		$cc_db = JFactory::getDBO();
		$q = "SELECT #__virtuemart_products.virtuemart_product_id as product_id FROM #__virtuemart_products, #__virtuemart_order_items, #__virtuemart_orders ";
		$q .= "WHERE #__virtuemart_products.virtuemart_product_id=#__virtuemart_order_items.virtuemart_product_id ";
		$q .= "AND #__virtuemart_order_items.virtuemart_order_id='$order_id' ";
		$q .= "AND #__virtuemart_orders.virtuemart_order_id=#__virtuemart_order_items.virtuemart_order_id";

		$cc_db->setQuery($q);
		$result = $cc_db->loadAssocList('product_id');

		if(!$result)
			false;
			
		foreach ($result as $key => $value){
			$item_id = $value['product_id'];
			if(in_array($item_id, $productsArray))
				return true;
		}

		return false;
	}

	private function isProductsInCategory($categoryArray, $order_id){
		if(empty($categoryArray))
			return true;

		$categoryAsStr = "";
		for($i = 0; $i < sizeof($categoryArray); $i++){
			$categoryAsStr .= "'".$categoryArray[$i]."'";
			if($i < sizeof($categoryArray)-1)
				$categoryAsStr .= ",";
		}

		$cc_db = JFactory::getDBO();
		$q = "SELECT #__virtuemart_products.virtuemart_product_id FROM #__virtuemart_products, #__virtuemart_order_items, #__virtuemart_orders, #__virtuemart_product_categories ";
		$q .= "WHERE #__virtuemart_products.virtuemart_product_id=#__virtuemart_order_items.virtuemart_product_id ";
		$q .= "AND #__virtuemart_order_items.virtuemart_order_id='$order_id' ";
		$q .= "AND #__virtuemart_orders.virtuemart_order_id=#__virtuemart_order_items.virtuemart_order_id ";
		$q .= "AND #__virtuemart_product_categories.virtuemart_product_id=#__virtuemart_products.virtuemart_product_id ";
		$q .= "AND #__virtuemart_product_categories.virtuemart_category_id IN (".$categoryAsStr.")";

		$cc_db->setQuery($q);
		$result = $cc_db->execute();
		if(!$result)
			return false;
		else if($cc_db->getNumRows() > 0)
			return true;
		else
			return false;
	}

	private function isUserInShopperGroup($shopperGroupArr, $user_id){
		if(empty($shopperGroupArr))
			return true;
			
		$shopper_group_name = $this->getShopperGroupName($user_id);

		if(in_array($shopper_group_name, $shopperGroupArr))
			return true;
			
		return false;
	}

	private function isPaymentInList($paymentArray, $order_id){
	
		if (empty($paymentArray)) return true;
	
		$paymentAsStr = join(',', $paymentArray);
		emp_logger::log('isPaymentInList $paymentAsStr = ' . $paymentAsStr , emp_logger::LEVEL_DEBUG);
	
		$db = JFactory::getDBO();
		$q = "SELECT * FROM #__virtuemart_orders WHERE virtuemart_order_id={$order_id} AND virtuemart_paymentmethod_id IN ({$paymentAsStr})";
		$db->setQuery($q);
		$result = $db->loadColumn();
	
		if (empty($result)) {
			emp_logger::log('isPaymentInList return false', emp_logger::LEVEL_DEBUG);
			return false;
		}
	
		emp_logger::log('isPaymentInList return true', emp_logger::LEVEL_DEBUG);
		return true;
	}
	
	private function isShipmentInList($shipmentArray, $order_id){
		if (empty($shipmentArray)) return true;
	
		$shipmentAsStr = join(',', $shipmentArray);
		emp_logger::log('isShipmentInList $shipmentAsStr = ' . $shipmentAsStr , emp_logger::LEVEL_DEBUG);
	
		$db = JFactory::getDBO();
		$q = "SELECT * FROM #__virtuemart_orders WHERE virtuemart_order_id={$order_id} AND virtuemart_shipmentmethod_id IN ({$shipmentAsStr})";
		$db->setQuery($q);
		$result = $db->loadColumn();
	
		if (empty($result)) {
			emp_logger::log('isShipmentInList return false', emp_logger::LEVEL_DEBUG);
			return false;
		}
	
		emp_logger::log('isShipmentInList return true', emp_logger::LEVEL_DEBUG);
		return true;
	}
	
	private function isCouponUsed($couponsArray, $user_id){
		if(empty($couponsArray))
			return true;

		$couponsAsStr = "";
		for($i = 0; $i < sizeof($couponsArray); $i++){
			$couponsAsStr .= "'".$couponsArray[$i]."'";
			if($i < sizeof($couponsArray)-1)
				$couponsAsStr .= ",";
		}
			
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_order_id FROM #__virtuemart_orders WHERE virtuemart_user_id=".$user_id." AND coupon_code IN(".$couponsAsStr.")";
		$db->setQuery($q);
		$result = $db->execute();

		if(!$result){
			return false;
		}
		else if($db->getNumRows() > 0){
			return true;
		}
		else{
			return false;
		}
	}

	private function customerHasProduct($productsArr, $user_id){
		if(empty($productsArr))
			return true;
	
		$productsAsStr = "";
		for($i = 0; $i < sizeof($productsArr); $i++){
			$productsAsStr .= "'".$productsArr[$i]."'";
			if($i < sizeof($productsArr)-1)
				$productsAsStr .= ",";
		}
		
		$finalStatusesArr = emp_helper::getGlobalParam('finalStatuses');
		if(!is_array($finalStatusesArr)){
			$finalStatusesArr = explode(',', $finalStatusesArr);
		}
		foreach ($finalStatusesArr as &$status){
			$status = "'" . $status . "'";
		}
		$finalStatuses = implode(',', $finalStatusesArr);
	
		$db = JFactory::getDBO();
		$q = "SELECT i.virtuemart_product_id FROM #__virtuemart_order_items i, #__virtuemart_orders o, #__virtuemart_order_userinfos u WHERE u.virtuemart_user_id=".$user_id." AND u.virtuemart_order_id=o.virtuemart_order_id AND u.address_type='BT' AND o.order_status IN(" . $finalStatuses . ") AND i.virtuemart_order_id=o.virtuemart_order_id AND i.virtuemart_product_id IN(" . $productsAsStr . ")";
		$db->setQuery($q);
		$result = $db->execute();
	
		if(!$result){
			return false;
		}
		else if($db->getNumRows() > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	private function isCouponInLastOrder($couponsArray, $order_id){
		if(empty($couponsArray))
			return true;

		$db = JFactory::getDBO();
		$q = "SELECT coupon_code FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();

		if(!$result)
			return false;
		else if(in_array($result, $couponsArray)){
			return true;
		}
		else
			return false;
	}

	private function isInStatus($statusArray, $order_id){
			
		if(empty($statusArray))
			return true;

		$order_status = $this->getOrderStatus($order_id);

		if(empty($order_status))
			return true;
			
		if(is_array($statusArray)){
			foreach ($statusArray as $status){
				if($status == $order_status)
					return true;
			}
		}else if($statusArray ==  $order_status){
			return true;
		}

		return false;
	}

	private function checkSendConditionalEmail(& $str, & $resources){
		$cond_results = $this->getEmailsArray($str);

		if(is_array($cond_results)){
			foreach ($cond_results as $cond_block){
				$whole_block = $cond_block[0];
				$emailsArrayStr = $cond_block[1];

				$emailsArray = explode(',', $emailsArrayStr);

				if(key_exists('emails_list', $resources)){
					$resources['emails_list'] = array_merge($resources['emails_list'], $emailsArray);
				}else{
					$resources['emails_list'] = $emailsArray;
				}

				$str = str_replace( $whole_block, '', $str);

			}
		}
	}

	private function getEmailsArray($str){
		$regex = '/\{\{([^\}\}]*)\}\}/Us';
		preg_match_all($regex, $str, $cond_results, PREG_SET_ORDER);
		return $cond_results;
	}

	function getPaymentMethod($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_paymentmethod_id FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	function getOrderStatus($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_status FROM #__virtuemart_orders where virtuemart_order_id =".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	private function getOrderTotal($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_total FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	private function getOrderCount($user_id){
		$db = JFactory::getDBO();
		$q = "SELECT count(*) FROM #__virtuemart_orders WHERE virtuemart_user_id=".$user_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	private function getOrderItemCount($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT SUM(product_quantity) FROM #__virtuemart_order_items WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	private function getCustomerTotal($user_id){
		$db = JFactory::getDBO();
		$q = "SELECT SUM(order_total) FROM #__virtuemart_orders WHERE virtuemart_user_id=".$user_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	private function getUserId($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_user_id FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	private function getShopperGroupName($user_id){
		$db = JFactory::getDBO();
		$q = "SELECT shopper_group_name FROM #__virtuemart_shoppergroups a JOIN #__virtuemart_vmuser_shoppergroups b ON a.virtuemart_shoppergroup_id=b.virtuemart_shoppergroup_id WHERE b.virtuemart_user_id=".$user_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}

	function getAvailableTagsDesc(){
		$tplVars = $this->getTemplateVariables();
		return array(
				vmemailsModelVmemails::$TYPE_REGISTRATION => null,
				vmemailsModelVmemails::$TYPE_ORDER_CONFIRM => $tplVars,
				vmemailsModelVmemails::$TYPE_ADMIN_ORDER_CONFIRM => $tplVars,
				vmemailsModelVmemails::$TYPE_ORDER_SATAUS_CHANGED => $tplVars
		);
	}

	private function getTemplateVariables(){
		$availableTags = $this->getAvailableTags(self::ORIENTATION_CART | self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_ORDER | self::ORIENTATION_WAITING_LIST);
		return $availableTags[0];
	}

	public function getAvailableTags($orientation){
		$availableTags = array();
		if($orientation & (self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_ORDER)){
			$db = JFactory::getDBO();
			$q = "SELECT extension_id FROM #__extensions WHERE element='conditionalContentInteraMind'";
			$db->setQuery($q);
			$result = $db->loadResult();
			$allOrientation = self::ORIENTATION_CART | self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_ORDER | self::ORIENTATION_WAITING_LIST;
			$link = "index.php?option=com_plugins&view=plugin&layout=edit&extension_id=".$result;
			
			$description = "The [IF] tag supports these types of conditions for this template type:
					<ul>";
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$STATUS_CONDITION_TAG."</li>", self::ORIENTATION_ORDER , $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$PRODUCT_ID_CONDITION_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$ORDER_TOTAL_CONDITION_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$PRODUCT_CATEGORY_CONDITION_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$ORDERS_COUNT_CONDITION_TAG."</li>", self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$CUSTOMER_TOTAL_CONDITION_TAG."</li>", self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$PAYMENT_METHOD_CONDITION_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$SHIPMENT_METHOD_CONDITION_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$COUPON_USED_CONDITION_TAG."</li>", self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$COUPON_USED_THIS_ORDER_CONDITION_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$NUMBER_OF_ITEMS_IN_ORDER_TAG."</li>", self::ORIENTATION_ORDER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$SHOPPER_GROUP_NAME_TAG."</li>", self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$CUSTOMER_PRODUCT_TAG."</li>", self::ORIENTATION_ORDER | self::ORIENTATION_EXISTING_CUSTOMER, $orientation);
			$description .= $this->checkOrientation("<li>".plgvmeeConditionalContentInteraMind::$LANG_CONDITION_TAG."</li>", $allOrientation, $orientation);
			$description .="<li>{{Email addresses list here. Comma separated.}}</li>";
			$description .= "</ul>
					<p style='color:maroon;'>Note: [IF] tags can not be nested!</p>
					<p style='color:maroon;'>Note: {{Email addresses}} will not work in VMEE Pro</p>
					<p>See Plugin for more details and examples <a target=\"_blank\" href=\"".$link."\">Click here</a></p>
					<p>For more help: <a target=\"_blank\" href=\"http://www.interamind.com/conditional-content-plugin\">Conditional Content Plugin</a></p>";
			
			
			$availableTags[] = array("title" => "Targeted marketing template variables",
									"name" => "Conditional content tags",
									"example" => "<ul><li>[IF:ORDER_STATUS=C,X,P] Some text here [ENDIF]</li><li>[IF:PRODUCT_ID=17] Offer a coupon here [ENDIF]</li><li>[IF:ORDER_TOTAL>750] Say thanks [ENDIF]</li><li>[IF:CUSTOMER_PRODUCT=41,42,43]I have noticed that in the past you purchased one of our proteine products[ENDIF]</li><li>[IF:EMAIL_LANG=en-gb]English text[ENDIF]</li></ul>",
									"description" => $description);
		}
		return $availableTags;
	}
	
	private function checkOrientation($data, $allowedOrientatiom, $testedOrientation){
		$res = '';
		if($allowedOrientatiom & $testedOrientation){
			$res = $data;
		}
		
		return $res;
	}
	
	private function getUserLang($user_id, $order_language, $langPref){
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
		
		return $userLang;
	}
	
	private function getOrderDetails($orderId){
		emp_helper::loadVirtueMartFiles();
		$orderModel = emp_helper::getVmModels('orders');
		$order = $orderModel->getOrder($orderId);
		return $order['details']['BT'];
	}
}


