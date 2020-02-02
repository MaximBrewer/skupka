<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_shopperGroup extends emp_conditionPlugins_base{

	public function getType(){
		return "SHOPPER_GROUP";
	}

	public function getDisplayName(){
		return JText::_( 'SHOPPER_GROUP' );
	}

	public function getOperators(){
		return array('=','NOT');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for productCategoryId class";
			return false;
		}
		
		if(isset($args['user_id'])){
			$userId = $args['user_id'];
		}
		else{
			//we know that if there is no user_id in args, there must be order_id otherwise
			//validateArgs() would have been failed.
			$userId = $this->getUserFromOrder($args['order_id']);
		}

		$shopperGroup = $this->getShooperGroupId($userId);

		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}

		//operarors = , NOT
		$bFoundItems =  (bool)count(array_intersect($shopperGroup, $valueArr));
		$bOberator = $operator == '=' ? true : false;
		$res = $bOberator == $bFoundItems;;

		return $res;
	}

	public function getDescription(){
		return JText::_( 'SHOPPER_GROUP_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		$valueNames = array();
		$values = array();
		$shopperGroups = emp_helper::getShopperGroups();
		foreach ($shopperGroups as $group){
			$valueNames[] = $group->shopper_group_name;
			$values[] = $group->virtuemart_shoppergroup_id;
		}

		return $this->formatPossibleValues(self::FORMATTYPE_MULTI, $valueNames, $values);
	}

	protected function validateArgs($operator, $value, $args, &$errors){
		if(!parent::validateArgs($operator, $value, $args, $errors)){
			return false;
		}

		$bRes = false;
		if(!empty($args['user_id']) && is_array($args) && key_exists("user_id", $args)){
			$bRes = true;
		}
		elseif(!empty($args['order_id']) && is_array($args) && key_exists("order_id", $args)){
			$bRes = true;
		}

		return $bRes;
	}
}