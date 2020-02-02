<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_cartProductId extends emp_conditionPlugins_base{

	public function getType(){
		return "CART_PRODUCT";
	}

	public function getDisplayName(){
		return JText::_( 'CART_PRODUCT' );
	}

	public function getOperators(){
		return array('=','NOT');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for cartProductId class";
			return false;
		}

		$cart = $args['cart'];
		$cartItemsIds = $this->getCartItemsIds($cart);
		if(empty($cartItemsIds)){
			return false;
		}

		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}

		$bFoundItems = count(array_diff($valueArr, $cartItemsIds)) < count($valueArr);
		$bOberator = $operator == '=' ? true : false;

		return $bOberator == $bFoundItems;
	}

	public function getDescription(){
		return JText::_( 'CART_PRODUCT_ID_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		$products = emp_helper::getProducts();
		foreach ($products as $product){
			$valueNames[] = $product->product_name . '(' . $product->product_sku . ')';
			$values[] = $product->virtuemart_product_id;
		}

		return $this->formatPossibleValues(self::FORMATTYPE_MULTI, $valueNames, $values);
	}

	protected function validateArgs($operator, $value, $args, &$errors){
		if(!parent::validateArgs($operator, $value, $args, $errors)){
			return false;
		}

		$bRes = false;
		if(!empty($args['cart']) && is_array($args) && key_exists("cart", $args)){
			$bRes = true;
		}

		return $bRes;
	}
}