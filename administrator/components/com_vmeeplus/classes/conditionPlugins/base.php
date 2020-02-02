<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

abstract class emp_conditionPlugins_base{

	const FORMATTYPE_TEXT = 1;
	const FORMATTYPE_SELECT = 2;
	const FORMATTYPE_MULTI = 3;
	
	abstract public function getType();

	abstract public function getDisplayName();

	abstract public function getOperators();

	/**
	 *
	 * Evaluate the condition
	 *
	 * @param string $operator
	 * @param object $value
	 * @param array	$args	Arguments needed for this condition, like order ID, product ID, etc.
	 * @param array	$errors	List of errors to return by this function
	 * @return boolean
	 */
	abstract public function evaluateCondition($operator, $value, $args, & $errors);

	abstract public function getDescription();

	protected function validateArgs($operator, $value, $args, & $errors){
		$bRes = false;
		if(is_array($args) ){
			if(!empty($operator)){
				if(in_array($operator, $this->getOperators())){
					$bRes = true;
				}
			}
		}
		return $bRes;
	}

	abstract public function getPossibleValues();
	
	/**
	 * 
	 * @desc format list of values in a html format
	 * @param $formatType - one of: self::FORMATTYPE_TEXT, self::FORMATTYPE_SELECT, self::FORMATTYPE_MULTI
	 * @param array $valuesNames - optional - can be also comma separated list or empty (in case of format self::FORMATTYPE_TEXT)
	 * @param array $values - optional - option values in case of select or multi select
	 * @param string $cssClass - optional - a css class that will be added to the html element; 
	 */
	protected function formatPossibleValues($formatType = self::FORMATTYPE_TEXT, $valuesNames, $values = '' ,$cssClass = null){
		$formattedValues = '';
		$class = '';
		$multiple = $formatType == self::FORMATTYPE_MULTI ? ' multiple="multiple" ' : '';
		
		//if the format is text box, there is no need for initial value
		if((!isset($valuesNames) || empty($valuesNames)) && $formatType != self::FORMATTYPE_TEXT){
			return false;
		}
		
		if(!empty($cssClass)){
			$class = sprintf(" class=\"%s\" ", $cssClass);
		}
		//normalize the data to an array
	if(!is_array($valuesNames)){
			$valuesNames = explode(',', $valuesNames);
		}
		
		//format the data into html
		switch ($formatType){
			case self::FORMATTYPE_TEXT:
				$formattedValues = '<input type="text" name="valuesselect" id="valuesselect" class="conditionvalue" value="' . $valuesNames[0] . '">';
				break;
			case self::FORMATTYPE_SELECT:
			case self::FORMATTYPE_MULTI;
				$formattedValues = '<select id="valuesselect" name="valuesselect"' . $multiple . '>' . "\n";
				foreach ($valuesNames as $idx=>$value){
					$optionValue = isset($values[$idx]) ? ' value="' . $values[$idx] . '" ' : '';
					$formattedValues .= '<option' . $optionValue . '>' . $value . '</option>' . "\n";	
				}
				
				$formattedValues .= "</select>\n";
		}
		
		return $formattedValues;
		
		
	}
	
	protected  function getOrderStatus($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_status FROM #__virtuemart_orders where virtuemart_order_id =".$order_id;
		$db->setQuery($q);
		$result = $db->loadColumn();
		return $result;
	}
	
	protected function getOrderItemsIds($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT DISTINCT virtuemart_product_id FROM #__virtuemart_order_items where virtuemart_order_id =".$order_id;
		$db->setQuery($q);
		$result = $db->loadColumn();
		return $result;
	}
	
	protected function getOrderTotal($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_total FROM #__virtuemart_orders where virtuemart_order_id =".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return (float)$result;
	}
	
	protected function getOrderItemsCategoryIds($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT DISTINCT pc.virtuemart_category_id FROM #__virtuemart_order_items oi, #__virtuemart_product_categories pc WHERE oi.virtuemart_product_id = pc.virtuemart_product_id AND oi.virtuemart_order_id =".$order_id;
		$db->setQuery($q);
		$result = $db->loadColumn();
		return $result;
	}
	
	protected function getOrdersCount($user_id){
		$db = JFactory::getDBO();
		$q = "SELECT count(1) FROM #__virtuemart_orders WHERE virtuemart_user_id=" . $user_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	protected function getCompleteOrdersCount($user_id){
		$finalStatusesArr =  emp_helper::getGlobalParam('finalStatuses');
		if(!is_array($finalStatusesArr)){
			$finalStatusesArr = explode(',', $finalStatusesArr);
		}
		foreach ($finalStatusesArr as &$status){
			$status = "'" . $status . "'";
		}
		$finalStatuses = implode(',', $finalStatusesArr);
		$db = JFactory::getDBO();
		$q = "SELECT count(1) FROM #__virtuemart_orders WHERE virtuemart_user_id=" . $user_id. " AND order_status IN(" . $finalStatuses . ")";
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	protected function getCustomerTotal($user_id){
		$finalStatusesArr =  emp_helper::getGlobalParam('finalStatuses');
		if(!is_array($finalStatusesArr)){
			$finalStatusesArr = explode(',', $finalStatusesArr);
		}
		foreach ($finalStatusesArr as &$status){
			$status = "'" . $status . "'";
		}
		$finalStatuses = implode(',', $finalStatusesArr);
		$db = JFactory::getDBO();
		$q = "SELECT SUM(order_total) FROM #__virtuemart_orders WHERE virtuemart_user_id=" . $user_id. " AND order_status IN(" . $finalStatuses . ")";
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	protected function doOperator($val1, $operator, $val2){
		switch ($operator){
			case '>':
				return $val1 > $val2;
				break;
			case '<':
				return $val1 < $val2;
				break;
			case '>=':
				return $val1 >= $val2;
				break;
			case '<=':
				return $val1 <= $val2;
				break;
			default:
				return false; 
		}
	}
	
	/**
	 * @desc order the values so $val1 will hold the smaller value and $val2 will hold the higher value
	 * @param float $val1
	 * @param float $val2
	 */
	protected function normalizeForBetweenOperator(&$val1, &$val2){
		if($val1 > $val2){
			//make the switch
			$val1 += $val2;
			$val2 = $val1 - $val2;
			$val1 -= $val2;
		}
	}
	
	protected function getCartItemsIds($cart){
		$cartItems = array();
		foreach ($cart->products as $product){
			$cartItems[] = $product->virtuemart_product_id;
		}
		return $cartItems;
	}
	
	protected function getCartTotal($userId, $cartContent){
		require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR."virtuemart_parser.php");
		require_once( CLASSPATH.'ps_product.php');
		require_once( CLASSPATH.'ps_shopper_group.php');
		$ps_product = new ps_product;
		$ps_shopper_group = new ps_shopper_group();
		$shopperGroup = $ps_shopper_group->get_shoppergroup_by_id($userId);
		$total = 0;
		foreach ($cartContent as $product){
			$productId = $product['product_id'];
			$qtty = $product['quantity'];
			$parentId = $product['parent_id'];
			$description = $product['description'];
			$price = $ps_product->get_adjusted_attribute_price($productId,$description);
			$price["product_price"] = $GLOBALS['CURRENCY']->convert( $price["product_price"], $price["product_currency"] );
			$product_parent_id=$ps_product->get_field($productId, "product_parent_id");
			if ($shopperGroup["show_price_including_tax"] == 1) {
				$my_taxrate = $ps_product->get_product_taxrate($productId);
				$price["product_price"] *= ($my_taxrate+1);
			}
			$subtotal = round( $price["product_price"], 2 ) * $qtty;
			$total += $subtotal;
		}
		return $total;
	}
	
	protected function getOrderVendorsIds($orderId){
		$db = JFactory::getDBO();
		$q = "SELECT DISTINCT p.virtuemart_vendor_id FROM #__virtuemart_products p, #__virtuemarts_order_items oi WHERE p.virtuemart_product_id=oi.virtuemart_product_id AND oi.virtuemart_order_id =".$orderId;
		$db->setQuery($q);
		$result = $db->loadColumn();
		return $result;
	}
	
	protected function getOrderManufacturerIds($orderId){
		$db = JFactory::getDBO();
		$q = "SELECT DISTINCT mf.virtuemart_manufacturer_id FROM #__virtuemart_product_manufacturers mf, #__virtuemart_order_items oi where mf.virtuemart_product_id=oi.virtuemart_product_id AND oi.virtuemart_order_id =".$orderId;
		$db->setQuery($q);
		$result = $db->loadColumn();
		return $result;
	}
	
	protected function getUserFromOrder($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_user_id FROM #__virtuemart_orders where virtuemart_order_id =".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return (int)$result;
	}
	
	protected function getShooperGroupId($userId){
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_shoppergroup_id FROM #__virtuemart_vmuser_shoppergroups where virtuemart_user_id =".$userId;
		$db->setQuery($q);
		$result = $db->loadColumn();
		return $result;
	}
	
	protected function getOrderPaymentMethodId($orderId){
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_paymentmethod_id FROM #__virtuemart_orders where virtuemart_order_id =".$orderId;
		$db->setQuery($q);
		$result = $db->loadResult();
		return (int)$result;
	}
	
	protected function getOrderDetails($orderId){
		emp_helper::loadVirtueMartFiles();
		$orderModel = emp_helper::getVmModels('orders');
		$order = $orderModel->getOrder($orderId);
		return $order['details']['BT'];
	}
}