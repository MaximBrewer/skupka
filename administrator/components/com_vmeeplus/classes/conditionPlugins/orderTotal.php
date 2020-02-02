<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_orderTotal extends emp_conditionPlugins_base{

	public function getType(){
		return "ORDER_TOTAL";
	}

	public function getDisplayName(){
		return JText::_( 'ORDER_TOTAL' );
	}

	public function getOperators(){
		return array('=','NOT', '<', '>', '<=', '>=', 'between');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for orderTotal class";
			return false;
		}

		$orderId = $args['order_id'];
		$orderTotal = $this->getOrderTotal($orderId);
		if(is_null($orderTotal) || $orderTotal === false){
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
			$res = $this->doOperator($orderTotal, $operator, $val);
		}
		elseif($operator == 'between'){
			$this->normalizeForBetweenOperator($valueArr[0], $valueArr[1]);
			$res = $orderTotal > $valueArr[0] && $orderTotal < $valueArr[1]; 
		}
		else{
			//operarors = , NOT
			$bFoundItems =  in_array($orderTotal, $valueArr);
			$bOberator = $operator == '=' ? true : false;
			$res = $bOberator == $bFoundItems;;
		}

		return $res;
	}

	public function getDescription(){
		return JText::_( 'ORDER_TOTAL_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		return $this->formatPossibleValues(self::FORMATTYPE_TEXT, '');
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