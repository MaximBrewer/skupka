<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_order extends emp_conditionPlugins_base{

	public function getType(){
		return "ORDER_ID";
	}
	
	public function getDisplayName(){
		return JText::_( 'ORDER_ID' );
	}
	
	public function getOperators(){
		return array('=','NOT');
	}
	
	public function evaluateCondition($operator, $value, $args, & $errors){
		
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for orderStatusCondition class"; 
			return false;
		}
		
		$orderId = $args['order_id'];
		$orderIds = array($orderId);
		
		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}
		
		$bFoundItems = count(array_diff($valueArr, $orderIds)) < count($valueArr);
		$bOberator = $operator == '=' ? true : false;
		
		return $bOberator == $bFoundItems;
	}
	
	public function getDescription(){
		return JText::_( 'ORDER_ID_CONDITION_DESCRIPTION' );
	}
	
	public function getPossibleValues(){
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_order_id, order_number FROM #__virtuemart_orders ORDER BY virtuemart_order_id DESC";
		$db->setQuery($q);
		$result = $db->loadAssocList();
		$statusCodes = array();
		$statusNames = array();
		foreach ($result as $row) {
			$values[] = $row['virtuemart_order_id'];
			$names[] = $row['order_number'];
		}

		return $this->formatPossibleValues(self::FORMATTYPE_MULTI, $names, $values);
	}
	
	protected function validateArgs($operator, $value, $args, & $errors){
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