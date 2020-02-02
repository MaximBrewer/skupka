<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_customerTotal extends emp_conditionPlugins_base{

	public function getType(){
		return "CUSTOMER_TOTAL";
	}

	public function getDisplayName(){
		return JText::_( 'CUSTOMER_TOTAL' );
	}

	public function getOperators(){
		return array('=','NOT', '<', '>', '<=', '>=', 'between');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for customerTotal class";
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
		
		$customerTtal = $this->getCustomerTotal($userId);
		if(is_null($customerTtal) || $customerTtal===false){
			return false;
		}

		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}
		
		$res = false;
		if($operator == '>' || $operator == '<' || $operator == '<=' || $operator == '>='){
			//there is no point in checking, for example, if orderTotal is greater than 2 values.
			$val = (float)$valueArr[0];
			$res = $this->doOperator($customerTtal, $operator, $val);
		}
		elseif($operator == 'between'){
			$this->normalizeForBetweenOperator($valueArr[0], $valueArr[1]);
			$res = $customerTtal > $valueArr[0] && $customerTtal < $valueArr[1];
		}
		else{
			//operarors = , NOT
			$bFoundItems =  in_array($customerTtal, $valueArr);
			$bOberator = $operator == '=' ? true : false;
			$res = $bOberator == $bFoundItems;;
		}
		
		return $res;
	}

	public function getDescription(){
		return JText::_( 'CUSTOMER_TOTAL_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		return $this->formatPossibleValues(self::FORMATTYPE_TEXT, '');
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