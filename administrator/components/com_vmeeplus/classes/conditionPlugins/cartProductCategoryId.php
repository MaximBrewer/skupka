<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_cartProductCategoryId extends emp_conditionPlugins_base{

	public function getType(){
		return "CART_PRODUCT_CATEGORY";
	}

	public function getDisplayName(){
		return JText::_( 'CART_PRODUCT_CATEGORY' );
	}

	public function getOperators(){
		return array('=','NOT');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for cartProductCategoryId class";
			return false;
		}

		$cart = $args['cart'];
		$cartItemsCatIds = $this->getCartItemsCategoryIds($cart);
		if(empty($cartItemsCatIds)){
			return false;
		}

		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}

		$bFoundItems = count(array_diff($valueArr, $cartItemsCatIds)) < count($valueArr);
		$bOberator = $operator == '=' ? true : false;

		return $bOberator == $bFoundItems;
	}

	public function getDescription(){
		return JText::_( 'CART_PRODUCT_CATEGORY_ID_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		$categories = emp_helper::getCategories();
		$valueNames = array();
		$values = array(); 
		foreach ($categories as $cat){
			$valueNames[] = $cat->category_name;
			$values[] = $cat->virtuemart_category_id;
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
	
	protected function getCartItemsCategoryIds($cart){
		$categoryIds = array();
		foreach ($cart->products as $product){
			$categoryIds[] = $product->virtuemart_category_id;
		}
		
		return $categoryIds;
	}
}