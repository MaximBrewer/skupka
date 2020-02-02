<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_conditionPlugins_customerLanguage extends emp_conditionPlugins_base{

	public function getType(){
		return "CUSTOMER_LANGUAGE";
	}

	public function getDisplayName(){
		return JText::_( 'CUSTOMER_LANGUAGE' );
	}

	public function getOperators(){
		return array('=','NOT');
	}

	public function evaluateCondition($operator, $value, $args, & $errors){
		if(!$this->validateArgs($operator, $value, $args, $errors) ){
			$errors .= "Wrong arguments for productId class";
			return false;
		}

		emp_logger::log("customer lang condition. value=", emp_logger::LEVEL_DEBUG,$value);
		$userLang = '';
		if(isset($args['order_id']) && $args['order_id'] !== 0){
			$orderDetails = $this->getOrderDetails($args['order_id']);
			$userLang = $orderDetails->order_language;
		}
		elseif(isset($args['user_id']) && $args['user_id'] !== 0){
			//$userLang = JRequest::getVar('language', false );
			$lang = JFactory::getLanguage();
			$userLang = $lang->getTag();
			if($userLang == false){
				$userId = $args['user_id'];
				$user = JFactory::getUser($userId);
				$userLang = $user->getParam('language'); // Front-end language
			}
		}
		elseif(isset($args['user_name']) && $args['user_name'] !== 0){
			//$userLang = JRequest::getVar('language', false );
			$lang = JFactory::getLanguage();
			$userLang = $lang->getTag();
		}
		else{
			$lang = JFactory::getLanguage();
			$userLang = $lang->getTag();
		}
	
		emp_logger::log("customer lang condition. userLang=", emp_logger::LEVEL_DEBUG,$userLang);
		
		if(!is_array($value)){
			//value may be a comma separated list or a single value
			$valueArr = explode(',',$value);
		}
		else{
			$valueArr = $value;
		}

		$bFoundItems = in_array($userLang, $valueArr);
		$bOberator = $operator == '=' ? true : false;

		return $bOberator == $bFoundItems;
	}

	public function getDescription(){
		return JText::_( 'CUSTOMER_LANGUAGE_CONDITION_DESCRIPTION' );
	}

	public function getPossibleValues(){
		$langsArr = emp_helper::getLanguagesArray();
		return $this->formatPossibleValues(self::FORMATTYPE_MULTI, $langsArr, $langsArr);
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
		elseif(!empty($args['user_name']) && is_array($args) && key_exists("user_name", $args)){
			$bRes = true;
		}

		return $bRes;
	}
}