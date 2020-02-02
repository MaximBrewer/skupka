<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class emp_rules_waitingListRule extends emp_rules_base{
	public function getTrigger(){
		return 'TRIGGER_WAITING_LIST';
	}

	public function getTriggerDisplayName(){
		return JText::_('TRIGGER_WAITING_LIST');
	}

	public function getDefaultReciepientByType($args){
		$parameters = $this->getParameters();
		$email = '';
		if(isset($parameters['preconds']['disabledefaultreciepient']['values']) && $parameters['preconds']['disabledefaultreciepient']['values'] == false){
			if(isset($args['email']) && !empty($args['email'])){
				$email = $args['email'];
			}
		}
		return $email;
	}

	public function getOrientation(){
		return self::ORIENTATION_WAITING_LIST;
	}

	public function getExcludeConditionTypes(){
		$excludeConditions = array('ORDER_STATUS','ORDER_TOTAL','PRODUCT_CATEGORY', 'PRODUCT', 'CART_PRODUCT', 'CART_PRODUCT_CATEGORY', 'CART_TOTAL','ORDER_VENDOR', 'ORDER_MANUFACTURER', 'ORDER_ID','ORDER_PAYMENT_METHOD', 'CUSTOMER', 'CUSTOMER_TOTAL', 'ORDERS_COUNT','COMPLETE_ORDERS_COUNT','SHOPPER_GROUP');
		return $excludeConditions;
	}

	public function isSendEmails(){
		return !emp_helper::getGlobalParam('is_disable_waiting_list');
	}
}