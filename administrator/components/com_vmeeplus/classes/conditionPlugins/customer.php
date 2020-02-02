<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_customer extends emp_conditionPlugins_base{

	public function getType(){
		return "CUSTOMER";
	}

	public function getDisplayName(){
		return JText::_( 'CUSTOMER' );
	}

	public function getOperators(){
		return array('=','NOT');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		emp_logger::log("customer cond-start. value=", emp_logger::LEVEL_DEBUG,$value);
		emp_logger::log("customer cond-start2. args=", emp_logger::LEVEL_DEBUG,$args);
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			emp_logger::log("customer cond-failed validation", emp_logger::LEVEL_DEBUG);
			$errors .= "Wrong arguments for customer class";
			return false;
		}

		if(isset($args['user_id'])){
			emp_logger::log("customer cond- if1", emp_logger::LEVEL_DEBUG);
			$userId = $args['user_id'];
		}
		else{
			emp_logger::log("customer cond- if2", emp_logger::LEVEL_DEBUG);
			//we know that if there is no user_id in args, there must be order_id otherwise
			//validateArgs() would have been failed.
			$userId = $this->getUserFromOrder($args['order_id']);
		}
		
		emp_logger::log("customer cond- user id=", emp_logger::LEVEL_DEBUG,$userId);
		$custIds = array($userId);

		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}

		$bFoundItems = count(array_diff($valueArr, $custIds)) < count($valueArr);
		$bOberator = $operator == '=' ? true : false;

		return $bOberator == $bFoundItems;
	}

	public function getDescription(){
		return JText::_( 'CUSTOMER_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		$db = JFactory::getDBO();
		$q = "SELECT first_name, last_name, virtuemart_user_id FROM #__virtuemart_userinfos";
		$db->setQuery($q);
		$result = $db->loadAssocList();
		$valueNames = array();
		$values = array();
		foreach ($result as $row){
			$valueNames[] = $row['first_name'] . ' ' . $row['last_name'];
			$values[] = $row['virtuemart_user_id'];
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