<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_productCategoryId extends emp_conditionPlugins_base{

	public function getType(){
		return "PRODUCT_CATEGORY";
	}

	public function getDisplayName(){
		return JText::_( 'PRODUCT_CATEGORY' );
	}

	public function getOperators(){
		return array('=','NOT');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for productCategoryId class";
			return false;
		}

		$orderId = $args['order_id'];
		$orderItemsCatIds = $this->getOrderItemsCategoryIds($orderId);
		if(empty($orderItemsCatIds)){
			return false;
		}

		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}

		$bFoundItems = count(array_diff($valueArr, $orderItemsCatIds)) < count($valueArr);
		$bOberator = $operator == '=' ? true : false;

		return $bOberator == $bFoundItems;
	}

	public function getDescription(){
		return JText::_( 'PRODUCT_CATEGORY_ID_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		$categories = emp_helper::getCategories();
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
		if(!empty($args['order_id']) && is_array($args) && key_exists("order_id", $args)){
			$bRes = true;
		}

		return $bRes;
	}
}