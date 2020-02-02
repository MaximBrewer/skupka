<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_orderPaymentMethod extends emp_conditionPlugins_base{

	public function getType(){
		return "ORDER_PAYMENT_METHOD";
	}
	
	public function getDisplayName(){
		return JText::_( 'ORDER_PAYMENT_METHOD' );
	}
	
	public function getOperators(){
		return array('=','NOT');
	}
	
	public function evaluateCondition($operator, $value, $args, & $errors){
		
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for orderStatusCondition class"; 
			return false;
		}
		
		$order_payment = $this->getOrderPaymentMethodId($args['order_id']);
		
		if(empty($order_payment))
			return false;
			
		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}
		
		$bFoundItems =  in_array($order_payment, $valueArr);
		$bOberator = $operator == '=' ? true : false;
		
		return $bOberator == $bFoundItems;
	}
	
	public function getDescription(){
		return JText::_( 'ORDER_PAYMENT_METHOD_CONDITION_DESCRIPTION' );
	}
	
	public function getPossibleValues(){
		emp_helper::loadVirtueMartFiles();
		$db = JFactory::getDBO();
		$q = "SELECT virtuemart_paymentmethod_id, payment_name FROM #__virtuemart_paymentmethods_" . VMLANG;
		$db->setQuery($q);
		$result = $db->loadAssocList();
		if(is_null($result) || empty($result)){
			//could not find the payment name in the default language, let's try in english
			$q = "SELECT virtuemart_paymentmethod_id, payment_name FROM #__virtuemart_paymentmethods_en_gb";
			$db->setQuery($q);
			$result = $db->loadAssocList();
		}
		$statusCodes = array();
		$statusNames = array();
		foreach ($result as $row) {
			$statusCodes[] = $row['virtuemart_paymentmethod_id'];
			$statusNames[] = $row['payment_name'];
		}

		return $this->formatPossibleValues(self::FORMATTYPE_MULTI, $statusNames, $statusCodes);
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